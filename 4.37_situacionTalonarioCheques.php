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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formato Entrega Recepción</title>
    <link rel="stylesheet" href="css/formatos.css">
    <link rel="icon" href="img/TDP-REDONDO.png" type="image/x-icon">
    <script src="js/mapeoER.js" defer></script> <!-- Cambia aquí -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        <form method="POST" action="php/subirFormato4_37.php">
            <!-- Campos ocultos para area y clasificacion -->
            <input type="hidden" name="area" value="<?php echo htmlspecialchars($area); ?>">
            <input type="hidden" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion); ?>">

            <div class="form-group">
                <label for="banco">BANCO</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre de la Institución Bancaria que administra la cuenta.</span>
                </div>
                <input type="text" id="banco" name="banco" placeholder="Ingrese la actividad"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="no_cuenta">NUMERO DE CUENTA.</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Los dígitos asignados por la Institución Bancaria a cada cuenta de cheques.</span>
                </div>
                <input type="text" id="no_cuenta" name="no_cuenta" placeholder="INGRESE EL NÚMERO"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="total">TOTAL</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Los números del primer y último cheque, correspondiente a la cuenta bancaria de que se trate.</span>
                </div>
                <input type="text" id="total" name="total" placeholder="INGRESE EL NÚMERO"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="utilizados">UTILIZADOS</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Los números del primer y último cheque expedido, correspondiente a la cuenta bancaria de que se trate.</span>
                </div>
                <input type="text" id="utilizados" name="utilizados" placeholder="INGRESE EL NÚMERO"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="por_utilizar">POR UTILIZAR</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Los números del primer y último cheque que se encuentran sin expedir, correspondiente a la cuenta bancaria de que se trate.</span>
                </div>
                <input type="text" id="por_utilizar" name="por_utilizar" placeholder="INGRESE EL NÚMERO"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="cancelados">CANCELADOS</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Los números de los cheques que fueron cancelados, según la cuenta bancaria de que se trate.</span>
                </div>
                <input type="text" id="cancelados" name="cancelados" placeholder="INGRESE EL NÚMERO"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="informacion_al">INFORMACIÓN AL</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El día, mes y año en que se actualizó la información de este formato Ejemplo: 15 de diciembre de 2021.</span>
                </div>
                <textarea id="informacion_al" name="informacion_al" placeholder="Ingrese información" required></textarea>
            </div>

            <div class="form-group">
                <label for="responsable">RESPONSABLE DE LA INFORMACIÓN</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre y cargo del servidor público responsable de integrar la información, y en su caso del resguardo de la documentación</span>
                </div>
                <textarea id="responsable" name="responsable" placeholder="Ingrese información" required></textarea>
            </div>

            <div class="button-container">
                <button id="save-btn" type="submit" name="guardar">
                    <i class="fas fa-save"></i> GUARDAR
                </button>
            </div>
</body>

</html>