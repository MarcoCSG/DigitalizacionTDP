<?php
session_start();
// Verificar si el usuario ha iniciado sesión y tiene un municipio asignado
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    // Redirigir al inicio de sesión si no está logueado
    header("Location: index.html");
    exit();
}

$municipio = $_SESSION["municipio"]; // Obtener el municipio del usuario logado

// Obtener parámetros de la URL
$area = isset($_GET['area']) ? $_GET['area'] : '';
$clasificacion = isset($_GET['clasificacion']) ? $_GET['clasificacion'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formato Entrega Recepción</title>
    <link rel="stylesheet" href="css/formatos.css">
    <link rel="icon" href="img/TDP-REDONDO.png" type="image/x-icon">
    <script src="js/mapeoER.js" defer></script>
</head>
<body>
    <div class="logo">
        <img src="img/logoTDP.png" alt="TDP Logo">
    </div>

    <div class="container">
        <h1>FORMATO ENTREGA RECEPCIÓN</h1>
        <h2>SELECCIONE LAS OPCIONES PARA FORMATO</h2>
        
        <h3 id="clasificacionSeleccionada">Área: <?php echo htmlspecialchars($area); ?> | Clasificación: <?php echo htmlspecialchars($clasificacion); ?></h3>

        <!-- Formulario con método POST y action hacia subirFormato.php -->
        <form method="POST" action="php/subirFormato.php">
            <!-- Se eliminan los campos ocultos de area y clasificacion -->

            <div class="form-group">
                <label for="no">No.</label>
                <input type="text" id="no" name="no" placeholder="INGRESE EL NUMERO" required>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El número consecutivo de los documentos relacionados (1, 2, 3, etc.).</span>
                </div>
            </div>

            <div class="form-group">
                <label for="denominacion">DENOMINACIÓN</label>
                <textarea id="denominacion" name="denominacion" placeholder="Ingrese información" required></textarea>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre y tipo del documento de que se trate. Ejemplo: Manual General de Organización, Manual de Procedimientos de la Tesorería o Manual de Servicios.</span>
                </div>
            </div>

            <div class="form-group">
                <label for="publicacion_fecha">PUBLICACIÓN Y FECHA</label>
                <input type="text" id="publicacion_fecha" name="publicacion_fecha" placeholder="Ingrese información" required>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El sitio de la publicación de los manuales y la fecha de la misma.</span>
                </div>
            </div>

            <div class="form-group">
                <label for="informacion_al">INFORMACIÓN AL:</label>
                <textarea id="informacion_al" name="informacion_al" placeholder="Ingrese información" required></textarea>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El día, mes y año en que se actualizó la información de este formato. Ejemplo: 15 de diciembre de 2021.</span>
                </div>
            </div>

            <div class="form-group">
                <label for="fecha_autorizacion">FECHA</label>
                <input type="date" id="fecha_autorizacion" name="fecha_autorizacion" required>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El día, mes y año en que se autorizó el manual administrativo referido.</span>
                </div>
            </div>

            <div class="form-group">
                <label for="responsable">RESPONSABLE DE LA INFORMACIÓN</label>
                <textarea id="responsable" name="responsable" placeholder="Ingrese información" required></textarea>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre y cargo del servidor público responsable de integrar la información, y en su caso del resguardo de la documentación soporte.</span>
                </div>
            </div>

            <div class="form-group">
                <label for="observaciones">OBSERVACIONES</label>
                <textarea id="observaciones" name="observaciones" placeholder="Ingrese información"></textarea>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Los comentarios que se consideren importantes respecto a los manuales administrativos.</span>
                </div>
            </div>

            <div class="button-container">
                <button type="submit" name="guardar">GUARDAR</button>
            </div>
        </form>

    </div>
</body>
</html>
