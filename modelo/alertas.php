<?php
// modelo/alertas.php
class AlertasInteligentes {
    private $db;
    
    public function __construct($conexion) {
        $this->db = $conexion;
    }
    
    public function generarAlertas($usuario_id) {
        $alertas = [];
        
        // 1. Alerta: Gasto mayor al promedio
        $gasto_promedio = $this->getGastoPromedio($usuario_id);
        $gasto_actual = $this->getGastoMesActual($usuario_id);
        
        if($gasto_promedio > 0 && $gasto_actual > ($gasto_promedio * 1.3)) {
            $alertas[] = [
                'tipo' => 'advertencia',
                'mensaje' => "⚠️ Tus gastos este mes son 30% mayores al promedio",
                'icono' => '⚠️'
            ];
        }
        
        // 2. Alerta: Meta de ahorro cercana
        if($this->cercaDeMetaAhorro($usuario_id)) {
            $alertas[] = [
                'tipo' => 'exito',
                'mensaje' => "🎯 ¡Estás cerca de completar una meta de ahorro!",
                'icono' => '🎯'
            ];
        }
        
        // 3. Alerta: Balance negativo
        $balance = $this->getBalanceActual($usuario_id);
        if($balance < 0) {
            $alertas[] = [
                'tipo' => 'peligro',
                'mensaje' => "🔴 ¡Balance negativo! Revisa tus gastos",
                'icono' => '🔴'
            ];
        }
        
        // 4. Alerta: Ingresos bajos este mes
        if($this->ingresosBajos($usuario_id)) {
            $alertas[] = [
                'tipo' => 'advertencia',
                'mensaje' => "📉 Ingresos más bajos de lo habitual este mes",
                'icono' => '📉'
            ];
        }
        
        return $alertas;
    }
    
    private function getGastoPromedio($usuario_id) {
        $sql = "SELECT AVG(total_mensual) as promedio 
                FROM (
                    SELECT EXTRACT(YEAR FROM fecha) as año, EXTRACT(MONTH FROM fecha) as mes, 
                           SUM(monto) as total_mensual 
                    FROM gastos 
                    WHERE id_usuario = $1 
                    GROUP BY año, mes
                ) as gastos_mensuales";
        
        $result = pg_query_params($this->db, $sql, array($usuario_id));
        if ($result && $row = pg_fetch_assoc($result)) {
            return floatval($row['promedio']);
        }
        return 0;
    }
    
    private function getGastoMesActual($usuario_id) {
        $mes_actual = date('Y-m');
        $sql = "SELECT COALESCE(SUM(monto), 0) as total 
                FROM gastos 
                WHERE id_usuario = $1 AND TO_CHAR(fecha, 'YYYY-MM') = $2";
        
        $result = pg_query_params($this->db, $sql, array($usuario_id, $mes_actual));
        if ($result && $row = pg_fetch_assoc($result)) {
            return floatval($row['total']);
        }
        return 0;
    }
    
    private function cercaDeMetaAhorro($usuario_id) {
        $sql = "SELECT nombre_meta, monto_actual, meta_total 
                FROM metas 
                WHERE id_usuario = $1 AND estado = 'activa' 
                AND (monto_actual / meta_total) >= 0.9
                AND (monto_actual / meta_total) < 1.0";
        
        $result = pg_query_params($this->db, $sql, array($usuario_id));
        return ($result && pg_num_rows($result) > 0);
    }
    
    private function getBalanceActual($usuario_id) {
        // Sumar ingresos
        $sql_ingresos = "SELECT COALESCE(SUM(monto), 0) as total FROM ingresos WHERE id_usuario = $1";
        $result_ingresos = pg_query_params($this->db, $sql_ingresos, array($usuario_id));
        $ingresos = $result_ingresos ? floatval(pg_fetch_assoc($result_ingresos)['total']) : 0;
        
        // Sumar gastos
        $sql_gastos = "SELECT COALESCE(SUM(monto), 0) as total FROM gastos WHERE id_usuario = $1";
        $result_gastos = pg_query_params($this->db, $sql_gastos, array($usuario_id));
        $gastos = $result_gastos ? floatval(pg_fetch_assoc($result_gastos)['total']) : 0;
        
        return $ingresos - $gastos;
    }
    
    private function ingresosBajos($usuario_id) {
        $mes_actual = date('Y-m');
        
        // Ingresos del mes actual
        $sql_actual = "SELECT COALESCE(SUM(monto), 0) as total 
                       FROM ingresos 
                       WHERE id_usuario = $1 AND TO_CHAR(fecha, 'YYYY-MM') = $2";
        $result_actual = pg_query_params($this->db, $sql_actual, array($usuario_id, $mes_actual));
        $ingresos_actual = $result_actual ? floatval(pg_fetch_assoc($result_actual)['total']) : 0;
        
        // Promedio de ingresos de los últimos 3 meses
        $sql_promedio = "SELECT COALESCE(AVG(total_mensual), 0) as promedio 
                         FROM (
                             SELECT EXTRACT(YEAR FROM fecha) as año, EXTRACT(MONTH FROM fecha) as mes, 
                                    SUM(monto) as total_mensual 
                             FROM ingresos 
                             WHERE id_usuario = $1 
                             AND fecha >= CURRENT_DATE - INTERVAL '3 months'
                             GROUP BY año, mes
                         ) as ingresos_mensuales";
        
        $result_promedio = pg_query_params($this->db, $sql_promedio, array($usuario_id));
        $ingresos_promedio = $result_promedio ? floatval(pg_fetch_assoc($result_promedio)['promedio']) : 0;
        
        return ($ingresos_promedio > 0 && $ingresos_actual < ($ingresos_promedio * 0.7));
    }
}
?>