<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/registroExito.css">
    <title>Registro Exitoso</title>
    <!-- Redirección fallback por si JS está deshabilitado -->
    <meta http-equiv="refresh" content="3;url=../login.php">
</head>
<body>
    <h1>¡Registro exitoso!</h1>
    <p>El usuario se ha registrado correctamente.</p>
    <p>Redireccionando al inicio de sesión en unos segundos...</p>
    <script>
        setTimeout(function () {
            window.location.href = "../login.php";
        }, 2500);
    </script>
</body>
</html>
