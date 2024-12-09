<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    header("Location: ../index.html?error=No%20has%20iniciado%20sesión");
    exit();
}

// Incluir la conexión a la base de datos
include 'conexion.php';

// Verificar si el ID está presente y es válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../mostrarRegistros3_5.php?error=ID%20inválido");
    exit();
}

$id = intval($_GET['id']);

// Capturar los filtros adicionales de la URL
$area = isset($_GET['area']) ? $_GET['area'] : '';
$clasificacion = isset($_GET['clasificacion']) ? $_GET['clasificacion'] : '';
$anio = isset($_GET['anio']) ? $_GET['anio'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

// Preparar la consulta para eliminar el registro
$stmt_delete = $conexion->prepare("DELETE FROM formato_3_5 WHERE formato_id = ?");
if (!$stmt_delete) {
    die("Error en la preparación de la consulta de eliminación: " . $conexion->error);
}
$stmt_delete->bind_param("i", $id);

// Ejecutar la eliminación
if ($stmt_delete->execute()) {
    // Redirigir con mensaje de éxito
    header("Location: ../mostrarRegistros3_5.php?mensaje=Registro%20eliminado%20correctamente&area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&anio=" . urlencode($anio) . "&search=" . urlencode($search) . "&pagina=" . urlencode($pagina));
    exit();
} else {
    // Redirigir con mensaje de error
    header("Location: ../mostrarRegistros3_5.php?error=No%20se%20pudo%20eliminar%20el%20registro&area=" . urlencode($area) . "&clasificacion=" . urlencode($clasificacion) . "&anio=" . urlencode($anio) . "&search=" . urlencode($search) . "&pagina=" . urlencode($pagina));
    exit();
}

$stmt_delete->close();
$conexion->close();
?>
