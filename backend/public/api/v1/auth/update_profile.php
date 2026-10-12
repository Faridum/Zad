<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../../app/Repositories/UserRepository.php';
require_once __DIR__ . '/../../../../app/Helpers/Response.php';
require_once __DIR__ . '/../../../../app/Helpers/Filter.php';

// 1️⃣ التحقق من التوكن
$payload = AuthMiddleware::handle();

if (!isset($payload['user_id'])) {
    http_response_code(401);
    echo json_encode(Response::error("Unauthorized"));
    exit;
}

$userId = (int)$payload['user_id'];

// 2️⃣ قراءة البيانات
$name  = Filter::string($_POST['name'] ?? null);
$email = Filter::email($_POST['email'] ?? null);
$phone = Filter::phone($_POST['phone'] ?? null);

if (!$name || !$email || !$phone) {
    echo json_encode(Response::error("Invalid input data"));
    exit;
}

// 3️⃣ تحديث المستخدم
$userRepo = new UserRepository();
$updated = $userRepo->updateUserProfile($userId, $name, $email, $phone);

if (!$updated) {
    echo json_encode(Response::error("Failed to update profile"));
    exit;
}

// 4️⃣ إرجاع البيانات الجديدة
$user = $userRepo->getUserById($userId);
unset($user['password']);

echo json_encode(Response::success("Profile updated successfully", $user));
exit;

