<?php
// modelo/metas.php - API para PostgreSQL en Render

session_start();
require_once 'conexion.php'; // Tu conexión a PostgreSQL

header('Content-Type: application/json');

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

switch ($accion) {
    case 'listar':
        listarMetas($conexion, $id_usuario);
        break;
    
    case 'agregar':
        agregarMeta($conexion, $id_usuario);
        break;
    
    case 'actualizar':
        actualizarMeta($conexion, $id_usuario);
        break;
    
    case 'eliminar':
        eliminarMeta($conexion, $id_usuario);
        break;
    
    case 'actualizar_monto':
        actualizarMonto($conexion, $id_usuario);
        break;
    
    case 'listar_logros':
        listarLogros($conexion, $id_usuario);
        break;
    
    default:
        echo json_encode(['error' => 'Acción no válida']);
}

// ========== FUNCIONES DE METAS ==========

function listarMetas($conexion, $id_usuario) {
    $sql = "SELECT * FROM metas WHERE id_usuario = $1 AND estado = 'activa' ORDER BY fecha_creacion DESC";
    $resultado = pg_query_params($conexion, $sql, array($id_usuario));
    
    $metas = [];
    while ($row = pg_fetch_assoc($resultado)) {
        $porcentaje = $row['meta_total'] > 0 ? min(round(($row['monto_actual'] / $row['meta_total']) * 100), 100) : 0;
        $row['porcentaje'] = $porcentaje;
        $metas[] = $row;
    }
    
    echo json_encode(['success' => true, 'metas' => $metas]);
}

function agregarMeta($conexion, $id_usuario) {
    $nombre = trim($_POST['nombre_meta'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $meta_total = floatval($_POST['meta_total'] ?? 0);
    $monto_actual = floatval($_POST['monto_actual'] ?? 0);
    $icono = $_POST['icono'] ?? '🎯';
    $fecha_objetivo = !empty($_POST['fecha_objetivo']) ? $_POST['fecha_objetivo'] : null;
    
    if (empty($nombre) || $meta_total <= 0) {
        echo json_encode(['error' => 'Nombre y monto objetivo son requeridos']);
        return;
    }
    
    $sql = "INSERT INTO metas (id_usuario, nombre_meta, descripcion, meta_total, monto_actual, icono, fecha_objetivo) 
            VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING id_meta";
    
    $resultado = pg_query_params($conexion, $sql, array(
        $id_usuario, $nombre, $descripcion, $meta_total, $monto_actual, $icono, $fecha_objetivo
    ));
    
    if ($resultado) {
        $row = pg_fetch_assoc($resultado);
        echo json_encode(['success' => true, 'mensaje' => 'Meta creada exitosamente', 'id_meta' => $row['id_meta']]);
    } else {
        echo json_encode(['error' => 'Error al crear la meta: ' . pg_last_error($conexion)]);
    }
}

function actualizarMeta($conexion, $id_usuario) {
    $id_meta = intval($_POST['id_meta'] ?? 0);
    $nombre = trim($_POST['nombre_meta'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $meta_total = floatval($_POST['meta_total'] ?? 0);
    $icono = $_POST['icono'] ?? '🎯';
    $fecha_objetivo = !empty($_POST['fecha_objetivo']) ? $_POST['fecha_objetivo'] : null;
    
    if ($id_meta <= 0 || empty($nombre) || $meta_total <= 0) {
        echo json_encode(['error' => 'Datos incompletos']);
        return;
    }
    
    $sql = "UPDATE metas SET nombre_meta = $1, descripcion = $2, meta_total = $3, icono = $4, fecha_objetivo = $5 
            WHERE id_meta = $6 AND id_usuario = $7";
    
    $resultado = pg_query_params($conexion, $sql, array(
        $nombre, $descripcion, $meta_total, $icono, $fecha_objetivo, $id_meta, $id_usuario
    ));
    
    if ($resultado) {
        echo json_encode(['success' => true, 'mensaje' => 'Meta actualizada exitosamente']);
    } else {
        echo json_encode(['error' => 'Error al actualizar: ' . pg_last_error($conexion)]);
    }
}

function eliminarMeta($conexion, $id_usuario) {
    $id_meta = intval($_POST['id_meta'] ?? 0);
    
    if ($id_meta <= 0) {
        echo json_encode(['error' => 'ID de meta inválido']);
        return;
    }
    
    $sql = "UPDATE metas SET estado = 'cancelada' WHERE id_meta = $1 AND id_usuario = $2";
    $resultado = pg_query_params($conexion, $sql, array($id_meta, $id_usuario));
    
    if ($resultado) {
        echo json_encode(['success' => true, 'mensaje' => 'Meta eliminada exitosamente']);
    } else {
        echo json_encode(['error' => 'Error al eliminar: ' . pg_last_error($conexion)]);
    }
}

function actualizarMonto($conexion, $id_usuario) {
    $id_meta = intval($_POST['id_meta'] ?? 0);
    $monto_adicional = floatval($_POST['monto_adicional'] ?? 0);
    
    if ($id_meta <= 0 || $monto_adicional <= 0) {
        echo json_encode(['error' => 'Datos inválidos']);
        return;
    }
    
    // Obtener meta actual
    $sql = "SELECT monto_actual, meta_total, nombre_meta FROM metas WHERE id_meta = $1 AND id_usuario = $2";
    $resultado = pg_query_params($conexion, $sql, array($id_meta, $id_usuario));
    $meta = pg_fetch_assoc($resultado);
    
    if (!$meta) {
        echo json_encode(['error' => 'Meta no encontrada']);
        return;
    }
    
    $nuevo_monto = $meta['monto_actual'] + $monto_adicional;
    
    // Actualizar monto
    $sql = "UPDATE metas SET monto_actual = $1 WHERE id_meta = $2 AND id_usuario = $3";
    $resultado = pg_query_params($conexion, $sql, array($nuevo_monto, $id_meta, $id_usuario));
    
    if ($resultado) {
        // Verificar si se completó la meta
        if ($nuevo_monto >= $meta['meta_total']) {
            $sql = "UPDATE metas SET estado = 'completada' WHERE id_meta = $1";
            pg_query_params($conexion, $sql, array($id_meta));
            
            // Crear logro de meta completada
            verificarYCrearLogro($conexion, $id_usuario, 'meta_completada', $meta['nombre_meta']);
        }
        
        echo json_encode([
            'success' => true, 
            'mensaje' => 'Monto actualizado exitosamente', 
            'nuevo_monto' => $nuevo_monto,
            'meta_completada' => $nuevo_monto >= $meta['meta_total']
        ]);
    } else {
        echo json_encode(['error' => 'Error al actualizar monto: ' . pg_last_error($conexion)]);
    }
}

// ========== FUNCIONES DE LOGROS ==========

function listarLogros($conexion, $id_usuario, $limite = 5) {
    $sql = "SELECT * FROM logros WHERE id_usuario = $1 ORDER BY fecha_obtenido DESC LIMIT $2";
    $resultado = pg_query_params($conexion, $sql, array($id_usuario, $limite));
    
    $logros = [];
    while ($row = pg_fetch_assoc($resultado)) {
        $logros[] = $row;
    }
    
    echo json_encode(['success' => true, 'logros' => $logros]);
}

function verificarYCrearLogro($conexion, $id_usuario, $tipo_logro, $contexto = '') {
    // Verificar si ya tiene el logro
    $sql = "SELECT id_logro FROM logros WHERE id_usuario = $1 AND tipo_logro = $2";
    $resultado = pg_query_params($conexion, $sql, array($id_usuario, $tipo_logro));
    
    if (pg_num_rows($resultado) > 0) {
        return false; // Ya tiene el logro
    }
    
    // Obtener información del tipo de logro
    $sql = "SELECT nombre, descripcion, icono FROM tipos_logros WHERE codigo = $1";
    $resultado = pg_query_params($conexion, $sql, array($tipo_logro));
    $info_logro = pg_fetch_assoc($resultado);
    
    if (!$info_logro) {
        return false;
    }
    
    // Personalizar mensaje según el tipo
    $mensaje = $info_logro['nombre'];
    if (!empty($contexto) && $tipo_logro == 'meta_completada') {
        $mensaje = "Completaste: " . $contexto;
    }
    
    $icono = $info_logro['icono'];
    
    // Crear el logro
    $sql = "INSERT INTO logros (id_usuario, tipo_logro, mensaje, icono, visto) VALUES ($1, $2, $3, $4, FALSE)";
    $resultado = pg_query_params($conexion, $sql, array($id_usuario, $tipo_logro, $mensaje, $icono));
    
    return $resultado !== false;
}

function verificarLogrosAutomaticos($conexion, $id_usuario) {
    // Verificar primer ingreso
    $sql = "SELECT COUNT(*) as total FROM ingresos WHERE id_usuario = $1";
    $resultado = pg_query_params($conexion, $sql, array($id_usuario));
    $data = pg_fetch_assoc($resultado);
    
    if ($data['total'] == 1) {
        verificarYCrearLogro($conexion, $id_usuario, 'primer_ingreso');
    }
    
    // Verificar primer gasto
    $sql = "SELECT COUNT(*) as total FROM gastos WHERE id_usuario = $1";
    $resultado = pg_query_params($conexion, $sql, array($id_usuario));
    $data = pg_fetch_assoc($resultado);
    
    if ($data['total'] == 1) {
        verificarYCrearLogro($conexion, $id_usuario, 'primer_gasto');
    }
    
    // Verificar ahorros (balance total)
    $balance = calcularBalance($conexion, $id_usuario);
    
    if ($balance >= 100 && $balance < 500) {
        verificarYCrearLogro($conexion, $id_usuario, 'ahorro_100');
    } elseif ($balance >= 500 && $balance < 1000) {
        verificarYCrearLogro($conexion, $id_usuario, 'ahorro_500');
    } elseif ($balance >= 1000) {
        verificarYCrearLogro($conexion, $id_usuario, 'ahorro_1000');
    }
    
    // Verificar balance positivo mensual
    $sql = "SELECT 
                (SELECT COALESCE(SUM(monto), 0) FROM ingresos WHERE id_usuario = $1 AND EXTRACT(MONTH FROM fecha) = EXTRACT(MONTH FROM CURRENT_DATE) AND EXTRACT(YEAR FROM fecha) = EXTRACT(YEAR FROM CURRENT_DATE)) -
                (SELECT COALESCE(SUM(monto), 0) FROM gastos WHERE id_usuario = $2 AND EXTRACT(MONTH FROM fecha) = EXTRACT(MONTH FROM CURRENT_DATE) AND EXTRACT(YEAR FROM fecha) = EXTRACT(YEAR FROM CURRENT_DATE))
            AS balance_mes";
    $resultado = pg_query_params($conexion, $sql, array($id_usuario, $id_usuario));
    $data = pg_fetch_assoc($resultado);
    
    if ($data['balance_mes'] > 0) {
        verificarYCrearLogro($conexion, $id_usuario, 'balance_positivo');
    }
}

function calcularBalance($conexion, $id_usuario) {
    $sql = "SELECT 
                (SELECT COALESCE(SUM(monto), 0) FROM ingresos WHERE id_usuario = $1) -
                (SELECT COALESCE(SUM(monto), 0) FROM gastos WHERE id_usuario = $2)
            AS balance";
    $resultado = pg_query_params($conexion, $sql, array($id_usuario, $id_usuario));
    $data = pg_fetch_assoc($resultado);
    
    return floatval($data['balance']);
}

// Llamar a verificación automática de logros cuando sea apropiado
if (in_array($accion, ['agregar', 'actualizar_monto'])) {
    verificarLogrosAutomaticos($conexion, $id_usuario);
}
?>