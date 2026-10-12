<?php
/**
 * ------------------------------------------------------------------------
 *  bootstrap.php
 * ------------------------------------------------------------------------
 *  هذا الملف يتم استدعاؤه من جميع ملفات API.
 *  وظيفته:
 *   - تحميل إعدادات المشروع
 *   - تحميل ملفات الـ core
 *   - تحميل الـ Models و Repositories و Controllers تلقائياً
 *   - تجهيز الاتصال بقاعدة البيانات
 * ------------------------------------------------------------------------
 */

// السماح باستخدام الجلسات عند الحاجة (يمكن إبقاؤها معطلة الآن)
// session_start();

try {
    require_once __DIR__ . '/../config/database.php';
    $db = DatabaseConfig::getConnection();
    $jwtConfig = require __DIR__ . '/../config/jwt.php';
} catch (RuntimeException $e) {
    error_log('Application configuration error: ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode([
        "status" => "error",
        "message" => "Application configuration error"
    ]));
}

/**
 * -----------------------------------------------------------
 * تحميل الـ core classes
 * -----------------------------------------------------------
 */
require_once __DIR__ . '/JWT.php';
require_once __DIR__ . '/response.php';

/**
 * -----------------------------------------------------------
 * Auto Loader بسيط لتحميل الـ Models, Repositories, Controllers
 * -----------------------------------------------------------
 */
spl_autoload_register(function ($className) {

    $paths = [
        __DIR__ . '/../app/Models/' . $className . '.php',
        __DIR__ . '/../app/Repositories/' . $className . '.php',
        __DIR__ . '/../app/Controllers/' . $className . '.php',
        __DIR__ . '/../app/Middleware/' . $className . '.php',
        __DIR__ . '/../app/Helpers/' . $className . '.php'
    ];

    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

/**
 * -----------------------------------------------------------
 * جعل متغيرات $db و $jwtConfig جاهزة للاستخدام في كل API
 * -----------------------------------------------------------
 */
$GLOBALS['db'] = $db;
$GLOBALS['jwt_settings'] = $jwtConfig;
