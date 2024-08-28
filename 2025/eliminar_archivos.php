<?php
// Paso 1: Conexión a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "tdp25");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['archivo_id']) && isset($_POST['area'])) {
        $archivo_id = $_POST['archivo_id'];
        $area = $_POST['area'];

        // Paso 2: Usar el valor del área para determinar la tabla
        $tabla = mysqli_real_escape_string($conexion, $area);

        // Paso 3: Obtener la ruta del archivo antes de eliminar el registro
        $consultaRuta = "SELECT ruta FROM `$tabla` WHERE id = '$archivo_id'";
        $resultado = mysqli_query($conexion, $consultaRuta);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $fila = mysqli_fetch_assoc($resultado);
            $rutaArchivo = $fila['ruta'];

            // Paso 4: Eliminar el archivo físicamente si existe
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
            }

            // Paso 5: Construir la consulta SQL para eliminar el registro por su ID
            $consultaEliminar = "DELETE FROM `$tabla` WHERE id = '$archivo_id'";

            // Paso 6: Ejecutar la consulta
            if (mysqli_query($conexion, $consultaEliminar)) {
                echo "Archivo y registro eliminados correctamente.";
            } else {
                echo "Error al eliminar el registro: " . mysqli_error($conexion);
            }
        } else {
            echo "No se encontró un archivo con ese ID en la tabla $tabla.";
        }
    } else {
        echo "ID de archivo o área no proporcionado.";
    }
} else {
    echo "Método de solicitud no válido.";
}

// Paso 7: Cerrar la conexión
mysqli_close($conexion);
?>
