<section id="step-3" class="addcourse-step d-none" data-step="3" aria-labelledby="step-3-title">
	<header class="mb-4">
		<h5 id="step-3-title" class="mb-1">Modules & lessons</h5>
		<p class="text-muted small mb-0">Build your course structure with video, PDF, and quiz lessons.</p>
	</header>

	<div class="card border-0 shadow-sm modules-shell mb-4">
		<div class="card-body">
			<div class="row g-3 align-items-end modules-toolbar">
				<div class="col-md-4">
					<label for="sectionSelector" class="form-label">Section</label>
					<select id="sectionSelector" class="form-select">
						<option value="1">Section-1</option>
						<option value="2">Section-2</option>
						<option value="3">Section-3</option>
						<option value="4">Section-4</option>
						<option value="5">Section-5</option>
						<option value="6">Section-6</option>
						<option value="7">Section-7</option>
						<option value="8">Section-8</option>
						<option value="9">Section-9</option>
						<option value="10">Section-10</option>
						<option value="11">Section-11</option>
						<option value="12">Section-12</option>
						<option value="13">Section-13</option>
						<option value="14">Section-14</option>
						<option value="15">Section-15</option>
						<option value="16">Section-16</option>
						<option value="17">Section-17</option>
						<option value="18">Section-18</option>
						<option value="19">Section-19</option>
						<option value="20">Section-20</option>
						<option value="21">Section-21</option>
						<option value="22">Section-22</option>
						<option value="23">Section-23</option>
						<option value="24">Section-24</option>
						<option value="25">Section-25</option>
						<option value="26">Section-26</option>
						<option value="27">Section-27</option>
						<option value="28">Section-28</option>
						<option value="29">Section-29</option>
						<option value="30">Section-30</option>
					</select>
				</div>
				<div class="col-md-8">
					<label for="sectionTitleInput" class="form-label">Section title</label>
					<input type="text" id="sectionTitleInput" class="form-control" placeholder="Enter section title name">
				</div>
			</div>

			<div id="modules-wrapper" class="mt-4">
				<div id="emptyState" class="text-center py-5 border rounded-3 bg-body-tertiary">
					<div class="h6 mb-3">You have not added any lessons or modules</div>
					<button type="button" id="globalAddBtn" class="btn btn-primary px-4">+ Add</button>
				</div>

				<div id="chooserArea" class="d-none text-center my-3">
					<div class="btn-group modules-chooser" role="group" aria-label="Choose lesson type">
						<button type="button" class="btn btn-primary choose-type" data-type="video">Video</button>
						<button type="button" class="btn btn-info text-white choose-type" data-type="pdf">PDF</button>
						<button type="button" class="btn btn-success choose-type" data-type="quiz">Quiz</button>
					</div>
					<button type="button" id="chooserCancel" class="btn btn-outline-secondary ms-2">Cancel</button>
				</div>

				<ul id="lessonsList" class="list-group list-group-flush"></ul>

				<div id="addBtnBottomWrapper" class="text-center mt-4 d-none">
					<button type="button" id="globalAddBtnBottom" class="btn btn-primary px-4">+ Add</button>
				</div>
			</div>
		</div>
	</div>
</section>

