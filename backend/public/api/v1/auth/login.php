<?php
file_put_contents(__DIR__ . '/auth_debug.log', "LOGIN HIT\n", FILE_APPEND);

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../../app/Controllers/AuthController.php';
require_once __DIR__ . '/../../../../app/Helpers/Response.php';

// 📥 قراءة JSON body
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// ✅ التحقق
if ($email === '' || $password === '') {
    echo json_encode(Response::error("Please fill all fields"));
    exit;
}

// 🔐 تنفيذ تسجيل الدخول
$auth = new AuthController();
$result = $auth->login($email, $password);

echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit;

