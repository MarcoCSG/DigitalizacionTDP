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
        input[type="text"],
        textarea {
            text-transform: none;
            /* Dejar el texto como lo escribe el usuario */
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
        <form method="POST" action="php/subirFormato3_2.php" onsubmit="convertirAMayusculas()">
            <!-- Campos ocultos para area y clasificacion -->
            <input type="hidden" name="area" value="<?php echo htmlspecialchars($area); ?>">
            <input type="hidden" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion); ?>">

            <div class="form-group">
                <label for="no">No.</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El número consecutivo de los documentos relacionados (1, 2, 3).</span>
                </div>
                <input type="number" id="no" name="no" placeholder="Ingrese un numero" spellcheck="true"> <!-- Agregado name -->
            </div>


            <div class="form-group">
                <label for="libro">LIBRO
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">La clave o número de identificación asignado al Libro de Actas de Cabildo. Ejemplo: 2018/II.</span>
                    </div>
                </label>
                <input type="text" id="libro" name="libro" placeholder="Ingrese la clave del libro" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="acta_del">ACTA DEL:
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">La fecha (día, mes y año) en la que se celebró la sesión de Cabildo consignada en la primer Acta que contiene el Libro.</span>
                    </div>
                </label>
                <input type="date" id="acta_del" name="acta_del" placeholder="Ingrese la fecha del acta" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="acta_al">ACTA AL:
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">La fecha (día, mes y año) en la que se celebró la sesión de Cabildo consignada en la última Acta que contiene el Libro.</span>
                    </div>
                </label>
                <input type="date" id="acta_al" name="acta_al" placeholder="Ingrese la fecha del acta" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="fojas">FOJAS
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">Los folios asignados a la primera y última foja del Libro. Ejemplo: 1-125.</span>
                    </div>
                </label>
                <input
                    type="text"
                    id="fojas"
                    name="fojas"
                    placeholder="Ejemplo: 1-125"
                    spellcheck="false"
                    pattern="^\d+-\d+$"
                    title="Solo se permite un rango de números separados por un guion, por ejemplo: 1-125"
                    oninput="validateFojas(this)">
            </div>

            <script>
                function validateFojas(input) {
                    // Permitir solo números y un guion
                    const pattern = /^\d*-\d*$/; // Permite dígitos opcionales seguidos por un guion y otros dígitos opcionales
                    if (!pattern.test(input.value)) {
                        input.value = input.value.replace(/[^0-9\-]/g, ''); // Remover caracteres no permitidos
                    }
                }
            </script>


            <div class="form-group">
                <label for="observaciones_3_2">OBSERVACIONES
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">La ubicación y los comentarios que se consideren importantes de los Libros que contienen las Actas de Cabildo de la
                            administración actual.</span>
                    </div>
                </label>
                <input type="text" id="observaciones_3_2" name="observaciones_3_2" placeholder="Ingrese observaciones" spellcheck="true">
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
                <input type="text" id="responsable" name="responsable" placeholder="Ingrese responsable" spellcheck="true">
            </div>

            <div class="button-container">
                <button id="save-btn" type="submit" name="guardar">
                    <i class="fas fa-save"></i> GUARDAR
                </button>
            </div>
</body>

</html>