<?php
require_once __DIR__ . '/../includes/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
  echo json_encode(['status' => 'error', 'message' => 'غير مصرح']);
  exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
  SELECT COUNT(*) FROM orders
  INNER JOIN products ON orders.product_id = products.id
  INNER JOIN restaurants ON products.restaurant_id = restaurants.id
  WHERE restaurants.owner_id = ? AND orders.status = 'pending'
");
$stmt->execute([$user_id]);
$count = $stmt->fetchColumn();

echo json_encode(['status' => 'success', 'count' => (int)$count]);
