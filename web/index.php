<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>زاد - نظام إدارة المطاعم والطلبات</title>

  <!-- Bootstrap & AOS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      background-color: #f8f9fa;
      color: #1C332F;
      overflow-x: hidden;
    }

    :root {
      --main-color: #1C332F;
      --gold: #C6A34F;
    }

    .navbar {
      background-color: rgba(28, 51, 47, 0.95);
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
      color: var(--gold) !important;
      font-weight: bold;
      font-size: 1.7rem;
    }

    .navbar a.nav-link {
      color: white !important;
      margin: 0 8px;
      transition: 0.3s;
    }

    .navbar a.nav-link:hover {
      color: var(--gold) !important;
    }

    /* 🖼️ Slider */
    .carousel-item {
      height: 90vh;
      background-position: center;
      background-size: cover;
      position: relative;
    }

    .carousel-caption {
      background: rgba(28, 51, 47, 0.6);
      backdrop-filter: blur(6px);
      border-radius: 12px;
      padding: 2rem;
    }

    .carousel-caption h2 {
      color: var(--gold);
      font-weight: 700;
    }

    /* 💡 Features */
    .feature-card {
      background: rgba(255, 255, 255, 0.8);
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      transition: 0.3s;
    }

    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
    }

    .feature-icon {
      color: var(--gold);
      font-size: 2.5rem;
      margin-bottom: 1rem;
    }

    /* 📱 App Section */
    .app-section {
      background: linear-gradient(135deg, #1C332F, #28473C);
      color: white;
      padding: 80px 0;
    }

    .app-section h2 {
      color: var(--gold);
    }

    .app-section img {
      max-width: 90%;
      border-radius: 20px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }

    /* 📢 Ads */
    .ad-banner img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      border-radius: 14px;
      transition: 0.3s;
    }

    .ad-banner img:hover {
      transform: scale(1.05);
    }

    /* 🏪 تسجيل المتاجر */
    .register-section {
      background: #fefdf9;
      padding: 80px 0;
    }

    .register-section h2 {
      color: var(--main-color);
      font-weight: 700;
    }

    .register-section form {
      background: rgba(255, 255, 255, 0.85);
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      padding: 40px;
    }

    .register-section .btn {
      background: var(--main-color);
      color: white;
      font-size: 1.1rem;
      border-radius: 10px;
    }

    .register-section .btn:hover {
      background: #10221B;
    }

    /* ⚫ Footer */
    footer {
      background: #1C332F;
      color: #ccc;
      padding: 30px 0;
      text-align: center;
    }

    footer span {
      color: var(--gold);
    }
  </style>
</head>

<body>
  <!-- 🔝 Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">زاد</a>
      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a href="#home" class="nav-link">الرئيسية</a></li>
          <li class="nav-item"><a href="#features" class="nav-link">المميزات</a></li>
          <li class="nav-item"><a href="#register" class="nav-link">انضم كمطعم</a></li>
          <li class="nav-item"><a href="admin/login.php" class="nav-link btn btn-outline-light px-3 ms-2">دخول التاجر</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- 🌟 HERO SECTION -->
  <section id="home" class="hero d-flex align-items-center" style="min-height: 90vh; background: linear-gradient(135deg,#1C332F,#28473C); color: white; overflow: hidden;">
    <div class="container text-center text-md-end">
      <div class="row align-items-center">
        <!-- نص الإعلان -->
        <div class="col-md-6" data-aos="fade-left">
          <h1 class="fw-bold mb-3" style="color: var(--gold); font-size: 2.5rem;">زاد - إدارة مطعمك بكل سهولة</h1>
          <p class="mb-4 fs-5 text-light">نظام متكامل يربطك بعملائك، ويتابع الطلبات لحظيًا من خلال لوحة تحكم ذكية وتطبيق عميل أنيق.</p>
          <div class="d-flex gap-3 justify-content-md-start justify-content-center">
            <a href="#register" class="btn btn-light px-4 py-2 fw-bold">تسجيل مطعم جديد</a>
            <a href="#" class="btn btn-outline-light px-4 py-2"><i class="fa-brands fa-google-play me-2"></i>تنزيل التطبيق</a>
          </div>
        </div>

        <!-- صورة التطبيق -->
        <div class="col-md-6 text-center mt-5 mt-md-0" data-aos="fade-right">
          <img src="assets/images/app_mockup.png" alt="ZAD App Mockup" class="img-fluid" style="max-width: 380px; border-radius: 25px; box-shadow: 0 8px 25px rgba(0,0,0,0.3);">
        </div>
      </div>
    </div>
  </section>

  <!-- 📢 قسم البنرات الإعلانية -->
  <section id="ads" class="py-5 bg-light">
    <div class="container">
      <h2 class="text-center fw-bold mb-5" style="color:#1C332F;">عروض ومزايا زاد</h2>
      <div class="row g-4">
        <div class="col-md-4" data-aos="fade-up">
          <div class="ad-banner shadow-sm overflow-hidden">
            <img src="assets/images/app_mockup.png" alt="عرض رقم 1">
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="150">
          <div class="ad-banner shadow-sm overflow-hidden">
            <img src="assets/images/ad2.jpg" alt="عرض رقم 2">
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
          <div class="ad-banner shadow-sm overflow-hidden">
            <img src="assets/images/ad3.jpg" alt="عرض رقم 3">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 🖼️ Slider -->
  <!-- <section id="home" class="mt-2 pt-2">
    <div id="heroSlider" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active" style="background-image:url('assets/images/banner1.jpg')">
          <div class="carousel-caption">
            <h2>زاد - إدارة مطعمك بكل سهولة</h2>
            <p>منصة ذكية تربطك بعملائك وتتابع طلباتك لحظيًا.</p>
          </div>
        </div>
        <div class="carousel-item" style="background-image:url('assets/img/banner2.jpg')">
          <div class="carousel-caption">
            <h2>نظام مرن وقابل للتوسعة</h2>
            <p>تحكم كامل بالمنتجات والطلبات من لوحة واحدة.</p>
          </div>
        </div>
        <div class="carousel-item" style="background-image:url('assets/img/banner3.jpg')">
          <div class="carousel-caption">
            <h2>تجربة حديثة وأداء سريع</h2>
            <p>واجهة استخدام مريحة وسريعة الأداء.</p>
          </div>
        </div>
      </div>
      <button class="carousel-control-prev" data-bs-target="#heroSlider" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
      <button class="carousel-control-next" data-bs-target="#heroSlider" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
    </div>
  </section> -->

  <!-- 💡 Features -->
  <section id="features" class="py-5">
    <div class="container text-center">
      <h2 class="mb-5 fw-bold">مميزات زاد</h2>
      <div class="row g-4">
        <div class="col-md-3">
          <div class="feature-card p-4"><i class="fa-solid fa-bolt feature-icon"></i>
            <h5>تحديث لحظي</h5>
            <p class="text-muted">نظام SSE لتحديث الطلبات في الوقت الفعلي.</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="feature-card p-4"><i class="fa-solid fa-chart-line feature-icon"></i>
            <h5>لوحة تحكم قوية</h5>
            <p class="text-muted">تحكم كامل في المستخدمين، المطاعم، والمنتجات.</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="feature-card p-4"><i class="fa-solid fa-mobile-screen-button feature-icon"></i>
            <h5>تطبيق متكامل</h5>
            <p class="text-muted">واجهة عميل عبر Flutter لربط الزبائن مباشرة.</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="feature-card p-4"><i class="fa-solid fa-lock feature-icon"></i>
            <h5>حماية عالية</h5>
            <p class="text-muted">نظام أمان وصلاحيات دقيق.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 🏪 تسجيل المتاجر -->
  <section id="register" class="register-section">
    <div class="container">
      <h2 class="text-center mb-4">انضم إلى زاد وابدأ إدارتك الآن</h2>
      <p class="text-center mb-5 text-muted">املأ البيانات التالية لإرسال طلب تسجيل مطعمك، وسيقوم فريق الإدارة بمراجعتها.</p>
      <form id="registerForm" method="post" class="mx-auto" style="max-width: 800px;">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">اسم المالك</label>
            <input type="text" name="owner_name" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">رقم الجوال</label>
            <input type="text" name="phone" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">البريد الإلكتروني</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">كلمة المرور</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">اسم المطعم</label>
            <input type="text" name="restaurant_name" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">العنوان</label>
            <input type="text" name="address" class="form-control" required>
          </div>
          <div class="col-12">
            <label class="form-label">وصف مختصر</label>
            <textarea name="description" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="text-center mt-4">
          <button type="submit" class="btn px-5">إرسال الطلب</button>
        </div>
        <div id="registerResult" class="text-center mt-3"></div>
      </form>
    </div>
  </section>

  <!-- ⚫ Footer -->
  <footer>
    <p>© 2025 جميع الحقوق محفوظة لـ <span>زاد</span></p>
    <small>Powered by <span>MUGRVERSE</span></small>
  </footer>

  <!-- 🎵 صوت نجاح -->
  <audio id="successSound" src="assets/sounds/success.mp3" preload="auto"></audio>

  <!-- ✅ نافذة منبثقة لنجاح الإرسال -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4" style="border-radius: 16px;">
        <div class="modal-body">
          <i class="fa-solid fa-circle-check text-success mb-3" style="font-size: 3rem;"></i>
          <h4 class="fw-bold mb-2">تم إرسال طلبك بنجاح 🎉</h4>
          <p class="text-muted mb-4">سيتم مراجعة الطلب والتواصل معك قريبًا عبر البريد أو الهاتف.</p>
          <button type="button" class="btn btn-dark px-4" data-bs-dismiss="modal">إغلاق</button>
        </div>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <script>
    AOS.init({
      duration: 1000,
      once: true
    });

    // ✅ إرسال الطلب عبر AJAX
    $('#registerForm').on('submit', function(e) {
      e.preventDefault();
      $('#registerResult').html('<div class="text-muted">⏳ جاري الإرسال...</div>');

      $.ajax({
        url: 'admin/api/register_store.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
          if (res.status === 'success') {
            $('#successSound')[0].play();
            $('#successModal').modal('show');
            $('#registerForm')[0].reset();
            $('#registerResult').html('');
          } else {
            $('#registerResult').html('<div class="text-danger mt-2">❌ ' + res.message + '</div>');
          }
        },
        error: function(xhr) {
          console.log(xhr.responseText);
          $('#registerResult').html('<div class="text-danger mt-2">⚠️ حدث خطأ أثناء الإرسال</div>');
        }
      });
    });
  </script>

</body>

</html>