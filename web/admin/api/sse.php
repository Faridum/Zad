<?php
// 🧩 تأكد أن هذا أول سطر في الملف، لا مسافات قبله
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php'; // لجلب $_SESSION['role'] و user_id

// ✅ إعداد رؤوس SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// منع ضغط المخرجات
if (function_exists('apache_setenv')) {
  @apache_setenv('no-gzip', '1');
}
ini_set('zlib.output_compression', 0);

// منع المهلة
set_time_limit(0);
ob_end_flush();
ob_implicit_flush(true);

// 🔹 فلترة حسب المالك (في حال المالك فقط)
$restaurant_id = null;
if (isset($_SESSION['role']) && $_SESSION['role'] === 'owner') {
  $stmt = $pdo->prepare("SELECT id FROM restaurants WHERE owner_id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($restaurant) {
    $restaurant_id = (int)$restaurant['id'];
  }
}

// 🔹 آخر معرف تم إرساله
$last_id = 0;

while (true) {
  try {
    // إذا تم قطع الاتصال، اخرج من الحلقة لتقليل التحميل
    if (connection_aborted()) {
      break;
    }

    // 🔹 الاستعلام الديناميكي (لكل المالكين أو المديرين)
    $sql = "SELECT id, type, product_id, restaurant_id, created_at 
            FROM updates 
            WHERE id > ?";

    $params = [$last_id];

    if ($restaurant_id) {
      $sql .= " AND restaurant_id = ?";
      $params[] = $restaurant_id;
    }

    $sql .= " ORDER BY id ASC";
    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

    if ($rows) {
      foreach ($rows as $update) {
        $last_id = $update['id'];

        // 🔹 إرسال event بالاسم الصحيح (حسب النوع)
        $eventType = $update['type'] ?? 'update';

        echo "event: {$eventType}\n";
        echo "data: " . json_encode($update, JSON_UNESCAPED_UNICODE) . "\n\n";

        @ob_flush();
        @flush();
      }
    }

    // 🔹 إرسال Ping كل 15 ثانية لإبقاء الاتصال حيًا
    echo ":\n\n";
    @ob_flush();
    @flush();

    sleep(3);
  } catch (Exception $e) {
    echo "event: error\n";
    echo "data: " . json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE) . "\n\n";
    @ob_flush();
    @flush();
    sleep(5);
  }
}
