<?php 
session_start();

// Activar la visualización de errores para depuración (desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si el usuario ha iniciado sesión y tiene un municipio asignado
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"]) || !isset($_SESSION["usuario_id"])) {
    echo "<script>alert('Sesión no válida. Redirigiendo al inicio de sesión.'); window.location.href = '../index.html';</script>";
    exit();
}

$municipio = $_SESSION["municipio"]; // Obtener el municipio del usuario logado
$usuario_id = $_SESSION["usuario_id"]; // Obtener el ID del usuario

// Incluir configuración de la base de datos
include 'conexion.php'; // Se asume que este archivo establece la conexión y asigna a $conexion

// Verificar conexión a la base de datos
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Procesar el formulario cuando se envíe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    // Recuperar datos del formulario y sanitizarlos
    $no = trim($_POST['no']);
    $denominacion = trim($_POST['denominacion']);

    $publicacion_fecha = trim($_POST['publicacion_fecha']);
    if ($publicacion_fecha) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $publicacion_fecha = date('d/m/Y', strtotime($publicacion_fecha));
    }
    $informacion_al = trim($_POST['informacion_al']);
    if ($informacion_al) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $informacion_al = date('d/m/Y', strtotime($informacion_al));
    }
    $fecha_autorizacion = $_POST['fecha_autorizacion'];
    if ($fecha_autorizacion) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $fecha_autorizacion = date('d/m/Y', strtotime($fecha_autorizacion));
    }
    
    $responsable = trim($_POST['responsable']);
    $observaciones = trim($_POST['observaciones']);

    // Obtener parámetros del formulario (campos ocultos)
    $area = isset($_POST['area']) ? $_POST['area'] : '';
    $clasificacion = isset($_POST['clasificacion']) ? $_POST['clasificacion'] : '';

    // Validar datos
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

    // Asignar el año fijo 2022
    $anio = "2022";

    if (count($errores) === 0) {
        // Iniciar transacción
        $conexion->begin_transaction();

        try {
            // Obtener el ID de área
            $stmt = $conexion->prepare("SELECT id FROM areas WHERE nombre = ?");
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
            }
            $stmt->bind_param("s", $area);
            $stmt->execute();
            $stmt->bind_result($area_id);
            if (!$stmt->fetch()) {
                $stmt->close();
                throw new Exception("Área no encontrada.");
            }
            $stmt->close();

            // Obtener el ID de clasificación
            $stmt = $conexion->prepare("SELECT id FROM clasificaciones WHERE codigo = ? AND area_id = ?");
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
            }
            $stmt->bind_param("si", $clasificacion, $area_id);
            $stmt->execute();
            $stmt->bind_result($clasificacion_id);
            if (!$stmt->fetch()) {
                $stmt->close();
                throw new Exception("Clasificación no encontrada para el área especificada.");
            }
            $stmt->close();

            // Insertar en la tabla 'formatos' (incluyendo el area_id)
            $stmt = $conexion->prepare("INSERT INTO formatos (usuarios_id, clasificaciones_id, area_id, municipio, anio, ruta_archivo) VALUES (?, ?, ?, ?, ?, NULL)");
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
            }
            $stmt->bind_param("iiiss", $usuario_id, $clasificacion_id, $area_id, $municipio, $anio);
            if (!$stmt->execute()) {
                $stmt->close();
                throw new Exception("Error al insertar en 'formatos': " . $stmt->error);
            }
            $formato_id = $stmt->insert_id;
            $stmt->close();

            // Insertar en la tabla específica según la clasificación
            $tabla_especifica = "formato_" . str_replace('.', '_', $clasificacion);
            $tabla_permitidas = ["formato_1_2", "formato_2_1"]; // Lista de tablas específicas permitidas

            if (in_array($tabla_especifica, $tabla_permitidas)) {
                if ($clasificacion === '1.2') {
                    $stmt = $conexion->prepare("INSERT INTO formato_1_2 (formato_id, no, denominacion, publicacion_fecha, informacion_al, fecha_autorizacion, responsable, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    if (!$stmt) {
                        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
                    }
                    $stmt->bind_param("iissssss", $formato_id, $no, $denominacion, $publicacion_fecha, $informacion_al, $fecha_autorizacion, $responsable, $observaciones);
                } elseif ($clasificacion === '2.1') {
                    // Implementar la lógica para la clasificación '2.1' si es necesario
                    $stmt = $conexion->prepare("INSERT INTO formato_2_1 (formato_id, no, denominacion, publicacion_fecha, informacion_al, fecha_autorizacion, responsable, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    if (!$stmt) {
                        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
                    }
                    $stmt->bind_param("iissssss", $formato_id, $no, $denominacion, $publicacion_fecha, $informacion_al, $fecha_autorizacion, $responsable, $observaciones);
                } else {
                    throw new Exception("Clasificación no soportada.");
                }

                if (!$stmt->execute()) {
                    $stmt->close();
                    throw new Exception("Error al insertar en '$tabla_especifica': " . $stmt->error);
                }
                $stmt->close();
            } else {
                throw new Exception("Clasificación no permitida o tabla específica no definida.");
            }

            // Si todo está bien, confirmar la transacción
            $conexion->commit();

            // Redirigir o mostrar un mensaje de éxito
            echo "<script>alert('Datos guardados exitosamente.'); window.location.href = '../1.2_manuales.php?area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&anio=2022';</script>";

        } catch (Exception $e) {
            // Deshacer la transacción en caso de error
            $conexion->rollback();
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        }

    } else {
        // Mostrar errores de validación
        foreach ($errores as $error) {
            echo "<script>alert('" . addslashes($error) . "'); window.history.back();</script>";
        }
    }

    // Cerrar conexión
    $conexion->close();
}
?>
