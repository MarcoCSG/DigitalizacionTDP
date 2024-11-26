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
        <form method="POST" action="php/subirFormato5_11.php" onsubmit="convertirAMayusculas()">
            <!-- Campos ocultos para area y clasificacion -->
            <input type="hidden" name="area" value="<?php echo htmlspecialchars($area); ?>">
            <input type="hidden" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion); ?>">

            <div class="form-group">
                <label for="no">No.</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El número consecutivo de los documentos relacionados (1, 2, 3, etc.).</span>
                </div>
                <input type="number" id="no" name="no" placeholder="INGRESE EL NÚMERO" spellcheck="true"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="nombre_expediente">NOMBRE DEL EXPEDIENTE</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre con el que se identifican los documentos que integran el expediente.</span>
                </div>
                <textarea id="nombre_expediente" name="nombre_expediente" placeholder="Ingrese el nombre del expediente" spellcheck="true"></textarea> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="serie_documental">SERIE DOCUMENTAL</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La división que corresponde al conjunto de documentos producidos en el desarrollo de una misma atribución general.</span>
                </div>
                <input type="text" id="serie_documental" name="serie_documental" placeholder="Ingrese la serie documental" spellcheck="true"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="clave">CLAVE</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">La clave alfanumérica con la que se identifican los niveles de archivo al que pertenece el expediente.</span>
                </div>
                <input type="text" id="clave" name="clave" placeholder="Ingrese la clave" spellcheck="true"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="descripcion_contenido">DESCRIPCIÓN DEL CONTENIDO DE LA SERIE</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Una breve explicación del contenido de la serie, especificando la materia o asunto del que versa.</span>
                </div>
                <textarea id="descripcion_contenido" name="descripcion_contenido" placeholder="Ingrese la descripción del contenido" spellcheck="true"></textarea> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label>VALOR DE LA INFORMACIÓN</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">Marca una “X” si la información contiene documentos reservados o confidenciales, según corresponda.</span>
                </div>
                <div class="radio-group">
                    <label for="resguardado">
                        <input type="radio" id="resguardado" name="valor_informacion" value="resguardado" required> RESGUARDADO
                    </label>
                    <label for="confidencial">
                        <input type="radio" id="confidencial" name="valor_informacion" value="confidencial"> CONFIDENCIAL
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="vigencia_documental">VIGENCIA DOCUMENTAL</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El periodo durante el cual un documento deberá permanecer como archivo de trámite.</span>
                </div>
                <input type="text" id="vigencia_documental" name="vigencia_documental" placeholder="Ingrese la vigencia documental" spellcheck="true"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="area_responsable">ÁREA RESPONSABLE</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre del área que desarrolla o elabora el documento, la cual será responsable de su resguardo.</span>
                </div>
                <input type="text" id="area_responsable" name="area_responsable" placeholder="Ingrese el área responsable" spellcheck="true"> <!-- Agregado name -->
            </div>

            <div class="form-group">
                <label for="informacion_al">INFORMACIÓN AL</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El día, mes y año en que se actualizó la información de este formato Ejemplo: 15 de diciembre de 2021.</span>
                </div>
                <input type="date" id="informacion_al" name="informacion_al" placeholder="Ingrese su informacion" spellcheck="true" >
            </div>

            <div class="form-group">
                <label for="responsable">RESPONSABLE DE LA INFORMACIÓN</label>
                <div class="tooltip-container">
                    <button type="button" class="help-button">?</button>
                    <span class="tooltip">El nombre y cargo del servidor público responsable de integrar la información, y en su caso del resguardo de la documentación soporte.</span>
                </div>
                <input type="text" id="responsable" name="responsable" placeholder="Ingrese el responsable" spellcheck="true" > 
            </div>

            <div class="button-container">
                <button id="save-btn" type="submit" name="guardar">
                    <i class="fas fa-save"></i> GUARDAR
                </button>
            </div>
</body>

</html>