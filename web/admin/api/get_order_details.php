<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

// السماح فقط للمالك أو المدير
if (!in_array($_SESSION['role'], ['owner', 'admin'])) {
  http_response_code(403);
  echo json_encode(['status' => 'error', 'message' => '🚫 صلاحيات غير كافية']);
  exit;
}

header('Content-Type: application/json; charset=utf-8');

// التحقق من وجود order_id
if (empty($_GET['order_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'رقم الطلب غير موجود']);
  exit;
}

$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'];

// ✅ جلب تفاصيل الطلب (مع التحقق من أنه يخص مطعم المالك)
$stmt = $pdo->prepare("
  SELECT 
    orders.id,
    orders.quantity,
    orders.total_price,
    orders.status,
    orders.created_at,
    orders.note,
    users.name AS customer_name,
    users.email AS customer_email,
    users.phone AS customer_phone,
    products.name AS product_name,
    products.description AS product_description,
    products.photo AS product_photo,
    restaurants.name AS restaurant_name
  FROM orders
  INNER JOIN users ON orders.customer_id = users.id
  INNER JOIN products ON orders.product_id = products.id
  INNER JOIN restaurants ON products.restaurant_id = restaurants.id
  WHERE orders.id = ? 
  AND (restaurants.owner_id = ? OR ? IN (SELECT id FROM users WHERE role = 'admin'))
");
$stmt->execute([$order_id, $user_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
  echo json_encode(['status' => 'error', 'message' => '🚫 لا يمكن الوصول إلى هذا الطلب']);
  exit;
}

// ✅ تجهيز الاستجابة
$response = [
  'status' => 'success',
  'order' => [
    'id' => $order['id'],
    'status' => $order['status'],
    'created_at' => $order['created_at'],
    'quantity' => $order['quantity'],
    'total_price' => $order['total_price'],
    'note' => $order['note'],
    'restaurant_name' => $order['restaurant_name'],
    'product' => [
      'name' => $order['product_name'],
      'description' => $order['product_description'],
      'photo' => $order['product_photo']
    ],
    'customer' => [
      'name' => $order['customer_name'],
      'email' => $order['customer_email'],
      'phone' => $order['customer_phone']
    ]
  ]
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
