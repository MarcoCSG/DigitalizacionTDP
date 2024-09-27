<?php
// Incluir la conexión a la base de datos
include 'conexion.php';

if (isset($_POST['id']) && isset($_POST['nuevaContrasena'])) {
    // Sanitizar y validar el ID
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

    // Hashear la nueva contraseña
    $nuevaContrasena = password_hash($_POST['nuevaContrasena'], PASSWORD_DEFAULT);

    // Preparar la consulta para actualizar la contraseña
    $query = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("si", $nuevaContrasena, $id); // "si" significa "string" e "integer"

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "Contraseña actualizada correctamente";
    } else {
        echo "Error al actualizar la contraseña: " . $stmt->error;
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conexion->close();
} else {
    echo "Datos incompletos";
}
?>
