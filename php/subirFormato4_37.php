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
    $banco = htmlspecialchars(trim($_POST['banco']), ENT_QUOTES, 'UTF-8');
    $no_cuenta = filter_input(INPUT_POST, 'no_cuenta', FILTER_SANITIZE_NUMBER_INT);
    $total = filter_input(INPUT_POST, 'total', FILTER_SANITIZE_NUMBER_INT);
    $utilizados = filter_input(INPUT_POST, 'utilizados', FILTER_SANITIZE_NUMBER_INT);
    $por_utilizar = filter_input(INPUT_POST, 'por_utilizar', FILTER_SANITIZE_NUMBER_INT);
    $cancelados = filter_input(INPUT_POST, 'cancelados', FILTER_SANITIZE_NUMBER_INT);
    
    $informacion_al = htmlspecialchars(trim($_POST['informacion_al']), ENT_QUOTES, 'UTF-8');
    if ($informacion_al) {
        // Convertir la fecha de yyyy-mm-dd a dd/mm/yyyy
        $informacion_al = date('d/m/Y', strtotime($informacion_al));
    }
    
    $responsable = htmlspecialchars(trim($_POST['responsable']), ENT_QUOTES, 'UTF-8');
    
    // Obtener parámetros del formulario (campos ocultos)
    $area = isset($_POST['area']) ? $_POST['area'] : '';
    $clasificacion = isset($_POST['clasificacion']) ? $_POST['clasificacion'] : '';

    // Validar datos
    $errores = [];
    if (empty($banco)) {
        $errores[] = "El campo 'banco' es obligatorio.";
    }
    if (empty($no_cuenta) || !is_numeric($no_cuenta)) {
        $errores[] = "El campo 'NO CUENTA' es obligatorio y debe ser numérico.";
    }
    if (empty($total) || !is_numeric($total)) {
        $errores[] = "El campo 'TOTAL' es obligatorio y debe ser numérico.";
    }
    if (empty($utilizados) || !is_numeric($utilizados)) {
        $errores[] = "El campo 'UTILIZADOS' es obligatorio y debe ser numérico.";
    }
    if (empty($por_utilizar) || !is_numeric($por_utilizar)) {
        $errores[] = "El campo 'POR UTILIZAR' es obligatorio y debe ser numérico.";
    }
    if (empty($cancelados) || !is_numeric($cancelados)) {
        $errores[] = "El campo 'CANCELADOS' es obligatorio y debe ser numérico.";
    }
    // if (empty($informacion_al)) {
    //     $errores[] = "El campo 'INFORMACIÓN AL' es obligatorio.";
    // }
    // if (empty($responsable)) {
    //     $errores[] = "El campo 'RESPONSABLE DE LA INFORMACIÓN' es obligatorio.";
    // }

    // Asignar el año actual
    $anio = date("2022");

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

            // Insertar en la tabla 'formato_4_37'
            $stmt = $conexion->prepare("INSERT INTO formato_4_37 (formato_id, banco, no_cuenta, total, utilizados, por_utilizar, cancelados, informacion_al, responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
            }
            $stmt->bind_param("isiiiiiss", $formato_id, $banco, $no_cuenta, $total, $utilizados, $por_utilizar, $cancelados, $informacion_al, $responsable);

            if (!$stmt->execute()) {
                $stmt->close();
                throw new Exception("Error al insertar en 'formato_4_37': " . $stmt->error);
            }
            $stmt->close();

            // Si todo está bien, confirmar la transacción
            $conexion->commit();

            // Redirigir o mostrar un mensaje de éxito
            echo "<script>alert('Datos guardados exitosamente.'); window.location.href = '../4.37_situacionTalonarioCheques.php?area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&anio=2022';</script>";

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
