<?php
/**
 * Footer Include File
 * EduSkill Marketplace System (EMS)
 * 
 * This file should be included at the end of all pages.
 * It closes HTML tags and includes Bootstrap JS CDN.
 */
?>
    <footer class="realhomes-footer">
        <div class="realhomes-footer-inner">
            <div class="realhomes-footer-content">
                <div class="realhomes-col realhomes-col-about">
                    <div class="footer-panel footer-panel-logo">
                        <img src="<?php echo BASE_URL; ?>assets/images/logo-eduskill.png" alt="EduSkill Marketplace">
                    </div>
                    <p class="realhomes-about-text">EduSkill Marketplace connects learners with skilled providers to discover practical courses, build career-ready knowledge, and grow through trusted learning opportunities.</p>
                </div>

                <div class="realhomes-col realhomes-col-links">
                    <h5 class="footer-panel-title">Quick Links</h5>
                    <div class="footer-panel">
                        <ul class="realhomes-links-list">
                            <li><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                            <li><a href="<?php echo BASE_URL; ?>auth/login.php">Login</a></li>
                            <li><a href="<?php echo BASE_URL; ?>auth/register-learner.php">Register Learner</a></li>
                            <li><a href="<?php echo BASE_URL; ?>auth/register-provider.php">Register Provider</a></li>
                        </ul>
                    </div>
                </div>

                <div class="realhomes-col realhomes-col-contact">
                    <h5 class="footer-panel-title">Contact Us</h5>
                    <div class="footer-panel">
                    <ul class="realhomes-contact-list">
                        <li>
                            <span class="icon"><i class="bi bi-geo-alt-fill"></i></span>
                            <span>EduSkill Marketplace Office,<br>Kathmandu, Nepal</span>
                        </li>
                        <li>
                            <span class="icon"><i class="bi bi-whatsapp"></i></span>
                            <span>+9779864062605</span>
                        </li>
                        <li>
                            <span class="icon"><i class="bi bi-envelope-fill"></i></span>
                            <span>support@eduskillmarketplace.com</span>
                        </li>
                    </ul>
                    </div>
                </div>

                <div class="realhomes-col realhomes-col-newsletter">
                    <h5 class="footer-panel-title">Stay Updated</h5>
                    <div class="footer-panel">
                        <form class="realhomes-newsletter" action="#" method="post" onsubmit="return false;">
                            <input type="email" placeholder="Enter your email address" aria-label="Your email address">
                            <button type="submit">Subscribe</button>
                        </form>
                        <div class="realhomes-social" aria-label="Social links">
                            <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                            <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                            <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="realhomes-footer-bottom">
                <span>© 2026 EduSkill Marketplace. All rights reserved.</span>
                <span>Built for learners, providers, and education officers.</span>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-geWF76RCwLtnZ8qwWbSxccPQtF3EpF3fnJHog6LaEVF6V6O9itMtAWV2nP47PSDNVe" 
            crossorigin="anonymous"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?php echo BASE_URL; ?>assets/js/main.js?v=<?php echo APP_VERSION; ?>"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/aaditya.js?v=<?php echo APP_VERSION; ?>"></script>
    
    <!-- Page-specific scripts can be added before closing body tag -->
</body>
</html>
