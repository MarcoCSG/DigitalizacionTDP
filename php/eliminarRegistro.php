<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    header("Location: index.html?error=No%20has%20iniciado%20sesión");
    exit();
}

// Incluir la conexión a la base de datos
include 'php/conexion.php';

// Obtener el ID del registro a eliminar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verificar que el ID es válido
if ($id > 0) {
    // Preparar la consulta para eliminar de 'formato_1_2' y 'formatos'
    // Dado que 'formato_1_2' está relacionada con 'formatos' mediante 'formato_id', eliminar primero 'formato_1_2' está bien.

    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Eliminar de 'formato_1_2'
        $stmt = $conexion->prepare("DELETE FROM formato_1_2 WHERE formato_id = ?");
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Error al eliminar de formato_1_2: " . $stmt->error);
        }
        $stmt->close();

        // Eliminar de 'formatos'
        $stmt = $conexion->prepare("DELETE FROM formatos WHERE id = ?");
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Error al eliminar de formatos: " . $stmt->error);
        }
        $stmt->close();

        // Confirmar la transacción
        $conexion->commit();

        echo "<script>alert('Registro eliminado exitosamente.'); window.location.href = 'mostrarRegistros.php?area=" . urlencode($_SESSION["municipio"]) . "&clasificacion=1.2&subclasificacion=tipo1&periodo=anual';</script>";
    } catch (Exception $e) {
        // Deshacer la transacción en caso de error
        $conexion->rollback();
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>ID de registro inválido.</p>";
}

// Cerrar la conexión
$conexion->close();
?>
