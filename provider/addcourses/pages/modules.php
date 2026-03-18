<section id="step-3" class="addcourse-step d-none" data-step="3" aria-labelledby="step-3-title">
	<h5 id="step-3-title">Modules & lessons</h5>
	<p class="text-muted small">Organise your content into modules and lessons.</p>

	<div id="modulesContainer">
		<div class="module-item card mb-3">
			<div class="card-body">
				<div class="d-flex align-items-start justify-content-between mb-2">
					<div>
						<label class="form-label">Module title</label>
						<input type="text" name="modules[0][title]" class="form-control" placeholder="Module 1: Getting started" required>
					</div>
					<div class="ms-3">
						<button type="button" class="btn btn-sm btn-outline-danger btn-remove-module">Remove</button>
					</div>
				</div>

				<div class="lessons-list mb-2">
					<div class="lesson-item input-group mb-2">
						<input type="text" name="modules[0][lessons][0][title]" class="form-control" placeholder="Lesson 1: Introduction" required>
						<input type="text" name="modules[0][lessons][0][duration]" class="form-control ms-2" placeholder="10:00">
						<select name="modules[0][lessons][0][type]" class="form-select ms-2" style="max-width:140px;">
							<option value="video">Video</option>
							<option value="quiz">Quiz</option>
							<option value="resource">Resource</option>
						</select>
						<button type="button" class="btn btn-outline-danger btn-remove-lesson ms-2">&times;</button>
					</div>
				</div>

				<div>
					<button type="button" class="btn btn-sm btn-outline-primary btn-add-lesson">Add lesson</button>
				</div>
			</div>
		</div>
	</div>

	<button type="button" class="btn btn-sm btn-primary" id="addModuleBtn">Add module</button>

	<hr>

	<style>
		.module-item { cursor: default; }
		.btn-remove-module, .btn-remove-lesson { cursor: pointer; }
	</style>

	<script>
	(function(){
		const section = document.querySelector('section.addcourse-step[data-step="3"]');
		if(!section) return;
		const modulesContainer = section.querySelector('#modulesContainer');
		const addModuleBtn = section.querySelector('#addModuleBtn');

		function createModule(index){
			const div = document.createElement('div');
			div.className = 'module-item card mb-3';
			div.innerHTML = `
				<div class="card-body">
					<div class="d-flex align-items-start justify-content-between mb-2">
						<div>
							<label class="form-label">Module title</label>
							<input type="text" name="modules[${index}][title]" class="form-control" placeholder="Module ${index+1}: Title" required>
						</div>
						<div class="ms-3">
							<button type="button" class="btn btn-sm btn-outline-danger btn-remove-module">Remove</button>
						</div>
					</div>

					<div class="lessons-list mb-2"></div>

					<div>
						<button type="button" class="btn btn-sm btn-outline-primary btn-add-lesson">Add lesson</button>
					</div>
				</div>`;
			return div;
		}

		function addModule(){
			const idx = modulesContainer.querySelectorAll('.module-item').length;
			const m = createModule(idx);
			modulesContainer.appendChild(m);
		}

		if(addModuleBtn){ addModuleBtn.addEventListener('click', addModule); }

		// Delegate remove module, add/remove lessons
		modulesContainer.addEventListener('click', function(e){
			if(e.target.closest('.btn-remove-module')){
				const m = e.target.closest('.module-item'); if(m) m.remove();
				return;
			}
			if(e.target.closest('.btn-add-lesson')){
				const moduleCard = e.target.closest('.module-item');
				if(!moduleCard) return;
				const lessonsList = moduleCard.querySelector('.lessons-list');
				const lessonIndex = lessonsList.querySelectorAll('.lesson-item').length;
				const moduleIndex = Array.from(modulesContainer.querySelectorAll('.module-item')).indexOf(moduleCard);
				const div = document.createElement('div');
				div.className = 'lesson-item input-group mb-2';
				div.innerHTML = `<input type="text" name="modules[${moduleIndex}][lessons][${lessonIndex}][title]" class="form-control" placeholder="Lesson ${lessonIndex+1}: Title" required><input type="text" name="modules[${moduleIndex}][lessons][${lessonIndex}][duration]" class="form-control ms-2" placeholder="10:00"><select name="modules[${moduleIndex}][lessons][${lessonIndex}][type]" class="form-select ms-2" style="max-width:140px;"><option value="video">Video</option><option value="quiz">Quiz</option><option value="resource">Resource</option></select><button type="button" class="btn btn-outline-danger btn-remove-lesson ms-2">&times;</button>`;
				lessonsList.appendChild(div);
				return;
			}
			if(e.target.closest('.btn-remove-lesson')){
				const item = e.target.closest('.lesson-item'); if(item) item.remove();
				return;
			}
		});
	})();
	</script>
</section>

