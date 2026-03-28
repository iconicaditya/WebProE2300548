<?php
/**
 * Landing Page / Home Page
 * EduSkill Marketplace System (EMS)
 * 
 * This is the main entry point of the application.
 * Currently a placeholder landing page structure.
 */

// Load configuration and database connection for dynamic course loading
require_once(__DIR__ . '/config/config.php');
require_once(__DIR__ . '/config/db.php');

$extraStylesheets = ['about-aaditya.css', 'partnership-aaditya.css', 'testinomials-aaditya.css', 'faq-aaditya.css', 'support-aaditya.css'];
$extraScripts = ['about-aaditya.js', 'partnership-aaditya.js', 'testinomials-aaditya.js', 'faq-aaditya.js', 'support-aaditya.js'];
?>
<?php require_once(__DIR__ . '/includes/header.php'); ?>
<?php require_once(__DIR__ . '/includes/navbar.php'); ?>

<!-- Hero Section (Full Width) -->
<?php require_once(__DIR__ . '/pages/hero.php'); ?>

<!-- Courses Section (Reused from cources.php) -->
<?php $embedCoursesSection = true; ?>
<?php require_once(__DIR__ . '/pages/cources.php'); ?>
<?php unset($embedCoursesSection); ?>

<!-- About Section -->
<?php require_once(__DIR__ . '/pages/about.php'); ?>

<!-- Partnership Institutes Section -->
<?php require_once(__DIR__ . '/pages/partnership.php'); ?>

<!-- Testimonials Section -->
<?php require_once(__DIR__ . '/pages/testinomials.php'); ?>

<!-- FAQ Section -->
<?php $embedFaqSection = true; ?>
<?php require_once(__DIR__ . '/pages/faq.php'); ?>
<?php unset($embedFaqSection); ?>

<!-- Support Section -->
<?php $embedSupportSection = true; ?>
<?php require_once(__DIR__ . '/pages/support.php'); ?>
<?php unset($embedSupportSection); ?>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
