<?php
require_once(__DIR__ . '/../../config/config.php');
require_once(__DIR__ . '/../../config/db.php');
require_once(__DIR__ . '/../../includes/auth.php');

ems_require_login(['provider']);

$portalUser = ems_load_portal_user($conn);
if (!$portalUser || ($portalUser['role'] ?? '') !== 'provider') {
	ems_logout_user();
	ems_set_flash('danger', 'Unable to load your provider profile. Please log in again.');
	ems_redirect('auth/login.php');
}

$providerDisplayName = ems_profile_text($portalUser['full_name'], 'Provider');
$providerInitials = ems_user_initials($providerDisplayName);

// Provider Add Course - frontend-only wizard UI
$pageTitle = 'Create Course';
$assetVersion = isset($assetVersion) ? $assetVersion : 'addcourses.' . time();
$pageStylesheet = 'provider-addcourses.css';
$extraScripts = ['provider-addcourses.js'];

require_once(__DIR__ . '/../../includes/header.php');
require_once(__DIR__ . '/../../provider/includes/topbar.php');
require_once(__DIR__ . '/includes/topbar.php');
?>

<div class="container-xxl my-4 addcourse-page">
	<div class="card border-0 shadow-sm addcourse-shell-card">
		<div class="row g-0 addcourse-shell">
			<aside class="col-lg-3 border-end bg-body-tertiary addcourses-sidebar-fixed addcourse-sidebar">
				<div class="p-3 p-lg-4">
					<h2 class="h6 mb-3 text-uppercase text-secondary fw-semibold">Course Builder</h2>
					<nav class="nav nav-pills flex-column gap-1" id="addcourse-steps-nav" aria-label="Course build steps">
						<a href="#step-1" class="nav-link step-link active" data-step="1">
							<span class="step-index">1</span>
							<span>Basic details</span>
						</a>
						<a href="#step-2" class="nav-link step-link" data-step="2">
							<span class="step-index">2</span>
							<span>Course media</span>
						</a>
						<a href="#step-3" class="nav-link step-link" data-step="3">
							<span class="step-index">3</span>
							<span>Modules & lessons</span>
						</a>
						<a href="#step-4" class="nav-link step-link" data-step="4">
							<span class="step-index">4</span>
							<span>Price & offers</span>
						</a>
						<a href="#step-5" class="nav-link step-link" data-step="5">
							<span class="step-index">5</span>
							<span>Resources</span>
						</a>
						<a href="#step-6" class="nav-link step-link" data-step="6">
							<span class="step-index">6</span>
							<span>Preview & publish</span>
						</a>
					</nav>
				</div>
			</aside>

			<div class="col-lg-9 addcourse-content">
				<div class="p-3 p-md-4 p-xl-5">
					<form id="addCourseForm" class="needs-validation" novalidate>
						<div id="steps-container">
							<?php require_once(__DIR__ . '/pages/basicdetails.php'); ?>
							<?php require_once(__DIR__ . '/pages/coursemedia.php'); ?>
							<?php require_once(__DIR__ . '/pages/modules.php'); ?>
							<?php require_once(__DIR__ . '/pages/price.php'); ?>
							<?php require_once(__DIR__ . '/pages/resources.php'); ?>
							<?php require_once(__DIR__ . '/pages/publish.php'); ?>
						</div>

						<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 border-top addcourse-form-nav">
							<button type="button" class="btn btn-outline-secondary" id="prevBtn" disabled>Previous</button>

							<div class="d-flex gap-2 ms-auto">
								<button type="button" class="btn btn-primary" id="nextBtn">Next</button>
								<button type="submit" class="btn btn-success d-none" id="submitBtn">Publish</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php require_once(__DIR__ . '/../../provider/includes/footer.php'); ?>
