<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusWay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                        url('map.png') no-repeat center center / cover;
            min-height: 100vh;
            color: white;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 20px;
            position: relative;
        }

        .hero-section h1 {
            font-size: 3.2rem;
            font-weight: 700;
            animation: fadeInDown 1s ease-in-out;
        }

        .hero-section p {
            font-size: 1.3rem;
            margin-top: 15px;
            animation: fadeInUp 1.2s ease-in-out;
        }

        .feature-boxes {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            padding: 60px 20px;
            background-color: #ffffff;
            color: #333;
            text-align: center;
        }

        .feature-box {
            background: #f9f9f9;
            border-radius: 16px;
            padding: 30px 20px;
            width: 280px;
            transition: transform 0.3s ease;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }

        .feature-box:hover {
            transform: translateY(-10px);
        }

        .feature-box i {
            font-size: 2.5rem;
            color: #5b2bd1;
            margin-bottom: 15px;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2rem;
            }
            .feature-box {
                width: 100%;
            }
        }

        .action-buttons {
            padding: 20px;
            background-color: #f8f9fa;
        }

        .action-button.square-button {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #5b2bd1;
            color: white;
            text-decoration: none;
            aspect-ratio: 1;
            width: 100%;
            max-width: 280px;
            margin: 0 auto;
            border-radius: 15px;
            border: 3px solid #4a1fb8;
            box-shadow: 0 10px 30px rgba(91, 43, 209, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .action-button.square-button:hover {
            background: #4a1fb8;
            color: white;
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(91, 43, 209, 0.3);
            border-color: #3a1a8f;
        }

        .button-content {
            text-align: center;
            padding: 15px;
            z-index: 1;
            width: 100%;
        }

        .action-button.square-button i {
            font-size: clamp(2.5rem, 4vw, 3.5rem);
            margin-bottom: clamp(15px, 2vw, 20px);
            display: block;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .action-button.square-button span {
            font-size: clamp(1rem, 2vw, 1.4rem);
            font-weight: 600;
            display: block;
            line-height: 1.3;
            padding: 0 5px;
        }

        @media (min-width: 992px) {
            .action-buttons {
                padding: 30px 20px;
            }
            
            .action-button.square-button {
                max-width: 250px;
            }
        }

        @media (max-width: 576px) {
            .action-buttons {
                padding: 10px;
            }
            
            .action-button.square-button {
                border-width: 2px;
                border-radius: 12px;
            }

            .button-content {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

    <section class="hero-section">
        <h1><?= $lang === 'ar' ? 'مرحبًا بكم في CampusWay' : 'Welcome to CampusWay' ?></h1>
        <p>
            <?= $lang === 'ar' 
                ? 'نظام ذكي لتحديد مواقع القاعات الدراسية والمرافق والأكاديميين داخل الحرم الجامعي بسهولة ودقة.' 
                : 'An intelligent system to locate classrooms, facilities, and staff within your campus accurately and easily.' ?>
        </p>
    </section>

    <section class="feature-boxes">
        <div class="feature-box">
            <i class="bi bi-map"></i>
            <h4><?= $lang === 'ar' ? 'خريطة تفاعلية ثلاثية الابعاد' : 'Interactive 3D Map' ?></h4>
            <p><?= $lang === 'ar' ? 'تصفح الكلية باستخدام خريطة تفاعلية ثلاثية الابعاد.' : 'Navigate the campus using a modern interactive map.' ?></p>
        </div>
        <div class="feature-box">
            <i class="bi bi-search"></i>
            <h4><?= $lang === 'ar' ? 'البحث الذكي' : 'Smart Search' ?></h4>
            <p><?= $lang === 'ar' ? 'اعثر على القاعات الدراسية والمرافق بسرعة وسهولة.' : 'Find classrooms and facilities instantly with smart search.' ?></p>
        </div>
        <div class="feature-box">
            <i class="bi bi-clock-history"></i>
            <h4><?= $lang === 'ar' ? 'ساعات التواجد' : 'Availability Times' ?></h4>
            <p><?= $lang === 'ar' ? 'تعرف على أوقات وجود أعضاء هيئة التدريس.' : 'View faculty availability and office hours.' ?></p>
        </div>
    </section>

    <section class="action-buttons">
        <div class="container">
            <div class="row justify-content-center g-3">
                <div class="col-6 col-lg-4">
                    <a href="map.php" class="action-button square-button">
                        <div class="button-content">
                            <i class="bi bi-map-fill"></i>
                            <span><?= $lang === 'ar' ? 'فتح الخريطة التفاعلية' : 'Open Interactive Map' ?></span>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-4">
                    <a href="search-teacher-office.php" class="action-button square-button">
                        <div class="button-content">
                            <i class="bi bi-search"></i>
                            <span><?= $lang === 'ar' ? 'البحث عن مكتب المحاضر' : 'Search Lecturer Office' ?></span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
</body>
</html>

