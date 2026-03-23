<?php
// Provider Add Course - Frontend only
$pageTitle = 'Create Course';
$assetVersion = isset($assetVersion) ? $assetVersion : 'addcourses.' . time();
$pageStylesheet = 'provider-addcourses.css';
$extraStylesheets = ['provider-addcourses.css'];
$extraScripts = ['provider-addcourses.js'];

require_once(__DIR__ . '/../../includes/header.php');
// Include provider dashboard navbar (keeps provider navigation consistent)
require_once(__DIR__ . '/../../provider/includes/topbar.php');
// Addcourses sub-header
require_once(__DIR__ . '/includes/topbar.php');
?>

<div class="container my-4">
	<div class="card shadow-sm">
		<div class="card-body p-0">
			<div class="row g-0">
				<div class="col-md-3 border-end p-3 bg-light addcourses-sidebar-fixed">
					<h5 class="mb-3">Course Builder</h5>
					<nav class="nav flex-column" id="addcourse-steps-nav" aria-label="Course build steps">
						<a href="#step-1" class="nav-link step-link active" data-step="1">1. Basic details</a>
						<a href="#step-3" class="nav-link step-link" data-step="3">2. Modules & lessons</a>
						<a href="#step-4" class="nav-link step-link" data-step="4">3. Price & offers</a>
						<a href="#step-5" class="nav-link step-link" data-step="5">4. Resources</a>
						<a href="#step-6" class="nav-link step-link" data-step="6">5. Preview & Publish</a>
					</nav>
				</div>

				<div class="col-md-9 p-4">
					<form id="addCourseForm" class="needs-validation" novalidate>
						<div id="steps-container">
							<?php require_once(__DIR__ . '/pages/basicdetails.php'); ?>
							<?php require_once(__DIR__ . '/pages/coursemedia.php'); ?>
							<?php require_once(__DIR__ . '/pages/modules.php'); ?>
							<?php require_once(__DIR__ . '/pages/price.php'); ?>
							<?php require_once(__DIR__ . '/pages/resources.php'); ?>
							<?php require_once(__DIR__ . '/pages/publish.php'); ?>
						</div>

						<div class="d-flex justify-content-between align-items-center mt-3">
							<div>
								<button type="button" class="btn btn-outline-secondary" id="prevBtn" disabled>Previous</button>
							</div>
							<div>
								<button type="button" class="btn btn-primary" id="nextBtn">Next</button>
								<button type="submit" class="btn btn-success" id="submitBtn" style="display:none;">Publish</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php require_once(__DIR__ . '/../../provider/includes/footer.php'); ?>
