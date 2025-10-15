<?php
session_start();
require 'modelo/conexion.php';

// Verificar que viene del flujo de Google
if (!isset($_SESSION['temp_user_id'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['temp_user_email'];
$name = $_SESSION['temp_user_name'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST["password"] ?? '';
    
    if (!empty($password)) {
        // Actualizar password del usuario
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password = :password WHERE id_usuario = :id";
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':password' => $hashed_password,
            ':id' => $_SESSION['temp_user_id']
        ]);
        
        // ✅ Completar la sesión normal
        $_SESSION['id_usuario'] = $_SESSION['temp_user_id'];
        $_SESSION['nombre'] = $name;
        $_SESSION['foto_perfil'] = ''; // Puedes obtenerla de la BD si la necesitas
        
        // Limpiar sesión temporal
        unset($_SESSION['temp_user_id']);
        unset($_SESSION['temp_user_email']);
        unset($_SESSION['temp_user_name']);
        
        // 🔁 Redirigir al inicio
        header("Location: inicio.php");
        exit;
    } else {
        $error = "Por favor ingresa una contraseña";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Contraseña</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            color: #202124;
            font-size: 1.5rem;
            font-weight: 400;
            margin-bottom: 0.5rem;
        }

        .user-email {
            color: #5f6368;
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .input-group {
            margin-bottom: 1.5rem;
        }

        .input-container {
            position: relative;
            border: 1px solid #dadce0;
            border-radius: 4px;
            padding: 1rem;
            transition: all 0.3s ease;
        }

        .input-container:focus-within {
            border-color: #1a73e8;
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
        }

        .input-container input {
            width: 100%;
            border: none;
            outline: none;
            font-size: 1rem;
            color: #202124;
        }

        .input-container input::placeholder {
            color: #5f6368;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #5f6368;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .password-toggle:hover {
            color: #1a73e8;
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .show-password {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #5f6368;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .show-password input[type="checkbox"] {
            width: 16px;
            height: 16px;
        }

        .forgot-password {
            color: #1a73e8;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .next-button {
            width: 100%;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .next-button:hover {
            background: #1669d6;
        }

        .error-message {
            color: #d93025;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .google-info {
            text-align: center;
            color: #5f6368;
            font-size: 0.9rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Te damos la bienvenida</h1>
            <div class="user-email"><?php echo htmlspecialchars($email); ?></div>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <div class="input-container">
                    <input type="password" name="password" id="password" placeholder="Ingresa tu contraseña" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <span id="toggleText">Mostrar</span>
                    </button>
                </div>
            </div>

            <div class="options">
                <label class="show-password">
                    <input type="checkbox" id="showPassword" onchange="togglePasswordVisibility()">
                    <span>Mostrar contraseña</span>
                </label>
                <a href="#" class="forgot-password">¿Olvidaste la contraseña?</a>
            </div>

            <button type="submit" class="next-button">Siguiente</button>
        </form>

        <div class="google-info">
            Te registraste con Google. Establece una contraseña para poder iniciar sesión con tu email.
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleText = document.getElementById('toggleText');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleText.textContent = 'Ocultar';
            } else {
                passwordField.type = 'password';
                toggleText.textContent = 'Mostrar';
            }
        }

        function togglePasswordVisibility() {
            const showPassword = document.getElementById('showPassword');
            const passwordField = document.getElementById('password');
            const toggleText = document.getElementById('toggleText');
            
            if (showPassword.checked) {
                passwordField.type = 'text';
                toggleText.textContent = 'Ocultar';
            } else {
                passwordField.type = 'password';
                toggleText.textContent = 'Mostrar';
            }
        }

        // Prevenir envío múltiple
        document.querySelector('form').addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            button.disabled = true;
            button.textContent = 'Configurando...';
        });
    </script>
</body>
</html>