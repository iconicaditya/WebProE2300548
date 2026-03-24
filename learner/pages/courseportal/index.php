<?php
$course = [
    'title' => 'React.js & Modern Frontend Development',
    'subtitle' => 'Master React hooks, state management, routing, and deployment to build real-world single-page applications with industry best practices.',
];
$sections = [
    [
        'title' => 'React Fundamentals & JSX',
        'duration' => '2h 30m',
        'modules' => [
            ['type' => 'video', 'title' => 'Introduction to React', 'duration' => '15:30'],
            ['type' => 'video', 'title' => 'Components and Props', 'duration' => '22:45'],
            ['type' => 'video', 'title' => 'JSX Deep Dive', 'duration' => '18:20'],
            ['type' => 'video', 'title' => 'Rendering Lists and Conditional Rendering', 'duration' => '25:10'],
            ['type' => 'quiz',  'title' => 'Quiz: React Basics', 'duration' => ''],
            ['type' => 'project', 'title' => 'Project: Build a Todo App', 'duration' => '1h 15m'],
        ]
    ],
    [
        'title' => 'React Hooks & State Management',
        'duration' => '3h 45m',
        'modules' => [
            ['type' => 'video', 'title' => 'useState Hook', 'duration' => '20:15'],
            ['type' => 'video', 'title' => 'useEffect Hook', 'duration' => '28:40'],
            ['type' => 'video', 'title' => 'useContext for Global State', 'duration' => '25:30'],
            ['type' => 'video', 'title' => 'Custom Hooks', 'duration' => '22:50'],
            ['type' => 'quiz',  'title' => 'Quiz: React Hooks', 'duration' => ''],
            ['type' => 'project', 'title' => 'Project: Weather App with Hooks', 'duration' => '1h 30m'],
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $course['title'] ?></title>
    <link rel="stylesheet" href="../../assets/css/courseportal.css">
</head>
<body>
    <div class="course-portal-header">
        <div class="header-content">
            <h1><?= $course['title'] ?></h1>
            <p class="course-subtitle"><?= $course['subtitle'] ?></p>
        </div>
    </div>
    <div class="course-portal-flex">
        <aside class="course-sidebar">
            <div class="sections-list">
                <?php foreach ($sections as $si => $section): ?>
                    <div class="section">
                        <div class="section-header" data-section="<?= $si ?>">
                            <span class="section-title"><b>Section <?= $si+1 ?>:</b> <?= $section['title'] ?></span>
                            <span class="section-duration">• <?= $section['duration'] ?></span>
                            <span class="section-toggle">&#9660;</span>
                        </div>
                        <ul class="modules-list" <?= $si === 0 ? '' : 'style="display:none;"' ?>>
                            <?php foreach ($section['modules'] as $mi => $mod): ?>
                                <li class="module <?= $mod['type'] ?>">
                                    <?php if ($mod['type'] === 'video'): ?>
                                        <span class="icon">&#9654;</span>
                                    <?php elseif ($mod['type'] === 'quiz'): ?>
                                        <span class="icon">&#128196;</span>
                                    <?php elseif ($mod['type'] === 'project'): ?>
                                        <span class="icon">&#128187;</span>
                                    <?php endif; ?>
                                    <span class="module-title"><?= $mi+1 ?>. <?= $mod['title'] ?></span>
                                    <?php if ($mod['duration']): ?>
                                        <span class="module-duration"><?= $mod['duration'] ?></span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </aside>
        <main class="course-main">
            <div class="video-container">
                <video controls width="100%" poster="">
                    <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="nav-buttons">
                <button class="nav-btn prev-btn">Previous</button>
                <button class="nav-btn next-btn">Next</button>
            </div>
        </main>
    </div>
    <script src="../../assets/js/courseportal.js"></script>
</body>
</html>