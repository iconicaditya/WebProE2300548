<?php
// Main layout for addcourses module
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course - EduSkill Marketplace</title>
    <link rel="stylesheet" href="/assets/css/bootstrap-fallback.css">
    <link rel="stylesheet" href="/assets/css/provider-addcourses.css">
    <style>
        body { background: #f7f9fc; }
        .brand {
            font-size: 1.7rem;
            font-weight: 700;
            color: #2d3e50;
            letter-spacing: 1px;
        }
        .main-header {
            background: #fff;
            border-bottom: 1px solid #e0e6ed;
            padding: 24px 0 12px 0;
        }
        .addcourse-container {
            width: 100%;
            margin: 40px 32px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(140,162,189,0.12);
            display: flex;
            gap: 0;
            overflow: hidden;
            min-height: 600px;
        }
        @media (max-width: 1200px) {
            .addcourse-container {
                margin: 32px 16px;
            }
        }
        @media (max-width: 900px) {
            .addcourse-container {
                flex-direction: column;
                margin: 24px 8px;
            }
            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #e0e6ed;
            }
            .content-area {
                padding: 32px 12px;
            }
        }
        .sidebar {
            width: 260px;
            background: #f0f4fa;
            padding: 32px 24px;
            border-right: 1px solid #e0e6ed;
        }
        .sidebar .title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 18px;
            color: #2d3e50;
        }
        .steps-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .steps-list li {
            margin-bottom: 12px;
        }
        .steps-list a {
            display: block;
            padding: 10px 16px;
            border-radius: 8px;
            color: #2476ff;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }
        .steps-list a.active, .steps-list a:hover {
            background: #eaf2ff;
            color: #174ea6;
            border-left: 4px solid #2476ff;
        }
        .content-area {
            flex: 1;
            padding: 40px 32px;
        }
        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2d3e50;
            margin-bottom: 8px;
        }
        .section-desc {
            color: #6c7a89;
            margin-bottom: 24px;
        }
        .toggle-group {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        .toggle-group input[type="radio"] {
            display: none;
        }
        .toggle-group label {
            padding: 8px 28px;
            border: 2px solid #2476ff;
            border-radius: 8px;
            background: #fff;
            color: #2476ff;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .toggle-group input[type="radio"]:checked + label {
            background: #2476ff;
            color: #fff;
        }
        .button-row {
            display: flex;
            justify-content: space-between;
            margin-top: 32px;
        }
        .btn-custom {
            padding: 8px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            transition: background 0.2s, color 0.2s;
        }
        .btn-custom.next {
            background: #2476ff;
            color: #fff;
        }
        .btn-custom.prev {
            background: #e0e6ed;
            color: #2d3e50;
        }
        .btn-custom.next:hover {
            background: #174ea6;
        }
        .btn-custom.prev:hover {
            background: #cfd8dc;
        }
        .footer {
            background: #f7f9fc;
            border-top: 1px solid #e0e6ed;
            margin-top: 48px;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="brand">EduSkill Marketplace</div>
            <div>
                <span class="fw-bold text-primary">Create new course</span>
            </div>
        </div>
    </header>
    <div class="addcourse-container">
        <aside class="sidebar">
            <div class="title">Course Builder</div>
            <ul class="steps-list">
                <li><a href="/provider/addcourses/pages/basicdetails.php" class="active">1. Basic details</a></li>
                <li><a href="/provider/addcourses/pages/modules.php">2. Modules & lessons</a></li>
                <li><a href="/provider/addcourses/pages/price.php">3. Price & offers</a></li>
                <li><a href="/provider/addcourses/pages/resources.php">4. Resources</a></li>
                <li><a href="/provider/addcourses/pages/publish.php">5. Preview & Publish</a></li>
            </ul>
        </aside>
        <main class="content-area">
            <!-- Page content starts here -->
            <?php if (isset($content)) echo $content; ?>
        </main>
    </div>
    <footer class="footer py-3 text-center">
        <small>&copy; 2026 EduSkill Marketplace. All rights reserved.</small>
    </footer>
</body>
</html>
