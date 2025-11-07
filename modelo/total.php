<?php
class TotalGasto {
    private $connection;
    
    public function __construct($connection) {
        $this->connection = $connection;
    }
    
    /**
     * Obtener total de gastos del mes actual
     */
    public function getTotalGastosMes($usuario_id) {
        try {
            $sql = "SELECT COALESCE(SUM(monto), 0) AS total_gastos 
                    FROM gastos 
                    WHERE usuario_id = :usuario_id 
                    AND EXTRACT(MONTH FROM fecha) = EXTRACT(MONTH FROM CURRENT_DATE)
                    AND EXTRACT(YEAR FROM fecha) = EXTRACT(YEAR FROM CURRENT_DATE)";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':usuario_id' => $usuario_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return floatval($result['total_gastos']);
        } catch (PDOException $e) {
            error_log("Error en getTotalGastosMes: " . $e->getMessage());
            return 0;
        }
    }
    
    // ... otros métodos similares con usuario_id en lugar de id_usuario
}
?>