<?php
try {
    require_once dirname(__DIR__, 3) . '/backend/config/database.php';
    $pdo = DatabaseConfig::getConnection();
} catch (RuntimeException $e) {
    error_log('Admin database configuration error: ' . $e->getMessage());
    http_response_code(500);
    die('Database connection failed.');
}
