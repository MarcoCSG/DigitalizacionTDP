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
        f38.concepto, 
            f38.serie, 
            f38.del_folio, 
            f38.al_folio, 
            f38.area_responsable,
            f38.observaciones,
            f38.informacion_al, 
            f38.responsable
    FROM 
        formato_4_38 f38 
    WHERE 
        f38.formato_id = ?
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
    $concepto = isset($_POST['concepto']) ? trim($_POST['concepto']) : '';
    $serie = isset($_POST['serie']) ? trim($_POST['serie']) : '';
    $al_folio = isset($_POST['al_folio']) ? intval($_POST['al_folio']) : 0;
    $del_folio = isset($_POST['del_folio']) ? intval($_POST['del_folio']) : 0;
    $area_responsable = isset($_POST['area_responsable']) ? trim($_POST['area_responsable']) : '';
    $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';
    $informacion_al = trim($_POST['informacion_al']);
    if ($informacion_al) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $informacion_al = date('d/m/Y', strtotime($informacion_al));
    }
    $responsable = isset($_POST['responsable']) ? trim($_POST['responsable']) : '';

    // Validar datos
    $errores = [];
    if (empty($concepto)) {
        $errores[] = "El campo 'concepto.' es obligatorio";
    }
    if (empty($serie)) {
        $errores[] = "El campo 'serie' es obligatorio.";
    }
    if (empty($del_folio) || !is_numeric($del_folio)) {
        $errores[] = "El campo 'del_folio' es obligatorio y debe ser numérico.";
    }
    if (empty($al_folio) || !is_numeric($al_folio)) {
        $errores[] = "El campo 'al_folio' es obligatorio y debe ser numérico.";
    }
    if (empty($area_responsable)) {
        $errores[] = "El campo 'area' es obligatorio.";
    }
    if (empty($observaciones)) {
        $errores[] = "El campo 'observaciones' es obligatorio.";
    }

    if (count($errores) === 0) {
        // Preparar la consulta de actualización
        $stmt_update = $conexion->prepare("
            UPDATE 
            formato_4_38 
            SET 
                concepto = ?, 
                serie = ?, 
                del_folio = ?, 
                al_folio = ?, 
                area_responsable = ?, 
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
            "ssiissssi",
            $concepto,
            $serie,
            $del_folio,
            $al_folio,
            $area_responsable,
            $observaciones,
            $informacion_al,
            $responsable,
            $id
        );

        if ($stmt_update->execute()) {
            // Redirigir con los mismos parámetros de la URL
            header("Location: ../mostrarRegistros4_38.php?mensaje=Registro%20actualizado%20correctamente&area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&anio=" . urlencode($anio) . "&search=" . urlencode($search) . "&pagina=" . urlencode($pagina));
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
            <label for="concepto">CONCEPTO
            <span class="tooltip">?
                <span class="tooltip-text">La denominación de la forma valorada. Ejemplo: Recibo Oficial, Forma del Registro Civil, etc.</span>
            </span>
            </label>
            <textarea name="concepto" id="concepto" required><?php echo htmlspecialchars($registro['concepto']); ?></textarea>

            <label for="serie">SERIE:
                <span class="tooltip">?
                <span class="tooltip-text">La clave numérica o alfanumérica que corresponda al talonario o bloque de formas valoradas.</span>
            </span>
            </label>
            <input type="text" name="serie" id="serie" value="<?php echo htmlspecialchars($registro['serie']); ?>" required>

            <label for="del_folio">DEL FOLIO
            <span class="tooltip">?
                <span class="tooltip-text">El número que corresponda a la primera y última forma del talonario o bloque.</span>
            </span>
            </label>
            <input type="number" name="del_folio" id="del_folio" value="<?php echo htmlspecialchars($registro['del_folio']); ?>" required>

            <label for="al_folio">AL FOLIO
            <span class="tooltip">?
                <span class="tooltip-text">El número que corresponda a la primera y última forma del talonario o bloque.</span>
            </span>
            </label>
            <input type="number" name="al_folio" id="al_folio" value="<?php echo htmlspecialchars($registro['al_folio']); ?>">

            <label for="area_responsable">ÁREA
            <span class="tooltip">?
                <span class="tooltip-text">El nombre del área responsable del control y resguardo de las formas valoradas.</span>
            </span>
            </label>
            <input type="text" name="area_responsable" id="area_responsable" value="<?php echo htmlspecialchars($registro['area_responsable']); ?>">

            <label for="observaciones">OBSERVACIONES
            <span class="tooltip">?
                <span class="tooltip-text">Los comentarios que se consideren importantes respecto a las formas valoradas existentes.</span>
            </span>
            </label>
            <textarea name="observaciones" id="observaciones" required><?php echo htmlspecialchars($registro['observaciones']); ?></textarea>

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
        <a class="cancelar" href="../mostrarRegistros4_38.php?area=<?php echo urlencode($area); ?>&clasificacion=<?php echo urlencode($clasificacion); ?>&anio=<?php echo urlencode($anio); ?>&search=<?php echo urlencode($search); ?>&pagina=<?php echo urlencode($pagina); ?>">Cancelar</a>
    </div>

</body>

</html>