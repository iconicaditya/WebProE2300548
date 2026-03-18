<section id="step-4" class="addcourse-step d-none" data-step="4" aria-labelledby="step-4-title">
	<h5 id="step-4-title">Price & offers</h5>
	<p class="text-muted small">Set the course price and any promotional offers.</p>

	<div class="mb-3">
		<label class="form-label">Course access</label>
		<div class="btn-group" role="group" aria-label="Course access">
			<input type="radio" class="btn-check" name="access" id="accessFree" autocomplete="off" value="free" checked>
			<label class="btn btn-outline-primary" for="accessFree">Free</label>

			<input type="radio" class="btn-check" name="access" id="accessPaid" autocomplete="off" value="paid">
			<label class="btn btn-outline-primary" for="accessPaid">Paid</label>
		</div>
	</div>

	<div id="priceFields" style="display:none;">
		<div class="row">
			<div class="col-md-4 mb-3">
				<label class="form-label">Price</label>
				<input type="number" name="price" class="form-control" min="0" step="0.01" placeholder="0.00">
			</div>
			<div class="col-md-4 mb-3">
				<label class="form-label">Currency</label>
				<select name="currency" class="form-select">
					<option value="USD">USD</option>
					<option value="NPR">NPR</option>
					<option value="EUR">EUR</option>
				</select>
			</div>
		</div>

		<div class="mb-3">
			<label class="form-label">Promotional code (optional)</label>
			<div class="input-group">
				<input type="text" name="coupon" class="form-control" placeholder="Enter coupon code">
				<button type="button" class="btn btn-outline-secondary">Apply</button>
			</div>
		</div>
	</div>

	<hr>

	<style>
		/* Price toggle UI */
		#priceFields { transition: all 0.15s ease; }
	</style>

	<script>
	(function(){
		const section = document.querySelector('section.addcourse-step[data-step="4"]');
		if(!section) return;
		const accessFree = section.querySelector('#accessFree');
		const accessPaid = section.querySelector('#accessPaid');
		const priceFields = section.querySelector('#priceFields');
		const applyBtn = section.querySelector('.input-group .btn');

		function update(){
			if(accessPaid && accessPaid.checked){ priceFields.style.display = ''; }
			else { priceFields.style.display = 'none'; }
		}

		if(accessFree) accessFree.addEventListener('change', update);
		if(accessPaid) accessPaid.addEventListener('change', update);
		update();

		if(applyBtn){
			applyBtn.addEventListener('click', function(){
				const parent = applyBtn.closest('.input-group');
				let note = parent.querySelector('.coupon-note');
				if(!note){ note = document.createElement('div'); note.className='coupon-note small text-success mt-2'; parent.appendChild(note); }
				note.textContent = 'Coupon applied (frontend demo)';
				setTimeout(()=>{ note.textContent = ''; }, 2200);
			});
		}
	})();
	</script>
</section>

