<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$area = $input['area'];
$periodo = $input['periodo'];
$clasificacion = $input['clasificacion'];
$documento = $input['documento'];

// Conexión a la base de datos (reemplaza con tus credenciales)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tdp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

// Crear la consulta SQL basada en los datos seleccionados
$sql = "SELECT * FROM alumbrado WHERE area = '$area' AND periodo = '$periodo'";

if ($clasificacion) {
    $sql .= " AND clasificacion = '$clasificacion'";
}

if ($documento) {
    $sql .= " AND documento = '$documento'";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = [];

    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
} else {
    echo json_encode(["message" => "No se encontraron resultados."]);
}

$conn->close();
?>
