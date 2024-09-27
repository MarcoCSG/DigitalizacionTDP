<?php
// Configuración de los detalles de la base de datos
$servidor = "localhost";   // Cambia a la dirección de tu servidor de base de datos
$usuario_bd = "root";      // Usuario de la base de datos
$password_bd = "";         // Contraseña del usuario de la base de datos
$base_datos = "tdp";       // Nombre de tu base de datos

// Crear la conexión
$conexion = new mysqli($servidor, $usuario_bd, $password_bd, $base_datos);

// Verificar si la conexión tuvo éxito
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Establecer el juego de caracteres a UTF-8 para evitar problemas con acentos y caracteres especiales
$conexion->set_charset("utf8");

?>
