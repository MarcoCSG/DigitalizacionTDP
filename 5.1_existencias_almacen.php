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
        <form method="POST" action="php/subirFormato5_1.php" onsubmit="convertirAMayusculas()">
            <!-- Campos ocultos para area y clasificacion -->
            <input type="hidden" name="area" value="<?php echo htmlspecialchars($area); ?>">
            <input type="hidden" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion); ?>">

            <div class="form-group">
                <label for="no">No.</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El número consecutivo de los artículos relacionados.</span>
                </div>
                <input type="number" id="no" name="no" placeholder="Ingrese un numero" spellcheck="true" > <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="articulo">ARTÍCULO</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La denominación del material en existencia, propiedad del Ayuntamiento.</span>
                </div>
                <input type="text" id="articulo" name="articulo" placeholder="Ingrese el artículo" spellcheck="true" > <!-- Agregado name -->
            </div>


            <div class="form-group">
                <label for="unidad_medida">UNIDAD DE MEDIDA</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La expresión o concepto con la que se contabilizan los materiales.</span>
                </div>
                <input type="text" id="unidad_medida" name="unidad_medida" placeholder="Ingrese una unidad de medida" spellcheck="true" > <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="existencia">EXISTENCIA</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La cantidad existente de cada uno de los materiales.</span>
                </div>
                <input type="number" id="existencia" name="existencia" placeholder="Ingrese la cantidad de existencias" spellcheck="true" > <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="costo_unitario">COSTO UNITARIO</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El valor establecido en la factura para los materiales por unidad de medida.</span>
                </div>
                <input type="text" id="costo_unitario" name="costo_unitario" class="moneda" placeholder="Ingrese el costo unitario" spellcheck="true" > <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="importe">IMPORTE</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El valor establecido en la factura para los materiales por unidad de medida.</span>
                </div>
                <input type="text" id="importe" name="importe" class="moneda" placeholder="Ingrese el importe" spellcheck="true" > <!-- Agregado name -->
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
                <label for="informacion_al">INFORMACIÓN AL</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El día, mes y año en que se actualizó la información de este formato.</span>
                </div>
                <input type="date" id="informacion_al" name="informacion_al" placeholder="Ingrese información" spellcheck="true" > 
            </div>

            <div class="form-group">
                <label for="responsable">RESPONSABLE DE LA INFORMACIÓN</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre y cargo del servidor público responsable de integrar la información, y en su caso del resguardo de la documentación soporte.</span>
                </div>
                <input type="text" id="responsable" name="responsable" placeholder="Ingrese al responsable" spellcheck="true" > 
            </div>

            <div class="button-container">
                <button id="save-btn" type="submit" name="guardar">
                    <i class="fas fa-save"></i> GUARDAR
                </button>
            </div>
</body>

</html>