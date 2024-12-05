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
$stmt = $conexion->prepare("SELECT 
        f33.no, 
        f33.acta, 
        f33.fecha, 
        f33.acuerdo, 
        f33.responsable_cabildo,
        f33.estado, 
        f33.observaciones3_3, 
        f33.informacion_al, 
        f33.responsable 
    FROM 
        formato_3_3 f33 
    WHERE 
        f33.formato_id = ?
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
    $acta = isset($_POST['acta']) ? trim($_POST['acta']) : '';

    $fecha = isset($_POST['fecha']);
    if ($fecha) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $fecha = date('d/m/Y', strtotime($fecha));
    }

    $acuerdo = isset($_POST['acuerdo']) ? trim($_POST['acuerdo']) : '';

    $responsable_cabildo = isset($_POST['responsable_cabildo']) ? trim($_POST['responsable_cabildo']) : '';
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : '';
    $observaciones3_3 = isset($_POST['observaciones3_3']) ? trim($_POST['observaciones3_3']) : '';

    $informacion_al = trim($_POST['informacion_al']);
    if ($informacion_al) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $informacion_al = date('d/m/Y', strtotime($informacion_al));
    }
    $responsable = isset($_POST['responsable']) ? trim($_POST['responsable']) : '';

    // Validar datos
    $errores = [];
    if (empty($no) || !is_numeric($no)) {
        $errores[] = "El campo 'no' es obligatorio y debe ser numérico.";
    }
    if (empty($acta)) {
        $errores[] = "El campo 'acta' es obligatorio.";
    }
    if (empty($fecha)) {
        $errores[] = "El campo 'fecha' es obligatorio.";
    }
    if (empty($acuerdo)) {
        $errores[] = "El campo 'acuerdo' es obligatorio.";
    }
    if (empty($responsable_cabildo || !is_numeric($responsable_cabildo))) {
        $errores[] = "El campo 'responsable_cabildo' es obligatorio.";
    }
    if (empty($estado)) {
        $errores[] = "El campo 'estado' es obligatorio.";
    }
    if (empty($observaciones3_3)) {
        $errores[] = "El campo 'observaciones' es obligatorio.";
    }

    if (count($errores) === 0) {
        // Preparar la consulta de actualización
        $stmt_update = $conexion->prepare("
            UPDATE 
                formato_3_3 
            SET 
                no = ?, 
                acta = ?, 
                fecha = ?, 
                acuerdo = ?, 
                responsable_cabildo = ?, 
                estado = ?, 
                observaciones3_3 = ?, 
                informacion_al = ?, 
                responsable = ? 
            WHERE 
                formato_id = ?
        ");
        if (!$stmt_update) {
            die("Error en la preparación de la consulta de actualización: " . $conexion->error);
        }
        $stmt_update->bind_param(
            "issssssssi",
            $no,
            $acta,
            $fecha,
            $acuerdo,
            $responsable_cabildo,
            $responsable,
            $observaciones3_3,
            $informacion_al,
            $responsable,
            $id
        );

        if ($stmt_update->execute()) {
            // Redirigir con los mismos parámetros de la URL
            header("Location: ../mostrarRegistros3_3.php?mensaje=Registro%20actualizado%20correctamente&area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&anio=" . urlencode($anio) . "&search=" . urlencode($search) . "&pagina=" . urlencode($pagina));
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

            <label for="no">No
                <span class="tooltip">?
                    <span class="tooltip-text">El número consecutivo de las Entidades Paramunicipales relacionadas (1, 2, 3).</span>
                </span>
            </label>
            <input type="number" name="no" id="no" value="<?php echo htmlspecialchars($registro['no']); ?>" required>

            <label for="acta">ACTA
                <span class="tooltip">?
                    <span class="tooltip-text">El número de identificación del Acta en donde consta el Acuerdo de Cabildo pendiente por cumplir.</span>
                </span>
            </label>
            <input type="text" name="acta" id="acta" value="<?php echo htmlspecialchars($registro['acta']); ?>" required>

            <label for="fecha">FECHA
                <span class="tooltip">?
                    <span class="tooltip-text">El día, mes y año en que se levantó el Acta en donde consta el Acuerdo de Cabildo pendiente de cumplir.</span>
                </span>
            </label>
            <input type="date" name="fecha" id="fecha" value="<?php echo htmlspecialchars($registro['fecha']); ?>" required>

            <label for="acuerdo">ACUERDO
                <span class="tooltip">?
                    <span class="tooltip-text">La descripción del Acuerdo de Cabildo pendiente de cumplir.</span>
                </span>
            </label>
            <input type="text" name="acuerdo" id="acuerdo" value="<?php echo htmlspecialchars($registro['acuerdo']); ?>" required>
            
            <label for="responsable_cabildo">RESPONSABLE
                <span class="tooltip">?
                    <span class="tooltip-text">El nombre y cargo del Edil o servidor público responsable de atender el Acuerdo de Cabildo.</span>
                </span>
            </label>
            <input type="text" name="responsable_cabildo" id="responsable_cabildo" value="<?php echo htmlspecialchars($registro['responsable_cabildo']); ?>" required>

            <label for="estado">ESTADO
                <span class="tooltip">?
                    <span class="tooltip-text">Las acciones instrumentadas o situación que guarda la atención del Acuerdo de Cabildo pendiente de cumplir.</span>
                </span>
            </label>
            <input type="text" name="estado" id="estado" value="<?php echo htmlspecialchars($registro['estado']); ?>" required>
            
            <label for="observaciones3_3">OBSERVACIONES
                <span class="tooltip">?
                    <span class="tooltip-text">Los comentarios que se consideren importantes respecto a los Acuerdos de Cabildo pendientes de cumplir.</span>
                </span>
            </label>
            <input type="text" name="observaciones3_3" id="observaciones3_3" value="<?php echo htmlspecialchars($registro['observaciones3_3']); ?>" required>

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
        <a class="cancelar" href="../mostrarregistros3_3.php?php echo urlencode($area); ?>&clasificacion=<?php echo urlencode($clasificacion); ?>&anio=<?php echo urlencode($anio); ?>&search=<?php echo urlencode($search); ?>&pagina=<?php echo urlencode($pagina); ?>">Cancelar</a>
    </div>

</body>

</html>