<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archivos</title>
    <link rel="stylesheet" href="css/mostrarArchivos.css">
    <link rel="icon" href="img/TDP-REDONDO.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <main>
        <h1 class="titulo">DIGITALIZACIÓN E-GOBIERNO</h1>
    </main>
    <section>
        <figure>
            <img class="imgEmpresa" src="img/logoTDP.png" alt="">
        </figure>
    </section>

    <?php
    // Paso 1: Incluir el archivo de conexión a la base de datos
    include 'php/conexion.php';

    // Paso 2: Obtener los valores de los parámetros de la URL
    if (isset($_GET['subclasificacion']) && isset($_GET['clasificacion']) && isset($_GET['area']) && isset($_GET['periodo'])) {
        $subclasificacion = $_GET['subclasificacion'];
        $clasificacion = $_GET['clasificacion'];
        $area = $_GET['area'];
        $periodo = $_GET['periodo'];
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        // Escapar valores para prevenir inyecciones SQL
        $subclasificacion = mysqli_real_escape_string($conexion, $subclasificacion);
        $clasificacion = mysqli_real_escape_string($conexion, $clasificacion);
        $area = mysqli_real_escape_string($conexion, $area);
        $periodo = mysqli_real_escape_string($conexion, $periodo);
        $search = mysqli_real_escape_string($conexion, $search);
    ?>

        <div class="search-container">
            <form method="get" action="">
                <input type="hidden" name="subclasificacion" value="<?php echo htmlspecialchars($subclasificacion); ?>">
                <input type="hidden" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion); ?>">
                <input type="hidden" name="area" value="<?php echo htmlspecialchars($area); ?>">
                <input type="hidden" name="periodo" value="<?php echo htmlspecialchars($periodo); ?>">
                <input type="text" name="search" placeholder="Buscar archivo" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </form>
        </div>

        <!-- Mensaje de resumen -->
        <p>En el área de <strong><?php echo htmlspecialchars($area); ?></strong>, en el rubro de <strong><?php echo htmlspecialchars($clasificacion); ?></strong>, de la clasificación <strong><?php echo htmlspecialchars($subclasificacion); ?></strong>, en el periodo <strong><?php echo htmlspecialchars($periodo); ?></strong>.</p>

        <?php
        // Paso 3: Construir la consulta SQL sin LIMIT
        $consultaTotal = "SELECT SUM(cantidad_folios) AS total_folios FROM `$area` WHERE subclasificacion = '$subclasificacion' AND clasificacion = '$clasificacion'";

        if ($periodo !== 'anual') {
            $consultaTotal .= " AND periodo = '$periodo'";
        }

        if (!empty($search)) {
            $consultaTotal .= " AND nombre_archivo LIKE '%$search%'";
        }

        // Paso 4: Ejecutar la consulta para obtener la suma total de folios
        $resultadoTotal = mysqli_query($conexion, $consultaTotal);
        $filaTotal = mysqli_fetch_assoc($resultadoTotal);
        $totalFolios = $filaTotal['total_folios'];

        // Paso 5: Construir la consulta SQL con LIMIT
        $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
        $filasPorPagina = 10;
        $offset = ($pagina - 1) * $filasPorPagina;

        $consulta = "SELECT * FROM `$area` WHERE subclasificacion = '$subclasificacion' AND clasificacion = '$clasificacion'";

        if ($periodo !== 'anual') {
            $consulta .= " AND periodo = '$periodo'";
        }

        if (!empty($search)) {
            $consulta .= " AND nombre_archivo LIKE '%$search%'";
        }

        $consulta .= " LIMIT $offset, $filasPorPagina";

        // Paso 6: Ejecución de la consulta con LIMIT
        $resultado = mysqli_query($conexion, $consulta);

        // Paso 7: Mostrar los resultados en una tabla
        ?>

        <table>
            <thead>
                <tr>
                    <th>Nombre del Archivo PDF</th>
                    <th>Cantidad de Folios</th>
                    <th class="ver-col">Vista Previa e Imprimir</th>
                    <th class="eliminar-col">ELIMINAR</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($fila = mysqli_fetch_assoc($resultado)) {
                ?>
                    <tr>
                        <td><img src="img/nube.png" alt="Icono PDF" class="icon"><?php echo htmlspecialchars($fila['nombre_archivo']); ?></td>
                        <td><?php echo htmlspecialchars($fila['cantidad_folios']); ?></td>
                        <td class="ver-col"><a href="descargar.php?id=<?php echo htmlspecialchars($fila['id']); ?>&area=<?php echo htmlspecialchars($area); ?>">Ver</a></td>
                        
                        <td class="eliminar-col">
                            <!-- Agregamos un formulario para enviar la solicitud de eliminación -->
                            <form method="post" action="eliminar_archivos.php">
                                <input type="hidden" name="archivo_id" value="<?php echo htmlspecialchars($fila['id']); ?>">
                                <input type="hidden" name="area" value="<?php echo htmlspecialchars($area); ?>">
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
            $totalFilas = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM `$area` WHERE subclasificacion = '$subclasificacion' AND clasificacion = '$clasificacion'" . ($periodo !== 'anual' ? " AND periodo = '$periodo'" : "") . (!empty($search) ? " AND nombre_archivo LIKE '%$search%'" : "")));
            $totalPaginas = ceil($totalFilas / $filasPorPagina);

            // Mostrar enlaces a las páginas
            for ($i = 1; $i <= $totalPaginas; $i++) {
                // Añade la clase 'selected' si la página actual es igual a $i
                $claseSeleccionada = ($pagina == $i) ? 'selected' : '';
                echo "<a class='$claseSeleccionada' href='?subclasificacion=$subclasificacion&clasificacion=$clasificacion&area=$area&periodo=$periodo&search=" . urlencode($search) . "&pagina=$i'>$i</a> ";
            }
            ?>
        </div>

        <!-- Mostrar la suma total de la cantidad de folios -->
        <p class="totalFolios">Total de Folios: <?php echo htmlspecialchars($totalFolios); ?></p>

    <?php
    } else {
        echo "<p>No se ha seleccionado una opción válida.</p>";
    }

    // Paso 9: Cerrar la conexión
    mysqli_close($conexion);
    ?>

<script src="js/inactividad.js"></script>
</body>

</html>
