<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    header("Location: index.html?error=No%20has%20iniciado%20sesión");
    exit();
}

// Incluir la conexión a la base de datos
include 'php/conexion.php';

// Obtener el usuario y municipio de la sesión
$usuario = $_SESSION["usuario"];
$municipio = $_SESSION["municipio"];

// Obtener parámetros de búsqueda
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');

// Obtener área y clasificación de la URL
$area_nombre = isset($_GET['area']) ? trim($_GET['area']) : null;
$clasificacion_codigo = isset($_GET['clasificacion']) ? trim($_GET['clasificacion']) : null;

// Obtener página actual
$pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$pagina_actual = max($pagina_actual, 1);

// Calcular el offset para la paginación
$registros_por_pagina = 10;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

$area_id = null;
$clasificacion_id = null;

// Traducir el nombre del área a area_id
if ($area_nombre !== null) {
    $stmt_area = $conexion->prepare("SELECT id FROM areas WHERE nombre = ?");
    if ($stmt_area === false) {
        die("Error en la preparación de la consulta de área: " . $conexion->error);
    }
    $stmt_area->bind_param("s", $area_nombre);
    $stmt_area->execute();
    $result_area = $stmt_area->get_result();

    if ($row_area = $result_area->fetch_assoc()) {
        $area_id = $row_area['id'];
    } else {
        die("Área no encontrada.");
    }

    $stmt_area->close();
}

// Traducir el código de clasificación a clasificacion_id
if ($clasificacion_codigo !== null) {
    if ($area_id === null) {
        die("Debe proporcionar un área para filtrar la clasificación.");
    }

    $stmt_clas = $conexion->prepare("SELECT id FROM clasificaciones WHERE codigo = ? AND area_id = ?");
    if ($stmt_clas === false) {
        die("Error en la preparación de la consulta de clasificación: " . $conexion->error);
    }
    $stmt_clas->bind_param("si", $clasificacion_codigo, $area_id);
    $stmt_clas->execute();
    $result_clas = $stmt_clas->get_result();

    if ($row_clas = $result_clas->fetch_assoc()) {
        $clasificacion_id = $row_clas['id'];
    } else {
        die("Clasificación no encontrada para el área especificada.");
    }

    $stmt_clas->close();
}

// Construir la consulta base adaptada a la tabla 5_11
$query = "SELECT 
            f.id AS formato_id, 
            f.municipio, 
            f.anio, 
            f.ruta_archivo, 
            f5.no, 
            f5.nombre_expediente, 
            f5.serie_documental, 
            f5.clave, 
            f5.descripcion_contenido, 
            f5.resguardado, 
            f5.confidencial, 
            f5.vigencia_documental, 
            f5.area_responsable, 
            f5.informacion_al, 
            f5.responsable, 
            u.usuario AS nombre_usuario
          FROM 
            formatos f
          JOIN 
            formato_5_11 f5 ON f.id = f5.formato_id
          JOIN 
            usuarios u ON f.usuarios_id = u.id
          WHERE 
            u.usuario = ? 
            AND f.municipio = ? 
            AND f.anio = ?";

// Inicializar tipos y parámetros para bind_param
$types = "ssi"; // s: string, s: string, i: integer
$params = [$usuario, $municipio, $anio];

// Añadir filtros adicionales si están presentes
if ($area_id !== null) {
    $query .= " AND f.area_id = ?";
    $types .= "i"; // i: integer
    $params[] = $area_id;
}

if ($clasificacion_id !== null) {
    $query .= " AND f.clasificaciones_id = ?";
    $types .= "i"; // i: integer
    $params[] = $clasificacion_id;
}

if (!empty($search)) {
    $query .= " AND (f5.nombre_expediente LIKE ? OR f5.responsable LIKE ?)";
    $types .= "ss"; // s: string, s: string
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
}

// Añadir orden ascendente y paginación
$query .= " ORDER BY f.fecha_creacion ASC, f.id ASC LIMIT ? OFFSET ?";
$types .= "ii"; // i: integer, i: integer
$params[] = $registros_por_pagina;
$params[] = $offset;

// Preparar la consulta
$stmt = $conexion->prepare($query);
if ($stmt === false) {
    die("Error en la preparación de la consulta principal: " . $conexion->error);
}

// Vincular parámetros dinámicamente
$stmt->bind_param($types, ...$params);

// Ejecutar la consulta
$stmt->execute();
$result = $stmt->get_result();

// Obtener el total de registros para calcular el número de páginas
$query_count = "SELECT COUNT(*) as total 
                FROM formatos f
                JOIN formato_5_11 f5 ON f.id = f5.formato_id
                JOIN usuarios u ON f.usuarios_id = u.id
                WHERE u.usuario = ? 
                  AND f.municipio = ? 
                  AND f.anio = ?";

$types_count = "ssi";
$params_count = [$usuario, $municipio, $anio];

if ($area_id !== null) {
    $query_count .= " AND f.area_id = ?";
    $types_count .= "i";
    $params_count[] = $area_id;
}

if ($clasificacion_id !== null) {
    $query_count .= " AND f.clasificaciones_id = ?";
    $types_count .= "i";
    $params_count[] = $clasificacion_id;
}

if (!empty($search)) {
    $query_count .= " AND (f5.nombre_expediente LIKE ? OR f5.responsable LIKE ?)";
    $types_count .= "ss";
    $params_count[] = $search_param;
    $params_count[] = $search_param;
}

$stmt_count = $conexion->prepare($query_count);
if ($stmt_count === false) {
    die("Error en la preparación de la consulta de conteo: " . $conexion->error);
}

// Vincular parámetros para la consulta de conteo
$stmt_count->bind_param($types_count, ...$params_count);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_registros = $result_count->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

$stmt_count->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEPARTAMENTOS</title>
    <link rel="stylesheet" href="css/mostrarArchivos.css">
    <link rel="icon" href="img/TDP-REDONDO.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
</head>

<body>
    <div class="header">
    <h1 class="titulo">ENTREGA RECEPCION - <?php echo htmlspecialchars($municipio); ?></h1>
        <img src="img/logoTDP.png" alt="Logo Empresa" class="imgEmpresa">
    </div>

    <?php
    // Mostrar mensajes de éxito o error
    if (isset($_GET['mensaje'])) {
        echo "<div class='mensaje'>" . htmlspecialchars($_GET['mensaje']) . "</div>";
    }
    if (isset($_GET['error'])) {
        echo "<div class='error'>" . htmlspecialchars($_GET['error']) . "</div>";
    }
    ?>

    <div class="search-container">
        <form method="get" action="mostrarArchivos5_11.php"> <!-- Cambiar a un archivo específico para 5_11 si es necesario -->
            <input type="text" name="search" placeholder="Buscar archivo..." value="<?php echo htmlspecialchars($search); ?>">
            <input type="hidden" name="anio" value="<?php echo htmlspecialchars($anio); ?>">
            <?php if ($area_nombre !== null): ?>
                <input type="hidden" name="area" value="<?php echo htmlspecialchars($area_nombre); ?>">
            <?php endif; ?>
            <?php if ($clasificacion_codigo !== null): ?>
                <input type="hidden" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion_codigo); ?>">
            <?php endif; ?>
            <button type="submit">Buscar</button>
        </form>
    </div>

    <h2 class="subtitulo">
        <?php
        if ($clasificacion_codigo !== null) {
            // Obtener el nombre completo de la clasificación
            $stmt_nombre = $conexion->prepare("SELECT nombre FROM clasificaciones WHERE codigo = ? AND area_id = ?");
            if ($stmt_nombre === false) {
                die("Error en la preparación de la consulta de nombre de clasificación: " . $conexion->error);
            }
            $stmt_nombre->bind_param("si", $clasificacion_codigo, $area_id);
            $stmt_nombre->execute();
            $result_nombre = $stmt_nombre->get_result();

            if ($row_nombre = $result_nombre->fetch_assoc()) {
                echo htmlspecialchars($row_nombre['nombre']);
            } else {
                echo "Clasificación no encontrada";
            }

            $stmt_nombre->close();
        } else {
            echo "Todas las Clasificaciones";
        }
        ?>
    </h2>

    <div class="imprimir-pdf-btn">
            <a href="php/generarPDF5_11.php?<?php echo http_build_query(['search' => $search, 'anio' => $anio, 'area' => $area_nombre, 'clasificacion' => $clasificacion_codigo]); ?>" target="_blank">Imprimir PDF</a>
        </div>


    <?php
    // Mostrar los resultados filtrados
    if ($result->num_rows > 0) {
        echo "<div class='tabla-contenedor'>"; // Div para contener la tabla
        echo "<table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nombre del Expediente</th>
                        <th>Serie Documental</th>
                        <th>Clave</th>
                        <th>Descripción del Contenido</th>
                        <th>Resguardo</th>
                        <th>Confidencial</th>
                        <th>Vigencia Documental</th>
                        <th>Área Responsable</th>
                        <th>Información Al</th>
                        <th>Responsable</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = $result->fetch_assoc()) {
            // Construir los parámetros de la URL para mantener los filtros al editar o eliminar
            $params = [
                'area' => $area_nombre,
                'clasificacion' => $clasificacion_codigo,
                'search' => $search,
                'anio' => $anio,
                'pagina' => $pagina_actual
            ];
            $query_string = http_build_query($params);

            echo "<tr>
                    <td>" . htmlspecialchars($row['no']) . "</td>
                    <td>" . htmlspecialchars($row['nombre_expediente']) . "</td>
                    <td>" . htmlspecialchars($row['serie_documental']) . "</td>
                    <td>" . htmlspecialchars($row['clave']) . "</td>
                    <td>" . htmlspecialchars($row['descripcion_contenido']) . "</td>
                    <td>" . htmlspecialchars($row['resguardado']) . "</td>
                    <td>" . htmlspecialchars($row['confidencial']) . "</td>
                    <td>" . htmlspecialchars($row['vigencia_documental']) . "</td>
                    <td>" . htmlspecialchars($row['area_responsable']) . "</td>
                    <td>" . htmlspecialchars($row['informacion_al']) . "</td>
                    <td>" . htmlspecialchars($row['responsable']) . "</td>
                    <td class='acciones'>
                        <a href='php/editarRegistro5_11.php?id=" . urlencode($row['formato_id']) . "&$query_string'>Editar</a> | 
                        <a href='php/eliminarRegistro5_11.php?id=" . urlencode($row['formato_id']) . "&$query_string' class='boton-eliminar' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este registro?\");'> Eliminar</a>
                    </td>
                    </tr>";
        }
        echo "</tbody></table>";
        echo "</div>"; // Cerrar el div de la tabla

        // Paginación
        if ($total_paginas > 1) {
            echo "<div class='paginacion'>";

            // Enlaces de navegación: Primero, Anterior
            if ($pagina_actual > 1) {
                echo "<a href='?pagina=1&search=" . urlencode($search) . "&anio=" . urlencode($anio) . "&area=" . urlencode($area_nombre) . "&clasificacion=" . urlencode($clasificacion_codigo) . "'>&laquo; Primero</a>";
                echo "<a href='?pagina=" . ($pagina_actual - 1) . "&search=" . urlencode($search) . "&anio=" . urlencode($anio) . "&area=" . urlencode($area_nombre) . "&clasificacion=" . urlencode($clasificacion_codigo) . "'>&lsaquo; Anterior</a>";
            }

            // Mostrar números de página alrededor de la página actual
            $limite = 2; // Número de páginas a mostrar a cada lado de la actual
            $inicio = max(1, $pagina_actual - $limite);
            $fin = min($total_paginas, $pagina_actual + $limite);

            if ($inicio > 1) {
                echo "<span>...</span>";
            }

            for ($i = $inicio; $i <= $fin; $i++) {
                // Mostrar el enlace de la página actual
                if ($i === $pagina_actual) {
                    echo "<span class='pagina-actual'>" . $i . "</span>";
                } else {
                    echo "<a href='?pagina=" . $i . "&search=" . urlencode($search) . "&anio=" . urlencode($anio) . "&area=" . urlencode($area_nombre) . "&clasificacion=" . urlencode($clasificacion_codigo) . "'>" . $i . "</a>";
                }
            }

            if ($fin < $total_paginas) {
                echo "<span>...</span>";
            }

            // Enlace Siguiente, Último
            if ($pagina_actual < $total_paginas) {
                echo "<a href='?pagina=" . ($pagina_actual + 1) . "&search=" . urlencode($search) . "&anio=" . urlencode($anio) . "&area=" . urlencode($area_nombre) . "&clasificacion=" . urlencode($clasificacion_codigo) . "'>Siguiente &rsaquo;</a>";
                echo "<a href='?pagina=" . $total_paginas . "&search=" . urlencode($search) . "&anio=" . urlencode($anio) . "&area=" . urlencode($area_nombre) . "&clasificacion=" . urlencode($clasificacion_codigo) . "'>Último &raquo;</a>";
            }

            echo "</div>"; // Cerrar el div de paginación
        }
    } else {
        echo "<div class='mensaje'>No se encontraron registros.</div>";
    }
    ?>

</body>
</html>
