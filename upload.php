<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si los campos existen y obtener los valores
    $area = isset($_POST['area']) ? $_POST['area'] : null;
    $clasificacion = isset($_POST['clasificacion']) ? $_POST['clasificacion'] : null;
    $subclasificacion = isset($_POST['documentos']) ? $_POST['documentos'] : null;
    $periodo = isset($_POST['periodo']) ? $_POST['periodo'] : null;
    $cantidad_folios = isset($_POST['cantidad_folios']) ? $_POST['cantidad_folios'] : null;

    // Verificar que todos los datos requeridos estén presentes
    if (!$area || !$clasificacion || !$subclasificacion || !$periodo || !$cantidad_folios) {
        if (!$area) echo "El campo 'Área' es obligatorio.<br>";
        if (!$clasificacion) echo "El campo 'Clasificación' es obligatorio.<br>";
        if (!$subclasificacion) echo "El campo 'Documentos' es obligatorio.<br>";
        if (!$periodo) echo "El campo 'Periodo' es obligatorio.<br>";
        if (!$cantidad_folios) echo "El campo 'Cantidad de folios' es obligatorio.<br>";
        die("Todos los campos son obligatorios.");
    }

    // Directorios específicos por área
    $directorios = [
        'presidencia' => 'C:/DIGITALIZACION TDP/PRESIDENCIA/',
        'sindicatura' => 'C:/DIGITALIZACION TDP/SINDICATURA/',
        'secretaria' => 'D:/SECRETARIA/',
        'regidores' => 'D:/REGIDORES/',
        'tesoreria' => 'D:/TESORERIA/',
        'contraloria' => 'D:/CONTRALORIA/',
        'obras-publicas' => 'D:/OBRAS_PUBLICAS/'
    ];

    // Verificar si el área seleccionada tiene un directorio asignado
    if (isset($directorios[$area])) {
        $targetDir = $directorios[$area];
    } else {
        die("Área no válida seleccionada.");
    }

    // Procesar archivo
    $nombre_archivo = basename($_FILES["archivo"]["name"]);
    $targetPath = $targetDir . $nombre_archivo;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

    // Validar el tipo de archivo
    $allowedFileTypes = ['pdf', 'png', 'jpeg', 'jpg', 'xml'];
    if (!in_array($fileType, $allowedFileTypes)) {
        die("Solo se permiten archivos de imagen (JPG, JPEG, PNG), PDF y XML.");
        $uploadOk = 0;
    }

    // Si la carga es válida, proceder a insertar en la base de datos
    if ($uploadOk && move_uploaded_file($_FILES["archivo"]["tmp_name"], $targetPath)) {
        // Conectar a la base de datos específica según el área
        $conn = new mysqli("localhost", "root", "", "tdp");

        if ($conn->connect_error) {
            die("Error de conexión a la base de datos: " . $conn->connect_error);
        }

        // Preparar la consulta para insertar los datos
        $sql = "INSERT INTO `$area` (clasificacion, subclasificacion, periodo, nombre_archivo, ruta, cantidad_folios) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $clasificacion, $subclasificacion, $periodo, $nombre_archivo, $targetPath, $cantidad_folios);

        if ($stmt->execute()) {
            echo "Archivo subido y clasificado exitosamente. Cantidad de folios: " . $cantidad_folios;
        } else {
            echo "Error al clasificar el archivo: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Error al mover el archivo a la carpeta de destino.";
    }
} else {
    echo "No se recibieron datos del formulario.";
}
?>
