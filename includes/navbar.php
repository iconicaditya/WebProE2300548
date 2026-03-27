<?php
/**
 * Custom Navbar matching screenshot (two layers, logo only, no border, no subtitle)
 */
if (!isset($conn)) {
    require_once(__DIR__ . '/../config/config.php');
    require_once(__DIR__ . '/../config/db.php');
}
if (!function_exists('ems_is_logged_in')) {
    require_once(__DIR__ . '/auth.php');
}

$navIsLoggedIn = ems_is_logged_in();
$navRole = (string)(ems_current_role() ?? '');
$navAuthUser = ems_current_user();
$navDisplayName = trim((string)($navAuthUser['full_name'] ?? 'User'));
if ($navDisplayName === '') {
  $navDisplayName = 'User';
}

$navInitials = function_exists('ems_user_initials')
  ? ems_user_initials($navDisplayName)
  : strtoupper(substr($navDisplayName, 0, 1));

$navProfilePhotoUrl = '';
if ($navIsLoggedIn && function_exists('ems_load_portal_user') && isset($conn) && $conn) {
  $navPortalUser = ems_load_portal_user($conn);
  if (is_array($navPortalUser)) {
    $portalName = trim((string)($navPortalUser['full_name'] ?? ''));
    if ($portalName !== '') {
      $navDisplayName = $portalName;
      $navInitials = function_exists('ems_user_initials')
        ? ems_user_initials($navDisplayName)
        : strtoupper(substr($navDisplayName, 0, 1));
    }

    $rawPhoto = trim((string)($navPortalUser['profile_photo_url'] ?? ''));
    if ($rawPhoto !== '') {
      $navProfilePhotoUrl = preg_match('#^https?://#i', $rawPhoto)
        ? $rawPhoto
        : (BASE_URL . ltrim($rawPhoto, '/'));
    }
  }
}

$navDashboardPath = ems_dashboard_path_for_role($navRole !== '' ? $navRole : 'learner');
$navDashboardUrl = BASE_URL . ltrim($navDashboardPath, '/');
$navMyCoursesUrl = $navRole === 'learner' ? (BASE_URL . 'learner/?page=courses') : $navDashboardUrl;
$navShowLearnerActions = !$navIsLoggedIn || $navRole === 'learner';
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
      <?php if ($navShowLearnerActions): ?>
      <span class="cart-icon" title="Cart">
        <svg width="22" height="22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.5 19a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm10 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM2 3h2.27a1 1 0 0 1 .98.8l2.1 10.5a1 1 0 0 0 .98.8h7.72a1 1 0 0 0 .98-.8l1.38-6.5H5.25" stroke="#333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <?php
          if ($navIsLoggedIn && $navRole === 'learner') {
            require_once(__DIR__ . '/../learner/includes/learner_data.php');
            $cartCount = ems_learner_count_cart_items($conn, (int)$_SESSION['auth_user']['id']);
            if ($cartCount > 0) {
              echo '<span class="cart-badge">' . $cartCount . '</span>';
            }
          }
        ?>
      </span>
      <span class="wishlist-icon" title="Wishlist">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.8 4.6a5.3 5.3 0 0 0-7.5 0L12 6l-1.3-1.4a5.3 5.3 0 0 0-7.5 0 5.5 5.5 0 0 0 0 7.8L12 22l8.8-9.6a5.5 5.5 0 0 0 0-7.8z" stroke="#333" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <?php
          if ($navIsLoggedIn && $navRole === 'learner') {
            $wishlistCount = ems_learner_count_wishlist_items($conn, (int)$_SESSION['auth_user']['id']);
            if ($wishlistCount > 0) {
              echo '<span class="wishlist-badge">' . $wishlistCount . '</span>';
            }
          }
        ?>
      </span>
      <?php endif; ?>

      <?php if (!$navIsLoggedIn): ?>
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
      <?php else: ?>
        <div class="navbar-user-menu">
          <button type="button" class="navbar-user-trigger" aria-expanded="false">
            <?php if ($navProfilePhotoUrl !== ''): ?>
              <img src="<?php echo ems_e($navProfilePhotoUrl); ?>" alt="<?php echo ems_e($navDisplayName); ?>" class="navbar-user-avatar-img">
            <?php else: ?>
              <span class="navbar-user-avatar"><?php echo ems_e($navInitials); ?></span>
            <?php endif; ?>
            <span class="navbar-user-name"><?php echo ems_e($navDisplayName); ?></span>
            <i class="bi bi-chevron-down" aria-hidden="true"></i>
          </button>
          <div class="navbar-user-dropdown" hidden>
            <a href="<?php echo ems_e($navDashboardUrl); ?>">
              <i class="bi bi-speedometer2"></i>
              <span>Dashboard</span>
            </a>
            <?php if ($navRole === 'learner'): ?>
              <a href="<?php echo ems_e($navMyCoursesUrl); ?>">
                <i class="bi bi-journal-check"></i>
                <span>My Courses</span>
              </a>
            <?php endif; ?>
            <a href="<?php echo BASE_URL; ?>auth/logout.php">
              <i class="bi bi-box-arrow-right"></i>
              <span>Logout</span>
            </a>
          </div>
        </div>
      <?php endif; ?>
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
    const userMenu = navbar.querySelector('.navbar-user-menu');
    const userTrigger = userMenu ? userMenu.querySelector('.navbar-user-trigger') : null;
    const userDropdown = userMenu ? userMenu.querySelector('.navbar-user-dropdown') : null;

    function closeRegisterMenu() {
      if (!registerMenu || !registerTrigger) {
        return;
      }
      registerMenu.classList.remove('open');
      registerTrigger.setAttribute('aria-expanded', 'false');
    }

    function closeUserMenu() {
      if (!userMenu || !userTrigger || !userDropdown) {
        return;
      }
      userMenu.classList.remove('open');
      userDropdown.hidden = true;
      userTrigger.setAttribute('aria-expanded', 'false');
    }

    if (!toggleButton) return;

    toggleButton.addEventListener('click', function () {
      const isOpen = navbar.classList.toggle('mobile-open');
      toggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      if (!isOpen) {
        closeRegisterMenu();
        closeUserMenu();
      }
    });

    if (registerTrigger && registerMenu) {
      registerTrigger.addEventListener('click', function (event) {
        event.preventDefault();
        closeUserMenu();
        const isRegisterOpen = registerMenu.classList.toggle('open');
        registerTrigger.setAttribute('aria-expanded', isRegisterOpen ? 'true' : 'false');
      });
    }

    if (userTrigger && userMenu && userDropdown) {
      userTrigger.addEventListener('click', function (event) {
        event.preventDefault();
        closeRegisterMenu();
        const isOpen = userMenu.classList.toggle('open');
        userDropdown.hidden = !isOpen;
        userTrigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      });
    }

    document.addEventListener('click', function (event) {
      if (registerMenu && !registerMenu.contains(event.target)) {
        closeRegisterMenu();
      }
      if (userMenu && !userMenu.contains(event.target)) {
        closeUserMenu();
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeRegisterMenu();
        closeUserMenu();
      }
    });
  })();

  // Cart and Wishlist functionality
  (function() {
    const cartIcon = document.querySelector('.cart-icon');
    const wishlistIcon = document.querySelector('.wishlist-icon');
    const baseUrl = window.eduSkillBaseUrl || <?php echo json_encode(BASE_URL); ?>;
    const isLoggedIn = <?php echo $navIsLoggedIn && $navRole === 'learner' ? 'true' : 'false'; ?>;
    const csrfToken = <?php echo json_encode((string)ems_csrf_token()); ?>;
    const learnerApiUrl = baseUrl + 'learner/api.php';
    const loginUrl = baseUrl + 'auth/login.php';

    function showLoginPrompt() {
      if (!isLoggedIn) {
        const modal = document.createElement('div');
        modal.className = 'navbar-login-modal';
        modal.innerHTML = `
          <div class="modal-backdrop"></div>
          <div class="modal-content">
            <div class="modal-header">
              <h5>Sign In Required</h5>
              <button type="button" class="btn-close" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p>Please log in to add courses to your cart or wishlist.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-dismiss">Cancel</button>
              <a href="${loginUrl}" class="btn btn-primary">Sign In</a>
            </div>
          </div>
        `;
        
        document.body.appendChild(modal);
        
        const closeBtn = modal.querySelector('.btn-close');
        const dismissBtn = modal.querySelector('.btn-dismiss');
        const backdrop = modal.querySelector('.modal-backdrop');
        
        function closeModal() {
          modal.remove();
        }
        
        closeBtn.addEventListener('click', closeModal);
        dismissBtn.addEventListener('click', closeModal);
        backdrop.addEventListener('click', closeModal);
        
        return false;
      }
      return true;
    }

    if (cartIcon) {
      cartIcon.style.cursor = 'pointer';
      cartIcon.addEventListener('click', function() {
        if (!showLoginPrompt()) {
          return;
        }
        
        // Logged in - get current course ID if on details page
        const courseId = window.eduSkillCourseDetailsContext ? window.eduSkillCourseDetailsContext.courseId : 0;
        if (courseId <= 0) {
          alert('Please navigate to a course page first.');
          return;
        }
        
        // Add to cart
        const data = new FormData();
        data.append('action', 'cart_add');
        data.append('course_id', courseId);
        data.append('csrf_token', csrfToken);
        
        fetch(learnerApiUrl, {
          method: 'POST',
          body: data
        })
        .then(response => response.json())
        .then(result => {
          if (result.ok) {
            alert('Course added to cart!');
          } else {
            alert('Error: ' + (result.message || 'Unable to add to cart'));
          }
        })
        .catch(error => console.error('Error:', error));
      });
    }

    if (wishlistIcon) {
      wishlistIcon.style.cursor = 'pointer';
      wishlistIcon.addEventListener('click', function() {
        if (!showLoginPrompt()) {
          return;
        }
        
        // Logged in - get current course ID if on details page
        const courseId = window.eduSkillCourseDetailsContext ? window.eduSkillCourseDetailsContext.courseId : 0;
        if (courseId <= 0) {
          alert('Please navigate to a course page first.');
          return;
        }
        
        // Toggle wishlist
        const data = new FormData();
        data.append('action', 'wishlist_toggle');
        data.append('course_id', courseId);
        data.append('csrf_token', csrfToken);
        
        fetch(learnerApiUrl, {
          method: 'POST',
          body: data
        })
        .then(response => response.json())
        .then(result => {
          if (result.ok) {
            if (result.data.state === 'added') {
              wishlistIcon.querySelector('svg path').style.fill = 'currentColor';
              alert('Course saved to wishlist!');
            } else {
              wishlistIcon.querySelector('svg path').style.fill = 'none';
              alert('Course removed from wishlist.');
            }
          } else {
            alert('Error: ' + (result.message || 'Unable to update wishlist'));
          }
        })
        .catch(error => console.error('Error:', error));
      });
    }
  })();
</script>

<style>
  .navbar-login-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
  }

  .navbar-login-modal .modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
  }

  .navbar-login-modal .modal-content {
    position: relative;
    z-index: 2001;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    max-width: 400px;
    width: 90%;
  }

  .navbar-login-modal .modal-header {
    padding: 20px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .navbar-login-modal .modal-header h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1d2939;
  }

  .navbar-login-modal .btn-close {
    background: transparent;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6a6f73;
    padding: 0;
    width: auto;
    height: auto;
  }

  .navbar-login-modal .modal-body {
    padding: 20px;
  }

  .navbar-login-modal .modal-body p {
    margin: 0;
    color: #6a6f73;
    line-height: 1.5;
  }

  .navbar-login-modal .modal-footer {
    padding: 16px 20px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
  }

  .navbar-login-modal .btn {
    padding: 8px 16px;
    border: 1px solid #d0d5dd;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
  }

  .navbar-login-modal .btn-secondary {
    background: white;
    color: #344054;
  }

  .navbar-login-modal .btn-primary {
    background: #0d6e84;
    color: white;
    border-color: #0d6e84;
  }

  .navbar-login-modal .btn:hover {
    background-color: #f8f9fa;
  }

  .navbar-login-modal .btn-primary:hover {
    background-color: #0a5568;
  }

  /* Cart and Wishlist Badge Styles */
  .navbar-actions .cart-icon,
  .navbar-actions .wishlist-icon {
    position: relative;
    display: inline-block;
  }

  .navbar-actions .cart-badge,
  .navbar-actions .wishlist-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #e74c3c;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    border: 2px solid white;
  }

  .eduskill-navbar .navbar-user-menu {
    position: relative;
    display: inline-flex;
    align-items: center;
  }

  .eduskill-navbar .navbar-user-trigger {
    border: 1px solid #dbe4ec;
    border-radius: 999px;
    background: linear-gradient(180deg, #f9fcff 0%, #f2f7fb 100%);
    color: #1f2937;
    min-height: 42px;
    padding: 5px 12px 5px 6px;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(31, 41, 55, 0.06);
    transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
  }

  .eduskill-navbar .navbar-user-trigger:hover {
    border-color: #b7c9d8;
    box-shadow: 0 8px 18px rgba(31, 41, 55, 0.11);
    transform: translateY(-1px);
  }

  .eduskill-navbar .navbar-user-avatar,
  .eduskill-navbar .navbar-user-avatar-img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    flex: 0 0 30px;
  }

  .eduskill-navbar .navbar-user-avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #d4edf7;
    color: #0f4e67;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.04em;
  }

  .eduskill-navbar .navbar-user-avatar-img {
    object-fit: cover;
    border: 1px solid #d7e4ee;
  }

  .eduskill-navbar .navbar-user-name {
    max-width: 165px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 0.95rem;
    line-height: 1;
  }

  .eduskill-navbar .navbar-user-dropdown {
    position: absolute;
    right: 0;
    top: calc(100% + 9px);
    min-width: 230px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 16px 34px rgba(15, 23, 42, 0.16);
    z-index: 1100;
    padding: 8px;
  }

  .eduskill-navbar .navbar-user-dropdown a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 9px;
    color: #1f2937;
    font-size: 0.93rem;
    font-weight: 600;
    text-decoration: none;
  }

  .eduskill-navbar .navbar-user-dropdown a:hover {
    background: #eef6fa;
    color: #0f4e67;
  }

  .eduskill-navbar .navbar-user-dropdown a i {
    font-size: 15px;
    color: #5b6b7d;
    width: 16px;
    text-align: center;
  }

  @media (max-width: 991px) {
    .eduskill-navbar.mobile-open .navbar-actions .navbar-user-menu {
      grid-column: 1 / -1;
      position: static;
      width: 100%;
      display: block;
    }

    .eduskill-navbar.mobile-open .navbar-actions .navbar-user-trigger {
      width: 100%;
      justify-content: space-between;
      border-radius: 10px;
      min-height: 44px;
      padding: 7px 12px;
    }

    .eduskill-navbar.mobile-open .navbar-actions .navbar-user-name {
      max-width: none;
      flex: 1;
      text-align: left;
    }

    .eduskill-navbar.mobile-open .navbar-actions .navbar-user-dropdown {
      position: static;
      margin-top: 8px;
      min-width: 100%;
      box-shadow: none;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
    }
  }
</style>
