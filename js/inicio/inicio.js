// Mini calculadora en inicio.php
document.addEventListener("DOMContentLoaded", function () {
    const display = document.getElementById("miniCalcDisplay");

    // Añadir valor al display
    window.appendMiniValue = function (value) {
        display.value += value;
    };

    // Limpiar pantalla
    window.clearMiniDisplay = function () {
        display.value = "";
    };

    // Borrar último carácter
    window.backspaceMini = function () {
        display.value = display.value.slice(0, -1);
    };

    // Calcular expresión
    window.calculateMiniResult = function () {
        try {
            display.value = eval(display.value);
        } catch (e) {
            display.value = "Error";
        }
    };
});
