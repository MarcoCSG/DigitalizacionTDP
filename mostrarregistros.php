<?php
session_start();

// Verificar si el usuario ha iniciado sesión y tiene un municipio asignado
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    // Redirigir al inicio de sesión si no está logueado
    header("Location: index.html");
    exit();
}

$usuario = $_SESSION["usuario"];
$municipio = $_SESSION["municipio"];

// Verificar si se envió la solicitud de búsqueda
$selected_area = isset($_GET['area']) ? $_GET['area'] : '';
$selected_clasificacion = isset($_GET['clasificacion']) ? $_GET['clasificacion'] : '';

// Incluir la conexión a la base de datos
include 'php/conexion.php';

// Obtener todas las áreas disponibles
$areas = [];
$stmt_area = $conexion->prepare("SELECT DISTINCT municipio FROM formatos WHERE municipio = ?");
$stmt_area->bind_param("s", $municipio);
$stmt_area->execute();
$result_area = $stmt_area->get_result();
while ($row = $result_area->fetch_assoc()) {
    $areas[] = $row['municipio'];
}
$stmt_area->close();
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
    </style>
</head>
<body>
    <h1 class="titulo">DIGITALIZACIÓN E-GOBIERNO</h1>
    <section>
        <figure>
            <img class="imgEmpresa" src="img/logoTDP.png" alt="Logo Empresa">
        </figure>
    </section>

    <h2>Mostrar Registros</h2>

    <div class="search-container">
        <form method="GET" action="mostrarRegistros.php">
            <input type="hidden" name="area" id="area" value="<?php echo htmlspecialchars($selected_area); ?>">
            <input type="hidden" name="clasificacion" id="clasificacion" value="<?php echo htmlspecialchars($selected_clasificacion); ?>">
            <button type="submit">
                <ion-icon name="search-outline"></ion-icon> Buscar
            </button>
        </form>
    </div>

    <?php
    // Realizar la búsqueda si se han seleccionado área y clasificación
    if (!empty($selected_area) && !empty($selected_clasificacion)) {
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
                    c.codigo AS clasificacion, 
                    u.usuario AS nombre_usuario
                FROM 
                    formatos f
                JOIN 
                    formato_1_2 f1 ON f.id = f1.formato_id
                JOIN 
                    clasificaciones c ON f.clasificaciones_id = c.id
                JOIN 
                    usuarios u ON f.usuarios_id = u.id
                WHERE 
                    u.usuario = ? AND 
                    f.municipio = ? AND 
                    c.codigo = ?
                ORDER BY 
                    f.fecha_creacion DESC";

        $stmt = $conexion->prepare($query);
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conexion->error);
        }
        $stmt->bind_param("sss", $usuario, $selected_area, $selected_clasificacion);
        $stmt->execute();
        $result = $stmt->get_result();

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
                        <th>Clasificación</th>
                        <th>Archivo</th>
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
                        <td>" . htmlspecialchars($row['observaciones']) . "</td>
                        <td>" . htmlspecialchars($row['clasificacion']) . "</td>
                        <td>";
                if ($row['ruta_archivo']) {
                    echo "<a href='" . htmlspecialchars($row['ruta_archivo']) . "' target='_blank'>Ver Archivo</a>";
                } else {
                    echo "No hay archivo";
                }
                echo "</td>
                        <td>
                            <a href='php/editarRegistro.php?id=" . $row['formato_id'] . "'>Editar</a> | 
                            <a href='php/eliminarRegistro.php?id=" . $row['formato_id'] . "' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este registro?\");'>Eliminar</a> | 
                            <a href='#' onclick='window.print();'>Imprimir</a>
                        </td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No se encontraron registros con los criterios seleccionados.</p>";
        }

        $stmt->close();
    }
    ?>
</body>
</html>
