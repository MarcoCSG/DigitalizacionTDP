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
        f34.no, 
        f34.numero, 
        f34.fecha, 
        f34.asunto, 
        f34.ejercicio, 
        f34.fojas, 
        f34.firma_si, 
        f34.firma_no, 
        f34.sello_si, 
        f34.sello_no, 
        f34.informacion_al, 
        f34.responsable 
    FROM 
        formato_3_4 f34 
    WHERE 
        f34.formato_id = ?
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
    $numero = isset($_POST['numero']) ? trim($_POST['numero']) : '';
    $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
    if ($fecha) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $fecha = date('d/m/Y', strtotime($fecha));
    }
    $asunto = isset($_POST['asunto']) ? trim($_POST['asunto']) : '';
    $ejercicio = isset($_POST['ejercicio']) ? trim($_POST['ejercicio']) : '';
    $fojas = isset($_POST['fojas']) ? trim($_POST['fojas']) : '';
    $firma_si = isset($_POST['firma_si']) ? trim($_POST['firma_si']) : '';
    $firma_no = isset($_POST['firma_no']) ? trim($_POST['firma_no']) : '';
    $sello_si = isset($_POST['sello_si']) ? trim($_POST['sello_si']) : '';
    $sello_no = isset($_POST['sello_no']) ? trim($_POST['sello_no']) : '';
    $informacion_al = isset($_POST['informacion_al']) ? trim($_POST['informacion_al']) : '';
    if ($informacion_al) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $informacion_al = date('d/m/Y', strtotime($informacion_al));
    }
    $responsable = isset($_POST['responsable']) ? trim($_POST['responsable']) : '';

    // Validaciones básicas
    $errores = [];
    if ($no <= 0) {
        $errores[] = "El número debe ser un valor positivo.";
    }
    if (empty($numero)) {
        $errores[] = "El nombre del numero es obligatorio.";
    }
    if (empty($fecha)) {
        $errores[] = "La fecha es obligatoria.";
    }
    if (empty($asunto)) {
        $errores[] = "el asunto es obligatoria.";
    }
    if (empty($ejercicio)) {
        $errores[] = "el ejercicio es obligatoria.";
    }
    if (empty($fojas)) {
        $errores[] = "las fojas es obligatorio.";
    }


    if (count($errores) === 0) {
        // Preparar la consulta de actualización
        $stmt_update = $conexion->prepare("
            UPDATE 
                formato_3_4 
            SET 
                no = ?, 
                numero = ?, 
                fecha = ?, 
                asunto = ?, 
                ejercicio = ?, 
                fojas = ?, 
                firma_si = ?, 
                firma_no = ?, 
                sello_si = ?, 
                sello_no = ?, 
                informacion_al = ?, 
                responsable = ? 
            WHERE 
                formato_id = ?
        ");
        if (!$stmt_update) {
            die("Error en la preparación de la consulta de actualización: " . $conexion->error);
        }
        $stmt_update->bind_param(
            "iissssssssssi",
            $no,
            $numero,
            $fecha,
            $asunto,
            $ejercicio,
            $fojas,
            $firma_si,
            $firma_no,
            $sello_si,
            $sello_no,
            $informacion_al,
            $responsable,
            $id
        );

        if ($stmt_update->execute()) {
            // Redirigir con los mismos parámetros de la URL
            header("Location: ../mostrarRegistros3_4.php?mensaje=Registro%20actualizado%20correctamente&area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&anio=" . urlencode($anio) . "&search=" . urlencode($search) . "&pagina=" . urlencode($pagina));
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
            border: 1px solid #f5c6cb;
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
            <label for="no">No:
                <span class="tooltip">?
                <span class="tooltip-text">El número consecutivo de los documentos relacionados (1, 2, 3, etc.).</span>
            </span>
            </label>
            <input type="number" name="no" id="no" value="<?php echo htmlspecialchars($registro['no']); ?>" required min="1">

            <label for="numero">NUMERO:
                <span class="tooltip">?
                <span class="tooltip-text">El número asignado al Acta del Consejo relacionada.</span>
            </span>
            </label>
            <input type="text" name="numero" id="numero" spellcheck="true" value="<?php echo htmlspecialchars($registro['numero']); ?>" required>

            <label for="fecha">FECHA:
                <span class="tooltip">?
                <span class="tooltip-text">El día, mes y año en que se levantó el Acta del Consejo relacionada.</span>
            </span>
            </label>
            <input type="date" name="fecha" id="fecha" spellcheck="true" value="<?php echo htmlspecialchars($registro['fecha']); ?>" required>

            <label for="asunto">ASUNTO
            <span class="tooltip">?
                <span class="tooltip-text">Una breve descripción de los asuntos consignados en el Acta.</span>
            </span>
            </label>
            <input type="text" name="asunto" id="asunto" spellcheck="true" value="<?php echo htmlspecialchars($registro['asunto']); ?>" required>

            <label for="ejercicio">EJERCICIO
                <span class="tooltip">?
                <span class="tooltip-text">El año al que corresponde el Acta. Se recomienda que las Actas se relacionen por año iniciando con 2018, posteriormente 2019,
                2020 y finalmente 2021.</span>
            </span>
            </label>
            <input type="number" name="ejercicio" id="ejercicio" spellcheck="true" value="<?php echo htmlspecialchars($registro['ejercicio']); ?>">

            <label for="fojas">FOJAS
                <span class="tooltip">?
                <span class="tooltip-text">El número de fojas que conforman el Acta, por ejemplo: 5, 10, 15, etc.</span>
            </span>
            </label>
            <input type="number" name="fojas" id="fojas" spellcheck="true" value="<?php echo htmlspecialchars($registro['fojas']); ?>">


            <label for="firma_si">FIRMAS SI:
                <span class="tooltip">?
                <span class="tooltip-text">Una “X” indicando si se cuenta o no con el total de firmas de los integrantes del Consejo. </span>
            </span>
            </label>
            <input type="text" name="firma_si" id="firma_si" spellcheck="true" value="<?php echo htmlspecialchars($registro['firma_si']); ?>">

            <label for="firma_no">FIRMAS NO:
                <span class="tooltip">?
                <span class="tooltip-text">Una “X” indicando si se cuenta o no con el total de firmas de los integrantes del Consejo. </span>
            </span>
            </label>
            <input type="text" name="firma_no" id="firma_no" spellcheck="true" value="<?php echo htmlspecialchars($registro['firma_no']); ?>">

            <label for="sello_si">SELLOS SI:
                <span class="tooltip">?
                <span class="tooltip-text">Una “X” indicando si se cuenta con los sellos correspondientes para su validez oficial.</span>
            </span>
            </label>
            <input type="text" name="sello_si" id="sello_si" spellcheck="true" value="<?php echo htmlspecialchars($registro['sello_si']); ?>">

            <label for="sello_no">SELLOS NO:
            <span class="tooltip">?
                <span class="tooltip-text">Una “X” indicando si se cuenta con los sellos correspondientes para su validez oficial.</span>
            </span>
            </label>
            <input type="text" name="sello_no" spellcheck="true" value="<?php echo htmlspecialchars($registro['sello_no']); ?>">

            <label for="informacion_al">INFORMACIÓN AL
                <span class="tooltip">?
                <span class="tooltip-text">El día, mes y año en que se actualizó la información de este formato Ejemplo: 15 de diciembre de 2021.</span>
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
        <a class="cancelar" href="../mostrarRegistros3_4.php?area=<?php echo urlencode($area); ?>&clasificacion=<?php echo urlencode($clasificacion); ?>&anio=<?php echo urlencode($anio); ?>&search=<?php echo urlencode($search); ?>&pagina=<?php echo urlencode($pagina); ?>">Cancelar</a>
    </div>

</body>

</html>
