<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

// ✅ السماح فقط للمدير
if ($_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

// ✅ معالجة القبول أو الرفض
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
  $id = (int)$_POST['id'];
  $action = $_POST['action'];

  $stmt = $pdo->prepare("SELECT * FROM restaurant_requests WHERE id = ?");
  $stmt->execute([$id]);
  $req = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$req) {
    echo json_encode(['status' => 'error', 'message' => 'الطلب غير موجود']);
    exit;
  }

  if ($action === 'approve') {
    try {
      $pdo->beginTransaction();

      // ✅ التحقق من وجود مستخدم بنفس البريد
      $checkUser = $pdo->prepare("SELECT id FROM users WHERE email = ?");
      $checkUser->execute([$req['email']]);
      if ($checkUser->fetch()) {
        throw new Exception("البريد الإلكتروني مستخدم مسبقًا");
      }

      // ✅ إنشاء المستخدم المالك
      $insertUser = $pdo->prepare("
        INSERT INTO users (name, email, password, role, phone, created_at)
        VALUES (?, ?, ?, 'owner', ?, NOW())
      ");
      $insertUser->execute([$req['owner_name'], $req['email'], $req['password'], $req['phone']]);
      $owner_id = $pdo->lastInsertId();

      // ✅ إنشاء المطعم المرتبط بالمالك
      $insertRestaurant = $pdo->prepare("
        INSERT INTO restaurants (name, owner_id, address, description, status, created_at)
        VALUES (?, ?, ?, ?, 'active', NOW())
      ");
      $insertRestaurant->execute([$req['restaurant_name'], $owner_id, $req['address'], $req['description']]);

      // ✅ تحديث حالة الطلب
      $update = $pdo->prepare("
        UPDATE restaurant_requests 
        SET status = 'approved', reviewed_by = ?, reviewed_at = NOW()
        WHERE id = ?
      ");
      $update->execute([$_SESSION['user_id'], $id]);

      $pdo->commit();
      echo json_encode(['status' => 'success', 'message' => '✅ تم قبول الطلب وإنشاء الحساب والمطعم بنجاح']);
    } catch (Exception $e) {
      $pdo->rollBack();
      echo json_encode(['status' => 'error', 'message' => '⚠️ خطأ أثناء العملية: ' . $e->getMessage()]);
    }
  } elseif ($action === 'reject') {
    $pdo->prepare("
      UPDATE restaurant_requests 
      SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW() 
      WHERE id = ?
    ")->execute([$_SESSION['user_id'], $id]);

    echo json_encode(['status' => 'success', 'message' => '❌ تم رفض الطلب بنجاح']);
  }
  exit;
}

$requests = $pdo->query("SELECT * FROM restaurant_requests ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// إحصائيات الطلبات
$total_requests = $pdo->query("SELECT COUNT(*) FROM restaurant_requests")->fetchColumn();
$pending_requests = $pdo->query("SELECT COUNT(*) FROM restaurant_requests WHERE status = 'pending'")->fetchColumn();
$approved_requests = $pdo->query("SELECT COUNT(*) FROM restaurant_requests WHERE status = 'approved'")->fetchColumn();
$rejected_requests = $pdo->query("SELECT COUNT(*) FROM restaurant_requests WHERE status = 'rejected'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>طلبات تسجيل المتاجر - زاد</title>
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
      background-color: #f5f7f9;
      color: #333;
      overflow-x: hidden;
      padding-right: 260px;
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

    .user-info {
      display: flex;
      align-items: center;
    }

    .user-avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--main-color), var(--gold));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      margin-left: 15px;
      box-shadow: 0 4px 12px rgba(28, 51, 47, 0.2);
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
      margin-bottom: 30px;
      flex-wrap: wrap;
      gap: 20px;
    }

    .content-header h2 {
      color: var(--main-color);
      font-weight: 800;
      margin: 0;
      font-size: 1.7rem;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    /* أدوات التحكم */
    .controls-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
      flex-wrap: wrap;
      gap: 15px;
    }

    /* شريط البحث */
    .search-container {
      position: relative;
      min-width: 350px;
    }

    .search-box {
      border-radius: 12px;
      padding: 14px 50px 14px 20px;
      border: 1px solid #e8e8e8;
      transition: var(--transition);
      background-color: #fafafa;
      font-size: 0.95rem;
    }

    .search-box:focus {
      border-color: var(--gold);
      box-shadow: 0 0 0 0.2rem rgba(198, 163, 79, 0.15);
      background-color: white;
    }

    .search-icon {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
    }

    /* فلتر الحالة */
    .filter-select {
      border-radius: 12px;
      padding: 14px 20px;
      border: 1px solid #e8e8e8;
      background-color: #fafafa;
      min-width: 180px;
      transition: var(--transition);
    }

    .filter-select:focus {
      border-color: var(--gold);
      box-shadow: 0 0 0 0.2rem rgba(198, 163, 79, 0.15);
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

    /* أزرار الإجراءات */
    .action-buttons {
      display: flex;
      gap: 8px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn-action {
      padding: 10px 16px;
      border-radius: 10px;
      font-size: 0.85rem;
      font-weight: 500;
      transition: var(--transition);
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      min-width: 90px;
      justify-content: center;
    }

    .btn-view {
      background-color: rgba(13, 110, 253, 0.12);
      color: #0d6efd;
      border: 1px solid rgba(13, 110, 253, 0.2);
    }

    .btn-view:hover {
      background-color: #0d6efd;
      color: white;
      transform: translateY(-2px);
    }

    .btn-approve {
      background-color: rgba(25, 135, 84, 0.12);
      color: #198754;
      border: 1px solid rgba(25, 135, 84, 0.2);
    }

    .btn-approve:hover {
      background-color: #198754;
      color: white;
      transform: translateY(-2px);
    }

    .btn-reject {
      background-color: rgba(220, 53, 69, 0.12);
      color: #dc3545;
      border: 1px solid rgba(220, 53, 69, 0.2);
    }

    .btn-reject:hover {
      background-color: #dc3545;
      color: white;
      transform: translateY(-2px);
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

    .status-approved {
      background-color: rgba(25, 135, 84, 0.12);
      color: #198754;
      border: 1px solid rgba(25, 135, 84, 0.2);
    }

    .status-rejected {
      background-color: rgba(220, 53, 69, 0.12);
      color: #dc3545;
      border: 1px solid rgba(220, 53, 69, 0.2);
    }

    /* النماذج (Modals) المحسنة */
    .modal-header {
      background: linear-gradient(135deg, var(--main-color), #152622);
      color: white;
      border-bottom: none;
      border-radius: 16px 16px 0 0;
      padding: 25px 30px;
    }

    .modal-title {
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 1.3rem;
    }

    .modal-content {
      border-radius: 16px;
      border: none;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
      overflow: hidden;
    }

    .modal-body {
      padding: 30px;
    }

    .modal-footer {
      border-top: 1px solid #eee;
      padding: 25px 30px;
      background: #fafafa;
    }

    /* التوست (الإشعارات) */
    .toast-container {
      z-index: 1055;
    }

    .toast {
      border-radius: 12px;
      border: none;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
      overflow: hidden;
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
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .sidebar-toggle {
        display: block;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--main-color);
      }

      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .controls-row {
        flex-direction: column;
        align-items: stretch;
      }

      .search-container {
        width: 100%;
      }

      .action-buttons {
        flex-direction: column;
        width: 100%;
      }

      .btn-action {
        min-width: 100%;
      }
    }

    @media (max-width: 768px) {
      .header-bar {
        flex-direction: column;
        align-items: flex-start;
        padding: 20px;
      }

      .user-info {
        margin-top: 15px;
        width: 100%;
        justify-content: flex-end;
      }

      .content-header {
        flex-direction: column;
        align-items: flex-start;
      }

      .stats-grid {
        grid-template-columns: 1fr;
      }

      .search-container {
        min-width: 100%;
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
      .modal-body {
        padding: 20px;
      }
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
    }

    .sidebar-toggle:hover {
      background-color: rgba(28, 51, 47, 0.1);
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
  <!-- ✅ القائمة الجانبية -->
  <?php include __DIR__ . '/includes/sidebar_admin.php'; ?>

  <!-- المحتوى الرئيسي -->
  <div class="main-content">
    <!-- شريط العنوان -->
    <div class="header-bar">
      <div class="d-flex align-items-center">
        <button class="sidebar-toggle me-3">
          <i class="fas fa-bars"></i>
        </button>
        <h1><i class="fas fa-clipboard-list text-gold"></i> طلبات تسجيل المتاجر</h1>
      </div>
      <div class="user-info">
        <div class="user-avatar">
          <?= strtoupper(substr($_SESSION['name'], 0, 1)) ?>
        </div>
        <div class="text-start">
          <div class="fw-bold"><?= htmlspecialchars($_SESSION['name']) ?></div>
          <small class="text-muted">مدير النظام</small>
        </div>
      </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="card-content">
          <h3><?= $total_requests ?></h3>
          <p>إجمالي الطلبات</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-clock"></i>
        </div>
        <div class="card-content">
          <h3><?= $pending_requests ?></h3>
          <p>طلبات قيد المراجعة</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="card-content">
          <h3><?= $approved_requests ?></h3>
          <p>طلبات مقبولة</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-times-circle"></i>
        </div>
        <div class="card-content">
          <h3><?= $rejected_requests ?></h3>
          <p>طلبات مرفوضة</p>
        </div>
      </div>
    </div>

    <!-- بطاقة المحتوى الرئيسية -->
    <div class="content-card">
      <div class="content-header">
        <h2><i class="fas fa-list-alt text-gold"></i> قائمة طلبات التسجيل</h2>
      </div>

      <!-- أدوات البحث والتصفية -->
      <div class="controls-row">
        <div class="search-container">
          <input type="text" id="searchBox" class="form-control search-box" placeholder="ابحث باسم المالك أو المطعم ...">
          <i class="fas fa-search search-icon"></i>
        </div>

        <select id="statusFilter" class="form-select filter-select">
          <option value="">جميع الحالات</option>
          <option value="pending">قيد المراجعة</option>
          <option value="approved">مقبولة</option>
          <option value="rejected">مرفوضة</option>
        </select>
      </div>

      <!-- جدول الطلبات -->
      <div class="table-container">
        <div class="table-responsive">
          <table class="table table-hover" id="requestsTable">
            <thead>
              <tr>
                <th>#</th>
                <th>المالك</th>
                <th>المطعم</th>
                <th>البريد الإلكتروني</th>
                <th>رقم الجوال</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($requests): ?>
                <?php foreach ($requests as $r): ?>
                  <tr data-status="<?= $r['status'] ?>" id="row-<?= $r['id'] ?>">
                    <td class="fw-bold"><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['owner_name']) ?></td>
                    <td><?= htmlspecialchars($r['restaurant_name']) ?></td>
                    <td><?= htmlspecialchars($r['email']) ?></td>
                    <td><?= htmlspecialchars($r['phone']) ?></td>
                    <td>
                      <span class="status-badge status-<?= $r['status'] ?>">
                        <i class="fas fa-<?= $r['status'] === 'pending' ? 'clock' : ($r['status'] === 'approved' ? 'check-circle' : 'times-circle') ?>"></i>
                        <?= $r['status'] === 'pending' ? 'قيد المراجعة' : ($r['status'] === 'approved' ? 'تم القبول' : 'مرفوض') ?>
                      </span>
                    </td>
                    <td>
                      <div class="action-buttons">
                        <button class="btn-action btn-view view-details" data-request='<?= json_encode($r, JSON_UNESCAPED_UNICODE) ?>'>
                          <i class="fas fa-eye"></i>
                          عرض
                        </button>
                        <?php if ($r['status'] === 'pending'): ?>
                          <button class="btn-action btn-approve approve" data-id="<?= $r['id'] ?>">
                            <i class="fas fa-check"></i>
                            قبول
                          </button>
                          <button class="btn-action btn-reject reject" data-id="<?= $r['id'] ?>">
                            <i class="fas fa-times"></i>
                            رفض
                          </button>
                        <?php else: ?>
                          <span class="text-muted">—</span>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="no-data">
                    <i class="fas fa-clipboard-list"></i>
                    <h5 class="mt-3">لا توجد طلبات</h5>
                    <p class="text-muted">لم يتم تقديم أي طلبات تسجيل متاجر جديدة</p>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ Toast Container -->
  <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer"></div>

  <!-- 🟢 نافذة عرض التفاصيل -->
  <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i> تفاصيل الطلب</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="detailsContent"></div>
          <div class="text-center mt-4" id="modalActions">
            <button class="btn-action btn-approve confirm-action" data-action="approve">
              <i class="fas fa-check"></i>
              قبول الطلب
            </button>
            <button class="btn-action btn-reject confirm-action" data-action="reject">
              <i class="fas fa-times"></i>
              رفض الطلب
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ⚠️ نافذة تأكيد العملية -->
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center">
        <div class="modal-body p-4">
          <div class="mb-4">
            <i class="fas fa-question-circle text-warning" style="font-size: 3rem;"></i>
          </div>
          <h5 class="mb-3" id="confirmMessage"></h5>
          <div class="d-flex justify-content-center gap-3">
            <button type="button" class="btn-action btn-approve" id="confirmYes">
              <i class="fas fa-check"></i>
              تأكيد
            </button>
            <button type="button" class="btn-action btn-view" data-bs-dismiss="modal">
              <i class="fas fa-times"></i>
              إلغاء
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    let selectedId = null,
      selectedAction = null;

    // تبديل الشريط الجانبي على الشاشات الصغيرة فقط
    if (window.innerWidth <= 992) {
      document.querySelector('.sidebar-toggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('active');
      });
    }

    // ✅ دالة عرض التوست
    function showToast(message, type = 'success') {
      const toast = `
        <div class="toast align-items-center text-white bg-${type} border-0 mb-2" role="alert">
          <div class="d-flex">
            <div class="toast-body">
              <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
              ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>
        </div>`;
      $('#toastContainer').append(toast);
      const el = $('#toastContainer .toast').last()[0];
      new bootstrap.Toast(el, {
        delay: 3000
      }).show();
    }

    // 🔍 البحث الفوري
    $('#searchBox').on('keyup', function() {
      const value = $(this).val().toLowerCase();
      $('#requestsTable tbody tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });

    // 🎛️ فلترة حسب الحالة
    $('#statusFilter').on('change', function() {
      const status = $(this).val();
      if (status === '') {
        $('#requestsTable tbody tr').show();
      } else {
        $('#requestsTable tbody tr').hide();
        $(`#requestsTable tbody tr[data-status="${status}"]`).show();
      }
    });

    // ✅ عرض تفاصيل الطلب
    $(document).on('click', '.view-details', function() {
      const data = $(this).data('request');
      selectedId = data.id;

      // إظهار أو إخفاء أزرار القبول والرفض بناءً على الحالة
      if (data.status === 'pending') {
        $('#modalActions').show();
      } else {
        $('#modalActions').hide();
      }

      let html = `
        <div class="row g-4">
          <div class="col-md-6">
            <div class="detail-item">
              <label class="form-label"><i class="fas fa-user me-2"></i> اسم المالك</label>
              <div class="form-control bg-light">${data.owner_name}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="detail-item">
              <label class="form-label"><i class="fas fa-envelope me-2"></i> البريد الإلكتروني</label>
              <div class="form-control bg-light">${data.email}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="detail-item">
              <label class="form-label"><i class="fas fa-phone me-2"></i> رقم الجوال</label>
              <div class="form-control bg-light">${data.phone}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="detail-item">
              <label class="form-label"><i class="fas fa-store me-2"></i> اسم المطعم</label>
              <div class="form-control bg-light">${data.restaurant_name}</div>
            </div>
          </div>
          <div class="col-12">
            <div class="detail-item">
              <label class="form-label"><i class="fas fa-map-marker-alt me-2"></i> العنوان</label>
              <div class="form-control bg-light">${data.address}</div>
            </div>
          </div>
          <div class="col-12">
            <div class="detail-item">
              <label class="form-label"><i class="fas fa-file-alt me-2"></i> الوصف</label>
              <textarea class="form-control bg-light" rows="3" readonly>${data.description || '—'}</textarea>
            </div>
          </div>
          <div class="col-md-6">
            <div class="detail-item">
              <label class="form-label"><i class="fas fa-calendar me-2"></i> تاريخ الطلب</label>
              <div class="form-control bg-light">${data.created_at}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="detail-item">
              <label class="form-label"><i class="fas fa-info-circle me-2"></i> الحالة</label>
              <div class="form-control bg-light">
                <span class="status-badge status-${data.status}">
                  ${data.status === 'pending' ? 'قيد المراجعة' : (data.status === 'approved' ? 'تم القبول' : 'مرفوض')}
                </span>
              </div>
            </div>
          </div>
        </div>`;

      $('#detailsContent').html(html);
      $('#detailsModal').modal('show');
    });

    // ✅ تأكيد العملية من داخل الجدول أو المودال
    $(document).on('click', '.approve, .reject, .confirm-action', function() {
      selectedId = $(this).data('id') || selectedId;
      selectedAction = $(this).hasClass('approve') || $(this).data('action') === 'approve' ? 'approve' : 'reject';

      $('#confirmMessage').text(`هل تريد ${selectedAction === 'approve' ? 'قبول' : 'رفض'} هذا الطلب؟`);
      $('#confirmModal').modal('show');
    });

    // ✅ تنفيذ العملية بعد التأكيد
    $('#confirmYes').on('click', function() {
      $('#confirmModal').modal('hide');
      $('#detailsModal').modal('hide');

      const button = $(this);
      const originalText = button.html();

      button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> جاري المعالجة...');

      $.post('', {
        id: selectedId,
        action: selectedAction
      }, function(res) {
        if (res.status === 'success') {
          showToast(res.message, 'success');
          setTimeout(() => {
            $('#row-' + selectedId).fadeOut(400, function() {
              $(this).remove();
            });
          }, 1000);
        } else {
          showToast(res.message, 'danger');
        }
      }, 'json').fail(() => {
        showToast('⚠️ حدث خطأ في الاتصال', 'danger');
      }).always(() => {
        button.prop('disabled', false).html(originalText);
      });
    });

    // إغلاق الشريط الجانبي عند النقر خارجيه على الشاشات الصغيرة
    if (window.innerWidth <= 992) {
      document.addEventListener('click', function(e) {
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.querySelector('.sidebar-toggle');
        if (sidebar.classList.contains('active') &&
          !sidebar.contains(e.target) &&
          !toggleBtn.contains(e.target)) {
          sidebar.classList.remove('active');
        }
      });
    }
  </script>
</body>

</html>