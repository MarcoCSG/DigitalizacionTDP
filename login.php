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
    // Obtener los valores enviados desde el formulario
    $usuario = $_POST["username"];
    $contrasena = $_POST["password"];

    // Consulta para verificar el inicio de sesión
    $consulta = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND contrasena = '$contrasena'";
    $resultado = mysqli_query($conexion, $consulta);

    // Verificar si se encontró un usuario válido
    if (mysqli_num_rows($resultado) == 1) {
        // Inicio de sesión exitoso, obtener el rol del usuario
        $fila = mysqli_fetch_assoc($resultado);
        $rol = $fila["rol"];

        // Guardar el rol en la sesión
        $_SESSION["rol"] = $rol;

        // Redireccionar según el rol del usuario
        if ($rol == "admin") {
            header("Location: subirInfo.html");
        } elseif ($rol == "usuario") {
            header("Location: IndexUsuario.html");
        } 
        exit();
    } else {
        // Credenciales inválidas, mostrar un mensaje de error
        echo "Nombre de usuario o contraseña incorrectos";
    }

    // Liberar el resultado de la consulta
    mysqli_free_result($resultado);
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>
