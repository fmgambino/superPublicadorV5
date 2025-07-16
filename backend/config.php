<?php
// /autoposteg/backend/config.php

// Detectar entorno según el host
$hostName = $_SERVER['HTTP_HOST'] ?? '';

if (strpos($hostName, 'localhost') !== false || strpos($hostName, '127.0.0.1') !== false) {
    // Entorno local (XAMPP)
    $dbHost = '127.0.0.1';
    $dbName = 'autopost';
    $dbUser = 'root';
    $dbPass = '';
} else {
    // Producción (Hostinger)
    $dbHost = 'localhost';
    $dbName = 'u197809344_pwa';
    $dbUser = 'u197809344_spv5';
    $dbPass = 'jamboree0342$$';
}

try {
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // En producción podrías registrar el error en un log
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
