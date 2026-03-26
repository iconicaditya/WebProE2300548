<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/../learner/includes/learner_data.php');

$activeLearner = null;
$activeLearnerId = 0;
if (ems_is_logged_in() && ems_current_role() === 'learner') {
    $activeLearner = ems_load_portal_user($conn);
    $activeLearnerId = (int)($activeLearner['id'] ?? 0);
}

$courseContext = ems_learner_get_course_by_title($conn, 'React.js & Modern Frontend Development');
$courseContextId = (int)($courseContext['id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>React.js & Modern Frontend Development | EduSkill Marketplace</title>
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
                        <h1 class="course-main-title">React.js & Modern Frontend Development</h1>
                        <p class="course-description">Master React hooks, state management, routing, and deployment to build real-world single-page applications with industry best practices.</p>
                        
                        <div class="course-rating-section">
                            <div class="rating-display">
                                <span class="rating-number">4.7</span>
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <span class="rating-count">(2,140 ratings)</span>
                            </div>
                            <div class="course-stats">
                                <span class="stat-item"><strong>15,420</strong> students</span>
                                <span class="stat-divider">•</span>
                                <span class="stat-item">Created by <strong>Saurav Pandey</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Instructor Card -->
                    <div class="instructor-card">
                        <div class="instructor-header">
                            <img src="../assets/images/cources/react-frontend.jpg" alt="Instructor" class="instructor-img">
                            <div class="instructor-info">
                                <h5>Instructor</h5>
                                <h4>Saurav Pandey</h4>
                                <p>Senior React Developer | 10+ Years Experience</p>
                            </div>
                        </div>
                        <p class="instructor-bio">Saurav is a passionate educator with over 10 years of experience in web development. He has trained more than 50,000 students worldwide and is committed to making complex concepts simple and understandable.</p>
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
                                        <div class="point">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Build and deploy full single-page applications (SPAs)</span>
                                        </div>
                                        <div class="point">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Master React hooks and functional components</span>
                                        </div>
                                        <div class="point">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Manage complex application state with Redux</span>
                                        </div>
                                        <div class="point">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Integrate REST APIs and handle authentication</span>
                                        </div>
                                        <div class="point">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Implement client-side routing with React Router</span>
                                        </div>
                                        <div class="point">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Deploy applications to production environments</span>
                                        </div>
                                        <div class="point">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Write clean, maintainable, and scalable code</span>
                                        </div>
                                        <div class="point">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Build responsive UIs with Tailwind CSS</span>
                                        </div>
                                    </div>

                                    <h3 class="mt-5">Course Description</h3>
                                    <p>This comprehensive React course is designed for developers who want to master modern frontend development. Starting from the basics, you'll progress through intermediate concepts to advanced patterns used in production applications.</p>
                                    <p>Throughout the course, you'll work on real-world projects that demonstrate practical applications of React concepts. Each module includes hands-on exercises, quizzes, and projects to reinforce your learning.</p>

                                    <h3 class="mt-5">Requirements</h3>
                                    <ul class="requirements-list">
                                        <li>Basic knowledge of JavaScript (ES6+)</li>
                                        <li>Familiarity with HTML and CSS</li>
                                        <li>Node.js and npm installed on your computer</li>
                                        <li>A code editor (VS Code recommended)</li>
                                        <li>Willingness to practice and build projects</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Curriculum Tab -->
                            <div class="tab-pane fade" id="curriculum" role="tabpanel">
                                <div class="curriculum-section">
                                    <h3>Course Curriculum</h3>
                                    
                                    <div class="curriculum-module">
                                        <div class="module-header" data-bs-toggle="collapse" data-bs-target="#module1">
                                            <div class="module-title">
                                                <i class="fas fa-chevron-down"></i>
                                                <span class="module-number">Section 1:</span>
                                                <span class="module-name">React Fundamentals & JSX</span>
                                            </div>
                                            <span class="module-meta">8 lectures • 2h 30m</span>
                                        </div>
                                        <div class="collapse" id="module1">
                                            <div class="module-content">
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>1. Introduction to React</span>
                                                    <span class="lesson-duration">15:30</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>2. Components and Props</span>
                                                    <span class="lesson-duration">22:45</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>3. JSX Deep Dive</span>
                                                    <span class="lesson-duration">18:20</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>4. Rendering Lists and Conditional Rendering</span>
                                                    <span class="lesson-duration">25:10</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-file-alt"></i>
                                                    <span>5. Quiz: React Basics</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-code"></i>
                                                    <span>6. Project: Build a Todo App</span>
                                                    <span class="lesson-duration">1h 15m</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="curriculum-module">
                                        <div class="module-header" data-bs-toggle="collapse" data-bs-target="#module2">
                                            <div class="module-title">
                                                <i class="fas fa-chevron-down"></i>
                                                <span class="module-number">Section 2:</span>
                                                <span class="module-name">React Hooks & State Management</span>
                                            </div>
                                            <span class="module-meta">10 lectures • 3h 45m</span>
                                        </div>
                                        <div class="collapse" id="module2">
                                            <div class="module-content">
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>1. useState Hook</span>
                                                    <span class="lesson-duration">20:15</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>2. useEffect Hook</span>
                                                    <span class="lesson-duration">28:40</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>3. useContext for Global State</span>
                                                    <span class="lesson-duration">25:30</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>4. Custom Hooks</span>
                                                    <span class="lesson-duration">22:50</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-file-alt"></i>
                                                    <span>5. Quiz: React Hooks</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-code"></i>
                                                    <span>6. Project: Weather App with Hooks</span>
                                                    <span class="lesson-duration">1h 30m</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="curriculum-module">
                                        <div class="module-header" data-bs-toggle="collapse" data-bs-target="#module3">
                                            <div class="module-title">
                                                <i class="fas fa-chevron-down"></i>
                                                <span class="module-number">Section 3:</span>
                                                <span class="module-name">Redux & Advanced State Management</span>
                                            </div>
                                            <span class="module-meta">8 lectures • 3h 20m</span>
                                        </div>
                                        <div class="collapse" id="module3">
                                            <div class="module-content">
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>1. Redux Fundamentals</span>
                                                    <span class="lesson-duration">18:45</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>2. Redux Toolkit Setup</span>
                                                    <span class="lesson-duration">20:30</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>3. Async Actions with Thunk</span>
                                                    <span class="lesson-duration">25:15</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-code"></i>
                                                    <span>4. Project: E-commerce Cart with Redux</span>
                                                    <span class="lesson-duration">1h 45m</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="curriculum-module">
                                        <div class="module-header" data-bs-toggle="collapse" data-bs-target="#module4">
                                            <div class="module-title">
                                                <i class="fas fa-chevron-down"></i>
                                                <span class="module-number">Section 4:</span>
                                                <span class="module-name">React Router & Navigation</span>
                                            </div>
                                            <span class="module-meta">6 lectures • 2h 15m</span>
                                        </div>
                                        <div class="collapse" id="module4">
                                            <div class="module-content">
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>1. React Router Setup</span>
                                                    <span class="lesson-duration">15:20</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>2. Dynamic Routing</span>
                                                    <span class="lesson-duration">18:45</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-code"></i>
                                                    <span>3. Project: Multi-page SPA</span>
                                                    <span class="lesson-duration">1h 30m</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="curriculum-module">
                                        <div class="module-header" data-bs-toggle="collapse" data-bs-target="#module5">
                                            <div class="module-title">
                                                <i class="fas fa-chevron-down"></i>
                                                <span class="module-number">Section 5:</span>
                                                <span class="module-name">API Integration & Deployment</span>
                                            </div>
                                            <span class="module-meta">7 lectures • 2h 50m</span>
                                        </div>
                                        <div class="collapse" id="module5">
                                            <div class="module-content">
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>1. Fetching Data from APIs</span>
                                                    <span class="lesson-duration">20:10</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>2. Error Handling & Loading States</span>
                                                    <span class="lesson-duration">18:30</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>3. Deploying to Production</span>
                                                    <span class="lesson-duration">22:45</span>
                                                </div>
                                                <div class="lesson">
                                                    <i class="fas fa-code"></i>
                                                    <span>4. Capstone Project: Full Stack App</span>
                                                    <span class="lesson-duration">2h 30m</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reviews Tab -->
                            <div class="tab-pane fade" id="reviews" role="tabpanel">
                                <div class="reviews-section">
                                    <h3>Student Reviews</h3>
                                    
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-info">
                                                <h5>Amit Sharma</h5>
                                                <div class="review-rating">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </div>
                                            </div>
                                            <span class="review-date">2 weeks ago</span>
                                        </div>
                                        <p class="review-text">"The best React course I've ever taken. The syllabus is deep and practical! Saurav explains complex concepts in a very simple way. Highly recommended for anyone wanting to master React."</p>
                                    </div>

                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-info">
                                                <h5>Priya Singh</h5>
                                                <div class="review-rating">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </div>
                                            </div>
                                            <span class="review-date">1 month ago</span>
                                        </div>
                                        <p class="review-text">"Loved the hands-on labs and real-world projects. The instructor is very responsive to questions. This course helped me land my first React job!"</p>
                                    </div>

                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-info">
                                                <h5>Rajesh Kumar</h5>
                                                <div class="review-rating">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star-half-alt"></i>
                                                </div>
                                            </div>
                                            <span class="review-date">1 month ago</span>
                                        </div>
                                        <p class="review-text">"Great course with excellent projects. The only thing I'd improve is adding more advanced patterns like render props and compound components."</p>
                                    </div>
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
                            <img src="../assets/images/cources/react-frontend.jpg" alt="React.js Course">
                            <div class="image-overlay">
                                <button class="play-btn-overlay">
                                    <i class="fas fa-play"></i>
                                </button>
                                <p class="preview-text">Preview this course</p>
                            </div>
                        </div>

                        <!-- Price Card -->
                        <div class="price-card" data-course-id="<?php echo (int)$courseContextId; ?>" data-learner-auth="<?php echo $activeLearnerId > 0 ? '1' : '0'; ?>">
                            <!-- Price Section -->
                            <div class="price-section">
                                <div class="price-badge">
                                    <span class="discount-badge">40% OFF</span>
                                </div>
                                <div class="price-display">
                                    <span class="price-amount">$119</span>
                                    <span class="price-original">$199</span>
                                </div>
                                <p class="price-note">Limited time offer �� Ends in 3 days</p>
                            </div>

                            <!-- Primary Action Button -->
                            <a href="payment.php<?php echo $courseContextId > 0 ? '?course_id=' . (int)$courseContextId : ''; ?>" class="btn btn-enroll-primary w-100 mb-3" data-course-id="<?php echo (int)$courseContextId; ?>">
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
                                    <span class="info-value">8 weeks</span>
                                </div>
                                <div class="info-card">
                                    <i class="fas fa-book"></i>
                                    <span class="info-label">Lessons</span>
                                    <span class="info-value">45</span>
                                </div>
                                <div class="info-card">
                                    <i class="fas fa-signal"></i>
                                    <span class="info-label">Level</span>
                                    <span class="info-value">Intermediate</span>
                                </div>
                                <div class="info-card">
                                    <i class="fas fa-globe"></i>
                                    <span class="info-label">Language</span>
                                    <span class="info-value">English</span>
                                </div>
                                <div class="info-card">
                                    <i class="fas fa-award"></i>
                                    <span class="info-label">Certificate</span>
                                    <span class="info-value">Yes</span>
                                </div>
                                <div class="info-card">
                                    <i class="fas fa-users"></i>
                                    <span class="info-label">Students</span>
                                    <span class="info-value">15.4K</span>
                                </div>
                            </div>

                            <div class="divider"></div>

                            <!-- Highlights -->
                            <div class="highlights-section">
                                <h6 class="highlights-title">What's Included</h6>
                                <ul class="highlights-list">
                                    <li><i class="fas fa-check"></i> Lifetime access</li>
                                    <li><i class="fas fa-check"></i> 45+ video lectures</li>
                                    <li><i class="fas fa-check"></i> Downloadable resources</li>
                                    <li><i class="fas fa-check"></i> Certificate of completion</li>
                                    <li><i class="fas fa-check"></i> Mobile-friendly</li>
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
                <article class="course-card">
                    <div class="course-thumb" style="background-image:url('../assets/images/cources/web-dev.jpg')"></div>
                    <div class="course-card-content">
                        <div class="course-meta-top">
                            <span class="course-category">Programming</span>
                            <span class="course-level">Beginner</span>
                        </div>
                        <h3 class="course-title">Full-Stack Web Development Bootcamp</h3>
                        <p class="course-instructor">By Aaditya Sharma</p>
                        <div class="course-rating">
                            <span class="stars">&#9733;</span>
                            <span class="rating-num">4.8</span>
                            <span class="rating-students">(3,200 students)</span>
                        </div>
                        <div class="course-meta-bottom">
                            <span class="course-duration"><i class="bi bi-clock"></i> 10 Weeks</span>
                            <span class="course-price">$129</span>
                        </div>
                    </div>
                </article>

                <article class="course-card">
                    <div class="course-thumb" style="background-image:url('../assets/images/cources/data-analytics.jpg')"></div>
                    <div class="course-card-content">
                        <div class="course-meta-top">
                            <span class="course-category">Data Science</span>
                            <span class="course-level">Intermediate</span>
                        </div>
                        <h3 class="course-title">Data Analytics With Python and Power BI</h3>
                        <p class="course-instructor">By Nisha Koirala</p>
                        <div class="course-rating">
                            <span class="stars">&#9733;</span>
                            <span class="rating-num">4.9</span>
                            <span class="rating-students">(2,050 students)</span>
                        </div>
                        <div class="course-meta-bottom">
                            <span class="course-duration"><i class="bi bi-clock"></i> 8 Weeks</span>
                            <span class="course-price">$149</span>
                        </div>
                    </div>
                </article>

                <article class="course-card">
                    <div class="course-thumb" style="background-image:url('../assets/images/cources/ui-ux.jpg')"></div>
                    <div class="course-card-content">
                        <div class="course-meta-top">
                            <span class="course-category">Design</span>
                            <span class="course-level">Beginner</span>
                        </div>
                        <h3 class="course-title">UI/UX Design for Digital Products</h3>
                        <p class="course-instructor">By Karan Basnet</p>
                        <div class="course-rating">
                            <span class="stars">&#9733;</span>
                            <span class="rating-num">4.7</span>
                            <span class="rating-students">(1,480 students)</span>
                        </div>
                        <div class="course-meta-bottom">
                            <span class="course-duration"><i class="bi bi-clock"></i> 6 Weeks</span>
                            <span class="course-price">$99</span>
                        </div>
                    </div>
                </article>

                <article class="course-card">
                    <div class="course-thumb" style="background-image:url('../assets/images/cources/nodejs-backend.jpg')"></div>
                    <div class="course-card-content">
                        <div class="course-meta-top">
                            <span class="course-category">Programming</span>
                            <span class="course-level">Intermediate</span>
                        </div>
                        <h3 class="course-title">Node.js & Express Backend Development</h3>
                        <p class="course-instructor">By Kabir Lama</p>
                        <div class="course-rating">
                            <span class="stars">&#9733;</span>
                            <span class="rating-num">4.7</span>
                            <span class="rating-students">(1,380 students)</span>
                        </div>
                        <div class="course-meta-bottom">
                            <span class="course-duration"><i class="bi bi-clock"></i> 7 Weeks</span>
                            <span class="course-price">$119</span>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    window.eduSkillCourseDetailsContext = {
        courseId: <?php echo (int)$courseContextId; ?>,
        isLearnerLoggedIn: <?php echo $activeLearnerId > 0 ? 'true' : 'false'; ?>,
        csrfToken: <?php echo json_encode((string)ems_csrf_token(), JSON_UNESCAPED_UNICODE); ?>,
        learnerApiUrl: <?php echo json_encode((string)(BASE_URL . 'learner/api.php'), JSON_UNESCAPED_UNICODE); ?>,
        loginUrl: <?php echo json_encode((string)(BASE_URL . 'auth/login.php'), JSON_UNESCAPED_UNICODE); ?>,
    };
    </script>
    <script src="../assets/js/courcedetails.js?v=3"></script>
</body>
</html>
