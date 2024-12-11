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
        <form method="POST" action="php/subirFormato3_6.php" onsubmit="convertirAMayusculas()">
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
                <label for="tipo">TIPO
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">La materia a la que pertenece el juicio interpuesto por el Ayuntamiento. Ejemplo: civil, laboral, mercantil</span>
                    </div>
                </label>
                <input type="text" id="tipo" name="tipo" placeholder="Ingrese el tipo de materia" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="fecha">FECHA
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">El día, mes y año en que inició el juicio.</span>
                    </div>
                </label>
                <input type="date" id="fecha" name="fecha" placeholder="Ingrese la fecha del acta" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="promovido_ante">PROMOVIDO ANTE
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">La instancia encargada del análisis y resolución del proceso correspondiente.</span>
                    </div>
                </label>
                <input type="text" id="promovido_ante" name="promovido_ante" placeholder="Ingrese la instancia encargada del análisis" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="en_contra_de">EN CONTRA DE
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">El nombre de la persona, razón social o representante legal contra quien interpuso el juicio el Ayuntamiento.</span>
                    </div>
                </label>
                <input type="text" id="en_contra_de" name="en_contra_de" placeholder="Ingrese en contra de" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="descripcion">DESCRIPCIÓN
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">El motivo por el cual se instauró el juicio por el Ayuntamiento.</span>
                    </div>
                </label>
                <textarea id="descripcion" name="descripcion" placeholder="Ingrese descripcion" spellcheck="true"></textarea>
            </div>

            <div class="form-group">
                <label for="estado_juridico">ESTADO JURIDICO
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">La etapa procesal en la que se encuentra el juicio. </span>
                    </div>
                </label>
                <input type="text" id="estado_juridico" name="estado_juridico" placeholder="Ingrese el estado juridico" spellcheck="true">
            </div>

            <div class="form-group">
                <label for="acciones_inmediatas">ACCIONES INMEDIATAS
                    <div class="tooltip-container">
                        <button type="button" class="help-button">?</button>
                        <span class="tooltip">Las acciones que deben realizarse oportunamente en el marco del proceso jurídico.</span>
                    </div>
                </label>
                <input type="text" id="acciones_inmediatas" name="acciones_inmediatas" placeholder="Ingrese acciones inmediatas" spellcheck="true">
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
                        <span class="tooltip">El nombre y cargo del servidor público responsable de integrar la información, y en su caso del resguardo de la documentación
                        soporte.</span>
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