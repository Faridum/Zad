<?php
require_once __DIR__ . '/../../config/database.php';

class HomeRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConfig::getConnection();
    }

    // -----------------------------------
    // Active Restaurants
    // -----------------------------------
    public function getActiveRestaurants(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                name,
                logo,
                address,
                phone,
                working_hours
            FROM restaurants
            WHERE status = 'active'
            ORDER BY created_at DESC
        ");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -----------------------------------
    // Products from Active Restaurants
    // -----------------------------------
    public function getLatestProducts(int $limit = 20): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                p.id,
                p.name,
                p.description,
                p.price,
                p.sale_price,
                p.photo,
                p.restaurant_id,
                r.name AS restaurant_name,
                r.logo AS restaurant_logo
            FROM products p
            INNER JOIN restaurants r ON r.id = p.restaurant_id
            WHERE r.status = 'active'
            ORDER BY p.created_at DESC
            LIMIT :limit
        ");

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

