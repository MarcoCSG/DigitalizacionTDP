<?php
include 'conexion.php';

// Obtener los valores enviados por el formulario
$nombre = $_POST['nombre'];
$usuario = $_POST['usuario'];
$contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT); // Encriptar la contraseña
$rol = $_POST['rol'];
$municipio = $_POST['municipio']; // Obtener el municipio seleccionado

// Consulta para insertar un nuevo usuario
$query = "INSERT INTO usuarios (nombre, usuario, contrasena, rol, municipio) VALUES (?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($query);
$stmt->bind_param("sssss", $nombre, $usuario, $contrasena, $rol, $municipio);

// Ejecutar la consulta
if ($stmt->execute()) {
    // Redirigir al administrador de usuarios con un mensaje de éxito
    header("Location: ../agregarUser.php?mensaje=Usuario%20agregado%20correctamente");
} else {
    // Redirigir con un mensaje de error
    header("Location: ../agregarUser.php?error=No%20se%20pudo%20agregar%20el%20usuario");
}

// Cerrar la conexión
$stmt->close();
$conexion->close();
?>

