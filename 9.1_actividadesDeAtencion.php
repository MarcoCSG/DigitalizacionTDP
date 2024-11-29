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
        <form method="POST" action="php/subirFormato9_1.php" onsubmit="convertirAMayusculas()">
            <!-- Campos ocultos para area y clasificacion -->
            <input type="hidden" name="area" value="<?php echo htmlspecialchars($area); ?>">
            <input type="hidden" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion); ?>">

            <div class="form-group">
                <label for="no">No.</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El número consecutivo de los documentos relacionados (1, 2, 3).</span>
                </div>
                <input type="number" id="no" name="no" placeholder="Ingrese el número" spellcheck="true"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="actividad">ACTIVIDAD</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La descripción de las actividades a desarrollar o atender, durante los 90 días posteriores a la entrega y recepción municipal.</span>
                </div>
                <input type="text" id="actividad" name="actividad" placeholder="Ingrese la actividad" spellcheck="true"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="fecha">FECHA</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">día, mes y año límite para llevarse a cabo o atender cada uno de los asuntos.</span>
                </div>
                <input type="date" id="fecha" name="fecha" placeholder="Ingrese la serie documental" spellcheck="true"> <!-- Agregado name -->
            </div>


            <div class="form-group">
                <label for="observaciones">OBSERVACIONES</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Comentarios que se consideren importantes respecto a las actividades relacionadas.</span>
                </div>
                <textarea id="observaciones" name="observaciones" placeholder="Ingrese la cantidad" spellcheck="true"></textarea> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="area_responsable">ÁREA RESPONSABLE</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre del área administrativa responsable de llevar a cabo o atender cada una de las actividades relacionadas.</span>
                </div>
                <input type="text" id="area_responsable" name="area_responsable" placeholder="Ingrese nombre de la persona" spellcheck="true"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="informacion_al">INFORMACIÓN AL</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El día, mes y año en que se actualizó la información de este formato.</span>
                </div>
                <input type="date" name="informacion_al" id="informacion_al" value="<?php echo htmlspecialchars($registro['informacion_al']); ?>" required>
            </div>

            <div class="form-group">
                <label for="responsable">RESPONSABLE DE LA INFORMACIÓN</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre y cargo del servidor público responsable de integrar la información, y en su caso del resguardo de la documentación</span>
                </div>
                <input type="text" id="responsable" name="responsable" placeholder="Ingrese información" spellcheck="true">
            </div>

            <div class="button-container">
                <button id="save-btn" type="submit" name="guardar">
                    <i class="fas fa-save"></i> GUARDAR
                </button>
            </div>
</body>

</html>