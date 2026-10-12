<?php
// backend/test_db.php

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = DatabaseConfig::getConnection();

    echo "تم الاتصال بقاعدة البيانات بنجاح\n";

    // اختبار بسيط: جلب عدد المستخدمين
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $row = $stmt->fetch();

    echo "عدد المستخدمين الحالي: " . $row['count'] . "\n";
} 
catch (Exception $e) {
    echo "خطأ في الاتصال: " . $e->getMessage();
}
