<?php
session_start();

// Verificar si el usuario ha iniciado sesión y tiene un municipio asignado
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    // Redirigir al inicio de sesión si no está logueado
    header("Location: index.html");
    exit();
}

$municipio = $_SESSION["municipio"]; // Obtener el municipio del usuario logueado
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TDP</title>
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
            <h1 class="titulo"><?php echo htmlspecialchars($municipio); ?></h1>
            <h2 class="subtitulo">EJERCICIOS FISCALES</h2>
        </main>
    </section>
    <div class="button-container">
        <button onclick="location.href='municipio.php'">2022<img src="img/ayuntamiento.png" alt="Icono"></button>
        <button onclick="location.href='2023/municipio.html'">2023<img src="img/ayuntamiento.png" alt="Icono"></button>
        <button onclick="location.href='2024/municipio.html'">2024<img src="img/ayuntamiento.png" alt="Icono"></button>
        <button onclick="location.href='2025/municipio.html'">2025<img src="img/ayuntamiento.png" alt="Icono"></button>
    </div>
    
    <script src="js/inactividad.js"></script>
</body>
</html>
4