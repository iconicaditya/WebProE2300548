

<section id="step-5" class="addcourse-step d-none" data-step="5" aria-labelledby="step-5-title">
	<h5 id="step-5-title">Course Resources</h5>
	<p class="text-muted small">Upload supporting materials for your course. Accepted file types: PDF, Word, PPT. Please provide a clear title and description for each resource.</p>

	<ul class="list-group mb-3">
		<li class="list-group-item">
			<strong>Tips:</strong>
			<ul class="mb-0">
				<li>Use descriptive titles for easy identification.</li>
				<li>Subtitles can help clarify the resource purpose.</li>
				<li>Maximum file size: 10MB per resource.</li>
			</ul>
		</li>
	</ul>

	<button type="button" class="btn btn-primary mb-3" id="addResourceBtn">Add Resource</button>

	<form id="resourcesForm" enctype="multipart/form-data" autocomplete="off">
		<div id="resourceFields"></div>
		<div id="resourceError" class="alert alert-danger d-none"></div>
		<!-- Save Resources button removed as requested -->
	</form>

	<script>
	function createResourceField(index) {
		return `
			<div class="card p-3 mb-3 resource-block">
				<div class="mb-2">
					<label for="resource-title-${index}">Resource Title <span class="text-danger">*</span></label>
					<input type="text" id="resource-title-${index}" name="resources[${index}][title]" class="form-control" required maxlength="100" placeholder="e.g. Course Syllabus">
				</div>
				<div class="mb-2">
					<label for="resource-subtitle-${index}">Resource Subtitle</label>
					<input type="text" id="resource-subtitle-${index}" name="resources[${index}][subtitle]" class="form-control" maxlength="150" placeholder="e.g. Overview of course topics">
				</div>
				<div class="mb-2">
					<label for="resource-file-${index}">Upload File <span class="text-danger">*</span></label>
					<input type="file" id="resource-file-${index}" name="resources[${index}][file]" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx" required>
					<small class="text-muted">PDF, Word (.doc/.docx), PPT (.ppt/.pptx) | Max 10MB</small>
				</div>
				<button type="button" class="btn btn-danger btn-sm remove-resource">Remove</button>
			</div>
		`;
	}

	document.getElementById('addResourceBtn').addEventListener('click', function() {
		const container = document.getElementById('resourceFields');
		const index = container.children.length;
		container.insertAdjacentHTML('beforeend', createResourceField(index));
	});

	document.getElementById('resourceFields').addEventListener('click', function(e) {
		if(e.target.classList.contains('remove-resource')) {
			e.target.closest('.resource-block').remove();
		}
	});

	document.getElementById('resourcesForm').addEventListener('submit', function(e) {
		e.preventDefault();
		const errorDiv = document.getElementById('resourceError');
		errorDiv.classList.add('d-none');
		errorDiv.textContent = '';
		let valid = true;
		let errorMsg = '';
		const fields = document.querySelectorAll('.resource-block');
		if(fields.length === 0) {
			valid = false;
			errorMsg = 'Please add at least one resource.';
		}
		fields.forEach(function(field, idx) {
			const title = field.querySelector('input[name^="resources"][name$="[title]"]');
			const file = field.querySelector('input[type="file"]');
			if(!title.value.trim()) {
				valid = false;
				errorMsg = 'Resource title is required.';
			}
			if(!file.files.length) {
				valid = false;
				errorMsg = 'Please upload a file for each resource.';
			} else {
				const f = file.files[0];
				if(f.size > 10 * 1024 * 1024) {
					valid = false;
					errorMsg = 'File size must be less than 10MB.';
				}
				const allowed = ['application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation'];
				if(!allowed.includes(f.type) && !f.name.match(/\.(pdf|doc|docx|ppt|pptx)$/i)) {
					valid = false;
					errorMsg = 'Invalid file type. Only PDF, Word, PPT allowed.';
				}
			}
		});
		if(!valid) {
			errorDiv.textContent = errorMsg;
			errorDiv.classList.remove('d-none');
			return;
		}
		// TODO: AJAX submission or form handling here
		alert('Resources saved successfully!');
	});
	</script>
</section>

