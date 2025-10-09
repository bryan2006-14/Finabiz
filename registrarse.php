<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="icono-ic.png" sizes="96x96" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <title>Regístrate - Finabiz</title>
</head>
<body>
    <div class="background-gradient"></div>
    <div class="shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <main class="main-container">
        <a href="login.php" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            <span>Volver</span>
        </a>

        <div class="register-card">
            <div class="card-header">
                <div class="logo">
                    <img src="icono-ic.png" alt="Finabiz">
                </div>
                <h1>¡Crea tu cuenta!</h1>
                <p>Empieza a gestionar tus finanzas hoy</p>
            </div>

            <form action="./modelo/registroUsuario.php" method="post" enctype="multipart/form-data" id="registerForm">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i>
                        Nombre de usuario
                    </label>
                    <input type="text" name="username" id="username" placeholder="Ingresa tu usuario" required>
                    <span class="error-msg" id="usernameError"></span>
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Correo electrónico
                    </label>
                    <input type="email" name="email" id="email" placeholder="tu@email.com" required>
                    <span class="error-msg" id="emailError"></span>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Contraseña
                    </label>
                    <div class="password-input">
                        <input type="password" name="password" id="password" placeholder="Mínimo 6 caracteres" required>
                        <button type="button" class="toggle-pass" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <span class="error-msg" id="passwordError"></span>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="showPassword">
                    <label for="showPassword">Mostrar contraseña</label>
                </div>

                <div class="upload-area" id="uploadArea">
                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                    <p class="upload-title">Arrastra tu foto de perfil aquí</p>
                    <span class="upload-subtitle">o</span>
                    <button type="button" class="upload-btn" id="selectFileBtn">Seleccionar archivo</button>
                    <input type="file" name="file" id="input-file" hidden accept="image/*">
                    <span class="upload-hint">JPG, PNG o GIF (máx. 5MB)</span>
                </div>

                <div class="preview-area" id="previewArea"></div>

                <button type="submit" class="btn-submit">
                    <span class="btn-text">Crear cuenta</span>
                    <i class="fas fa-arrow-right btn-icon"></i>
                </button>

                <div class="divider">
                    <span>O regístrate con</span>
                </div>

                <a href="social_login.php?provider=google" class="btn-google">
                    <i class="fab fa-google"></i>
                    <span>Continuar con Google</span>
                </a>

                <div class="login-link">
                    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
                </div>
            </form>
        </div>
    </main>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
            background: #0f172a;
        }

        .background-gradient {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: -2;
        }

        .shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite ease-in-out;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            top: -150px;
            left: -100px;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            bottom: -100px;
            right: -50px;
            animation-delay: -7s;
        }

        .shape-3 {
            width: 250px;
            height: 250px;
            top: 50%;
            right: -125px;
            animation-delay: -14s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 30px) scale(0.9);
            }
        }

        .main-container {
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 1;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-5px);
        }

        .register-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
        }

        .logo img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .card-header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .card-header p {
            color: #64748b;
            font-size: 15px;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .form-group label i {
            color: #667eea;
            font-size: 16px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 500;
            color: #1e293b;
            background: #f8fafc;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-group input.error {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .password-input {
            position: relative;
        }

        .password-input input {
            padding-right: 50px;
        }

        .toggle-pass {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 8px;
            transition: color 0.3s ease;
        }

        .toggle-pass:hover {
            color: #667eea;
        }

        .error-msg {
            display: none;
            color: #ef4444;
            font-size: 13px;
            font-weight: 500;
            margin-top: 6px;
        }

        .error-msg.show {
            display: block;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #667eea;
        }

        .checkbox-group label {
            font-size: 14px;
            color: #64748b;
            cursor: pointer;
            user-select: none;
        }

        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 32px 24px;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 16px;
        }

        .upload-area:hover,
        .upload-area.dragover {
            border-color: #667eea;
            background: #eff6ff;
        }

        .upload-icon {
            font-size: 48px;
            color: #94a3b8;
            margin-bottom: 12px;
            transition: color 0.3s ease;
        }

        .upload-area:hover .upload-icon {
            color: #667eea;
        }

        .upload-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .upload-subtitle {
            display: block;
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 12px;
        }

        .upload-btn {
            padding: 10px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .upload-hint {
            display: block;
            margin-top: 12px;
            font-size: 12px;
            color: #94a3b8;
        }

        .preview-area {
            display: none;
            margin-bottom: 20px;
        }

        .preview-area.show {
            display: block;
        }

        .preview-item {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .remove-preview {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 28px;
            height: 28px;
            background: #ef4444;
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .remove-preview:hover {
            transform: scale(1.1) rotate(90deg);
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s ease;
            margin-top: 24px;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(102, 126, 234, 0.5);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-icon {
            transition: transform 0.3s ease;
        }

        .btn-submit:hover .btn-icon {
            transform: translateX(4px);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 28px 0;
            gap: 16px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            font-size: 14px;
            color: #94a3b8;
            font-weight: 600;
        }

        .btn-google {
            width: 100%;
            padding: 14px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            color: #1e293b;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-google:hover {
            background: #f8fafc;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-google i {
            font-size: 20px;
        }

        .login-link {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .login-link p {
            font-size: 14px;
            color: #64748b;
        }

        .login-link a {
            color: #667eea;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .register-card {
                padding: 32px 28px;
            }

            .card-header h1 {
                font-size: 24px;
            }

            .logo {
                width: 70px;
                height: 70px;
            }

            .logo img {
                width: 45px;
                height: 45px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 16px;
            }

            .btn-back {
                width: 100%;
                justify-content: center;
                margin-bottom: 16px;
            }

            .register-card {
                padding: 28px 20px;
                border-radius: 20px;
            }

            .card-header {
                margin-bottom: 24px;
            }

            .card-header h1 {
                font-size: 22px;
            }

            .card-header p {
                font-size: 14px;
            }

            .logo {
                width: 64px;
                height: 64px;
            }

            .logo img {
                width: 40px;
                height: 40px;
            }

            .form-group {
                margin-bottom: 16px;
            }

            .form-group input {
                padding: 13px 14px;
                font-size: 14px;
            }

            .upload-area {
                padding: 24px 16px;
            }

            .upload-icon {
                font-size: 40px;
            }

            .btn-submit {
                padding: 14px;
                font-size: 15px;
            }

            .btn-google {
                padding: 13px;
                font-size: 14px;
            }
        }

        @media (max-width: 360px) {
            .register-card {
                padding: 24px 16px;
            }

            .card-header h1 {
                font-size: 20px;
            }

            .upload-area {
                padding: 20px 12px;
            }
        }

        @media (max-height: 700px) and (orientation: landscape) {
            body {
                padding: 12px;
            }

            .btn-back {
                padding: 8px 16px;
                margin-bottom: 12px;
            }

            .register-card {
                padding: 20px;
            }

            .card-header {
                margin-bottom: 20px;
            }

            .logo {
                width: 56px;
                height: 56px;
                margin-bottom: 12px;
            }

            .card-header h1 {
                font-size: 20px;
            }

            .form-group {
                margin-bottom: 12px;
            }

            .upload-area {
                padding: 20px 16px;
            }

            .btn-submit {
                margin-top: 16px;
            }

            .divider {
                margin: 20px 0;
            }

            .login-link {
                margin-top: 16px;
                padding-top: 16px;
            }
        }
    </style>

    <script>
        // Mostrar/ocultar contraseña con checkbox
        const showPasswordCheckbox = document.getElementById('showPassword');
        const passwordInput = document.getElementById('password');
        const togglePasswordBtn = document.getElementById('togglePassword');

        showPasswordCheckbox.addEventListener('change', function() {
            if (this.checked) {
                passwordInput.type = 'text';
                togglePasswordBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                togglePasswordBtn.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });

        // Toggle con el botón
        togglePasswordBtn.addEventListener('click', function() {
            showPasswordCheckbox.checked = !showPasswordCheckbox.checked;
            showPasswordCheckbox.dispatchEvent(new Event('change'));
        });

        // Validación de email
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');

        emailInput.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('error');
                emailError.textContent = 'Ingresa un correo válido';
                emailError.classList.add('show');
            } else {
                this.classList.remove('error');
                emailError.classList.remove('show');
            }
        });

        emailInput.addEventListener('input', function() {
            if (emailError.classList.contains('show')) {
                this.classList.remove('error');
                emailError.classList.remove('show');
            }
        });

        // Validación de contraseña
        const passError = document.getElementById('passwordError');

        passwordInput.addEventListener('blur', function() {
            if (this.value && this.value.length < 6) {
                this.classList.add('error');
                passError.textContent = 'La contraseña debe tener al menos 6 caracteres';
                passError.classList.add('show');
            } else {
                this.classList.remove('error');
                passError.classList.remove('show');
            }
        });

        passwordInput.addEventListener('input', function() {
            if (passError.classList.contains('show')) {
                this.classList.remove('error');
                passError.classList.remove('show');
            }
        });

        // Upload de archivos
        const uploadArea = document.getElementById('uploadArea');
        const selectFileBtn = document.getElementById('selectFileBtn');
        const fileInput = document.getElementById('input-file');
        const previewArea = document.getElementById('previewArea');

        selectFileBtn.addEventListener('click', () => {
            fileInput.click();
        });

        uploadArea.addEventListener('click', (e) => {
            if (e.target !== selectFileBtn && !selectFileBtn.contains(e.target)) {
                fileInput.click();
            }
        });

        // Drag and drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.remove('dragover');
            });
        });

        uploadArea.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFiles(files);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                handleFiles(this.files);
            }
        });

        function handleFiles(files) {
            const file = files[0];
            
            if (!file.type.startsWith('image/')) {
                alert('Por favor selecciona solo imágenes');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('La imagen no debe superar los 5MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewArea.innerHTML = `
                    <div class="preview-item">
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="remove-preview" onclick="removeFile()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                previewArea.classList.add('show');
            };
            reader.readAsDataURL(file);
        }

        function removeFile() {
            fileInput.value = '';
            previewArea.innerHTML = '';
            previewArea.classList.remove('show');
        }

        // Validación antes de enviar
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username');
            const email = document.getElementById('email');
            const password = document.getElementById('password');

            let isValid = true;

            // Validar username
            if (username.value.trim().length < 3) {
                alert('El nombre de usuario debe tener al menos 3 caracteres');
                isValid = false;
            }

            // Validar email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                email.classList.add('error');
                emailError.textContent = 'Ingresa un correo válido';
                emailError.classList.add('show');
                isValid = false;
            }

            // Validar password
            if (password.value.length < 6) {
                password.classList.add('error');
                passError.textContent = 'La contraseña debe tener al menos 6 caracteres';
                passError.classList.add('show');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>