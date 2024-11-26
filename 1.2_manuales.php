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

// Para depuración: Mostrar los valores obtenidos
// echo "Área: " . htmlspecialchars($area) . "<br>";
// echo "Clasificación: " . htmlspecialchars($clasificacion) . "<br>";

// Validar que area y clasificacion no estén vacíos
if (empty($area) || empty($clasificacion)) {
    echo "<script>alert('Área o clasificación no especificadas.'); window.location.href = 'pagina_principal.php';</script>";
    exit();
}
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Muestra el texto tal como lo escribe el usuario */
        input[type="text"], textarea {
            text-transform: none; /* Dejar el texto como lo escribe el usuario */
        }
    </style>

    <script>
        // Convertir todos los campos de texto a mayúsculas al enviar el formulario
        function convertirAMayusculas() {
            const inputs = document.querySelectorAll('input[type="text"], textarea');
            inputs.forEach(input => {
                input.value = input.value.toUpperCase(); // Convierte a mayúsculas antes de enviar
            });
        }
    </script>

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
        <form method="POST" action="php/subirFormato.php" onsubmit="convertirAMayusculas()">
            <!-- Campos ocultos para area y clasificacion -->
            <input type="hidden" name="area" value="<?php echo htmlspecialchars($area); ?>">
            <input type="hidden" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion); ?>">

            <div class="form-group">
                <label for="no">No.
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El número consecutivo de los documentos relacionados (1, 2, 3, etc.).</span>
                </div>
                </label>
                <input type="number" id="no" name="no" placeholder="INGRESE EL NUMERO" spellcheck="true" required>
            </div>

            <div class="form-group">
                <label for="denominacion">DENOMINACIÓN
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre y tipo del documento de que se trate. Ejemplo: Manual General de Organización, Manual de Procedimientos de la Tesorería o Manual de Servicios.</span>
                </div>
                </label>
                <textarea id="denominacion" name="denominacion" placeholder="Ingrese información" spellcheck="true" required></textarea>
            </div>

            <div class="form-group">
                <label for="fecha_autorizacion">FECHA
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El día, mes y año en que se autorizó el manual administrativo referido.</span>
                </div>
                </label>
                <input type="date" id="fecha_autorizacion" name="fecha_autorizacion" spellcheck="true" required>
            </div>

            <div class="form-group">
                <label for="observaciones">OBSERVACIONES
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Los comentarios que se consideren importantes respecto a los manuales administrativos.</span>
                </div>
                </label>
                <textarea id="observaciones" name="observaciones" placeholder="Ingrese información" spellcheck="true"></textarea>
            </div>

            <div class="form-group">
                <label for="publicacion_fecha">PUBLICACIÓN Y FECHA
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El sitio de la publicación de los manuales y la fecha de la misma.</span>
                </div>
                </label>
                <input type="date" id="publicacion_fecha" name="publicacion_fecha" placeholder="Ingrese información" spellcheck="true" required>
            </div>

            <div class="form-group">
                <label for="informacion_al">INFORMACIÓN AL:
                    <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El día, mes y año en que se actualizó la información de este formato. Ejemplo: 15 de diciembre de 2021.</span>
                </div>
                </label>
                <input type="date" id="informacion_al" name="informacion_al" placeholder="Ingrese informacion al" spellcheck="true" required>
            </div>

            <div class="form-group">
                <label for="responsable">RESPONSABLE DE LA INFORMACIÓN
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre y cargo del servidor público responsable de integrar la información, y en su caso del resguardo de la documentación soporte.</span>
                </div>
                </label>
                <input type="text" id="responsable" name="responsable" placeholder="Ingrese el responsable" spellcheck="true" required>
            </div>

            <div class="button-container">
                <button id="save-btn" type="submit" name="guardar">
                    <i class="fas fa-save"></i> GUARDAR
                </button>
            </div>
        </form>

    </div>
</body>
</html>
