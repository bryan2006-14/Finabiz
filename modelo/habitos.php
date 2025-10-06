<?php
class AnalisisHabitos {
    private $db;
    
    public function __construct($conexion) {
        $this->db = $conexion;
    }
    
    public function getHabitosSemana($usuario_id) {
        $dias_semana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $habitos = [];
        
        foreach($dias_semana as $dia) {
            $habitos[$dia] = [
                'gastos' => $this->getGastosDia($usuario_id, $dia),
                'ingresos' => $this->getIngresosDia($usuario_id, $dia),
                'tendencia' => $this->getTendenciaDia($usuario_id, $dia),
                'tipo_gasto' => $this->getTipoGastoDia($usuario_id, $dia)
            ];
        }
        
        return $habitos;
    }
    
    public function getAnalisisHabitos($usuario_id) {
        $habitos = $this->getHabitosSemana($usuario_id);
        $analisis = [];
        
        // Análisis de día de mayor gasto
        $dia_mayor_gasto = $this->getDiaMayorGasto($habitos);
        if ($dia_mayor_gasto && $habitos[$dia_mayor_gasto]['gastos'] > 0) {
            $analisis[] = "💰 Tu día de mayor gasto suele ser el <strong>$dia_mayor_gasto</strong>";
        }
        
        // Análisis de consistencia en ingresos
        $consistencia_ingresos = $this->getConsistenciaIngresos($habitos);
        if ($consistencia_ingresos > 70) {
            $analisis[] = "📈 Tienes ingresos consistentes durante la semana (" . number_format($consistencia_ingresos, 1) . "% de consistencia)";
        } else if ($consistencia_ingresos > 0) {
            $analisis[] = "📊 Tus ingresos varían significativamente durante la semana";
        }
        
        // Análisis de fin de semana vs semana
        $comparacion_finde = $this->compararFindeSemana($habitos);
        if ($comparacion_finde['diferencia'] > 20) {
            $analisis[] = "🎯 Gastas " . number_format($comparacion_finde['diferencia'], 1) . "% más los fines de semana";
        } else if ($comparacion_finde['diferencia'] < -20) {
            $analisis[] = "🏢 Gastas " . number_format(abs($comparacion_finde['diferencia']), 1) . "% más entre semana";
        }
        
        // Patrón de ahorro semanal
        $patron_ahorro = $this->getPatronAhorro($habitos);
        if ($patron_ahorro['positivo']) {
            $analisis[] = "💪 Mejor día para ahorrar: <strong>{$patron_ahorro['mejor_dia']}</strong>";
        }
        
        // Análisis de días activos
        $dias_activos = $this->getDiasConMovimientos($habitos);
        if ($dias_activos > 0) {
            $analisis[] = "📅 Eres activo <strong>$dias_activos/7</strong> días a la semana";
        }

        return $analisis;
    }
    
    private function getGastosDia($usuario_id, $dia) {
        $numero_dia = $this->getNumeroDia($dia);
        $sql = "SELECT COALESCE(SUM(monto), 0) as total 
                FROM gastos 
                WHERE id_usuario = :usuario_id AND EXTRACT(DOW FROM fecha) = :numero_dia
                AND fecha >= CURRENT_DATE - INTERVAL '30 days'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':numero_dia' => $numero_dia
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return floatval($row['total']);
        }
        return 0;
    }
    
    private function getIngresosDia($usuario_id, $dia) {
        $numero_dia = $this->getNumeroDia($dia);
        $sql = "SELECT COALESCE(SUM(monto), 0) as total 
                FROM ingresos 
                WHERE id_usuario = :usuario_id AND EXTRACT(DOW FROM fecha) = :numero_dia
                AND fecha >= CURRENT_DATE - INTERVAL '30 days'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':numero_dia' => $numero_dia
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return floatval($row['total']);
        }
        return 0;
    }
    
    private function getTendenciaDia($usuario_id, $dia) {
        $numero_dia = $this->getNumeroDia($dia);
        
        // Gastos de las últimas 4 semanas para este día
        $sql = "SELECT 
                    COALESCE(SUM(CASE WHEN fecha >= CURRENT_DATE - INTERVAL '7 days' THEN monto ELSE 0 END), 0) as semana_actual,
                    COALESCE(SUM(CASE WHEN fecha >= CURRENT_DATE - INTERVAL '14 days' AND fecha < CURRENT_DATE - INTERVAL '7 days' THEN monto ELSE 0 END), 0) as semana_anterior
                FROM gastos 
                WHERE id_usuario = :usuario_id AND EXTRACT(DOW FROM fecha) = :numero_dia
                AND fecha >= CURRENT_DATE - INTERVAL '28 days'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':numero_dia' => $numero_dia
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $semana_actual = floatval($row['semana_actual']);
            $semana_anterior = floatval($row['semana_anterior']);
            
            if ($semana_anterior > 0) {
                $tendencia = (($semana_actual - $semana_anterior) / $semana_anterior) * 100;
                return $tendencia;
            }
        }
        
        return 0;
    }
    
    private function getTipoGastoDia($usuario_id, $dia) {
        $numero_dia = $this->getNumeroDia($dia);
        
        // Primero, verifiquemos si hay gastos en este día
        $sql_check = "SELECT COALESCE(SUM(monto), 0) as total 
                      FROM gastos 
                      WHERE id_usuario = :usuario_id AND EXTRACT(DOW FROM fecha) = :numero_dia
                      AND fecha >= CURRENT_DATE - INTERVAL '30 days'";
        
        $stmt_check = $this->db->prepare($sql_check);
        $stmt_check->execute([
            ':usuario_id' => $usuario_id,
            ':numero_dia' => $numero_dia
        ]);
        $row = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $total_gastos = floatval($row['total']);
            
            // Clasificar el tipo de gasto basado en el monto
            if ($total_gastos == 0) {
                return 'Sin gastos';
            } elseif ($total_gastos < 50) {
                return 'Gasto bajo';
            } elseif ($total_gastos < 150) {
                return 'Gasto medio';
            } else {
                return 'Gasto alto';
            }
        }
        
        return 'Sin datos';
    }
    
    private function getNumeroDia($dia) {
        $dias_map = [
            'Lun' => 1,  // PostgreSQL: 1=Lunes, 0=Domingo
            'Mar' => 2,
            'Mié' => 3, 
            'Jue' => 4,
            'Vie' => 5,
            'Sáb' => 6,
            'Dom' => 0
        ];
        return $dias_map[$dia] ?? 0;
    }
    
    private function getDiaMayorGasto($habitos) {
        $mayor_gasto = 0;
        $dia_mayor = '';
        
        foreach($habitos as $dia => $datos) {
            if ($datos['gastos'] > $mayor_gasto) {
                $mayor_gasto = $datos['gastos'];
                $dia_mayor = $dia;
            }
        }
        
        return $mayor_gasto > 0 ? $dia_mayor : '';
    }
    
    private function getConsistenciaIngresos($habitos) {
        $ingresos = [];
        foreach($habitos as $datos) {
            if ($datos['ingresos'] > 0) {
                $ingresos[] = $datos['ingresos'];
            }
        }
        
        if (count($ingresos) < 2) return 0;
        
        $promedio = array_sum($ingresos) / count($ingresos);
        $desviaciones = [];
        
        foreach($ingresos as $ingreso) {
            if ($promedio > 0) {
                $desviaciones[] = abs($ingreso - $promedio) / $promedio * 100;
            }
        }
        
        if (empty($desviaciones)) return 0;
        
        $desviacion_promedio = array_sum($desviaciones) / count($desviaciones);
        return max(0, 100 - $desviacion_promedio);
    }
    
    private function compararFindeSemana($habitos) {
        $gasto_semana = 0;
        $gasto_finde = 0;
        $dias_semana = 0;
        $dias_finde = 0;
        
        foreach($habitos as $dia => $datos) {
            if (in_array($dia, ['Sáb', 'Dom'])) {
                $gasto_finde += $datos['gastos'];
                $dias_finde++;
            } else {
                $gasto_semana += $datos['gastos'];
                $dias_semana++;
            }
        }
        
        $promedio_semana = $dias_semana > 0 ? $gasto_semana / $dias_semana : 0;
        $promedio_finde = $dias_finde > 0 ? $gasto_finde / $dias_finde : 0;
        
        if ($promedio_semana > 0) {
            $diferencia = (($promedio_finde - $promedio_semana) / $promedio_semana) * 100;
        } else {
            $diferencia = $promedio_finde > 0 ? 100 : 0;
        }
        
        return [
            'semana' => $promedio_semana,
            'finde' => $promedio_finde,
            'diferencia' => $diferencia
        ];
    }
    
    private function getPatronAhorro($habitos) {
        $mejor_balance = -999999;
        $mejor_dia = '';
        
        foreach($habitos as $dia => $datos) {
            $balance = $datos['ingresos'] - $datos['gastos'];
            if ($balance > $mejor_balance) {
                $mejor_balance = $balance;
                $mejor_dia = $dia;
            }
        }
        
        return [
            'positivo' => $mejor_balance > 0,
            'mejor_dia' => $mejor_dia,
            'balance' => $mejor_balance
        ];
    }
    
    public function getResumenHabitos($usuario_id) {
        $habitos = $this->getHabitosSemana($usuario_id);
        
        $total_gastos = 0;
        $total_ingresos = 0;
        
        foreach($habitos as $datos) {
            $total_gastos += $datos['gastos'];
            $total_ingresos += $datos['ingresos'];
        }
        
        return [
            'total_gastos' => $total_gastos,
            'total_ingresos' => $total_ingresos,
            'balance_semanal' => $total_ingresos - $total_gastos,
            'dias_con_movimientos' => $this->getDiasConMovimientos($habitos)
        ];
    }
    
    private function getDiasConMovimientos($habitos) {
        $dias_con_movimientos = 0;
        foreach($habitos as $datos) {
            if ($datos['gastos'] > 0 || $datos['ingresos'] > 0) {
                $dias_con_movimientos++;
            }
        }
        return $dias_con_movimientos;
    }

    // Método para diagnóstico - puedes removerlo después
    public function verificarConexion() {
        return $this->db ? true : false;
    }
}
?>