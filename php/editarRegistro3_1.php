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
        f31.no, 
        f31.denominacion, 
        f31.no_acta, 
        f31.fecha_aprobacion, 
        f31.no_gaceta, 
        f31.fecha_publicacion, 
        f31.observaciones_3_1, 
        f31.informacion_al, 
        f31.responsable 
    FROM 
        formato_3_1 f31 
    WHERE 
        f31.formato_id = ?
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
    $denominacion = isset($_POST['denominacion']) ? trim($_POST['denominacion']) : '';
    $no_acta = isset($_POST['no_acta']) ? trim($_POST['no_acta']) : '';

    $fecha_aprobacion = isset($_POST['fecha_aprobacion']) ? $_POST['fecha_aprobacion'] : null;
    if ($fecha_aprobacion) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $fecha_aprobacion = date('d/m/Y', strtotime($fecha_aprobacion));
    }

    $no_gaceta = isset($_POST['no_gaceta']) ? trim($_POST['no_gaceta']) : '';

    $fecha_publicacion = isset($_POST['fecha_publicacion']) ? $_POST['fecha_publicacion'] : null;
    if ($fecha_publicacion) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $fecha_publicacion = date('d/m/Y', strtotime($fecha_publicacion));
    }

    $observaciones_3_1 = isset($_POST['observaciones_3_1']) ? trim($_POST['observaciones_3_1']) : '';

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
    if (empty($denominacion)) {
        $errores[] = "El campo 'denominacion' es obligatorio.";
    }
    if (empty($no_acta)) {
        $errores[] = "El campo 'no_acta' es obligatorio.";
    }
    if (empty($fecha_aprobacion)) {
        $errores[] = "El campo 'fecha_aprobacion' es obligatorio.";
    }
    if (empty($no_gaceta)) {
        $errores[] = "El campo 'no_gaceta' es obligatorio.";
    }
    if (empty($fecha_publicacion)) {
        $errores[] = "El campo 'fecha_publicacion' es obligatorio.";
    }    
    if (empty($observaciones_3_1)) {
        $errores[] = "El campo 'observaciones' es obligatorio.";
    }    

    if (count($errores) === 0) {
        // Preparar la consulta de actualización
        $stmt_update = $conexion->prepare("
            UPDATE 
                formato_3_1 
            SET 
                no = ?, 
                denominacion = ?, 
                no_acta = ?, 
                fecha_aprobacion = ?, 
                no_gaceta = ?, 
                fecha_publicacion = ?, 
                observaciones_3_1 = ?, 
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
            $denominacion,
            $no_acta,
            $fecha_aprobacion,
            $no_gaceta,
            $fecha_publicacion,
            $observaciones_3_1,
            $informacion_al,
            $responsable,
            $id
        );

        if ($stmt_update->execute()) {
            // Redirigir con los mismos parámetros de la URL
            header("Location: ../mostrarRegistros3_1.php?mensaje=Registro%20actualizado%20correctamente&area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&anio=" . urlencode($anio) . "&search=" . urlencode($search) . "&pagina=" . urlencode($pagina));
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

            <label for="denominacion">DENOMINACIÓN
                <span class="tooltip">?
                    <span class="tooltip-text">>El nombre del Reglamento de que se trate. Ejemplo: Reglamento Interior del Ayuntamiento de…, Reglamento de Servicios del
                    Ayuntamiento</span>
                </span>
            </label>
            <input type="text" name="denominacion" id="denominacion" value="<?php echo htmlspecialchars($registro['denominacion']); ?>" required>

            <label for="no_acta">No. DE ACTA
                <span class="tooltip">?
                    <span class="tooltip-text">La referencia numérica del acta de la sesión de cabildo en la que se haya aprobado el reglamento municipal referido, así como el
                    día, mes y año de la misma.</span>
                </span>
            </label>
            <input type="text" name="no_acta" id="no_acta" value="<?php echo htmlspecialchars($registro['no_acta']); ?>" required>

            <label for="fecha_aprobacion">FECHA DE APROBACIÓN
                <span class="tooltip">?
                    <span class="tooltip-text">La referencia numérica del acta de la sesión de cabildo en la que se haya aprobado el reglamento municipal referido, así como el
                    día, mes y año de la misma.</span>
                </span>
            </label>
            <input type="date" name="fecha_aprobacion" id="fecha_aprobacion" value="<?php echo htmlspecialchars($registro['fecha_aprobacion']); ?>" required>

            <label for="no_gaceta">No. DE GACETA
                <span class="tooltip">?
                    <span class="tooltip-text">La referencia numérica de la Gaceta Oficial en que se publicó el reglamento municipal referido, así como, el día, mes y año de la
                    misma.</span>
                </span>
            </label>
            <input type="text" name="no_gaceta" id="no_gaceta" value="<?php echo htmlspecialchars($registro['no_gaceta']); ?>" required>

            <label for="fecha_publicacion">FECHA DE PUBLICACIÓN
                <span class="tooltip">?
                    <span class="tooltip-text">La referencia numérica de la Gaceta Oficial en que se publicó el reglamento municipal referido, así como, el día, mes y año de la
                    misma.</span>
                </span>
            </label>
            <input type="date" name="fecha_publicacion" id="fecha_publicacion" value="<?php echo htmlspecialchars($registro['fecha_publicacion']); ?>" required>

            <label for="observaciones_3_1">OBSERVACIONES
                <span class="tooltip">?
                    <span class="tooltip-text">El nombre de la persona designada como Titular de la Entidad Paramunicipal.</span>
                </span>
            </label>
            <input type="text" name="observaciones_3_1" id="observaciones_3_1" value="<?php echo htmlspecialchars($registro['observaciones_3_1']); ?>" required>

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
        <a class="cancelar" href="../mostrarregistros3_1.php?php echo urlencode($area); ?>&clasificacion=<?php echo urlencode($clasificacion); ?>&anio=<?php echo urlencode($anio); ?>&search=<?php echo urlencode($search); ?>&pagina=<?php echo urlencode($pagina); ?>">Cancelar</a>
    </div>

</body>

</html>