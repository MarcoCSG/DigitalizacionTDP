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
        f516.inventario, 
        f516.area516, 
        f516.responsable_516, 
        f516.combinacion_si, 
        f516.combinacion_no, 
        f516.observaciones,  
        f516.informacion_al, 
        f516.responsable 
    FROM 
        formato_5_16 f516 
    WHERE 
        f516.formato_id = ?
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
    $inventario = isset($_POST['inventario']) ? trim($_POST['inventario']) : '';
    $area516 = isset($_POST['area516']) ? trim($_POST['area516']) : '';
    $responsable_516 = isset($_POST['responsable_516']) ? trim($_POST['responsable_516']) : '';
    $combinacion_si = isset($_POST['combinacion_si']) ? trim($_POST['combinacion_si']) : '';
    $combinacion_no = isset($_POST['combinacion_no']) ? trim($_POST['combinacion_no']) : '';
    $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';
    $informacion_al = isset($_POST['informacion_al']) ? trim($_POST['informacion_al']) : '';
    if ($informacion_al) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $informacion_al = date('d/m/Y', strtotime($informacion_al));
    }
    $responsable = isset($_POST['responsable']) ? trim($_POST['responsable']) : '';

    // Validaciones básicas
    $errores = [];
    if (empty($inventario)) {
        $errores[] = "El nombre del inventario es obligatorio.";
    }
    if (empty($area516)) {
        $errores[] = "el nombre del area es obligatoria.";
    }
    if (empty($responsable_516)) {
        $errores[] = "el nombre del responsable es obligatoria.";
    }
    if (empty($observaciones)) {
        $errores[] = "Las observaciones del contenido es obligatoria.";
    }


    if (count($errores) === 0) {
        // Preparar la consulta de actualización
        $stmt_update = $conexion->prepare("
            UPDATE 
                formato_5_16 
            SET 
                inventario = ?, 
                area516 = ?, 
                responsable_516 = ?,  
                combinacion_si = ?, 
                combinacion_no = ?, 
                observaciones = ?,  
                informacion_al = ?, 
                responsable = ? 
            WHERE 
                formato_id = ?
        ");
        if (!$stmt_update) {
            die("Error en la preparación de la consulta de actualización: " . $conexion->error);
        }
        $stmt_update->bind_param(
            "ssssssssi",

            $inventario,
            $area516,
            $responsable_516,
            $combinacion_si,
            $combinacion_no,
            $observaciones,
            $informacion_al,
            $responsable,
            $id
        );

        if ($stmt_update->execute()) {
            // Redirigir con los mismos parámetros de la URL
            header("Location: ../mostrarRegistros5_16.php?mensaje=Registro%20actualizado%20correctamente&area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&anio=" . urlencode($anio) . "&search=" . urlencode($search) . "&pagina=" . urlencode($pagina));
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
        form input[type="date"],

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
            border: 1px solid #f516c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            color: #721c24;
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
        <form method="post" action="" onsubmit="convertirAMayusculas()">

            <label for="inventario">INVENTARIO:
                <span class="tooltip">?
                <span class="tooltip-text">La clave numérica o alfanumérica asignada a la caja fuerte en el inventario.</span>
            </span>
            </label>
            <input type="text" name="inventario" id="inventario" spellcheck="true" value="<?php echo htmlspecialchars($registro['inventario']); ?>" required>

            <label for="area516">ÁREA:
                <span class="tooltip">?
                <span class="tooltip-text">El nombre de la Unidad Administrativa en donde se localiza físicamente la caja fuerte.</span>
            </span>
            </label>
            <input type="text" name="area516" id="area516" spellcheck="true" value="<?php echo htmlspecialchars($registro['area516']); ?>" required>

            <label for="responsable_516">RESPONSABLE
            <span class="tooltip">?
                <span class="tooltip-text">El nombre y cargo de la persona titular de la Unidad Administrativa en donde se localiza, o aquélla que tiene bajo su resguardo la
                caja fuerte.</span>
            </span>
            </label>
            <input type="text" name="responsable_516" id="responsable_516" spellcheck="true" value="<?php echo htmlspecialchars($registro['responsable_516']); ?>" required>

            <label for="combinacion_si">SI:
                <span class="tooltip">?
                <span class="tooltip-text">Una “X” si se realiza o no la entrega de la combinación en sobre sellado y cerrado.</span>
            </span>
            </label>
            <input type="text" name="combinacion_si" id="combinacion_si" spellcheck="true" value="<?php echo htmlspecialchars($registro['combinacion_si']); ?>">

            <label for="combinacion_no">NO:
                <span class="tooltip">?
                <span class="tooltip-text">Una “X” si se realiza o no la entrega de la combinación en sobre sellado y cerrado.</span>
            </span>
            </label>
            <input type="text" name="combinacion_no" id="combinacion_no" spellcheck="true" value="<?php echo htmlspecialchars($registro['combinacion_no']); ?>">

            <label for="observaciones">OBSERVACIONES:
                <span class="tooltip">?
                <span class="tooltip-text">Los comentarios que se consideren importantes respecto a la (s) caja(s) fuerte(s) relacionada(s).</span>
            </span>
            </label>
            <input type="text" name="observaciones" id="observaciones" spellcheck="true" value="<?php echo htmlspecialchars($registro['observaciones']); ?>">

            <label for="informacion_al">INFORMACIÓN AL
                <span class="tooltip">?
                <span class="tooltip-text">El día, mes y año en que se actualizó la información de este formato</span>
            </span>
            </label>
            <input type="date" name="informacion_al" id="informacion_al" value="<?php echo htmlspecialchars($registro['informacion_al']); ?>" required>

            <label for="responsable">RESPONSABLE DE LA INFORMACIÓN
                <span class="tooltip">?
                <span class="tooltip-text">El nombre y cargo del servidor público responsable de integrar la información, y en su caso del resguardo de la documentación soporte.</span>
            </span>
            </label>
            <input type="text" name="responsable" id="responsable" value="<?php echo htmlspecialchars($registro['responsable']); ?>" required>

            <button type="submit">Actualizar</button>
        </form>
        <a class="cancelar" href="../mostrarRegistros5_16.php?area=<?php echo urlencode($area); ?>&clasificacion=<?php echo urlencode($clasificacion); ?>&anio=<?php echo urlencode($anio); ?>&search=<?php echo urlencode($search); ?>&pagina=<?php echo urlencode($pagina); ?>">Cancelar</a>
    </div>

</body>

</html>
