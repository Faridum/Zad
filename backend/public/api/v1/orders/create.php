<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../../app/Controllers/OrderController.php';
require_once __DIR__ . '/../../../../app/Helpers/Response.php';

try {
    // 🔐 Auth
    $payload = AuthMiddleware::handle();
    $customerId = (int)$payload['user_id'];

    // 📥 Read JSON body
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        echo json_encode(Response::error("Invalid JSON body"));
        exit;
    }

    // ✅ Validation
    if (
        empty($data['restaurant_id']) ||
        empty($data['payment_method']) ||
        empty($data['address']) ||
        empty($data['items']) ||
        !is_array($data['items'])
    ) {
        echo json_encode(Response::error("Missing required fields"));
        exit;
    }

    $controller = new OrderController();
    $result = $controller->create($data, $customerId);

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(Response::error(
        "Server error",
        ["message" => $e->getMessage()]
    ));
    exit;
}

