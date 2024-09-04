<?php
// Conectar a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "tdp");

if (!$conexion) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Obtener todos los usuarios
$consulta = "SELECT id, contrasena FROM usuarios";
$resultado = mysqli_query($conexion, $consulta);

while ($fila = mysqli_fetch_assoc($resultado)) {
    $id = $fila['id'];
    $contrasena_plana = $fila['contrasena'];
    
    // Crear un hash de la contraseña
    $contrasena_hash = password_hash($contrasena_plana, PASSWORD_BCRYPT);
    
    // Actualizar la contraseña en la base de datos
    $actualizar = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
    $stmt = mysqli_prepare($conexion, $actualizar);
    mysqli_stmt_bind_param($stmt, "si", $contrasena_hash, $id);
    mysqli_stmt_execute($stmt);
}

// Cerrar la conexión
mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>
