<?php
/**
 * Secure Database Connection with Environment Variables
 * Creates database and table if they don't exist.
 * Supports dynamic table alteration.
 */

function getDbConnection(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $envFile = __DIR__ . '/../.env';
    if (!file_exists($envFile)) {
        error_log('Missing .env file. Copy .env.example to .env and set credentials.');
        die('Configuration error.');
    }

    $env = parse_ini_file($envFile);
    $required = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_CHARSET'];
    foreach ($required as $key) {
        if (!isset($env[$key])) {
            error_log("Missing config: $key");
            die('Configuration error.');
        }
    }

    try {
        $dsn = sprintf('mysql:host=%s;charset=%s', $env['DB_HOST'], $env['DB_CHARSET']);
        $pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASS']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Create database if not exists
        $pdo->exec(sprintf(
            "CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET %s COLLATE %s",
            $env['DB_NAME'],
            $env['DB_CHARSET'],
            $env['DB_CHARSET'] . '_unicode_ci'
        ));
        $pdo->exec(sprintf("USE `%s`", $env['DB_NAME']));

        // Create table if not exists
        $createSQL = "
            CREATE TABLE IF NOT EXISTS employees (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                gender ENUM('Male','Female','Other') NOT NULL,
                hobbies SET('Reading','Gaming','Sports','Music','Travel') NOT NULL,
                salary DECIMAL(10,2) NOT NULL,
                department VARCHAR(50) DEFAULT 'General',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ";
        $pdo->exec($createSQL);

        // Ensure department column exists (dynamic alteration)
        try {
            $pdo->exec("ALTER TABLE employees ADD COLUMN department VARCHAR(50) DEFAULT 'General'");
        } catch (PDOException $e) {
            // Ignore duplicate column error (1060)
            if ($e->errorInfo[1] != 1060) error_log('Alter error: ' . $e->getMessage());
        }

        return $pdo;
    } catch (PDOException $e) {
        error_log('DB Connection failed: ' . $e->getMessage());
        die('Database connection error.');
    }
}