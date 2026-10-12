<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/json; charset=UTF-8');

try {
  // ✅ السماح فقط للمالك
  if ($_SESSION['role'] !== 'owner') {
    echo json_encode(['status' => 'error', 'message' => 'غير مصرح لك بحذف المنتجات.']);
    exit;
  }

  $owner_id = $_SESSION['user_id'];

  // ✅ جلب المطعم المرتبط بالمالك
  $stmt = $pdo->prepare("SELECT id FROM restaurants WHERE owner_id = ?");
  $stmt->execute([$owner_id]);
  $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$restaurant) {
    echo json_encode(['status' => 'error', 'message' => 'لا يوجد مطعم مرتبط بحسابك.']);
    exit;
  }

  $restaurant_id = $restaurant['id'];

  // ✅ استقبال الطلب
  if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'معرّف المنتج غير موجود.']);
    exit;
  }

  $id = (int)$_POST['id'];

  // ✅ تحقق أن المنتج فعلاً تابع لمطعمه
  $stmt = $pdo->prepare("SELECT photo FROM products WHERE id = ? AND restaurant_id = ?");
  $stmt->execute([$id, $restaurant_id]);
  $product = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$product) {
    echo json_encode(['status' => 'error', 'message' => 'المنتج غير موجود أو لا يتبع مطعمك.']);
    exit;
  }

  // ✅ حذف الصورة من السيرفر (اختياري)
  if (!empty($product['photo'])) {
    $filePath = __DIR__ . '/../uploads/products/' . $product['photo'];
    if (file_exists($filePath)) {
      @unlink($filePath);
    }
  }

  // ✅ حذف المنتج من قاعدة البيانات
  $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND restaurant_id = ?");
  $stmt->execute([$id, $restaurant_id]);

  // ✅ تسجيل العملية في جدول updates
  $pdo->prepare("INSERT INTO updates (type, product_id) VALUES ('delete_product', ?)")->execute([$id]);

  echo json_encode(['status' => 'success', 'message' => 'تم حذف المنتج بنجاح 🗑️']);
  exit;
} catch (Exception $e) {
  echo json_encode(['status' => 'error', 'message' => 'حدث خطأ في الخادم: ' . $e->getMessage()]);
  exit;
}
