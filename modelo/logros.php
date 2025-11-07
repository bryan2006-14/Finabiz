<?php
class SistemaLogros {
    private $connection;
    
    public function __construct($connection) {
        $this->connection = $connection;
    }
    
    /**
     * Obtener los logros de un usuario
     */
    public function getLogrosUsuario($usuario_id, $limite = 5) {
        try {
            $sql = "SELECT * FROM logros WHERE id_usuario = :usuario_id ORDER BY fecha_obtenido DESC LIMIT :limite";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getLogrosUsuario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Verificar y asignar logros automáticamente
     */
    public function verificarLogros($usuario_id) {
        try {
            $this->verificarPrimerGasto($usuario_id);
            $this->verificarPrimerIngreso($usuario_id);
            $this->verificarMetaCompletada($usuario_id);
            $this->verificarConsistenciaSemanal($usuario_id);
            return true;
        } catch (Exception $e) {
            error_log("Error en verificarLogros: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Marcar logros como vistos
     */
    public function marcarLogrosComoVistos($usuario_id) {
        try {
            $sql = "UPDATE logros SET visto = true WHERE id_usuario = :usuario_id AND visto = false";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':usuario_id' => $usuario_id]);
            return true;
        } catch (PDOException $e) {
            error_log("Error en marcarLogrosComoVistos: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar logro: Primer Gasto
     */
    private function verificarPrimerGasto($usuario_id) {
        try {
            // Verificar si ya tiene este logro
            $sql_check = "SELECT COUNT(*) as count FROM logros WHERE id_usuario = :usuario_id AND tipo_logro = 'primer_gasto'";
            $stmt_check = $this->connection->prepare($sql_check);
            $stmt_check->execute([':usuario_id' => $usuario_id]);
            $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] == 0) {
                // Verificar si tiene gastos
                $sql_gastos = "SELECT COUNT(*) as count FROM gastos WHERE usuario_id = :usuario_id";
                $stmt_gastos = $this->connection->prepare($sql_gastos);
                $stmt_gastos->execute([':usuario_id' => $usuario_id]);
                $gastos = $stmt_gastos->fetch(PDO::FETCH_ASSOC);
                
                if ($gastos['count'] > 0) {
                    // Asignar logro
                    $this->asignarLogro($usuario_id, 'primer_gasto', '¡Felicidades! Realizaste tu primer gasto', '🛒');
                }
            }
        } catch (PDOException $e) {
            error_log("Error en verificarPrimerGasto: " . $e->getMessage());
        }
    }
    
    /**
     * Verificar logro: Primer Ingreso
     */
    private function verificarPrimerIngreso($usuario_id) {
        try {
            // Verificar si ya tiene este logro
            $sql_check = "SELECT COUNT(*) as count FROM logros WHERE id_usuario = :usuario_id AND tipo_logro = 'primer_ingreso'";
            $stmt_check = $this->connection->prepare($sql_check);
            $stmt_check->execute([':usuario_id' => $usuario_id]);
            $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] == 0) {
                // Verificar si tiene ingresos
                $sql_ingresos = "SELECT COUNT(*) as count FROM ingresos WHERE usuario_id = :usuario_id";
                $stmt_ingresos = $this->connection->prepare($sql_ingresos);
                $stmt_ingresos->execute([':usuario_id' => $usuario_id]);
                $ingresos = $stmt_ingresos->fetch(PDO::FETCH_ASSOC);
                
                if ($ingresos['count'] > 0) {
                    // Asignar logro
                    $this->asignarLogro($usuario_id, 'primer_ingreso', '¡Excelente! Registraste tu primer ingreso', '💰');
                }
            }
        } catch (PDOException $e) {
            error_log("Error en verificarPrimerIngreso: " . $e->getMessage());
        }
    }
    
    /**
     * Verificar logro: Meta Completada
     */
    private function verificarMetaCompletada($usuario_id) {
        try {
            // Buscar metas recién completadas
            $sql_metas = "SELECT id_meta, nombre_meta FROM metas WHERE id_usuario = :usuario_id AND estado = 'completada' AND fecha_completado >= CURRENT_DATE - INTERVAL '1 day'";
            $stmt_metas = $this->connection->prepare($sql_metas);
            $stmt_metas->execute([':usuario_id' => $usuario_id]);
            $metas_completadas = $stmt_metas->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($metas_completadas as $meta) {
                // Verificar si ya tiene logro para esta meta
                $sql_check = "SELECT COUNT(*) as count FROM logros WHERE id_usuario = :usuario_id AND tipo_logro = 'meta_completada' AND mensaje LIKE :mensaje";
                $stmt_check = $this->connection->prepare($sql_check);
                $stmt_check->execute([
                    ':usuario_id' => $usuario_id,
                    ':mensaje' => '%' . $meta['nombre_meta'] . '%'
                ]);
                $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
                
                if ($result['count'] == 0) {
                    $this->asignarLogro($usuario_id, 'meta_completada', "¡Felicidades! Completaste la meta: " . $meta['nombre_meta'], '🎯');
                }
            }
        } catch (PDOException $e) {
            error_log("Error en verificarMetaCompletada: " . $e->getMessage());
        }
    }
    
    /**
     * Verificar logro: Consistencia Semanal
     */
    private function verificarConsistenciaSemanal($usuario_id) {
        try {
            // Verificar registros en los últimos 7 días
            $sql = "SELECT COUNT(DISTINCT DATE(fecha)) as dias_con_registros 
                    FROM (
                        SELECT fecha FROM gastos WHERE usuario_id = :usuario_id AND fecha >= CURRENT_DATE - INTERVAL '7 days'
                        UNION ALL
                        SELECT fecha FROM ingresos WHERE usuario_id = :usuario_id AND fecha >= CURRENT_DATE - INTERVAL '7 days'
                    ) AS registros";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':usuario_id' => $usuario_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['dias_con_registros'] >= 7) {
                // Verificar si ya tiene este logro
                $sql_check = "SELECT COUNT(*) as count FROM logros WHERE id_usuario = :usuario_id AND tipo_logro = 'consistencia_semanal'";
                $stmt_check = $this->connection->prepare($sql_check);
                $stmt_check->execute([':usuario_id' => $usuario_id]);
                $logro_existente = $stmt_check->fetch(PDO::FETCH_ASSOC);
                
                if ($logro_existente['count'] == 0) {
                    $this->asignarLogro($usuario_id, 'consistencia_semanal', '¡Increíble! Mantuviste consistencia por 7 días seguidos', '📊');
                }
            }
        } catch (PDOException $e) {
            error_log("Error en verificarConsistenciaSemanal: " . $e->getMessage());
        }
    }
    
    /**
     * Método auxiliar para asignar logros
     */
    private function asignarLogro($usuario_id, $tipo_logro, $mensaje, $icono) {
        try {
            $sql = "INSERT INTO logros (id_usuario, tipo_logro, mensaje, icono, fecha_obtenido) 
                    VALUES (:usuario_id, :tipo_logro, :mensaje, :icono, NOW())";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':usuario_id' => $usuario_id,
                ':tipo_logro' => $tipo_logro,
                ':mensaje' => $mensaje,
                ':icono' => $icono
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error en asignarLogro: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener estadísticas de logros
     */
    public function getEstadisticasLogros($usuario_id) {
        try {
            $sql = "SELECT 
                    COUNT(*) as total_logros,
                    COUNT(CASE WHEN visto = false THEN 1 END) as logros_nuevos,
                    MAX(fecha_obtenido) as ultimo_logro
                    FROM logros 
                    WHERE id_usuario = :usuario_id";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':usuario_id' => $usuario_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getEstadisticasLogros: " . $e->getMessage());
            return ['total_logros' => 0, 'logros_nuevos' => 0, 'ultimo_logro' => null];
        }
    }
    
    /**
     * Obtener todos los tipos de logros disponibles
     */
    public function getTiposLogros() {
        try {
            $sql = "SELECT * FROM tipos_logros ORDER BY id_tipo";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getTiposLogros: " . $e->getMessage());
            return [];
        }
    }
}
?>