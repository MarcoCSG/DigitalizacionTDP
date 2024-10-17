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

    // Propiedades para almacenar el municipio y rutas de logos
    public $municipio;
    public $logoPath;

    // Propiedades para almacenar los nombres de firmas
    public $elaboro;
    public $autorizo;

    // Encabezado de página
    function Header()
    {
        // Márgenes
        $this->SetMargins(15, 15, 15);

        // Fuente del encabezado principal
        $this->SetFont('Arial', 'B', 18);

        // Tamaño específico para los logos (en mm)
        $logo_ancho = 30;
        $logo_alto = 30;

        // Determinar la ruta del logo según el municipio
        if (isset($this->logoPath) && file_exists($this->logoPath)) {
            // Agregar el logo del municipio en la esquina superior izquierda con tamaño específico
            $this->Image($this->logoPath, 22, 18, $logo_ancho, $logo_alto);
        } else {
            // Logo por defecto si no se encuentra el logo específico del municipio
            $this->Image('../img/ayuntamiento.png', 15, 15, $logo_ancho, $logo_alto); // Asegúrate de tener un logo por defecto
        }

        // Agregar logo fijo en la esquina superior derecha con tamaño específico
        $this->Image('../img/logoTDP.png', $this->GetPageWidth() - 20 - $logo_ancho, 18, $logo_ancho, $logo_alto); // Esquina superior derecha

        // Salto de línea después de las imágenes
        $this->Ln($logo_alto - 20); // Ajuste basado en la altura del logo (30 mm de altura - 25 mm)

        // Verificar si el municipio está definido
        if (isset($this->municipio)) {
            // Formatear el nombre del ayuntamiento dinámicamente
            $titulo_ayuntamiento = '' . strtoupper($this->municipio) . '';
        } else {
            // Valor por defecto si no se define el municipio
            $titulo_ayuntamiento = 'AYUNTAMIENTO';
        }

        // Centrar el título del ayuntamiento
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', $titulo_ayuntamiento), 0, 1, 'C');
        $this->Ln(5);

        // Subtítulo del reporte
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', '5.18 RELACIÓN DE LLAVES'), 0, 1, 'C');
        $this->Ln(10);

        // Definir la fuente del encabezado de la tabla
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(192, 192, 192); // Color gris

        // Definir anchos de columnas ajustados al ancho de la página
        $ancho_columnas = [
            'No.' => 10,     
            'ACTIVIDAD' => 62,                 // Proporción para 'No.'
            'FECHA' => 55,    // Proporción para 'Nombre del Expediente'
            'OBSERVACIONES' => 70,         // Proporción para 'Serie Documental'               // Proporción para 'Clave'
            'AREA RESPONSABLE' => 70,// Proporción para 'Descripción del Contenido'
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

    // // Pie de página
    // function Footer()
    // {
    //     // Posición: 15 mm desde el final
    //     $this->SetY(-15);
    //     // Fuente
    //     $this->SetFont('Arial', 'I', 8);
    //     // Número de página
    //     $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    // }
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

// Obtener los nombres de "Elaboró" y "Autorizó" desde GET
$elaboro = isset($_GET['elaboro']) ? htmlspecialchars(trim($_GET['elaboro']), ENT_QUOTES, 'UTF-8') : 'ELABORÓ';
$autorizo = isset($_GET['autorizo']) ? htmlspecialchars(trim($_GET['autorizo']), ENT_QUOTES, 'UTF-8') : 'AUTORIZÓ';

// Definir la correspondencia entre municipios y sus logos
$logo_mapping = [
    'H.AYUNTAMIENTO DE MISANTLA, VER' => '../img/logoMisantla.png',
    'H.AYUNTAMIENTO DE SANTIAGO TUXTLA, VER' => '../img/logo_santiago.png',
    'H.AYUNTAMIENTO DE CORDOBA, VER' => '../img/logo_Cordoba.png'
    // Agrega más municipios y sus logos aquí según sea necesario
];

// Determinar la ruta del logo basado en el municipio del usuario
$logoPath = isset($logo_mapping[$municipio]) ? $logo_mapping[$municipio] : '../img/ayuntamiento.png'; // Asegúrate de tener un logo por defecto

// Función para obtener datos filtrados
function obtenerDatos($conexion, $usuario, $municipio, $anio, $area_nombre, $clasificacion_codigo, $search)
{
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
                    f1.no, f1.actividad, f1.fecha, f1.observaciones, 
                    f1.area_responsable, f1.informacion_al, f1.responsable, 
                    u.usuario AS nombre_usuario
                FROM formatos f
                JOIN formato_9_1 f1 ON f.id = f1.formato_id
                JOIN usuarios u ON f.usuarios_id = u.id
                WHERE u.usuario = ? AND f.municipio = ? AND f.anio = ?";

    if ($area_id !== null) $query .= " AND f.area_id = ?";
    if ($clasificacion_id !== null) $query .= " AND f.clasificaciones_id = ?";
    if (!empty($search)) {
        $query .= " AND (f1.actividad LIKE ? OR f1.observaciones LIKE ?)";
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

// Asignar las propiedades a la instancia de la clase PDF
$pdf->municipio = $municipio;
$pdf->logoPath = $logoPath;
$pdf->elaboro = $elaboro;
$pdf->autorizo = $autorizo;

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
    $pdf->Cell($pdf->ancho_columnas['ACTIVIDAD'], 10, iconv('UTF-8', 'ISO-8859-1', $row['actividad']), 1, 0, 'C');
    $pdf->Cell($pdf->ancho_columnas['FECHA'], 10, iconv('UTF-8', 'ISO-8859-1', $row['fecha']), 1, 0, 'C');
    $pdf->Cell($pdf->ancho_columnas['OBSERVACIONES'], 10, iconv('UTF-8', 'ISO-8859-1', $row['observaciones']), 1, 0, 'C');
    $pdf->Cell($pdf->ancho_columnas['AREA RESPONSABLE'], 10, iconv('UTF-8', 'ISO-8859-1', $row['area_responsable']), 1, 0, 'C');
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

//$pdf->Ln(5); // Espacio para separar los nombres de las líneas de firma

// Nombres de las personas centrados en cada columna
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(135, 10, iconv('UTF-8', 'ISO-8859-1', $pdf->elaboro), 0, 0, 'C'); // Nombre de ELABORÓ
$pdf->Cell(135, 10, iconv('UTF-8', 'ISO-8859-1', $pdf->autorizo), 0, 1, 'C'); // Nombre de AUTORIZÓ

//$pdf->Ln(5); // Espacio adicional para separar los cargos

// Cargos de las personas centrados en cada columna y en negritas
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(135, 13, iconv('UTF-8', 'ISO-8859-1', 'ENCARGADO DE LOS TRABAJOS'), 0, 0, 'C'); // Cargo de ELABORÓ centrado en negritas
$pdf->Cell(135, 13, iconv('UTF-8', 'ISO-8859-1', 'REPRESENTANTE LEGAL'), 0, 1, 'C'); // Cargo de AUTORIZÓ centrado en negritas

// Salvar el PDF
$pdf->Output('D', 'reporte.pdf');
?>
