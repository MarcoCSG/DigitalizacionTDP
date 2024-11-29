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
        <form method="POST" action="php/subirFormato5_8.php" onsubmit="convertirAMayusculas()">
            <!-- Campos ocultos para area y clasificacion -->
            <input type="hidden" name="area" value="<?php echo htmlspecialchars($area); ?>">
            <input type="hidden" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion); ?>">

            <div class="form-group">
                <label for="no">No.</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El número consecutivo de las reservas territoriales relacionadas (1, 2, 3).</span>
                </div>
                <input type="number" id="no" name="no" placeholder="Ingrese un número" spellcheck="true" > <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="ubicacion">UBICACIÓN</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre de la calle, número, colonia, localidad y código postal donde se ubica.</span>
                </div>
                <input type="text" id="ubicacion" name="ubicacion" placeholder="Ingrese la ubicación" spellcheck="true" > <!-- Agregado name -->
            </div>


            <div class="form-group">
                <label for="colindancias">COLINDANCIAS</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La descripción de las características de colindancia al norte, sur, este y oeste de la reserva territorial.</span>
                </div>
                <textarea id="colindancias" name="colindancias" placeholder="Ingrese colindancias" spellcheck="true" required></textarea>
            </div>

            <div class="form-group">
                <label for="superficie_total">SUPERFICIE TOTAL</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Los metros cuadrados que conforman la reserva territorial.</span>
                </div>
                <input type="text" id="superficie_total" name="superficie_total" placeholder="Ingrese la superficie total" spellcheck="true" > <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="documento_aval">DOCUMENTO QUE AVALE LA PROPIEDAD</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El tipo y número de documento oficial que avala la propiedad.</span>
                </div>
                <input type="text" id="documento_aval" name="documento_aval" placeholder="Ingrese sus documentos que avalen la propiedad" spellcheck="true" > <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="valor">VALOR</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La cantidad monetaria que corresponde a cada reserva territorial, ya sea de acuerdo al valor catastral o de avalúo (cifras en pesos),
                    según corresponda.</span>
                </div>
                <input type="text" id="valor" name="valor" class="moneda" placeholder="Ingrese su valor" spellcheck="true" > <!-- Agregado name -->
            </div>
            <script>
                document.querySelectorAll('.moneda').forEach((input) => {
                    // Función para formatear el valor como moneda
                    function formatCurrency(value) {
                        const numberValue = parseFloat(value.replace(/[^0-9.-]+/g, ''));
                        if (isNaN(numberValue)) return ''; // Si no es un número, retornar vacío
                        return '$' + numberValue.toFixed(2); // Formatear como moneda
                    }

                    // Evento al escribir en el input
                    input.addEventListener('input', (e) => {
                        const cursorPosition = e.target.selectionStart; // Guardar posición del cursor
                        const formattedValue = formatCurrency(e.target.value); // Formatear el valor
                        e.target.value = formattedValue; // Asignar el valor formateado
                        e.target.setSelectionRange(cursorPosition, cursorPosition); // Restaurar posición del cursor
                    });

                    // Formatear valor inicial si existe
                    if (input.value) {
                        input.value = formatCurrency(input.value);
                    }
                });
            </script>

            <div class="form-group">
                <label for="uso_actual">USO ACTUAL</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La descripción del uso que se le da a la reserva territorial. </span>
                </div>
                <input type="text" id="uso_actual" name="uso_actual" placeholder="Ingrese su uso actual" spellcheck="true" > <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="observaciones">OBSERVACIONES</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Los comentarios que se consideren importantes respecto a la información catastral.</span>
                </div>
                <input type="text" id="observaciones" name="observaciones" placeholder="Ingrese sus observaciones" spellcheck="true" > <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="informacion_al">INFORMACIÓN AL</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El día, mes y año en que se actualizó la información de este formato.</span>
                </div>
                <input type="date" id="informacion_al" name="informacion_al" placeholder="Ingrese su información" spellcheck="true" > <!-- Agregado name -->
                </div>

            <div class="form-group">
                <label for="responsable">RESPONSABLE DE LA INFORMACIÓN</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre y cargo del servidor público responsable de integrar la información, y en su caso del resguardo de la documentación soporte.</span>
                </div>
                <input type="text" id="responsable" name="responsable" placeholder="Ingrese el responsable" spellcheck="true" > <!-- Agregado name -->
            </div>

            <div class="button-container">
                <button id="save-btn" type="submit" name="guardar">
                    <i class="fas fa-save"></i> GUARDAR
                </button>
            </div>
            </div>
</body>

</html>