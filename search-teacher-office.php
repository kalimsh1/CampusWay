<?php include 'includes/header.php'; ?>
<?php
$conn = new mysqli("localhost", "root", "", "campusway");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= $lang === 'ar' ? 'البحث عن جدول المحاضر' : 'Search Lecturer Schedule' ?> - CampusWay</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .search-section {
      background: url('images/Se.png') no-repeat center center;
      background-size: cover;
      padding: 60px 20px;
      min-height: 100vh;
      text-align: center;
      position: relative;
    }
    .search-section::before {
      content: "";
      position: absolute;
      inset: 0;
      background-color: rgba(255,255,255,0.4);
      backdrop-filter: blur(8px);
      z-index: 0;
    }
    .search-section > div {
      position: relative;
      z-index: 1;
    }
    .search-section h2 {
      font-weight: 700;
      color: #5b2bd1;
      margin-bottom: 20px;
    }
    .search-box {
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: center;
      max-width: 700px;
      margin: 0 auto 30px;
      gap: 12px;
      width: 100%;
      background: none;
      box-shadow: none;
      padding: 0;
    }
    .search-box input {
      flex: 1 1 300px;
      min-width: 0;
      padding: 10px 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
      height: 44px;
      resize: none;
      box-sizing: border-box;
      background: #fff;
    }
    .search-box button {
      height: 44px;
      border-radius: 8px;
      font-weight: 500;
      border: none;
      cursor: pointer;
      font-size: 16px;
      white-space: nowrap;
      padding: 0 24px;
      transition: background 0.2s, color 0.2s;
      box-shadow: none;
    }
    .search-box button[name="search"] {
      background-color: #5b2bd1;
      color: white;
    }
    .search-box button[name="search"]:hover {
      background-color: #4a1fb8;
    }
    .search-box button[name="q"] {
      background-color: #fff;
      color: #5b2bd1;
      border: 2px solid #5b2bd1;
    }
    .search-box button[name="q"]:hover {
      background-color: #f2edfc;
    }
    .results-container {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 20px;
    }
    .result-card {
      background: white;
      padding: 20px;
      border-radius: 14px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.05);
      width: 300px;
      text-align: start;
      animation: zoomIn 0.6s ease;
    }
    .result-card h5 {
      color: #5b2bd1;
      margin-bottom: 10px;
    }
    .result-card h5 a {
      color: #5b2bd1;
      text-decoration: none;
      transition: color 0.3s ease;
    }
    .result-card h5 a:hover {
      color: #3f1e8f;
      text-decoration: underline;
    }
    .result-card p {
      margin: 5px 0;
      color: #555;
    }
    .lecturer-button {
      margin-top: 40px;
      padding: 20px;
      text-align: center;
    }
    .lecturer-button a {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 12px 25px;
      background-color: #5b2bd1;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.3s ease;
      border: 2px solid #5b2bd1;
    }
    .lecturer-button a:hover {
      background-color: white;
      color: #5b2bd1;
    }
    .lecturer-button i {
      font-size: 1.2rem;
    }
    @keyframes zoomIn {
      from { transform: scale(0.95); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
    @media (max-width: 600px) {
      .search-box {
        max-width: 100%;
        gap: 8px;
      }
      .search-box input {
        font-size: 14px;
        padding: 8px 10px;
        height: 38px;
      }
      .search-box button {
        font-size: 14px;
        height: 38px;
        padding: 0 12px;
      }
    }
  </style>
</head>
<body>

<section class="search-section">
  <div>
    <h2><?= $lang === 'ar' ? 'البحث عن جدول المحاضر' : 'Search for lecturers schedule' ?></h2>

    <form class="search-box" method="GET">
      <input type="text" name="q" value="<?= isset($_GET['q']) && $_GET['q'] !== '*' ? htmlspecialchars($_GET['q']) : '' ?>" 
             placeholder="<?= $lang === 'ar' ? 'ادخل اسم المحاضر' : 'Enter lecturers name' ?>" style="height: 40px; resize: none;" />

      <button type="submit" name="search" value="1">
        <?= $lang === 'ar' ? 'بحث' : 'Search' ?>
      </button>

      <button type="submit" name="q" value="*">
        <?= $lang === 'ar' ? 'عرض الجميع' : 'Show All' ?>
      </button>
    </form>

    <?php
    if (isset($_GET['q'])) {
        $q = $conn->real_escape_string($_GET['q']);

        if ($q === '*' || trim($q) === '') {
            $query = "SELECT * FROM teachers";
        } else {
            $query = "SELECT * FROM teachers WHERE name LIKE '%$q%'";
        }

        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            echo "<p><strong>" . $result->num_rows . ($lang === 'ar' ? ' نتيجة تم العثور عليها' : ' results found') . "</strong></p>";
            echo '<div class="results-container">';
            while ($row = $result->fetch_assoc()) {
                echo '
                <div class="result-card">
                  <h5><i class="bi bi-person-circle me-2"></i><a href="view-schedule.php?teacher_id=' . htmlspecialchars($row['teacher_id']) . '">' . htmlspecialchars($row['name']) . '</a></h5>
                  <p>' . ($lang === 'ar' ? 'القسم: ' : 'Department: ') . htmlspecialchars($row['department']) . '</p>
                  <p>' . ($lang === 'ar' ? 'رقم المكتب: ' : 'Office Number: ') . htmlspecialchars($row['office_number']) . '</p>
                  <p>Email: ' . htmlspecialchars($row['email']) . '</p>
                </div>';
            }
            echo '</div>';
        } else {
            echo '<div style="color: #999; margin-top: 30px;">
                    <i class="bi bi-search" style="font-size: 3rem;"></i>
                    <p>' . ($lang === 'ar' ? 'لم يتم العثور على نتائج.' : 'No results found.') . '</p>
                  </div>';
        }
    }
    ?>

    <div class="lecturer-button">
      <a href="teacher-login.php">
        <i class="bi bi-person-plus-fill"></i>
        <?= $lang === 'ar' ? 'هل أنت محاضر؟ أضف جدولك هنا' : 'Are you a lecturer? Add your schedule here' ?>
      </a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
