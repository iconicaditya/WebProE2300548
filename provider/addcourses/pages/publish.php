<section id="step-6" class="addcourse-step d-none" data-step="6" aria-labelledby="step-6-title">
	<h5 id="step-6-title">Preview & Publish</h5>
	<p class="text-muted small">Review your course and publish when ready.</p>

	<div class="mb-3">
		<h6>Visibility</h6>
		<div class="form-check form-switch">
			<input class="form-check-input" type="checkbox" id="visibilitySwitch" checked>
			<label class="form-check-label" for="visibilitySwitch">Public (visible to learners)</label>
		</div>
	</div>

	<div class="mb-3">
		<h6>Course summary</h6>
		<div id="courseSummary" class="p-3 border rounded bg-light">
			<em>Course summary will be generated here from the entered data for preview.</em>
		</div>
	</div>

	<!-- Navigation handled by global form buttons (Previous / Publish) -->
	<div class="mb-3 text-end">
		<small class="text-muted">Use the buttons below to publish your course.</small>
	</div>

	<hr>
</section>

<style>
/* Small interactive affordances */
.form-check-label { cursor: pointer; }
#courseSummary { cursor: pointer; }
</style>

<script>
(function(){
	const section = document.querySelector('section.addcourse-step[data-step="6"]');
	if(!section) return;
	const visibility = section.querySelector('#visibilitySwitch');
	const label = section.querySelector('label[for="visibilitySwitch"]');
	const summary = section.querySelector('#courseSummary');

	function updateLabel(){
		if(!label || !visibility) return;
		label.textContent = visibility.checked ? 'Public (visible to learners)' : 'Private (hidden from learners)';
	}

	if(visibility){
		visibility.addEventListener('change', updateLabel);
		updateLabel();
	}

	// Make summary clickable: copy text to clipboard (frontend only)
	if(summary){
		summary.addEventListener('click', function(){
			const text = summary.innerText || summary.textContent || '';
			if(!navigator.clipboard) return;
			navigator.clipboard.writeText(text.trim()).then(function(){
				const note = document.createElement('div'); note.className='small text-muted mt-2'; note.textContent='Summary copied (frontend demo)';
				summary.appendChild(note);
				setTimeout(()=> note.remove(), 1500);
			});
		});
	}
})();
</script>

