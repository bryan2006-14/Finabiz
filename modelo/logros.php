<?php
class SistemaLogros {
    private $db;
    
    public function __construct() {
        // Tu conexión a la base de datos aquí
        $this->db = new PDO("mysql:host=localhost;dbname=tu_base_datos", "usuario", "password");
    }
    
    public function verificarLogros($usuario_id) {
        $logros = [
            'primer_ingreso' => "¡Primer ingreso registrado! 🎉",
            'ahorro_100' => "¡Has ahorrado S/100! 💰",
            'mes_positivo' => "¡Mes con balance positivo! 📈"
        ];
        
        foreach($logros as $logro => $mensaje) {
            if($this->cumpleLogro($usuario_id, $logro)) {
                $this->guardarLogro($usuario_id, $logro, $mensaje);
            }
        }
    }
    
    private function cumpleLogro($usuario_id, $logro) {
        switch($logro) {
            case 'primer_ingreso':
                return $this->tienePrimerIngreso($usuario_id);
            case 'ahorro_100':
                return $this->tieneAhorro100($usuario_id);
            case 'mes_positivo':
                return $this->tieneMesPositivo($usuario_id);
            default:
                return false;
        }
    }
    
    private function tienePrimerIngreso($usuario_id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM ingresos WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        $result = $stmt->fetch();
        return $result['total'] == 1;
    }
    
    private function tieneAhorro100($usuario_id) {
        // Simulación - reemplaza con tu lógica real
        $stmt = $this->db->prepare("SELECT (COALESCE(SUM(i.monto), 0) - COALESCE(SUM(g.monto), 0)) as balance FROM ingresos i, gastos g WHERE i.usuario_id = ? OR g.usuario_id = ?");
        $stmt->execute([$usuario_id, $usuario_id]);
        $result = $stmt->fetch();
        return $result['balance'] >= 100;
    }
    
    private function tieneMesPositivo($usuario_id) {
        $mes_actual = date('Y-m');
        $stmt = $this->db->prepare("SELECT (COALESCE(SUM(i.monto), 0) - COALESCE(SUM(g.monto), 0)) as balance FROM ingresos i, gastos g WHERE (i.usuario_id = ? AND DATE_FORMAT(i.fecha, '%Y-%m') = ?) OR (g.usuario_id = ? AND DATE_FORMAT(g.fecha, '%Y-%m') = ?)");
        $stmt->execute([$usuario_id, $mes_actual, $usuario_id, $mes_actual]);
        $result = $stmt->fetch();
        return $result['balance'] > 0;
    }
    
    private function guardarLogro($usuario_id, $logro, $mensaje) {
        // Verificar si ya tiene el logro
        $stmt = $this->db->prepare("SELECT id FROM usuario_logros WHERE usuario_id = ? AND logro = ?");
        $stmt->execute([$usuario_id, $logro]);
        
        if($stmt->rowCount() == 0) {
            $stmt = $this->db->prepare("INSERT INTO usuario_logros (usuario_id, logro, mensaje) VALUES (?, ?, ?)");
            $stmt->execute([$usuario_id, $logro, $mensaje]);
        }
    }
    
    public function getLogrosUsuario($usuario_id) {
        $stmt = $this->db->prepare("SELECT logro, mensaje, fecha_obtenido FROM usuario_logros WHERE usuario_id = ? ORDER BY fecha_obtenido DESC");
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>