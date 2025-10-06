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
                    WHERE id_usuario = :usuario_id 
                    GROUP BY año, mes
                ) as gastos_mensuales";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return floatval($row['promedio']);
        }
        return 0;
    }
    
    private function getGastoMesActual($usuario_id) {
        $mes_actual = date('Y-m');
        $sql = "SELECT COALESCE(SUM(monto), 0) as total 
                FROM gastos 
                WHERE id_usuario = :usuario_id AND TO_CHAR(fecha, 'YYYY-MM') = :mes_actual";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':mes_actual' => $mes_actual
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return floatval($row['total']);
        }
        return 0;
    }
    
    private function cercaDeMetaAhorro($usuario_id) {
        $sql = "SELECT nombre_meta, monto_actual, meta_total 
                FROM metas 
                WHERE id_usuario = :usuario_id AND estado = 'activa' 
                AND (monto_actual / meta_total) >= 0.9
                AND (monto_actual / meta_total) < 1.0";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        
        return $stmt->rowCount() > 0;
    }
    
    private function getBalanceActual($usuario_id) {
        // Sumar ingresos
        $sql_ingresos = "SELECT COALESCE(SUM(monto), 0) as total FROM ingresos WHERE id_usuario = :usuario_id";
        $stmt_ingresos = $this->db->prepare($sql_ingresos);
        $stmt_ingresos->execute([':usuario_id' => $usuario_id]);
        $ingresos_row = $stmt_ingresos->fetch(PDO::FETCH_ASSOC);
        $ingresos = $ingresos_row ? floatval($ingresos_row['total']) : 0;
        
        // Sumar gastos
        $sql_gastos = "SELECT COALESCE(SUM(monto), 0) as total FROM gastos WHERE id_usuario = :usuario_id";
        $stmt_gastos = $this->db->prepare($sql_gastos);
        $stmt_gastos->execute([':usuario_id' => $usuario_id]);
        $gastos_row = $stmt_gastos->fetch(PDO::FETCH_ASSOC);
        $gastos = $gastos_row ? floatval($gastos_row['total']) : 0;
        
        return $ingresos - $gastos;
    }
    
    private function ingresosBajos($usuario_id) {
        $mes_actual = date('Y-m');
        
        // Ingresos del mes actual
        $sql_actual = "SELECT COALESCE(SUM(monto), 0) as total 
                       FROM ingresos 
                       WHERE id_usuario = :usuario_id AND TO_CHAR(fecha, 'YYYY-MM') = :mes_actual";
        $stmt_actual = $this->db->prepare($sql_actual);
        $stmt_actual->execute([
            ':usuario_id' => $usuario_id,
            ':mes_actual' => $mes_actual
        ]);
        $actual_row = $stmt_actual->fetch(PDO::FETCH_ASSOC);
        $ingresos_actual = $actual_row ? floatval($actual_row['total']) : 0;
        
        // Promedio de ingresos de los últimos 3 meses
        $sql_promedio = "SELECT COALESCE(AVG(total_mensual), 0) as promedio 
                         FROM (
                             SELECT EXTRACT(YEAR FROM fecha) as año, EXTRACT(MONTH FROM fecha) as mes, 
                                    SUM(monto) as total_mensual 
                             FROM ingresos 
                             WHERE id_usuario = :usuario_id 
                             AND fecha >= CURRENT_DATE - INTERVAL '3 months'
                             GROUP BY año, mes
                         ) as ingresos_mensuales";
        
        $stmt_promedio = $this->db->prepare($sql_promedio);
        $stmt_promedio->execute([':usuario_id' => $usuario_id]);
        $promedio_row = $stmt_promedio->fetch(PDO::FETCH_ASSOC);
        $ingresos_promedio = $promedio_row ? floatval($promedio_row['promedio']) : 0;
        
        return ($ingresos_promedio > 0 && $ingresos_actual < ($ingresos_promedio * 0.7));
    }
}
?>