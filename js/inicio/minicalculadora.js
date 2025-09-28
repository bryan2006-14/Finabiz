// ===== MINI CALCULADORA =====
let miniCalcInput = "";

// Mostrar en pantalla
function updateMiniDisplay() {
    document.getElementById("miniCalcDisplay").value = miniCalcInput;
}

// Agregar números u operadores
function miniAppend(value) {
    miniCalcInput += value;
    updateMiniDisplay();
}

// Borrar todo
function miniClearCalc() {
    miniCalcInput = "";
    updateMiniDisplay();
}

// Borrar último carácter
function miniDeleteLast() {
    miniCalcInput = miniCalcInput.slice(0, -1);
    updateMiniDisplay();
}

// Calcular resultado
function miniCalculate() {
    try {
        let result = eval(miniCalcInput); // ⚠️ eval es sencillo pero inseguro si el input no está controlado
        miniCalcInput = result.toString();
        updateMiniDisplay();
    } catch (error) {
        miniCalcInput = "Error";
        updateMiniDisplay();
        setTimeout(() => {
            miniCalcInput = "";
            updateMiniDisplay();
        }, 1500);
    }
}
