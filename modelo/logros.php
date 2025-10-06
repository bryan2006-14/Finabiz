<?php
class SistemaLogros {
    private $db;
    
    public function __construct($conexion) {
        $this->db = $conexion;
    }
    
    public function verificarLogros($usuario_id) {
        // Verificar cada tipo de logro disponible
        $tipos_logros = $this->getTiposLogros();
        
        foreach($tipos_logros as $tipo_logro) {
            if($this->cumpleLogro($usuario_id, $tipo_logro['codigo'])) {
                $this->otorgarLogro($usuario_id, $tipo_logro);
            }
        }
    }
    
    private function getTiposLogros() {
        $sql = "SELECT * FROM tipos_logros";
        $result = pg_query($this->db, $sql);
        $tipos = [];
        
        while ($tipo = pg_fetch_assoc($result)) {
            $tipos[] = $tipo;
        }
        
        return $tipos;
    }
    
    private function cumpleLogro($usuario_id, $codigo_logro) {
        switch($codigo_logro) {
            case 'primer_ingreso':
                return $this->tienePrimerIngreso($usuario_id);
            case 'primer_gasto':
                return $this->tienePrimerGasto($usuario_id);
            case 'ahorro_100':
                return $this->tieneAhorro($usuario_id, 100);
            case 'ahorro_500':
                return $this->tieneAhorro($usuario_id, 500);
            case 'ahorro_1000':
                return $this->tieneAhorro($usuario_id, 1000);
            case 'balance_positivo':
                return $this->tieneBalancePositivo($usuario_id);
            case 'racha_7dias':
                return $this->tieneRacha7Dias($usuario_id);
            case 'meta_completada':
                return $this->tieneMetaCompletada($usuario_id);
            case 'presupuesto_cumplido':
                return $this->tienePresupuestoCumplido($usuario_id);
            default:
                return false;
        }
    }
    
    private function tienePrimerIngreso($usuario_id) {
        $sql = "SELECT COUNT(*) as total FROM ingresos WHERE id_usuario = $1";
        $result = pg_query_params($this->db, $sql, array($usuario_id));
        $row = pg_fetch_assoc($result);
        return $row['total'] == 1;
    }
    
    private function tienePrimerGasto($usuario_id) {
        $sql = "SELECT COUNT(*) as total FROM gastos WHERE id_usuario = $1";
        $result = pg_query_params($this->db, $sql, array($usuario_id));
        $row = pg_fetch_assoc($result);
        return $row['total'] == 1;
    }
    
    private function tieneAhorro($usuario_id, $monto) {
        // Calcular balance total (ingresos - gastos)
        $sql_ingresos = "SELECT COALESCE(SUM(monto), 0) as total_ingresos FROM ingresos WHERE id_usuario = $1";
        $result_ingresos = pg_query_params($this->db, $sql_ingresos, array($usuario_id));
        $ingresos = pg_fetch_assoc($result_ingresos)['total_ingresos'];
        
        $sql_gastos = "SELECT COALESCE(SUM(monto), 0) as total_gastos FROM gastos WHERE id_usuario = $1";
        $result_gastos = pg_query_params($this->db, $sql_gastos, array($usuario_id));
        $gastos = pg_fetch_assoc($result_gastos)['total_gastos'];
        
        $ahorro = $ingresos - $gastos;
        return $ahorro >= $monto;
    }
    
    private function tieneBalancePositivo($usuario_id) {
        $mes_actual = date('Y-m');
        
        $sql_ingresos = "SELECT COALESCE(SUM(monto), 0) as total_ingresos FROM ingresos 
                         WHERE id_usuario = $1 AND TO_CHAR(fecha, 'YYYY-MM') = $2";
        $result_ingresos = pg_query_params($this->db, $sql_ingresos, array($usuario_id, $mes_actual));
        $ingresos = pg_fetch_assoc($result_ingresos)['total_ingresos'];
        
        $sql_gastos = "SELECT COALESCE(SUM(monto), 0) as total_gastos FROM gastos 
                       WHERE id_usuario = $1 AND TO_CHAR(fecha, 'YYYY-MM') = $2";
        $result_gastos = pg_query_params($this->db, $sql_gastos, array($usuario_id, $mes_actual));
        $gastos = pg_fetch_assoc($result_gastos)['total_gastos'];
        
        return ($ingresos - $gastos) > 0;
    }
    
    private function tieneRacha7Dias($usuario_id) {
        // Verificar si ha registrado algo en los últimos 7 días consecutivos
        $sql = "SELECT COUNT(DISTINCT DATE(fecha)) as dias_consecutivos 
                FROM (
                    SELECT fecha FROM ingresos WHERE id_usuario = $1 
                    UNION ALL 
                    SELECT fecha FROM gastos WHERE id_usuario = $1
                ) AS movimientos 
                WHERE fecha >= CURRENT_DATE - INTERVAL '7 days'";
        $result = pg_query_params($this->db, $sql, array($usuario_id));
        $row = pg_fetch_assoc($result);
        return $row['dias_consecutivos'] >= 7;
    }
    
    private function tieneMetaCompletada($usuario_id) {
        $sql = "SELECT COUNT(*) as total FROM metas WHERE id_usuario = $1 AND estado = 'completada'";
        $result = pg_query_params($this->db, $sql, array($usuario_id));
        $row = pg_fetch_assoc($result);
        return $row['total'] > 0;
    }
    
    private function tienePresupuestoCumplido($usuario_id) {
        // Esta es una implementación básica - puedes ajustarla según tu lógica de presupuestos
        $mes_actual = date('Y-m');
        
        $sql_gastos = "SELECT COALESCE(SUM(monto), 0) as total_gastos FROM gastos 
                       WHERE id_usuario = $1 AND TO_CHAR(fecha, 'YYYY-MM') = $2";
        $result_gastos = pg_query_params($this->db, $sql_gastos, array($usuario_id, $mes_actual));
        $gastos = pg_fetch_assoc($result_gastos)['total_gastos'];
        
        // Supongamos que el presupuesto mensual es S/1000 (ajusta según tu lógica)
        $presupuesto_mensual = 1000;
        
        return $gastos <= $presupuesto_mensual;
    }
    
    private function otorgarLogro($usuario_id, $tipo_logro) {
        // Verificar si ya tiene el logro
        $sql_check = "SELECT id_logro FROM logros WHERE id_usuario = $1 AND tipo_logro = $2";
        $result_check = pg_query_params($this->db, $sql_check, array($usuario_id, $tipo_logro['codigo']));
        
        if (pg_num_rows($result_check) == 0) {
            // Otorgar el logro
            $sql_insert = "INSERT INTO logros (id_usuario, tipo_logro, mensaje, icono) VALUES ($1, $2, $3, $4)";
            pg_query_params($this->db, $sql_insert, array(
                $usuario_id, 
                $tipo_logro['codigo'], 
                $tipo_logro['descripcion'], 
                $tipo_logro['icono']
            ));
            
            return true;
        }
        
        return false;
    }
    
    public function getLogrosUsuario($usuario_id, $limite = 5) {
        $sql = "SELECT * FROM logros WHERE id_usuario = $1 ORDER BY fecha_obtenido DESC LIMIT $2";
        $result = pg_query_params($this->db, $sql, array($usuario_id, $limite));
        $logros = [];
        
        while ($logro = pg_fetch_assoc($result)) {
            $logros[] = $logro;
        }
        
        return $logros;
    }
    
    public function marcarLogrosComoVistos($usuario_id) {
        $sql = "UPDATE logros SET visto = TRUE WHERE id_usuario = $1 AND visto = FALSE";
        pg_query_params($this->db, $sql, array($usuario_id));
    }
}
?>