<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['email'])) {
    header("Location: teacher-login.php");
    exit();
}

$lang = $_SESSION['lang'] ?? 'en';
$content = [
    'en' => [
        'title' => 'Verify OTP',
        'placeholder' => 'Enter 6-digit OTP',
        'button' => 'Verify OTP',
        'resend' => 'Resend OTP'
    ],
    'ar' => [
        'title' => 'التحقق من الرمز',
        'placeholder' => 'أدخل رمز التحقق المكون من 6 أرقام',
        'button' => 'تحقق من الرمز',
        'resend' => 'إعادة إرسال الرمز'
    ]
];

$c = $content[$lang];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = filter_var($_POST['otp'], FILTER_SANITIZE_NUMBER_INT);
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT * FROM otp_verifications WHERE email = ? AND otp = ? AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("si", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['authenticated'] = true;
        header("Location: add-schedule.php");
        exit();
    } else {
        $error = ($lang === 'ar') ? 'رمز التحقق غير صحيح أو منتهي.' : 'Invalid or expired OTP.';
    }
}

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $c['title'] ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap<?= $lang === 'ar' ? '.rtl' : '' ?>.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to bottom right, #f9faff, #f0ebff);
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }

    .otp-box {
      background: #fff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
      width: 100%;
      max-width: 450px;
      text-align: center;
    }

    .otp-box h1 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 20px;
    }

    .form-control {
      border-radius: 10px;
      height: 48px;
      font-size: 16px;
      margin-bottom: 20px;
    }

    .submit-btn {
      background-color: #5b2bd1;
      border: none;
      color: white;
      font-weight: 500;
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      font-size: 16px;
    }

    .submit-btn:hover {
      background-color: #4721a0;
    }

    .resend {
      margin-top: 16px;
      display: inline-block;
    }
  </style>
</head>
<body>
<main>
  <div class="otp-box">
    <h1><?= $c['title'] ?></h1>
    <?php if (isset($error)): ?>
      <div class="alert alert-danger"> <?= htmlspecialchars($error) ?> </div>
    <?php endif; ?>
    <form method="post" action="">
      <input type="text" name="otp" class="form-control" placeholder="<?= $c['placeholder'] ?>" required maxlength="6">
      <button type="submit" class="submit-btn"><?= $c['button'] ?></button>
    </form>
    <a href="resend-otp.php" class="resend"><?= $c['resend'] ?></a>
  </div>
</main>
<?php include 'includes/footer.php'; ?>
</body>
</html>
