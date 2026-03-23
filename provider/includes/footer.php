<!-- PROVIDER DASHBOARD FOOTER -->
<footer class="provider-dashboard-footer">
    <p class="footer-text">© 2026 EduSkill Marketplace. All rights reserved.</p>
</footer>

<?php
// Ensure $extraScripts and $assetVersion are available
$extraScripts = (isset($extraScripts) && is_array($extraScripts)) ? $extraScripts : [];
$useVersion = isset($assetVersion) ? $assetVersion : APP_VERSION;
?>

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-geWF76RCwLtnZ8qwWbSxccPQtF3EpF3fnJHog6LaEVF6V6O9itMtAWV2nP47PSDNVe" 
            crossorigin="anonymous"></script>
    
    <!-- Custom JavaScript -->
    <!-- <script src="<?php echo BASE_URL; ?>assets/js/main.js?v=<?php echo $useVersion; ?>"></script> -->
    <script src="<?php echo BASE_URL; ?>assets/js/sandhya.js?v=<?php echo $useVersion; ?>"></script>
    <?php foreach ($extraScripts as $script): ?>
    <script src="<?php echo BASE_URL; ?>assets/js/<?php echo htmlspecialchars($script); ?>?v=<?php echo $useVersion; ?>"></script>
    <?php endforeach; ?>

</body>
</html>
