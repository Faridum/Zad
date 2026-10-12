<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../../app/Controllers/AuthController.php';
require_once __DIR__ . '/../../../../app/Helpers/Response.php';

// 📥 قراءة JSON body
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

$name     = trim($data['name'] ?? '');
$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$confirm  = $data['passwordConfirmation'] ?? '';
$phone    = trim($data['phone'] ?? '');

// ✅ تحقق مبدئي
if ($name === '' || $email === '' || $password === '' || $confirm === '') {
    echo json_encode(Response::error("Please fill all required fields"));
    exit;
}

// 🔐 تنفيذ التسجيل
$auth = new AuthController();
$result = $auth->register(
    $name,
    $email,
    $password,
    $confirm,
    $phone
);

// 📤 الرد
echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit;

