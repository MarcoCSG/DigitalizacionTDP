<?php
session_start();
// Verificar si el usuario ha iniciado sesión y tiene un municipio asignado
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    header("Location: index.html");
    exit();
}

// Obtener parámetros de la URL
$area = isset($_GET['area']) ? $_GET['area'] : '';
$clasificacion = isset($_GET['clasificacion']) ? $_GET['clasificacion'] : '';

// Depuración: mostrar el valor de $area
var_dump($area); // Muestra el valor de $area
echo "Área solicitada: " . htmlspecialchars($area); // Asegúrate de que esto no está en HTML aún


// Incluir configuración de la base de datos
include 'conexion.php'; // Se asume que este archivo contiene la conexión a la base de datos
// Listar todas las áreas para depuración
$result = $conexion->query("SELECT nombre FROM areas");
if ($result) {
    echo "<h3>Áreas en la Base de Datos:</h3><ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['nombre']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "Error al listar áreas: " . $conexion->error;
}

// Procesar el formulario cuando se envíe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    // Recuperar datos del formulario y sanitizarlos
    $no = trim($_POST['no']);
    $denominacion = trim($_POST['denominacion']);
    $publicacion_fecha = trim($_POST['publicacion_fecha']);
    $informacion_al = trim($_POST['informacion_al']);
    $fecha_autorizacion = $_POST['fecha_autorizacion'];
    $responsable = trim($_POST['responsable']);
    $observaciones = trim($_POST['observaciones']);

    // Validar datos (puedes agregar más validaciones según tus necesidades)
    $errores = [];
    if (empty($no) || !is_numeric($no)) {
        $errores[] = "El campo 'No.' es obligatorio y debe ser numérico.";
    }
    if (empty($denominacion)) {
        $errores[] = "El campo 'DENOMINACIÓN' es obligatorio.";
    }
    if (empty($publicacion_fecha)) {
        $errores[] = "El campo 'PUBLICACIÓN Y FECHA' es obligatorio.";
    }
    if (empty($informacion_al)) {
        $errores[] = "El campo 'INFORMACIÓN AL' es obligatorio.";
    }
    if (empty($fecha_autorizacion)) {
        $errores[] = "El campo 'FECHA' es obligatorio.";
    }
    if (empty($responsable)) {
        $errores[] = "El campo 'RESPONSABLE DE LA INFORMACIÓN' es obligatorio.";
    }
    // 'observaciones' es opcional

    if (count($errores) === 0) {
        // Iniciar transacción
        $conexion->begin_transaction();

        try {
            // Obtener el ID del usuario desde la sesión
            $usuario_nombre = $_SESSION["usuario"];

            // Obtener el ID del usuario
            $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
            $stmt->bind_param("s", $usuario_nombre);
            $stmt->execute();
            $stmt->bind_result($usuario_id);
            if (!$stmt->fetch()) {
                throw new Exception("Usuario no encontrado.");
            }
            $stmt->close();

            // Obtener el ID de área
            $stmt = $conexion->prepare("SELECT id FROM areas WHERE nombre = ?");
            $stmt->bind_param("s", $area);
            $stmt->execute();
            $stmt->bind_result($area_id);
            if (!$stmt->fetch()) {
                echo "Área solicitada: " . htmlspecialchars($area); // Muestra el área solicitada
                throw new Exception("Área no encontrada.");
            }
            $stmt->close();
            

            // Obtener el ID de clasificación
            $stmt = $conexion->prepare("SELECT id FROM clasificaciones WHERE codigo = ? AND area_id = ?");
            $stmt->bind_param("si", $clasificacion, $area_id);
            $stmt->execute();
            $stmt->bind_result($clasificacion_id);
            if (!$stmt->fetch()) {
                throw new Exception("Clasificación no encontrada para el área especificada.");
            }
            $stmt->close();

            // Obtener el año de la fecha de autorización
            $anio = date("Y", strtotime($fecha_autorizacion));

            // Insertar en la tabla 'formatos'
            $stmt = $conexion->prepare("INSERT INTO formatos (usuario_id, clasificacion_id, municipio, anio) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iisi", $usuario_id, $clasificacion_id, $municipio, $anio);
            if (!$stmt->execute()) {
                throw new Exception("Error al insertar en 'formatos': " . $stmt->error);
            }
            $formato_id = $stmt->insert_id;
            $stmt->close();

            // Insertar en la tabla específica según la clasificación
            $tabla_especifica = "formato_" . str_replace('.', '_', $clasificacion);
            $tabla_permitidas = ["1_2", "2_1"]; // Lista de tablas específicas permitidas
            if (in_array(str_replace('.', '_', $clasificacion), $tabla_permitidas)) {
                if ($clasificacion === '1.2') {
                    $stmt = $conexion->prepare("INSERT INTO formato_1_2 (formato_id, no, denominacion, publicacion_fecha, informacion_al, fecha_autorizacion, responsable, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("iissssss", $formato_id, $no, $denominacion, $publicacion_fecha, $informacion_al, $fecha_autorizacion, $responsable, $observaciones);
                } elseif ($clasificacion === '2.1') {
                    throw new Exception("Implementación para la clasificación '2.1' no está definida.");
                } else {
                    throw new Exception("Clasificación no soportada.");
                }

                if (!$stmt->execute()) {
                    throw new Exception("Error al insertar en '$tabla_especifica': " . $stmt->error);
                }
                $stmt->close();
            } else {
                throw new Exception("Clasificación no permitida o tabla específica no definida.");
            }

            // Si todo está bien, confirmar la transacción
            $conexion->commit();

            // Redirigir o mostrar un mensaje de éxito
            echo "<script>alert('Datos guardados exitosamente.'); window.location.href = '1.2_manuales.php?area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "';</script>";

        } catch (Exception $e) {
            // Deshacer la transacción en caso de error
            $conexion->rollback();
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }

    } else {
        // Mostrar errores de validación
        foreach ($errores as $error) {
            echo "<script>alert('" . addslashes($error) . "');</script>";
        }
    }

    // Cerrar conexión
    $conexion->close();
}
?>