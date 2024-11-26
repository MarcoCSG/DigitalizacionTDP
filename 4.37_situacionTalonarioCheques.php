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
<html lang="es"> <!-- Mantiene el idioma español para la corrección ortográfica -->
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

        <!-- Llama a la función convertirAMayusculas al enviar el formulario -->
        <form method="POST" action="php/subirFormato4_37.php" onsubmit="convertirAMayusculas()">
            <input type="hidden" name="area" value="<?php echo htmlspecialchars($area); ?>">
            <input type="hidden" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion); ?>">

            <div class="form-group">
                <label for="banco">BANCO
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre de la Institución Bancaria que administra la cuenta.</span>
                </div>
                </label>
                <input type="text" id="banco" name="banco" placeholder="Ingrese el banco" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="no_cuenta">NÚMERO DE CUENTA
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Los dígitos asignados por la Institución Bancaria a cada cuenta de cheques.</span>
                </div>
                </label>
                <input type="text" id="no_cuenta" name="no_cuenta" placeholder="Ingrese el número de cuenta" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="total">TOTAL
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Los números del primer y último cheque, correspondiente a la cuenta bancaria de que se trate.</span>
                </div>
                </label>
                <input type="number" id="total" name="total" placeholder="Ingrese el total" spellcheck="false"> 
            </div>

            <div class="form-group">
                <label for="utilizados">UTILIZADOS
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Los números del primer y último cheque expedido, correspondiente a la cuenta bancaria de que se trate.</span>
                </div>
                </label>
                <input type="number" id="utilizados" name="utilizados" placeholder="Ingrese el número" spellcheck="false"> 
            </div>

            <div class="form-group">
                <label for="por_utilizar">POR UTILIZAR
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Los números del primer y último cheque que se encuentran sin expedir, correspondiente a la cuenta bancaria de que se trate.</span>
                </div>
                </label>
                <input type="number" id="por_utilizar" name="por_utilizar" placeholder="Ingrese el número" spellcheck="false"> 
            </div>

            <div class="form-group">
                <label for="cancelados">CANCELADOS
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Los números de los cheques que fueron cancelados, según la cuenta bancaria de que se trate.</span>
                </div>
                </label>
                <input type="number" id="cancelados" name="cancelados" placeholder="Ingrese el número" spellcheck="false"> 
            </div>

            <div class="form-group">
                <label for="informacion_al">INFORMACIÓN AL
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El día, mes y año en que se actualizó la información de este formato Ejemplo: 15 de diciembre de 2021.</span>
                </div>
                </label>
                <input type="date" id="informacion_al" name="informacion_al" placeholder="Ingrese la información" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="responsable">RESPONSABLE DE LA INFORMACIÓN
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre y cargo del servidor público responsable de integrar la información, y en su caso del resguardo de la documentación
                    soporte.</span>
                </div>
                </label>
                <input type="text" id="responsable" name="responsable" placeholder="Ingrese el nombre del responsable" spellcheck="true" >
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
