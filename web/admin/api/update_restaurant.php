<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json; charset=UTF-8');

// ✅ السماح فقط للمدير أو المالك
if (!in_array($_SESSION['role'], ['admin', 'owner'], true)) {
  echo json_encode(['status' => 'error', 'message' => 'غير مصرح لك بالوصول.']);
  exit;
}

try {
  $id        = (int)($_POST['id'] ?? 0);
  $name      = trim($_POST['name'] ?? '');
  $owner_id  = (int)($_POST['owner_id'] ?? 0);
  $phone     = trim($_POST['phone'] ?? '');
  $address   = trim($_POST['address'] ?? '');
  $status    = $_POST['status'] ?? 'active';
  $hours     = trim($_POST['working_hours'] ?? '');

  if (empty($id) || empty($name)) {
    echo json_encode(['status' => 'error', 'message' => 'بيانات ناقصة لتحديث المطعم.']);
    exit;
  }

  // ✅ التحقق من صلاحية المالك (لا يمكنه تعديل مطعم غيره)
  if ($_SESSION['role'] === 'owner') {
    $stmt = $pdo->prepare("SELECT owner_id FROM restaurants WHERE id = ?");
    $stmt->execute([$id]);
    $restaurant = $stmt->fetch();
    if (!$restaurant || $restaurant['owner_id'] != $_SESSION['user_id']) {
      echo json_encode(['status' => 'error', 'message' => 'غير مصرح لك بتعديل هذا المطعم.']);
      exit;
    }
    $owner_id = $_SESSION['user_id'];
  }

  // 🕓 التحقق من منطق ساعات العمل
  if (!empty($hours)) {
    $parts = explode('-', $hours);
    if (count($parts) === 2) {
      $open_time = trim($parts[0]);
      $close_time = trim($parts[1]);

      if (strtotime($open_time) === false || strtotime($close_time) === false) {
        echo json_encode(['status' => 'error', 'message' => 'صيغة ساعات العمل غير صحيحة.']);
        exit;
      }

      if (strtotime($close_time) <= strtotime($open_time)) {
        echo json_encode(['status' => 'error', 'message' => '⏰ وقت الإغلاق يجب أن يكون بعد وقت الافتتاح.']);
        exit;
      }
    } else {
      echo json_encode(['status' => 'error', 'message' => 'صيغة ساعات العمل غير صحيحة.']);
      exit;
    }
  }

  // 🖼️ جلب الشعار القديم
  $old_logo_stmt = $pdo->prepare("SELECT logo FROM restaurants WHERE id = ?");
  $old_logo_stmt->execute([$id]);
  $current_logo = $old_logo_stmt->fetchColumn();

  // 🖼️ رفع شعار جديد
  if (!empty($_FILES['logo']['name'])) {
    $upload_dir = __DIR__ . '/../../uploads/restaurants/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) {
      echo json_encode(['status' => 'error', 'message' => 'صيغة الصورة غير مدعومة.']);
      exit;
    }

    $logo_name = 'logo_' . time() . '.' . $ext;
    move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo_name);

    if ($current_logo && file_exists($upload_dir . $current_logo)) {
      unlink($upload_dir . $current_logo);
    }
  } else {
    $logo_name = $current_logo;
  }

  // ✅ تحديث البيانات بما فيها ساعات العمل
  $stmt = $pdo->prepare("
    UPDATE restaurants 
    SET name = ?, owner_id = ?, phone = ?, address = ?, working_hours = ?, logo = ?, status = ?
    WHERE id = ?
  ");
  $stmt->execute([$name, $owner_id, $phone, $address, $hours, $logo_name, $status, $id]);

  echo json_encode(['status' => 'success', 'message' => 'تم تحديث بيانات المطعم بنجاح ✏️']);
} catch (Exception $e) {
  echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage()]);
}
