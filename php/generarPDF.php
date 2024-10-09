<?php
// Incluir la biblioteca FPDF
require('../fpdf/fpdf.php');

// Incluir la conexión a la base de datos
require 'conexion.php';

// Verificar si el usuario ha iniciado sesión
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    die("Acceso no autorizado.");
}

// Obtener los parámetros de filtrado desde GET
$usuario = $_SESSION["usuario"];
$municipio = $_SESSION["municipio"];
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');
$area_nombre = isset($_GET['area']) ? trim($_GET['area']) : null;
$clasificacion_codigo = isset($_GET['clasificacion']) ? trim($_GET['clasificacion']) : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Función para obtener datos filtrados
function obtenerDatos($conexion, $usuario, $municipio, $anio, $area_nombre, $clasificacion_codigo, $search) {
    $area_id = null;
    $clasificacion_id = null;
    $params = [$usuario, $municipio, $anio];
    $types = "ssi"; // Tipos de parámetros para bind_param

    // Traducir area_nombre a area_id
    if ($area_nombre !== null) {
        $stmt_area = $conexion->prepare("SELECT id FROM areas WHERE nombre = ?");
        $stmt_area->bind_param("s", $area_nombre);
        $stmt_area->execute();
        $result_area = $stmt_area->get_result();
        $row_area = $result_area->fetch_assoc();
        $area_id = $row_area ? $row_area['id'] : die("Área no encontrada.");
        $params[] = $area_id;
        $types .= "i";
        $stmt_area->close();
    }

    // Traducir clasificacion_codigo a clasificacion_id
    if ($clasificacion_codigo !== null) {
        if ($area_id === null) die("Debe proporcionar un área para filtrar la clasificación.");
        $stmt_clas = $conexion->prepare("SELECT id FROM clasificaciones WHERE codigo = ? AND area_id = ?");
        $stmt_clas->bind_param("si", $clasificacion_codigo, $area_id);
        $stmt_clas->execute();
        $result_clas = $stmt_clas->get_result();
        $row_clas = $result_clas->fetch_assoc();
        $clasificacion_id = $row_clas ? $row_clas['id'] : die("Clasificación no encontrada.");
        $params[] = $clasificacion_id;
        $types .= "i";
        $stmt_clas->close();
    }

    // Construcción de la consulta para todos los registros
    $query = "SELECT f.id, f.municipio, f.anio, f.ruta_archivo, 
                     f1.no, f1.denominacion, f1.publicacion_fecha, f1.informacion_al, 
                     f1.fecha_autorizacion, f1.responsable, f1.observaciones, 
                     u.usuario AS nombre_usuario
              FROM formatos f
              JOIN formato_1_2 f1 ON f.id = f1.formato_id
              JOIN usuarios u ON f.usuarios_id = u.id
              WHERE u.usuario = ? AND f.municipio = ? AND f.anio = ?";

    if ($area_id !== null) $query .= " AND f.area_id = ?";
    if ($clasificacion_id !== null) $query .= " AND f.clasificaciones_id = ?";
    if (!empty($search)) {
        $query .= " AND (f1.denominacion LIKE ? OR f1.responsable LIKE ?)";
        $search_param = "%" . $search . "%";
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "ss";
    }

    // Agregar ORDER BY para que la consulta devuelva registros ordenados
    $query .= " ORDER BY f.fecha_creacion ASC, f.id ASC"; 

    $stmt = $conexion->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $datos = [];
    while ($row = $result->fetch_assoc()) $datos[] = $row;

    $stmt->close();
    return $datos;
}

// Obtener los datos filtrados
$datos = obtenerDatos($conexion, $usuario, $municipio, $anio, $area_nombre, $clasificacion_codigo, $search);
if (empty($datos)) die("No se encontraron registros para generar el PDF.");

// Obtener solo un registro para "Información al" y "Responsable de la información"
$info_al = $datos[0]['informacion_al'] ?? 'No disponible';
$responsable = $datos[0]['responsable'] ?? 'No disponible';

// Generar el PDF
// Generar el PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Agregar imágenes en las esquinas superiores
$pdf->Image('../img/logoMisantla.png', 10, 10, 30); // Esquina superior izquierda
$pdf->Image('../img/logoTDP.png', 260, 10, 30); // Esquina superior derecha

// Centrar el título del ayuntamiento
$pdf->Cell(0, 10, 'AYUNTAMIENTO DE MISANTLA, VER.', 0, 1, 'C');
$pdf->Ln(5);

// Subtítulo del reporte
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, '1.2 RELACIÓN DE MANUALES ADMINISTRATIVOS', 0, 1, 'C');
$pdf->Ln(10);

// Encabezados de tabla
$ancho_columnas = ['No.' => 15, 'DENOMINACIÓN' => 80, 'FECHA' => 35, 'OBSERVACIONES' => 60, 'PUBLICACIÓN Y FECHA' => 40];
$pdf->SetFont('Arial', 'B', 10);
foreach ($ancho_columnas as $columna => $ancho) {
    $pdf->Cell($ancho, 10, $columna, 1, 0, 'C', true);
}
$pdf->Ln();

// Imprimir filas de datos
$pdf->SetFont('Arial', '', 10);
foreach ($datos as $row) {
    if ($pdf->GetY() > 190) $pdf->AddPage();
    $pdf->Cell($ancho_columnas['No.'], 10, $row['no'], 1, 0, 'C'); // Centrar
    $pdf->Cell($ancho_columnas['DENOMINACIÓN'], 10, iconv('UTF-8', 'ISO-8859-1',$row['denominacion']), 1, 0, 'C'); // Centrar
    $pdf->Cell($ancho_columnas['FECHA'], 10, $row['fecha_autorizacion'], 1, 0, 'C'); // Centrar
    $pdf->Cell($ancho_columnas['OBSERVACIONES'], 10,iconv('UTF-8', 'ISO-8859-1',$row['observaciones']), 1, 0, 'C'); // Centrar
    $pdf->Cell($ancho_columnas['PUBLICACIÓN Y FECHA'], 10, $row['publicacion_fecha'], 1, 0, 'C'); // Centrar
    $pdf->Ln();
}

// Salto de línea para información adicional
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 10, 'INFORMACIÓN AL:', 0, 0, 'L'); // Justificar a la izquierda
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1',$info_al), 0, 1, 'L'); // Mostrar información al

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 10, 'RESPONSABLE DE LA INFORMACIÓN:', 0, 0, 'L'); // Justificar a la izquierda
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1',$responsable), 0, 1, 'L'); // Mostrar responsable de la información
$pdf->Ln(10); // Espacio para firmas

// Información del encargado en la misma fila
$pdf->Cell(0, 10, 'ELABORÓ: L.C. TÓMAS RAFAEL FUENTES SÁNCHEZ', 0, 0, 'L'); // Centrar
$pdf->Cell(0, 10, 'AUTORIZA: L.C.C. HEBER JOHANAN BALÁN CÁCERES', 0, 1, 'R'); // Centrar
$pdf->Cell(0, 10, 'ENCARGADO DE LOS TRABAJOS', 0, 0, 'L'); // Centrar
$pdf->Cell(0, 10, 'REPRESENTANTE LEGAL', 0, 1, 'R'); // Centrar

// Salvar el PDF
$pdf->Output('D', 'reporte.pdf');   


?>
