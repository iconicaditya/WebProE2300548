/* provider-addcourses.js
   Frontend behavior for the multi-step add course form.
*/
document.addEventListener('DOMContentLoaded', function () {
    const steps = Array.from(document.querySelectorAll('.addcourse-step'));
    let current = 0;
    let maxVisited = 0; // keep for analytics, but no longer used to block clicks

    const showStep = (index) => {
        steps.forEach((s, i) => {
            s.classList.toggle('d-none', i !== index);
        });
        document.querySelectorAll('.step-link').forEach((link) => {
            const linkIndex = Number(link.dataset.step) - 1;
            link.classList.toggle('active', linkIndex === index);
            // ensure links are clickable
            link.classList.remove('disabled');
            link.removeAttribute('aria-disabled');
            link.style.pointerEvents = '';
        });
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        prevBtn.disabled = index === 0;
        // Show only Previous + Next for intermediate steps; on last step show Previous + Publish
        if (index === steps.length - 1) {
            nextBtn.classList.add('d-none');
            submitBtn.classList.remove('d-none');
            // generate review summary when arriving at final step
            generateSummary();
        } else {
            nextBtn.classList.remove('d-none');
            submitBtn.classList.add('d-none');
        }
    };

    // If URL contains a hash like #step-3, navigate there on load
    function stepFromHash() {
        const m = (location.hash || '').match(/^#step-(\d+)$/);
        if (m) {
            const idx = Number(m[1]) - 1;
            if (!isNaN(idx) && idx >= 0 && idx < steps.length) return idx;
        }
        return null;
    }

    const hashIndex = stepFromHash();
    if (hashIndex !== null) current = hashIndex;
    showStep(current);

    // Step nav clicks - allow navigation to any step
    document.getElementById('addcourse-steps-nav').addEventListener('click', function (e) {
        const link = e.target.closest('.step-link');
        if (!link) return;
        // Let the hash change (from anchor href) and handle navigation via hashchange.
        const target = Number(link.dataset.step) - 1;
        if (!isNaN(target)) {
            current = target;
            if (current > maxVisited) maxVisited = current;
            showStep(current);
            // update URL without scrolling
            try { history.replaceState(null, '', '#step-' + (current + 1)); } catch (err) { location.hash = 'step-' + (current + 1); }
        }
    });

    // Respond to browser hash changes (back/forward or direct link)
    window.addEventListener('hashchange', function () {
        const idx = stepFromHash();
        if (idx !== null) {
            current = idx;
            if (current > maxVisited) maxVisited = current;
            showStep(current);
        }
    });

    document.getElementById('nextBtn').addEventListener('click', function () {
        if (current < steps.length - 1) {
            current++;
            // user visits a new step, update maxVisited
            if (current > maxVisited) maxVisited = current;
            showStep(current);
        } else {
            // final review
            generateSummary();
            current = steps.length - 1;
            showStep(current);
        }
    });

    document.getElementById('prevBtn').addEventListener('click', function () {
        if (current > 0) current--;
        showStep(current);
    });

    // Thumbnail preview
    const thumbInput = document.getElementById('thumbnailInput');
    if (thumbInput) {
        thumbInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            const preview = document.querySelector('#thumbnailPreview img');
            const container = document.getElementById('thumbnailPreview');
            if (!file) { container.style.display = 'none'; return; }
            const url = URL.createObjectURL(file);
            preview.src = url;
            container.style.display = 'block';
        });
    }

    // Outcomes dynamic
    document.getElementById('addOutcomeBtn')?.addEventListener('click', function () {
        const list = document.getElementById('outcomesList');
        const item = document.createElement('div');
        item.className = 'input-group mb-2 outcome-item';
        item.innerHTML = '<input type="text" class="form-control" name="outcomes[]" placeholder="Learning outcome">' +
            '<button type="button" class="btn btn-outline-danger btn-remove-outcome">&times;</button>';
        list.appendChild(item);
    });

    document.body.addEventListener('click', function (e) {
        if (e.target.matches('.btn-remove-outcome')) {
            e.target.closest('.outcome-item')?.remove();
        }
        if (e.target.matches('.btn-remove-module')) {
            e.target.closest('.module-item')?.remove();
        }
        if (e.target.matches('.btn-remove-lesson')) {
            e.target.closest('.lesson-item')?.remove();
        }
        if (e.target.matches('.btn-add-lesson')) {
            const module = e.target.closest('.module-item');
            const lessons = module.querySelector('.lessons-list');
            const idx = lessons.querySelectorAll('.lesson-item').length;
            const template = document.createElement('div');
            template.className = 'lesson-item input-group mb-2';
            template.innerHTML = '<input type="text" name="" class="form-control" placeholder="Lesson title">' +
                '<input type="text" name="" class="form-control ms-2" placeholder="10:00">' +
                '<select class="form-select ms-2" style="max-width:140px;"><option>video</option><option>quiz</option></select>' +
                '<button type="button" class="btn btn-outline-danger btn-remove-lesson ms-2">&times;</button>';
            lessons.appendChild(template);
        }
        if (e.target.matches('.btn-remove-link')) {
            e.target.closest('.input-group')?.remove();
        }
    });

    // Add module
    document.getElementById('addModuleBtn')?.addEventListener('click', function () {
        const container = document.getElementById('modulesContainer');
        const count = container.querySelectorAll('.module-item').length;
        const card = document.createElement('div');
        card.className = 'module-item card mb-3';
        card.innerHTML = '<div class="card-body">' +
            '<div class="d-flex align-items-start justify-content-between mb-2">' +
            '<div><label class="form-label">Module title</label><input type="text" name="modules[' + count + '][title]" class="form-control" placeholder="Module title"></div>' +
            '<div class="ms-3"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-module">Remove</button></div>' +
            '</div>' +
            '<div class="lessons-list mb-2"></div>' +
            '<div><button type="button" class="btn btn-sm btn-outline-primary btn-add-lesson">Add lesson</button></div>' +
            '</div>';
        container.appendChild(card);
    });

    // Price toggle
    document.querySelectorAll('input[name="access"]').forEach(r => r.addEventListener('change', function () {
        const priceFields = document.getElementById('priceFields');
        if (this.value === 'paid') priceFields.style.display = '';
        else priceFields.style.display = 'none';
    }));

    // Add resource link
    document.getElementById('addResourceLink')?.addEventListener('click', function () {
        const container = document.getElementById('resourceLinks');
        const item = document.createElement('div');
        item.className = 'input-group mb-2';
        item.innerHTML = '<input type="url" name="resource_links[]" class="form-control" placeholder="https://example.com">' +
            '<button type="button" class="btn btn-outline-danger btn-remove-link">&times;</button>';
        container.appendChild(item);
    });

    // Summary generator
    function generateSummary() {
        const title = document.querySelector('input[name="title"]')?.value || '(Untitled)';
        const short = document.querySelector('textarea[name="short_description"]')?.value || '';
        const modules = document.querySelectorAll('.module-item');
        let modulesCount = modules.length;
        let lessonsCount = 0;
        modules.forEach(m => lessonsCount += m.querySelectorAll('.lesson-item').length);
        const price = document.querySelector('input[name="price"]')?.value || 'Free';

        const html = '<strong>' + escapeHtml(title) + '</strong>' +
            '<p>' + escapeHtml(short) + '</p>' +
            '<ul><li>Modules: ' + modulesCount + '</li><li>Lessons: ' + lessonsCount + '</li><li>Price: ' + (price || 'Free') + '</li></ul>';
        document.getElementById('courseSummary').innerHTML = html;
    }

    function escapeHtml(s) { return String(s).replace(/[&<>"]/g, function (m) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'})[m]; }); }

    // Simple client-side submit handler (frontend only)
    const form = document.getElementById('addCourseForm');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        alert('Frontend only: form validated and ready to submit to server.');
    });

});
