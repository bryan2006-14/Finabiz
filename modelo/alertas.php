// modelo/alertas.php
class AlertasInteligentes {
    public function generarAlertas($usuario_id) {
        $alertas = [];
        
        // Alerta simple: Gasto mayor al promedio
        $gasto_promedio = $this->getGastoPromedio($usuario_id);
        $gasto_actual = $this->getGastoMesActual($usuario_id);
        
        if($gasto_actual > ($gasto_promedio * 1.3)) {
            $alertas[] = "⚠️ Tus gastos este mes son 30% mayores al promedio";
        }
        
        // Alerta: Meta de ahorro cercana
        if($this->cercaDeMetaAhorro($usuario_id)) {
            $alertas[] = "🎯 ¡Estás a 10% de tu meta de ahorro!";
        }
        
        return $alertas;
    }
}