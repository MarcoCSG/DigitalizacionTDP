<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    header("Location: index.html?error=No%20has%20iniciado%20sesión");
    exit();
}

// Incluir la conexión a la base de datos
include 'php/conexion.php';

// Obtener el ID del registro a editar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$area = isset($_GET['area']) ? $_GET['area'] : '';
$clasificacion = isset($_GET['clasificacion']) ? $_GET['clasificacion'] : '';

// Validar parámetros
if ($id <= 0 || empty($area) || empty($clasificacion)) {
    echo "<p>Parámetros inválidos.</p>";
    exit();
}

// Obtener detalles del registro
$query = "SELECT f.id, f12.no, f12.denominacion, f12.publicacion_fecha, f12.informacion_al, f12.fecha_autorizacion, f12.responsable, f12.observaciones, f.ruta_archivo
          FROM formatos f
          JOIN formato_1_2 f12 ON f.id = f12.formato_id
          WHERE f.id = ? AND f.clasificaciones_id = (
              SELECT id FROM clasificaciones WHERE codigo = ? AND area_id = (
                  SELECT id FROM areas WHERE nombre = ?
              )
          ) AND f.municipio = ? AND f.usuarios_id = ?";

$stmt = $conexion->prepare($query);
$stmt->bind_param("isssi", $id, $clasificacion, $area, $_SESSION["municipio"], $_SESSION["usuario_id"]);
$stmt->execute();
$result = $stmt->get_result();
$registro = $result->fetch_assoc();
$stmt->close();

if (!$registro) {
    echo "<p>Registro no encontrado o no autorizado.</p>";
    exit();
}

// Procesar el formulario al guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar y sanitizar datos del formulario
    $no = intval($_POST['no']);
    $denominacion = trim($_POST['denominacion']);
    $publicacion_fecha = $_POST['publicacion_fecha'];
    $informacion_al = trim($_POST['informacion_al']);
    $fecha_autorizacion = $_POST['fecha_autorizacion'];
    $responsable = trim($_POST['responsable']);
    $observaciones = trim($_POST['observaciones']);

    // Validaciones básicas
    $errores = [];
    if ($no <= 0) {
        $errores[] = "El número debe ser un valor positivo.";
    }
    if (empty($denominacion)) {
        $errores[] = "La denominación es obligatoria.";
    }
    if (empty($publicacion_fecha)) {
        $errores[] = "La fecha de publicación es obligatoria.";
    }
    if (empty($informacion_al)) {
        $errores[] = "La información al es obligatoria.";
    }
    if (empty($fecha_autorizacion)) {
        $errores[] = "La fecha de autorización es obligatoria.";
    }
    if (empty($responsable)) {
        $errores[] = "El responsable es obligatorio.";
    }

    if (count($errores) === 0) {
        // Actualizar las tablas 'formatos' y 'formato_1_2'
        $conexion->begin_transaction();

        try {
            // Actualizar la tabla 'formatos' si es necesario (por ejemplo, ruta_archivo)
            // Si deseas permitir la actualización del archivo, implementa aquí la lógica para manejar la carga y actualización del archivo.

            // Actualizar la tabla 'formato_1_2'
            $updateQuery = "UPDATE formato_1_2 SET no = ?, denominacion = ?, publicacion_fecha = ?, informacion_al = ?, fecha_autorizacion = ?, responsable = ?, observaciones = ? WHERE formato_id = ?";
            $stmt = $conexion->prepare($updateQuery);
            $stmt->bind_param("issssssi", $no, $denominacion, $publicacion_fecha, $informacion_al, $fecha_autorizacion, $responsable, $observaciones, $id);
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar formato_1_2: " . $stmt->error);
            }
            $stmt->close();

            // Confirmar la transacción
            $conexion->commit();

            echo "<script>alert('Registro actualizado exitosamente.'); window.location.href = 'mostrarRegistros.php?area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&subclasificacion=" . urlencode($subclasificacion) . "&periodo=" . urlencode($periodo) . "';</script>";
        } catch (Exception $e) {
            // Deshacer la transacción en caso de error
            $conexion->rollback();
            echo "<p>Error: " . $e->getMessage() . "</p>";
        }
    } else {
        // Mostrar errores
        foreach ($errores as $error) {
            echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Registro</title>
    <link rel="stylesheet" href="css/mostrarArchivos.css">
    <link rel="icon" href="img/TDP-REDONDO.png" type="image/x-icon">
    <style>
        .form-container {
            margin: 20px;
        }

        .form-container input[type="text"],
        .form-container input[type="date"],
        .form-container textarea {
            width: 100%;
            padding: 8px;
            margin: 6px 0 12px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }

        a {
            text-decoration: none;
            color: #4CAF50;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Editar Registro</h2>
        <form method="POST">
            <label for="no">No.</label>
            <input type="number" id="no" name="no" value="<?php echo htmlspecialchars($registro['no']); ?>" required>

            <label for="denominacion">Denominación</label>
            <input type="text" id="denominacion" name="denominacion" value="<?php echo htmlspecialchars($registro['denominacion']); ?>" required>

            <label for="publicacion_fecha">Fecha de Publicación</label>
            <input type="date" id="publicacion_fecha" name="publicacion_fecha" value="<?php echo htmlspecialchars($registro['publicacion_fecha']); ?>" required>

            <label for="informacion_al">Información Al</label>
            <input type="text" id="informacion_al" name="informacion_al" value="<?php echo htmlspecialchars($registro['informacion_al']); ?>" required>

            <label for="fecha_autorizacion">Fecha de Autorización</label>
            <input type="date" id="fecha_autorizacion" name="fecha_autorizacion" value="<?php echo htmlspecialchars($registro['fecha_autorizacion']); ?>" required>

            <label for="responsable">Responsable</label>
            <input type="text" id="responsable" name="responsable" value="<?php echo htmlspecialchars($registro['responsable']); ?>" required>

            <label for="observaciones">Observaciones</label>
            <textarea id="observaciones" name="observaciones"><?php echo htmlspecialchars($registro['observaciones']); ?></textarea>

            <input type="submit" value="Guardar Cambios">
        </form>
        <br>
        <a href="mostrarRegistros.php?area=<?php echo urlencode($area); ?>&clasificacion=<?php echo urlencode($clasificacion); ?>&subclasificacion=<?php echo urlencode($subclasificacion); ?>&periodo=<?php echo urlencode($periodo); ?>">Volver a Registros</a>
    </div>
</body>

</html>
