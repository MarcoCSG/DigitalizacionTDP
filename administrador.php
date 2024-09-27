<?php
session_start();
// Verificar si el usuario ha iniciado sesión y tiene un municipio asignado
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    // Redirigir al inicio de sesión si no está logueado
    header("Location: index.html");
    exit();
}

$municipioSeleccionado = isset($_POST['municipio']) ? $_POST['municipio'] : null;

// Si no se seleccionó ningún municipio, redirigir a la página anterior
if (!$municipioSeleccionado) {
    header("Location: index.html");
    exit();
}

// Obtener el municipio del usuario logueado
$municipio = $_SESSION["municipio"]; 

// Aquí puedes incluir la conexión a tu base de datos
include 'php/conexion.php';

// Filtrar datos de la base de datos por municipio
$query = "SELECT * FROM usuarios WHERE municipio = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("s", $municipioSeleccionado);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TDP - <?php echo htmlspecialchars($municipioSeleccionado); ?></title>
    <link rel="stylesheet" href="css/style2.css">
    <link rel="icon" href="img/TDP-REDONDO.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
</head>
<body>
    <section>
        <figure>
            <img class="imglogoPrincipal" src="img/logoTDP.png" alt="logo">
        </figure>
        <main>
            <h1 class="titulo">TECNOLOGÍA, DISEÑO Y PRODUCTIVIDAD</h1>
            <h2 class="subtitulo"><?php echo htmlspecialchars($municipioSeleccionado); ?></h2>
        </main>
    </section>
    
    <div class="button-container">
        <button onclick="location.href='agregarUser.php'">REGISTRAR USUARIO<img src="img/gente.png" alt="Icono"></button>
        <button onclick="location.href='indexUsuario.php'">EJERCICIOS FISCALES<img src="img/ayuntamiento.png" alt="Icono"></button>
        <button onclick="location.href='entregaRecepcion.php'">RESPONSABLES<img src="img/responsable.png" alt="Icono"></button>
        <button onclick="location.href='subirInfo_ER_2022.html'">LOGOS<img src="img/logos.png" alt="Icono"></button>
    </div>
    
    <script src="js/inactividad.js"></script>
</body>
</html>
