<section id="step-4" class="addcourse-step d-none" data-step="4" aria-labelledby="step-4-title">
	<header class="mb-4">
		<h5 id="step-4-title" class="mb-1">Price & offers</h5>
		<p class="text-muted small mb-0">Set the course price and add optional promotional settings.</p>
	</header>

	<div class="mb-3">
		<label class="form-label d-block">Course access</label>
		<div class="btn-group" role="group" aria-label="Course access">
			<input type="radio" class="btn-check" name="access" id="accessFree" autocomplete="off" value="free" checked>
			<label class="btn btn-outline-primary" for="accessFree">Free</label>

			<input type="radio" class="btn-check" name="access" id="accessPaid" autocomplete="off" value="paid">
			<label class="btn btn-outline-primary" for="accessPaid">Paid</label>
		</div>
	</div>

	<div id="priceFields" class="d-none">
		<div class="row g-3">
			<div class="col-md-4">
				<label class="form-label" for="coursePrice">Price</label>
				<input type="number" name="price" id="coursePrice" class="form-control" min="0" step="0.01" placeholder="0.00">
			</div>
			<div class="col-md-4">
				<label class="form-label" for="courseCurrency">Currency</label>
				<select name="currency" id="courseCurrency" class="form-select">
					<option value="USD">USD</option>
					<option value="NPR">NPR</option>
					<option value="EUR">EUR</option>
				</select>
			</div>
		</div>

		<div class="mt-3">
			<label class="form-label" for="courseCoupon">Promotional code (optional)</label>
			<div class="input-group">
				<input type="text" id="courseCoupon" name="coupon" class="form-control" placeholder="Enter coupon code">
				<button type="button" class="btn btn-outline-secondary" id="applyCouponBtn">Apply</button>
			</div>
			<div class="coupon-note small text-success mt-2 d-none"></div>
		</div>
	</div>
</section>
