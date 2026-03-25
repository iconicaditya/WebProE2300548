<section id="step-2" class="addcourse-step d-none" data-step="2" aria-labelledby="step-2-title">
	<header class="mb-4">
		<h5 id="step-2-title" class="mb-1">Course media</h5>
		<p class="text-muted small mb-0">Upload your promo content, gallery assets, and trailer files.</p>
	</header>

	<div class="row g-3">
		<div class="col-12">
			<label class="form-label" for="promoVideoUrl">Intro / Promo video (optional)</label>
			<input type="url" id="promoVideoUrl" name="promo_video_url" class="form-control" placeholder="YouTube / Vimeo URL">
			<div class="form-text">Paste a video URL or upload media in the resources step.</div>
			<div id="promoPreview" class="mt-2"></div>
		</div>

		<div class="col-12">
			<label class="form-label" for="galleryUpload">Upload images (carousel / gallery)</label>
			<input type="file" id="galleryUpload" name="gallery[]" accept="image/*" multiple class="form-control">
			<div class="form-text">Used on course landing pages and previews.</div>
		</div>

		<div class="col-12">
			<label class="form-label" for="trailerUpload">Course trailer file (optional)</label>
			<input type="file" id="trailerUpload" name="trailer" accept="video/*" class="form-control">
			<div class="form-text">MP4 preferred. Keep file size small for faster preview.</div>
		</div>
	</div>
</section>
