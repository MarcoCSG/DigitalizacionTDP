<?php
// Conectar a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "tdp");

if (!$conexion) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar los valores enviados desde el formulario
    $nombre = filter_var($_POST["nombre"], FILTER_SANITIZE_STRING);
    $usuario = filter_var($_POST["usuario"], FILTER_SANITIZE_STRING);
    $contrasena = $_POST["contrasena"];
    $rol = $_POST["rol"];

    // Crear un hash de la contraseña
    $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);

    // Consultar para agregar el nuevo usuario
    $consulta = "INSERT INTO usuarios (nombre, usuario, contrasena, rol) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($stmt, "ssss", $nombre, $usuario, $contrasena_hash, $rol);
    mysqli_stmt_execute($stmt);

    // Verificar si el usuario fue agregado exitosamente
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo "Usuario agregado exitosamente.";
    } else {
        echo "Error al agregar el usuario.";
    }

    // Cerrar la sentencia
    mysqli_stmt_close($stmt);
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>
