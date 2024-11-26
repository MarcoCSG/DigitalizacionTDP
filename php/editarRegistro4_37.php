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
        f37.banco, 
        f37.no_cuenta, 
        f37.total, 
        f37.utilizados, 
        f37.por_utilizar, 
        f37.cancelados, 
        f37.informacion_al, 
        f37.responsable 
    FROM 
        formato_4_37 f37 
    WHERE 
        f37.formato_id = ?
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
    $banco = isset($_POST['banco']) ? trim($_POST['banco']) : '';
    $no_cuenta = isset($_POST['no_cuenta']) ? intval($_POST['no_cuenta']) : 0;
    $total = isset($_POST['total']) ? intval($_POST['total']) : 0;
    $utilizados = isset($_POST['utilizados']) ? intval($_POST['utilizados']) : 0;
    $por_utilizar = isset($_POST['por_utilizar']) ? intval($_POST['por_utilizar']) : 0;
    $cancelados = isset($_POST['cancelados']) ? intval($_POST['cancelados']) : 0;
    
    $informacion_al = trim($_POST['informacion_al']);
    if ($informacion_al) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $informacion_al = date('d/m/Y', strtotime($informacion_al));
    }
    $responsable = isset($_POST['responsable']) ? trim($_POST['responsable']) : '';

    // Validaciones básicas
    $errores = [];
    
    if (empty($banco)) {
        $errores[] = "El nombre del banco es obligatorio.";
    }
    if ($no_cuenta <= 0) {
        $errores[] = "El no cuenta debe ser un valor positivo y numero";
    }
    if ($total <= 0) {
        $errores[] = "El total debe ser un valor positivo  y numero";
    }
    if ($utilizados <= 0) {
        $errores[] = "utilizados debe ser un valor positivo  y numero";
    }
    if ($por_utilizar <= 0) {
        $errores[] = "El por utilizar debe ser un valor positivo  y numero";
    }
    if ($cancelados <= 0) {
        $errores[] = "El cancelados debe ser un valor positivo y numero";
    }
    

    if (count($errores) === 0) {
        // Preparar la consulta de actualización
        $stmt_update = $conexion->prepare("
            UPDATE 
                formato_4_37 
            SET 
                banco = ?, 
                no_cuenta = ?, 
                total = ?, 
                utilizados = ?, 
                por_utilizar = ?, 
                cancelados = ?, 
                informacion_al = ?, 
                responsable = ? 
            WHERE 
                formato_id = ?
        ");
        if (!$stmt_update) {
            die("Error en la preparación de la consulta de actualización: " . $conexion->error);
        }
        $stmt_update->bind_param(
            "siiiiissi",
            $banco,
            $no_cuenta,
            $total,
            $utilizados,
            $por_utilizar,
            $cancelados,
            $informacion_al,
            $responsable,
            $id
        );

        if ($stmt_update->execute()) {
            // Redirigir con los mismos parámetros de la URL
            header("Location: ../mostrarRegistros4_37.php?mensaje=Registro%20actualizado%20correctamente&area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&anio=" . urlencode($anio) . "&search=" . urlencode($search) . "&pagina=" . urlencode($pagina));
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

            <label for="banco">BANCO
            <span class="tooltip">?
                <span class="tooltip-text">El nombre de la Institución Bancaria que administra la cuenta.</span>
            </span>
            </label>
            <input type="text" name="banco" id="banco" value="<?php echo htmlspecialchars($registro['banco']); ?>" required>
            
            <label for="no_cuenta">NÚMERO DE CUENTA
                <span class="tooltip">?
                <span class="tooltip-text">Los dígitos asignados por la Institución Bancaria a cada cuenta de cheques.</span>
            </span>
            </label>
            <input type="number" name="no_cuenta" id="no_cuenta" value="<?php echo htmlspecialchars($registro['no_cuenta']); ?>" required min="1">

            <label for="total">TOTAL
                <span class="tooltip">?
                <span class="tooltip-text">Los números del primer y último cheque, correspondiente a la cuenta bancaria de que se trate.</span>
            </span>
            </label>
            <input type="number" name="total" id="total" value="<?php echo htmlspecialchars($registro['total']); ?>" required min="1">

            <label for="utilizados">UTILIZADOS
                <span class="tooltip">?
                <span class="tooltip-text">Los números del primer y último cheque expedido, correspondiente a la cuenta bancaria de que se trate.</span>
            </span>
            </label>
            <input type="number" name="utilizados" id="utilizados" value="<?php echo htmlspecialchars($registro['utilizados']); ?>" required min="1">

            <label for="por_utilizar">POR UTILIZAR
                <span class="tooltip">?
                <span class="tooltip-text">Los números del primer y último cheque que se encuentran sin expedir, correspondiente a la cuenta bancaria de que se trate.</span>
            </span>
            </label>
            <input type="number" name="por_utilizar" id="por_utilizar" value="<?php echo htmlspecialchars($registro['por_utilizar']); ?>" required min="1">

            <label for="cancelados">CANCELADOS
                <span class="tooltip">?
                <span class="tooltip-text">Los números de los cheques que fueron cancelados, según la cuenta bancaria de que se trate.</span>
            </span>
            </label>
            <input type="number" name="cancelados" id="cancelados" value="<?php echo htmlspecialchars($registro['cancelados']); ?>" required min="1">

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
        <a class="cancelar" href="../mostrarRegistros4_37.php?area=<?php echo urlencode($area); ?>&clasificacion=<?php echo urlencode($clasificacion); ?>&anio=<?php echo urlencode($anio); ?>&search=<?php echo urlencode($search); ?>&pagina=<?php echo urlencode($pagina); ?>">Cancelar</a>
    </div>

</body>
</html>
