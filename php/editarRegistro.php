<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    header("Location: ../index.html?error=No%20has%20iniciado%20sesión");
    exit();
}

// Incluir la conexión a la base de datos
include 'conexion.php';

// Obtener el ID del registro a editar y los filtros de la URL
if (!isset($_GET['id'])) {
    die("ID no proporcionado.");
}

$id = intval($_GET['id']);

// Capturar los filtros adicionales de la URL
$area = isset($_GET['area']) ? $_GET['area'] : '';
$clasificacion = isset($_GET['clasificacion']) ? $_GET['clasificacion'] : '';
$anio = isset($_GET['anio']) ? $_GET['anio'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Obtener los datos actuales del registro
$stmt = $conexion->prepare("SELECT f1.no, f1.denominacion, f1.publicacion_fecha, f1.informacion_al, f1.fecha_autorizacion, f1.responsable, f1.observaciones FROM formato_1_2 f1 JOIN formatos f ON f1.formato_id = f.id WHERE f1.formato_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Registro no encontrado.");
}

$registro = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar y sanitizar datos del formulario
    $no = intval($_POST['no']);
    $denominacion = trim($_POST['denominacion']);

    $publicacion_fecha = $_POST['publicacion_fecha'];
    if ($publicacion_fecha) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $publicacion_fecha = date('d/m/Y', strtotime($publicacion_fecha));
    }

    $informacion_al = trim($_POST['informacion_al']);
    if ($informacion_al) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $informacion_al = date('d/m/Y', strtotime($informacion_al));
    }

    $fecha_autorizacion = trim($_POST['fecha_autorizacion']);
    if ($fecha_autorizacion) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $fecha_autorizacion = date('d/m/Y', strtotime($fecha_autorizacion));
    }
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
        // Preparar la consulta de actualización
        $stmt_update = $conexion->prepare("UPDATE formato_1_2 SET no = ?, denominacion = ?, publicacion_fecha = ?, informacion_al = ?, fecha_autorizacion = ?, responsable = ?, observaciones = ? WHERE formato_id = ?");
        $stmt_update->bind_param("issssssi", $no, $denominacion, $publicacion_fecha, $informacion_al, $fecha_autorizacion, $responsable, $observaciones, $id);
        
        if ($stmt_update->execute()) {
            // Redirigir con los mismos parámetros de la URL
            header("Location: ../mostrarRegistros.php?success=1&area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&anio=" . urlencode($anio) . "&search=" . urlencode($search));
            exit();
        } else {
            echo "<p style='color:red;'>Error al actualizar el registro: " . htmlspecialchars($stmt_update->error) . "</p>";
        }
        $stmt_update->close();
    } else {
        // Mostrar errores
        foreach ($errores as $error) {
            echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
        }
    }
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro</title>
    <link rel="stylesheet" href="../css/editarRegistros.css">
    <style>
        .imgEmpresa {
            height: 50px; /* Ajustar el tamaño según sea necesario */
            width: auto;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        form input[type="text"],
        form input[type="date"],
        form input[type="number"],
        form textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
<script>
        // Convertir todos los campos de texto a mayúsculas al enviar el formulario
        function convertirAMayusculas() {
            const inputs = document.querySelectorAll('input[type="text"], textarea');
            inputs.forEach(input => {
                input.value = input.value.toUpperCase(); // Convierte a mayúsculas antes de enviar
            });
        }
    </script>
</head>
<body>
    <div class="header">
        <h1 class="titulo">Editar Registro</h1>
        <img src="../img/logoTDP.png" alt="Logo Empresa" class="imgEmpresa">
    </div>
    <div class="form-container">

        <form method="post" action="" onsubmit="convertirAMayusculas()">
                <label for="no">No:
                        <span class="tooltip">?
                    <span class="tooltip-text">El número consecutivo de los documentos relacionados (1, 2, 3, etc.).</span>
                </span>
            </label>
            <input type="number" name="no" id="no" value="<?php echo htmlspecialchars($registro['no']); ?>" required>
            
            <label for="denominacion">Denominación:
                <span class="tooltip">?
                    <span class="tooltip-text">El nombre y tipo del documento de que se trate. Ejemplo: Manual General de Organización, Manual de Procedimientos de la Tesorería o Manual de Servicios.</span>
                </span>
            </label>
            <input type="text" name="denominacion" id="denominacion" spellcheck="true" value="<?php echo htmlspecialchars($registro['denominacion']); ?>" required>
            
            <label for="fecha_autorizacion">Fecha de Autorización:
                <span class="tooltip">?
                <span class="tooltip-text">El día, mes y año en que se autorizó el manual administrativo referido.</span>
            </span>
            </label>
            <input type="date" name="fecha_autorizacion" id="fecha_autorizacion" spellcheck="true" value="<?php echo htmlspecialchars($registro['fecha_autorizacion']); ?>" required>
            
            <label for="observaciones">Observaciones:
                <span class="tooltip">?
                <span class="tooltip-text">Los comentarios que se consideren importantes respecto a los manuales administrativos.</span>
            </span>
            </label>
            <textarea name="observaciones" id="observaciones" spellcheck="true" required><?php echo htmlspecialchars($registro['observaciones']); ?></textarea>
            
            <label for="publicacion_fecha">Fecha de Publicación:
                <span class="tooltip">?
                <span class="tooltip-text">El sitio de la publicación de los manuales y la fecha de la misma.</span>
            </span>
            </label>
            <input type="date" name="publicacion_fecha" id="publicacion_fecha" spellcheck="true" value="<?php echo htmlspecialchars($registro['publicacion_fecha']); ?>" required>
            
            <label for="informacion_al">Información Al:
                <span class="tooltip">?
                <span class="tooltip-text">El día, mes y año en que se actualizó la información de este formato. Ejemplo: 15 de diciembre de 2021.</span>
            </span>
            </label>
            <input type="date" name="informacion_al" id="informacion_al" spellcheck="true" value="<?php echo htmlspecialchars($registro['informacion_al']); ?>" required>
                
            <label for="responsable">Responsable:
                <span class="tooltip">?
                <span class="tooltip-text">El nombre y cargo del servidor público responsable de integrar la información, y en su caso del resguardo de la documentación soporte.</span>
            </span>
            </label>
            <input type="text" name="responsable" id="responsable" spellcheck="true" value="<?php echo htmlspecialchars($registro['responsable']); ?>" required>
            
            <button type="submit">Actualizar</button>
        </form>
        <a class="cancelar" href="../mostrarRegistros.php?area=<?php echo urlencode($area); ?>&clasificacion=<?php echo urlencode($clasificacion); ?>&anio=<?php echo urlencode($anio); ?>&search=<?php echo urlencode($search); ?>">Cancelar</a>
    </div>
</body>
</html>
