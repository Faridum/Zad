<?php
require_once __DIR__ . '/../../config/database.php';

class OrderRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConfig::getConnection();
    }

    public function createOrder(
        int $customerId,
        int $restaurantId,
        string $paymentMethod,
        string $deliveryAddress,
        array $items,
        ?string $notes = null
    ): int {
        $this->pdo->beginTransaction();

        try {
            // 1️⃣ جلب بيانات العميل
            $stmt = $this->pdo->prepare("
                SELECT name, phone
                FROM users
                WHERE id = ?
            ");
            $stmt->execute([$customerId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("Customer not found");
            }

            // 2️⃣ حساب السعر الإجمالي
            $totalAmount = 0;

            foreach ($items as $item) {
                if (
                    !isset($item['product_id']) ||
                    !isset($item['quantity'])
                ) {
                    throw new Exception("Invalid item structure");
                }

                $stmt = $this->pdo->prepare(
                    "SELECT price FROM products WHERE id = ?"
                );
                $stmt->execute([$item['product_id']]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$product) {
                    throw new Exception("Product not found");
                }

                $totalAmount += $product['price'] * (int)$item['quantity'];
            }

            // 3️⃣ إنشاء الطلب
            $stmt = $this->pdo->prepare("
                INSERT INTO orders
                (
                    customer_id,
                    restaurant_id,
                    total_price,
                    payment_method,
                    customer_name,
                    customer_phone,
                    delivery_address,
                    notes
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $customerId,
                $restaurantId,
                $totalAmount,
                $paymentMethod,
                $user['name'],
                $user['phone'],
                $deliveryAddress,
                $notes
            ]);

            $orderId = (int)$this->pdo->lastInsertId();

            // 4️⃣ إنشاء عناصر الطلب
            $stmtItem = $this->pdo->prepare("
                INSERT INTO order_items
                (
                    order_id,
                    product_id,
                    quantity,
                    unit_price,
                    total_price
                )
                VALUES (?, ?, ?, ?, ?)
            ");

            foreach ($items as $item) {
                $stmt = $this->pdo->prepare(
                    "SELECT price FROM products WHERE id = ?"
                );
                $stmt->execute([$item['product_id']]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                $qty = (int)$item['quantity'];
                $unit = $product['price'];

                $stmtItem->execute([
                    $orderId,
                    $item['product_id'],
                    $qty,
                    $unit,
                    $unit * $qty
                ]);
            }

            $this->pdo->commit();
            return $orderId;

        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}

