<section id="step-2" class="addcourse-step d-none" data-step="2" aria-labelledby="step-2-title">
	<h5 id="step-2-title">Course media</h5>
	<p class="text-muted small">Upload your course trailer, sample videos and supporting images.</p>

	<div class="mb-3">
		<label class="form-label">Intro / Promo video (optional)</label>
		<input type="url" name="promo_video_url" class="form-control" placeholder="YouTube / Vimeo URL">
		<small class="text-muted">Paste a video URL or upload in resources step.</small>
	</div>

	<div class="mb-3">
		<label class="form-label">Upload images (carousel / gallery)</label>
		<input type="file" name="gallery[]" accept="image/*" multiple class="form-control">
		<small class="text-muted">Used on course landing page and previews.</small>
	</div>

	<div class="mb-3">
		<label class="form-label">Course trailer file (optional)</label>
		<input type="file" name="trailer" accept="video/*" class="form-control">
		<small class="text-muted">MP4 preferred. Keep file size small for preview.</small>
	</div>

	<hr>

	<style>
		/* Clickable preview styles */
		#promoPreview { margin-top:8px; }
		#promoPreview a { display:inline-block; color:#0d6efd; text-decoration:underline; }
	</style>

	<script>
	(function(){
		const section = document.querySelector('section.addcourse-step[data-step="2"]');
		if(!section) return;
		const promoInput = section.querySelector('input[name="promo_video_url"]');
		let previewEl = section.querySelector('#promoPreview');
		if(!previewEl){ previewEl = document.createElement('div'); previewEl.id = 'promoPreview'; promoInput && promoInput.parentNode.appendChild(previewEl); }

		function updatePreview(){
			const url = (promoInput && promoInput.value || '').trim();
			previewEl.innerHTML = '';
			if(!url) return;
			const a = document.createElement('a');
			a.href = url; a.target = '_blank'; a.rel = 'noopener';
			a.textContent = 'Open promo video';
			previewEl.appendChild(a);
		}

		if(promoInput){
			promoInput.addEventListener('change', updatePreview);
			promoInput.addEventListener('input', updatePreview);
			updatePreview();
		}
	})();
	</script>
</section>

