<?php
session_start();
// Verificar si el usuario ha iniciado sesión y tiene un municipio asignado
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    // Redirigir al inicio de sesión si no está logueado
    header("Location: index.html");
    exit();
}

$municipio = $_SESSION["municipio"]; // Obtener el municipio del usuario logueado

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
    <script src="js/mapeoER.js" defer></script> <!-- Cambia aquí -->
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
        <form method="POST" action="php/subirFormato1_3.php" onsubmit="convertirAMayusculas()">
            <!-- Campos ocultos para area y clasificacion -->
            <input type="hidden" name="area" value="<?php echo htmlspecialchars($area); ?>">
            <input type="hidden" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion); ?>">

            <div class="form-group">
                <label for="no">No.</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El número consecutivo de los documentos relacionados (1, 2, 3).</span>
                </div>
                <input type="number" id="no" name="no" placeholder="Ingrese un numero" spellcheck="true" > <!-- Agregado name -->
            </div>


            <div class="form-group">
                <label for="entidad">ENTIDAD PARAMUNICIPAL
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre del organismo descentralizado, empresa de participación municipal o fideicomiso público que se relaciona.</span>
                </div>
                </label>
                <input type="text" id="entidad" name="entidad" placeholder="Ingrese la entidad municipal" spellcheck="true" > 
            </div>


            <div class="form-group">
                <label for="no_acta">No. DE ACTA
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La referencia numérica del acta de la sesión de cabildo en la que se autoriza la creación de la Entidad Paramunicipal, así como el
                    día, mes y año de la misma.</span>
                </div>
                </label>
                <input type="text" id="no_acta" name="no_acta" placeholder="Ingrese el numero de acta" spellcheck="true" > 
            </div>

            <div class="form-group">
                <label for="fecha_aprobacion">FECHA DE APROBACIÓN
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La referencia numérica del acta de la sesión de cabildo en la que se autoriza la creación de la Entidad Paramunicipal, así como el
                    día, mes y año de la misma.</span>
                </div>
                </label>
                <input type="date" id="fecha_aprobacion" name="fecha_aprobacion" placeholder="Ingrese la fecha de aprobación" spellcheck="true" > 
            </div>

            <div class="form-group">
                <label for="no_gaceta">No. DE GACETA
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La referencia numérica de la Gaceta Oficial del Estado de Veracruz con la que fue publicado el Decreto de creación de la Entidad
                    Paramunicipal, así como el día, mes y año del mismo.</span>
                </div>
                </label>
                <input type="text" id="no_gaceta" name="no_gaceta" placeholder="Ingrese el numero de gaceta" spellcheck="true" > 
            </div>

            <div class="form-group">
                <label for="fecha_publicacion">FECHA DE PUBLICACIÓN
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La referencia numérica de la Gaceta Oficial del Estado de Veracruz con la que fue publicado el Decreto de creación de la Entidad
                    Paramunicipal, así como el día, mes y año del mismo.</span>
                </div>
                </label>
                <input type="date" id="fecha_publicacion" name="fecha_publicacion" placeholder="Ingrese la fecha de publicación" spellcheck="true" > 
            </div>
            
            <div class="form-group">
                <label for="nombre">NOMBRE
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre de la persona designada como Titular de la Entidad Paramunicipal.</span>
                </div>
                </label>
                <input type="text" id="nombre" name="nombre" placeholder="Ingrese un nombre" spellcheck="true" > 
            </div>

            <div class="form-group">
                <label for="cargo">CARGO
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La denominación del puesto que ocupa el Titular de la Entidad Paramunicipal. Ejemplo: Director General.</span>
                </div>
                </label>
                <input type="text" id="cargo" name="cargo" placeholder="Ingrese el cargo" spellcheck="true" > 
            </div>

            <div class="form-group">
                <label for="informacion_al">INFORMACIÓN AL
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El día, mes y año en que se actualizó la información de este formato.</span>
                </div>
                </label>
                <input type="date" id="informacion_al" name="informacion_al" placeholder="Ingrese información" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="responsable">RESPONSABLE DE LA INFORMACIÓN
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre y cargo del servidor público responsable de integrar la información, y en su caso del resguardo de la documentación soporte.</span>
                </div>
                </label>
                <input type="text" id="responsable" name="responsable" placeholder="Ingrese responsable" spellcheck="true" >
            </div>

            <div class="button-container">
                <button id="save-btn" type="submit" name="guardar">
                    <i class="fas fa-save"></i> GUARDAR
                </button>
            </div>
</body>

</html>