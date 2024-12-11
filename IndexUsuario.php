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
        <!-- Se pasa el valor del año como parámetro en la URL -->
        <button onclick="location.href='municipio.php?anio=2022'">2022<img src="img/ayuntamiento.png" alt="Icono"></button>
        <button onclick="location.href='municipio.php?anio=2023'">2023<img src="img/ayuntamiento.png" alt="Icono"></button>
        <button onclick="location.href='municipio.php?anio=2024'">2024<img src="img/ayuntamiento.png" alt="Icono"></button>
        <button onclick="location.href='municipio.php?anio=2025'">2025<img src="img/ayuntamiento.png" alt="Icono"></button>
    </div>

    <script src="js/inactividad.js"></script>
</body>
<footer>
    <div class="footer-container">
        <h2 class="footer-title">Documentos de Información Para La Entrega Recepción</h2>
        <div class="footer-buttons">
            <div class="section">
                <h3>COMUNICADOS</h3>
                <button onclick="location.href='https://onedrive.live.com/download?resid=67F982E3012322AB%21116438'">COMUNICADO 1</button>
                <button onclick="location.href='https://onedrive.live.com/download?resid=67F982E3012322AB%21116437'">COMUNICADO 2</button>
                <button onclick="location.href='https://onedrive.live.com/download?resid=67F982E3012322AB%21116436'">COMUNICADO 3</button>
                <button onclick="location.href='https://onedrive.live.com/download?resid=67F982E3012322AB%21116435'">COMUNICADO 4</button>
                <button onclick="location.href='https://onedrive.live.com/download?resid=67F982E3012322AB%21116434'">COMUNICADO 5</button>
            </div>

            <div class="section">
                <h3>ENTREGA RECEPCIÓN</h3>
                <button onclick="location.href='https://onedrive.live.com/download?resid=67F982E3012322AB%21116429'">IVAI. AVISO - ACTAS ENTREGA RECEPCIÓN</button>
                <button onclick="location.href='https://onedrive.live.com/download?resid=67F982E3012322AB%21116430'">IVAI. GUIA DE PROCESOS ENTREGA RECEPCIÓN</button>
                <button onclick="location.href='https://onedrive.live.com/download?resid=67F982E3012322AB%21116427'">IVAI. POLITICAS ENTREGA RECEPCIÓN</button>
                <button onclick="location.href='https://onedrive.live.com/download?resid=67F982E3012322AB%21116428'">IVAI. RECOMENDACIONES ENTREGA RECEPCIÓN</button>
            </div>

            <div class="section">
                <h3>GUIAS</h3>
                <button onclick="location.href='https://onedrive.live.com/download?resid=67F982E3012322AB%21116439'">GUIA PROCESO ENTREGA RECEPCIÓN</button>
                <button onclick="location.href='https://onedrive.live.com/download?resid=67F982E3012322AB%21116441'">CAPACITACIÓN PARA LA ENTREGA RECEPCIÓN</button>
            </div>

            <div class="section">
                <h3>ORFIS</h3>
                <button onclick="location.href='https://onedrive.live.com/download?resid=67F982E3012322AB%21116431'">INFORMACIÓN ENTREGA RECEPCIÓN</button>
            </div>

            <div class="section">
                <h3>LEY</h3>
                <button onclick="location.href='https://onedrive.live.com/download?resid=67F982E3012322AB%21116426'">LEY PARA ENTREGA RECEPCIÓN </button>
            </div>
        </div>
    </div>
</footer>

</html>