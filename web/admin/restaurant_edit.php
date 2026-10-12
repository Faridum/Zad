<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

// ✅ السماح فقط للمالك
if ($_SESSION['role'] !== 'owner') {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// ✅ جلب بيانات المطعم
$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE owner_id = ?");
$stmt->execute([$user_id]);
$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$restaurant) {
  echo "<div style='text-align:center;margin-top:50px;font-family:Tajawal'>🚫 لا يوجد مطعم مرتبط بحسابك.</div>";
  exit;
}

// ✅ تقسيم ساعات العمل الحالية (إن وجدت)
$working_hours = explode(' - ', $restaurant['working_hours'] ?? '');
$open_time = $working_hours[0] ?? '';
$close_time = $working_hours[1] ?? '';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تعديل بيانات المطعم - زاد</title>
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
      padding: 40px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
      border: 1px solid rgba(0, 0, 0, 0.05);
      margin-bottom: 30px;
    }

    /* رأس الصفحة */
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 40px;
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

    /* النموذج المحسن */
    .form-section {
      margin-bottom: 40px;
    }

    .section-title {
      color: var(--main-color);
      font-weight: 700;
      font-size: 1.4rem;
      margin-bottom: 25px;
      padding-bottom: 15px;
      border-bottom: 2px solid rgba(198, 163, 79, 0.2);
      display: flex;
      align-items: center;
      gap: 10px;
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

    .input-group-text {
      background: linear-gradient(135deg, var(--main-color) 0%, #152622 100%);
      color: white;
      border: none;
      border-radius: 12px;
    }

    /* رفع الصورة */
    .image-upload-container {
      position: relative;
      border: 2px dashed #dee2e6;
      border-radius: 16px;
      padding: 30px;
      text-align: center;
      transition: var(--transition);
      background: #f8f9fa;
      cursor: pointer;
    }

    .image-upload-container:hover {
      border-color: var(--gold);
      background: rgba(198, 163, 79, 0.05);
    }

    .image-upload-container.dragover {
      border-color: var(--gold);
      background: rgba(198, 163, 79, 0.1);
    }

    .upload-icon {
      font-size: 3rem;
      color: var(--gold);
      margin-bottom: 15px;
    }

    .image-preview {
      max-width: 200px;
      max-height: 200px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      border: 3px solid var(--white);
      display: none;
    }

    /* ساعات العمل */
    .time-inputs {
      display: flex;
      gap: 15px;
      align-items: end;
    }

    .time-input-group {
      flex: 1;
    }

    .time-separator {
      color: var(--gold);
      font-weight: bold;
      font-size: 1.2rem;
      padding-bottom: 12px;
    }

    /* الأزرار */
    .btn-primary {
      background: linear-gradient(135deg, var(--main-color) 0%, #152622 100%);
      border: none;
      border-radius: 12px;
      padding: 14px 30px;
      font-weight: 600;
      font-size: 1rem;
      transition: var(--transition);
      box-shadow: 0 4px 15px rgba(28, 51, 47, 0.2);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(28, 51, 47, 0.3);
    }

    .btn-outline-primary {
      border: 2px solid var(--main-color);
      color: var(--main-color);
      border-radius: 12px;
      padding: 12px 25px;
      font-weight: 600;
      transition: var(--transition);
    }

    .btn-outline-primary:hover {
      background: var(--main-color);
      color: white;
      transform: translateY(-2px);
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
        padding: 30px;
      }

      .page-header {
        flex-direction: column;
        align-items: flex-start;
      }

      .time-inputs {
        flex-direction: column;
        gap: 10px;
      }

      .time-separator {
        text-align: center;
        padding: 0;
      }
    }

    @media (max-width: 768px) {
      .content-card {
        padding: 25px;
      }

      .page-title {
        font-size: 1.8rem;
      }

      .page-title i {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
      }

      .form-section {
        margin-bottom: 30px;
      }
    }

    @media (max-width: 576px) {
      .main-content {
        padding: 15px;
      }

      .content-card {
        padding: 20px;
      }

      .page-title {
        font-size: 1.6rem;
      }

      .info-card {
        padding: 20px;
      }

      .image-upload-container {
        padding: 20px;
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
            <i class="fas fa-store"></i>
            تعديل بيانات المطعم
          </h1>
        </div>
      </div>

      <!-- نموذج التعديل -->
      <div class="content-card">
        <form id="editRestaurantForm" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?= htmlspecialchars($restaurant['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

          <!-- القسم الأول: المعلومات الأساسية -->
          <div class="form-section">
            <h3 class="section-title">
              <i class="fas fa-info-circle text-gold"></i>
              المعلومات الأساسية
            </h3>

            <div class="row g-4">
              <div class="col-md-6">
                <label class="form-label">اسم المطعم <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-store"></i></span>
                  <input type="text" name="name" value="<?= htmlspecialchars($restaurant['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control" required placeholder="أدخل اسم المطعم">
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label">رقم الهاتف</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-phone"></i></span>
                  <input type="text" name="phone" value="<?= htmlspecialchars($restaurant['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control" placeholder="أدخل رقم الهاتف">
                </div>
              </div>

              <div class="col-12">
                <label class="form-label">العنوان</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                  <input type="text" name="address" value="<?= htmlspecialchars($restaurant['address'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control" placeholder="أدخل عنوان المطعم">
                </div>
              </div>
            </div>
          </div>

          <!-- القسم الثاني: ساعات العمل -->
          <div class="form-section">
            <h3 class="section-title">
              <i class="fas fa-clock text-gold"></i>
              ساعات العمل
            </h3>

            <div class="row">
              <div class="col-md-8">
                <div class="time-inputs">
                  <div class="time-input-group">
                    <label class="form-label">وقت الفتح</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-door-open"></i></span>
                      <input type="time" name="open_time" value="<?= htmlspecialchars($open_time, ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                    </div>
                  </div>

                  <div class="time-separator">إلى</div>

                  <div class="time-input-group">
                    <label class="form-label">وقت الإغلاق</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-door-closed"></i></span>
                      <input type="time" name="close_time" value="<?= htmlspecialchars($close_time, ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- القسم الثالث: الشعار -->
          <div class="form-section">
            <h3 class="section-title">
              <i class="fas fa-image text-gold"></i>
              شعار المطعم
            </h3>

            <div class="row">
              <div class="col-md-6">
                <div class="image-upload-container" id="imageUploadContainer">
                  <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                  </div>
                  <h5>اسحب وأفلت الصورة هنا</h5>
                  <p class="text-muted">أو انقر لاختيار صورة</p>
                  <p class="small text-muted">(PNG, JPG - الحد الأقصى 5MB)</p>
                  <input type="file" name="logo" class="d-none" accept="image/*" id="logoInput">
                  <img src="<?= !empty($restaurant['logo']) ? '../uploads/restaurants/' . htmlspecialchars($restaurant['logo'], ENT_QUOTES, 'UTF-8') : '' ?>"
                    class="image-preview rounded shadow-sm" id="imagePreview"
                    style="object-fit:cover; <?= empty($restaurant['logo']) ? 'display:none;' : '' ?>">
                </div>
              </div>

              <div class="col-md-6">
                <div class="alert alert-info border-0 rounded-12">
                  <h6><i class="fas fa-lightbulb me-2"></i>نصائح للشعار:</h6>
                  <ul class="mb-0 mt-2 small">
                    <li>استخدم صورة ذات خلفية شفافة أو بيضاء</li>
                    <li>يفضل أن تكون الصورة مربعة الشكل</li>
                    <li>الحجم المثالي 500x500 بيكسل</li>
                    <li>تأكد من وضوح الشعار وجودته</li>
                  </ul>
                </div>

                <?php if (!empty($restaurant['logo'])): ?>
                  <div class="alert alert-warning border-0 rounded-12 mt-3">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>الشعار الحالي:</h6>
                    <p class="mb-0 small">سيتم استبدال الشعار الحالي بالشعار الجديد عند الحفظ.</p>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- أزرار الحفظ -->
          <div class="form-section text-start mt-5">
            <div class="d-flex gap-3 flex-wrap">
              <button type="submit" class="btn btn-primary px-5">
                <i class="fas fa-save me-2"></i>حفظ التعديلات
              </button>
              <button type="button" class="btn btn-outline-primary px-4" onclick="resetForm()">
                <i class="fas fa-undo me-2"></i>إعادة تعيين
              </button>
              <a href="dashboard.php" class="btn btn-outline-secondary px-4">
                <i class="fas fa-times me-2"></i>إلغاء
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ✅ Toast محسن -->
  <div class="position-fixed bottom-0 start-0 p-4" style="z-index: 1080">
    <div id="toastContainer"></div>
  </div>

  <script>
    // عناصر DOM
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const imageUploadContainer = document.getElementById('imageUploadContainer');
    const logoInput = document.getElementById('logoInput');
    const imagePreview = document.getElementById('imagePreview');

    // تبديل الشريط الجانبي
    function toggleSidebar() {
      sidebar.classList.toggle('active');
      sidebarOverlay.classList.toggle('active');
      document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }

    // إضافة event listeners
    sidebarToggle.addEventListener('click', toggleSidebar);
    sidebarOverlay.addEventListener('click', toggleSidebar);

    // رفع الصورة
    imageUploadContainer.addEventListener('click', () => logoInput.click());

    logoInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        if (file.size > 5 * 1024 * 1024) {
          showToast('❌ حجم الصورة كبير جداً. الحد الأقصى 5MB', 'error');
          return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
          imagePreview.src = e.target.result;
          imagePreview.style.display = 'block';
          imageUploadContainer.querySelector('h5').textContent = 'تم اختيار الصورة';
          imageUploadContainer.querySelector('p').textContent = 'انقر لتغيير الصورة';
        };
        reader.readAsDataURL(file);
      }
    });

    // سحب وإفلات الصورة
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      imageUploadContainer.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
      e.preventDefault();
      e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
      imageUploadContainer.addEventListener(eventName, () => {
        imageUploadContainer.classList.add('dragover');
      }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
      imageUploadContainer.addEventListener(eventName, () => {
        imageUploadContainer.classList.remove('dragover');
      }, false);
    });

    imageUploadContainer.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
      const dt = e.dataTransfer;
      const files = dt.files;
      logoInput.files = files;
      logoInput.dispatchEvent(new Event('change'));
    }

    // إعادة تعيين النموذج
    function resetForm() {
      if (confirm('هل تريد إعادة تعيين جميع الحقول إلى القيم الأصلية؟')) {
        document.getElementById('editRestaurantForm').reset();
        if (!imagePreview.src.includes('uploads/restaurants/')) {
          imagePreview.style.display = 'none';
          imageUploadContainer.querySelector('h5').textContent = 'اسحب وأفلت الصورة هنا';
          imageUploadContainer.querySelector('p').textContent = 'أو انقر لاختيار صورة';
        }
        showToast('🔄 تم إعادة تعيين النموذج', 'warning');
      }
    }

    // إرسال النموذج
    $(document).ready(function() {
      $('#editRestaurantForm').on('submit', function(e) {
        e.preventDefault();

        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>جاري الحفظ...');

        const formData = new FormData(this);
        // دمج ساعات العمل بصيغة واحدة
        const openTime = $('input[name="open_time"]').val();
        const closeTime = $('input[name="close_time"]').val();
        formData.append('working_hours', `${openTime} - ${closeTime}`);

        $.ajax({
          url: 'api/update_restaurant.php',
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          dataType: 'json',
          success: function(response) {
            if (response.status === 'success') {
              showToast('✅ ' + response.message, 'success');
              setTimeout(() => {
                window.location.href = 'dashboard.php';
              }, 1500);
            } else {
              showToast('⚠️ ' + response.message, 'warning');
              submitBtn.prop('disabled', false).html(originalText);
            }
          },
          error: function(xhr, status, error) {
            showToast('❌ حدث خطأ أثناء الحفظ: ' + error, 'error');
            submitBtn.prop('disabled', false).html(originalText);
          }
        });
      });
    });

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