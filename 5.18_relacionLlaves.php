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
        <form method="POST" action="php/subirFormato5_18.php">
            <!-- Campos ocultos para area y clasificacion -->
            <input type="hidden" name="area" value="<?php echo htmlspecialchars($area); ?>">
            <input type="hidden" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion); ?>">

            <div class="form-group">
                <label for="no">No.</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El número consecutivo de los documentos relacionados (1, 2, 3, etc.).</span>
                </div>
                <input type="text" id="no" name="no" placeholder="INGRESE EL NÚMERO"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="clave">CLAVE</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La identificación numérica o alfanumérica asignada a las llaves que se entregan.</span>
                </div>
                <input type="text" id="clave" name="clave" placeholder="Ingrese la clave"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="lugar_movilidad_equipo">LUGAR, MOVILIARIO O EQUIPO</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La denominación del espacio físico o del bien que permite abrir la llave. Ejemplo: Presidencia Municipal, sala de juntas, etc</span>
                </div>
                <input type="text" id="lugar_movilidad_equipo" name="lugar_movilidad_equipo" placeholder="Ingrese la serie documental"> <!-- Agregado name -->
            </div>


            <div class="form-group">
                <label for="cantidad">CANTIDAD</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El número de llaves que se están entregando.</span>
                </div>
                <textarea id="cantidad" name="cantidad" placeholder="Ingrese la cantidad"></textarea> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="en_poder">EN PODER DE:</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre y cargo de la persona que tiene en su poder un duplicado de la llave.</span>
                </div>
                <input type="text" id="en_poder" name="en_poder" placeholder="Ingrese nombre de la persona"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="cantidad_copias">CANTIDAD(COPIAS)</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El número de duplicados que tenga en su poder la persona referida.</span>
                </div>
                <input type="text" id="cantidad_copias" name="cantidad_copias" placeholder="Ingrese la cantidad de copias"> <!-- Agregado name -->
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
                    <span class="tooltip">El nombre y cargo del servidor público responsable de integrar la información, y en su caso del resguardo de la documentación soporte.</span>
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