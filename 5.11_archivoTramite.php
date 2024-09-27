<?php
session_start();
// Verificar si el usuario ha iniciado sesión y tiene un municipio asignado
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    // Redirigir al inicio de sesión si no está logueado
    header("Location: index.html");
    exit();
}

$municipio = $_SESSION["municipio"]; // Obtener el municipio del usuario logueado
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
</head>
<body>
    <div class="logo">
        <img src="img/logoTDP.png" alt="TDP Logo">
    </div>

    <div class="container">
        <h1>FORMATO ENTREGA RECEPCIÓN</h1>
        <h2>SELECCIONE LAS OPCIONES PARA FORMATO</h2>
        
        <!-- Muestra el texto completo de la clasificación aquí -->
        <h3 id="clasificacionSeleccionada"></h3>

        <div class="form-group">
            <label for="no">No.</label>
            <input type="text" id="no" placeholder="INGRESE EL NUMERO">
        </div>

        <div class="form-group">
            <label for="denominacion">DENOMINACIÓN</label>
            <textarea id="denominacion" placeholder="Ingrese información"></textarea>
        </div>

        <div class="form-group">
            <label for="fecha">PUBLICACIÓN Y FECHA</label>
            <input type="text" id="fecha" placeholder="Ingrese información">
        </div>

        <div class="form-group">
            <label for="informacion-al">INFORMACIÓN AL:</label>
            <textarea id="informacion-al" placeholder="Ingrese información"></textarea>
        </div>

        <div class="form-group">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" value="2023-08-17">
        </div>

        <div class="form-group">
            <label for="responsable">RESPONSABLE DE LA INFORMACIÓN</label>
            <textarea id="responsable" placeholder="Ingrese información"></textarea>
        </div>

        <div class="form-group">
            <label for="observaciones">OBSERVACIONES</label>
            <textarea id="observaciones" placeholder="Ingrese información"></textarea>
        </div>

        <div class="button-container">
            <button>GUARDAR</button>
            <button>ANEXAR ARCHIVO</button>
        </div>
    </div>
</body>
</html>
