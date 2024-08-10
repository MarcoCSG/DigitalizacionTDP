<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meces</title>
    <link rel="stylesheet" href="../../css/meces.css">
    <link rel="icon" href="../../img/escudo.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&family=Shippori+Antique&display=swap" rel="stylesheet">
    <style>
        body::after {
            content: "";
            background: url("../../img/escudo.png") center no-repeat;
            background-size: 80% 110%;
            opacity: 0.5; /* Ajusta la opacidad aquí */
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            position: absolute;
            z-index: -1;   
        }
    </style>
</head>
<body>
    <main>
        <h1 class="titulo">Seleccione un mes </h1>
    </main>
    <section>
        <figure>
            <img src="../../img/misantla.png" alt="" class="imgMisantla">
        </figure>
        <figure>
            <img  class="imgEmpresa" src="../../img/logoEmpresa.png" alt="">
        </figure>
    </section>
    
    <div class="btn_contenedor">
        <!-- <table class="button-table"> -->
        <tr>
        <button class="btnImagen" onclick="location.href='mostrarArchivos.php?subclasificacion=ENERO A DICIEMBRE&clasificacion=<?php echo $_GET['clasificacion']; ?>&area=<?php echo $_GET['area']; ?>'">ENERO A DICIEMBRE</button>
        </tr>
    <!-- </table> -->

        </div>
</body>
</html>