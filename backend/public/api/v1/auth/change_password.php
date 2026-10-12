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

$userId = (int) $payload['user_id'];

// 2️⃣ قراءة البيانات
$currentPassword = $_POST['current_password'] ?? null;
$newPassword     = $_POST['new_password'] ?? null;
$confirmPassword = $_POST['confirm_password'] ?? null;

// 3️⃣ التحقق من القيم
if (!$currentPassword || !$newPassword || !$confirmPassword) {
    echo json_encode(Response::error("All password fields are required"));
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(Response::error("Passwords do not match"));
    exit;
}

// 4️⃣ جلب المستخدم
$userRepo = new UserRepository();
$user = $userRepo->getUserById($userId);

if (!$user) {
    http_response_code(401);
    echo json_encode(Response::error("User not found"));
    exit;
}

// 5️⃣ التحقق من كلمة المرور الحالية
if (!password_verify($currentPassword, $user['password'])) {
    echo json_encode(Response::error("Current password is incorrect"));
    exit;
}

// 6️⃣ تشفير كلمة المرور الجديدة
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

// 7️⃣ التحديث
$updated = $userRepo->updateUserPassword($userId, $hashedPassword);

if (!$updated) {
    echo json_encode(Response::error("Failed to update password"));
    exit;
}

// 8️⃣ نجاح
echo json_encode(Response::success("Password updated successfully"));
exit;

