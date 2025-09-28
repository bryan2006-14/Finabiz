// ================== CALCULADORA BÁSICA ==================
let display = document.getElementById("display");
let currentInput = "0";

function updateDisplay() {
    display.value = currentInput;
}

function appendNumber(num) {
    if (currentInput === "0" || currentInput === "Error") {
        currentInput = num;
    } else {
        currentInput += num;
    }
    updateDisplay();
}

function appendOperation(op) {
    if (/[+\-*/.]$/.test(currentInput)) return; // evita duplicar operadores
    currentInput += op;
    updateDisplay();
}

function clearDisplay() {
    currentInput = "0";
    updateDisplay();
}

function clearEntry() {
    currentInput = currentInput.slice(0, -1);
    if (currentInput === "") currentInput = "0";
    updateDisplay();
}

function deleteLast() {
    currentInput = currentInput.slice(0, -1);
    if (currentInput === "") currentInput = "0";
    updateDisplay();
}

function calculate() {
    try {
        currentInput = eval(currentInput).toString();
        updateDisplay();
    } catch (e) {
        currentInput = "Error";
        updateDisplay();
    }
}

// ================== CALCULADORA DE PRÉSTAMO ==================
function calculateLoan() {
    const amount = parseFloat(document.getElementById("loanAmount").value);
    const rate = parseFloat(document.getElementById("loanRate").value) / 100 / 12;
    const term = parseInt(document.getElementById("loanTerm").value) * 12;

    if (isNaN(amount) || isNaN(rate) || isNaN(term)) {
        document.getElementById("loanResult").innerHTML = "<p>⚠️ Por favor ingrese valores válidos.</p>";
        return;
    }

    const monthlyPayment = (amount * rate) / (1 - Math.pow(1 + rate, -term));
    const totalPayment = monthlyPayment * term;
    const totalInterest = totalPayment - amount;

    document.getElementById("loanResult").innerHTML = `
        <p><strong>Pago mensual:</strong> S/ ${monthlyPayment.toFixed(2)}</p>
        <p><strong>Pago total:</strong> S/ ${totalPayment.toFixed(2)}</p>
        <p><strong>Interés total:</strong> S/ ${totalInterest.toFixed(2)}</p>
    `;
}

// ================== CALCULADORA DE INVERSIÓN ==================
function calculateInvestment() {
    const initial = parseFloat(document.getElementById("initialAmount").value);
    const monthly = parseFloat(document.getElementById("monthlyContribution").value);
    const rate = parseFloat(document.getElementById("interestRate").value) / 100 / 12;
    const years = parseInt(document.getElementById("investmentTerm").value);
    const months = years * 12;

    if (isNaN(initial) || isNaN(monthly) || isNaN(rate) || isNaN(years)) {
        document.getElementById("investmentResult").innerHTML = "<p>⚠️ Por favor ingrese valores válidos.</p>";
        return;
    }

    let futureValue = initial * Math.pow(1 + rate, months);
    for (let i = 1; i <= months; i++) {
        futureValue += monthly * Math.pow(1 + rate, months - i);
    }

    document.getElementById("investmentResult").innerHTML = `
        <p><strong>Valor futuro estimado:</strong> S/ ${futureValue.toFixed(2)}</p>
    `;
}

// ================== TABS ==================
function switchTab(tabId) {
    document.querySelectorAll(".tab-content").forEach(tab => tab.classList.remove("active"));
    document.querySelectorAll(".tab-button").forEach(btn => btn.classList.remove("active"));

    document.getElementById(tabId).classList.add("active");
    document.querySelector(`.tab-button[onclick="switchTab('${tabId}')"]`).classList.add("active");
}
