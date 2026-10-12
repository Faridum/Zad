<?php
// backend/app/Repositories/UserRepository.php

/**
 * UserRepository
 * ---------------
 * هذا الملف مسؤول عن تنفيذ العمليات الخاصة بجدول المستخدمين:
 * (جلب المستخدم – إنشاء مستخدم – التحقق من البريد – ...)
 *
 * ملاحظة:
 * هذا الملف لا يحتوي أي منطق تسجيل دخول أو JWT.
 * فقط CRUD على المستخدمين.
 */

// استدعاء ملف الاتصال بقاعدة البيانات
require_once __DIR__ . '/../../config/database.php';

class UserRepository
{
    private PDO $db;

    /**
     * عند إنشاء الكائن، نحصل على اتصال PDO مرة واحدة
     */
    public function __construct()
    {
        $this->db = DatabaseConfig::getConnection();
    }

    /**
     * إرجاع مستخدم حسب البريد الإلكتروني
     */
    public function getUserByEmail(string $email): ?array
    {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);

        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * إنشاء مستخدم جديد
     */
    public function createUser(string $name, string $email, string $password, string $phone, string $role = "customer"): int|false
    {
        $query = "INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);

        $success = $stmt->execute([$name, $email, $password, $phone, $role]);

        if ($success) {
            return (int)$this->db->lastInsertId();
        }

        return false;
    }

    /**
     * إرجاع مستخدم عبر ID
     */
    public function getUserById(int $id): ?array
    {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);

        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * التحقق من وجود بريد مسبقاً
     */
    public function emailExists(string $email): bool
    {
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);

        return $stmt->fetch() ? true : false;
    }

    public function updateUserProfile(int $id, string $name, string $email, string $phone): bool
{
    $query = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
    $stmt = $this->db->prepare($query);

    return $stmt->execute([$name, $email, $phone, $id]);
}
/**
 * تحديث كلمة مرور المستخدم
 */
public function updateUserPassword(int $id, string $hashedPassword): bool
{
    $query = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $this->db->prepare($query);

    return $stmt->execute([$hashedPassword, $id]);
}



}
