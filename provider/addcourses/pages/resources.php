<section id="step-5" class="addcourse-step d-none" data-step="5" aria-labelledby="step-5-title">
	<h5 id="step-5-title">Resources</h5>
	<p class="text-muted small">Upload PDFs, slides, and attachments learners can download.</p>

	<div class="mb-3">
		<label class="form-label">Upload resources</label>
		<input type="file" name="resources[]" class="form-control" multiple>
		<small class="text-muted">PDF, PPT, ZIP and other helpful materials.</small>
	</div>

	<div class="mb-3">
		<label class="form-label">External links</label>
		<div id="resourceLinks">
			<div class="input-group mb-2">
				<input type="url" name="resource_links[]" class="form-control" placeholder="https://example.com/article">
				<button type="button" class="btn btn-outline-danger btn-remove-link">&times;</button>
			</div>
		</div>
		<button type="button" class="btn btn-sm btn-outline-primary" id="addResourceLink">Add link</button>
	</div>

	<hr>

	<style>
		.btn-remove-link { cursor: pointer; }
	</style>

	<script>
	(function(){
		const section = document.querySelector('section.addcourse-step[data-step="5"]');
		if(!section) return;
		const linksContainer = section.querySelector('#resourceLinks');
		const addBtn = section.querySelector('#addResourceLink');

		if(addBtn && linksContainer){
			addBtn.addEventListener('click', function(){
				const div = document.createElement('div');
				div.className = 'input-group mb-2';
				div.innerHTML = '<input type="url" name="resource_links[]" class="form-control" placeholder="https://example.com/article"><button type="button" class="btn btn-outline-danger btn-remove-link">&times;</button>';
				linksContainer.appendChild(div);
			});

			linksContainer.addEventListener('click', function(e){
				if(e.target.closest('.btn-remove-link')){
					const item = e.target.closest('.input-group'); if(item) item.remove();
				}
			});
		}
	})();
	</script>
</section>

