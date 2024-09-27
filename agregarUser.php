<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <link rel="stylesheet" href="css/user.css">
    <link rel="icon" href="img/TDP-REDONDO.png" type="image/x-icon">
</head>

<body>
    <div class="container">
        <h1>Agregar Nuevo Usuario</h1>
        <form action="php/agregarUsuario.php" method="post">
            <label for="nombre">Nombre Completo:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="usuario">Nombre de Usuario:</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>

            <label for="rol">Rol:</label>
            <select id="rol" name="rol" required>
                <option value="">Seleccionar Rol</option>
                <option value="admin">Administrador</option>
                <option value="usuario">Usuario</option>
            </select>

            <label for="municipio">Municipio:</label>
            <select id="municipio" name="municipio" required>
                <option value="">Seleccionar Municipio</option>
                <!-- Aquí se pueden agregar los municipios como opciones -->
                <option value="H.AYUNTAMIENTO DE MISANTLA, VER">H.AYUNTAMIENTO DE MISANTLA, VER</option>
                <option value="H.AYUNTAMIENTO DE CORDOBA, VER">H.AYUNTAMIENTO DE CORDOBA, VER</option>
                <option value="H.AYUNTAMIENTO DE ZENTLA, VER">H.AYUNTAMIENTO DE ZENTLA, VER</option>
                <!-- Puedes agregar más municipios según tus necesidades -->
            </select>


            <button type="submit">Agregar Usuario</button>
        </form>

        <h2>Usuarios Registrados</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Municipio</th>
                    <th>Cambiar Contraseña</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Incluir archivo de conexión a la base de datos
                include 'php/conexion.php';

                $query = "SELECT id, nombre, usuario, rol, municipio FROM usuarios";
                $stmt = $conexion->prepare($query);
                $stmt->execute();
                $resultado = $stmt->get_result();

                while ($row = $resultado->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['nombre'] . "</td>";
                    echo "<td>" . $row['usuario'] . "</td>";
                    echo "<td>" . $row['rol'] . "</td>";
                    echo "<td>" . $row['municipio'] . "</td>";
                    echo "<td>";
                    echo "<form action='php/cambiarContrasena.php' method='post'>";
                    echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                    echo "<input type='password' name='nuevaContrasena' placeholder='Nueva Contraseña' required>";
                    echo "<button type='submit'>Cambiar</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="js/inactividad.js"></script>
</body>

</html>