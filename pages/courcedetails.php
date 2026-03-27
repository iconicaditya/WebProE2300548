<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/../learner/includes/learner_data.php');

// Check if user is logged in
$activeLearner = null;
$activeLearnerId = 0;
if (ems_is_logged_in() && ems_current_role() === 'learner') {
    $activeLearner = ems_load_portal_user($conn);
    $activeLearnerId = (int)($activeLearner['id'] ?? 0);
}

// Get course ID from URL parameter
$courseId = (int)($_GET['id'] ?? 0);
if ($courseId <= 0) {
    http_response_code(404);
    die('<!DOCTYPE html><html><head><title>Course Not Found</title><style>body{font-family:Arial;text-align:center;padding:50px;background:#f5f5f5}h1{color:#333}p{color:#666}</style></head><body><h1>404 - Course Not Found</h1><p>The course you\'re looking for does not exist.</p><p><a href="' . BASE_URL . 'pages/allcources.php">Back to All Courses</a></p></body></html>');
}

// Fetch course data from database
$courseContext = ems_learner_fetch_course_by_id($conn, $courseId);
if (!$courseContext) {
    http_response_code(404);
    die('<!DOCTYPE html><html><head><title>Course Not Found</title><style>body{font-family:Arial;text-align:center;padding:50px;background:#f5f5f5}h1{color:#333}p{color:#666}</style></head><body><h1>404 - Course Not Found</h1><p>The course you\'re looking for does not exist or is not available.</p><p><a href="' . BASE_URL . 'pages/allcources.php">Back to All Courses</a></p></body></html>');
}

$courseContextId = (int)($courseContext['id'] ?? 0);
$courseTitle = (string)($courseContext['title'] ?? 'Course Details');
$courseShortDescription = (string)($courseContext['short_description'] ?? '');
$courseDescription = (string)($courseContext['description'] ?? 'This is a comprehensive course designed for learners who want to master this subject.');
$courseThumbnailUrl = (string)($courseContext['thumbnail_url'] ?? (BASE_URL . 'assets/images/cources/web-dev.jpg'));
$coursePromoVideoUrl = trim((string)($courseContext['promo_video_url'] ?? ''));
$courseLanguage = trim((string)($courseContext['language'] ?? ''));
if ($courseLanguage === '') {
    $courseLanguage = 'English';
}

$courseIncludes = isset($courseContext['includes']) && is_array($courseContext['includes'])
    ? $courseContext['includes']
    : [];

$relatedCourses = [];
$relatedPool = ems_learner_fetch_all_published_courses($conn, 24, 0);
foreach ($relatedPool as $relatedCourse) {
    $relatedId = (int)($relatedCourse['id'] ?? 0);
    if ($relatedId <= 0 || $relatedId === $courseContextId) {
        continue;
    }

    $levelRaw = strtolower(trim((string)($relatedCourse['level'] ?? 'all_levels')));
    $levelLabel = ucwords(str_replace('_', ' ', $levelRaw));
    if ($levelLabel === '') {
        $levelLabel = 'All Levels';
    }

    $relatedCourses[] = [
        'id' => $relatedId,
        'title' => (string)($relatedCourse['title'] ?? 'Untitled Course'),
        'thumbnail_url' => (string)($relatedCourse['thumbnail_url'] ?? (BASE_URL . 'assets/images/cources/web-dev.jpg')),
        'instructor_name' => (string)($relatedCourse['instructor_name'] ?? 'Instructor'),
        'level_label' => $levelLabel,
        'rating' => round((float)($relatedCourse['avg_rating'] ?? 0), 1),
        'student_count_estimate' => (int)($relatedCourse['student_count_estimate'] ?? 0),
        'duration_label' => (string)($relatedCourse['duration_label'] ?? 'Self-paced'),
        'price_label' => ((string)($relatedCourse['access_type'] ?? 'free') === 'free')
            ? 'FREE'
            : ems_learner_currency_format((float)($relatedCourse['price_amount'] ?? 0), (string)($relatedCourse['currency_code'] ?? 'USD')),
    ];

    if (count($relatedCourses) >= 4) {
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($courseTitle); ?> | EduSkill Marketplace</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/courcedetails.css?v=3">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <!-- Main Course Section -->
    <div class="course-container">
        <div class="container-fluid">
            <div class="row g-0">
                <!-- Left Content Area -->
                <div class="col-lg-8">
                    <!-- Course Title & Meta -->
                    <div class="course-header-content">
                        <h1 class="course-main-title"><?php echo htmlspecialchars($courseTitle); ?></h1>
                        <p class="course-description"><?php echo htmlspecialchars($courseShortDescription); ?></p>
                        
                        <div class="course-rating-section">
                            <div class="rating-display">
                                <span class="rating-number"><?php echo $courseContext['avg_rating']; ?></span>
                                <div class="stars">
                                    <?php
                                        $rating = round($courseContext['avg_rating']);
                                        for ($i = 0; $i < 5; $i++) {
                                            if ($i < $rating) {
                                                echo '<i class="fas fa-star"></i>';
                                            } elseif ($i < $rating + 0.5) {
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                    ?>
                                </div>
                                <span class="rating-count">(<?php echo (int)$courseContext['review_count']; ?> ratings)</span>
                            </div>
                            <div class="course-stats">
                                <span class="stat-item"><strong><?php echo number_format($courseContext['enrollment_count']); ?></strong> students</span>
                                <span class="stat-divider">•</span>
                                <span class="stat-item">Created by <strong><?php echo htmlspecialchars($courseContext['instructor_name']); ?></strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Instructor Card -->
                    <div class="instructor-card">
                        <div class="instructor-header">
                            <img src="<?php echo htmlspecialchars($courseContext['thumbnail_url']); ?>" alt="Instructor" class="instructor-img">
                            <div class="instructor-info">
                                <h5>Instructor</h5>
                                <h4><?php echo htmlspecialchars($courseContext['instructor_name']); ?></h4>
                                <p><?php echo htmlspecialchars($courseContext['level']); ?> • <?php echo htmlspecialchars($courseLanguage); ?></p>
                            </div>
                        </div>
                        <p class="instructor-bio"><?php echo htmlspecialchars($courseDescription !== '' ? $courseDescription : 'Passionate instructor dedicated to helping learners master this subject.'); ?></p>
                    </div>

                    <!-- Course Content Tabs -->
                    <div class="course-content-tabs">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Overview</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="curriculum-tab" data-bs-toggle="tab" data-bs-target="#curriculum" type="button" role="tab">Curriculum</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">Reviews</button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Overview Tab -->
                            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                                <div class="overview-section">
                                    <h3>What you'll learn</h3>
                                    <div class="learning-points">
                                        <?php
                                            if (!empty($courseContext['outcomes'])) {
                                                foreach ($courseContext['outcomes'] as $outcome) {
                                                    echo '<div class="point">';
                                                    echo '<i class="fas fa-check-circle"></i>';
                                                    echo '<span>' . htmlspecialchars($outcome) . '</span>';
                                                    echo '</div>';
                                                }
                                            } else {
                                                echo '<div class="point"><i class="fas fa-check-circle"></i><span>Complete comprehensive learning experience</span></div>';
                                            }
                                        ?>
                                    </div>

                                    <h3 class="mt-5">Course Description</h3>
                                    <p><?php echo htmlspecialchars($courseDescription); ?></p>

                                    <h3 class="mt-5">Requirements</h3>
                                    <ul class="requirements-list">
                                        <?php
                                            if (!empty($courseContext['requirements'])) {
                                                foreach ($courseContext['requirements'] as $requirement) {
                                                    echo '<li>' . htmlspecialchars($requirement) . '</li>';
                                                }
                                            } else {
                                                echo '<li>Basic computer literacy</li>';
                                                echo '<li>Willingness to learn</li>';
                                            }
                                        ?>
                                    </ul>
                                </div>
                            </div>

                            <!-- Curriculum Tab -->
                            <div class="tab-pane fade" id="curriculum" role="tabpanel">
                                <div class="curriculum-section">
                                    <h3>Course Curriculum</h3>
                                    
                                    <?php
                                        if (!empty($courseContext['sections'])) {
                                            foreach ($courseContext['sections'] as $sectionIndex => $section) {
                                                $lessonCount = !empty($section['lessons']) ? count($section['lessons']) : 0;
                                                $totalDuration = 0;
                                                foreach ($section['lessons'] as $lesson) {
                                                    $totalDuration += (int)($lesson['duration_seconds'] ?? 0);
                                                }
                                                $durationLabel = ems_learner_seconds_to_duration($totalDuration);
                                                
                                                echo '<div class="curriculum-module">';
                                                echo '<div class="module-header" data-bs-toggle="collapse" data-bs-target="#module' . ($sectionIndex + 1) . '">';
                                                echo '<div class="module-title">';
                                                echo '<i class="fas fa-chevron-down"></i>';
                                                echo '<span class="module-number">Section ' . ($sectionIndex + 1) . ':</span>';
                                                echo '<span class="module-name">' . htmlspecialchars($section['section_title']) . '</span>';
                                                echo '</div>';
                                                echo '<span class="module-meta">' . $lessonCount . ' lesson' . ($lessonCount !== 1 ? 's' : '') . ' • ' . $durationLabel . '</span>';
                                                echo '</div>';
                                                echo '<div class="collapse" id="module' . ($sectionIndex + 1) . '">';
                                                echo '<div class="module-content">';
                                                
                                                if (!empty($section['lessons'])) {
                                                    foreach ($section['lessons'] as $lessonIndex => $lesson) {
                                                        echo '<div class="lesson">';
                                                        
                                                        if ($lesson['lesson_type'] === 'video') {
                                                            echo '<i class="fas fa-play-circle"></i>';
                                                        } elseif ($lesson['lesson_type'] === 'quiz') {
                                                            echo '<i class="fas fa-file-alt"></i>';
                                                        } else {
                                                            echo '<i class="fas fa-code"></i>';
                                                        }
                                                        
                                                        echo '<span>' . ($lessonIndex + 1) . '. ' . htmlspecialchars($lesson['lesson_title']) . '</span>';
                                                        echo '<span class="lesson-duration">' . ems_learner_seconds_to_duration($lesson['duration_seconds']) . '</span>';
                                                        echo '</div>';
                                                    }
                                                } else {
                                                    echo '<p>No lessons in this section yet.</p>';
                                                }
                                                
                                                echo '</div>';
                                                echo '</div>';
                                                echo '</div>';
                                            }
                                        } else {
                                            echo '<p>Curriculum coming soon.</p>';
                                        }
                                    ?>
                                </div>
                            </div>

                            <!-- Reviews Tab -->
                            <div class="tab-pane fade" id="reviews" role="tabpanel">
                                <div class="reviews-section">
                                    <h3>Student Reviews</h3>
                                    
                                    <?php
                                        if (!empty($courseContext['reviews'])) {
                                            foreach ($courseContext['reviews'] as $review) {
                                                echo '<div class="review-item">';
                                                echo '<div class="review-header">';
                                                echo '<div class="reviewer-info">';
                                                echo '<h5>' . htmlspecialchars($review['learner_name'] ?? 'Anonymous') . '</h5>';
                                                echo '<div class="review-rating">';
                                                
                                                $ratingVal = (int)($review['rating'] ?? 0);
                                                for ($i = 0; $i < 5; $i++) {
                                                    if ($i < $ratingVal) {
                                                        echo '<i class="fas fa-star"></i>';
                                                    } else {
                                                        echo '<i class="far fa-star"></i>';
                                                    }
                                                }
                                                
                                                echo '</div>';
                                                echo '</div>';
                                                echo '<span class="review-date">' . $review['time_ago'] . '</span>';
                                                echo '</div>';
                                                echo '<p class="review-text">' . htmlspecialchars($review['review_text'] ?? 'Great course!') . '</p>';
                                                echo '</div>';
                                            }
                                        } else {
                                            echo '<p>No reviews yet. Be the first to review this course!</p>';
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="col-lg-4">
                    <div class="course-sidebar">
                        <!-- Course Image -->
                        <div class="sidebar-course-image">
                            <img src="<?php echo htmlspecialchars($courseThumbnailUrl); ?>" alt="<?php echo htmlspecialchars($courseTitle); ?>">
                            <div class="image-overlay">
                                <button class="play-btn-overlay" type="button" data-promo-url="<?php echo htmlspecialchars($coursePromoVideoUrl); ?>">
                                    <i class="fas fa-play"></i>
                                </button>
                                <p class="preview-text"><?php echo $coursePromoVideoUrl !== '' ? 'Preview this course' : 'Preview not available'; ?></p>
                            </div>
                        </div>

                        <!-- Price Card -->
                        <div class="price-card" data-course-id="<?php echo (int)$courseContextId; ?>" data-learner-auth="<?php echo $activeLearnerId > 0 ? '1' : '0'; ?>">
                            <!-- Price Section -->
                            <div class="price-section">
                                <?php if ($courseContext['access_type'] === 'paid'): ?>
                                <div class="price-badge">
                                    <span class="discount-badge"><?php echo htmlspecialchars($courseContext['discount_label'] ?? '40% OFF'); ?></span>
                                </div>
                                <div class="price-display">
                                    <span class="price-amount"><?php echo htmlspecialchars(ems_learner_currency_format($courseContext['price_amount'], $courseContext['currency_code'])); ?></span>
                                    <?php if (!empty($courseContext['original_price'])): ?>
                                    <span class="price-original"><?php echo htmlspecialchars(ems_learner_currency_format($courseContext['original_price'], $courseContext['currency_code'])); ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="price-note">Limited time offer 🎉 Ends in 3 days</p>
                                <?php elseif ($courseContext['access_type'] === 'free'): ?>
                                <div class="price-display">
                                    <span class="price-amount" style="color: #27ae60; font-size: 24px;">FREE</span>
                                </div>
                                <p class="price-note">Free access to all course materials</p>
                                <?php endif; ?>
                            </div>

                            <!-- Primary Action Button -->
                            <a href="<?php echo BASE_URL; ?>pages/payment.php<?php echo $courseContextId > 0 ? '?course_id=' . (int)$courseContextId : ''; ?>" class="btn btn-enroll-primary w-100 mb-3" data-course-id="<?php echo (int)$courseContextId; ?>">
                                <i class="fas fa-graduation-cap me-2"></i>Enroll Now
                            </a>

                            <!-- Secondary Actions -->
                            <div class="secondary-actions">
                                <button class="btn btn-add-cart w-100 mb-2" data-course-id="<?php echo (int)$courseContextId; ?>" data-action="add-to-cart">
                                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                </button>
                                <button class="btn btn-wishlist w-100" data-course-id="<?php echo (int)$courseContextId; ?>" data-action="toggle-wishlist">
                                    <i class="far fa-heart me-2"></i>Save for Later
                                </button>
                            </div>

                            <!-- Trust Badge -->
                            <div class="trust-badge">
                                <i class="fas fa-shield-alt"></i>
                                <span>30-day money-back guarantee</span>
                            </div>

                            <div class="divider"></div>
                            
                            <!-- Course Info Grid -->
                            <div class="course-info-grid">
                                <div class="info-card">
                                    <i class="fas fa-clock"></i>
                                    <span class="info-label">Duration</span>
                                    <span class="info-value"><?php echo htmlspecialchars($courseContext['duration_label'] ?? 'Self-paced'); ?></span>
                                </div>
                                <div class="info-card">
                                    <i class="fas fa-book"></i>
                                    <span class="info-label">Lessons</span>
                                    <span class="info-value"><?php echo htmlspecialchars($courseContext['lesson_count_estimate'] ?? '—'); ?></span>
                                </div>
                                <div class="info-card">
                                    <i class="fas fa-signal"></i>
                                    <span class="info-label">Level</span>
                                    <span class="info-value"><?php echo htmlspecialchars(ucfirst($courseContext['level'] ?? 'All')); ?></span>
                                </div>
                                <div class="info-card">
                                    <i class="fas fa-globe"></i>
                                    <span class="info-label">Language</span>
                                    <span class="info-value"><?php echo htmlspecialchars($courseLanguage); ?></span>
                                </div>
                                <div class="info-card">
                                    <i class="fas fa-award"></i>
                                    <span class="info-label">Certificate</span>
                                    <span class="info-value"><?php echo !empty($courseContext['certification_enabled']) ? 'Yes' : 'No'; ?></span>
                                </div>
                                <div class="info-card">
                                    <i class="fas fa-users"></i>
                                    <span class="info-label">Students</span>
                                    <span class="info-value"><?php echo htmlspecialchars(number_format($courseContext['student_count_estimate'] ?? 0)); ?></span>
                                </div>
                            </div>

                            <div class="divider"></div>

                            <!-- Highlights -->
                            <div class="highlights-section">
                                <h6 class="highlights-title">What's Included</h6>
                                <ul class="highlights-list">
                                    <?php
                                    $highlights = [];
                                    if (!empty($courseIncludes)) {
                                        foreach ($courseIncludes as $includeItem) {
                                            $label = trim((string)$includeItem);
                                            if ($label === '') {
                                                continue;
                                            }
                                            $highlights[] = $label;
                                            if (count($highlights) >= 6) {
                                                break;
                                            }
                                        }
                                    }

                                    if (empty($highlights)) {
                                        $estimatedLessons = (int)($courseContext['lesson_count_estimate'] ?? 0);
                                        $highlights = [
                                            'Lifetime access',
                                            $estimatedLessons > 0 ? ($estimatedLessons . '+ structured lessons') : 'Practical guided lessons',
                                            'Downloadable resources',
                                            !empty($courseContext['certification_enabled']) ? 'Certificate of completion' : 'Skill completion support',
                                            'Mobile-friendly',
                                        ];
                                    }

                                    foreach ($highlights as $highlight) {
                                        echo '<li><i class="fas fa-check"></i> ' . htmlspecialchars((string)$highlight) . '</li>';
                                    }
                                    ?>
                                </ul>
                            </div>

                            <div class="divider"></div>

                            <!-- Share Section -->
                            <div class="share-section">
                                <p class="share-title">Share this course</p>
                                <div class="share-buttons">
                                    <button class="share-btn" title="Share on Facebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </button>
                                    <button class="share-btn" title="Share on Twitter">
                                        <i class="fab fa-twitter"></i>
                                    </button>
                                    <button class="share-btn" title="Share on LinkedIn">
                                        <i class="fab fa-linkedin-in"></i>
                                    </button>
                                    <button class="share-btn" title="Copy Link">
                                        <i class="fas fa-link"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Courses Section -->
    <div class="related-courses-section">
        <div class="container">
            <h2>Students also bought</h2>
            <div class="courses-grid-related">
                <?php if (!empty($relatedCourses)): ?>
                    <?php foreach ($relatedCourses as $relatedCourse): ?>
                        <article class="course-card" data-course-id="<?php echo (int)$relatedCourse['id']; ?>" role="button" tabindex="0" aria-label="Open <?php echo htmlspecialchars($relatedCourse['title']); ?>">
                            <div class="course-thumb" style="background-image:url('<?php echo htmlspecialchars($relatedCourse['thumbnail_url']); ?>')"></div>
                            <div class="course-card-content">
                                <div class="course-meta-top">
                                    <span class="course-category"><?php echo htmlspecialchars($relatedCourse['level_label']); ?></span>
                                    <span class="course-level"><?php echo htmlspecialchars($relatedCourse['level_label']); ?></span>
                                </div>
                                <h3 class="course-title"><?php echo htmlspecialchars($relatedCourse['title']); ?></h3>
                                <p class="course-instructor">By <?php echo htmlspecialchars($relatedCourse['instructor_name']); ?></p>
                                <div class="course-rating">
                                    <span class="stars">&#9733;</span>
                                    <span class="rating-num"><?php echo htmlspecialchars(number_format((float)$relatedCourse['rating'], 1)); ?></span>
                                    <span class="rating-students">(<?php echo htmlspecialchars(number_format((int)$relatedCourse['student_count_estimate'])); ?> students)</span>
                                </div>
                                <div class="course-meta-bottom">
                                    <span class="course-duration"><i class="bi bi-clock"></i> <?php echo htmlspecialchars((string)$relatedCourse['duration_label']); ?></span>
                                    <span class="course-price"><?php echo htmlspecialchars((string)$relatedCourse['price_label']); ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">More related courses will appear here soon.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    window.eduSkillCourseDetailsContext = {
        courseId: <?php echo (int)$courseContextId; ?>,
        courseTitle: <?php echo json_encode($courseTitle, JSON_UNESCAPED_UNICODE); ?>,
        baseUrl: <?php echo json_encode((string)BASE_URL, JSON_UNESCAPED_UNICODE); ?>,
        promoVideoUrl: <?php echo json_encode($coursePromoVideoUrl, JSON_UNESCAPED_UNICODE); ?>,
        isLearnerLoggedIn: <?php echo $activeLearnerId > 0 ? 'true' : 'false'; ?>,
        csrfToken: <?php echo json_encode((string)ems_csrf_token(), JSON_UNESCAPED_UNICODE); ?>,
        learnerApiUrl: <?php echo json_encode((string)(BASE_URL . 'learner/api.php'), JSON_UNESCAPED_UNICODE); ?>,
        loginUrl: <?php echo json_encode((string)(BASE_URL . 'auth/login.php'), JSON_UNESCAPED_UNICODE); ?>,
    };
    </script>
    <script src="../assets/js/courcedetails.js?v=3"></script>
</body>
</html>
