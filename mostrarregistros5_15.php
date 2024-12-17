<?php 
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    header("Location: index.html?error=No%20has%20iniciado%20sesión");
    exit();
}

// la conexión a la base de datos
include 'php/conexion.php';

// Obtener el municipio de la sesión
$municipio = $_SESSION["municipio"];

// parámetros de búsqueda
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');

// área y clasificación de la URL
$area_nombre = isset($_GET['area']) ? trim($_GET['area']) : null;
$clasificacion_codigo = isset($_GET['clasificacion']) ? trim($_GET['clasificacion']) : null;

// página actual
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

// Modificar la consulta para no filtrar por usuario, solo por municipio y otros parámetros
$query = "SELECT 
            f.id AS formato_id, 
            f.municipio, 
            f.anio, 
            f.ruta_archivo, 
            f515.no, 
            f515.poliza, 
            f515.vigencia_del, 
            f515.vigencia_al,
            f515.descripcion,
            f515.aseguradora, 
            f515.observaciones, 
            f515.informacion_al, 
            f515.responsable, 
            u.usuario AS nombre_usuario
            FROM 
            formatos f
            JOIN 
            formato_5_15 f515 ON f.id = f515.formato_id
            JOIN 
            usuarios u ON f.usuarios_id = u.id
            WHERE 
            f.municipio = ? 
            AND f.anio = ?";

// Inicializar polizas y parámetros para bind_param
$types = "si"; // s: string, i: integer
$params = [$municipio, $anio];

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
    $query .= " AND (f515.no LIKE ? OR f515.poliza LIKE ?)";
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

// Obtener el aseguradora de registros para calcular el número de páginas
$query_count = "SELECT COUNT(*) as aseguradora 
                FROM formatos f
                JOIN formato_5_15 f515 ON f.id = f515.formato_id
                JOIN usuarios u ON f.usuarios_id = u.id
                WHERE f.municipio = ? 
                AND f.anio = ?";

$types_count = "si";
$params_count = [$municipio, $anio];

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
    $query_count .= " AND (f515.no LIKE ? OR f515.poliza LIKE ?)";
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
$aseguradora_registros = $result_count->fetch_assoc()['aseguradora'];
$aseguradora_paginas = ceil($aseguradora_registros / $registros_por_pagina);

$stmt_count->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEPARTAMENTOS</title>
    <link rel="stylesheet" href="css/mostrarArchivos.css">
    <link rel="stylesheet" href="css/modal.css">
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
        <form method="get" action="mostrarregistros5_15.php"> <!-- Cambiar a un archivo específico para 5_11 si es necesario -->
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

    <!-- Botón para el PDF de TDP -->
    <div class="imprimir-pdf-btn">
        <button onclick="abrirModalTDP()">IMPRIMIR PDF - TDP</button>
    </div>

    <!-- Modal para TDP -->
    <div id="modalPDFTDP" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalTDP()">&times;</span>
            <h2>Introduzca el nombre de las personas autorizadas para firmar y cualquier observación pertinente</h2>

            <label for="elaboroTDP">Nombre de quien Elaboró:
                <span class="tooltip">?
                    <span class="tooltip-text">Ingrese el nombre completo de la persona que elaboró el documento.</span>
                </span>
            </label>
            <input type="text" id="elaboroTDP" spellcheck="true" required>

            <label for="autorizoTDP">Nombre de quien Autorizó:
                <span class="tooltip">?
                    <span class="tooltip-text">Ingrese el nombre completo del representante legal de la empresa.</span>
                </span>
            </label>
            <input type="text" id="autorizoTDP" spellcheck="true" required>

            <label for="supervisoTDP">Nombre de quien Supervisó:
                <span class="tooltip">?
                    <span class="tooltip-text">Ingrese el nombre completo de la persona que supervisó el documento.</span>
                </span>
            </label>
            <input type="text" id="supervisoTDP" spellcheck="true" required>

            <label for="observacionesTDP">Observaciones:
                <span class="tooltip">?
                    <span class="tooltip-text">Ingrese cualquier información adicional relevante.</span>
                </span>
            </label>
            <textarea id="observacionesTDP" rows="4" placeholder="Ingrese observaciones aquí..."></textarea>

            <label for="informacion">¿Contiene Información?</label>
            <select id="informacion" name="informacion">
                <option value="">Selecciona una opción (opcional)</option>
                <option value="NO APLICA">NO APLICA</option>
                <option value="PENDIENTE DE INFORMACIÓN">PENDIENTE DE INFORMACIÓN</option>
                <option value="NO SE PROPORCIONÓ INFORMACIÓN">NO SE PROPORCIONÓ INFORMACIÓN</option>
            </select>

            <button onclick="generarPDFTDP()">Generar PDF</button>
        </div>
    </div>

    <!-- Botón para el PDF de Municipio -->
    <div class="imprimir-pdf-btn">
        <button onclick="abrirModalMunicipio()">IMPRIMIR PDF - MUNICIPIO</button>
    </div>

    <!-- Modal para Municipio -->
    <div id="modalPDFMunicipio" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalMunicipio()">&times;</span>
            <h2>Introduzca el nombre de las personas autorizadas para firmar y cualquier observación pertinente</h2>

            <label for="elaboroMunicipio">Nombre de quien Entrega:
                <span class="tooltip">?
                    <span class="tooltip-text">El nombre completo, el cargo y la firma del servidor público responsable de la entrega (Presidente Municipal).</span>
                </span>
            </label>
            <input type="text" id="elaboroMunicipio" spellcheck="true" required>

            <label for="autorizoMunicipio">Nombre de quien Recibe:
                <span class="tooltip">?
                    <span class="tooltip-text">El nombre completo y la firma de la persona designada para recibir.</span>
                </span>
            </label>
            <input type="text" id="autorizoMunicipio" spellcheck="true" required>

            <label for="observacionesMunicipio">Observaciones:
                <span class="tooltip">?
                    <span class="tooltip-text">Ingrese cualquier información adicional relevante.</span>
                </span>
            </label>
            <textarea id="observacionesMunicipio" rows="4" placeholder="Ingrese observaciones aquí..."></textarea>

            <label for="informacion_municipio">¿Contiene Información?</label>
            <select id="informacion_municipio" name="informacion_municipio">
                <option value="">Selecciona una opción (opcional)</option>
                <option value="NO APLICA">NO APLICA</option>
                <option value="PENDIENTE DE INFORMACIÓN">PENDIENTE DE INFORMACIÓN</option>
                <option value="NO SE PROPORCIONÓ INFORMACIÓN">NO SE PROPORCIONÓ INFORMACIÓN</option>
            </select>

            <button onclick="generarPDFMunicipio()">Generar PDF</button>
        </div>
    </div>

    <script>
        // Función para convertir texto a mayúsculas antes de enviarlo
        function obtenerTextoEnMayusculas(elementId) {
            return document.getElementById(elementId).value.trim().toUpperCase();
        }

        // Funciones para el modal de TDP
        function abrirModalTDP() {
            document.getElementById("modalPDFTDP").style.display = "block";
        }

        function cerrarModalTDP() {
            document.getElementById("modalPDFTDP").style.display = "none";
        }

        function generarPDFTDP() {
            const elaboro = obtenerTextoEnMayusculas('elaboroTDP');
            const autorizo = obtenerTextoEnMayusculas('autorizoTDP');
            const superviso = obtenerTextoEnMayusculas('supervisoTDP');
            const observaciones = obtenerTextoEnMayusculas('observacionesTDP');

            // Obtener la opción seleccionada del select (si la tabla no tiene datos)
            let opcionSeleccionada = obtenerTextoEnMayusculas('informacion');

            // Si no se selecciona ninguna opción, asignar un valor predeterminado vacío
            opcionSeleccionada = opcionSeleccionada || "";

            const search = "<?php echo addslashes($search); ?>";
            const anio = "<?php echo addslashes($anio); ?>";
            const area = "<?php echo addslashes($area_nombre); ?>";
            const clasificacion = "<?php echo addslashes($clasificacion_codigo); ?>";

            // Construir URL con parámetros
            const url = `php/generarPDF5_15.php?search=${encodeURIComponent(search)}&anio=${encodeURIComponent(anio)}&area=${encodeURIComponent(area)}&clasificacion=${encodeURIComponent(clasificacion)}&elaboro=${encodeURIComponent(elaboro)}&autorizo=${encodeURIComponent(autorizo)}&superviso=${encodeURIComponent(superviso)}&observaciones=${encodeURIComponent(observaciones)}&opcion=${encodeURIComponent(opcionSeleccionada)}`;

            window.open(url, '_blank');
            cerrarModalTDP();
        }

        // Funciones para el modal de Municipio
        function abrirModalMunicipio() {
            document.getElementById("modalPDFMunicipio").style.display = "block";
        }

        function cerrarModalMunicipio() {
            document.getElementById("modalPDFMunicipio").style.display = "none";
        }

        function generarPDFMunicipio() {
            const elaboro = obtenerTextoEnMayusculas("elaboroMunicipio");
            const autorizo = obtenerTextoEnMayusculas("autorizoMunicipio");
            const observaciones = obtenerTextoEnMayusculas("observacionesMunicipio");

            // Obtener la opción seleccionada del select (si la tabla no tiene datos)
            let opcionSeleccionada = obtenerTextoEnMayusculas('informacion_municipio');

            // Si no se selecciona ninguna opción, asignar un valor predeterminado vacío
            opcionSeleccionada = opcionSeleccionada || "";


            const search = "<?php echo addslashes($search); ?>";
            const anio = "<?php echo addslashes($anio); ?>";
            const area = "<?php echo addslashes($area_nombre); ?>";
            const clasificacion = "<?php echo addslashes($clasificacion_codigo); ?>";

            const url = `php/generarPDF5_15_municipio.php?search=${encodeURIComponent(search)}&anio=${encodeURIComponent(anio)}&area=${encodeURIComponent(area)}&clasificacion=${encodeURIComponent(clasificacion)}&elaboro=${encodeURIComponent(elaboro)}&autorizo=${encodeURIComponent(autorizo)}&observaciones=${encodeURIComponent(observaciones)}&opcion=${encodeURIComponent(opcionSeleccionada)}`;

            window.open(url, '_blank');
            cerrarModalMunicipio();
        }

        // Cerrar el modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modalTDP = document.getElementById("modalPDFTDP");
            const modalMunicipio = document.getElementById("modalPDFMunicipio");
            if (event.target == modalTDP) {
                cerrarModalTDP();
            } else if (event.target == modalMunicipio) {
                cerrarModalMunicipio();
            }
        }
    </script>

    <?php
    // Mostrar los resultados filtrados
    if ($result->num_rows > 0) {
        echo "<div class='tabla-contenedor'>"; // Div para contener la tabla
        echo "<table>
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>POLIZA</th>
                        <th>VIGENCIA DEL</th>
                        <th>VIGENCIA AL</th>
                        <th>DESCRIPCIÓN</th>
                        <th>ASEGURADORA</th>
                        <th>OBSERVACIONES</th>
                        <th>INFORMACIÓN Al</th>
                        <th>RESPONSABLE</th>
                        <th>ACCIONES</th>
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
                    <td>" . htmlspecialchars($row['poliza']) . "</td>
                    <td>" . htmlspecialchars($row['vigencia_del']) . "</td>
                    <td>" . htmlspecialchars($row['vigencia_al']) . "</td>
                    <td>" . htmlspecialchars($row['descripcion']) . "</td>
                    <td>" . htmlspecialchars($row['aseguradora']) . "</td>
                    <td>" . htmlspecialchars($row['observaciones']) . "</td>
                    <td>" . htmlspecialchars($row['informacion_al']) . "</td>
                    <td>" . htmlspecialchars($row['responsable']) . "</td>
                    <td class='acciones'>
                        <a href='php/editarRegistro5_15.php?id=" . urlencode($row['formato_id']) . "&$query_string'>Editar</a> | 
                        <a href='php/eliminarRegistro5_15.php?id=" . urlencode($row['formato_id']) . "&$query_string' class='boton-eliminar' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este registro?\");'> Eliminar</a>
                    </td>
                    </tr>";
        }
        echo "</tbody></table>";
        echo "</div>"; // Cerrar el div de la tabla

        // Paginación
        if ($aseguradora_paginas > 1) {
            echo "<div class='paginacion'>";

            // Enlaces de navegación: Primero, Anterior
            if ($pagina_actual > 1) {
                echo "<a href='?pagina=1&search=" . urlencode($search) . "&anio=" . urlencode($anio) . "&area=" . urlencode($area_nombre) . "&clasificacion=" . urlencode($clasificacion_codigo) . "'>&laquo; Primero</a>";
                echo "<a href='?pagina=" . ($pagina_actual - 1) . "&search=" . urlencode($search) . "&anio=" . urlencode($anio) . "&area=" . urlencode($area_nombre) . "&clasificacion=" . urlencode($clasificacion_codigo) . "'>&lsaquo; Anterior</a>";
            }

            // Mostrar números de página alrededor de la página actual
            $limite = 2; // Número de páginas a mostrar a cada lado de la actual
            $inicio = max(1, $pagina_actual - $limite);
            $fin = min($aseguradora_paginas, $pagina_actual + $limite);

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

            if ($fin < $aseguradora_paginas) {
                echo "<span>...</span>";
            }

            // Enlace Siguiente, Último
            if ($pagina_actual < $aseguradora_paginas) {
                echo "<a href='?pagina=" . ($pagina_actual + 1) . "&search=" . urlencode($search) . "&anio=" . urlencode($anio) . "&area=" . urlencode($area_nombre) . "&clasificacion=" . urlencode($clasificacion_codigo) . "'>Siguiente &rsaquo;</a>";
                echo "<a href='?pagina=" . $aseguradora_paginas . "&search=" . urlencode($search) . "&anio=" . urlencode($anio) . "&area=" . urlencode($area_nombre) . "&clasificacion=" . urlencode($clasificacion_codigo) . "'>Último &raquo;</a>";
            }

            echo "</div>"; // Cerrar el div de paginación
        }
    } else {
        echo "<div class='mensaje'>No se encontraron registros.</div>";
    }
    ?>

</body>
</html>