<?php
session_start();
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'ar';

$content = [
  'ar' => [
    'title' => 'المساعدة والدعم',
    'subtitle' => 'هنا ستجد إجابات للأسئلة الشائعة أو يمكنك التواصل معنا.',
    'faq' => [
      ['q' => 'س: كيف أجد ساعات مكتب المحاضر؟', 'a' => 'استخدم صفحة "جدول المحاضر".'],
      ['q' => 'س: كيف أضيف جدولي كمحاضر؟', 'a' => 'قم بتسجيل الدخول من "دخول المحاضرين" ثم أضف الجدول.'],
      ['q' => 'س: لم يصلني كود التحقق (OTP)؟', 'a' => 'تحقق من بريدك الإلكتروني ومن مجلد الرسائل المزعجة.']
    ],
    'home' => 'العودة للرئيسية',
    'support' => 'الدعم الفني',
    'switch' => 'English'
  ],
  'en' => [
    'title' => 'Help & Support',
    'subtitle' => 'Find answers to common questions or contact us.',
    'faq' => [
      ['q' => 'Q: How do I find a lecturer\'s schedule?', 'a' => 'Use the "Find Lecturer schedule" page to search by name.'],
      ['q' => 'Q: How do I add my schedule as a lecturer?', 'a' => 'Click the "Are you a lecturer? Add your schedule here" button in Find Lecturer Schedule page and add your schedule.'],
      ['q' => 'Q: Where is my OTP code?', 'a' => 'Check your email inbox and spam folder.']
    ],
    'home' => 'Back to Home',
    'support' => 'Contact Support',
    'switch' => 'عربي'
  ]
];

$c = $content[$lang];
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= $c['title'] ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap<?= $lang === 'ar' ? '.rtl' : '' ?>.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #f9faff 0%, #f0ebff 100%);
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      overflow-x: hidden;
    }

    .help-container {
      max-width: 800px;
      margin: auto;
      padding: 60px 20px;
      text-align: center;
    }

    .help-title {
      font-size: 36px;
      font-weight: bold;
      color: #2d2d2d;
      margin-bottom: 10px;
    }

    .help-subtitle {
      font-size: 18px;
      color: #555;
      margin-bottom: 40px;
    }

    .faq-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.06);
      padding: 20px;
      margin-bottom: 20px;
      font-size: 17px;
      color: #333;
      text-align: <?= $lang === 'ar' ? 'right' : 'left' ?>;
      animation: fadeInUp 0.6s ease forwards;
      opacity: 0;
    }

    .faq-card strong {
      display: block;
      margin-bottom: 6px;
      color: #5b2bd1;
    }

    .help-buttons {
      margin-top: 30px;
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }

    .help-buttons a {
      text-decoration: none;
      padding: 10px 24px;
      border-radius: 8px;
      font-weight: 500;
      color: white;
      transition: 0.3s ease;
    }

    .btn-home {
      background-color: #5b2bd1;
    }

    .btn-support {
      background-color: #444d5e;
    }

    .help-buttons a:hover {
      opacity: 0.9;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 600px) {
      .help-title { font-size: 28px; }
      .faq-card { font-size: 15px; }
    }
  </style>
</head>
<body>

<div class="help-container">
  <h1 class="help-title"><?= $c['title'] ?></h1>
  <p class="help-subtitle"><?= $c['subtitle'] ?></p>

  <?php foreach ($c['faq'] as $f): ?>
    <div class="faq-card">
      <strong><?= $f['q'] ?></strong>
      <?= $f['a'] ?>
    </div>
  <?php endforeach; ?>

  <div class="help-buttons">
    <a href="index.php" class="btn-home"><?= $c['home'] ?></a>
    <a href="mailto:support@campusway.edu" class="btn-support"><?= $c['support'] ?></a>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
