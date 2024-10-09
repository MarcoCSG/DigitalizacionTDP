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
$pdf->SetMargins(15, 15, 15); // Márgenes: izquierda, arriba, derecha
$pdf->AddPage();

// Dibujar el rectángulo que engloba todo el contenido con el margen especificado
$pdf->Rect(15, 15, 270, 180); // (x, y, ancho, alto)

// Dibujar las líneas de margen para dividir el contenido
$pdf->Line(15, 86, 285, 86); // Línea horizontal superior 1
$pdf->Line(15, 95, 285, 95); // Línea horizontal superior 2
$pdf->Line(15, 105, 285, 105); // Línea horizontal inferior

// Definir la fuente del encabezado principal
$pdf->SetFont('Arial', 'B', 16);

// Agregar imágenes en las esquinas superiores
$pdf->Image('../img/logoMisantla.png', 20, 20, 30); // Esquina superior izquierda con ajuste de posición según el margen
$pdf->Image('../img/logoTDP.png', 250, 20, 30); // Esquina superior derecha con ajuste de posición según el margen

// Centrar el título del ayuntamiento
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'AYUNTAMIENTO DE MISANTLA, VER.'), 0, 1, 'C');
$pdf->Ln(5);

// Subtítulo del reporte
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', '1.2 RELACIÓN DE MANUALES ADMINISTRATIVOS'), 0, 1, 'C');
$pdf->Ln(10);

// Encabezados de tabla en color gris
$ancho_columnas = [
    'No.' => 15, 
    'DENOMINACIÓN' => 80, 
    'FECHA' => 40, 
    'OBSERVACIONES' => 70, 
    'PUBLICACIÓN Y FECHA' => 65
];

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(192, 192, 192); // Color gris

foreach ($ancho_columnas as $columna => $ancho) {
    $pdf->Cell($ancho, 10, iconv('UTF-8', 'ISO-8859-1', $columna), 1, 0, 'C', true); // Usar iconv en encabezados y color de relleno gris
}
$pdf->Ln();

// Imprimir filas de datos
$pdf->SetFont('Arial', '', 10);
foreach ($datos as $row) {
    if ($pdf->GetY() > 190) $pdf->AddPage();

    $pdf->Cell($ancho_columnas['No.'], 10, iconv('UTF-8', 'ISO-8859-1', $row['no']), 1, 0, 'C');
    $pdf->Cell($ancho_columnas['DENOMINACIÓN'], 10, iconv('UTF-8', 'ISO-8859-1', $row['denominacion']), 1, 0, 'C');
    $pdf->Cell($ancho_columnas['FECHA'], 10, iconv('UTF-8', 'ISO-8859-1', $row['fecha_autorizacion']), 1, 0, 'C');
    $pdf->Cell($ancho_columnas['OBSERVACIONES'], 10, iconv('UTF-8', 'ISO-8859-1', $row['observaciones']), 1, 0, 'C');
    $pdf->Cell($ancho_columnas['PUBLICACIÓN Y FECHA'], 10, iconv('UTF-8', 'ISO-8859-1', $row['publicacion_fecha']), 1, 0, 'C');

    $pdf->Ln();
}

// Salto de línea para información adicional
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 10, iconv('UTF-8', 'ISO-8859-1', 'INFORMACIÓN AL:'), 0, 0, 'L'); // Justificar a la izquierda
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', $info_al), 0, 1, 'L'); // Mostrar información al

// Sección de información con ajuste de alineación
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(70, 10, iconv('UTF-8', 'ISO-8859-1', 'RESPONSABLE DE LA INFORMACIÓN:'), 0, 0, 'L'); // Aumentar el ancho para alinear el texto
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', $responsable), 0, 1, 'L'); // Mantener el valor más cercano al título anterior

$pdf->Ln(8); // Espacio adicional para separar las líneas de firma
// Sección de firmas ajustada y espaciada
$pdf->SetFont('Arial', 'B', 10);

// Colocación de títulos "ELABORÓ" y "AUTORIZÓ" centrados y con más espacio
$pdf->Cell(135, 10, iconv('UTF-8', 'ISO-8859-1', 'ELABORÓ:'), 0, 0, 'C'); // Centrado en la primera columna (izquierda)
$pdf->Cell(135, 10, iconv('UTF-8', 'ISO-8859-1', 'AUTORIZÓ:'), 0, 1, 'C'); // Centrado en la segunda columna (derecha)

$pdf->Ln(8); // Espacio adicional para separar las líneas de firma

// Líneas de firmas centradas en cada columna
$pdf->Cell(135, 10, '_______________________________', 0, 0, 'C'); // Línea de firma de ELABORÓ
$pdf->Cell(135, 10, '_______________________________', 0, 1, 'C'); // Línea de firma de AUTORIZÓ

$pdf->Ln(4); // Espacio entre la línea de firma y el nombre

// Nombres de las personas centrados en cada columna
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(135, 10, iconv('UTF-8', 'ISO-8859-1', 'L.C. TOMÁS RAFAEL FUENTES SÁNCHEZ'), 0, 0, 'C'); // Nombre de ELABORÓ centrado
$pdf->Cell(135, 10, iconv('UTF-8', 'ISO-8859-1', 'ING. MARCO CÉSAR SALOMÓN GONZÁLEZ'), 0, 1, 'C'); // Nombre de AUTORIZÓ centrado

// Cargos de las personas centrados en cada columna y en negritas
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(135, 10, iconv('UTF-8', 'ISO-8859-1', 'ENCARGADO DE LOS TRABAJOS'), 0, 0, 'C'); // Cargo de ELABORÓ centrado en negritas
$pdf->Cell(135, 10, iconv('UTF-8', 'ISO-8859-1', 'REPRESENTANTE LEGAL'), 0, 1, 'C'); // Cargo de AUTORIZÓ centrado en negritas

// Salvar el PDF
$pdf->Output('D', 'reporte.pdf');


?>
