<?php
try {
    $host = "dpg-d3cp1eumcj7s73dpm8sg-a.oregon-postgres.render.com";
    $port = "5432";
    $dbname = "db_finanzas_fxs9";
    $user = "db_finanzas_fxs9_user";
    $password = "MzArnjJx2t87VeEF1Cr03C35Qv3M49CU";

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $connection = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}
?>
