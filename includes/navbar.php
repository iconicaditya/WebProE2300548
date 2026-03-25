<?php
/**
 * Custom Navbar matching screenshot (two layers, logo only, no border, no subtitle)
 */
?>
<!-- EduSkill Custom Navbar -->
<nav class="eduskill-navbar">
  <div class="navbar-container">
    <!-- Logo only -->
    <a class="eduskill-logo" href="<?php echo BASE_URL; ?>" style="display:flex; align-items:center; height:64px; flex:0 0 auto;">
      <img src="<?php echo BASE_URL; ?>assets/images/logo-eduskill.png" alt="EDUSKILL Logo" style="height:36px; width:auto; max-width:100%; display:block;">
    </a>
    <button type="button" class="mobile-menu-toggle" aria-label="Toggle navigation" aria-expanded="false">
      <span></span>
      <span></span>
      <span></span>
      <span></span>
    </button>
    <!-- Search -->
    <div class="eduskill-search">
      <span class="search-icon">
        <svg width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="8" cy="8" r="7" stroke="#b0b7c3" stroke-width="2"/><path d="M13.5 13.5L17 17" stroke="#b0b7c3" stroke-width="2" stroke-linecap="round"/></svg>
      </span>
      <input type="text" placeholder="Search for anything">
    </div>
    <!-- Right links and actions -->
    <div class="navbar-actions">
      <a href="<?php echo BASE_URL; ?>index.php" class="navbar-action-link">
        <span class="nav-link-icon" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 11.5L12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5z" stroke="#6a6f73" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        Home
      </a>
      <a href="<?php echo BASE_URL; ?>pages/allcources.php" class="navbar-action-link">
        <span class="nav-link-icon" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6z" stroke="#6a6f73" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 10h8" stroke="#6a6f73" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        All Courses
      </a>
      <span class="cart-icon" title="Cart">
        <svg width="22" height="22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.5 19a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm10 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM2 3h2.27a1 1 0 0 1 .98.8l2.1 10.5a1 1 0 0 0 .98.8h7.72a1 1 0 0 0 .98-.8l1.38-6.5H5.25" stroke="#333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </span>
      <span class="wishlist-icon" title="Wishlist">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.8 4.6a5.3 5.3 0 0 0-7.5 0L12 6l-1.3-1.4a5.3 5.3 0 0 0-7.5 0 5.5 5.5 0 0 0 0 7.8L12 22l8.8-9.6a5.5 5.5 0 0 0 0-7.8z" stroke="#333" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </span>
      <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn btn-login text-decoration-none">Log in</a>
      <div class="register-menu">
        <a href="#" class="btn btn-signup text-decoration-none" aria-expanded="false">
          <span>Register</span>
          <span class="register-chevron" aria-hidden="true">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </span>
        </a>
        <div class="register-dropdown">
          <a href="<?php echo BASE_URL; ?>auth/register-learner.php">Register as Learner</a>
          <a href="<?php echo BASE_URL; ?>auth/register-provider.php">Register as Provider</a>
        </div>
      </div>
    </div>
  </div>
  <!-- Category Links Layer -->
  <div class="navbar-links">
    <div>
    <a href="#">Programming</a>
    <a href="#">Bussiness</a>
    <a href="#">Cyber security</a>
    <a href="#">Data Science</a>
    <a href="#">Digital Marketing</a>
    <a href="#">DevOps</a>
    <a href="#">cloud</a>
    <a href="#">Project Management</a>
    <span class="more-link">......More</span>
    </div>
  </div>
</nav>

<script>
  (function () {
    const navbar = document.querySelector('.eduskill-navbar');
    if (!navbar) return;
    const toggleButton = navbar.querySelector('.mobile-menu-toggle');
    const registerMenu = navbar.querySelector('.register-menu');
    const registerTrigger = registerMenu ? registerMenu.querySelector('.btn-signup') : null;
    if (!toggleButton) return;

    toggleButton.addEventListener('click', function () {
      const isOpen = navbar.classList.toggle('mobile-open');
      toggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      if (!isOpen && registerMenu) {
        registerMenu.classList.remove('open');
      }
    });

    if (registerTrigger && registerMenu) {
      registerTrigger.addEventListener('click', function (event) {
        event.preventDefault();
        const isRegisterOpen = registerMenu.classList.toggle('open');
        registerTrigger.setAttribute('aria-expanded', isRegisterOpen ? 'true' : 'false');
      });

      document.addEventListener('click', function (event) {
        if (!registerMenu.contains(event.target)) {
          registerMenu.classList.remove('open');
          registerTrigger.setAttribute('aria-expanded', 'false');
        }
      });

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
          registerMenu.classList.remove('open');
          registerTrigger.setAttribute('aria-expanded', 'false');
        }
      });
    }
  })();
</script>
