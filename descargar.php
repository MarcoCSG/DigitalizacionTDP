<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tdp";

if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['area'])) {
    $id = $_GET['id'];
    $area = $_GET['area'];

    // Lista de áreas permitidas para evitar inyecciones SQL
    $areasPermitidas = [
        'presidencia',
        'sindicatura',
        'secretaria',
        'regidores',
        'tesoreria',
        'contraloria',
        'obras-publicas'
    ];

    // Validar el área seleccionada
    if (in_array($area, $areasPermitidas)) {
        // Crear el nombre de la tabla basado en el área
        $tabla = $area;

        // Conectar a la base de datos
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Error de conexión a la base de datos: " . $conn->connect_error);
        }

        // Preparar y ejecutar la consulta para obtener la ruta del archivo
        $sql = "SELECT ruta FROM `$tabla` WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($ruta);

        if ($stmt->fetch()) {
            // Verificar si el archivo existe en la ruta especificada
            if (file_exists($ruta)) {
                // Descargar el archivo desde la ruta almacenada en la base de datos
                header("Content-Type: application/octet-stream");
                header("Content-Disposition: attachment; filename=" . basename($ruta));
                header("Content-Length: " . filesize($ruta));
                readfile($ruta); // Envía el archivo al navegador
                exit;
            } else {
                echo "El archivo no existe en la ruta especificada.";
            }
        } else {
            echo "No se encontró el archivo con el ID proporcionado en la base de datos.";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Área no válida.";
    }
} else {
    echo "ID de archivo o área inválidos.";
}
?>
