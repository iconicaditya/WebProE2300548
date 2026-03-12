<?php
/**
 * Standalone All Courses page.
 * Reuses the shared courses catalog markup with dedicated CSS and JS assets.
 */
$pageTitle = 'All Courses';
$pageStylesheet = 'allcources-aaditya.css';
$extraScripts = ['all-cources-aaditya.js'];
$renderCoursesOnly = true;
$useExternalAllCoursesAssets = true;
$hideCoursesHero = true;

require_once(__DIR__ . '/../includes/header.php');
require_once(__DIR__ . '/../includes/navbar.php');
?>
<script>
window.eduSkillBaseUrl = '<?php echo BASE_URL; ?>';
</script>
<?php require_once(__DIR__ . '/cources.php'); ?>
<?php require_once(__DIR__ . '/../includes/footer.php'); ?>