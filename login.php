<?php
session_start();

// Establecer la conexión con la base de datos
$conexion = mysqli_connect("localhost", "root", "", "tdp");

// Verificar la conexión
if (!$conexion) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar los valores enviados desde el formulario
    $usuario = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
    $contrasena = $_POST["password"];

    // Consulta para verificar el usuario
    $consulta = "SELECT contrasena, rol FROM usuarios WHERE usuario = ?";
    $stmt = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($stmt, "s", $usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $hash_contrasena, $rol);
    mysqli_stmt_fetch($stmt);

    // Verificar la contraseña y el rol del usuario
    if ($hash_contrasena && password_verify($contrasena, $hash_contrasena)) {
        // Inicio de sesión exitoso, guardar el rol en la sesión
        $_SESSION["rol"] = $rol;

        // Redireccionar según el rol del usuario
        if ($rol == "admin") {
            header("Location: agregarUser.html");
        } elseif ($rol == "usuario") {
            header("Location: IndexUsuario.html");
        }
        exit();
    } else {
        // Credenciales inválidas, almacenar mensaje de error en la URL
        header("Location: index.html?error=Contraseña%20incorrecta");
        exit();
    }

    // Liberar el resultado de la consulta y cerrar la sentencia
    mysqli_stmt_close($stmt);
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>
