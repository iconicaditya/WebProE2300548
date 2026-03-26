/* provider-addcourses.js
 * Provider add-course wizard with backend draft/save/publish integration.
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
    const stepsContainer = document.getElementById('steps-container');

    const apiUrl = (form.dataset.apiUrl || '').trim();
    const coursesUrl = (form.dataset.coursesUrl || '').trim();
    const csrfTokenInput = form.querySelector('input[name="csrf_token"]');
    const courseIdField = document.getElementById('courseIdField');
    const appBaseUrl = apiUrl
        ? apiUrl.replace(/provider\/addcourses\/api\.php.*$/i, '')
        : (window.location.origin + '/');

    let currentStep = 0;
    let maxVisited = 0;
    let resourceCounter = 0;

    let currentCourseId = Number(form.dataset.courseId || courseIdField?.value || 0);
    if (Number.isNaN(currentCourseId) || currentCourseId < 0) {
        currentCourseId = 0;
    }

    let busy = false;
    let sectionTitleMap = {};

    const apiAlert = document.createElement('div');
    apiAlert.id = 'addcourseApiAlert';
    apiAlert.className = 'alert d-none';
    if (stepsContainer && stepsContainer.parentElement) {
        stepsContainer.parentElement.insertBefore(apiAlert, stepsContainer);
    }

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

    function toPublicUrl(path) {
        const value = String(path || '').trim();
        if (!value) return '';
        if (/^https?:\/\//i.test(value)) return value;
        return appBaseUrl + value.replace(/^\/+/, '');
    }

    function showApiAlert(type, message, timeoutMs) {
        if (!apiAlert) return;
        apiAlert.className = 'alert alert-' + type;
        apiAlert.textContent = message;
        apiAlert.classList.remove('d-none');
        if (timeoutMs && timeoutMs > 0) {
            window.setTimeout(function () {
                apiAlert.classList.add('d-none');
            }, timeoutMs);
        }
    }

    function clearApiAlert() {
        if (!apiAlert) return;
        apiAlert.classList.add('d-none');
    }

    function clearFieldErrors() {
        form.querySelectorAll('.is-invalid').forEach(function (field) {
            field.classList.remove('is-invalid');
        });
    }

    function markFieldError(name) {
        if (!name) return;
        const selectors = [
            '[name="' + name + '"]',
            '[name="' + name + '[]"]',
            '[name^="' + name + '["]'
        ];

        for (let i = 0; i < selectors.length; i++) {
            const field = form.querySelector(selectors[i]);
            if (field) {
                field.classList.add('is-invalid');
                return;
            }
        }

        if (name === 'title') {
            form.querySelector('#courseTitle')?.classList.add('is-invalid');
        } else if (name === 'short_description') {
            form.querySelector('#courseShortDescription')?.classList.add('is-invalid');
        }
    }

    function applyValidationErrors(errors) {
        clearFieldErrors();
        const keys = Object.keys(errors || {});
        keys.forEach(markFieldError);

        if (keys.length > 0) {
            const firstMessage = errors[keys[0]];
            showApiAlert('danger', firstMessage || 'Please correct the highlighted fields.');
        }
    }

    function setBusy(isBusy, buttonText) {
        busy = !!isBusy;

        const defaultNext = 'Next';
        const defaultSubmit = 'Publish';

        if (prevBtn) prevBtn.disabled = busy || currentStep === 0;
        if (nextBtn) {
            nextBtn.disabled = busy;
            if (buttonText && !nextBtn.classList.contains('d-none')) {
                nextBtn.textContent = buttonText;
            } else {
                nextBtn.textContent = defaultNext;
            }
        }
        if (submitBtn) {
            submitBtn.disabled = busy;
            if (buttonText && !submitBtn.classList.contains('d-none')) {
                submitBtn.textContent = buttonText;
            } else {
                submitBtn.textContent = defaultSubmit;
            }
        }
    }

    function normalizeApiError(error) {
        if (!error) {
            return {
                message: 'Unexpected error occurred.',
                errors: null
            };
        }

        if (error.validationErrors) {
            return {
                message: error.message || 'Validation failed.',
                errors: error.validationErrors
            };
        }

        return {
            message: error.message || 'Request failed.',
            errors: null
        };
    }

    async function apiRequest(action, config) {
        if (!apiUrl) {
            throw new Error('API endpoint URL is missing.');
        }

        const options = Object.assign({
            method: 'GET',
            params: null,
            formData: null
        }, config || {});

        const method = String(options.method || 'GET').toUpperCase();
        let url = apiUrl;
        const fetchOptions = {
            method: method,
            credentials: 'same-origin'
        };

        if (method === 'GET') {
            const params = new URLSearchParams();
            params.set('action', action);
            Object.entries(options.params || {}).forEach(function (entry) {
                const key = entry[0];
                const value = entry[1];
                if (value !== undefined && value !== null && String(value) !== '') {
                    params.set(key, String(value));
                }
            });
            url += (url.indexOf('?') >= 0 ? '&' : '?') + params.toString();
        } else {
            const fd = options.formData instanceof FormData ? options.formData : new FormData();
            fd.set('action', action);
            const csrfToken = String(csrfTokenInput?.value || '');
            if (csrfToken) {
                fd.set('csrf_token', csrfToken);
            }
            fetchOptions.body = fd;
        }

        const response = await fetch(url, fetchOptions);
        let payload = null;
        try {
            payload = await response.json();
        } catch (error) {
            throw new Error('Server returned invalid JSON response.');
        }

        if (!response.ok || !payload || payload.ok !== true) {
            const err = new Error((payload && payload.message) || 'Request failed.');
            err.code = payload && payload.code ? payload.code : 'REQUEST_FAILED';
            if (payload && payload.errors && typeof payload.errors === 'object') {
                err.validationErrors = payload.errors;
            }
            throw err;
        }

        return payload.data || {};
    }

    function isApprovalRequiredError(error) {
        const code = String(error && error.code ? error.code : '').toUpperCase();
        return code === 'APPROVAL_REQUIRED';
    }

    function updateCourseId(courseId) {
        const id = Number(courseId || 0);
        currentCourseId = Number.isNaN(id) || id < 0 ? 0 : id;
        if (courseIdField) {
            courseIdField.value = String(currentCourseId);
        }
        form.dataset.courseId = String(currentCourseId);
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

        prevBtn.disabled = busy || stepIndex === 0;

        const isLastStep = stepIndex === steps.length - 1;
        nextBtn.classList.toggle('d-none', isLastStep);
        submitBtn.classList.toggle('d-none', !isLastStep);

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

    function appendInputsFromSection(fd, sectionElement) {
        if (!sectionElement) return;
        const controls = Array.from(sectionElement.querySelectorAll('input, select, textarea'));

        controls.forEach(function (control) {
            if (!control || control.disabled || !control.name) return;

            const tag = control.tagName.toLowerCase();
            const type = (control.type || '').toLowerCase();

            if (tag === 'input' && ['button', 'submit', 'reset'].includes(type)) return;

            if (type === 'checkbox' || type === 'radio') {
                if (control.checked) {
                    fd.append(control.name, control.value || '1');
                }
                return;
            }

            if (type === 'file') {
                if (control.files && control.files.length) {
                    Array.from(control.files).forEach(function (file) {
                        fd.append(control.name, file);
                    });
                }
                return;
            }

            fd.append(control.name, control.value || '');
        });
    }

    async function ensureDraftExists() {
        if (currentCourseId > 0) {
            return currentCourseId;
        }

        const title = String(form.querySelector('input[name="title"]')?.value || '').trim();
        const shortDescription = String(form.querySelector('textarea[name="short_description"]')?.value || '').trim();

        const validation = {};
        if (!title) validation.title = 'Course name is required.';
        if (!shortDescription) validation.short_description = 'Short description is required.';

        if (Object.keys(validation).length > 0) {
            const err = new Error('Please fill required fields to create draft.');
            err.validationErrors = validation;
            throw err;
        }

        const draftPayload = new FormData();
        draftPayload.append('title', title);
        draftPayload.append('short_description', shortDescription);

        const draftResponse = await apiRequest('create_draft', {
            method: 'POST',
            formData: draftPayload
        });

        const newCourseId = Number(draftResponse.course_id || 0);
        if (newCourseId <= 0) {
            throw new Error('Server did not return a valid draft id.');
        }

        updateCourseId(newCourseId);
        return currentCourseId;
    }

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

    const studentsInput = document.querySelector('input[name="students"]');
    studentsInput?.addEventListener('input', function () {
        if (Number(this.value) < 0) this.value = 0;
    });

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

    const emptyState = document.getElementById('emptyState');
    const lessonsList = document.getElementById('lessonsList');
    const chooserArea = document.getElementById('chooserArea');
    const addBtnTop = document.getElementById('globalAddBtn');
    const addBtnBottomWrapper = document.getElementById('addBtnBottomWrapper');
    const addBtnBottom = document.getElementById('globalAddBtnBottom');
    const chooserCancel = document.getElementById('chooserCancel');
    const sectionSelector = document.getElementById('sectionSelector');
    const sectionTitleInput = document.getElementById('sectionTitleInput');

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

    function createQuestionNode(config) {
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

        if (config && config.text) {
            const questionTextInput = wrapper.querySelector('.question-text');
            if (questionTextInput) questionTextInput.value = config.text;
        }

        if (config && Array.isArray(config.options) && config.options.length) {
            const optionsWrap = wrapper.querySelector('.options');
            config.options.forEach(function (optionText, index) {
                const optionNode = createOptionRow(questionId, optionText, index === Number(config.correct_index));
                optionsWrap?.appendChild(optionNode);
            });
        }

        return wrapper;
    }

    function createOptionRow(questionId, value, checked) {
        const row = document.createElement('div');
        row.className = 'option-row';
        row.innerHTML = '' +
            '<input type="radio" class="form-check-input correct-radio" name="correct_' + questionId + '">' +
            '<input type="text" class="form-control option-text" placeholder="Option text">' +
            '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-option">Remove</button>';

        const optionText = row.querySelector('.option-text');
        const correctRadio = row.querySelector('.correct-radio');
        if (optionText) optionText.value = value || '';
        if (correctRadio) correctRadio.checked = !!checked;

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

    function syncSectionTitleInputFromMap() {
        if (!sectionSelector || !sectionTitleInput) return;
        const sectionOrder = String(sectionSelector.value || '1');
        sectionTitleInput.value = sectionTitleMap[sectionOrder] || '';
    }

    sectionSelector?.addEventListener('change', syncSectionTitleInputFromMap);
    sectionTitleInput?.addEventListener('input', function () {
        if (!sectionSelector) return;
        const sectionOrder = String(sectionSelector.value || '1');
        const title = String(sectionTitleInput.value || '').trim();
        if (title) {
            sectionTitleMap[sectionOrder] = title;
        } else {
            delete sectionTitleMap[sectionOrder];
        }
    });

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
            optionsWrap?.appendChild(createOptionRow(questionId, '', false));
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

        note.textContent = 'Coupon applied';
        note.classList.remove('d-none');
        window.setTimeout(function () {
            note.textContent = '';
            note.classList.add('d-none');
        }, 2200);
    });

    const addResourceBtn = document.getElementById('addResourceBtn');
    const resourceFields = document.getElementById('resourceFields');
    const resourceError = document.getElementById('resourceError');

    function createResourceField(index, existingData) {
        const hasExisting = existingData && existingData.file_path;
        const existingName = hasExisting ? String(existingData.file_path).split('/').pop() : '';

        return '' +
            '<div class="card resource-block" data-file-path="' + escapeHtml(hasExisting ? existingData.file_path : '') + '" data-file-mime="' + escapeHtml(hasExisting ? (existingData.mime_type || '') : '') + '" data-file-size="' + escapeHtml(hasExisting ? String(existingData.file_size_bytes || 0) : '0') + '">' +
            '  <div class="card-body">' +
            '    <div class="row g-3">' +
            '      <div class="col-md-5">' +
            '        <label for="resource-title-' + index + '" class="form-label">Resource title <span class="text-danger">*</span></label>' +
            '        <input type="text" id="resource-title-' + index + '" name="resources[' + index + '][title]" class="form-control" required maxlength="100" placeholder="e.g. Course Syllabus" value="' + escapeHtml(existingData?.title || '') + '">' +
            '      </div>' +
            '      <div class="col-md-4">' +
            '        <label for="resource-subtitle-' + index + '" class="form-label">Resource subtitle</label>' +
            '        <input type="text" id="resource-subtitle-' + index + '" name="resources[' + index + '][subtitle]" class="form-control" maxlength="150" placeholder="e.g. Overview of course topics" value="' + escapeHtml(existingData?.subtitle || '') + '">' +
            '      </div>' +
            '      <div class="col-md-3 d-flex align-items-end">' +
            '        <button type="button" class="btn btn-outline-danger w-100 remove-resource">Remove</button>' +
            '      </div>' +
            '      <div class="col-12">' +
            '        <label for="resource-file-' + index + '" class="form-label">Upload file' + (hasExisting ? ' (optional replace)' : ' <span class="text-danger">*</span>') + '</label>' +
            '        <input type="file" id="resource-file-' + index + '" name="resources[' + index + '][file]" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx" ' + (hasExisting ? '' : 'required') + '>' +
            (hasExisting ? ('        <div class="form-text">Existing file: ' + escapeHtml(existingName) + '</div>') : '') +
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
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/octet-stream',
            'application/zip'
        ];

        for (let i = 0; i < blocks.length; i++) {
            const block = blocks[i];
            const title = block.querySelector('input[name^="resources"][name$="[title]"]');
            const fileInput = block.querySelector('input[type="file"]');
            const existingPath = String(block.dataset.filePath || '').trim();

            if (!title || !title.value.trim()) {
                return { valid: false, message: 'Resource title is required.' };
            }

            const hasNewFile = fileInput && fileInput.files && fileInput.files.length;
            if (!hasNewFile && !existingPath) {
                return { valid: false, message: 'Please upload a file for each resource.' };
            }

            if (hasNewFile) {
                const file = fileInput.files[0];
                if (file.size > 10 * 1024 * 1024) {
                    return { valid: false, message: 'File size must be less than 10MB.' };
                }

                const extensionAllowed = /\.(pdf|doc|docx|ppt|pptx)$/i.test(file.name || '');
                if (!allowedTypes.includes(file.type) && !extensionAllowed) {
                    return { valid: false, message: 'Invalid file type. Only PDF, Word, PPT allowed.' };
                }
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
            note.textContent = 'Summary copied';
            courseSummary.appendChild(note);

            window.setTimeout(function () {
                note.remove();
            }, 1500);
        }).catch(function () {
            // Browser clipboard limitations.
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

    function collectSectionsPayload() {
        const orders = new Set();
        const lessonCards = Array.from(document.querySelectorAll('#lessonsList .lesson-card'));

        lessonCards.forEach(function (card) {
            const sectionValue = Number(card.querySelector('.section-select')?.value || 1);
            orders.add(sectionValue);
        });

        if (orders.size === 0) {
            orders.add(Number(sectionSelector?.value || 1));
        }

        return Array.from(orders)
            .filter(function (order) { return Number.isFinite(order) && order > 0; })
            .sort(function (a, b) { return a - b; })
            .map(function (order) {
                const key = String(order);
                return {
                    order: order,
                    title: sectionTitleMap[key] || ('Section ' + order)
                };
            });
    }

    function collectQuizFromLessonCard(lessonCard) {
        const questions = Array.from(lessonCard.querySelectorAll('.question'));
        return questions.map(function (questionNode) {
            const questionText = String(questionNode.querySelector('.question-text')?.value || '').trim();
            const options = Array.from(questionNode.querySelectorAll('.option-row .option-text'))
                .map(function (input) { return String(input.value || '').trim(); })
                .filter(function (text) { return text !== ''; });

            let correctIndex = -1;
            const optionRows = Array.from(questionNode.querySelectorAll('.option-row'));
            optionRows.forEach(function (row, idx) {
                const radio = row.querySelector('.correct-radio');
                if (radio && radio.checked) {
                    correctIndex = idx;
                }
            });

            return {
                text: questionText,
                options: options,
                correct_index: correctIndex
            };
        }).filter(function (q) {
            return q.text !== '' || q.options.length > 0;
        });
    }

    function collectLessonsPayload(payloadFormData) {
        const lessonCards = Array.from(document.querySelectorAll('#lessonsList .lesson-card'));
        return lessonCards.map(function (lessonCard, index) {
            const type = String(lessonCard.querySelector('input[name="lessons[][type]"]')?.value || 'video').toLowerCase();
            const title = String(lessonCard.querySelector('.lesson-title-input, .quiz-title')?.value || '').trim();
            const sectionOrder = Number(lessonCard.querySelector('.section-select')?.value || 1);

            const row = {
                type: ['video', 'pdf', 'quiz'].includes(type) ? type : 'video',
                title: title,
                section_order: sectionOrder > 0 ? sectionOrder : 1,
                is_preview: false
            };

            if (row.type === 'video') {
                const input = lessonCard.querySelector('.video-upload');
                const file = input && input.files && input.files[0] ? input.files[0] : null;
                if (file) {
                    const key = 'lesson_video_' + index;
                    payloadFormData.append(key, file);
                    row.video_upload_key = key;
                } else if (lessonCard.dataset.videoPath) {
                    row.video_path = lessonCard.dataset.videoPath;
                }
            } else if (row.type === 'pdf') {
                const input = lessonCard.querySelector('.pdf-upload');
                const file = input && input.files && input.files[0] ? input.files[0] : null;
                if (file) {
                    const key = 'lesson_pdf_' + index;
                    payloadFormData.append(key, file);
                    row.pdf_upload_key = key;
                } else if (lessonCard.dataset.pdfPath) {
                    row.pdf_path = lessonCard.dataset.pdfPath;
                }
            } else {
                row.quiz = collectQuizFromLessonCard(lessonCard);
            }

            return row;
        });
    }

    function collectResourcesPayload(payloadFormData) {
        if (!resourceFields) return [];

        const blocks = Array.from(resourceFields.querySelectorAll('.resource-block'));
        return blocks.map(function (block, index) {
            const title = String(block.querySelector('input[name^="resources"][name$="[title]"]')?.value || '').trim();
            const subtitle = String(block.querySelector('input[name^="resources"][name$="[subtitle]"]')?.value || '').trim();
            const fileInput = block.querySelector('input[type="file"]');

            const row = {
                title: title,
                subtitle: subtitle
            };

            const file = fileInput && fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
            if (file) {
                const key = 'resource_file_' + index;
                payloadFormData.append(key, file);
                row.upload_key = key;
            } else {
                const existingPath = String(block.dataset.filePath || '').trim();
                if (existingPath) {
                    row.file_path = existingPath;
                    row.mime_type = String(block.dataset.fileMime || 'application/octet-stream');
                    row.file_size_bytes = Number(block.dataset.fileSize || 0);
                }
            }

            return row;
        });
    }

    function buildStepPayload(stepNumber) {
        const payload = new FormData();
        payload.append('step', String(stepNumber));

        if (stepNumber === 1 || stepNumber === 2 || stepNumber === 4) {
            appendInputsFromSection(payload, document.getElementById('step-' + stepNumber));
        } else if (stepNumber === 3) {
            payload.append('sections', JSON.stringify(collectSectionsPayload()));
            payload.append('lessons', JSON.stringify(collectLessonsPayload(payload)));
        } else if (stepNumber === 5) {
            payload.append('resources', JSON.stringify(collectResourcesPayload(payload)));
        } else if (stepNumber === 6) {
            payload.append('visibility', visibilitySwitch && !visibilitySwitch.checked ? 'private' : 'public');
        }

        return payload;
    }

    async function saveCurrentStep() {
        await ensureDraftExists();

        const stepNumber = currentStep + 1;
        const payload = buildStepPayload(stepNumber);
        payload.append('course_id', String(currentCourseId));

        const response = await apiRequest('save_step', {
            method: 'POST',
            formData: payload
        });

        if (response && Number(response.course_id || 0) > 0) {
            updateCourseId(response.course_id);
        }

        return response;
    }

    function resetListWithValues(listElement, values, itemClass, inputName, placeholder, removeBtnClass) {
        if (!listElement) return;

        listElement.innerHTML = '';
        const source = Array.isArray(values) && values.length ? values : [''];

        source.forEach(function (value) {
            const item = document.createElement('div');
            item.className = 'input-group ' + itemClass;
            item.innerHTML =
                '<input type="text" name="' + inputName + '" class="form-control" placeholder="' + escapeHtml(placeholder) + '" value="' + escapeHtml(String(value || '')) + '">' +
                '<button type="button" class="btn btn-outline-danger ' + removeBtnClass + '" aria-label="Remove">&times;</button>';
            listElement.appendChild(item);
        });
    }

    function hydrateLessons(course) {
        if (!lessonsList) return;

        lessonsList.innerHTML = '';
        const sectionMap = {};
        (course.sections || []).forEach(function (section) {
            const order = Number(section.section_order || 0);
            if (order > 0) {
                sectionMap[String(section.id)] = order;
                sectionTitleMap[String(order)] = String(section.title || ('Section ' + order));
            }
        });

        (course.lessons || []).forEach(function (lesson) {
            let lessonNode;
            const type = String(lesson.lesson_type || '').toLowerCase();
            if (type === 'pdf') {
                lessonNode = createPdfLessonItem();
            } else if (type === 'quiz') {
                lessonNode = createQuizLessonItem();
            } else {
                lessonNode = createVideoLessonItem();
            }

            const titleInput = lessonNode.querySelector('.lesson-title-input, .quiz-title');
            if (titleInput) titleInput.value = String(lesson.title || '');

            const sectionSelect = lessonNode.querySelector('.section-select');
            let sectionOrder = 1;
            if (lesson.section_id && sectionMap[String(lesson.section_id)]) {
                sectionOrder = sectionMap[String(lesson.section_id)];
            }
            if (sectionSelect) sectionSelect.value = String(sectionOrder);

            if (type === 'video' && lesson.video_path) {
                lessonNode.dataset.videoPath = String(lesson.video_path);
                const preview = lessonNode.querySelector('.video-preview');
                const filename = lessonNode.querySelector('.video-preview .filename');
                if (preview && filename) {
                    filename.textContent = String(lesson.video_path).split('/').pop();
                    preview.classList.remove('d-none');
                    const videoTag = preview.querySelector('video');
                    if (videoTag) {
                        videoTag.removeAttribute('src');
                    }
                }
            }

            if (type === 'pdf' && lesson.pdf_path) {
                lessonNode.dataset.pdfPath = String(lesson.pdf_path);
                const nameBox = lessonNode.querySelector('.pdf-filename');
                if (nameBox) {
                    nameBox.textContent = String(lesson.pdf_path).split('/').pop();
                    nameBox.classList.remove('d-none');
                }
            }

            if (type === 'quiz') {
                const questionsWrap = lessonNode.querySelector('.questions');
                const quiz = Array.isArray(lesson.quiz) ? lesson.quiz : [];
                if (questionsWrap) {
                    questionsWrap.innerHTML = '';
                    quiz.forEach(function (questionObj) {
                        questionsWrap.appendChild(createQuestionNode(questionObj));
                    });
                }
            }

            lessonsList.appendChild(lessonNode);
            updateLessonSummary(lessonNode);
        });

        updateEmptyStateAndAddButtons();
        syncSectionTitleInputFromMap();
    }

    function hydrateResources(course) {
        if (!resourceFields) return;
        resourceFields.innerHTML = '';
        resourceCounter = 0;

        (course.resources || []).forEach(function (resource) {
            resourceCounter += 1;
            resourceFields.insertAdjacentHTML('beforeend', createResourceField(resourceCounter, resource));
        });
    }

    function hydrateCourseForm(course) {
        if (!course || typeof course !== 'object') return;

        const setValue = function (selector, value) {
            const node = form.querySelector(selector);
            if (node) node.value = value == null ? '' : String(value);
        };

        setValue('input[name="title"]', course.title || '');
        setValue('textarea[name="short_description"]', course.short_description || '');
        setValue('textarea[name="description"]', course.description || '');
        setValue('input[name="duration"]', course.duration_label || '');
        setValue('input[name="lessons"]', course.lesson_count_estimate || 0);
        setValue('input[name="students"]', course.student_count_estimate || 0);
        setValue('input[name="language"]', course.language || 'English');

        const levelMap = {
            all_levels: 'All levels',
            beginner: 'Beginner',
            intermediate: 'Intermediate',
            advanced: 'Advanced'
        };
        setValue('select[name="level"]', levelMap[String(course.level || 'all_levels')] || 'All levels');

        const certYes = form.querySelector('#certYes');
        const certNo = form.querySelector('#certNo');
        const certEnabled = !!course.certification_enabled;
        if (certYes) certYes.checked = certEnabled;
        if (certNo) certNo.checked = !certEnabled;

        const includes = Array.isArray(course.includes) ? course.includes : [];
        form.querySelectorAll('input[name="included[]"]').forEach(function (checkbox) {
            checkbox.checked = includes.includes(checkbox.value);
        });

        resetListWithValues(
            outcomesList,
            Array.isArray(course.outcomes) ? course.outcomes : [],
            'outcome-item',
            'outcomes[]',
            'Learning outcome',
            'btn-remove-outcome'
        );
        resetListWithValues(
            requirementsList,
            Array.isArray(course.requirements) ? course.requirements : [],
            'requirement-item',
            'requirements[]',
            'Requirement',
            'btn-remove-requirement'
        );

        setValue('input[name="promo_video_url"]', course.promo_video_url || '');

        if (thumbnailPreview && thumbnailPreviewImg && course.thumbnail_path) {
            thumbnailPreviewImg.src = toPublicUrl(course.thumbnail_path);
            thumbnailPreview.classList.remove('d-none');
        }

        hydrateLessons(course);

        const accessType = String(course.access_type || 'free');
        const accessRadio = form.querySelector('input[name="access"][value="' + accessType + '"]');
        if (accessRadio) accessRadio.checked = true;

        setValue('input[name="price"]', course.price_amount != null ? course.price_amount : '');
        setValue('select[name="currency"]', course.currency_code || 'USD');
        setValue('input[name="coupon"]', course.coupon_code || '');

        hydrateResources(course);

        if (visibilitySwitch) {
            visibilitySwitch.checked = String(course.visibility || 'public') !== 'private';
        }

        updatePromoPreview();
        updatePriceFieldsVisibility();
        updateVisibilityLabel();
        updateEmptyStateAndAddButtons();
        generateSummary();
    }

    async function loadCourseForEdit(courseId) {
        if (courseId <= 0) return;

        try {
            setBusy(true, 'Loading...');
            clearApiAlert();

            const data = await apiRequest('get_course', {
                method: 'GET',
                params: { course_id: courseId }
            });

            if (data && data.schema_ready === false) {
                showApiAlert('warning', 'Course schema is not ready. Apply migration before editing.', 4500);
                return;
            }

            if (!data || !data.course) {
                showApiAlert('danger', 'Unable to load course data for editing.', 4500);
                return;
            }

            hydrateCourseForm(data.course);
            showApiAlert('info', 'Loaded existing draft/course for editing.', 2200);
        } catch (error) {
            const parsed = normalizeApiError(error);
            if (parsed.errors) {
                applyValidationErrors(parsed.errors);
            } else if (isApprovalRequiredError(error)) {
                showApiAlert('warning', parsed.message || 'Admin approval is required before creating courses.');
            } else {
                showApiAlert('danger', parsed.message);
            }
        } finally {
            setBusy(false);
        }
    }

    async function handleStepSaveAndMove(targetStep) {
        if (busy) return;

        try {
            clearFieldErrors();
            clearApiAlert();

            if (currentStep === 4) {
                const resourceValidation = validateResources();
                if (!resourceValidation.valid) {
                    showResourceError(resourceValidation.message);
                    return;
                }
                hideResourceError();
            }

            setBusy(true, 'Saving...');
            await saveCurrentStep();

            showApiAlert('success', 'Step saved.', 1400);
            goToStep(targetStep);
        } catch (error) {
            const parsed = normalizeApiError(error);
            if (parsed.errors) {
                applyValidationErrors(parsed.errors);
            } else if (isApprovalRequiredError(error)) {
                showApiAlert('warning', parsed.message || 'Admin approval is required before creating courses.');
            } else {
                showApiAlert('danger', parsed.message);
            }
        } finally {
            setBusy(false);
        }
    }

    stepLinks.forEach(function (link) {
        link.addEventListener('click', async function (event) {
            event.preventDefault();
            if (busy) return;

            const target = Number(link.dataset.step) - 1;
            if (Number.isNaN(target) || target < 0 || target >= steps.length) return;

            if (target === currentStep) {
                goToStep(target);
                return;
            }

            if (target > currentStep) {
                await handleStepSaveAndMove(target);
                return;
            }

            goToStep(target);
        });
    });

    window.addEventListener('hashchange', function () {
        const hashStep = getStepFromHash();
        if (hashStep === null || busy) return;
        goToStep(hashStep, { updateUrl: false });
    });

    prevBtn?.addEventListener('click', function () {
        if (busy) return;
        goToStep(currentStep - 1);
    });

    nextBtn?.addEventListener('click', async function () {
        if (busy) return;
        await handleStepSaveAndMove(currentStep + 1);
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (busy) return;

        const resourceValidation = validateResources();
        if (!resourceValidation.valid) {
            showResourceError(resourceValidation.message);
            goToStep(4);
            return;
        }

        hideResourceError();

        try {
            clearFieldErrors();
            clearApiAlert();

            setBusy(true, 'Publishing...');

            await saveCurrentStep();

            const publishPayload = new FormData();
            publishPayload.append('course_id', String(currentCourseId));
            publishPayload.append('visibility', visibilitySwitch && !visibilitySwitch.checked ? 'private' : 'public');

            const publishResponse = await apiRequest('publish', {
                method: 'POST',
                formData: publishPayload
            });

            showApiAlert('success', 'Course published successfully. Redirecting...', 2500);

            const redirectUrl = String(publishResponse.redirect_url || coursesUrl || '').trim();
            if (redirectUrl) {
                window.setTimeout(function () {
                    window.location.href = redirectUrl;
                }, 900);
            }
        } catch (error) {
            const parsed = normalizeApiError(error);
            if (parsed.errors) {
                applyValidationErrors(parsed.errors);
            } else if (isApprovalRequiredError(error)) {
                showApiAlert('warning', parsed.message || 'Admin approval is required before creating courses.');
                window.setTimeout(function () {
                    window.location.href = (coursesUrl || (appBaseUrl + 'provider/?page=dashboard'));
                }, 1200);
            } else {
                showApiAlert('danger', parsed.message);
            }
        } finally {
            setBusy(false);
        }
    });

    const initialHashStep = getStepFromHash();
    currentStep = initialHashStep !== null ? initialHashStep : 0;
    showStep(currentStep, { updateUrl: initialHashStep === null });

    updatePromoPreview();
    updatePriceFieldsVisibility();
    updateVisibilityLabel();
    updateEmptyStateAndAddButtons();
    syncSectionTitleInputFromMap();

    if (currentCourseId > 0) {
        loadCourseForEdit(currentCourseId);
    }
});

