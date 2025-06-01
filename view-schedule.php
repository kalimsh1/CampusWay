<?php
session_start();
require_once 'includes/db.php';

$teacher = null;
$schedule = [];
if (isset($_GET['teacher_id'])) {
    $teacher_id = filter_var($_GET['teacher_id'], FILTER_SANITIZE_NUMBER_INT);
    $stmt = $conn->prepare("
        SELECT name, email, department, office_number
        FROM teachers
        WHERE teacher_id = ?
    ");
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();

    if ($teacher) {
        $stmt = $conn->prepare("
            SELECT day_of_week, start_time, end_time
            FROM teacher_schedules
            WHERE teacher_id = ?
            ORDER BY FIELD(day_of_week, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')
        ");
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $schedule[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'ar' ? 'ar' : 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusWay - <?= $teacher ? htmlspecialchars($teacher['name']) . '\'s Schedule' : 'Lecturer Schedule' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f2edfc;
            direction: <?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'ar' ? 'rtl' : 'ltr' ?>;
        }

        .schedule-container {
            padding: 60px 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .schedule-card {
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            max-width: 600px;
            width: 100%;
            animation: zoomIn 0.6s ease;
        }

        .schedule-card h3 {
            color: #5b2bd1;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }

        .schedule-card p {
            color: #555;
            margin: 10px 0;
        }

        .schedule-card .bi {
            margin-right: 8px;
            color: #5b2bd1;
        }

        .schedule-table {
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        .schedule-table th {
            background-color: #5b2bd1;
            color: white;
            font-weight: 500;
            padding: 12px;
        }

        .schedule-table td {
            padding: 12px;
            color: #333;
        }

        .schedule-table tr:nth-child(even) {
            background-color: #f2edfc;
        }

        .schedule-table tr:hover {
            background-color: #e6d9f7;
        }

        .btn-primary {
            background-color: #5b2bd1;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #3f1e8f;
        }

        .alert {
            border-radius: 8px;
            color: #555;
        }

        @keyframes zoomIn {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @media (max-width: 768px) {
            .schedule-card {
                padding: 20px;
            }

            .schedule-table th,
            .schedule-table td {
                padding: 8px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="schedule-container">
        <div class="schedule-card">
            <?php if ($teacher) { ?>
                <h3><i class="bi bi-person-circle"></i> <?= htmlspecialchars($teacher['name']) ?><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'ar' ? ' - الجدول' : '\'s Schedule' ?></h3>
                <p><i class="bi bi-envelope"></i> <strong><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'ar' ? 'البريد الإلكتروني: ' : 'Email: ' ?></strong> <?= htmlspecialchars($teacher['email']) ?></p>
                <p><i class="bi bi-building"></i> <strong><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'ar' ? 'القسم: ' : 'Department: ' ?></strong> <?= htmlspecialchars($teacher['department']) ?></p>
                <p><i class="bi bi-geo-alt"></i> <strong><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'ar' ? 'المكتب: ' : 'Office: ' ?></strong> <?= htmlspecialchars($teacher['office_number']) ?></p>
                <?php if (!empty($schedule)) { ?>
                    <h4 class="mt-4"><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'ar' ? 'الساعات المكتبية' : 'Office Hours' ?></h4>
                    <table class="table schedule-table">
                        <thead>
                            <tr>
                                <th><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'ar' ? 'اليوم' : 'Day' ?></th>
                                <th><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'ar' ? 'الوقت' : 'Time' ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedule as $slot) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($slot['day_of_week']) ?></td>
                                    <td><?= htmlspecialchars($slot['start_time'] . ' - ' . $slot['end_time']) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="alert alert-info mt-4"><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'ar' ? 'لا توجد ساعات مكتبية مجدولة.' : 'No office hours scheduled.' ?></div>
                <?php } ?>
            <?php } else { ?>
                <div class="alert alert-warning"><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'ar' ? 'لم يتم العثور على المحاضر.' : 'Lecturer not found.' ?></div>
            <?php } ?>
            <div class="text-center mt-4">
                <a href="search-teacher-office.php" class="btn btn-primary"><?= isset($_SESSION['lang']) && $_SESSION['lang'] === 'ar' ? 'العودة إلى البحث' : 'Back to Search' ?></a>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>