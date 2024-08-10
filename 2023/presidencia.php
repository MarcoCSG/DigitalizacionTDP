<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLASIFICACIÓN</title>
    <link rel="stylesheet" href="../css/clasificaciones.css">
    <link rel="icon" href="../img/escudo.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&family=Shippori+Antique&display=swap" rel="stylesheet">
    <style>
        body::after {
            content: "";
            background: url('../img/escudo.png') center no-repeat;
            background-size: 80% 70%;
            opacity: 0.5; /* Ajusta la opacidad aquí */
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            position: fixed; /* Cambia 'absolute' a 'fixed' */
            z-index: -1;   
        }
    </style>
    
</head>
<body>
    <main>
        <h1 class="titulo">PRESIDENCIA</h1>
    </main>
    <section>
        <figure>
            <img src="../img/misantla.png" alt="" class="imgMisantla">
        </figure>
        <figure>
            <img  class="imgEmpresa" src="../img/logoEmpresa.png" alt="">
        </figure>
    </section>

    <div class="btn_contenedor">
        <button class="btnImagen" onclick="location.href='presidencia/enero-diciembre.php?clasificacion=OFICIOS ENVIADOS&area=<?php echo $_GET['area']; ?>'">OFICIOS ENVIADOS</button>
        <button class="btnImagen" onclick="location.href='presidencia/enero-diciembre.php?clasificacion=OFICIOS RECIBIDOS DEPENDENCIAS&area=<?php echo $_GET['area']; ?>'">OFICIOS RECIBIDOS DEPENDENCIAS</button>
        

    </div>
</body>
</html>