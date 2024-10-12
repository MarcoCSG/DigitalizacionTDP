<?php
// Iniciar el almacenamiento en búfer de salida
ob_start();

// Incluir la subclase PDF
require('../fpdf/fpdf.php');
require 'conexion.php';

// Subclase PDF con Header y Footer
class PDF extends FPDF
{
    // Anchos de columnas
    public $ancho_columnas = [];

    // Encabezado de página
    function Header()
    {
        // Márgenes
        $this->SetMargins(15, 15, 15);

        // Fuente del encabezado principal
        $this->SetFont('Arial', 'B', 18);

        // Agregar imágenes en las esquinas superiores
        $this->Image('../img/logoMisantla.png', 15, 15, 40); // Esquina superior izquierda
        $this->Image('../img/logoTDP.png', $this->GetPageWidth() - 15 - 31, 15, 31); // Esquina superior derecha

        // Salto de línea después de las imágenes
        $this->Ln(10);

        // Centrar el título del ayuntamiento
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'AYUNTAMIENTO DE MISANTLA, VER.'), 0, 1, 'C');
        $this->Ln(5);

        // Subtítulo del reporte
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', '5.11 RELACIÓN DE MANUALES ADMINISTRATIVOS'), 0, 1, 'C');
        $this->Ln(10);

        // Definir la fuente del encabezado de la tabla
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(192, 192, 192); // Color gris

        // Definir anchos de columnas ajustados al ancho de la página
        $ancho_columnas = [
            'No.' => 10,                  // Proporción para 'No.'
            'Nombre del Expediente' => 45, // Proporción para 'Nombre del Expediente'
            'Serie Documental' => 35,      // Proporción para 'Serie Documental'
            'Clave' => 25,                 // Proporción para 'Clave'
            'Descripción del Contenido' => 50, // Proporción para 'Descripción del Contenido'
            'Resguardo' => 20,           // Proporción para 'Resguardado'
            'Confidencial' => 22,          // Proporción para 'Confidencial'
            'Vigencia Documental' => 35,   // Proporción para 'Vigencia Documental'
            'Responsable' => 25  
        ];

        // Guardar los anchos de columnas en una propiedad de la clase
        $this->ancho_columnas = $ancho_columnas;

        // Dibujar el encabezado de la tabla
        foreach ($ancho_columnas as $columna => $ancho) {
            $this->Cell($ancho, 10, iconv('UTF-8', 'ISO-8859-1', strtoupper($columna)), 1, 0, 'C', true);
        }
        $this->Ln();

        // Dibujar el rectángulo que engloba todo el contenido
        $this->Rect(15, 15, $this->GetPageWidth() - 30, $this->GetPageHeight() - 30); // Ajustado al ancho de la página
    }

    // Pie de página
    function Footer()
    {
        // Posición: 15 mm desde el final
        $this->SetY(-15);
        // Fuente
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, '' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Verificar si el usuario ha iniciado sesión
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    die("Acceso no autorizado."); // Considera manejar esto de otra forma para evitar salida directa
}

// Obtener los parámetros de filtrado desde GET
$usuario = $_SESSION["usuario"];
$municipio = $_SESSION["municipio"];
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');
$area_nombre = isset($_GET['area']) ? trim($_GET['area']) : null;
$clasificacion_codigo = isset($_GET['clasificacion']) ? trim($_GET['clasificacion']) : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Función para obtener datos filtrados
function obtenerDatos($conexion, $usuario, $municipio, $anio, $area_nombre, $clasificacion_codigo, $search){
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
                    f5.no, f5.nombre_expediente, f5.serie_documental, f5.clave, 
                    f5.descripcion_contenido, f5.resguardado, f5.confidencial, f5.area_responsable,
                    f5.vigencia_documental, f5.informacion_al, f5.responsable, 
                    u.usuario AS nombre_usuario
                FROM formatos f
                JOIN formato_5_11 f5 ON f.id = f5.formato_id
                JOIN usuarios u ON f.usuarios_id = u.id
                WHERE u.usuario = ? AND f.municipio = ? AND f.anio = ?";

    if ($area_id !== null) $query .= " AND f.area_id = ?";
    if ($clasificacion_id !== null) $query .= " AND f.clasificaciones_id = ?";
    if (!empty($search)) {
        $query .= " AND (f5.nombre_expediente LIKE ? OR f5.responsable LIKE ?)";
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
if (empty($datos)) die("No se encontraron registros para generar el PDF."); // Considera manejar esto de otra forma

// Obtener solo un registro para "Información al" y "Responsable de la información"
$info_al = $datos[0]['informacion_al'] ?? 'No disponible';
$responsable = $datos[0]['responsable'] ?? 'No disponible';

// Crear instancia de la clase PDF
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages(); // Para mostrar el total de páginas en el pie de página
$pdf->AddPage();

// Definir la fuente para las filas de la tabla
$pdf->SetFont('Arial', '', 8);

// Iterar sobre los datos y agregarlos a la tabla
foreach ($datos as $row) {
    // Verificar si es necesario agregar una nueva página
    if ($pdf->GetY() > ($pdf->GetPageHeight() - 30)) { // Margen inferior de 15 mm y espacio para pie de página
        $pdf->AddPage();
    }

    // Agregar las celdas de la fila
    $pdf->Cell($pdf->ancho_columnas['No.'], 10, iconv('UTF-8', 'ISO-8859-1', $row['no']), 1, 0, 'C');
    $pdf->Cell($pdf->ancho_columnas['Nombre del Expediente'], 10, iconv('UTF-8', 'ISO-8859-1', $row['nombre_expediente']), 1, 0, 'C');
    $pdf->Cell($pdf->ancho_columnas['Serie Documental'], 10, iconv('UTF-8', 'ISO-8859-1', $row['serie_documental']), 1, 0, 'C');
    $pdf->Cell($pdf->ancho_columnas['Clave'], 10, iconv('UTF-8', 'ISO-8859-1', $row['clave']), 1, 0, 'C');

    // Usar MultiCell para "Descripción del Contenido"
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->MultiCell($pdf->ancho_columnas['Descripción del Contenido'], 10, iconv('UTF-8', 'ISO-8859-1', $row['descripcion_contenido']), 1, 'C');
    $pdf->SetXY($x + $pdf->ancho_columnas['Descripción del Contenido'], $y);

    $pdf->Cell($pdf->ancho_columnas['Resguardo'], 10, iconv('UTF-8', 'ISO-8859-1', $row['resguardado']), 1, 0, 'C');
    $pdf->Cell($pdf->ancho_columnas['Confidencial'], 10, iconv('UTF-8', 'ISO-8859-1', $row['confidencial']), 1, 0, 'C');
    $pdf->Cell($pdf->ancho_columnas['Vigencia Documental'], 10, iconv('UTF-8', 'ISO-8859-1', $row['vigencia_documental'] ?? 'No disponible'), 1, 0, 'C');
    $pdf->Cell($pdf->ancho_columnas['Responsable'], 10, iconv('UTF-8', 'ISO-8859-1', $row['area_responsable'] ?? 'No disponible'), 1, 0, 'C');

    $pdf->Ln();
}

// Salto de línea para información adicional
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 10, iconv('UTF-8', 'ISO-8859-1', 'INFORMACIÓN AL:'), 0, 0, 'L'); // Justificar a la izquierda
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', $info_al), 0, 1, 'L'); // Mostrar información al

// Sección de información con ajuste de alineación
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(80, 10, iconv('UTF-8', 'ISO-8859-1', 'RESPONSABLE DE LA INFORMACIÓN:'), 0, 0, 'L'); // Aumentar el ancho para alinear el texto
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', $responsable), 0, 2, 'L'); // Mantener el valor más cercano al título anterior

$pdf->Ln(8); // Espacio adicional para separar las líneas de firma
// Sección de firmas ajustada y espaciada
$pdf->SetFont('Arial', 'B', 11);

// Colocación de títulos "ELABORÓ" y "AUTORIZÓ" centrados y con más espacio
$pdf->Cell(135, 13, iconv('UTF-8', 'ISO-8859-1', 'ELABORÓ:'), 0, 0, 'C'); // Centrado en la primera columna (izquierda)
$pdf->Cell(135, 13, iconv('UTF-8', 'ISO-8859-1', 'AUTORIZÓ:'), 0, 1, 'C'); // Centrado en la segunda columna (derecha)

$pdf->Ln(8); // Espacio adicional para separar las líneas de firma

// Líneas de firmas centradas en cada columna
$pdf->Cell(135, 10, '_______________________________', 0, 0, 'C'); // Línea de firma de ELABORÓ
$pdf->Cell(135, 10, '_______________________________', 0, 1, 'C'); // Línea de firma de AUTORIZÓ

//$pdf->Ln(4); // Espacio entre la línea de firma y el nombre

// Nombres de las personas centrados en cada columna
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(135, 12, iconv('UTF-8', 'ISO-8859-1', 'L.C. TOMÁS RAFAEL FUENTES SÁNCHEZ'), 0, 0, 'C'); // Nombre de ELABORÓ centrado
$pdf->Cell(135, 12, iconv('UTF-8', 'ISO-8859-1', 'ING. MARCO CÉSAR SALOMÓN GONZÁLEZ'), 0, 1, 'C'); // Nombre de AUTORIZÓ centrado

// Cargos de las personas centrados en cada columna y en negritas
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(135, 13, iconv('UTF-8', 'ISO-8859-1', 'ENCARGADO DE LOS TRABAJOS'), 0, 0, 'C'); // Cargo de ELABORÓ centrado en negritas
$pdf->Cell(135, 13, iconv('UTF-8', 'ISO-8859-1', 'REPRESENTANTE LEGAL'), 0, 1, 'C'); // Cargo de AUTORIZÓ centrado en negritas

// Salvar el PDF
$pdf->Output('D', 'reporte.pdf');

?>
