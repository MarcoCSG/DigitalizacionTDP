<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["rol"])) {
    // Redirigir al inicio de sesión si no está logueado
    header("Location: index.html");
    exit();
}

$rol = $_SESSION["rol"]; // Obtener el rol del usuario logueado

// Verificar si el usuario es administrador
if ($rol !== 'admin') {
    // Si no es admin, redirigir a la página según el municipio
    header("Location: municipio.php");
    exit();
}

include 'php/conexion.php'; // Conexión a la base de datos

// Consultar los usuarios registrados en la base de datos
$query = "SELECT DISTINCT municipio FROM usuarios";
$stmt = $conexion->prepare($query);
$stmt->execute();
$resultadoMunicipios = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Municipio</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" href="img/TDP-REDONDO.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <section>
        <figure>
            <img class="imglogoPrincipal" src="img/logoTDP.png" alt="logo">
        </figure>
        <main>
            <h1 class="titulo">Administración de Municipios</h1>
            <h2 class="subtitulo">Selecciona un municipio para administrar</h2>
        </main>
    </section>
    
    <div class="municipio-selection-container">
        <form action="administrador.php" method="post" class="municipio-form">
            <label for="municipio">Municipio:</label>
            <select id="municipio" name="municipio" required>
                <option value="">Seleccionar Municipio</option>
                <?php while ($row = $resultadoMunicipios->fetch_assoc()) : ?>
                    <option value="<?php echo htmlspecialchars($row['municipio']); ?>">
                        <?php echo htmlspecialchars($row['municipio']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="submit-button">Seleccionar Municipio</button>
        </form>
    </div>
    
    <script src="js/inactividad.js"></script>
</body>
</html>
