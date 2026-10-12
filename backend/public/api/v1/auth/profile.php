<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../../app/Repositories/UserRepository.php';
require_once __DIR__ . '/../../../../app/Helpers/Response.php';

// 1️⃣ التحقق من التوكن
$payload = AuthMiddleware::handle();

if (!isset($payload['user_id'])) {
    http_response_code(401);
    echo json_encode(Response::error("Unauthorized"));
    exit;
}

// 2️⃣ جلب المستخدم
$userRepo = new UserRepository();
$user = $userRepo->getUserById((int)$payload['user_id']);

if (!$user) {
    // المستخدم محذوف → التوكن غير صالح
    http_response_code(401);
    echo json_encode(Response::error("User not found"));
    exit;
}

// 3️⃣ إخفاء كلمة المرور
unset($user['password']);

// 4️⃣ نجاح
echo json_encode(Response::success("User profile", $user));
exit;
