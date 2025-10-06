// modelo/comparativa.php
class ComparativaAnonima {
    public function getStatsComparativa($usuario_id) {
        return [
            'tu_ahorro' => $this->getTuAhorro($usuario_id),
            'promedio_ahorro' => $this->getPromedioAhorro(),
            'tu_gasto_alimentacion' => $this->getGastoCategoria($usuario_id, 'comida'),
            'promedio_gasto_alimentacion' => $this->getPromedioCategoria('comida')
        ];
    }
}