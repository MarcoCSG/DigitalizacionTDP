<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archivos</title>
    <link rel="stylesheet" href="css/mostrarArchivos.css">
    <link rel="icon" href="img/TDP-REDONDO.png" type="image/x-icon">
</head>

<body>
    <main>
        <h1 class="titulo">DIGITALIZACIÓN</h1>
    </main>
    <section>
        <figure>
            <img class="imgEmpresa" src="img/logoTDP.png" alt="">
        </figure>
    </section>
    
    <?php
    // Paso 1: Conexión a la base de datos
    $conexion = mysqli_connect("localhost", "root", "", "tdp");

    // Paso 2: Obtener los valores de los parámetros de la URL
    if (isset($_GET['subclasificacion']) && isset($_GET['clasificacion']) && isset($_GET['area'])) {
        $subclasificacion = $_GET['subclasificacion'];
        $clasificacion = $_GET['clasificacion'];
        $area = $_GET['area'];

        // Paso 3: Construir la consulta SQL sin LIMIT
        $consultaTotal = "SELECT SUM(cantidad_folios) AS total_folios FROM alumbrado WHERE subclasificacion = '$subclasificacion' AND clasificacion = '$clasificacion' AND area = '$area'";
        
        // Paso 4: Ejecutar la consulta para obtener la suma total de folios
        $resultadoTotal = mysqli_query($conexion, $consultaTotal);
        $filaTotal = mysqli_fetch_assoc($resultadoTotal);
        $totalFolios = $filaTotal['total_folios'];

        // Paso 5: Construir la consulta SQL con LIMIT
        $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
        $filasPorPagina = 10;
        $offset = ($pagina - 1) * $filasPorPagina;
        $consulta = "SELECT * FROM alumbrado WHERE subclasificacion = '$subclasificacion' AND clasificacion = '$clasificacion' AND area = '$area' LIMIT $offset, $filasPorPagina";

        // Paso 6: Ejecución de la consulta con LIMIT
        $resultado = mysqli_query($conexion, $consulta);

        // Paso 7: Mostrar los resultados en una tabla
        ?>

        <h2>Resultados de <?php echo $subclasificacion; ?> en la clasificación <?php echo $clasificacion; ?> del area <?php echo $area; ?></h2>

        <table>
            <thead>
                <tr>
                    <th>Nombre del Archivo PDF</th>
                    <th>Cantidad de Folios</th>
                    <th>Vista Previa e Imprimir</th>
                    <th>ELIMINAR</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($fila = mysqli_fetch_assoc($resultado)) {
                    ?>
                    <tr>
                        <td><img src="img/archivo.png" alt="Icono PDF" class="icon"><?php echo $fila['nombre_archivo']; ?></td>
                        <td><?php echo $fila['cantidad_folios']; ?></td>
                        <td><a href="descargar.php?id=<?php echo $fila['id']; ?>">Ver</a></td>
                        <td>
                            <!-- Agregamos un formulario para enviar la solicitud de eliminación -->
                            <form method="post" action="eliminar_archivos.php">
                                <input type="hidden" name="archivo_id" value="<?php echo $fila['id']; ?>">
                                <button class="boton-eliminar" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Paso 8: Agregar controles de paginación -->
        <div class="paginacion">
            <?php
            // Calcular el número total de páginas
            $totalFilas = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM alumbrado WHERE subclasificacion = '$subclasificacion' AND clasificacion = '$clasificacion' AND area = '$area'"));
            $totalPaginas = ceil($totalFilas / $filasPorPagina);

            // Mostrar enlaces a las páginas
            for ($i = 1; $i <= $totalPaginas; $i++) {
                // Añade la clase 'selected' si la página actual es igual a $i
                $claseSeleccionada = ($pagina == $i) ? 'selected' : '';
                echo "<a class='$claseSeleccionada' href='?subclasificacion=$subclasificacion&clasificacion=$clasificacion&area=$area&pagina=$i'>$i</a> ";
            }
            ?>
        </div>

        <!-- Mostrar la suma total de la cantidad de folios -->
        <p class="totalFolios">Total de Folios: <?php echo $totalFolios; ?></p>

    <?php
    } else {
        echo "<p>No se ha seleccionado una opción válida.</p>";
    }

    // Paso 8: Cerrar la conexión
    mysqli_close($conexion);
    ?>
</body>

</html>
