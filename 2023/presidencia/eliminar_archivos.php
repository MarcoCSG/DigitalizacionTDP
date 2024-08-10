<?php
// Paso 1: Conexión a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "dg_misantla");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['archivo_id'])) {
        $archivo_id = $_POST['archivo_id'];

        // Paso 2: Construir la consulta SQL para eliminar el archivo por su ID
        $consulta = "DELETE FROM presidencia WHERE id = '$archivo_id'";

        // Paso 3: Ejecutar la consulta
        if (mysqli_query($conexion, $consulta)) {
            echo "Archivo eliminado correctamente.";
        } else {
            echo "Error al eliminar el archivo: " . mysqli_error($conexion);
        }
    }
}

// Paso 4: Cerrar la conexión
mysqli_close($conexion);
?>
