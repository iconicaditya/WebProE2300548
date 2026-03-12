<?php
/**
 * Landing Page / Home Page
 * EduSkill Marketplace System (EMS)
 * 
 * This is the main entry point of the application.
 * Currently a placeholder landing page structure.
 */
?>
<?php require_once(__DIR__ . '/includes/header.php'); ?>
<?php require_once(__DIR__ . '/includes/navbar.php'); ?>

<!-- Hero Section (Full Width) -->
<?php require_once(__DIR__ . '/pages/hero.php'); ?>

<!-- Courses Section (Reused from cources.php) -->
<?php $embedCoursesSection = true; ?>
<?php require_once(__DIR__ . '/pages/cources.php'); ?>
<?php unset($embedCoursesSection); ?>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
