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
        f18.no, 
            f18.clave, 
            f18.lugar_movilidad_equipo, 
            f18.cantidad, 
            f18.en_poder, 
            f18.cantidad_copias,
            f18.informacion_al, 
            f18.responsable
    FROM 
        formato_5_18 f18 
    WHERE 
        f18.formato_id = ?
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
    $clave = isset($_POST['clave']) ? trim($_POST['clave']) : '';
    $lugar_movilidad_equipo = isset($_POST['lugar_movilidad_equipo']) ? trim($_POST['lugar_movilidad_equipo']) : '';
    $cantidad = isset($_POST['cantidad']) ? trim($_POST['cantidad']) : '';
    $en_poder = isset($_POST['en_poder']) ? trim($_POST['en_poder']) : '';
    $cantidad_copias = isset($_POST['cantidad_copias']) ? trim($_POST['cantidad_copias']) : '';
    $informacion_al = isset($_POST['informacion_al']) ? trim($_POST['informacion_al']) : '';
    $responsable = isset($_POST['responsable']) ? trim($_POST['responsable']) : '';

    // Validar datos
    $errores = [];
    if (empty($no) || !is_numeric($no)) {
        $errores[] = "El campo 'No.' es obligatorio y debe ser numérico.";
    }
    if (empty($clave)) {
        $errores[] = "El campo 'CLAVE' es obligatorio.";
    }
    if (empty($lugar_movilidad_equipo)) {
        $errores[] = "El campo 'LUGAR, MOVILIARIO O EQUIPO' es obligatorio.";
    }
    if (empty($cantidad) || !is_numeric($cantidad)) {
        $errores[] = "El campo 'CANTIDAD' es obligatorio y debe ser numérico.";
    }
    if (empty($en_poder)) {
        $errores[] = "El campo 'EN PODER DE' es obligatorio.";
    }
    if (empty($cantidad_copias) || !is_numeric($cantidad_copias)) {
        $errores[] = "El campo 'CANTIDAD(COPIAS)' es obligatorio y debe ser numérico.";
    }

    if (count($errores) === 0) {
        // Preparar la consulta de actualización
        $stmt_update = $conexion->prepare("
            UPDATE 
                formato_5_18 
            SET 
                no = ?, 
                clave = ?, 
                lugar_movilidad_equipo = ?, 
                cantidad = ?, 
                en_poder = ?, 
                cantidad_copias = ?, 
                informacion_al = ?, 
                responsable = ? 
            WHERE 
                formato_id = ?
        ");
        if (!$stmt_update) {
            die("Error en la preparación de la consulta de actualización: " . $conexion->error);
        }
        $stmt_update->bind_param(
            "issisissi",
            $no,
            $clave,
            $lugar_movilidad_equipo,
            $cantidad,
            $en_poder,
            $cantidad_copias,
            $informacion_al,
            $responsable,
            $id
        );

        if ($stmt_update->execute()) {
            // Redirigir con los mismos parámetros de la URL
            header("Location: ../mostrarRegistros5_18.php?mensaje=Registro%20actualizado%20correctamente&area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&anio=" . urlencode($anio) . "&search=" . urlencode($search) . "&pagina=" . urlencode($pagina));
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

            <label for="clave">Clave:</label>
            <input type="text" name="clave" id="clave" value="<?php echo htmlspecialchars($registro['clave']); ?>" required>

            <label for="lugar_movilidad_equipo">LUGAR, MOVILIARIO O EQUIPO</label>
            <input type="text" name="lugar_movilidad_equipo" id="lugar_movilidad_equipo" value="<?php echo htmlspecialchars($registro['lugar_movilidad_equipo']); ?>" required>

            <label for="cantidad">CANTIDAD</label>
            <textarea name="cantidad" id="cantidad" required><?php echo htmlspecialchars($registro['cantidad']); ?></textarea>

            <label for="en_poder">EN PODER DE</label>
            <input type="text" name="en_poder" id="en_poder" value="<?php echo htmlspecialchars($registro['en_poder']); ?>">

            <label for="cantidad_copias">CANTIDAD (COPIAS)</label>
            <input type="text" name="cantidad_copias" id="cantidad_copias" value="<?php echo htmlspecialchars($registro['cantidad_copias']); ?>">

            <label for="informacion_al">Información Al:</label>
            <input type="text" name="informacion_al" id="informacion_al" value="<?php echo htmlspecialchars($registro['informacion_al']); ?>" required>

            <label for="responsable">Responsable:</label>
            <input type="text" name="responsable" id="responsable" value="<?php echo htmlspecialchars($registro['responsable']); ?>" required>

            <button type="submit">Actualizar</button>
        </form>
        <a class="cancelar" href="../mostrarRegistros5_18.php?area=<?php echo urlencode($area); ?>&clasificacion=<?php echo urlencode($clasificacion); ?>&anio=<?php echo urlencode($anio); ?>&search=<?php echo urlencode($search); ?>&pagina=<?php echo urlencode($pagina); ?>">Cancelar</a>
    </div>

</body>

</html>