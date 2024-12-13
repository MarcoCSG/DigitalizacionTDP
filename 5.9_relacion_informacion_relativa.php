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

        <!-- Llama a la función convertirAMayusculas al enviar el formulario -->
        <form method="POST" action="php/subirFormato5_9.php" onsubmit="convertirAMayusculas()">
            <input type="hidden" name="area" value="<?php echo htmlspecialchars($area); ?>">
            <input type="hidden" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion); ?>">

            <div class="form-group">
                <label for="no">No
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">Ingrese un número consecutivo del 1 al 11</span>
                    </div>
                </label>
                <input type="text" id="no" name="no" placeholder="Ingrese el no" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="informacion">INFORMACIÓN
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">Seleccione una opcion consecutiva para la información</span>
                    </div>
                </label>
                <select id="informacion" name="informacion" class="form-group">
                    <option value="" disabled selected>Seleccione una opción</option>
                    <option value="PADRÓN CATASTRAL (BASE DE DATOS)">1.- PADRÓN CATASTRAL (BASE DE DATOS)</option>
                    <option value="SISTEMA DE GESTIÓN CATASTRAL">2.- SISTEMA DE GESTIÓN CATASTRAL</option>
                    <option value="CARPETAS MANZANERAS DE LOS EXPEDIENTES DE REGISTROS CATASTRALES INCORPORADOS">3.- CARPETAS MANZANERAS DE LOS EXPEDIENTES DE REGISTROS CATASTRALES INCORPORADOS</option>
                    <option value="ORTOFOTOS">4.- ORTOFOTOS</option>
                    <option value="FOTOS AÉREAS">5.- FOTOS AÉREAS</option>
                    <option value="RESTITUCIÓN FOTOGRAMÉTRICA">6.- RESTITUCIÓN FOTOGRAMÉTRICA</option>
                    <option value="CARTOGRAFÍA CATASTRAL DIGITAL (ARCHIVOS AUTOCAD)">7.- CARTOGRAFÍA CATASTRAL DIGITAL (ARCHIVOS AUTOCAD)</option>
                    <option value="PLANOS GENERALES, REGIONALES, COLONIAS, INEGI, PROCEDE, DE LAS LOCALIDADES DEL MUNICIPIO (PAPEL)">8.- PLANOS GENERALES, REGIONALES, COLONIAS, INEGI, PROCEDE, DE LAS LOCALIDADES DEL MUNICIPIO (PAPEL)</option>
                    <option value="CARTOGRAFÍA CATASTRAL DE CONSERVACIÓN (PAPEL)">9.- CARTOGRAFÍA CATASTRAL DE CONSERVACIÓN (PAPEL)</option>
                    <option value="ARCHIVO GENERAL DE CATASTRO (OFICIOS, INFORMES)">10.- ARCHIVO GENERAL DE CATASTRO (OFICIOS, INFORMES)</option>
                    <option value="OTROS (ESPECIFICAR)">11.- OTROS (ESPECIFICAR)</option>

                </select>
            </div>

            <div class="form-group">
                <label for="medio">MEDIO
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">El tipo de soporte en que se encuentra la información. Ejemplo: sistema, archivo electrónico, legajo, plano</span>
                    </div>
                </label>
                <input type="text" id="medio" name="medio" placeholder="Ingrese el medio" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="cantidad">CANTIDAD
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">El número total de medios en que se encuentra la información relativa a catastro.</span>
                    </div>
                </label>
                <input type="number" id="cantidad" name="cantidad" placeholder="Ingrese la cantidad" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="observaciones">OBSERVACIONES
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">Los comentarios que se consideren importantes respecto a la información catastral.</span>
                    </div>
                </label>
                <input type="text" id="observaciones" name="observaciones" placeholder="Ingrese observaciones" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="informacion_al">INFORMACIÓN AL
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">El día, mes y año en que se actualizó la información de este formato.</span>
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
                <input type="text" id="responsable" name="responsable" placeholder="Ingrese el nombre del responsable" spellcheck="true">
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