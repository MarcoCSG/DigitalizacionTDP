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
    <title>DEPARTAMENTOS</title>
    <link rel="stylesheet" href="css/departamentos2.css">
    <link rel="icon" href="img/TDP-REDONDO.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
</head>
<body>
    <!-- CONTENIDO -->
    <main class="main-content">
        <section>
            <figure>
                <img class="imgTDP" src="img/logoTDP.png" alt="logo">
            </figure>
            <h1 class="titulo">FORMATOS ENTREGA RECEPCION <?php echo htmlspecialchars($municipio); ?></h1>
            <h2 class="subtitulo">SELECCIONE LAS OPCIONES PARA CREAR SU FORMATO</h2>

            <section class="search-options">
                <div class="form-group">
                    <label for="area">ÁREA</label>
                    <select id="area">
                        <option value="">Seleccione una opción</option>
                        <option value="presidencia_er">PRESIDENCIA</option>
                        <option value="tesoreria_er">TESORERIA</option>
                        <option value="catastro_er">CATASTRO</option>
                        <option value="secretaria_er">SECRETARIA</option>
                        <option value="contraloria_er">CONTRALORIA MUNICIPAL</option>
                        <option value="obraspublicas_er">OBRAS PUBLICAS E INFRAESTRUCTURA</option>   
                        <option value="regidores_er">REGIDORES</option>
                        <option value="areas_er">TODAS LAS AREAS</option>
                        <option value="areasUsuarias_er">AREAS USUARIAS</option>
                        <option value="Utransparencia_er">UNIDAD DE TRANSPARENCIA</option>
                    </select>
                </div>

                <!-- CLASIFICACION DOCUMENTAL -->
                <div class="form-group" id="clasificacionContainer" style="display:none;">
                    <label for="clasificacion">CLASIFICACIONES ENTREGA RECEPCION</label>
                    <select id="clasificacion">
                        <option value="">Seleccione una clasificación</option>
                        <!-- Opciones dinámicas -->
                    </select>
                </div>
            </section>
        </section>

        <div class="search-container">
            <button id="consultaBtn" class="search-button">
                <ion-icon name="search-outline"></ion-icon> CREAR FORMATO
            </button>
        </div>
        
        <div id="resultado">
            <!-- Aquí se mostrarán los resultados de la consulta -->
        </div>
    </main>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="js/ER.JS"></script>
    <script src="js/btn_buscarER.js"></script>
    <script src="js/inactividad.js"></script>
</body>
</html>
