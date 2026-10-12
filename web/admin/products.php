<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

// ✅ السماح فقط للمالك
if ($_SESSION['role'] !== 'owner') {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// ✅ جلب المطعم المرتبط بالمالك
$stmt = $pdo->prepare("SELECT id, name FROM restaurants WHERE owner_id = ?");
$stmt->execute([$user_id]);
$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$restaurant) {
  echo "<div style='text-align:center;margin-top:60px;font-family:Tajawal'>🚫 لا يوجد مطعم مرتبط بحسابك.</div>";
  exit;
}

$restaurant_id = $restaurant['id'];
$restaurant_name = $restaurant['name'];

// ✅ جلب المنتجات الخاصة بمطعم المالك فقط
$stmt = $pdo->prepare("SELECT * FROM products WHERE restaurant_id = ? ORDER BY id DESC");
$stmt->execute([$restaurant_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ إحصائيات المنتجات
$total_products = count($products);
$active_products = 0;
$total_value = 0;

foreach ($products as $product) {
  $total_value += $product['sale_price'] ?: $product['price'];
  if ($product['is_active'] ?? true) $active_products++;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إدارة المنتجات - زاد</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
      background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ed 100%);
      min-height: 100vh;
      padding-right: 280px;
    }

    @media (max-width: 992px) {
      body {
        padding-right: 0;
      }
    }

    /* المحتوى الرئيسي */
    .main-content {
      padding: 30px;
      min-height: 100vh;
    }

    /* بطاقة المحتوى */
    .content-card {
      background: var(--white);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
      border: 1px solid rgba(0, 0, 0, 0.05);
      margin-bottom: 30px;
    }

    /* رأس الصفحة */
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      flex-wrap: wrap;
      gap: 20px;
    }

    .page-title {
      color: var(--main-color);
      font-weight: 800;
      font-size: 2.2rem;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .page-title i {
      color: var(--gold);
      background: linear-gradient(135deg, rgba(198, 163, 79, 0.1) 0%, rgba(28, 51, 47, 0.1) 100%);
      width: 60px;
      height: 60px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* بطاقات الإحصائيات */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
      font-size: 2rem;
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

    /* أزرار العمل */
    .action-buttons {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--main-color) 0%, #152622 100%);
      border: none;
      border-radius: 12px;
      padding: 14px 25px;
      font-weight: 600;
      font-size: 1rem;
      transition: var(--transition);
      box-shadow: 0 4px 15px rgba(28, 51, 47, 0.2);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(28, 51, 47, 0.3);
    }

    .btn-success {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      border: none;
      border-radius: 12px;
      padding: 12px 25px;
      font-weight: 600;
      transition: var(--transition);
      box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
    }

    /* البحث والتصفية */
    .search-filter-bar {
      background: var(--white);
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
      margin-bottom: 25px;
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
      align-items: center;
    }

    .search-box {
      position: relative;
      flex: 1;
      min-width: 250px;
    }

    .search-box i {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
    }

    .search-box input {
      padding-right: 45px;
      border-radius: 12px;
      border: 2px solid #e9ecef;
      transition: var(--transition);
    }

    .search-box input:focus {
      border-color: var(--gold);
      box-shadow: 0 0 0 0.25rem rgba(198, 163, 79, 0.15);
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

    /* صورة المنتج */
    .product-image {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      object-fit: cover;
      border: 2px solid #e9ecef;
      transition: var(--transition);
    }

    .product-image:hover {
      transform: scale(1.1);
      border-color: var(--gold);
    }

    .image-placeholder {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #6c757d;
      border: 2px dashed #dee2e6;
    }

    /* الأسعار */
    .original-price {
      color: #6c757d;
      text-decoration: line-through;
      font-size: 0.85rem;
    }

    .sale-price {
      color: #28a745;
      font-weight: 700;
      font-size: 1.1rem;
    }

    .regular-price {
      color: var(--main-color);
      font-weight: 700;
      font-size: 1.1rem;
    }

    /* أزرار الإجراءات */
    .btn-action {
      padding: 8px 12px;
      border-radius: 8px;
      font-size: 0.85rem;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: var(--transition);
    }

    .btn-edit {
      background: rgba(255, 193, 7, 0.1);
      color: #ffc107;
      border: 1px solid rgba(255, 193, 7, 0.2);
    }

    .btn-edit:hover {
      background: #ffc107;
      color: white;
      transform: translateY(-2px);
    }

    .btn-delete {
      background: rgba(220, 53, 69, 0.1);
      color: #dc3545;
      border: 1px solid rgba(220, 53, 69, 0.2);
    }

    .btn-delete:hover {
      background: #dc3545;
      color: white;
      transform: translateY(-2px);
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
      padding: 10px;
      border-radius: 10px;
      transition: var(--transition);
      z-index: 1001;
    }

    .sidebar-toggle:hover {
      background-color: rgba(28, 51, 47, 0.1);
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

    /* Modal محسن */
    .modal-content {
      border-radius: 20px;
      border: none;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
      border-bottom: 1px solid rgba(0, 0, 0, 0.08);
      padding: 25px 30px;
    }

    .modal-body {
      padding: 30px;
    }

    .modal-footer {
      border-top: 1px solid rgba(0, 0, 0, 0.08);
      padding: 20px 30px;
    }

    .form-label {
      color: var(--main-color);
      font-weight: 600;
      margin-bottom: 10px;
      font-size: 0.95rem;
    }

    .form-control,
    .form-select {
      border: 2px solid #e9ecef;
      border-radius: 12px;
      padding: 12px 16px;
      font-size: 0.95rem;
      transition: var(--transition);
      background: var(--white);
    }

    .form-control:focus,
    .form-select:focus {
      border-color: var(--gold);
      box-shadow: 0 0 0 0.25rem rgba(198, 163, 79, 0.15);
    }

    /* رفع الصورة */
    .image-upload-container {
      position: relative;
      border: 2px dashed #dee2e6;
      border-radius: 12px;
      padding: 20px;
      text-align: center;
      transition: var(--transition);
      background: #f8f9fa;
      cursor: pointer;
    }

    .image-upload-container:hover {
      border-color: var(--gold);
      background: rgba(198, 163, 79, 0.05);
    }

    .upload-icon {
      font-size: 2rem;
      color: var(--gold);
      margin-bottom: 10px;
    }

    .image-preview {
      max-width: 150px;
      max-height: 150px;
      border-radius: 8px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      border: 2px solid var(--white);
    }

    /* Toast محسن */
    .custom-toast {
      border-radius: 12px;
      border: none;
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
      backdrop-filter: blur(10px);
    }

    .toast-success {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white;
    }

    .toast-error {
      background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
      color: white;
    }

    .toast-warning {
      background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
      color: #212529;
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

      .main-content {
        padding: 20px;
      }

      .content-card {
        padding: 25px;
      }

      .page-header {
        flex-direction: column;
        align-items: flex-start;
      }

      .action-buttons {
        width: 100%;
        justify-content: flex-start;
      }

      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 768px) {
      .content-card {
        padding: 20px;
      }

      .page-title {
        font-size: 1.8rem;
      }

      .page-title i {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
      }

      .stats-grid {
        grid-template-columns: 1fr;
      }

      .table-responsive {
        font-size: 0.9rem;
      }

      .search-filter-bar {
        flex-direction: column;
        align-items: stretch;
      }

      .search-box {
        min-width: 100%;
      }
    }

    @media (max-width: 576px) {
      .main-content {
        padding: 15px;
      }

      .content-card {
        padding: 15px;
      }

      .page-title {
        font-size: 1.6rem;
      }

      .action-buttons {
        flex-direction: column;
      }

      .btn-primary,
      .btn-success {
        width: 100%;
        justify-content: center;
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

      .btn-action {
        width: 100%;
        justify-content: center;
        margin-bottom: 5px;
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
  </style>
</head>

<body>
  <!-- ✅ الشريط الجانبي -->
  <?php include __DIR__ . '/includes/sidebar_owner.php'; ?>

  <!-- طبقة overlay للشاشات الصغيرة -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- المحتوى الرئيسي -->
  <div class="main-content">
    <div class="container-fluid">
      <!-- رأس الصفحة -->
      <div class="page-header">
        <div class="d-flex align-items-center">
          <button class="sidebar-toggle me-3" id="sidebarToggle">
            <i class="fas fa-bars"></i>
          </button>
          <h1 class="page-title">
            <i class="fas fa-boxes"></i>
            إدارة المنتجات
          </h1>
        </div>
        <div class="action-buttons">
          <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus"></i> إضافة منتج جديد
          </button>
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
            <p>إجمالي المنتجات</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="card-content">
            <h3><?= $active_products ?></h3>
            <p>المنتجات النشطة</p>
          </div>
        </div>


        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-store"></i>
          </div>
          <div class="card-content">
            <h3><?= htmlspecialchars($restaurant_name) ?></h3>
            <p>المطعم التابع له</p>
          </div>
        </div>
      </div>

      <!-- شريط البحث والتصفية -->
      <div class="search-filter-bar">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input type="text" class="form-control" placeholder="ابحث عن منتج..." id="searchInput">
        </div>
        <div class="filter-buttons">
          <button class="btn btn-outline-secondary" id="filterAll">الكل</button>
          <button class="btn btn-outline-secondary" id="filterActive">النشطة</button>
          <button class="btn btn-outline-secondary" id="filterDiscounted">المنتجات المخفضة</button>
        </div>
      </div>

      <!-- جدول المنتجات -->
      <div class="content-card">
        <div class="table-container">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th>الصورة</th>
                  <th>اسم المنتج</th>
                  <th>الوصف</th>
                  <th>السعر</th>
                  <th>الحالة</th>
                  <th>الإجراءات</th>
                </tr>
              </thead>
              <tbody id="productTable">
                <?php foreach ($products as $p): ?>
                  <tr data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>"
                    data-active="true"
                    data-discounted="<?= $p['sale_price'] ? 'true' : 'false' ?>">
                    <td class="fw-bold" data-label="رقم المنتج"><?= $p['id'] ?></td>
                    <td data-label="الصورة">
                      <?php if ($p['photo']): ?>
                        <img src="../uploads/products/<?= htmlspecialchars($p['photo']) ?>"
                          class="product-image"
                          alt="<?= htmlspecialchars($p['name']) ?>">
                      <?php else: ?>
                        <div class="image-placeholder">
                          <i class="fas fa-image"></i>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td data-label="اسم المنتج">
                      <div class="fw-bold"><?= htmlspecialchars($p['name']) ?></div>
                    </td>
                    <td data-label="الوصف">
                      <span class="text-muted small"><?= htmlspecialchars($p['description'] ?: '—') ?></span>
                    </td>
                    <td data-label="السعر">
                      <?php if ($p['sale_price']): ?>
                        <div class="original-price"><?= number_format($p['price'], 2) ?> ر.س</div>
                        <div class="sale-price"><?= number_format($p['sale_price'], 2) ?> ر.س</div>
                      <?php else: ?>
                        <div class="regular-price"><?= number_format($p['price'], 2) ?> ر.س</div>
                      <?php endif; ?>
                    </td>
                    <td data-label="الحالة">
                      <span class="badge bg-success">نشط</span>
                    </td>
                    <td data-label="الإجراءات">
                      <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-action btn-edit edit-btn"
                          data-id="<?= $p['id'] ?>"
                          data-name="<?= htmlspecialchars($p['name']) ?>"
                          data-description="<?= htmlspecialchars($p['description']) ?>"
                          data-price="<?= $p['price'] ?>"
                          data-sale_price="<?= $p['sale_price'] ?>"
                          data-photo="<?= htmlspecialchars($p['photo']) ?>">
                          <i class="fas fa-edit"></i> تعديل
                        </button>
                        <button class="btn btn-action btn-delete delete-btn" data-id="<?= $p['id'] ?>">
                          <i class="fas fa-trash"></i> حذف
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                  <tr>
                    <td colspan="7" class="no-data">
                      <i class="fas fa-box-open"></i>
                      <h5 class="mt-3">لا توجد منتجات</h5>
                      <p class="text-muted">لم تقم بإضافة أي منتجات بعد</p>
                      <button class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus"></i> إضافة أول منتج
                      </button>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ Toast محسن -->
  <div class="position-fixed bottom-0 start-0 p-4" style="z-index: 1080">
    <div id="toastContainer"></div>
  </div>

  <!-- 🟩 Modal إضافة منتج -->
  <div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="addForm" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title d-flex align-items-center gap-2">
              <i class="fas fa-plus-circle text-success"></i>
              إضافة منتج جديد
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row g-4">
              <div class="col-md-6">
                <label class="form-label">اسم المنتج <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required placeholder="أدخل اسم المنتج">
              </div>
              <div class="col-md-3">
                <label class="form-label">السعر <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">ر.س</span>
                  <input type="number" name="price" step="0.01" class="form-control" required placeholder="0.00">
                </div>
              </div>
              <div class="col-md-3">
                <label class="form-label">سعر بعد الخصم</label>
                <div class="input-group">
                  <span class="input-group-text">ر.س</span>
                  <input type="number" name="sale_price" step="0.01" class="form-control" placeholder="0.00">
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">الوصف</label>
                <textarea name="description" rows="3" class="form-control" placeholder="أدخل وصفاً للمنتج..."></textarea>
              </div>
              <div class="col-12">
                <label class="form-label">صورة المنتج</label>
                <div class="image-upload-container" id="addImageUpload">
                  <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                  </div>
                  <h6>اسحب وأفلت الصورة هنا</h6>
                  <p class="text-muted mb-2">أو انقر لاختيار صورة</p>
                  <p class="small text-muted">(PNG, JPG, JPEG - الحد الأقصى 5MB)</p>
                  <input type="file" name="photo" class="d-none" accept="image/*" id="addPhotoInput">
                  <img class="image-preview mt-3" id="addImagePreview" style="display:none;">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save me-2"></i>حفظ المنتج
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- 🟡 Modal تعديل منتج -->
  <div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="editForm" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title d-flex align-items-center gap-2">
              <i class="fas fa-edit text-warning"></i>
              تعديل المنتج
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" id="edit-id">
            <input type="hidden" name="old_photo" id="edit-old_photo">
            <div class="row g-4">
              <div class="col-md-6">
                <label class="form-label">اسم المنتج <span class="text-danger">*</span></label>
                <input type="text" name="name" id="edit-name" class="form-control" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">السعر <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">ر.س</span>
                  <input type="number" name="price" id="edit-price" step="0.01" class="form-control" required>
                </div>
              </div>
              <div class="col-md-3">
                <label class="form-label">سعر بعد الخصم</label>
                <div class="input-group">
                  <span class="input-group-text">ر.س</span>
                  <input type="number" name="sale_price" id="edit-sale_price" step="0.01" class="form-control">
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">الوصف</label>
                <textarea name="description" id="edit-description" rows="3" class="form-control"></textarea>
              </div>
              <div class="col-md-4">
                <label class="form-label">الحالة</label>
                <select name="is_active" id="edit-is_active" class="form-select">
                  <option value="1">نشط</option>
                  <option value="0">غير نشط</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">صورة المنتج</label>
                <div class="image-upload-container" id="editImageUpload">
                  <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                  </div>
                  <h6>اسحب وأفلت الصورة هنا</h6>
                  <p class="text-muted mb-2">أو انقر لاختيار صورة</p>
                  <p class="small text-muted">(PNG, JPG, JPEG - الحد الأقصى 5MB)</p>
                  <input type="file" name="photo" class="d-none" accept="image/*" id="editPhotoInput">
                  <img class="image-preview mt-3" id="editImagePreview">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" class="btn btn-warning text-white">
              <i class="fas fa-save me-2"></i>حفظ التعديلات
            </button>
          </div>
        </form>
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

    // البحث والتصفية
    document.getElementById('searchInput').addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      const rows = document.querySelectorAll('#productTable tr[data-name]');

      rows.forEach(row => {
        const productName = row.getAttribute('data-name');
        if (productName.includes(searchTerm)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });

    // تصفية حسب النوع
    document.getElementById('filterAll').addEventListener('click', () => filterProducts('all'));
    document.getElementById('filterActive').addEventListener('click', () => filterProducts('active'));
    document.getElementById('filterDiscounted').addEventListener('click', () => filterProducts('discounted'));

    function filterProducts(type) {
      const rows = document.querySelectorAll('#productTable tr[data-name]');

      rows.forEach(row => {
        switch (type) {
          case 'all':
            row.style.display = '';
            break;
          case 'active':
            row.style.display = row.getAttribute('data-active') === 'true' ? '' : 'none';
            break;
          case 'discounted':
            row.style.display = row.getAttribute('data-discounted') === 'true' ? '' : 'none';
            break;
        }
      });
    }

    // رفع الصور للمودالات
    function setupImageUpload(uploadContainer, inputElement, previewElement) {
      uploadContainer.addEventListener('click', () => inputElement.click());

      inputElement.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
          if (file.size > 5 * 1024 * 1024) {
            showToast('❌ حجم الصورة كبير جداً. الحد الأقصى 5MB', 'error');
            return;
          }

          const reader = new FileReader();
          reader.onload = function(e) {
            previewElement.src = e.target.result;
            previewElement.style.display = 'block';
          };
          reader.readAsDataURL(file);
        }
      });

      // سحب وإفلات
      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadContainer.addEventListener(eventName, preventDefaults, false);
      });

      function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
      }

      ['dragenter', 'dragover'].forEach(eventName => {
        uploadContainer.addEventListener(eventName, () => {
          uploadContainer.style.borderColor = 'var(--gold)';
          uploadContainer.style.background = 'rgba(198, 163, 79, 0.1)';
        }, false);
      });

      ['dragleave', 'drop'].forEach(eventName => {
        uploadContainer.addEventListener(eventName, () => {
          uploadContainer.style.borderColor = '';
          uploadContainer.style.background = '';
        }, false);
      });

      uploadContainer.addEventListener('drop', handleDrop, false);

      function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        inputElement.files = files;
        inputElement.dispatchEvent(new Event('change'));
      }
    }

    // إعداد رفع الصور
    setupImageUpload(
      document.getElementById('addImageUpload'),
      document.getElementById('addPhotoInput'),
      document.getElementById('addImagePreview')
    );

    setupImageUpload(
      document.getElementById('editImageUpload'),
      document.getElementById('editPhotoInput'),
      document.getElementById('editImagePreview')
    );

    // إظهار Toast
    function showToast(message, type) {
      const colorClass = {
        success: 'toast-success',
        error: 'toast-error',
        warning: 'toast-warning'
      } [type] || 'toast-success';

      const icon = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle'
      } [type] || 'fa-info-circle';

      const toast = $(`
        <div class="toast custom-toast ${colorClass} show mb-3" role="alert">
          <div class="toast-header border-0">
            <i class="fas ${icon} me-2"></i>
            <strong class="me-auto">إشعار</strong>
            <small>الآن</small>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
          </div>
          <div class="toast-body">
            ${message}
          </div>
        </div>
      `);

      $('#toastContainer').append(toast);
      setTimeout(() => toast.remove(), 5000);
    }

    // 🟩 إضافة منتج
    $('#addForm').on('submit', function(e) {
      e.preventDefault();

      const submitBtn = $(this).find('button[type="submit"]');
      const originalText = submitBtn.html();
      submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>جاري الإضافة...');

      const formData = new FormData(this);
      formData.append('add_product', '1'); // ✅ ضروري حتى يتعرف PHP على الطلب

      $.ajax({
        url: 'add_product.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(res) {
          if (res.status === 'success') {
            $('#addModal').modal('hide');
            showToast('✅ ' + res.message, 'success');
            setTimeout(() => location.reload(), 1500);
          } else {
            showToast('⚠️ ' + res.message, 'warning');
            submitBtn.prop('disabled', false).html(originalText);
          }
        },
        error: function(xhr, status, error) {
          showToast('❌ حدث خطأ أثناء الإضافة: ' + error, 'error');
          submitBtn.prop('disabled', false).html(originalText);
        }
      });
    });

    // ✏️ فتح مودال التعديل
    $('.edit-btn').on('click', function() {
      const p = $(this).data();
      $('#edit-id').val(p.id);
      $('#edit-name').val(p.name);
      $('#edit-description').val(p.description);
      $('#edit-price').val(p.price);
      $('#edit-sale_price').val(p.sale_price);
      $('#edit-old_photo').val(p.photo);
      $('#edit-is_active').val(p.is_active ? '1' : '0');

      if (p.photo) {
        $('#editImagePreview').attr('src', '../uploads/products/' + p.photo).show();
      } else {
        $('#editImagePreview').hide();
      }

      new bootstrap.Modal('#editModal').show();
    });

    // 🟨 تعديل منتج
    $('#editForm').on('submit', function(e) {
      e.preventDefault();

      const submitBtn = $(this).find('button[type="submit"]');
      const originalText = submitBtn.html();
      submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>جاري التعديل...');

      const formData = new FormData(this);
      formData.append('update_product', '1'); // ✅ أضف هذا السطر المهم

      $.ajax({
        url: 'update_product.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(res) {
          if (res.status === 'success') {
            $('#editModal').modal('hide');
            showToast('✅ ' + res.message, 'success');
            setTimeout(() => location.reload(), 1500);
          } else {
            showToast('⚠️ ' + res.message, 'warning');
            submitBtn.prop('disabled', false).html(originalText);
          }
        },
        error: function(xhr, status, error) {
          showToast('❌ حدث خطأ أثناء التعديل: ' + error, 'error');
          submitBtn.prop('disabled', false).html(originalText);
        }
      });
    });

    // 🗑️ حذف منتج
    // 🗑️ حذف منتج (بتصميم الموقع)
    $('.delete-btn').on('click', function() {
      const productId = $(this).data('id');
      const productName = $(this).closest('tr').find('td:nth-child(3)').text().trim();

      // إنشاء نافذة تأكيد بتصميم الموقع
      const confirmBox = $(`
    <div class="custom-confirm shadow-lg p-4 rounded-4" 
         style="position:fixed; top:50%; left:50%; transform:translate(-50%,-50%);
                background:#fff; z-index:2000; text-align:center; width:350px;
                border:2px solid #C6A34F; font-family:'Tajawal',sans-serif;">
      <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size:2.5rem;"></i>
      <h5 class="fw-bold mb-3">هل أنت متأكد؟</h5>
      <p class="text-muted mb-4">سيتم حذف المنتج <b>${productName}</b> نهائيًا ولا يمكن التراجع عن ذلك.</p>
      <div class="d-flex justify-content-center gap-3">
        <button class="btn btn-secondary px-4" id="cancelDelete">إلغاء</button>
        <button class="btn btn-danger px-4" id="confirmDelete">حذف</button>
      </div>
    </div>
  `);

      $('body').append(confirmBox);

      // عند الضغط على "إلغاء"
      $('#cancelDelete').on('click', () => confirmBox.remove());

      // عند الضغط على "حذف"
      $('#confirmDelete').on('click', function() {
        confirmBox.remove();

        $.ajax({
          url: 'delete_product.php',
          type: 'POST',
          data: {
            id: productId
          },
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              showToast('✅ ' + res.message, 'success');
              setTimeout(() => location.reload(), 1500);
            } else {
              showToast('⚠️ ' + res.message, 'warning');
            }
          },
          error: function(xhr, status, error) {
            showToast('❌ حدث خطأ أثناء الحذف: ' + error, 'error');
          }
        });
      });
    });

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

    // إغلاق الشريط الجانبي عند تغيير حجم النافذة
    window.addEventListener('resize', function() {
      if (window.innerWidth > 992 && sidebar.classList.contains('active')) {
        toggleSidebar();
      }
    });
  </script>
</body>

</html>