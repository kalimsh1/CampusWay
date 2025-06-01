<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['authenticated']) || !isset($_SESSION['email'])) {
    header("Location: teacher-login.php");
    exit();
}

$email = $_SESSION['email'];
$teacher = null;
$schedule = [];
$lang = $_SESSION['lang'] ?? 'ar';

$content = [
    'ar' => [
        'title' => 'إدارة الساعات المكتبية',
        'add_title' => 'إضافة ساعات مكتبية جديدة',
        'current_title' => 'الجدول الحالي',
        'days' => 'اختر الأيام',
        'start_time' => 'وقت البدء',
        'end_time' => 'وقت الانتهاء',
        'add_button' => 'إضافة الجدول',
        'edit_button' => 'تعديل',
        'delete_button' => 'حذف',
        'save_button' => 'حفظ التغييرات',
        'cancel_button' => 'إلغاء',
        'view_full' => 'عرض الجدول الكامل',
        'success_add' => 'تمت إضافة الجدول بنجاح.',
        'success_edit' => 'تم تحديث الجدول بنجاح.',
        'success_delete' => 'تم حذف الجدول بنجاح.',
        'error_fields' => 'يرجى إكمال جميع الحقول.',
        'error_time' => 'وقت الانتهاء يجب أن يكون بعد وقت البدء.',
        'error_overlap' => 'هذا الوقت يتداخل مع جدول موجود.',
        'no_schedule' => 'لا توجد ساعات مكتبية مجدولة.'
    ],
    'en' => [
        'title' => 'Manage Office Hours',
        'add_title' => 'Add New Office Hours',
        'current_title' => 'Current Schedule',
        'days' => 'Select Days',
        'start_time' => 'Start Time',
        'end_time' => 'End Time',
        'add_button' => 'Add Schedule',
        'edit_button' => 'Edit',
        'delete_button' => 'Delete',
        'save_button' => 'Save Changes',
        'cancel_button' => 'Cancel',
        'view_full' => 'View Full Schedule',
        'success_add' => 'Schedule added successfully.',
        'success_edit' => 'Schedule updated successfully.',
        'success_delete' => 'Schedule deleted successfully.',
        'error_fields' => 'Please complete all fields.',
        'error_time' => 'End time must be after start time.',
        'error_overlap' => 'This time overlaps with an existing schedule.',
        'no_schedule' => 'No office hours scheduled.'
    ]
];

$c = $content[$lang];

try {
    $stmt = $conn->prepare("SELECT teacher_id, name FROM teachers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();

    if ($teacher) {
        $stmt = $conn->prepare("SELECT schedule_id, day_of_week, start_time, end_time FROM teacher_schedules WHERE teacher_id = ? ORDER BY FIELD(day_of_week, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')");
        $stmt->bind_param("i", $teacher['teacher_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $schedule[] = $row;
        }
    }
} catch (Exception $e) {
    $error = "Database error occurred.";
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $days = $_POST['day_of_week'] ?? [];
        $start_time = $_POST['start_time'] ?? '';
        $end_time = $_POST['end_time'] ?? '';

        if ($teacher && $days && $start_time && $end_time) {
            if (strtotime($end_time) <= strtotime($start_time)) {
                $error = $c['error_time'];
            } else {
                // Check for overlapping schedules
                $overlap = false;
                foreach ($days as $day) {
                    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM teacher_schedules 
                        WHERE teacher_id = ? AND day_of_week = ? 
                        AND ((start_time <= ? AND end_time > ?) OR (start_time < ? AND end_time >= ?))");
                    $stmt->bind_param("isssss", $teacher['teacher_id'], $day, $end_time, $start_time, $end_time, $start_time);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    if ($row['count'] > 0) {
                        $overlap = true;
                        break;
                    }
                }

                if ($overlap) {
                    $error = $c['error_overlap'];
                } else {
                    $stmt = $conn->prepare("INSERT INTO teacher_schedules (teacher_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
                    foreach ($days as $day) {
                        $stmt->bind_param("isss", $teacher['teacher_id'], $day, $start_time, $end_time);
                        $stmt->execute();
                    }
                    $success = $c['success_add'];
                    // Refresh schedule data
                    $schedule = [];
                    $stmt = $conn->prepare("SELECT schedule_id, day_of_week, start_time, end_time FROM teacher_schedules WHERE teacher_id = ? ORDER BY FIELD(day_of_week, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')");
                    $stmt->bind_param("i", $teacher['teacher_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $schedule[] = $row;
                    }
                }
            }
        } else {
            $error = $c['error_fields'];
        }
    } elseif ($action === 'edit') {
        $schedule_id = $_POST['schedule_id'] ?? '';
        $day = $_POST['day_of_week'] ?? '';
        $start_time = $_POST['start_time'] ?? '';
        $end_time = $_POST['end_time'] ?? '';

        if ($teacher && $schedule_id && $day && $start_time && $end_time) {
            if (strtotime($end_time) <= strtotime($start_time)) {
                $error = $c['error_time'];
            } else {
                // Check for overlapping schedules excluding current one
                $stmt = $conn->prepare("SELECT COUNT(*) as count FROM teacher_schedules 
                    WHERE teacher_id = ? AND day_of_week = ? AND schedule_id != ?
                    AND ((start_time <= ? AND end_time > ?) OR (start_time < ? AND end_time >= ?))");
                $stmt->bind_param("isisssss", $teacher['teacher_id'], $day, $schedule_id, $end_time, $start_time, $end_time, $start_time);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if ($row['count'] > 0) {
                    $error = $c['error_overlap'];
                } else {
                    $stmt = $conn->prepare("UPDATE teacher_schedules SET day_of_week = ?, start_time = ?, end_time = ? WHERE schedule_id = ? AND teacher_id = ?");
                    $stmt->bind_param("sssii", $day, $start_time, $end_time, $schedule_id, $teacher['teacher_id']);
                    if ($stmt->execute()) {
                        $success = $c['success_edit'];
                        // Refresh schedule data
                        $schedule = [];
                        $stmt = $conn->prepare("SELECT schedule_id, day_of_week, start_time, end_time FROM teacher_schedules WHERE teacher_id = ? ORDER BY FIELD(day_of_week, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')");
                        $stmt->bind_param("i", $teacher['teacher_id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            $schedule[] = $row;
                        }
                    }
                }
            }
        } else {
            $error = $c['error_fields'];
        }
    } elseif ($action === 'delete') {
        $schedule_id = $_POST['schedule_id'] ?? '';
        if ($teacher && $schedule_id) {
            $stmt = $conn->prepare("DELETE FROM teacher_schedules WHERE schedule_id = ? AND teacher_id = ?");
            $stmt->bind_param("ii", $schedule_id, $teacher['teacher_id']);
            if ($stmt->execute()) {
                $success = $c['success_delete'];
                // Refresh schedule data
                $schedule = [];
                $stmt = $conn->prepare("SELECT schedule_id, day_of_week, start_time, end_time FROM teacher_schedules WHERE teacher_id = ? ORDER BY FIELD(day_of_week, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')");
                $stmt->bind_param("i", $teacher['teacher_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $schedule[] = $row;
                }
            }
        }
    }
}

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $c['title'] ?> - CampusWay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom right, #f9faff, #f0ebff);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            direction: <?= $lang === 'ar' ? 'rtl' : 'ltr' ?>;
        }
        .container {
            padding-top: 50px;
            max-width: 800px;
        }
        .card {
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-primary {
            background-color: #5b2bd1;
            border: none;
        }
        .btn-primary:hover {
            background-color: #4721a0;
        }
        .btn-outline-danger {
            color: #dc3545;
            border-color: #dc3545;
        }
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }
        .schedule-table {
            margin-top: 20px;
        }
        .schedule-table th {
            background-color: #5b2bd1;
            color: white;
        }
        .schedule-table td {
            vertical-align: middle;
        }
        .edit-form {
            display: none;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .edit-form.show {
            display: block;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h2 class="mb-4 text-center"><?= $c['title'] ?></h2>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <h4 class="mb-3"><?= $c['add_title'] ?></h4>
        <form method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="action" value="add">
            <div class="mb-3">
                <label class="form-label"><?= $c['days'] ?></label><br>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach (["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"] as $day): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="day_of_week[]" value="<?= $day ?>" id="<?= $day ?>">
                            <label class="form-check-label" for="<?= $day ?>"><?= $day ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?= $c['start_time'] ?></label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?= $c['end_time'] ?></label>
                    <input type="time" name="end_time" class="form-control" required>
                </div>
            </div>
            <button class="btn btn-primary w-100"><?= $c['add_button'] ?></button>
        </form>
    </div>

    <?php if (!empty($schedule)): ?>
        <div class="card">
            <h4 class="mb-3"><?= $c['current_title'] ?></h4>
            <table class="table table-bordered schedule-table">
                <thead>
                    <tr>
                        <th><?= $lang === 'ar' ? 'اليوم' : 'Day' ?></th>
                        <th><?= $lang === 'ar' ? 'وقت البدء' : 'Start Time' ?></th>
                        <th><?= $lang === 'ar' ? 'وقت الانتهاء' : 'End Time' ?></th>
                        <th><?= $lang === 'ar' ? 'الإجراءات' : 'Actions' ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedule as $row): ?>
                        <tr id="row-<?= $row['schedule_id'] ?>">
                            <td><?= htmlspecialchars($row['day_of_week']) ?></td>
                            <td><?= htmlspecialchars($row['start_time']) ?></td>
                            <td><?= htmlspecialchars($row['end_time']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary edit-btn" data-id="<?= $row['schedule_id'] ?>">
                                    <i class="bi bi-pencil"></i> <?= $c['edit_button'] ?>
                                </button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('<?= $lang === 'ar' ? 'هل أنت متأكد من حذف هذا الجدول؟' : 'Are you sure you want to delete this schedule?' ?>');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="schedule_id" value="<?= $row['schedule_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> <?= $c['delete_button'] ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="p-0">
                                <div class="edit-form" id="edit-form-<?= $row['schedule_id'] ?>">
                                    <form method="POST" class="row g-3">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="schedule_id" value="<?= $row['schedule_id'] ?>">
                                        <div class="col-md-4">
                                            <select name="day_of_week" class="form-select" required>
                                                <?php foreach (["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"] as $day): ?>
                                                    <option value="<?= $day ?>" <?= $day === $row['day_of_week'] ? 'selected' : '' ?>><?= $day ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="time" name="start_time" class="form-control" value="<?= $row['start_time'] ?>" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="time" name="end_time" class="form-control" value="<?= $row['end_time'] ?>" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary w-100"><?= $c['save_button'] ?></button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="text-center mt-4">
                <a href="view-schedule.php?teacher_id=<?= $teacher['teacher_id'] ?>" class="btn btn-outline-primary">
                    <i class="bi bi-eye"></i> <?= $c['view_full'] ?>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info"><?= $c['no_schedule'] ?></div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Edit form toggle
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const scheduleId = this.dataset.id;
            const editForm = document.getElementById(`edit-form-${scheduleId}`);
            const allEditForms = document.querySelectorAll('.edit-form');
            
            // Hide all other edit forms
            allEditForms.forEach(form => {
                if (form !== editForm) {
                    form.classList.remove('show');
                }
            });
            
            // Toggle current edit form
            editForm.classList.toggle('show');
        });
    });
});
</script>
</body>
</html>
