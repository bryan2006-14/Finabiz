// modelo/habitos.php
class AnalisisHabitos {
    public function getHabitosSemana($usuario_id) {
        $dias_semana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $habitos = [];
        
        foreach($dias_semana as $dia) {
            $habitos[$dia] = [
                'gastos' => $this->getGastosDia($usuario_id, $dia),
                'ingresos' => $this->getIngresosDia($usuario_id, $dia),
                'tendencia' => $this->getTendenciaDia($usuario_id, $dia)
            ];
        }
        
        return $habitos;
    }
}