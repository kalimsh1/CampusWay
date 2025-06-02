<?php
session_start();
require_once 'includes/db.php';
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

date_default_timezone_set('Asia/Riyadh');

if (isset($_GET['lang'])) {
  $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'ar';

$content = [
  'ar' => [
    'title' => 'دخول المحاضرين',
    'subtitle' => 'أدخل بريدك الجامعي للحصول على رمز التحقق.',
    'email' => 'البريد الإلكتروني الجامعي',
    'button' => 'إرسال رمز التحقق'
  ],
  'en' => [
    'title' => 'Lecturer Login',
    'subtitle' => 'Enter your academic email to receive an OTP.',
    'email' => 'Academic Email',
    'button' => 'Send OTP'
  ]
];

$c = $content[$lang];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $stmt = $conn->prepare("SELECT teacher_id FROM teachers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $otp = sprintf("%06d", mt_rand(100000, 999999));
        $expires_at = date("Y-m-d H:i:s", strtotime("+30 minutes"));

        $stmt = $conn->prepare("INSERT INTO otp_verifications (email, otp, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $otp, $expires_at);
        $stmt->execute();

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'qpksnx7@gmail.com';
            $mail->Password = '#####';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('qpksnx7@gmail.com', 'CampusWay');
            $mail->addAddress($email);
            $mail->Subject = 'Your CampusWay OTP';
            $mail->isHTML(true);
            $mail->Body = "<h3>Hello,</h3><p>Your OTP is <strong>$otp</strong></p><p>Valid for 30 minutes.</p>";
            $mail->AltBody = "Your OTP is $otp. Valid for 30 minutes.";
            $mail->send();
            $_SESSION['email'] = $email;
            header("Location: verify-otp.php");
            exit();
        } catch (Exception $e) {
            $error = "Failed to send OTP. Please try again.";
        }
    } else {
        $error = "Email not found in database.";
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
  <style>
    body {
      background: linear-gradient(to bottom right, #f9faff, #f0ebff);
      font-family: 'Segoe UI', sans-serif;
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

    .login-box {
      background: #fff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
      width: 100%;
      max-width: 450px;
      text-align: center;
    }

    .login-box h1 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .login-box p {
      color: #666;
      margin-bottom: 30px;
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
  </style>
</head>
<body>
  <main>
    <div class="login-box">
      <h1><?= $c['title'] ?></h1>
      <p><?= $c['subtitle'] ?></p>
      <?php if (isset($error)) : ?>
        <div class="alert alert-danger" role="alert">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>
      <form method="post" action="">
        <input type="email" name="email" class="form-control" placeholder="<?= $c['email'] ?>" required>
        <button type="submit" class="submit-btn"><?= $c['button'] ?></button>
      </form>
    </div>
  </main>

<?php include 'includes/footer.php'; ?>
</body>
</html>
