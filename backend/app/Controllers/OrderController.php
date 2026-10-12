<?php
require_once __DIR__ . '/../Repositories/OrderRepository.php';
require_once __DIR__ . '/../Repositories/UserRepository.php';
require_once __DIR__ . '/../Helpers/Response.php';

class OrderController
{
    private OrderRepository $repo;
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->repo = new OrderRepository();
        $this->userRepo = new UserRepository();
    }

    public function create(array $data, int $customerId): array
    {
        // ✅ Validation
        if (
            empty($data['restaurant_id']) ||
            empty($data['payment_method']) ||
            empty($data['address']) ||
            empty($data['items']) ||
            !is_array($data['items'])
        ) {
            return Response::error("Invalid order data");
        }

        // 👤 جلب بيانات المستخدم
        $user = $this->userRepo->getUserById($customerId);
        if (!$user) {
            return Response::error("User not found");
        }

        $orderId = $this->repo->createOrder(
        customerId: $customerId,
        restaurantId: (int)$data['restaurant_id'],
        paymentMethod: $data['payment_method'],
        deliveryAddress: $data['address'],
        items: $data['items'],
        notes: $data['notes'] ?? null
    );



        return Response::success("Order created successfully", [
            "order_id" => $orderId
        ]);
    }
}

