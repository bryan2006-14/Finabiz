<?php
class SistemaLogros {
    private $db;
    
    public function __construct($conexion) {
        $this->db = $conexion;
    }
    
    public function verificarLogros($usuario_id) {
        // Obtener todos los tipos de logros de una vez
        $tipos_logros = $this->getTiposLogros();
        
        // Obtener datos del usuario una sola vez para múltiples verificaciones
        $datos_usuario = $this->getDatosUsuarioParaLogros($usuario_id);
        
        foreach($tipos_logros as $tipo_logro) {
            if($this->cumpleLogro($usuario_id, $tipo_logro['codigo'], $datos_usuario)) {
                $this->otorgarLogro($usuario_id, $tipo_logro);
            }
        }
    }
    
    private function getDatosUsuarioParaLogros($usuario_id) {
        $datos = [];
        
        // Obtener conteos básicos
        $sql_conteos = "SELECT 
            (SELECT COUNT(*) FROM ingresos WHERE id_usuario = :usuario_id) as total_ingresos,
            (SELECT COUNT(*) FROM gastos WHERE id_usuario = :usuario_id) as total_gastos,
            (SELECT COUNT(*) FROM metas WHERE id_usuario = :usuario_id AND estado = 'completada') as metas_completadas";
        
        $stmt = $this->db->prepare($sql_conteos);
        $stmt->execute([':usuario_id' => $usuario_id]);
        $conteos = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $datos['total_ingresos'] = $conteos['total_ingresos'] ?? 0;
        $datos['total_gastos'] = $conteos['total_gastos'] ?? 0;
        $datos['metas_completadas'] = $conteos['metas_completadas'] ?? 0;
        
        // Obtener balances
        $sql_balances = "SELECT 
            (SELECT COALESCE(SUM(monto), 0) FROM ingresos WHERE id_usuario = :usuario_id) as total_ingresos_monto,
            (SELECT COALESCE(SUM(monto), 0) FROM gastos WHERE id_usuario = :usuario_id) as total_gastos_monto";
        
        $stmt2 = $this->db->prepare($sql_balances);
        $stmt2->execute([':usuario_id' => $usuario_id]);
        $balances = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        $datos['total_ingresos_monto'] = $balances['total_ingresos_monto'] ?? 0;
        $datos['total_gastos_monto'] = $balances['total_gastos_monto'] ?? 0;
        $datos['ahorro_total'] = $datos['total_ingresos_monto'] - $datos['total_gastos_monto'];
        
        return $datos;
    }
    
    private function getTiposLogros() {
        $sql = "SELECT * FROM tipos_logros";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function cumpleLogro($usuario_id, $codigo_logro, $datos_usuario = null) {
        // Si no se pasaron datos, obtenerlos
        if ($datos_usuario === null) {
            $datos_usuario = $this->getDatosUsuarioParaLogros($usuario_id);
        }
        
        switch($codigo_logro) {
            case 'primer_ingreso':
                return $datos_usuario['total_ingresos'] == 1;
                
            case 'primer_gasto':
                return $datos_usuario['total_gastos'] == 1;
                
            case 'ahorro_100':
                return $datos_usuario['ahorro_total'] >= 100;
                
            case 'ahorro_500':
                return $datos_usuario['ahorro_total'] >= 500;
                
            case 'ahorro_1000':
                return $datos_usuario['ahorro_total'] >= 1000;
                
            case 'balance_positivo':
                return $this->tieneBalancePositivo($usuario_id);
                
            case 'racha_7dias':
                return $this->tieneRacha7Dias($usuario_id);
                
            case 'meta_completada':
                return $datos_usuario['metas_completadas'] > 0;
                
            case 'presupuesto_cumplido':
                return $this->tienePresupuestoCumplido($usuario_id);
                
            default:
                return false;
        }
    }
    
    private function tieneBalancePositivo($usuario_id) {
        $mes_actual = date('Y-m');
        
        $sql = "SELECT 
            (SELECT COALESCE(SUM(monto), 0) FROM ingresos WHERE id_usuario = :usuario_id AND TO_CHAR(fecha, 'YYYY-MM') = :mes_actual) as ingresos_mes,
            (SELECT COALESCE(SUM(monto), 0) FROM gastos WHERE id_usuario = :usuario_id AND TO_CHAR(fecha, 'YYYY-MM') = :mes_actual) as gastos_mes";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':mes_actual' => $mes_actual
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $ingresos = $row['ingresos_mes'] ?? 0;
        $gastos = $row['gastos_mes'] ?? 0;
        
        return ($ingresos - $gastos) > 0;
    }
    
    private function tieneRacha7Dias($usuario_id) {
        // Versión optimizada de la consulta
        $sql = "SELECT COUNT(DISTINCT DATE(fecha)) as dias_consecutivos 
                FROM (
                    SELECT fecha FROM ingresos WHERE id_usuario = :usuario_id AND fecha >= CURRENT_DATE - INTERVAL '7 days'
                    UNION 
                    SELECT fecha FROM gastos WHERE id_usuario = :usuario_id AND fecha >= CURRENT_DATE - INTERVAL '7 days'
                ) AS movimientos";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return ($row['dias_consecutivos'] ?? 0) >= 7;
    }
    
    private function tienePresupuestoCumplido($usuario_id) {
        $mes_actual = date('Y-m');
        
        $sql = "SELECT COALESCE(SUM(monto), 0) as total_gastos 
                FROM gastos 
                WHERE id_usuario = :usuario_id AND TO_CHAR(fecha, 'YYYY-MM') = :mes_actual";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':mes_actual' => $mes_actual
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $gastos = $row['total_gastos'] ?? 0;
        $presupuesto_mensual = 1000; // Ajusta según tu lógica
        
        return $gastos <= $presupuesto_mensual;
    }
    
    private function otorgarLogro($usuario_id, $tipo_logro) {
        // Verificar si ya tiene el logro
        $sql_check = "SELECT id_logro FROM logros WHERE id_usuario = :usuario_id AND tipo_logro = :tipo_logro";
        $stmt_check = $this->db->prepare($sql_check);
        $stmt_check->execute([
            ':usuario_id' => $usuario_id,
            ':tipo_logro' => $tipo_logro['codigo']
        ]);
        
        if ($stmt_check->rowCount() == 0) {
            // Otorgar el logro
            $sql_insert = "INSERT INTO logros (id_usuario, tipo_logro, mensaje, icono) 
                          VALUES (:usuario_id, :tipo_logro, :mensaje, :icono)";
            $stmt_insert = $this->db->prepare($sql_insert);
            $stmt_insert->execute([
                ':usuario_id' => $usuario_id,
                ':tipo_logro' => $tipo_logro['codigo'],
                ':mensaje' => $tipo_logro['descripcion'],
                ':icono' => $tipo_logro['icono']
            ]);
            
            return true;
        }
        
        return false;
    }
    
    public function getLogrosUsuario($usuario_id, $limite = 5) {
        $sql = "SELECT * FROM logros WHERE id_usuario = :usuario_id ORDER BY fecha_obtenido DESC LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function marcarLogrosComoVistos($usuario_id) {
        $sql = "UPDATE logros SET visto = TRUE WHERE id_usuario = :usuario_id AND visto = FALSE";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
    }
}
?>