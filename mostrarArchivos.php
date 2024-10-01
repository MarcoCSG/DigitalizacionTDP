<?php
session_start();

// Verificar si el usuario ha iniciado sesión y tiene un municipio asignado
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    header("Location: index.html?error=No%20has%20iniciado%20sesión");
    exit();
}

// Incluir la conexión a la base de datos
include 'php/conexion.php';

// Obtener el nombre de usuario y municipio de la sesión
$usuario = $_SESSION["usuario"];
$municipio = $_SESSION["municipio"];

// Obtener parámetros de la URL
$subclasificacion = isset($_GET['subclasificacion']) ? $_GET['subclasificacion'] : '';
$clasificacion = isset($_GET['clasificacion']) ? $_GET['clasificacion'] : '';
$area = isset($_GET['area']) ? $_GET['area'] : '';
$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Escapar valores para prevenir inyecciones SQL
$subclasificacion = mysqli_real_escape_string($conexion, $subclasificacion);
$clasificacion = mysqli_real_escape_string($conexion, $clasificacion);
$area = mysqli_real_escape_string($conexion, $area);
$periodo = mysqli_real_escape_string($conexion, $periodo);
$search = mysqli_real_escape_string($conexion, $search);

// Función para obtener el ID de área
function getAreaId($conexion, $area)
{
    $stmt = $conexion->prepare("SELECT id FROM areas WHERE nombre = ?");
    $stmt->bind_param("s", $area);
    $stmt->execute();
    $stmt->bind_result($area_id);
    $stmt->fetch();
    $stmt->close();
    return $area_id;
}

// Función para obtener el ID de clasificación
function getClasificacionId($conexion, $clasificacion, $area_id)
{
    $stmt = $conexion->prepare("SELECT id FROM clasificaciones WHERE codigo = ? AND area_id = ?");
    $stmt->bind_param("si", $clasificacion, $area_id);
    $stmt->execute();
    $stmt->bind_result($clasificacion_id);
    $stmt->fetch();
    $stmt->close();
    return $clasificacion_id;
}

// Obtener IDs necesarios
$area_id = getAreaId($conexion, $area);
$clasificacion_id = getClasificacionId($conexion, $clasificacion, $area_id);

// Obtener el ID del usuario
$stmt = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->bind_result($usuario_id);
$stmt->fetch();
$stmt->close();

// Verificar si los IDs fueron obtenidos correctamente
if (!$area_id || !$clasificacion_id || !$usuario_id) {
    echo "<p>Parámetros inválidos o no encontrados.</p>";
    exit();
}

// Construir la consulta principal para obtener los formatos filtrados
$query = "SELECT f.id, f12.no, f12.denominacion, f12.publicacion_fecha, f12.informacion_al, f12.fecha_autorizacion, f12.responsable, f12.observaciones, f.ruta_archivo
          FROM formatos f
          JOIN formato_1_2 f12 ON f.id = f12.formato_id
          WHERE f.clasificaciones_id = ? AND f.municipio = ? AND f.usuarios_id = ? AND f12.subclasificacion = ? AND f12.clasificacion = ?";

// Añadir condiciones adicionales
$params = array();
$types = "iisss";
$params[] = $clasificacion_id;
$params[] = $municipio;
$params[] = $usuario_id;
$params[] = $subclasificacion;
$params[] = $clasificacion;

// Manejar la búsqueda
if (!empty($search)) {
    $query .= " AND f12.denominacion LIKE ?";
    $types .= "s";
    $params[] = "%" . $search . "%";
}

// Preparar la consulta
$stmt = $conexion->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Función para generar enlaces de edición y eliminación
function generarEnlacesAcciones($id, $area, $clasificacion, $subclasificacion, $periodo)
{
    $editar = "<a href='editarRegistro.php?id={$id}&area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&subclasificacion=" . urlencode($subclasificacion) . "&periodo=" . urlencode($periodo) . "'>Editar</a>";
    $eliminar = "<a href='eliminarRegistro.php?id={$id}' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este registro?\");'>Eliminar</a>";
    return "{$editar} | {$eliminar}";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mostrar Registros</title>
    <link rel="stylesheet" href="css/mostrarArchivos.css">
    <link rel="icon" href="img/TDP-REDONDO.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Agrega aquí tus estilos personalizados o utiliza el archivo CSS existente */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .search-container {
            margin-top: 20px;
        }

        .search-container input[type="text"] {
            padding: 8px;
            width: 200px;
        }

        .search-container button {
            padding: 8px 12px;
        }

        .paginacion a {
            margin: 0 5px;
            padding: 8px 12px;
            text-decoration: none;
            background-color: #f2f2f2;
            color: #333;
            border-radius: 4px;
        }

        .paginacion a.selected {
            background-color: #4CAF50;
            color: white;
        }

        .totalFolios {
            margin-top: 20px;
            font-weight: bold;
        }

        .boton-imprimir {
            background-color: #4CAF50;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
        }

        .boton-imprimir:hover {
            background-color: #45a049;
        }

        .boton-eliminar {
            background-color: #f44336;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .boton-eliminar:hover {
            background-color: #da190b;
        }

        .icon {
            width: 20px;
            vertical-align: middle;
            margin-right: 5px;
        }

        .boton-imprimir {
            background-color: #4CAF50;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }

        .boton-imprimir:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <main>
        <h1 class="titulo">DIGITALIZACIÓN E-GOBIERNO</h1>
    </main>
    <section>
        <figure>
            <img class="imgEmpresa" src="img/logoTDP.png" alt="Logo Empresa">
        </figure>
    </section>

    <div class="search-container">
        <form method="get" action="mostrarRegistros.php">
            <input type="hidden" name="subclasificacion" value="<?php echo htmlspecialchars($subclasificacion); ?>">
            <input type="hidden" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion); ?>">
            <input type="hidden" name="area" value="<?php echo htmlspecialchars($area); ?>">
            <input type="hidden" name="periodo" value="<?php echo htmlspecialchars($periodo); ?>">
            <input type="text" name="search" placeholder="Buscar archivo" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">
                <i class="fas fa-search"></i> Buscar
            </button>
        </form>
    </div>

    <!-- Mensaje de resumen -->
    <p>En el área de <strong><?php echo htmlspecialchars($area); ?></strong>, en el rubro de <strong><?php echo htmlspecialchars($clasificacion); ?></strong>, de la clasificación <strong><?php echo htmlspecialchars($subclasificacion); ?></strong>, en el periodo <strong><?php echo htmlspecialchars($periodo); ?></strong>.</p>
    <button onclick="window.print()" class="boton-imprimir"><i class="fas fa-print"></i> Imprimir Tabla</button>

    <?php
    // Verificar si hay resultados
    if ($result->num_rows > 0) {
    ?>
        <table>
            <thead>
                <tr>
                    <th>Nombre del Archivo PDF</th>
                    <th>Cantidad de Folios</th>
                    <th class="ver-col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($fila = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><img src='img/nube.png' alt='Icono PDF' class='icon'><a href='descargar.php?id=" . $fila['id'] . "' target='_blank'>" . htmlspecialchars(basename($fila['ruta_archivo'])) . "</a></td>";
                    echo "<td>" . htmlspecialchars($fila['cantidad_folios']) . "</td>";
                    echo "<td class='ver-col'>
                            <a class='boton-imprimir' href='descargar.php?id=" . $fila['id'] . "' target='_blank'><i class='fas fa-print'></i> Imprimir</a> |
                            " . generarEnlacesAcciones($fila['id'], $area, $clasificacion, $subclasificacion, $periodo) . "
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    <?php
    } else {
        echo "<p>No se encontraron registros.</p>";
    }

    // Cerrar la declaración y conexión
    $stmt->close();
    $conexion->close();
    ?>

    <script src="js/inactividad.js"></script>
</body>

</html>