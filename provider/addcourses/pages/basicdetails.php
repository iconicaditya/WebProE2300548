<section id="step-1" class="addcourse-step" data-step="1" aria-labelledby="step-1-title">
	<h5 id="step-1-title">Basic details</h5>
	<p class="text-muted small">Enter the core information for your course.</p>

	<div class="mb-3">
		<label class="form-label">Course name</label>
		<input type="text" name="title" class="form-control" placeholder="e.g. React.js & Modern Frontend Development" required>
	</div>

	<div class="mb-3">
		<label class="form-label">Short description</label>
		<textarea name="short_description" class="form-control" rows="2" placeholder="One-line summary for listing" required></textarea>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="form-label">Course thumbnail</label>
			<input type="file" name="thumbnail" accept="image/*" class="form-control" id="thumbnailInput">
			<small class="text-muted">Recommended: 1280×720</small>
			<div class="mt-2" id="thumbnailPreview" style="display:none;"><img src="" alt="thumbnail preview" class="img-fluid rounded" style="max-height:160px;"></div>
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Duration</label>
			<input type="text" name="duration" class="form-control" placeholder="e.g. 8 weeks">

			<label class="form-label mt-3">Lessons (count)</label>
			<input type="number" name="lessons" class="form-control" min="0" placeholder="e.g. 45">

			<label class="form-label mt-3">Students (approx.)</label>
			<input type="number" name="students" class="form-control" min="0" placeholder="e.g. 15420">
		</div>
	</div>

	<div class="row">
		<div class="col-md-4 mb-3">
			<label class="form-label">Level</label>
			<select name="level" class="form-select">
				<option>All levels</option>
				<option>Beginner</option>
				<option>Intermediate</option>
				<option>Advanced</option>
			</select>
		</div>
		<div class="col-md-4 mb-3">
			<label class="form-label">Language</label>
			<input type="text" name="language" class="form-control" value="English">
		</div>
		<div class="col-md-4 mb-3">
			<label class="form-label">Certification</label>
			<div>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="certification" id="certYes" value="yes" checked>
					<label class="form-check-label" for="certYes">Yes</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="certification" id="certNo" value="no">
					<label class="form-check-label" for="certNo">No</label>
				</div>
			</div>
		</div>
	</div>

	<div class="mb-3">
		<label class="form-label">What's included</label>
		<div class="row">
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

	<div class="mb-3">
		<label class="form-label">Course description</label>
		<textarea name="description" class="form-control" rows="6" placeholder="Full course description for the course landing page"></textarea>
	</div>

	<div class="mb-3">
		<label class="form-label">Requirements</label>
		<div id="requirementsList">
			<div class="input-group mb-2 requirement-item">
				<input type="text" name="requirements[]" class="form-control" placeholder="e.g. Basic knowledge of JavaScript (ES6+)">
				<button type="button" class="btn btn-outline-danger btn-remove-requirement">&times;</button>
			</div>
		</div>
		<button type="button" id="addRequirementBtn" class="btn btn-sm btn-outline-primary">Add requirement</button>
	</div>

	<hr>

	<style>
		/* Basic styles inside step */
		#thumbnailPreview img { cursor: default; max-width:100%; }
		#addcourse-steps-nav .nav-link.active { box-shadow: inset 0 0 0 9999px rgba(10,88,202,0.02); }
	</style>

	<script>
	(function(){
		const section = document.querySelector('section.addcourse-step[data-step="1"]');
		if(!section) return;

		// Thumbnail preview
		const thumbInput = section.querySelector('#thumbnailInput');
		const preview = section.querySelector('#thumbnailPreview');
		const previewImg = preview && preview.querySelector('img');
		if(thumbInput){
			thumbInput.addEventListener('change', function(e){
				const f = e.target.files && e.target.files[0];
				if(f && previewImg){
					previewImg.src = URL.createObjectURL(f);
					preview.style.display = 'block';
				} else if(preview){ preview.style.display = 'none'; }
			});
		}

		// Simple client-side sanitised preview for students count
		const students = section.querySelector('input[name="students"]');
		if(students){
			students.addEventListener('input', function(){
				if(this.value < 0) this.value = 0;
			});
		}

		// Requirements dynamic add/remove
		const reqList = section.querySelector('#requirementsList');
		const addReqBtn = section.querySelector('#addRequirementBtn');
		if(addReqBtn && reqList){
			addReqBtn.addEventListener('click', function(){
				const div = document.createElement('div');
				div.className = 'input-group mb-2 requirement-item';
				div.innerHTML = '<input type="text" name="requirements[]" class="form-control" placeholder="Requirement"><button type="button" class="btn btn-outline-danger btn-remove-requirement">&times;</button>';
				reqList.appendChild(div);
			});

			reqList.addEventListener('click', function(e){
				if(e.target.closest('.btn-remove-requirement')){
					const item = e.target.closest('.requirement-item'); if(item) item.remove();
				}
			});
		}
	})();
	</script>
</section>

