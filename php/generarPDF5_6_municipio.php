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
    public $superviso;
    public $observaciones;
    public $opcion;


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
        //$this->Image('../img/logoTDP.png', $this->GetPageWidth() - 20 - $logo_ancho, 18, $logo_ancho, $logo_alto); // Esquina superior derecha

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
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', '5.6 RELACIÓN DE BIENES EN PRÉSTAMO O COMODATO'), 0, 1, 'C');
        $this->Ln(10);

        // Definir la fuente del encabezado de la tabla
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(192, 192, 192); // Color gris

      // Definir anchos de columnas ajustados al ancho de la página
        $ancho_columnas = [
            'No.' => 25,
            'TIPO DE BIEN' => 62,
            'PROPIETARIO' => 55,
            'VIGENCIA' => 55,
            'OBSERVACIONES' => 70,          
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
$municipio = $_SESSION["municipio"];
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');
$area_nombre = isset($_GET['area']) ? trim($_GET['area']) : null;
$clasificacion_codigo = isset($_GET['clasificacion']) ? trim($_GET['clasificacion']) : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Obtener los nombres de "Elaboró" y "Autorizó" desde GET
$elaboro = isset($_GET['elaboro']) ? htmlspecialchars(trim($_GET['elaboro']), ENT_QUOTES, 'UTF-8') : 'ELABORÓ';
$superviso = isset($_GET['superviso']) ? htmlspecialchars(trim($_GET['superviso']), ENT_QUOTES, 'UTF-8') : 'SUPERVISO';
$autorizo = isset($_GET['autorizo']) ? htmlspecialchars(trim($_GET['autorizo']), ENT_QUOTES, 'UTF-8') : 'AUTORIZÓ';
$observaciones = isset($_GET['observaciones']) ? htmlspecialchars(trim($_GET['observaciones']), ENT_QUOTES, 'UTF-8') : 'OBSERVACIONES';
$opcion = isset($_GET['opcion']) ? htmlspecialchars(trim($_GET['opcion']), ENT_QUOTES, 'UTF-8') : 'OPCION';

// Definir la correspondencia entre municipios y sus logos
$logo_mapping = [
    'MUNICIPIO DE MISANTLA, VER' => '../img/logoMisantla.png',
    'MUNICIPIO DE SANTIAGO TUXTLA, VER' => '../img/logo_santiago.png',
    'MUNICIPIO DE CORDOBA, VER' => '../img/logo_Cordoba.png'
    // Agregar más municipios y sus logos aquí según sea necesario
];

// Determinar la ruta del logo basado en el municipio del usuario
$logoPath = isset($logo_mapping[$municipio]) ? $logo_mapping[$municipio] : '../img/ayuntamiento.png'; // Asegúrate de tener un logo por defecto

// Función para obtener datos filtrados
function obtenerDatos($conexion, $municipio, $anio, $area_nombre, $clasificacion_codigo, $search)
{
    $area_id = null;
    $clasificacion_id = null;
    $params = [$municipio, $anio];
    $types = "si"; // Inicia con dos parámetros

    // Traducir area_nombre a area_id
    if ($area_nombre !== null) {
        $stmt_area = $conexion->prepare("SELECT id FROM areas WHERE nombre = ?");
        $stmt_area->bind_param("s", $area_nombre);
        $stmt_area->execute();
        $result_area = $stmt_area->get_result();
        $row_area = $result_area->fetch_assoc();
        if ($row_area) {
            $area_id = $row_area['id'];
            $params[] = $area_id; // Agregar area_id a los parámetros
            $types .= "i"; // Agregar tipo entero
        } else {
            die("Área no encontrada.");
        }
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
        if ($row_clas) {
            $clasificacion_id = $row_clas['id'];
            $params[] = $clasificacion_id; // Agregar clasificacion_id a los parámetros
            $types .= "i"; // Agregar tipo entero
        } else {
            die("Clasificación no encontrada.");
        }
        $stmt_clas->close();
    }

    // Construcción de la consulta para todos los registros
    $query = "SELECT f.id, f.municipio, f.anio, f.ruta_archivo, 
            f56.no, f56.tipo_bien, f56.propietario, f56.vigencia, 
            f56.observaciones, f56.informacion_al, f56.responsable
        FROM formatos f
        JOIN formato_5_6 f56 ON f.id = f56.formato_id
        WHERE f.municipio = ? AND f.anio = ?";

    if ($area_id !== null) $query .= " AND f.area_id = ?";
    if ($clasificacion_id !== null) $query .= " AND f.clasificaciones_id = ?";
    if (!empty($search)) {
        $query .= " AND (f56.tipo_bien LIKE ? OR f56.propietario LIKE ?)";
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
$datos = obtenerDatos($conexion, $municipio, $anio, $area_nombre, $clasificacion_codigo, $search);
//if (empty($datos)) die("No se encontraron registros para generar el PDF."); // Considera manejar esto de otra forma

// Obtener solo un registro para "Información al" y "Responsable de la información"
$info_al = $datos[0]['informacion_al'] ?? 'No disponible';
$responsable = $datos[0]['responsable'] ?? 'No disponible';

// Crear instancia de la clase PDF      
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();

// Asignar las propiedades a la instancia de la clase PDF
$pdf->municipio = $municipio;
$pdf->logoPath = $logoPath;
$pdf->elaboro = $elaboro;
$pdf->autorizo = $autorizo;
$pdf->superviso = $superviso;
$pdf->observaciones = $observaciones;
$pdf->opcion = $opcion;

$pdf->AddPage();
$pdf->SetFont('Arial', '', 8);

// Definir los anchos de las columnas
$ancho_no = 25;
$ancho_tipo_bien = 62;
$ancho_propietario = 55;
$ancho_vigencia = 55;
$ancho_observaciones = 70;

// Establecer el color del borde (negro)
$pdf->SetDrawColor(0, 0, 0);

// Función para manejar la conversión de cadenas
function ConvertToISO($texto)
{
    return iconv('UTF-8', 'ISO-8859-1', $texto);
}

// Función para obtener la altura de una celda basada en el contenido
function GetMultiCellHeight($pdf, $ancho, $texto)
{
    $x_inicial = $pdf->GetX();
    $y_inicial = $pdf->GetY();

    $pdf_clon = clone $pdf;
    $pdf_clon->MultiCell($ancho, 5, $texto, 0, 'C');
    $altura = $pdf_clon->GetY() - $y_inicial;

    $pdf->SetXY($x_inicial, $y_inicial);
    return $altura;
}

// Función para imprimir una celda centrada verticalmente con bordes completos
function PrintCenteredCell($pdf, $ancho, $altura_maxima, $texto)
{
    $x_inicial = $pdf->GetX();
    $y_inicial = $pdf->GetY();

    // Calcular la altura de la celda real
    $altura_texto = GetMultiCellHeight($pdf, $ancho, $texto);

    // Calcular el offset para centrar el contenido
    $offset = ($altura_maxima - $altura_texto) / 2;

    // Dibujar el borde de la celda de toda la fila
    $pdf->Rect($x_inicial, $y_inicial, $ancho, $altura_maxima);

    // Mover a la posición central del texto
    $pdf->SetXY($x_inicial, $y_inicial + $offset);

    // Imprimir el texto sin bordes (ya que los bordes fueron dibujados manualmente con Rect)
    $pdf->MultiCell($ancho, 5, ConvertToISO($texto), 0, 'C', false);

    // Volver a la posición inicial para la siguiente celda
    $pdf->SetXY($x_inicial + $ancho, $y_inicial);
}

// Ajustar la función MultiCell y el tamaño de las columnas para que el texto no se sobreponga
function PrintUniformRow($pdf, $row, $altura_maxima) {
    global $ancho_no, $ancho_tipo_bien, $ancho_propietario, $ancho_vigencia, $ancho_observaciones;
    
    // Imprimir las celdas con contenido centrado verticalmente y bordes
    PrintCenteredCell($pdf, $ancho_no, $altura_maxima, $row['no']);
    PrintCenteredCell($pdf, $ancho_tipo_bien, $altura_maxima, $row['tipo_bien']);
    PrintCenteredCell($pdf, $ancho_propietario, $altura_maxima, $row['propietario']);
    PrintCenteredCell($pdf, $ancho_vigencia, $altura_maxima, $row['vigencia']);
    PrintCenteredCell($pdf, $ancho_observaciones, $altura_maxima, $row['observaciones']);

    // Mover a la siguiente fila
    $pdf->Ln($altura_maxima);
}

// Función para imprimir la sección de información y firmas
function ImprimirSeccionFirmas($pdf, $info_al, $responsable, $observaciones, $elaboro, $autorizo, $superviso)
{
    $pdf->Ln(); // Espacio entre la tabla y la información adicional
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(40, 8, iconv('UTF-8', 'ISO-8859-1', 'INFORMACIÓN AL:'), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1', $info_al), 0, 1, 'L');

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(80, 8, iconv('UTF-8', 'ISO-8859-1', 'RESPONSABLE DE LA INFORMACIÓN:'), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1', $responsable), 0, 1, 'L');

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(40, 8, iconv('UTF-8', 'ISO-8859-1', 'OBSERVACIONES:'), 0, 1, 'L');
    $pdf->SetFont('Arial', '', 11);
    // Ajuste para las observaciones con MultiCell para permitir saltos de línea
    $pdf->MultiCell(0, 8, iconv('UTF-8', 'ISO-8859-1', $observaciones), 0, 'L');

    $pdf->Ln(10); // Espacio antes de las firmas

    // Sección de firmas
    $pdf->SetFont('Arial', 'B', 11);

    // Definir el ancho total de la página (270mm para A4 horizontal)
    $ancho_total = 270;
    $margen = 10; // Márgenes de la página
    $ancho_disponible = $ancho_total - 2 * $margen; // Ancho utilizable

    // Definir los anchos para las tres columnas
    $ancho_columna = $ancho_disponible / 3; // Cada columna ocupará un tercio del ancho disponible

    // Primera fila de títulos (ELABORO, SUPERVISO, AUTORIZO)
    $pdf->Cell($ancho_columna, 10, iconv('UTF-8', 'ISO-8859-1', 'ENTREGA:'), 0, 0, 'C');
    $pdf->Cell($ancho_columna, 5, '', 0, 0); // Vacío para que esté alineado
    $pdf->Cell($ancho_columna, 10, iconv('UTF-8', 'ISO-8859-1', 'RECIBE:'), 0, 1, 'C');

    $pdf->Ln(10); // Espacio antes de las líneas de firma

    // Segunda fila de líneas de firma (todas centradas)
    $pdf->Cell($ancho_columna, 8, '_______________________________', 0, 0, 'C');
    $pdf->Cell($ancho_columna, 5, '', 0, 0); // Vacío para que esté alineado
    $pdf->Cell($ancho_columna, 8, '_______________________________', 0, 1, 'C');

    $pdf->Ln(-2); // Espacio antes de los nombres

    // Tercera fila con los nombres (centrados)
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($ancho_columna, 8, iconv('UTF-8', 'ISO-8859-1', $elaboro), 0, 0, 'C');
    $pdf->Cell($ancho_columna, 5, '', 0, 0); // Vacío para que esté alineado
    $pdf->Cell($ancho_columna, 8, iconv('UTF-8', 'ISO-8859-1', $autorizo), 0, 1, 'C');

    $pdf->Ln(-2); // Espacio antes de los cargos

    // Cuarta fila con los cargos (centrados)
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($ancho_columna, 8, iconv('UTF-8', 'ISO-8859-1', 'PRESIDENCIA MUNICIPAL'), 0, 0, 'C');
    $pdf->Cell($ancho_columna, 5, '', 0, 0); // Vacío para que esté alineado
    $pdf->Cell($ancho_columna, 8, iconv('UTF-8', 'ISO-8859-1', 'NOMBRE Y FIRMA'), 0, 1, 'C');

    $pdf->Ln(-2); // Espacio antes de la segunda línea de texto

    // Segunda línea de texto debajo de "REPRESENTANTE LEGAL" (centrada)
    $pdf->Cell($ancho_columna, 5, '', 0, 0); // Vacío para que esté alineado
    $pdf->Cell($ancho_columna, 5, iconv('UTF-8', 'ISO-8859-1', ''), 0, 0, 'C');
    $pdf->Cell($ancho_columna, 5, '', 0, 1); // Vacío para alineación

    $pdf->Ln(5); // Espacio final

}
// Validar si la consulta retornó datos
if (!is_array($datos) || count($datos) === 0) {
    // Manejar el caso de datos vacíos
    $pdf->SetFont('Arial', 'B', 36);
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', $opcion), 0, 1, 'C'); // Aquí cerramos correctamente

    // Simular dos filas vacías
    $pdf->SetFont('Arial', '', 12);
    $altura_fila = 8; // Altura de cada fila simulada
    for ($i = 0; $i < 2; $i++) {
        $pdf->Cell(25, $altura_fila, '', 1, 0, 'C'); // Celda vacía (ajusta el ancho según tu tabla)
        $pdf->Cell(62, $altura_fila, '', 1, 0, 'C');
        $pdf->Cell(55, $altura_fila, '', 1, 0, 'C');
        $pdf->Cell(55, $altura_fila, '', 1, 0, 'C');
        $pdf->Cell(70, $altura_fila, '', 1, 0, 'C'); // Ajusta según el número de columnas
        $pdf->Ln(); // Salto de línea para la siguiente fila
    }
}
// Calcular el número de filas
$total_filas = count($datos);

// Calcular la altura de la sección de firmas
$altura_seccion_firmas = 40; // Ajusta este valor según el tamaño real de la sección de firmas
$espacio_total_disponible = 190; // Altura total de la página sin márgenes
$altura_fila_minima = 15; // Altura mínima de fila

// Función para calcular la altura de una celda basada en el texto
function CalcularAlturaCelda($pdf, $texto, $ancho_celda)
{
    $lineas = $pdf->GetStringWidth($texto) / $ancho_celda;
    $altura = ceil($lineas) * 5; // Ajusta el multiplicador para controlar el espacio entre líneas
    return $altura;
}

// Imprimir las filas de la tabla
foreach ($datos as $row) {
    // Calcular la altura máxima necesaria para esta fila
    $altura_maxima = $altura_fila_minima;
    foreach ($row as $celda_texto) {
        $ancho_celda = 30; // Ajusta según el ancho de cada celda en tu tabla
        $altura_celda = CalcularAlturaCelda($pdf, $celda_texto, $ancho_celda);
        if ($altura_celda > $altura_maxima) {
            $altura_maxima = $altura_celda;
        }
    }

    // Comprobar si hay suficiente espacio para la fila actual
    if ($pdf->GetY() + $altura_maxima > $espacio_total_disponible) {
        // Si no cabe la fila actual, forzar una nueva página
        $pdf->AddPage();
    }

    // Imprimir la fila con la altura máxima calculada
    PrintUniformRow($pdf, $row, $altura_maxima);
}

// Lógica para la sección de firmas
if ($total_filas > 2 || ($pdf->GetY() + $altura_seccion_firmas > $espacio_total_disponible)) {
    // Si hay más de 6 filas o la sección no cabe en la misma página, agregar nueva página
    $pdf->AddPage();
}

// Imprimir la sección de firmas como un solo bloque
ImprimirSeccionFirmas($pdf, $info_al, $responsable, $observaciones, $elaboro, $autorizo, $superviso);

// Salida del PDF
$pdf->Output();
