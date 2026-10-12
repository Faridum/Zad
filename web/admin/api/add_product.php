<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/json; charset=UTF-8');

// السماح للمالك أو المدير فقط
if (!in_array($_SESSION['role'], ['owner', 'admin'], true)) {
  echo json_encode(['status' => 'error', 'message' => 'غير مصرح لك بالوصول.']);
  exit;
}

try {
  $name = trim($_POST['name'] ?? '');
  $price = isset($_POST['price']) ? (float)$_POST['price'] : null;
  $sale_price = strlen($_POST['sale_price'] ?? '') ? (float)$_POST['sale_price'] : null;
  $description = trim($_POST['description'] ?? '');

  if ($name === '' || $price === null) {
    echo json_encode(['status' => 'error', 'message' => 'الاسم والسعر مطلوبان.']);
    exit;
  }

  // تحديد restaurant_id
  if ($_SESSION['role'] === 'owner') {
    $stmt = $pdo->prepare("SELECT id FROM restaurants WHERE owner_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$restaurant) {
      echo json_encode(['status' => 'error', 'message' => 'لا يوجد مطعم مرتبط بحسابك.']);
      exit;
    }
    $restaurant_id = (int)$restaurant['id'];
  } else {
    // admin: يمكن إرسال restaurant_id صراحةً
    $restaurant_id = isset($_POST['restaurant_id']) ? (int)$_POST['restaurant_id'] : 0;
    if ($restaurant_id <= 0) {
      echo json_encode(['status' => 'error', 'message' => 'يرجى تحديد معرف المطعم.']);
      exit;
    }
  }

  // رفع الصورة (اختياري)
  $photo_name = null;
  if (!empty($_FILES['photo']['name'])) {
    $upload_dir = __DIR__ . '/../uploads/products/';
    if (!is_dir($upload_dir)) {
      mkdir($upload_dir, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed, true)) {
      echo json_encode(['status' => 'error', 'message' => 'صيغة الصورة غير مدعومة.']);
      exit;
    }
    $photo_name = 'product_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo_name)) {
      echo json_encode(['status' => 'error', 'message' => 'فشل رفع الصورة.']);
      exit;
    }
  }

  // إدخال المنتج
  $stmt = $pdo->prepare("INSERT INTO products (name, description, price, sale_price, photo, restaurant_id) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->execute([$name, $description, $price, $sale_price, $photo_name, $restaurant_id]);

  // تسجيل تحديث لـ SSE
  $product_id = (int)$pdo->lastInsertId();
  $pdo->prepare("INSERT INTO updates (type, product_id) VALUES ('new_product', ?)")->execute([$product_id]);

  echo json_encode(['status' => 'success', 'message' => 'تمت إضافة المنتج بنجاح ✅']);
} catch (Exception $e) {
  echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء الإضافة: ' . $e->getMessage()]);
}
