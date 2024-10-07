<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["municipio"])) {
    header("Location: ../index.html?error=No%20has%20iniciado%20sesión");
    exit();
}

// Incluir la conexión a la base de datos
include 'conexion.php';

// Verificar si se ha proporcionado un ID válido en la URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Obtener los parámetros de filtrado para redirigir de vuelta correctamente
    $area_nombre = isset($_GET['area']) ? trim($_GET['area']) : null;
    $clasificacion_codigo = isset($_GET['clasificacion']) ? trim($_GET['clasificacion']) : null;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');
    $pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;

    // Validar página
    $pagina = max($pagina, 1);

    // Iniciar la transacción para asegurar la integridad de la eliminación
    $conexion->begin_transaction();

    try {
        // Primero, eliminar en la tabla 'formatos' (si hay restricciones de clave foránea, de lo contrario, eliminar en 'formato_1_2' primero)
        // Suponiendo que 'formato_1_2' tiene una relación de clave foránea con 'formatos'

        // Eliminar en 'formato_1_2' primero
        $stmt_delete_f12 = $conexion->prepare("DELETE FROM formato_1_2 WHERE formato_id = ?");
        if ($stmt_delete_f12 === false) {
            throw new Exception("Error en la preparación de la consulta de eliminación en formato_1_2: " . $conexion->error);
        }
        $stmt_delete_f12->bind_param("i", $id);
        if (!$stmt_delete_f12->execute()) {
            throw new Exception("Error al eliminar en formato_1_2: " . $stmt_delete_f12->error);
        }
        $stmt_delete_f12->close();

        // Luego, eliminar en 'formatos'
        $stmt_delete_f = $conexion->prepare("DELETE FROM formatos WHERE id = ?");
        if ($stmt_delete_f === false) {
            throw new Exception("Error en la preparación de la consulta de eliminación en formatos: " . $conexion->error);
        }
        $stmt_delete_f->bind_param("i", $id);
        if (!$stmt_delete_f->execute()) {
            throw new Exception("Error al eliminar en formatos: " . $stmt_delete_f->error);
        }
        $stmt_delete_f->close();

        // Confirmar la transacción
        $conexion->commit();

        // Construir la URL de redirección con los mismos filtros y un mensaje de éxito
        $params_redirect = [
            'mensaje' => 'Registro eliminado exitosamente',
            'search' => $search,
            'anio' => $anio,
            'area' => $area_nombre,
            'clasificacion' => $clasificacion_codigo,
            'pagina' => $pagina
        ];

        // Filtrar los parámetros que no son nulos o vacíos
        $params_redirect = array_filter($params_redirect, function($value) {
            return $value !== null && $value !== '';
        });

        $query_redirect = http_build_query($params_redirect);
        header("Location: ../mostrarRegistros.php?$query_redirect");
        exit();
    } catch (Exception $e) {
        // Deshacer la transacción en caso de error
        $conexion->rollback();
        // Redirigir de vuelta con un mensaje de error
        $params_redirect = [
            'error' => 'Error al eliminar el registro: ' . $e->getMessage(),
            'search' => $search,
            'anio' => $anio,
            'area' => $area_nombre,
            'clasificacion' => $clasificacion_codigo,
            'pagina' => $pagina
        ];

        // Filtrar los parámetros que no son nulos o vacíos
        $params_redirect = array_filter($params_redirect, function($value) {
            return $value !== null && $value !== '';
        });

        $query_redirect = http_build_query($params_redirect);
        header("Location: ../mostrarRegistros.php?$query_redirect");
        exit();
    }
} else {
    // Si no se proporciona un ID válido, redirigir con un mensaje de error
    $params_redirect = [
        'error' => 'ID de registro no válido',
        'search' => isset($_GET['search']) ? trim($_GET['search']) : '',
        'anio' => isset($_GET['anio']) ? intval($_GET['anio']) : date('Y'),
        'area' => isset($_GET['area']) ? trim($_GET['area']) : null,
        'clasificacion' => isset($_GET['clasificacion']) ? trim($_GET['clasificacion']) : null,
        'pagina' => isset($_GET['pagina']) ? intval($_GET['pagina']) : 1
    ];

    // Filtrar los parámetros que no son nulos o vacíos
    $params_redirect = array_filter($params_redirect, function($value) {
        return $value !== null && $value !== '';
    });

    $query_redirect = http_build_query($params_redirect);
    header("Location: ../mostrarRegistros.php?$query_redirect");
    exit();
}

// Cerrar la conexión
$conexion->close();
?>
