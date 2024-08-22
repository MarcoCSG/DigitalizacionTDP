<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tdp";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Obtener la ruta del archivo desde la base de datos
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Error de conexión a la base de datos: " . $conn->connect_error);
    }

    $sql = "SELECT ruta_archivo FROM alumbrado WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($ruta_archivo);

    if ($stmt->fetch()) {
        // Descargar el archivo desde la ruta almacenada en la base de datos
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . basename($ruta_archivo));
        readfile($ruta_archivo); // Envía el archivo al navegador

        exit;
    } else {
        echo "El archivo no existe.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "ID de archivo inválido.";
}
?>