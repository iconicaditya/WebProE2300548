/* provider-addcourses.js
 * Frontend behavior for provider/addcourses multi-step builder.
 * Keeps all step interactivity in one file (no inline scripts in templates).
 */

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('addCourseForm');
    if (!form) return;

    const steps = Array.from(document.querySelectorAll('.addcourse-step'));
    const stepLinks = Array.from(document.querySelectorAll('.step-link'));

    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    const navContainer = document.querySelector('.addcourse-form-nav');

    let currentStep = 0;
    let maxVisited = 0;
    let resourceCounter = 0;

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, function (char) {
            return ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            })[char];
        });
    }

    function getStepFromHash() {
        const match = (window.location.hash || '').match(/^#step-(\d+)$/);
        if (!match) return null;
        const stepIndex = Number(match[1]) - 1;
        if (Number.isNaN(stepIndex) || stepIndex < 0 || stepIndex >= steps.length) return null;
        return stepIndex;
    }

    function updateHash(stepIndex) {
        const targetHash = '#step-' + (stepIndex + 1);
        try {
            window.history.replaceState(null, '', targetHash);
        } catch (error) {
            window.location.hash = targetHash;
        }
    }

    function setNavButtons(stepIndex) {
        if (!prevBtn || !nextBtn || !submitBtn) return;

        prevBtn.disabled = stepIndex === 0;

        const isLastStep = stepIndex === steps.length - 1;
        nextBtn.classList.toggle('d-none', isLastStep);
        submitBtn.classList.toggle('d-none', !isLastStep);

        // Defensive fallback when utility classes are unavailable.
        nextBtn.style.display = isLastStep ? 'none' : '';
        submitBtn.style.display = isLastStep ? '' : 'none';
    }

    function setStepNavActive(stepIndex) {
        stepLinks.forEach(function (link) {
            const linkIndex = Number(link.dataset.step) - 1;
            const isActive = linkIndex === stepIndex;
            link.classList.toggle('active', isActive);
            if (isActive) {
                link.setAttribute('aria-current', 'step');
            } else {
                link.removeAttribute('aria-current');
            }
            link.classList.remove('disabled');
            link.removeAttribute('aria-disabled');
            link.style.pointerEvents = '';
        });
    }

    function showStep(stepIndex, options) {
        const config = Object.assign({
            updateUrl: true,
            updateSummary: true
        }, options || {});

        steps.forEach(function (section, index) {
            section.classList.toggle('d-none', index !== stepIndex);
        });

        setStepNavActive(stepIndex);
        setNavButtons(stepIndex);

        if (navContainer) {
            navContainer.querySelectorAll('button').forEach(function (btn) {
                if (!['prevBtn', 'nextBtn', 'submitBtn'].includes(btn.id)) {
                    btn.classList.add('d-none');
                }
            });
        }

        if (config.updateUrl) {
            updateHash(stepIndex);
        }

        if (config.updateSummary && stepIndex === steps.length - 1) {
            generateSummary();
        }
    }

    function goToStep(stepIndex, options) {
        if (stepIndex < 0 || stepIndex >= steps.length) return;
        currentStep = stepIndex;
        if (currentStep > maxVisited) maxVisited = currentStep;
        showStep(currentStep, options);
    }

    // Step navigation
    stepLinks.forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            const target = Number(link.dataset.step) - 1;
            if (Number.isNaN(target)) return;
            goToStep(target);
        });
    });

    window.addEventListener('hashchange', function () {
        const hashStep = getStepFromHash();
        if (hashStep === null) return;
        goToStep(hashStep, { updateUrl: false });
    });

    prevBtn?.addEventListener('click', function () {
        goToStep(currentStep - 1);
    });

    nextBtn?.addEventListener('click', function () {
        goToStep(currentStep + 1);
    });

    // Basic details: thumbnail preview
    const thumbnailInput = document.getElementById('thumbnailInput');
    const thumbnailPreview = document.getElementById('thumbnailPreview');
    const thumbnailPreviewImg = thumbnailPreview ? thumbnailPreview.querySelector('img') : null;

    thumbnailInput?.addEventListener('change', function (event) {
        const file = event.target.files && event.target.files[0];
        if (!thumbnailPreview || !thumbnailPreviewImg) return;

        if (!file) {
            thumbnailPreview.classList.add('d-none');
            thumbnailPreviewImg.removeAttribute('src');
            return;
        }

        thumbnailPreviewImg.src = URL.createObjectURL(file);
        thumbnailPreview.classList.remove('d-none');
    });

    // Basic details: prevent negative students count
    const studentsInput = document.querySelector('input[name="students"]');
    studentsInput?.addEventListener('input', function () {
        if (Number(this.value) < 0) this.value = 0;
    });

    // Dynamic outcomes/requirements
    const outcomesList = document.getElementById('outcomesList');
    const addOutcomeBtn = document.getElementById('addOutcomeBtn');

    addOutcomeBtn?.addEventListener('click', function () {
        if (!outcomesList) return;
        const item = document.createElement('div');
        item.className = 'input-group outcome-item';
        item.innerHTML = '<input type="text" class="form-control" name="outcomes[]" placeholder="Learning outcome">' +
            '<button type="button" class="btn btn-outline-danger btn-remove-outcome" aria-label="Remove outcome">&times;</button>';
        outcomesList.appendChild(item);
    });

    const requirementsList = document.getElementById('requirementsList');
    const addRequirementBtn = document.getElementById('addRequirementBtn');

    addRequirementBtn?.addEventListener('click', function () {
        if (!requirementsList) return;
        const item = document.createElement('div');
        item.className = 'input-group requirement-item';
        item.innerHTML = '<input type="text" class="form-control" name="requirements[]" placeholder="Requirement">' +
            '<button type="button" class="btn btn-outline-danger btn-remove-requirement" aria-label="Remove requirement">&times;</button>';
        requirementsList.appendChild(item);
    });

    document.body.addEventListener('click', function (event) {
        if (event.target.closest('.btn-remove-outcome')) {
            event.target.closest('.outcome-item')?.remove();
        }
        if (event.target.closest('.btn-remove-requirement')) {
            event.target.closest('.requirement-item')?.remove();
        }
        if (event.target.closest('.btn-remove-link')) {
            event.target.closest('.input-group')?.remove();
        }
    });

    // Step 2: Promo URL preview
    const promoInput = document.getElementById('promoVideoUrl') || document.querySelector('input[name="promo_video_url"]');
    const promoPreview = document.getElementById('promoPreview');

    function updatePromoPreview() {
        if (!promoInput || !promoPreview) return;

        const url = (promoInput.value || '').trim();
        promoPreview.innerHTML = '';
        if (!url) return;

        const link = document.createElement('a');
        link.href = url;
        link.target = '_blank';
        link.rel = 'noopener noreferrer';
        link.className = 'link-primary';
        link.textContent = 'Open promo video';
        promoPreview.appendChild(link);
    }

    promoInput?.addEventListener('change', updatePromoPreview);
    promoInput?.addEventListener('input', updatePromoPreview);

    // Step 3: Modules & lessons builder
    const emptyState = document.getElementById('emptyState');
    const lessonsList = document.getElementById('lessonsList');
    const chooserArea = document.getElementById('chooserArea');
    const addBtnTop = document.getElementById('globalAddBtn');
    const addBtnBottomWrapper = document.getElementById('addBtnBottomWrapper');
    const addBtnBottom = document.getElementById('globalAddBtnBottom');
    const chooserCancel = document.getElementById('chooserCancel');
    const sectionSelector = document.getElementById('sectionSelector');

    function createSectionOptionsMarkup() {
        const options = [];
        for (let index = 1; index <= 30; index++) {
            options.push('<option value="' + index + '">Section ' + index + '</option>');
        }
        return options.join('');
    }

    function lessonCardTemplate(config) {
        const options = createSectionOptionsMarkup();
        return '' +
            '<li class="list-group-item border-0 px-0 lesson-card">' +
            '  <div class="card shadow-sm">' +
            '    <div class="card-header bg-white d-flex align-items-center justify-content-between lesson-card-header">' +
            '      <div class="d-flex align-items-center gap-2 flex-wrap">' +
            '        <span class="badge ' + config.badgeClass + '">' + config.label + '</span>' +
            '        <strong class="lesson-summary-title">' + config.defaultTitle + '</strong>' +
            '        <small class="text-muted lesson-summary-section">Section 1</small>' +
            '      </div>' +
            '      <div class="d-flex gap-2">' +
            '        <button type="button" class="btn btn-sm btn-outline-secondary btn-toggle">Collapse</button>' +
            '        <button type="button" class="btn btn-sm btn-outline-danger btn-remove">Remove</button>' +
            '      </div>' +
            '    </div>' +
            '    <div class="card-body">' +
            '      <input type="hidden" name="lessons[][type]" value="' + config.type + '">' +
            '      <div class="row g-2 mb-3">' +
            '        <div class="col-md-7">' +
            '          <input type="text" name="lessons[][title]" class="form-control ' + config.titleClass + '" placeholder="' + config.titlePlaceholder + '">' +
            '        </div>' +
            '        <div class="col-md-5">' +
            '          <select class="form-select section-select" name="lessons[][section]">' + options + '</select>' +
            '        </div>' +
            '      </div>' +
            config.body +
            '    </div>' +
            '  </div>' +
            '</li>';
    }

    function createVideoLessonItem() {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = lessonCardTemplate({
            type: 'video',
            label: 'Video',
            defaultTitle: 'Untitled video',
            badgeClass: 'bg-primary',
            titleClass: 'lesson-title-input',
            titlePlaceholder: 'Video title',
            body: '' +
                '<label class="form-label small">Upload video</label>' +
                '<input type="file" accept="video/*" name="lessons[][video]" class="form-control form-control-sm video-upload">' +
                '<div class="video-preview d-none mt-2">' +
                '  <video controls class="w-100"></video>' +
                '  <div class="small text-muted filename mt-1"></div>' +
                '</div>'
        });
        return wrapper.firstElementChild;
    }

    function createPdfLessonItem() {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = lessonCardTemplate({
            type: 'pdf',
            label: 'PDF',
            defaultTitle: 'Untitled PDF',
            badgeClass: 'bg-secondary',
            titleClass: 'lesson-title-input',
            titlePlaceholder: 'PDF title',
            body: '' +
                '<label class="form-label small">Upload PDF</label>' +
                '<input type="file" accept="application/pdf" name="lessons[][pdf]" class="form-control form-control-sm pdf-upload">' +
                '<div class="pdf-filename small text-muted d-none mt-2"></div>'
        });
        return wrapper.firstElementChild;
    }

    function createQuizLessonItem() {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = lessonCardTemplate({
            type: 'quiz',
            label: 'Quiz',
            defaultTitle: 'Untitled quiz',
            badgeClass: 'bg-success',
            titleClass: 'quiz-title',
            titlePlaceholder: 'Quiz title',
            body: '' +
                '<div class="questions"></div>' +
                '<div class="mt-3">' +
                '  <button type="button" class="btn btn-sm btn-outline-primary btn-add-question">Add question</button>' +
                '</div>'
        });
        return wrapper.firstElementChild;
    }

    function createQuestionNode() {
        const questionId = 'q_' + Math.random().toString(36).slice(2, 10);
        const wrapper = document.createElement('div');
        wrapper.className = 'question';
        wrapper.dataset.questionId = questionId;
        wrapper.innerHTML = '' +
            '<div class="d-flex gap-2 mb-2 flex-wrap">' +
            '  <input type="text" class="form-control question-text" placeholder="Question text">' +
            '  <button type="button" class="btn btn-sm btn-outline-danger btn-remove-question">Remove</button>' +
            '</div>' +
            '<div class="options"></div>' +
            '<button type="button" class="btn btn-sm btn-outline-secondary btn-add-option mt-2">Add option</button>';
        return wrapper;
    }

    function createOptionRow(questionId) {
        const row = document.createElement('div');
        row.className = 'option-row';
        row.innerHTML = '' +
            '<input type="radio" class="form-check-input correct-radio" name="correct_' + questionId + '">' +
            '<input type="text" class="form-control option-text" placeholder="Option text">' +
            '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-option">Remove</button>';
        return row;
    }

    function updateEmptyStateAndAddButtons() {
        if (!lessonsList || !emptyState || !addBtnBottomWrapper) return;
        const hasLessons = lessonsList.children.length > 0;
        emptyState.classList.toggle('d-none', hasLessons);

        if (chooserArea && !chooserArea.classList.contains('d-none')) {
            addBtnBottomWrapper.classList.add('d-none');
            return;
        }

        addBtnBottomWrapper.classList.toggle('d-none', !hasLessons);
    }

    function hideChooser() {
        chooserArea?.classList.add('d-none');
        addBtnTop?.classList.remove('d-none');
        updateEmptyStateAndAddButtons();
    }

    function showChooser() {
        chooserArea?.classList.remove('d-none');
        addBtnTop?.classList.add('d-none');
        addBtnBottomWrapper?.classList.add('d-none');
    }

    addBtnTop?.addEventListener('click', showChooser);
    addBtnBottom?.addEventListener('click', showChooser);
    chooserCancel?.addEventListener('click', hideChooser);

    chooserArea?.addEventListener('click', function (event) {
        const button = event.target.closest('.choose-type');
        if (!button || !lessonsList) return;

        let lessonNode;
        if (button.dataset.type === 'video') lessonNode = createVideoLessonItem();
        else if (button.dataset.type === 'pdf') lessonNode = createPdfLessonItem();
        else lessonNode = createQuizLessonItem();

        lessonsList.appendChild(lessonNode);

        const selectedSection = String(sectionSelector?.value || '1');
        const sectionSelect = lessonNode.querySelector('.section-select');
        if (sectionSelect) sectionSelect.value = selectedSection;

        hideChooser();
        updateLessonSummary(lessonNode);
    });

    function updateLessonSummary(lessonCard) {
        if (!lessonCard) return;

        const titleInput = lessonCard.querySelector('.lesson-title-input, .quiz-title');
        const typeInput = lessonCard.querySelector('input[name="lessons[][type]"]');
        const sectionInput = lessonCard.querySelector('.section-select');

        const summaryTitle = lessonCard.querySelector('.lesson-summary-title');
        const summarySection = lessonCard.querySelector('.lesson-summary-section');

        const fallback = typeInput ? typeInput.value : 'lesson';
        const title = titleInput ? titleInput.value.trim() : '';
        const sectionValue = sectionInput ? sectionInput.value : '1';

        if (summaryTitle) summaryTitle.textContent = title || ('Untitled ' + fallback);
        if (summarySection) summarySection.textContent = 'Section ' + sectionValue;
    }

    lessonsList?.addEventListener('click', function (event) {
        const removeBtn = event.target.closest('.btn-remove');
        if (removeBtn) {
            removeBtn.closest('.lesson-card')?.remove();
            updateEmptyStateAndAddButtons();
            return;
        }

        const toggleBtn = event.target.closest('.btn-toggle');
        if (toggleBtn) {
            const card = toggleBtn.closest('.card');
            const body = card ? card.querySelector('.card-body') : null;
            if (!body) return;

            const collapsed = body.classList.toggle('d-none');
            toggleBtn.textContent = collapsed ? 'Edit' : 'Collapse';
            return;
        }

        const header = event.target.closest('.lesson-card-header');
        if (header && !event.target.closest('button')) {
            const card = header.closest('.card');
            const body = card ? card.querySelector('.card-body') : null;
            const toggle = card ? card.querySelector('.btn-toggle') : null;
            if (!body) return;

            const collapsed = body.classList.toggle('d-none');
            if (toggle) toggle.textContent = collapsed ? 'Edit' : 'Collapse';
            return;
        }

        const addQuestionBtn = event.target.closest('.btn-add-question');
        if (addQuestionBtn) {
            const lessonCard = addQuestionBtn.closest('.lesson-card');
            const questions = lessonCard ? lessonCard.querySelector('.questions') : null;
            if (!questions) return;
            questions.appendChild(createQuestionNode());
            return;
        }

        const removeQuestionBtn = event.target.closest('.btn-remove-question');
        if (removeQuestionBtn) {
            removeQuestionBtn.closest('.question')?.remove();
            return;
        }

        const addOptionBtn = event.target.closest('.btn-add-option');
        if (addOptionBtn) {
            const question = addOptionBtn.closest('.question');
            if (!question) return;

            const optionsWrap = question.querySelector('.options');
            const questionId = question.dataset.questionId || ('q_' + Math.random().toString(36).slice(2, 8));
            question.dataset.questionId = questionId;
            optionsWrap?.appendChild(createOptionRow(questionId));
            return;
        }

        const removeOptionBtn = event.target.closest('.btn-remove-option');
        if (removeOptionBtn) {
            removeOptionBtn.closest('.option-row')?.remove();
        }
    });

    lessonsList?.addEventListener('input', function (event) {
        const lessonCard = event.target.closest('.lesson-card');
        if (!lessonCard) return;
        updateLessonSummary(lessonCard);
    });

    lessonsList?.addEventListener('change', function (event) {
        const lessonCard = event.target.closest('.lesson-card');
        if (lessonCard) {
            updateLessonSummary(lessonCard);
        }

        const videoInput = event.target.closest('.video-upload');
        if (videoInput) {
            const file = videoInput.files && videoInput.files[0];
            const card = videoInput.closest('.lesson-card');
            const preview = card ? card.querySelector('.video-preview') : null;
            const video = preview ? preview.querySelector('video') : null;
            const filename = preview ? preview.querySelector('.filename') : null;

            if (!preview || !video || !filename) return;

            if (!file) {
                preview.classList.add('d-none');
                video.removeAttribute('src');
                filename.textContent = '';
                return;
            }

            video.src = URL.createObjectURL(file);
            video.load();
            filename.textContent = file.name;
            preview.classList.remove('d-none');
            return;
        }

        const pdfInput = event.target.closest('.pdf-upload');
        if (pdfInput) {
            const file = pdfInput.files && pdfInput.files[0];
            const card = pdfInput.closest('.lesson-card');
            const filename = card ? card.querySelector('.pdf-filename') : null;
            if (!filename) return;

            if (!file) {
                filename.textContent = '';
                filename.classList.add('d-none');
                return;
            }

            filename.textContent = file.name;
            filename.classList.remove('d-none');
        }
    });

    // Step 4: Price & offer interactions
    const accessRadios = Array.from(document.querySelectorAll('input[name="access"]'));
    const priceFields = document.getElementById('priceFields');
    const applyCouponBtn = document.getElementById('applyCouponBtn');

    function updatePriceFieldsVisibility() {
        if (!priceFields) return;
        const selected = document.querySelector('input[name="access"]:checked');
        const isPaid = selected && selected.value === 'paid';
        priceFields.classList.toggle('d-none', !isPaid);
    }

    accessRadios.forEach(function (radio) {
        radio.addEventListener('change', updatePriceFieldsVisibility);
    });

    applyCouponBtn?.addEventListener('click', function () {
        const wrapper = applyCouponBtn.closest('.mt-3') || applyCouponBtn.closest('.input-group')?.parentElement;
        const note = wrapper ? wrapper.querySelector('.coupon-note') : null;
        if (!note) return;

        note.textContent = 'Coupon applied (frontend demo)';
        note.classList.remove('d-none');
        window.setTimeout(function () {
            note.textContent = '';
            note.classList.add('d-none');
        }, 2200);
    });

    // Step 5: Resources builder & validation
    const addResourceBtn = document.getElementById('addResourceBtn');
    const resourceFields = document.getElementById('resourceFields');
    const resourceError = document.getElementById('resourceError');

    function createResourceField(index) {
        return '' +
            '<div class="card resource-block">' +
            '  <div class="card-body">' +
            '    <div class="row g-3">' +
            '      <div class="col-md-5">' +
            '        <label for="resource-title-' + index + '" class="form-label">Resource title <span class="text-danger">*</span></label>' +
            '        <input type="text" id="resource-title-' + index + '" name="resources[' + index + '][title]" class="form-control" required maxlength="100" placeholder="e.g. Course Syllabus">' +
            '      </div>' +
            '      <div class="col-md-4">' +
            '        <label for="resource-subtitle-' + index + '" class="form-label">Resource subtitle</label>' +
            '        <input type="text" id="resource-subtitle-' + index + '" name="resources[' + index + '][subtitle]" class="form-control" maxlength="150" placeholder="e.g. Overview of course topics">' +
            '      </div>' +
            '      <div class="col-md-3 d-flex align-items-end">' +
            '        <button type="button" class="btn btn-outline-danger w-100 remove-resource">Remove</button>' +
            '      </div>' +
            '      <div class="col-12">' +
            '        <label for="resource-file-' + index + '" class="form-label">Upload file <span class="text-danger">*</span></label>' +
            '        <input type="file" id="resource-file-' + index + '" name="resources[' + index + '][file]" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx" required>' +
            '        <div class="form-text">PDF, Word (.doc/.docx), PPT (.ppt/.pptx) | Max 10MB</div>' +
            '      </div>' +
            '    </div>' +
            '  </div>' +
            '</div>';
    }

    function hideResourceError() {
        if (!resourceError) return;
        resourceError.classList.add('d-none');
        resourceError.textContent = '';
    }

    function showResourceError(message) {
        if (!resourceError) return;
        resourceError.textContent = message;
        resourceError.classList.remove('d-none');
    }

    function validateResources() {
        if (!resourceFields) return { valid: true, message: '' };

        const blocks = Array.from(resourceFields.querySelectorAll('.resource-block'));
        if (blocks.length === 0) return { valid: true, message: '' };

        const allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ];

        for (let i = 0; i < blocks.length; i++) {
            const block = blocks[i];
            const title = block.querySelector('input[name^="resources"][name$="[title]"]');
            const fileInput = block.querySelector('input[type="file"]');

            if (!title || !title.value.trim()) {
                return { valid: false, message: 'Resource title is required.' };
            }

            if (!fileInput || !fileInput.files || !fileInput.files.length) {
                return { valid: false, message: 'Please upload a file for each resource.' };
            }

            const file = fileInput.files[0];
            if (file.size > 10 * 1024 * 1024) {
                return { valid: false, message: 'File size must be less than 10MB.' };
            }

            const extensionAllowed = /\.(pdf|doc|docx|ppt|pptx)$/i.test(file.name || '');
            if (!allowedTypes.includes(file.type) && !extensionAllowed) {
                return { valid: false, message: 'Invalid file type. Only PDF, Word, PPT allowed.' };
            }
        }

        return { valid: true, message: '' };
    }

    addResourceBtn?.addEventListener('click', function () {
        if (!resourceFields) return;
        resourceCounter += 1;
        resourceFields.insertAdjacentHTML('beforeend', createResourceField(resourceCounter));
        hideResourceError();
    });

    resourceFields?.addEventListener('click', function (event) {
        const removeBtn = event.target.closest('.remove-resource');
        if (!removeBtn) return;
        removeBtn.closest('.resource-block')?.remove();
        hideResourceError();
    });

    // Step 6: Visibility text and summary copy helper
    const visibilitySwitch = document.getElementById('visibilitySwitch');
    const visibilityLabel = document.querySelector('label[for="visibilitySwitch"]');
    const courseSummary = document.getElementById('courseSummary');

    function updateVisibilityLabel() {
        if (!visibilitySwitch || !visibilityLabel) return;
        visibilityLabel.textContent = visibilitySwitch.checked
            ? 'Public (visible to learners)'
            : 'Private (hidden from learners)';
    }

    visibilitySwitch?.addEventListener('change', function () {
        updateVisibilityLabel();
        generateSummary();
    });

    courseSummary?.addEventListener('click', function () {
        const summaryText = (courseSummary.innerText || courseSummary.textContent || '').trim();
        if (!summaryText || !navigator.clipboard) return;

        navigator.clipboard.writeText(summaryText).then(function () {
            const note = document.createElement('div');
            note.className = 'small text-muted mt-2 summary-copy-note';
            note.textContent = 'Summary copied (frontend demo)';
            courseSummary.appendChild(note);

            window.setTimeout(function () {
                note.remove();
            }, 1500);
        }).catch(function () {
            // No-op fallback for browser clipboard restrictions.
        });
    });

    function generateSummary() {
        if (!courseSummary) return;

        const title = document.querySelector('input[name="title"]')?.value || '(Untitled)';
        const shortDescription = document.querySelector('textarea[name="short_description"]')?.value || '';

        const lessonCards = Array.from(document.querySelectorAll('#lessonsList .lesson-card'));
        const lessonsCount = lessonCards.length;
        const uniqueSections = new Set();

        lessonCards.forEach(function (card) {
            const sectionValue = card.querySelector('.section-select')?.value;
            if (sectionValue) uniqueSections.add(sectionValue);
        });

        const modulesCount = uniqueSections.size;

        const access = document.querySelector('input[name="access"]:checked')?.value || 'free';
        const priceValue = document.querySelector('input[name="price"]')?.value || '';
        const currency = document.querySelector('select[name="currency"]')?.value || 'USD';
        const priceText = access === 'paid'
            ? (priceValue ? escapeHtml(priceValue + ' ' + currency) : 'Paid')
            : 'Free';

        const resourcesCount = document.querySelectorAll('#resourceFields .resource-block').length;
        const visibilityText = visibilitySwitch && !visibilitySwitch.checked ? 'Private' : 'Public';

        const summaryHtml = '' +
            '<strong>' + escapeHtml(title) + '</strong>' +
            '<p class="mb-2">' + escapeHtml(shortDescription) + '</p>' +
            '<ul class="mb-0">' +
            '  <li>Modules: ' + modulesCount + '</li>' +
            '  <li>Lessons: ' + lessonsCount + '</li>' +
            '  <li>Resources: ' + resourcesCount + '</li>' +
            '  <li>Price: ' + priceText + '</li>' +
            '  <li>Visibility: ' + visibilityText + '</li>' +
            '</ul>';

        courseSummary.innerHTML = summaryHtml;
    }

    // Global submit (frontend demo)
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const resourceValidation = validateResources();
        if (!resourceValidation.valid) {
            showResourceError(resourceValidation.message);
            goToStep(4);
            return;
        }

        hideResourceError();
        generateSummary();
        alert('Frontend only: form validated and ready to submit to server.');
    });

    // Initial render
    const initialHashStep = getStepFromHash();
    currentStep = initialHashStep !== null ? initialHashStep : 0;
    showStep(currentStep, { updateUrl: initialHashStep === null });

    updatePromoPreview();
    updatePriceFieldsVisibility();
    updateVisibilityLabel();
    updateEmptyStateAndAddButtons();
});

