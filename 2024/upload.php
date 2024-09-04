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
        $errorMsg = "Todos los campos son obligatorios.";
        header("Location: subirinfo2024.html?error=" . urlencode($errorMsg));
        exit();
    }

    // Directorios específicos por área
    $directorios = [
        'presidencia' => 'C:/DIGITALIZACION TDP/PRESIDENCIA/',
        'sindicatura' => 'C:/DIGITALIZACION TDP/SINDICATURA/',
        'secretaria' => 'C:/DIGITALIZACION TDP/SECRETARIA/',
        'regidores' => 'C:/DIGITALIZACION TDP/REGIDORES/',
        'tesoreria' => 'C:/DIGITALIZACION TDP/TESORERIA/',
        'contraloria' => 'C:/DIGITALIZACION TDP/CONTRALORIA/',
        'obraspublicas' => 'C:/DIGITALIZACION TDP/OBRAS PUBLICAS/'
    ];

    // Verificar si el área seleccionada tiene un directorio asignado
    if (isset($directorios[$area])) {
        $targetDir = $directorios[$area];
    } else {
        $errorMsg = "Área no válida seleccionada.";
        header("Location: subirinfo2024.html?error=" . urlencode($errorMsg));
        exit();
    }

    // Procesar archivo
    $nombre_archivo = basename($_FILES["archivo"]["name"]);
    $targetPath = $targetDir . $nombre_archivo;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

    // Validar el tipo de archivo
    $allowedFileTypes = ['pdf', 'png', 'jpeg', 'jpg', 'xml'];
    if (!in_array($fileType, $allowedFileTypes)) {
        $errorMsg = "Solo se permiten archivos de imagen (JPG, JPEG, PNG), PDF y XML.";
        header("Location: subirinfo2024.html?error=" . urlencode($errorMsg));
        exit();
    }

    // Si la carga es válida, proceder a insertar en la base de datos
    if ($uploadOk && move_uploaded_file($_FILES["archivo"]["tmp_name"], $targetPath)) {
        // Conectar a la base de datos específica según el área
        $conn = new mysqli("localhost", "root", "", "tdp24");

        if ($conn->connect_error) {
            $errorMsg = "Error de conexión a la base de datos: " . $conn->connect_error;
            header("Location: subirinfo2024.html?error=" . urlencode($errorMsg));
            exit();
        }

        // Preparar la consulta para insertar los datos
        $sql = "INSERT INTO `$area` (clasificacion, subclasificacion, periodo, nombre_archivo, ruta, cantidad_folios) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $clasificacion, $subclasificacion, $periodo, $nombre_archivo, $targetPath, $cantidad_folios);

        if ($stmt->execute()) {
            $successMsg = "Archivo subido y clasificado exitosamente. Cantidad de folios: " . $cantidad_folios;
            header("Location: subirinfo2024.html?success=" . urlencode($successMsg));
        } else {
            $errorMsg = "Error al clasificar el archivo: " . $stmt->error;
            header("Location: subirinfo2024.html?error=" . urlencode($errorMsg));
        }

        $stmt->close();
        $conn->close();
    } else {
        $errorMsg = "Error al mover el archivo a la carpeta de destino.";
        header("Location: subirinfo2024.html?error=" . urlencode($errorMsg));
    }
} else {
    $errorMsg = "No se recibieron datos del formulario.";
    header("Location: subirinfo2024.html?error=" . urlencode($errorMsg));
}
?>
