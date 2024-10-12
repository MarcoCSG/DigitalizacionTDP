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
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID no proporcionado o inválido.");
}

$id = intval($_GET['id']);

// Capturar los filtros adicionales de la URL
$area = isset($_GET['area']) ? $_GET['area'] : '';
$clasificacion = isset($_GET['clasificacion']) ? $_GET['clasificacion'] : '';
$anio = isset($_GET['anio']) ? $_GET['anio'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

// Obtener los datos actuales del registro
$stmt = $conexion->prepare("
    SELECT 
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
        f5.responsable 
    FROM 
        formato_5_11 f5 
    WHERE 
        f5.formato_id = ?
");
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Registro no encontrado.");
}

$registro = $result->fetch_assoc();
$stmt->close();

// Obtener listas para áreas (si es necesario)
$areas = [];

// Obtener todas las áreas (aunque no las vas a modificar)
$stmt_areas = $conexion->prepare("SELECT id, nombre FROM areas ORDER BY nombre ASC");
if (!$stmt_areas) {
    die("Error en la preparación de la consulta de áreas: " . $conexion->error);
}
$stmt_areas->execute();
$result_areas = $stmt_areas->get_result();
while ($row = $result_areas->fetch_assoc()) {
    $areas[] = $row;
}
$stmt_areas->close();

// No hay clasificaciones en la tabla, por lo tanto, omitimos esta sección

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar y sanitizar datos del formulario
    $no = isset($_POST['no']) ? intval($_POST['no']) : 0;
    $nombre_expediente = isset($_POST['nombre_expediente']) ? trim($_POST['nombre_expediente']) : '';
    $serie_documental = isset($_POST['serie_documental']) ? trim($_POST['serie_documental']) : '';
    $clave = isset($_POST['clave']) ? trim($_POST['clave']) : '';
    $descripcion_contenido = isset($_POST['descripcion_contenido']) ? trim($_POST['descripcion_contenido']) : '';
    $resguardado = isset($_POST['resguardado']) ? trim($_POST['resguardado']) : '';
    $confidencial = isset($_POST['confidencial']) ? trim($_POST['confidencial']) : '';
    $vigencia_documental = isset($_POST['vigencia_documental']) ? trim($_POST['vigencia_documental']) : '';
    $informacion_al = isset($_POST['informacion_al']) ? trim($_POST['informacion_al']) : '';
    $responsable = isset($_POST['responsable']) ? trim($_POST['responsable']) : '';

    // Validaciones básicas
    $errores = [];
    if ($no <= 0) {
        $errores[] = "El número debe ser un valor positivo.";
    }
    if (empty($nombre_expediente)) {
        $errores[] = "El nombre del expediente es obligatorio.";
    }
    if (empty($serie_documental)) {
        $errores[] = "La serie documental es obligatoria.";
    }
    if (empty($clave)) {
        $errores[] = "La clave es obligatoria.";
    }
    if (empty($descripcion_contenido)) {
        $errores[] = "La descripción del contenido es obligatoria.";
    }
    if (empty($responsable)) {
        $errores[] = "El responsable es obligatorio.";
    }

    if (count($errores) === 0) {
        // Preparar la consulta de actualización
        $stmt_update = $conexion->prepare("
            UPDATE 
                formato_5_11 
            SET 
                no = ?, 
                nombre_expediente = ?, 
                serie_documental = ?, 
                clave = ?, 
                descripcion_contenido = ?, 
                resguardado = ?, 
                confidencial = ?, 
                vigencia_documental = ?, 
                informacion_al = ?, 
                responsable = ? 
            WHERE 
                formato_id = ?
        ");
        if (!$stmt_update) {
            die("Error en la preparación de la consulta de actualización: " . $conexion->error);
        }
        $stmt_update->bind_param(
            "isssssssssi",
            $no,
            $nombre_expediente,
            $serie_documental,
            $clave,
            $descripcion_contenido,
            $resguardado,
            $confidencial,
            $vigencia_documental,
            $informacion_al,
            $responsable,
            $id
        );

        if ($stmt_update->execute()) {
            // Redirigir con los mismos parámetros de la URL
            header("Location: ../mostrarRegistros5_11.php?mensaje=Registro%20actualizado%20correctamente&area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&anio=" . urlencode($anio) . "&search=" . urlencode($search) . "&pagina=" . urlencode($pagina));
            exit();
        } else {
            $errores[] = "Error al actualizar el registro: " . htmlspecialchars($stmt_update->error);
        }
        $stmt_update->close();
    }
}
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Registro</title>
    <link rel="stylesheet" href="../css/editarRegistros.css">
    <style>
        .imgEmpresa {
            height: 50px;
            /* Ajustar el tamaño según sea necesario */
            width: auto;
        }

        .form-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 25px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        form input[type="text"],
        form input[type="number"],
        form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        form textarea {
            resize: vertical;
            height: 100px;
        }

        button {
            padding: 12px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #218838;
        }

        .cancelar {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .cancelar:hover {
            background-color: #5a6268;
        }

        .errores {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            color: #721c24;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 class="titulo">Editar Registro</h1>
        <img src="../img/logoTDP.png" alt="Logo Empresa" class="imgEmpresa">
    </div>
    <div class="form-container">
        <?php
        // Mostrar errores si existen
        if (isset($errores) && count($errores) > 0) {
            echo "<div class='errores'>";
            foreach ($errores as $error) {
                echo "<p>" . htmlspecialchars($error) . "</p>";
            }
            echo "</div>";
        }
        ?>
        <form method="post" action="">
            <label for="no">No:</label>
            <input type="number" name="no" id="no" value="<?php echo htmlspecialchars($registro['no']); ?>" required min="1">

            <label for="nombre_expediente">Nombre del Expediente:</label>
            <input type="text" name="nombre_expediente" id="nombre_expediente" value="<?php echo htmlspecialchars($registro['nombre_expediente']); ?>" required>

            <label for="serie_documental">Serie Documental:</label>
            <input type="text" name="serie_documental" id="serie_documental" value="<?php echo htmlspecialchars($registro['serie_documental']); ?>" required>

            <label for="clave">Clave:</label>
            <input type="text" name="clave" id="clave" value="<?php echo htmlspecialchars($registro['clave']); ?>" required>

            <label for="descripcion_contenido">Descripción del Contenido:</label>
            <textarea name="descripcion_contenido" id="descripcion_contenido" required><?php echo htmlspecialchars($registro['descripcion_contenido']); ?></textarea>

            <label for="resguardado">Resguardado:</label>
            <input type="text" name="resguardado" id="resguardado" value="<?php echo htmlspecialchars($registro['resguardado']); ?>">

            <label for="confidencial">Confidencial:</label>
            <input type="text" name="confidencial" id="confidencial" value="<?php echo htmlspecialchars($registro['confidencial']); ?>">

            <label for="vigencia_documental">Vigencia Documental:</label>
            <input type="text" name="vigencia_documental" id="vigencia_documental" value="<?php echo htmlspecialchars($registro['vigencia_documental']); ?>">

            <!-- Campos Ocultos para Área Responsable -->
            <input type="hidden" name="area_responsable" value="<?php echo htmlspecialchars($registro['area_responsable']); ?>">

            <label for="informacion_al">Información Al:</label>
            <input type="text" name="informacion_al" id="informacion_al" value="<?php echo htmlspecialchars($registro['informacion_al']); ?>" required>

            <label for="responsable">Responsable:</label>
            <input type="text" name="responsable" id="responsable" value="<?php echo htmlspecialchars($registro['responsable']); ?>" required>

            <button type="submit">Actualizar</button>
        </form>
        <a class="cancelar" href="../mostrarRegistros5_11.php?area=<?php echo urlencode($area); ?>&clasificacion=<?php echo urlencode($clasificacion); ?>&anio=<?php echo urlencode($anio); ?>&search=<?php echo urlencode($search); ?>&pagina=<?php echo urlencode($pagina); ?>">Cancelar</a>
    </div>

</body>

</html>