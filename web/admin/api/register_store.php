<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

// ✅ تأكد أن الطلب فعلاً POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['status' => 'error', 'message' => 'طريقة الإرسال غير صحيحة.']);
  exit;
}

// ✅ استلام القيم بشكل آمن
$owner_name      = $_POST['owner_name']      ?? '';
$phone           = $_POST['phone']           ?? '';
$email           = $_POST['email']           ?? '';
$password_plain  = $_POST['password']        ?? '';
$restaurant_name = $_POST['restaurant_name'] ?? '';
$address         = $_POST['address']         ?? '';
$description     = $_POST['description']     ?? '';

if (!$owner_name || !$phone || !$email || !$password_plain || !$restaurant_name || !$address) {
  echo json_encode(['status' => 'error', 'message' => 'الرجاء تعبئة جميع الحقول المطلوبة.']);
  exit;
}

// ✅ تشفير كلمة المرور
$password = password_hash($password_plain, PASSWORD_BCRYPT);

try {
  // ✅ إنشاء الجدول إن لم يكن موجودًا
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS restaurant_requests (
      id INT AUTO_INCREMENT PRIMARY KEY,
      owner_name VARCHAR(100),
      phone VARCHAR(50),
      email VARCHAR(100),
      password VARCHAR(255),
      restaurant_name VARCHAR(100),
      address VARCHAR(255),
      description TEXT,
      status ENUM('pending','approved','rejected') DEFAULT 'pending',
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ");

  // ✅ فحص التكرار بالبريد أو الجوال
  $check = $pdo->prepare("SELECT id FROM restaurant_requests WHERE email = ? OR phone = ?");
  $check->execute([$email, $phone]);
  if ($check->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'هذا البريد الإلكتروني أو رقم الجوال مستخدم مسبقًا.']);
    exit;
  }

  // ✅ إدخال البيانات
  $stmt = $pdo->prepare("
    INSERT INTO restaurant_requests (owner_name, phone, email, password, restaurant_name, address, description)
    VALUES (?, ?, ?, ?, ?, ?, ?)
  ");
  $stmt->execute([$owner_name, $phone, $email, $password, $restaurant_name, $address, $description]);

  echo json_encode(['status' => 'success', 'message' => '✅ تم إرسال طلب التسجيل بنجاح! سيتم التواصل معك قريبًا.']);
} catch (Exception $e) {
  echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء معالجة الطلب: ' . $e->getMessage()]);
}
