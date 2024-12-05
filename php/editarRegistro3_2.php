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
        f32.no, 
        f32.libro, 
        f32.acta_del, 
        f32.acta_al, 
        f32.fojas, 
        f32.observaciones_3_2, 
        f32.informacion_al, 
        f32.responsable 
    FROM 
        formato_3_2 f32 
    WHERE 
        f32.formato_id = ?
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
    $libro = isset($_POST['libro']) ? trim($_POST['libro']) : '';

    $acta_del = isset($_POST['acta_del']);
    if ($acta_del) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $acta_del = date('d/m/Y', strtotime($acta_del));
    }

    $acta_al = isset($_POST['acta_al']);
    if ($acta_al) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $acta_al = date('d/m/Y', strtotime($acta_al));
    }

    $fojas = isset($_POST['fojas']) ? trim($_POST['fojas']) : 0;

    $observaciones_3_2 = isset($_POST['observaciones_3_2']) ? trim($_POST['observaciones_3_2']) : '';

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
    if (empty($libro)) {
        $errores[] = "El campo 'libro' es obligatorio.";
    }
    if (empty($acta_del)) {
        $errores[] = "El campo 'acta_del' es obligatorio.";
    }
    if (empty($acta_al)) {
        $errores[] = "El campo 'acta_al' es obligatorio.";
    }
    if (empty($fojas || !is_numeric($fojas))) {
        $errores[] = "El campo 'fojas' es obligatorio.";
    }

    if (empty($observaciones_3_2)) {
        $errores[] = "El campo 'observaciones' es obligatorio.";
    }

    if (count($errores) === 0) {
        // Preparar la consulta de actualización
        $stmt_update = $conexion->prepare("
            UPDATE 
                formato_3_2 
            SET 
                no = ?, 
                libro = ?, 
                acta_del = ?, 
                acta_al = ?, 
                fojas = ?, 
                observaciones_3_2 = ?, 
                informacion_al = ?, 
                responsable = ? 
            WHERE 
                formato_id = ?
        ");
        if (!$stmt_update) {
            die("Error en la preparación de la consulta de actualización: " . $conexion->error);
        }
        $stmt_update->bind_param(
            "isssssssi",
            $no,
            $libro,
            $acta_del,
            $acta_al,
            $fojas,
            $observaciones_3_2,
            $informacion_al,
            $responsable,
            $id
        );

        if ($stmt_update->execute()) {
            // Redirigir con los mismos parámetros de la URL
            header("Location: ../mostrarRegistros3_2.php?mensaje=Registro%20actualizado%20correctamente&area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&anio=" . urlencode($anio) . "&search=" . urlencode($search) . "&pagina=" . urlencode($pagina));
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

            <label for="libro">LIBRO
                <span class="tooltip">?
                    <span class="tooltip-text">La clave o número de identificación asignado al Libro de Actas de Cabildo. Ejemplo: 2018/II.</span>
                </span>
            </label>
            <input type="text" name="libro" id="libro" value="<?php echo htmlspecialchars($registro['libro']); ?>" required>

            <label for="acta_del">ACTA DEL:
                <span class="tooltip">?
                    <span class="tooltip-text">La fecha (día, mes y año) en la que se celebró la sesión de Cabildo consignada en la primer Acta que contiene el Libro.</span>
                </span>
            </label>
            <input type="date" name="acta_del" id="acta_del" value="<?php echo htmlspecialchars($registro['acta_del']); ?>" required>

            <label for="acta_al">ACTA AL:
                <span class="tooltip">?
                    <span class="tooltip-text">La fecha (día, mes y año) en la que se celebró la sesión de Cabildo consignada en la última Acta que contiene el Libro.</span>
                </span>
            </label>
            <input type="date" name="acta_al" id="acta_al" value="<?php echo htmlspecialchars($registro['acta_al']); ?>" required>
            
            <label for="fojas">FOJAS
                <span class="tooltip">?
                    <span class="tooltip-text">Ingrese un rango de fojas en el formato: número-número, por ejemplo: 1-125.</span>
                </span>
            </label>
            <input
                type="text"
                name="fojas"
                id="fojas"
                value="<?php echo htmlspecialchars($registro['fojas']); ?>"
                pattern="^\d+-\d+$"
                title="Solo se permite un rango de números separados por un guion, por ejemplo: 1-125"
                required
                oninput="validateFojas(this)">

            <script>
                function validateFojas(input) {
                    // Permitir solo números y un guion
                    const pattern = /^\d*-\d*$/; // Permite números opcionales seguidos de un guion y más números opcionales
                    if (!pattern.test(input.value)) {
                        input.value = input.value.replace(/[^0-9\-]/g, ''); // Elimina caracteres no permitidos
                    }
                }
            </script>

            <label for="observaciones_3_2">OBSERVACIONES
                <span class="tooltip">?
                    <span class="tooltip-text">La ubicación y los comentarios que se consideren importantes de los Libros que contienen las Actas de Cabildo de la
                    administración actual.</span>
                </span>
            </label>
            <input type="text" name="observaciones_3_2" id="observaciones_3_2" value="<?php echo htmlspecialchars($registro['observaciones_3_2']); ?>" required>

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
        <a class="cancelar" href="../mostrarregistros3_2.php?php echo urlencode($area); ?>&clasificacion=<?php echo urlencode($clasificacion); ?>&anio=<?php echo urlencode($anio); ?>&search=<?php echo urlencode($search); ?>&pagina=<?php echo urlencode($pagina); ?>">Cancelar</a>
    </div>

</body>

</html>