<?php
session_start();

// Incluir la conexión a la base de datos
include 'php/conexion.php';

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar los valores enviados desde el formulario
    $usuario = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
    $contrasena = $_POST["password"];

    // Consulta para verificar el usuario y obtener la contraseña, rol y municipio
    $consulta = "SELECT id, contrasena, rol, municipio FROM usuarios WHERE usuario = ?";
    $stmt = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($stmt, "s", $usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $hash_contrasena, $rol, $municipio);
    mysqli_stmt_fetch($stmt);

    // Verificar la contraseña y el rol del usuario
    if ($hash_contrasena && password_verify($contrasena, $hash_contrasena)) {
        // Inicio de sesión exitoso, guardar el rol, municipio y usuario_id en la sesión
        $_SESSION["rol"] = $rol;
        $_SESSION["usuario"] = $usuario;
        $_SESSION["usuario_id"] = $id; // Agregado para almacenar el ID del usuario

        // Si el usuario es normal, almacenar también el municipio
        if ($rol == "usuario") {
            $_SESSION["municipio"] = $municipio;
        }

        // Redireccionar según el rol del usuario
        if ($rol == "admin") {
            // Los administradores pueden acceder a todo, redirigir a su panel
            header("Location: municipios_admin.php");
        } elseif ($rol == "usuario") {
            header("Location: IndexUsuario.php"); // Ajusta los parámetros según corresponda
        }
        exit();
    } else {
        // Credenciales inválidas, almacenar mensaje de error en la URL
        header("Location: index.html?error=Credenciales%20incorrectas");
        exit();
    }

    // Liberar el resultado de la consulta y cerrar la sentencia
    mysqli_stmt_close($stmt);
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>
