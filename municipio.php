<?php
session_start();

// Verificar si el usuario ha iniciado sesión y tiene un municipio asignado
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    header("Location: index.html");
    exit();
}

$municipio = $_SESSION["municipio"];
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y'); // Obtener el año de la URL, o el año actual por defecto


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
            <!-- <h1 class="titulo">TECNOLOGÍA, DISEÑO Y PRODUCTIVIDAD</h1> -->
            <h2 class="subtitulo"><?php echo htmlspecialchars($municipio); ?></h2>
        </main>
    </section>
    <div class="button-container">
        <!-- <button onclick="location.href='departamentos2022.html'">DIGITALIZACIÓN<img src="img/archivo.png" alt="Icono"></button>
        <button onclick="location.href='subirInfo2022.html'">SUBIR INFORMACIÓN<img src="img/subir.png" alt="Icono"></button> -->
        <button onclick="location.href='entregaRecepcion.php?anio=2022'">ENTREGA RECEPCIÓN<img src="img/entrega.png" alt="Icono"></button>
        <button onclick="location.href='subirInfo_ER.php?anio=2022'">CREAR FORMATO ER<img src="img/formato.png" alt="Icono"></button>
        <button onclick="location.href='docExistenten_ER.php?anio=2022'">SUBIR DOCUMENTOS EXISTENTES<img src="img/existencias.png" alt="Icono"></button>

    </div>
    
    <script src="js/inactividad.js"></script>
    
</body>
</html>
