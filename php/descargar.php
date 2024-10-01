<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    header("Location: index.html?error=No%20has%20iniciado%20sesión");
    exit();
}

// Incluir la conexión a la base de datos
include 'php/conexion.php';

// Obtener el ID del registro
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Obtener la ruta del archivo
    $stmt = $conexion->prepare("SELECT ruta_archivo FROM formatos WHERE id = ? AND usuarios_id = ?");
    $stmt->bind_param("ii", $id, $_SESSION["usuario_id"]);
    $stmt->execute();
    $stmt->bind_result($ruta_archivo);
    if ($stmt->fetch()) {
        $stmt->close();

        // Verificar que el archivo existe
        if (file_exists($ruta_archivo)) {
            // Forzar la descarga
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($ruta_archivo) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($ruta_archivo));
            readfile($ruta_archivo);
            exit;
        } else {
            echo "<p>El archivo no existe.</p>";
        }
    } else {
        echo "<p>No se encontró el archivo o no tienes permisos para acceder a él.</p>";
    }
} else {
    echo "<p>ID de registro inválido.</p>";
}

// Cerrar la conexión
$conexion->close();
?>
