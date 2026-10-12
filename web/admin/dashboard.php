<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

// ✅ السماح فقط للمالك
if ($_SESSION['role'] !== 'owner') {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// ✅ جلب بيانات المطعم المرتبط بالمالك (بما في ذلك الشعار)
$stmt = $pdo->prepare("SELECT id, name, logo FROM restaurants WHERE owner_id = ?");
$stmt->execute([$user_id]);
$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$restaurant) {
  echo "<div style='text-align:center;margin-top:60px;font-family:Tajawal'>🚫 لا يوجد مطعم مرتبط بحسابك.</div>";
  exit;
}

$restaurant_id = $restaurant['id'];
$restaurant_name = $restaurant['name'];
$restaurant_logo = $restaurant['logo'];

// ✅ عدد المنتجات الخاصة بالمطعم
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE restaurant_id = ?");
$stmt->execute([$restaurant_id]);
$total_products = $stmt->fetchColumn();

// ✅ عدد الطلبات الخاصة بالمطعم
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE restaurant_id = ?");
$stmt->execute([$restaurant_id]);
$total_orders = $stmt->fetchColumn();

// ✅ إجمالي المبيعات للطلبات المكتملة أو الجاهزة
$stmt = $pdo->prepare("SELECT COALESCE(SUM(total_price),0) FROM orders WHERE restaurant_id = ? AND status IN ('completed','ready')");
$stmt->execute([$restaurant_id]);
$total_sales = $stmt->fetchColumn();

// ✅ طلبات قيد الانتظار
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE restaurant_id = ? AND status = 'pending'");
$stmt->execute([$restaurant_id]);
$pending_orders = $stmt->fetchColumn();

// ✅ آخر 5 طلبات للمطعم
$stmt = $pdo->prepare("
  SELECT orders.*, users.name AS customer_name, products.name AS product_name
  FROM orders
  JOIN users ON orders.customer_id = users.id
  JOIN products ON orders.product_id = products.id
  WHERE orders.restaurant_id = ?
  ORDER BY orders.created_at DESC
  LIMIT 5
");
$stmt->execute([$restaurant_id]);
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة تحكم المالك - زاد</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    :root {
      --main-color: #1C332F;
      --gold: #C6A34F;
      --light-bg: #f8f9fa;
      --white: #ffffff;
      --transition: all 0.3s ease;
    }

    body {
      font-family: 'Tajawal', sans-serif;
      background-color: #f5f7f9;
      color: #333;
      overflow-x: hidden;
      padding-right: 280px;
    }

    /* المحتوى الرئيسي */
    .main-content {
      padding: 20px;
      min-height: 100vh;
    }

    .header-bar {
      background-color: var(--white);
      border-radius: 16px;
      padding: 25px 30px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-right: 4px solid var(--gold);
    }

    .header-bar h1 {
      color: var(--main-color);
      font-weight: 800;
      margin: 0;
      font-size: 1.9rem;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .restaurant-info {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .restaurant-logo {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      object-fit: cover;
      border: 2px solid var(--gold);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .logo-placeholder {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      background: linear-gradient(135deg, var(--main-color), var(--gold));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.5rem;
      border: 2px solid var(--gold);
    }

    .restaurant-details {
      text-align: right;
    }

    .restaurant-name {
      color: var(--gold);
      font-weight: 700;
      font-size: 1.3rem;
      margin-bottom: 5px;
    }

    .welcome-text {
      color: #6c757d;
      font-size: 1rem;
      margin: 0;
    }

    /* بطاقات الإحصائيات */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background: var(--white);
      border-radius: 16px;
      padding: 25px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
      transition: var(--transition);
      border-top: 4px solid var(--gold);
      position: relative;
      overflow: hidden;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(198, 163, 79, 0.05) 0%, rgba(28, 51, 47, 0.05) 100%);
      z-index: 0;
    }

    .stat-card .card-content {
      position: relative;
      z-index: 1;
    }

    .stat-card h3 {
      font-size: 2.2rem;
      font-weight: 800;
      color: var(--main-color);
      margin-bottom: 5px;
    }

    .stat-card p {
      color: #6c757d;
      font-weight: 500;
      margin-bottom: 0;
    }

    .stat-icon {
      position: absolute;
      left: 25px;
      top: 25px;
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(28, 51, 47, 0.1);
      color: var(--main-color);
      font-size: 1.5rem;
    }

    /* بطاقة المحتوى */
    .content-card {
      background: var(--white);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
      margin-bottom: 30px;
      border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .content-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
      flex-wrap: wrap;
      gap: 15px;
    }

    .content-header h2 {
      color: var(--main-color);
      font-weight: 700;
      margin: 0;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    /* الجدول المحسن */
    .table-container {
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.06);
      border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .table {
      margin-bottom: 0;
      border-collapse: separate;
      border-spacing: 0;
    }

    .table thead th {
      background: linear-gradient(135deg, var(--main-color), #152622);
      color: white;
      font-weight: 600;
      padding: 18px 15px;
      border: none;
      font-size: 0.95rem;
      position: relative;
    }

    .table thead th::after {
      content: '';
      position: absolute;
      bottom: 0;
      right: 0;
      width: 100%;
      height: 2px;
      background: var(--gold);
    }

    .table tbody td {
      padding: 16px 15px;
      vertical-align: middle;
      border-bottom: 1px solid #f0f0f0;
      transition: var(--transition);
    }

    .table tbody tr {
      transition: var(--transition);
    }

    .table tbody tr:hover {
      background-color: rgba(28, 51, 47, 0.03);
      transform: scale(1.002);
    }

    /* حالة الطلب */
    .status-badge {
      padding: 8px 16px;
      border-radius: 25px;
      font-size: 0.85rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .status-pending {
      background-color: rgba(255, 193, 7, 0.12);
      color: #ffc107;
      border: 1px solid rgba(255, 193, 7, 0.2);
    }

    .status-ready {
      background-color: rgba(23, 162, 184, 0.12);
      color: #17a2b8;
      border: 1px solid rgba(23, 162, 184, 0.2);
    }

    .status-completed {
      background-color: rgba(40, 167, 69, 0.12);
      color: #28a745;
      border: 1px solid rgba(40, 167, 69, 0.2);
    }

    .status-canceled {
      background-color: rgba(220, 53, 69, 0.12);
      color: #dc3545;
      border: 1px solid rgba(220, 53, 69, 0.2);
    }

    /* زر عرض الكل */
    .view-all {
      color: var(--gold);
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      transition: var(--transition);
      padding: 8px 15px;
      border-radius: 8px;
      background-color: rgba(198, 163, 79, 0.1);
    }

    .view-all:hover {
      color: var(--main-color);
      background-color: rgba(198, 163, 79, 0.2);
    }

    /* حالة عدم وجود بيانات */
    .no-data {
      text-align: center;
      padding: 60px 20px;
      color: #6c757d;
    }

    .no-data i {
      font-size: 4rem;
      margin-bottom: 20px;
      opacity: 0.4;
    }

    .no-data h5 {
      margin-bottom: 10px;
      color: #495057;
    }

    /* زر تبديل الشريط الجانبي */
    .sidebar-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 1.5rem;
      color: var(--main-color);
      padding: 8px;
      border-radius: 8px;
      transition: var(--transition);
      z-index: 1001;
    }

    .sidebar-toggle:hover {
      background-color: rgba(28, 51, 47, 0.1);
    }

    /* تحسينات للاستجابة */
    @media (max-width: 1200px) {
      body {
        padding-right: 0;
      }
    }

    @media (max-width: 992px) {
      .sidebar {
        transform: translateX(100%);
        transition: var(--transition);
        width: 280px;
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .sidebar-toggle {
        display: block;
      }

      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .header-bar {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }

      .restaurant-info {
        width: 100%;
        justify-content: flex-end;
      }
    }

    @media (max-width: 768px) {
      .stats-grid {
        grid-template-columns: 1fr;
      }

      .header-bar {
        padding: 20px;
      }

      .header-bar h1 {
        font-size: 1.6rem;
      }

      .content-header {
        flex-direction: column;
        align-items: flex-start;
      }

      .main-content {
        padding: 15px;
      }

      .content-card {
        padding: 20px;
      }

      .table-responsive {
        font-size: 0.9rem;
      }
    }

    @media (max-width: 576px) {
      .stat-card {
        padding: 20px;
      }

      .stat-icon {
        left: 20px;
        top: 20px;
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
      }

      .restaurant-info {
        flex-direction: column;
        text-align: center;
        gap: 10px;
      }

      .restaurant-details {
        text-align: center;
      }

      .table thead {
        display: none;
      }

      .table tbody tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 15px;
      }

      .table tbody td {
        display: block;
        text-align: left;
        border: none;
        padding: 8px 0;
        position: relative;
        padding-right: 50%;
      }

      .table tbody td::before {
        content: attr(data-label);
        position: absolute;
        right: 0;
        width: 45%;
        padding-left: 10px;
        font-weight: bold;
        color: var(--main-color);
      }

      .status-badge {
        justify-content: center;
      }
    }

    /* تخصيص شريط التمرير */
    ::-webkit-scrollbar {
      width: 8px;
    }

    ::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
      background: var(--gold);
      border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: #b8943a;
    }

    /* طبقة overlay للشاشات الصغيرة */
    .sidebar-overlay {
      display: none;
      position: fixed;
      top: 0;
      right: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 999;
    }

    .sidebar-overlay.active {
      display: block;
    }
  </style>
</head>

<body>
  <!-- ✅ الشريط الجانبي -->
  <?php include __DIR__ . '/includes/sidebar_owner.php'; ?>

  <!-- طبقة overlay للشاشات الصغيرة -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- المحتوى الرئيسي -->
  <div class="main-content">
    <!-- شريط العنوان -->
    <div class="header-bar">
      <div class="d-flex align-items-center">
        <button class="sidebar-toggle me-3" id="sidebarToggle">
          <i class="fas fa-bars"></i>
        </button>
        <h1><i class="fas fa-tachometer-alt text-gold"></i> لوحة تحكم المالك</h1>
      </div>
      <div class="restaurant-info">
        <?php if ($restaurant_logo): ?>
          <img src="../uploads/restaurants/<?= htmlspecialchars($restaurant_logo) ?>" alt="<?= htmlspecialchars($restaurant_name) ?>" class="restaurant-logo">
        <?php else: ?>
          <div class="logo-placeholder">
            <i class="fas fa-utensils"></i>
          </div>
        <?php endif; ?>
        <div class="restaurant-details">
          <div class="restaurant-name"><?= htmlspecialchars($restaurant_name) ?></div>
          <p class="welcome-text">مرحباً بعودتك، <?= htmlspecialchars($_SESSION['name']) ?> 👋</p>
        </div>
      </div>
    </div>

    <!-- بطاقات الإحصائيات -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-box"></i>
        </div>
        <div class="card-content">
          <h3><?= $total_products ?></h3>
          <p>المنتجات</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="card-content">
          <h3><?= $total_orders ?></h3>
          <p>إجمالي الطلبات</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-chart-line"></i>
        </div>
        <div class="card-content">
          <h3><?= number_format($total_sales, 2) ?> ر.س</h3>
          <p>إجمالي المبيعات</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-clock"></i>
        </div>
        <div class="card-content">
          <h3><?= $pending_orders ?></h3>
          <p>طلبات قيد الانتظار</p>
        </div>
      </div>
    </div>

    <!-- آخر الطلبات -->
    <div class="content-card">
      <div class="content-header">
        <h2><i class="fas fa-shopping-cart text-gold"></i> آخر الطلبات</h2>
        <a href="orders.php" class="view-all">عرض الكل <i class="fas fa-arrow-left ms-1"></i></a>
      </div>

      <div class="table-container">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>العميل</th>
                <th>المنتج</th>
                <th>الكمية</th>
                <th>الإجمالي</th>
                <th>الحالة</th>
                <th>التاريخ</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($recent_orders): ?>
                <?php foreach ($recent_orders as $order): ?>
                  <tr>
                    <td class="fw-bold" data-label="رقم الطلب"><?= $order['id'] ?></td>
                    <td data-label="العميل"><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td data-label="المنتج"><?= htmlspecialchars($order['product_name']) ?></td>
                    <td data-label="الكمية"><?= $order['quantity'] ?></td>
                    <td data-label="الإجمالي" class="fw-bold text-success"><?= number_format($order['total_price'], 2) ?> ر.س</td>
                    <td data-label="الحالة">
                      <?php
                      $status = $order['status'];
                      $badge_class = 'status-' . $status;
                      $status_text = '';
                      $status_icon = '';

                      switch ($status) {
                        case 'pending':
                          $status_text = 'قيد الانتظار';
                          $status_icon = 'clock';
                          break;
                        case 'ready':
                          $status_text = 'جاهز';
                          $status_icon = 'check-circle';
                          break;
                        case 'completed':
                          $status_text = 'مكتمل';
                          $status_icon = 'check-double';
                          break;
                        case 'canceled':
                          $status_text = 'ملغي';
                          $status_icon = 'times-circle';
                          break;
                        default:
                          $status_text = $status;
                          $status_icon = 'info-circle';
                      }
                      ?>
                      <span class="status-badge <?= $badge_class ?>">
                        <i class="fas fa-<?= $status_icon ?>"></i>
                        <?= $status_text ?>
                      </span>
                    </td>
                    <td data-label="التاريخ">
                      <small class="text-muted"><?= date('Y-m-d', strtotime($order['created_at'])) ?></small>
                      <br>
                      <small class="text-muted"><?= date('H:i', strtotime($order['created_at'])) ?></small>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="no-data">
                    <i class="fas fa-shopping-cart"></i>
                    <h5 class="mt-3">لا توجد طلبات</h5>
                    <p class="text-muted">لم يتم تقديم أي طلبات لمطعمك بعد</p>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
    // عناصر DOM
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    // تبديل الشريط الجانبي
    function toggleSidebar() {
      sidebar.classList.toggle('active');
      sidebarOverlay.classList.toggle('active');
      document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }

    // إضافة event listeners
    sidebarToggle.addEventListener('click', toggleSidebar);
    sidebarOverlay.addEventListener('click', toggleSidebar);

    // إغلاق الشريط الجانبي عند النقر على رابط (للشاشات الصغيرة)
    if (window.innerWidth <= 992) {
      document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', () => {
          if (sidebar.classList.contains('active')) {
            toggleSidebar();
          }
        });
      });
    }

    // جعل الجدول متجاوباً على الشاشات الصغيرة
    document.addEventListener('DOMContentLoaded', function() {
      const table = document.querySelector('table');
      if (window.innerWidth <= 576) {
        const headers = [];
        table.querySelectorAll('thead th').forEach(header => {
          headers.push(header.textContent);
        });

        table.querySelectorAll('tbody td').forEach((cell, index) => {
          const headerIndex = index % headers.length;
          cell.setAttribute('data-label', headers[headerIndex]);
        });
      }
    });

    // إغلاق الشريط الجانبي عند تغيير حجم النافذة
    window.addEventListener('resize', function() {
      if (window.innerWidth > 992 && sidebar.classList.contains('active')) {
        toggleSidebar();
      }
    });
  </script>
</body>

</html>