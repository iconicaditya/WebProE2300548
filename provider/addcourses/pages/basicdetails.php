<section id="step-1" class="addcourse-step" data-step="1" aria-labelledby="step-1-title">
	<header class="mb-4">
		<h5 id="step-1-title" class="mb-1">Basic details</h5>
		<p class="text-muted small mb-0">Enter the core information for your course listing and learner experience.</p>
	</header>

	<div class="row g-3">
		<div class="col-12">
			<label class="form-label" for="courseTitle">Course name</label>
			<input type="text" name="title" id="courseTitle" class="form-control" placeholder="e.g. React.js & Modern Frontend Development" required>
		</div>

		<div class="col-12">
			<label class="form-label" for="courseShortDescription">Short description</label>
			<textarea name="short_description" id="courseShortDescription" class="form-control" rows="2" placeholder="One-line summary for listing" required></textarea>
		</div>

		<div class="col-md-6">
			<label class="form-label" for="thumbnailInput">Course thumbnail</label>
			<input type="file" name="thumbnail" accept="image/*" class="form-control" id="thumbnailInput">
			<div class="form-text">Recommended: 1280×720</div>
			<div class="mt-2 d-none" id="thumbnailPreview">
				<img src="" alt="Course thumbnail preview" class="img-fluid rounded border">
			</div>
		</div>

		<div class="col-md-6">
			<div class="mb-3">
				<label class="form-label" for="courseDuration">Duration</label>
				<input type="text" name="duration" id="courseDuration" class="form-control" placeholder="e.g. 8 weeks">
			</div>
			<div class="mb-3">
				<label class="form-label" for="courseLessons">Lessons (count)</label>
				<input type="number" name="lessons" id="courseLessons" class="form-control" min="0" placeholder="e.g. 45">
			</div>
			<div>
				<label class="form-label" for="courseStudents">Students (approx.)</label>
				<input type="number" name="students" id="courseStudents" class="form-control" min="0" placeholder="e.g. 15420">
			</div>
		</div>

		<div class="col-md-4">
			<label class="form-label" for="courseLevel">Level</label>
			<select name="level" id="courseLevel" class="form-select">
				<option>All levels</option>
				<option>Beginner</option>
				<option>Intermediate</option>
				<option>Advanced</option>
			</select>
		</div>

		<div class="col-md-4">
			<label class="form-label" for="courseLanguage">Language</label>
			<input type="text" name="language" id="courseLanguage" class="form-control" value="English">
		</div>

		<div class="col-md-4">
			<label class="form-label d-block">Certification</label>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="certification" id="certYes" value="yes" checked>
				<label class="form-check-label" for="certYes">Yes</label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="certification" id="certNo" value="no">
				<label class="form-check-label" for="certNo">No</label>
			</div>
		</div>

		<div class="col-12">
			<label class="form-label d-block mb-2">What's included</label>
			<div class="row g-2">
				<div class="col-sm-6">
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="included[]" value="lifetime" id="incLifetime" checked>
						<label class="form-check-label" for="incLifetime">Lifetime access</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="included[]" value="videos" id="incVideos" checked>
						<label class="form-check-label" for="incVideos">Video lectures</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="included[]" value="resources" id="incResources" checked>
						<label class="form-check-label" for="incResources">Downloadable resources</label>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="included[]" value="certificate" id="incCertificate" checked>
						<label class="form-check-label" for="incCertificate">Certificate of completion</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="included[]" value="mobile" id="incMobile">
						<label class="form-check-label" for="incMobile">Mobile-friendly</label>
					</div>
				</div>
			</div>
		</div>

		<div class="col-12">
			<label class="form-label" for="courseDescription">Course description</label>
			<textarea name="description" id="courseDescription" class="form-control" rows="6" placeholder="Full course description for the course landing page"></textarea>
		</div>

		<div class="col-12">
			<label class="form-label">Learning outcomes</label>
			<div id="outcomesList" class="vstack gap-2">
				<div class="input-group outcome-item">
					<input type="text" name="outcomes[]" class="form-control" placeholder="e.g. Build complete frontend apps with React">
					<button type="button" class="btn btn-outline-danger btn-remove-outcome" aria-label="Remove outcome">&times;</button>
				</div>
			</div>
			<button type="button" id="addOutcomeBtn" class="btn btn-sm btn-outline-primary mt-2">Add outcome</button>
		</div>

		<div class="col-12">
			<label class="form-label">Requirements</label>
			<div id="requirementsList" class="vstack gap-2">
				<div class="input-group requirement-item">
					<input type="text" name="requirements[]" class="form-control" placeholder="e.g. Basic knowledge of JavaScript (ES6+)">
					<button type="button" class="btn btn-outline-danger btn-remove-requirement" aria-label="Remove requirement">&times;</button>
				</div>
			</div>
			<button type="button" id="addRequirementBtn" class="btn btn-sm btn-outline-primary mt-2">Add requirement</button>
		</div>
	</div>
</section>
