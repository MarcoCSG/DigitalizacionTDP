<?php
// Obtener parámetros de la URL
$area = isset($_GET['area']) ? $_GET['area'] : '';
$clasificacion = isset($_GET['clasificacion']) ? $_GET['clasificacion'] : '';

// Mostrar valores capturados
echo "Área: " . htmlspecialchars($area) . "<br>";
echo "Clasificación: " . htmlspecialchars($clasificacion);
?>
