<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/json; charset=UTF-8');

// السماح فقط للمالك أو المدير
if (!in_array($_SESSION['role'], ['owner', 'admin'], true)) {
  echo json_encode(['status' => 'error', 'message' => 'غير مصرح لك بالوصول.']);
  exit;
}

try {
  $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'معرف المنتج غير صالح.']);
    exit;
  }

  // جلب بيانات المنتج
  $stmt = $pdo->prepare("SELECT id, restaurant_id, photo FROM products WHERE id = ?");
  $stmt->execute([$id]);
  $product = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$product) {
    echo json_encode(['status' => 'error', 'message' => 'المنتج غير موجود.']);
    exit;
  }

  // إذا كان المالك، تحقق أن المنتج يخص مطعمه فقط
  if ($_SESSION['role'] === 'owner') {
    $stmt = $pdo->prepare("SELECT id FROM restaurants WHERE owner_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$restaurant || (int)$restaurant['id'] !== (int)$product['restaurant_id']) {
      echo json_encode(['status' => 'error', 'message' => 'غير مصرح لك بحذف هذا المنتج.']);
      exit;
    }
  }

  // حذف الصورة إن وجدت
  if (!empty($product['photo'])) {
    $photo_path = __DIR__ . '/../uploads/products/' . $product['photo'];
    if (file_exists($photo_path)) {
      @unlink($photo_path);
    }
  }

  // حذف المنتج
  $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
  $stmt->execute([$id]);

  // تسجيل التحديث في جدول updates (لدعم SSE)
  $pdo->prepare("INSERT INTO updates (type, product_id) VALUES ('delete_product', ?)")->execute([$id]);

  echo json_encode(['status' => 'success', 'message' => '🗑️ تم حذف المنتج بنجاح']);
} catch (Exception $e) {
  echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()]);
}
