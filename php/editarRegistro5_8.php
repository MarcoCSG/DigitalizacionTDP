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
        f58.no, 
            f58.ubicacion, 
            f58.colindancias, 
            f58.superficie_total,
            f58.documento_aval, 
            f58.valor, 
            f58.uso_actual, 
            f58.observaciones, 
            f58.informacion_al, 
            f58.responsable
    FROM 
        formato_5_8 f58 
    WHERE 
        f58.formato_id = ?
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
    $ubicacion = isset($_POST['ubicacion']) ? trim($_POST['ubicacion']) : '';
    $colindancias = isset($_POST['colindancias']) ? trim($_POST['colindancias']) : '';
    $superficie_total = isset($_POST['superficie_total']) ? trim($_POST['superficie_total']) : '';
    $documento_aval = isset($_POST['documento_aval']) ? trim($_POST['documento_aval']) : '';
    $valor = isset($_POST['valor']) ? trim($_POST['valor']) : '';
    $uso_actual = isset($_POST['uso_actual']) ? trim($_POST['uso_actual']) : '';
    $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';
    $informacion_al = isset($_POST['informacion_al']) ? trim($_POST['informacion_al']) : '';
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
    if (empty($ubicacion)) {
        $errores[] = "El campo 'clasificiacion del activo' es obligatorio.";
    }
    if (empty($colindancias)) {
        $errores[] = "El campo 'colindancias' es obligatorio.";
    }
    if (empty($superficie_total)) {
        $errores[] = "El campo 'superficie_total' es obligatorio.";
    }
    if (empty($documento_aval)) {
        $errores[] = "El campo 'documento_aval' es obligatorio.";
    }
    if (empty($valor)) {
        $errores[] = "El campo 'valor' es obligatorio.";
    }
    if (empty($uso_actual)) {
        $errores[] = "El campo 'uso_actual' es obligatorio.";
    }
    if (empty($observaciones)) {
        $errores[] = "El campo 'observaciones' es obligatorio.";
    }
    

    if (count($errores) === 0) {
        // Preparar la consulta de actualización
        $stmt_update = $conexion->prepare("
            UPDATE 
            formato_5_8 
            SET 
                no = ?, 
                ubicacion = ?, 
                colindancias = ?, 
                superficie_total = ?, 
                documento_aval = ?, 
                valor = ?, 
                uso_actual = ?, 
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
            "isssssssssi",
            $no,
            $ubicacion,
            $colindancias,
            $superficie_total,
            $documento_aval,
            $valor,
            $uso_actual,
            $observaciones,
            $informacion_al,
            $responsable,
            $id
        );

        if ($stmt_update->execute()) {
            // Redirigir con los mismos parámetros de la URL
            header("Location: ../mostrarRegistros5_8.php?mensaje=Registro%20actualizado%20correctamente&area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&anio=" . urlencode($anio) . "&search=" . urlencode($search) . "&pagina=" . urlencode($pagina));
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
                <span class="tooltip-text">El número consecutivo de las reservas territoriales relacionadas (1, 2, 3, etc.).</span>
            </span>
        </label>
        <input type="number" name="no" id="no" value="<?php echo htmlspecialchars($registro['no']); ?>" required>

        <label for="ubicacion">UBICACIÓN
            <span class="tooltip">?
                <span class="tooltip-text"></span>
            </span>
        </label>
        <input type="text" name="ubicacion" id="ubicacion" value="<?php echo htmlspecialchars($registro['ubicacion']); ?>" required>

        <label for="colindancias">COLINDANCIAS
            <span class="tooltip">?
                <span class="tooltip-text">La descripción de las características de colindancia al norte, sur, este y oeste de la reserva territorial.</span>
            </span>
        </label>
        <textarea id="colindancias" name="colindancias" placeholder="Ingrese colindansias" spellcheck="true" required></textarea>

        <label for="superficie_total">SUPERFICIE TOTAL
            <span class="tooltip">?
                <span class="tooltip-text">Los metros cuadrados que conforman la reserva territorial.</span>
            </span>
        </label>
        <input type="text" name="superficie_total" id="superficie_total" value="<?php echo htmlspecialchars($registro['superficie_total']); ?>">

        <label for="documento_aval">DOCUMENTO QUE AVALE LA PROPIEDAD
            <span class="tooltip">?
                <span class="tooltip-text">El tipo y número de documento oficial que avala la propiedad.</span>
            </span>
        </label>
        <input type="text" name="documento_aval" id="documento_aval" value="<?php echo htmlspecialchars($registro['documento_aval']); ?>">

        <label for="valor">VALOR
            <span class="tooltip">?
                <span class="tooltip-text">La cantidad monetaria que corresponde a cada reserva territorial, ya sea de acuerdo al valor catastral o de avalúo (cifras en pesos),
                según corresponda.</span>
            </span>
        </label>
        <input type="text" name="valor" id="valor" class="moneda" value="<?php echo htmlspecialchars($registro['valor']); ?>">
        <script>
                document.querySelectorAll('.moneda').forEach((input) => {
                    // Función para formatear el valor como moneda
                    function formatCurrency(value) {
                        const numberValue = parseFloat(value.replace(/[^0-9.-]+/g, ''));
                        if (isNaN(numberValue)) return ''; // Si no es un número, retornar vacío
                        return '$' + numberValue.toFixed(2); // Formatear como moneda
                    }

                    // Evento al escribir en el input
                    input.addEventListener('input', (e) => {
                        const cursorPosition = e.target.selectionStart; // Guardar posición del cursor
                        const formattedValue = formatCurrency(e.target.value); // Formatear el valor
                        e.target.value = formattedValue; // Asignar el valor formateado
                        e.target.setSelectionRange(cursorPosition, cursorPosition); // Restaurar posición del cursor
                    });

                    // Formatear valor inicial si existe
                    if (input.value) {
                        input.value = formatCurrency(input.value);
                    }
                });
            </script>

        <label for="uso_actual">USO ACTUAL
            <span class="tooltip">?
                <span class="tooltip-text">La descripción del uso que se le da a la reserva territorial. </span>
            </span>
        </label>
        <input type="text" name="uso_actual" id="uso_actual" value="<?php echo htmlspecialchars($registro['uso_actual']); ?>">

        <label for="observaciones">OBSERVACIONES
            <span class="tooltip">?
                <span class="tooltip-text">Los comentarios que se consideren importantes respecto a la información catastral.</span>
            </span>
        </label>
        <input type="text" name="observaciones" id="observaciones" value="<?php echo htmlspecialchars($registro['observaciones']); ?>">

        <label for="informacion_al">INFORMACIÓN AL
            <span class="tooltip">?
                <span class="tooltip-text">El día, mes y año en que se actualizó la información de este formato Ejemplo: 15 de diciembre de 2021.</span>
            </span>
        </label>
        <input type="date" name="informacion_al" id="informacion_al" value="<?php echo htmlspecialchars($registro['informacion_al']); ?>" required>

        <label for="responsable">RESPONSABLE:
            <span class="tooltip">?
                <span class="tooltip-text">El nombre y cargo del servidor público responsable de integrar la información, y en su caso del resguardo de la documentación soporte.</span>
            </span>
        </label>
        <input type="text" name="responsable" id="responsable" value="<?php echo htmlspecialchars($registro['responsable']); ?>" required>

        <button type="submit">Actualizar</button>
    </form>
    <a class="cancelar" href="../mostrarregistros5_8.php?area=<?php echo urlencode($area); ?>&clasificacion=<?php echo urlencode($clasificacion); ?>&anio=<?php echo urlencode($anio); ?>&search=<?php echo urlencode($search); ?>&pagina=<?php echo urlencode($pagina); ?>">Cancelar</a>
</div>

</body>

</html>