(function () {
	const pageRoot = document.querySelector('.courses-catalog-page');
	if (!pageRoot) return;

	const baseUrl = window.eduSkillBaseUrl || '/';
	const IMAGES_BASE = baseUrl + 'assets/images/cources/';
	
	// Use courses from PHP or use empty array and fetch from API
	let courses = window.coursesData || [];

	const searchInput = document.getElementById('searchCourse');
	const levelFilter = document.getElementById('levelFilter');
	const categoryFilters = document.getElementById('categoryFilters');
	const instructorDropdown = document.getElementById('instructorDropdown');
	const instructorToggle = document.getElementById('instructorToggle');
	const instructorMenu = document.getElementById('instructorMenu');
	const instructorSearch = document.getElementById('instructorSearch');
	const instructorOptions = document.getElementById('instructorOptions');
	const instructorSelectedLabel = document.getElementById('instructorSelectedLabel');
	const priceRangeFilter = document.getElementById('priceRangeFilter');
	const resetFilters = document.getElementById('resetFilters');
	const coursesGrid = document.getElementById('coursesGrid');
	const courseCount = document.getElementById('courseCount');

	let currentCategory = 'all';
	let currentCourseId = null;
	let currentInstructor = 'all';

	function parseCoursePrice(priceText) {
		return parseInt(String(priceText).replace(/[^0-9]/g, ''), 10) || 0;
	}

	function getSelectedBudgetRange() {
		if (priceRangeFilter.value === 'all') {
			return { min: 0, max: Number.MAX_SAFE_INTEGER };
		}
		if (priceRangeFilter.value === '0-0') {
			return { min: 0, max: 0 };
		}
		const parts = priceRangeFilter.value.split('-');
		return {
			min: parseInt(parts[0], 10),
			max: parseInt(parts[1], 10)
		};
	}

	function getUniqueInstructors() {
		return Array.from(new Set(courses.map(function (course) {
			return course.instructor;
		}))).sort();
	}

	function closeInstructorDropdown() {
		instructorDropdown.classList.remove('open');
		instructorMenu.hidden = true;
		instructorToggle.setAttribute('aria-expanded', 'false');
	}

	function openInstructorDropdown() {
		instructorDropdown.classList.add('open');
		instructorMenu.hidden = false;
		instructorToggle.setAttribute('aria-expanded', 'true');
	}

	function setInstructor(value) {
		currentInstructor = value;
		instructorSelectedLabel.textContent = value === 'all' ? 'All Instructors' : value;
		rerender();
	}

	function renderInstructorOptions(query) {
		const keyword = (query || '').trim().toLowerCase();
		const names = getUniqueInstructors().filter(function (name) {
			return !keyword || name.toLowerCase().includes(keyword);
		});

		instructorOptions.innerHTML = '';

		if (!keyword || 'all instructors'.includes(keyword)) {
			const allBtn = document.createElement('button');
			allBtn.type = 'button';
			allBtn.className = 'instructor-option' + (currentInstructor === 'all' ? ' active' : '');
			allBtn.setAttribute('data-value', 'all');
			allBtn.textContent = 'All Instructors';
			instructorOptions.appendChild(allBtn);
		}

		if (!names.length) {
			const empty = document.createElement('div');
			empty.className = 'instructor-empty';
			empty.textContent = 'No instructor found.';
			instructorOptions.appendChild(empty);
			return;
		}

		names.forEach(function (name) {
			const btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'instructor-option' + (currentInstructor === name ? ' active' : '');
			btn.setAttribute('data-value', name);
			btn.textContent = name;
			instructorOptions.appendChild(btn);
		});
	}

	function renderCards(filteredCourses) {
		coursesGrid.innerHTML = '';
		courseCount.textContent = filteredCourses.length + (filteredCourses.length === 1 ? ' course' : ' courses');

		if (!filteredCourses.length) {
			coursesGrid.innerHTML = '<div class="empty-state">No courses match these filters. Try a different category or reset all filters.</div>';
			return;
		}

		filteredCourses.forEach(function (course) {
			const card = document.createElement('article');
			card.className = 'course-card' + (course.id === currentCourseId ? ' active' : '');
			card.setAttribute('data-id', course.id);
			card.style.cursor = 'pointer';
			card.innerHTML =
				'<div class="course-thumb" style="background-image:url(\'' + course.image + '\')"></div>' +
				'<div class="course-card-content">' +
					'<div class="course-meta-top">' +
						'<span class="course-category">' + course.category + '</span>' +
						'<span class="course-level">' + course.level + '</span>' +
					'</div>' +
					'<h3 class="course-title">' + course.title + '</h3>' +
					'<p class="course-instructor">By ' + course.instructor + '</p>' +
					'<div class="course-rating">' +
						'<span class="stars">&#9733;</span>' +
						'<span class="rating-num">' + course.rating + '</span>' +
						'<span class="rating-students">(' + course.students + ' students)</span>' +
					'</div>' +
					'<div class="course-meta-bottom">' +
						'<span class="course-duration"><i class="bi bi-clock"></i> ' + course.duration + '</span>' +
						'<span class="course-price">' + course.price + '</span>' +
					'</div>' +
				'</div>';

			card.addEventListener('click', function () {
				// Navigate to course details page
				const baseUrl = window.eduSkillBaseUrl || '/';
				window.location.href = baseUrl + 'pages/courcedetails.php?id=' + course.id;
			});

			coursesGrid.appendChild(card);
		});

		if (!currentCourseId) {
			currentCourseId = filteredCourses[0].id;
			renderCards(filteredCourses);
		}
	}

	function getFilteredCourses() {
		const keyword = (searchInput.value || '').trim().toLowerCase();
		const selectedLevel = levelFilter.value;
		const budgetRange = getSelectedBudgetRange();

		return courses.filter(function (course) {
			const byCategory = currentCategory === 'all' || course.category === currentCategory;
			const byLevel = selectedLevel === 'all' || course.level === selectedLevel;
			const byInstructor = currentInstructor === 'all' || course.instructor === currentInstructor;
			const priceNum = parseCoursePrice(course.price);
			const byPrice = priceNum >= budgetRange.min && priceNum <= budgetRange.max;
			const byKeyword = !keyword ||
				course.title.toLowerCase().includes(keyword) ||
				course.category.toLowerCase().includes(keyword) ||
				course.instructor.toLowerCase().includes(keyword) ||
				course.tools.join(' ').toLowerCase().includes(keyword);

			return byCategory && byLevel && byInstructor && byPrice && byKeyword;
		});
	}

	function rerender() {
		const filtered = getFilteredCourses();
		if (!filtered.some(function (course) { return course.id === currentCourseId; })) {
			currentCourseId = filtered.length ? filtered[0].id : null;
		}
		renderCards(filtered);
	}

	categoryFilters.addEventListener('click', function (event) {
		const target = event.target;
		if (!target.classList.contains('filter-chip')) return;

		currentCategory = target.getAttribute('data-filter');
		Array.prototype.forEach.call(categoryFilters.querySelectorAll('.filter-chip'), function (chip) {
			chip.classList.remove('active');
		});
		target.classList.add('active');
		rerender();
	});

	instructorToggle.addEventListener('click', function () {
		if (instructorDropdown.classList.contains('open')) {
			closeInstructorDropdown();
			return;
		}
		renderInstructorOptions(instructorSearch.value);
		openInstructorDropdown();
		instructorSearch.focus();
	});

	instructorSearch.addEventListener('input', function () {
		renderInstructorOptions(instructorSearch.value);
	});

	instructorOptions.addEventListener('click', function (event) {
		const target = event.target;
		if (!target.classList.contains('instructor-option')) return;
		setInstructor(target.getAttribute('data-value'));
		instructorSearch.value = '';
		renderInstructorOptions('');
		closeInstructorDropdown();
	});

	document.addEventListener('click', function (event) {
		if (!instructorDropdown.contains(event.target)) {
			closeInstructorDropdown();
		}
	});

	instructorSearch.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			closeInstructorDropdown();
			instructorToggle.focus();
		}
	});

	[searchInput, levelFilter, priceRangeFilter].forEach(function (element) {
		element.addEventListener('input', rerender);
		element.addEventListener('change', rerender);
	});

	resetFilters.addEventListener('click', function () {
		currentCategory = 'all';
		currentCourseId = null;
		searchInput.value = '';
		levelFilter.value = 'all';
		currentInstructor = 'all';
		instructorSelectedLabel.textContent = 'All Instructors';
		instructorSearch.value = '';
		renderInstructorOptions('');
		closeInstructorDropdown();
		priceRangeFilter.value = 'all';
		Array.prototype.forEach.call(categoryFilters.querySelectorAll('.filter-chip'), function (chip) {
			chip.classList.toggle('active', chip.getAttribute('data-filter') === 'all');
		});
		rerender();
	});

	renderInstructorOptions('');
	rerender();
})();
