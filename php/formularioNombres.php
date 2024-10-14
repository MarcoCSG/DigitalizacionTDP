<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar Reporte PDF</title>
</head>
<body>
    <h2>Generar Reporte PDF</h2>
    <form action="generarPDF5_11.php" method="post">
        <label for="elaboro">Nombre de quien Elaboró:</label><br>
        <input type="text" id="elaboro" name="elaboro" required><br><br>
        
        <label for="autorizo">Nombre de quien Autorizó:</label><br>
        <input type="text" id="autorizo" name="autorizo" required><br><br>
        
        <input type="submit" value="Generar PDF">
    </form>
</body>
</html>
