<?php
// modelo/conexion.php

class Database {
    private string $host = 'dpg-d421923ipnbc73buvavg-a.oregon-postgres.render.com';
    private string $db_name = 'db_finabiz'; 
    private string $username = 'db_finabiz_user';
    private string $password = 'AkwKCIh1aJYNAqd687v8a6WZWgun5Axm';
    private string $port = '5432';

    private ?PDO $conn = null;

    public function getConnection(): ?PDO
    {
        try {
            // 🔥 Render REQUIERE SSL
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name};sslmode=require";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

            return $this->conn;

        } catch (PDOException $e) {
            error_log("❌ Error al conectar a PostgreSQL: " . $e->getMessage());
            return null;
        }
    }
}

// 🔹 Crear instancia global
$database = new Database();
$conn = $database->getConnection();

// 🔍 Verificar conexión
if (!$conn) {
    die("❌ Error: No se pudo conectar a la base de datos PostgreSQL");
}
?>
