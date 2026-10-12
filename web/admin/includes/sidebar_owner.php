<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// تأكيد أن المستخدم مالك
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
  header("Location: ../login.php");
  exit;
}

// تحديد الصفحة الحالية لتفعيل الرابط النشط
$current_page = basename($_SERVER['PHP_SELF']);

// جلب معلومات المطعم للمالك
require_once __DIR__ . '/../includes/db.php';
$owner_id = $_SESSION['user_id'];
$restaurant_stmt = $pdo->prepare("SELECT name FROM restaurants WHERE owner_id = ?");
$restaurant_stmt->execute([$owner_id]);
$restaurant = $restaurant_stmt->fetch(PDO::FETCH_ASSOC);
$restaurant_name = $restaurant ? $restaurant['name'] : 'مطعمك';
?>

<!-- 🟢 الشريط الجانبي المحسن للمالك -->
<aside class="sidebar">
  <!-- رأس الشريط الجانبي -->
  <!-- <div class="sidebar-header">
    <div class="logo-container">
      <i class="fas fa-utensils logo-icon"></i>
      <div class="logo-text">
        <h3>زاد</h3>
        <small>نظام إدارة المطاعم</small>
      </div>
    </div>
  </div> -->

  <!-- معلومات المالك -->
  <div class="user-info">
    <div class="user-avatar">
      <?= strtoupper(substr($_SESSION['name'], 0, 1)) ?>
    </div>
    <div class="user-details">
      <div class="user-name"><?= htmlspecialchars($_SESSION['name']) ?></div>
      <div class="user-role">مالك مطعم</div>
      <div class="restaurant-name"><?= htmlspecialchars($restaurant_name) ?></div>
    </div>
  </div>

  <!-- قائمة التنقل -->
  <nav class="sidebar-nav">
    <ul class="nav-links">
      <li>
        <a href="dashboard.php" class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
          <i class="fas fa-tachometer-alt"></i>
          <span class="link-text">لوحة التحكم</span>
        </a>
      </li>
      <li>
        <a href="restaurant_edit.php" class="nav-link <?= $current_page === 'restaurant_edit.php' ? 'active' : '' ?>">
          <i class="fas fa-store"></i>
          <span class="link-text">إدارة المطعم</span>
        </a>
      </li>
      <li>
        <a href="products.php" class="nav-link <?= $current_page === 'products.php' ? 'active' : '' ?>">
          <i class="fas fa-box"></i>
          <span class="link-text">المنتجات</span>
        </a>
      </li>
      <li>
        <a href="orders.php" class="nav-link <?= $current_page === 'orders.php' ? 'active' : '' ?>">
          <i class="fas fa-shopping-cart"></i>
          <span class="link-text">الطلبات</span>
          <span class="badge" id="orders-badge">0</span>
        </a>
      </li>
      <li>
        <a href="reports.php" class="nav-link <?= $current_page === 'reports.php' ? 'active' : '' ?>">
          <i class="fas fa-chart-bar"></i>
          <span class="link-text">التقارير</span>
        </a>
      </li>
      <li>
        <a href="settings.php" class="nav-link <?= $current_page === 'settings.php' ? 'active' : '' ?>">
          <i class="fas fa-cog"></i>
          <span class="link-text">الإعدادات</span>
        </a>
      </li>
    </ul>
  </nav>

  <!-- قسم تسجيل الخروج -->
  <div class="sidebar-footer">
    <a href="logout.php" class="logout-btn">
      <i class="fas fa-sign-out-alt"></i>
      <span>تسجيل الخروج</span>
    </a>
  </div>
</aside>

<style>
  :root {
    --main-color: #1C332F;
    --gold: #C6A34F;
    --light-bg: #f8f9fa;
    --white: #ffffff;
    --transition: all 0.3s ease;
  }

  .sidebar {
    width: 280px;
    height: 100vh;
    position: fixed;
    right: 0;
    top: 0;
    background: linear-gradient(180deg, var(--main-color) 0%, #152622 100%);
    color: white;
    display: flex;
    flex-direction: column;
    z-index: 1000;
    box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
    transition: var(--transition);
  }

  /* رأس الشريط الجانبي */
  .sidebar-header {
    padding: 25px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background-color: rgba(0, 0, 0, 0.1);
  }

  .logo-container {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .logo-icon {
    font-size: 2.2rem;
    color: var(--gold);
  }

  .logo-text h3 {
    margin: 0;
    color: var(--gold);
    font-weight: 800;
    font-size: 1.5rem;
  }

  .logo-text small {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.8rem;
  }

  /* معلومات المستخدم */
  .user-info {
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background-color: rgba(0, 0, 0, 0.05);
  }

  .user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--gold), #e6c878);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--main-color);
    font-weight: bold;
    font-size: 1.2rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  }

  .user-details {
    flex: 1;
  }

  .user-name {
    font-weight: 700;
    font-size: 1rem;
    margin-bottom: 2px;
  }

  .user-role {
    color: var(--gold);
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 2px;
  }

  .restaurant-name {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.8rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* قائمة التنقل */
  .sidebar-nav {
    flex: 1;
    padding: 20px 0;
    overflow-y: auto;
  }

  .nav-links {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .nav-links li {
    margin-bottom: 5px;
  }

  .nav-link {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 14px 20px;
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    transition: var(--transition);
    border-right: 3px solid transparent;
    position: relative;
  }

  .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.08);
    color: white;
    border-right-color: rgba(255, 255, 255, 0.2);
  }

  .nav-link.active {
    background-color: rgba(255, 255, 255, 0.12);
    color: white;
    border-right-color: var(--gold);
  }

  .nav-link i {
    font-size: 1.2rem;
    width: 24px;
    text-align: center;
    color: var(--gold);
  }

  .link-text {
    flex: 1;
    font-weight: 500;
    font-size: 0.95rem;
  }

  .badge {
    background-color: #e74c3c;
    color: white;
    border-radius: 20px;
    padding: 4px 8px;
    font-size: 0.75rem;
    font-weight: 600;
    min-width: 20px;
    text-align: center;
  }

  /* قسم تسجيل الخروج */
  .sidebar-footer {
    padding: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    background-color: rgba(0, 0, 0, 0.1);
  }

  .logout-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    border-radius: 8px;
    transition: var(--transition);
    background-color: rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.2);
  }

  .logout-btn:hover {
    background-color: rgba(220, 53, 69, 0.2);
    color: white;
    transform: translateY(-1px);
  }

  .logout-btn i {
    font-size: 1.1rem;
  }

  /* شريط التمرير المخصص */
  .sidebar-nav::-webkit-scrollbar {
    width: 6px;
  }

  .sidebar-nav::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
  }

  .sidebar-nav::-webkit-scrollbar-thumb {
    background: var(--gold);
    border-radius: 3px;
  }

  .sidebar-nav::-webkit-scrollbar-thumb:hover {
    background: #b8943a;
  }

  /* تحسينات للاستجابة */
  @media (max-width: 768px) {
    .sidebar {
      width: 260px;
      transform: translateX(100%);
    }

    .sidebar.active {
      transform: translateX(0);
    }
  }

  @media (max-width: 480px) {
    .sidebar {
      width: 100%;
    }

    .user-info {
      flex-direction: column;
      text-align: center;
      gap: 10px;
    }

    .user-details {
      text-align: center;
    }
  }
</style>

<script>
  // تحديث عدد الطلبات الجديدة (يمكن تعديل هذا الجزء حسب احتياجاتك)
  function updateOrdersBadge() {
    // هذا مثال - يمكنك استبداله بطلب AJAX حقيقي
    fetch('api/get_new_orders_count.php')
      .then(response => response.json())
      .then(data => {
        if (data.count > 0) {
          document.getElementById('orders-badge').textContent = data.count;
          document.getElementById('orders-badge').style.display = 'inline-block';
        } else {
          document.getElementById('orders-badge').style.display = 'none';
        }
      })
      .catch(error => {
        console.error('Error fetching orders count:', error);
        document.getElementById('orders-badge').style.display = 'none';
      });
  }

  // تحديث عدد الطلبات كل 30 ثانية
  setInterval(updateOrdersBadge, 30000);

  // تحديث عند تحميل الصفحة
  document.addEventListener('DOMContentLoaded', updateOrdersBadge);
</script>