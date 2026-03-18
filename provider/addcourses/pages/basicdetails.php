<section id="step-1" class="addcourse-step" data-step="1" aria-labelledby="step-1-title">
	<h5 id="step-1-title">Basic details</h5>
	<p class="text-muted small">Start with the essentials: title, description and a strong thumbnail.</p>

	<div class="mb-3">
		<label class="form-label">Course title</label>
		<input type="text" name="title" class="form-control" placeholder="e.g. Mastering React: From Zero to Production" required>
		<div class="invalid-feedback">Please enter a course title.</div>
	</div>

	<div class="mb-3">
		<label class="form-label">Short description</label>
		<textarea name="short_description" class="form-control" rows="3" placeholder="One-line summary for listing" required></textarea>
		<div class="invalid-feedback">Please provide a short description.</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="form-label">Category</label>
			<select name="category" class="form-select" required>
				<option value="">Select category</option>
				<option>Development</option>
				<option>Design</option>
				<option>Business</option>
				<option>Marketing</option>
				<option>Photography</option>
			</select>
			<div class="invalid-feedback">Choose a category.</div>
		</div>
		<div class="col-md-3 mb-3">
			<label class="form-label">Level</label>
			<select name="level" class="form-select">
				<option>All levels</option>
				<option>Beginner</option>
				<option>Intermediate</option>
				<option>Advanced</option>
			</select>
		</div>
		<div class="col-md-3 mb-3">
			<label class="form-label">Language</label>
			<input type="text" name="language" class="form-control" value="English">
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="form-label">Thumbnail</label>
			<input type="file" name="thumbnail" accept="image/*" class="form-control" id="thumbnailInput">
			<small class="text-muted">Recommended size: 1280 x 720</small>
			<div class="mt-2" id="thumbnailPreview" style="display:none;">
				<img src="" alt="thumbnail preview" class="img-fluid rounded" style="max-height:160px;">
			</div>
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Tags</label>
			<input type="text" name="tags" class="form-control" placeholder="Add comma separated tags (e.g. react,frontend)" aria-label="Course tags">
			<small class="text-muted">Tags help learners find your course.</small>
		</div>
	</div>

	<div class="mb-3">
		<label class="form-label">What will learners gain? (Learning outcomes)</label>
		<div id="outcomesList">
			<div class="input-group mb-2 outcome-item">
				<input type="text" class="form-control" name="outcomes[]" placeholder="E.g. Build production-ready React apps" required>
				<button type="button" class="btn btn-outline-danger btn-remove-outcome" title="Remove">&times;</button>
			</div>
		</div>
		<button type="button" class="btn btn-sm btn-outline-primary" id="addOutcomeBtn">Add another outcome</button>
	</div>

	<hr>

	<style>
		/* Small styles to indicate clickability */
		.btn-remove-outcome { cursor: pointer; }
		#thumbnailPreview img { cursor: default; }
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
				} else if(preview){
					preview.style.display = 'none';
				}
			});
		}

		// Outcomes add/remove
		const outcomesList = section.querySelector('#outcomesList');
		const addBtn = section.querySelector('#addOutcomeBtn');
		if(addBtn && outcomesList){
			addBtn.addEventListener('click', function(){
				const div = document.createElement('div');
				div.className = 'input-group mb-2 outcome-item';
				div.innerHTML = '<input type="text" class="form-control" name="outcomes[]" placeholder="E.g. Build production-ready React apps" required><button type="button" class="btn btn-outline-danger btn-remove-outcome" title="Remove">&times;</button>';
				outcomesList.appendChild(div);
			});

			outcomesList.addEventListener('click', function(e){
				if(e.target.closest('.btn-remove-outcome')){
					const item = e.target.closest('.outcome-item');
					if(item) item.remove();
				}
			});
		}
	})();
	</script>
</section>

