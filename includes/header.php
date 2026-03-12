<?php
/**
 * Header Include File
 * EduSkill Marketplace System (EMS)
 * 
 * This file should be included at the beginning of all pages.
 * It includes Bootstrap 5 CDN and sets up the HTML structure.
 */

// Include configuration files
require_once(__DIR__ . '/../config/config.php');

$assetVersion = isset($assetVersion) ? $assetVersion : APP_VERSION;
$pageStylesheet = isset($pageStylesheet) ? $pageStylesheet : 'aaditya.css';
$extraStylesheets = (isset($extraStylesheets) && is_array($extraStylesheets)) ? $extraStylesheets : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="EduSkill Marketplace System - Connect learners with skill providers">
    <meta name="author" content="EMS Development Team">
    
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' . APP_NAME : APP_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-9ndCyUaIbzAi2FUarbnLmA+4sSeDgFHka+Y1R0KXGXCcasV4Un7RCN6+IHohkiYE" 
          crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/main.css?v=<?php echo $assetVersion; ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/<?php echo htmlspecialchars($pageStylesheet); ?>?v=<?php echo $assetVersion; ?>">
    <?php foreach ($extraStylesheets as $sheet): ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/<?php echo htmlspecialchars($sheet); ?>?v=<?php echo $assetVersion; ?>">
    <?php endforeach; ?>
    <?php if (!isset($skipSandhyaCss) || !$skipSandhyaCss): ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sandhya.css?v=<?php echo $assetVersion; ?>">
    <?php endif; ?>
    <!-- Brand fonts (Inter for UI, Merriweather for headings) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    
</head>
<body class="<?php echo isset($bodyClass) ? htmlspecialchars($bodyClass) : ''; ?>">
    <!-- Navigation will be included in each page -->
    <!-- Main content area starts after navbar -->
