<?php
session_start();
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'ar';

$content = [
  'ar' => [
    'title' => 'مرافق الحرم الجامعي',
    'subtitle' => 'استكشف المرافق المتوفرة في الحرم الجامعي',
    'facilities' => [
      '📚 المكتبة – المبنى A، الدور الثاني',
      '🍽️ الكافيتيريا – المبنى B، الدور الأرضي',
      '🛋️ صالة الطلاب – المبنى C، الدور الأول',
      '🏋️‍♂️ المركز الرياضي – المبنى D'
    ],
    'home' => 'العودة للرئيسية',
    'map' => 'عرض الخريطة'
  ],
  'en' => [
    'title' => 'Campus Facilities',
    'subtitle' => 'Explore the facilities available on campus',
    'facilities' => [
      '📚 Library – Building A, Floor 2',
      '🍽️ Cafeteria – Building B, Ground Floor',
      '🛋️ Student Lounge – Building C, Floor 1',
      '🏋️‍♂️ Sports Center – Building D'
    ],
    'home' => 'Back to Home',
    'map' => 'View Map'
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

    .facilities-container {
      max-width: 800px;
      margin: auto;
      padding: 60px 20px;
      text-align: center;
    }

    .facilities-title {
      font-size: 36px;
      font-weight: bold;
      color: #2d2d2d;
      margin-bottom: 10px;
    }

    .facilities-subtitle {
      font-size: 18px;
      color: #555;
      margin-bottom: 40px;
    }

    .facility-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.06);
      padding: 20px;
      margin-bottom: 20px;
      font-size: 17px;
      color: #333;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      animation: fadeInUp 0.6s ease forwards;
      opacity: 0;
    }

    .facility-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 24px rgba(0,0,0,0.08);
    }

    .facility-card i {
      color: #5b2bd1;
      margin-right: 10px;
      animation: pulse 1.6s infinite ease-in-out;
    }

    .facility-buttons {
      margin-top: 30px;
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }

    .facility-buttons a {
      text-decoration: none;
      padding: 10px 24px;
      border-radius: 8px;
      font-weight: 500;
      color: white;
      background-color: #5b2bd1;
      transition: background-color 0.3s ease;
    }

    .facility-buttons a:hover {
      background-color: #4721a0;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.2); }
    }

    .delay-1 { animation-delay: 0.2s; }
    .delay-2 { animation-delay: 0.4s; }
    .delay-3 { animation-delay: 0.6s; }
    .delay-4 { animation-delay: 0.8s; }

    @media (max-width: 600px) {
      .facilities-title { font-size: 28px; }
      .facility-card { font-size: 15px; }
    }
  </style>
</head>
<body>

<div class="facilities-container">
  <h1 class="facilities-title"><?= $c['title'] ?></h1>
  <p class="facilities-subtitle"><?= $c['subtitle'] ?></p>

  <?php foreach ($c['facilities'] as $index => $facility): ?>
    <div class="facility-card delay-<?= $index + 1 ?>"><i></i><?= $facility ?></div>
  <?php endforeach; ?>

  <div class="facility-buttons">
    <a href="index.php"><?= $c['home'] ?></a>
    <a href="map.php"><?= $c['map'] ?></a>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>

