<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si los campos existen y obtener los valores
    $area = isset($_POST['area']) ? $_POST['area'] : null;
    $clasificacion = isset($_POST['clasificacion']) ? $_POST['clasificacion'] : null;
    $cantidad_folios = isset($_POST['cantidad_folios']) ? $_POST['cantidad_folios'] : null;

    // Verificar que todos los datos requeridos estén presentes
    if (!$area || !$clasificacion || !$cantidad_folios) {
        if (!$area) echo "El campo 'Área' es obligatorio.<br>";
        if (!$clasificacion) echo "El campo 'Clasificación' es obligatorio.<br>";
        if (!$cantidad_folios) echo "El campo 'Cantidad de folios' es obligatorio.<br>";
        die("Todos los campos son obligatorios.");
    }

    // Directorios específicos por área
    $directorios = [
        'presidencia_er' => 'C:/DIGITALIZACION TDP/PRESIDENCIA/',
        'sindicatura_er' => 'C:/DIGITALIZACION TDP/SINDICATURA/',
        'secretaria_er' => 'C:/DIGITALIZACION TDP/SECRETARIA/',
        'regidores_er' => 'C:/DIGITALIZACION TDP/REGIDORES/',
        'tesoreria_er' => 'C:/DIGITALIZACION TDP/TESORERIA/',
        'contraloria_er' => 'C:/DIGITALIZACION TDP/CONTRALORIA/',
        'obraspublicas_er' => 'C:/DIGITALIZACION TDP/OBRAS PUBLICAS/',
        'regidores_er' => 'C:/DIGITALIZACION TDP/REGIDORES/',
        'areas_er' => 'C:/DIGITALIZACION TDP/TODAS LAS AREAS/',
        'areasUsuarias_er' => 'C:/DIGITALIZACION TDP/AREAS USUARIAS/',
        'Utransparencia_er' => 'C:/DIGITALIZACION TDP/UNIDAD DE TRANSPARENCIA/',

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
        $errorMsg = "Solo se permiten archivos de imagen (JPG, JPEG, PNG), PDF y XML.";
        header("Location: subirinfo_er_2023.html?error=" . urlencode($errorMsg));
        exit();
    }

    // Si la carga es válida, proceder a insertar en la base de datos
    if ($uploadOk && move_uploaded_file($_FILES["archivo"]["tmp_name"], $targetPath)) {
        // Conectar a la base de datos específica según el área
        $conn = new mysqli("localhost", "root", "", "tdp23");

        if ($conn->connect_error) {
            $errorMsg = "Error de conexión a la base de datos: " . $conn->connect_error;
            header("Location: subirinfo_er_2023.html?error=" . urlencode($errorMsg));
            exit();
        }

        // Preparar la consulta para insertar los datos
        $sql = "INSERT INTO `$area` (clasificacion, ruta, cantidad_folios, nombre_archivo) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $clasificacion, $targetPath, $cantidad_folios, $nombre_archivo);

        if ($stmt->execute()) {
            $successMsg = "Archivo subido y clasificado exitosamente. Cantidad de folios: " . $cantidad_folios;
            header("Location: subirinfo_er_2023.html?success=" . urlencode($successMsg));
        } else {
            $errorMsg = "Error al clasificar el archivo: " . $stmt->error;
            header("Location: subirinfo_er_2023.html?error=" . urlencode($errorMsg));
        }

        $stmt->close();
        $conn->close();
    } else {
        $errorMsg = "Error al mover el archivo a la carpeta de destino.";
        header("Location: subirinfo_er_2023.html?error=" . urlencode($errorMsg));
    }
} else {
    $errorMsg = "No se recibieron datos del formulario.";
    header("Location: subirinfo_er_2023.html?error=" . urlencode($errorMsg));
}
?>
