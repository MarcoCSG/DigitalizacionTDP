<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    header("Location: index.html");
    exit();
}

// Incluir la conexión a la base de datos
include 'php/conexion.php';

// Obtener el usuario y municipio de la sesión
$usuario = $_SESSION["usuario"];
$municipio = $_SESSION["municipio"];

// Obtener parámetros de búsqueda
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Corregir la obtención del año
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y'); // Correcto

// Filtrar funciones o datos basados en el año
echo "<h1>Año: $anio</h1>";
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
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #ddd;
        }
        .search-container {
            margin-bottom: 20px;
        }
        .search-container form {
            display: flex;
            align-items: center;
        }
        .search-container input[type="text"] {
            padding: 5px;
            width: 200px;
            margin-right: 10px;
        }
        .search-container button {
            padding: 5px 10px;
        }
    </style>
</head>
<body>
    <h1 class="titulo">DIGITALIZACIÓN E-GOBIERNO</h1>
    <section>
        <figure>
            <img class="imgEmpresa" src="img/logoTDP.png" alt="Logo Empresa">
        </figure>
    </section>
    <!-- Campo oculto para preservar el año -->
    <input type="hidden" name="anio" value="<?php echo htmlspecialchars($anio); ?>">

    <!-- Barra de búsqueda -->
    <div class="search-container">
        <form method="get" action="mostrarRegistros.php">
            <input type="text" name="search" placeholder="Buscar archivo" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">
                <i class="fas fa-search"></i> Buscar
            </button>
        </form>
    </div>

    <h2>Mostrar Registros</h2>

    <?php
    // Preparar la consulta para obtener los registros filtrados
    $query = "SELECT 
                f.id AS formato_id, 
                f.municipio, 
                f.anio, 
                f.ruta_archivo, 
                f1.no, 
                f1.denominacion, 
                f1.publicacion_fecha, 
                f1.informacion_al, 
                f1.fecha_autorizacion, 
                f1.responsable, 
                f1.observaciones, 
                u.usuario AS nombre_usuario
            FROM 
                formatos f
            JOIN 
                formato_1_2 f1 ON f.id = f1.formato_id
            JOIN 
                usuarios u ON f.usuarios_id = u.id
            WHERE 
                u.usuario = ? AND 
                f.municipio = ? AND 
                f.anio = ?"; // Añadir filtro por año
    
    // Si hay búsqueda, añadir condiciones adicionales
    if (!empty($search)) {
        $query .= " AND (f1.denominacion LIKE ? OR f1.responsable LIKE ?)";
    }

    $query .= " ORDER BY f.fecha_creacion DESC";

    // Preparar la consulta
    $stmt = $conexion->prepare($query);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    // Bind de parámetros
    if (!empty($search)) {
        $search_param = "%" . $search . "%";
        // 'ssiss' corresponde a: usuario, municipio, año, deno LIKE, resp LIKE
        $stmt->bind_param("ssiss", $usuario, $municipio, $anio, $search_param, $search_param);
    } else {
        // 'ssi' corresponde a: usuario, municipio, año
        $stmt->bind_param("ssi", $usuario, $municipio, $anio);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Mostrar los resultados filtrados
    if ($result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>No.</th>
                    <th>Denominación</th>
                    <th>Fecha de Publicación</th>
                    <th>Información Al</th>
                    <th>Fecha de Autorización</th>
                    <th>Responsable</th>
                    <th>Observaciones</th>
                    <th>Acciones</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['no']) . "</td>
                    <td>" . htmlspecialchars($row['denominacion']) . "</td>
                    <td>" . htmlspecialchars($row['publicacion_fecha']) . "</td>
                    <td>" . htmlspecialchars($row['informacion_al']) . "</td>
                    <td>" . htmlspecialchars($row['fecha_autorizacion']) . "</td>
                    <td>" . htmlspecialchars($row['responsable']) . "</td>
                    <td>" . htmlspecialchars($row['observaciones']) . "</td>";
                    
            echo "</td>
                    <td>
                        <a href='php/editarRegistro.php?id=" . urlencode($row['formato_id']) . "'>Editar</a> | 
                        <a href='php/eliminarRegistro.php?id=" . urlencode($row['formato_id']) . "' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este registro?\");'>Eliminar</a> | 
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No se encontraron registros con los criterios seleccionados.</p>";
    }

    $stmt->close();
    $conexion->close();
    ?>
</body>
</html>
