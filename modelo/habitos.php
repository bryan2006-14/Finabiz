<?php
class AnalisisHabitos {
    private $db;
    
    public function __construct($conexion) {
        $this->db = $conexion;
    }
    
    public function getHabitosSemana($usuario_id) {
        try {
            // Obtener todos los datos en una sola consulta optimizada
            $sql = "
                SELECT 
                    CASE EXTRACT(DOW FROM fecha)
                        WHEN 1 THEN 'Lun'
                        WHEN 2 THEN 'Mar' 
                        WHEN 3 THEN 'Mié'
                        WHEN 4 THEN 'Jue'
                        WHEN 5 THEN 'Vie'
                        WHEN 6 THEN 'Sáb'
                        WHEN 0 THEN 'Dom'
                    END as dia,
                    COALESCE(SUM(CASE WHEN tabla = 'gastos' THEN monto ELSE 0 END), 0) as gastos,
                    COALESCE(SUM(CASE WHEN tabla = 'ingresos' THEN monto ELSE 0 END), 0) as ingresos
                FROM (
                    SELECT fecha, monto, 'gastos' as tabla FROM gastos WHERE id_usuario = :usuario_id1 AND fecha >= CURRENT_DATE - INTERVAL '30 days'
                    UNION ALL
                    SELECT fecha, monto, 'ingresos' as tabla FROM ingresos WHERE id_usuario = :usuario_id2 AND fecha >= CURRENT_DATE - INTERVAL '30 days'
                ) as movimientos
                GROUP BY dia
                ORDER BY 
                    CASE dia
                        WHEN 'Lun' THEN 1
                        WHEN 'Mar' THEN 2
                        WHEN 'Mié' THEN 3
                        WHEN 'Jue' THEN 4
                        WHEN 'Vie' THEN 5
                        WHEN 'Sáb' THEN 6
                        WHEN 'Dom' THEN 7
                    END
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':usuario_id1' => $usuario_id,
                ':usuario_id2' => $usuario_id
            ]);
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Inicializar estructura con todos los días
            $dias_semana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
            $habitos = [];
            
            foreach($dias_semana as $dia) {
                $habitos[$dia] = [
                    'gastos' => 0,
                    'ingresos' => 0,
                    'tendencia' => 0,
                    'tipo_gasto' => 'Sin datos'
                ];
            }
            
            // Llenar con datos reales
            foreach($resultados as $row) {
                $dia = $row['dia'];
                if (isset($habitos[$dia])) {
                    $habitos[$dia]['gastos'] = floatval($row['gastos']);
                    $habitos[$dia]['ingresos'] = floatval($row['ingresos']);
                    $habitos[$dia]['tipo_gasto'] = $this->clasificarTipoGasto(floatval($row['gastos']));
                }
            }
            
            // Calcular tendencias (de forma más eficiente)
            $this->calcularTendencias($habitos, $usuario_id);
            
            return $habitos;
            
        } catch (PDOException $e) {
            error_log("Error en getHabitosSemana: " . $e->getMessage());
            return $this->getEstructuraVacia();
        }
    }
    
    private function calcularTendencias(&$habitos, $usuario_id) {
        try {
            // Consulta optimizada para tendencias
            $sql = "
                SELECT 
                    CASE EXTRACT(DOW FROM fecha)
                        WHEN 1 THEN 'Lun'
                        WHEN 2 THEN 'Mar' 
                        WHEN 3 THEN 'Mié'
                        WHEN 4 THEN 'Jue'
                        WHEN 5 THEN 'Vie'
                        WHEN 6 THEN 'Sáb'
                        WHEN 0 THEN 'Dom'
                    END as dia,
                    COALESCE(SUM(CASE WHEN fecha >= CURRENT_DATE - INTERVAL '7 days' THEN monto ELSE 0 END), 0) as semana_actual,
                    COALESCE(SUM(CASE WHEN fecha >= CURRENT_DATE - INTERVAL '14 days' AND fecha < CURRENT_DATE - INTERVAL '7 days' THEN monto ELSE 0 END), 0) as semana_anterior
                FROM gastos 
                WHERE id_usuario = :usuario_id 
                AND fecha >= CURRENT_DATE - INTERVAL '28 days'
                GROUP BY dia
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':usuario_id' => $usuario_id]);
            $tendencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach($tendencias as $tendencia) {
                $dia = $tendencia['dia'];
                if (isset($habitos[$dia])) {
                    $semana_actual = floatval($tendencia['semana_actual']);
                    $semana_anterior = floatval($tendencia['semana_anterior']);
                    
                    if ($semana_anterior > 0) {
                        $habitos[$dia]['tendencia'] = (($semana_actual - $semana_anterior) / $semana_anterior) * 100;
                    }
                }
            }
            
        } catch (PDOException $e) {
            error_log("Error en calcularTendencias: " . $e->getMessage());
        }
    }
    
    private function clasificarTipoGasto($monto) {
        if ($monto == 0) {
            return 'Sin gastos';
        } elseif ($monto < 50) {
            return 'Gasto bajo';
        } elseif ($monto < 150) {
            return 'Gasto medio';
        } else {
            return 'Gasto alto';
        }
    }
    
    private function getEstructuraVacia() {
        $dias_semana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $estructura = [];
        
        foreach($dias_semana as $dia) {
            $estructura[$dia] = [
                'gastos' => 0,
                'ingresos' => 0,
                'tendencia' => 0,
                'tipo_gasto' => 'Sin datos'
            ];
        }
        
        return $estructura;
    }
    
    public function getAnalisisHabitos($usuario_id) {
        try {
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
            
        } catch (Exception $e) {
            error_log("Error en getAnalisisHabitos: " . $e->getMessage());
            return ["ℹ️ No hay suficientes datos para el análisis de hábitos"];
        }
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
        try {
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
            
        } catch (Exception $e) {
            error_log("Error en getResumenHabitos: " . $e->getMessage());
            return [
                'total_gastos' => 0,
                'total_ingresos' => 0,
                'balance_semanal' => 0,
                'dias_con_movimientos' => 0
            ];
        }
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

    // Método para diagnóstico
    public function verificarConexion() {
        return $this->db ? true : false;
    }
}
?>