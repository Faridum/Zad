<?php

header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../../app/Helpers/Response.php';
require_once __DIR__ . '/../../../../app/Controllers/HomeController.php';

try {
    $controller = new HomeController();

    $result = $controller->index();

    echo json_encode(
        $result,
        JSON_UNESCAPED_UNICODE
    );

} catch (Throwable $e) {
    // مؤقتًا أثناء التطوير فقط
    echo json_encode(
        Response::error(
            $e->getMessage(),
            [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ),
        JSON_UNESCAPED_UNICODE
    );
}
