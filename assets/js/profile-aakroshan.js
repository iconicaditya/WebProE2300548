// Admin Officer Profile page interactions

(function () {
	'use strict';
	var STORAGE_KEY = 'aoProfileFrontendStateV1';

	var root = document.getElementById('aoProfilePage');
	if (!root) {
		return;
	}

	var form = document.getElementById('aoProfileForm');
	var cancelBtn = document.getElementById('aoCancelBtn');
	var saveBtn = document.getElementById('aoSaveBtn');
	var avatarBtn = document.querySelector('.ao-avatar-edit-btn');
	var changePasswordBtn = document.getElementById('aoChangePasswordBtn');
	var twoFaToggleBtn = document.getElementById('aoTwoFaToggleBtn');
	var manageSessionsBtn = document.getElementById('aoManageSessionsBtn');
	var passwordMeta = document.getElementById('aoPasswordMeta');
	var twoFaMeta = document.getElementById('aoTwoFaMeta');
	var sessionMeta = document.getElementById('aoSessionMeta');
	var fullNameInput = document.getElementById('aoFullName');
	var avatar = document.getElementById('aoProfileAvatar');
	var navbarName = document.querySelector('.profile-name');
	var fields = form ? form.querySelectorAll('input, textarea') : [];

	var originalValues = {};
	fields.forEach(function (input) {
		originalValues[input.name] = input.value;
	});

	var securityState = {
		twoFaEnabled: true,
		sessionCount: 2,
		passwordMeta: passwordMeta ? passwordMeta.textContent : 'Last changed 28 days ago'
	};

	function getInitials(name) {
		var cleaned = (name || '').trim();
		if (!cleaned) {
			return 'AO';
		}
		var parts = cleaned.split(/\s+/).slice(0, 2);
		return parts.map(function (part) { return part.charAt(0).toUpperCase(); }).join('');
	}

	function updateIdentityUI() {
		var nameValue = fullNameInput ? fullNameInput.value.trim() : 'Admin Officer';
		if (avatar) {
			avatar.textContent = getInitials(nameValue);
		}
		if (navbarName) {
			navbarName.textContent = nameValue || 'Admin Officer';
		}
	}

	function serializeState() {
		var values = {};
		fields.forEach(function (field) {
			if (!field.name) {
				return;
			}
			if (field.type === 'checkbox') {
				values[field.name] = field.checked;
			} else {
				values[field.name] = field.value;
			}
		});

		return {
			values: values,
			security: securityState
		};
	}

	function applySerializedState(data) {
		if (!data || !data.values) {
			return;
		}

		fields.forEach(function (field) {
			if (!field.name || !Object.prototype.hasOwnProperty.call(data.values, field.name)) {
				return;
			}
			if (field.type === 'checkbox') {
				field.checked = Boolean(data.values[field.name]);
			} else {
				field.value = data.values[field.name];
			}
		});

		if (data.security) {
			securityState = data.security;
		}
		applySecurityState();
		updateIdentityUI();
	}

	function applySecurityState() {
		if (twoFaToggleBtn) {
			twoFaToggleBtn.textContent = securityState.twoFaEnabled ? 'Enabled' : 'Disabled';
			twoFaToggleBtn.classList.toggle('ao-chip-danger', !securityState.twoFaEnabled);
			twoFaToggleBtn.setAttribute('aria-pressed', securityState.twoFaEnabled ? 'true' : 'false');
		}
		if (twoFaMeta) {
			twoFaMeta.textContent = securityState.twoFaEnabled ? 'Currently enabled' : 'Currently disabled';
		}
		if (sessionMeta) {
			sessionMeta.textContent = securityState.sessionCount + ' device' + (securityState.sessionCount === 1 ? '' : 's') + ' connected';
		}
		if (passwordMeta) {
			passwordMeta.textContent = securityState.passwordMeta;
		}
	}

	function persistState() {
		try {
			window.localStorage.setItem(STORAGE_KEY, JSON.stringify(serializeState()));
		} catch (e) {
			// Ignore storage failures in strict/private browser modes.
		}
	}

	function restoreState() {
		try {
			var raw = window.localStorage.getItem(STORAGE_KEY);
			if (!raw) {
				updateIdentityUI();
				applySecurityState();
				return;
			}
			applySerializedState(JSON.parse(raw));
		} catch (e) {
			updateIdentityUI();
			applySecurityState();
		}
	}

	function isValidEmail(email) {
		return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
	}

	function isValidPhone(phone) {
		return /^[0-9+\-()\s]{7,20}$/.test(phone);
	}

	function validateForm() {
		var email = document.getElementById('aoEmail');
		var phone = document.getElementById('aoPhone');
		if (email && !isValidEmail(email.value.trim())) {
			showToast('Please enter a valid email address');
			email.focus();
			return false;
		}
		if (phone && !isValidPhone(phone.value.trim())) {
			showToast('Please enter a valid phone number');
			phone.focus();
			return false;
		}
		return true;
	}

	function showToast(message) {
		var toast = document.createElement('div');
		toast.className = 'ao-toast';
		toast.textContent = message;
		document.body.appendChild(toast);

		requestAnimationFrame(function () {
			toast.classList.add('show');
		});

		setTimeout(function () {
			toast.classList.remove('show');
			setTimeout(function () {
				if (toast.parentNode) {
					toast.parentNode.removeChild(toast);
				}
			}, 220);
		}, 1800);
	}

	if (avatarBtn) {
		avatarBtn.addEventListener('click', function () {
			showToast('Profile photo upload will be connected in backend phase');
		});
	}

	if (fullNameInput) {
		fullNameInput.addEventListener('input', updateIdentityUI);
	}

	if (cancelBtn) {
		cancelBtn.addEventListener('click', function () {
			fields.forEach(function (field) {
				if (Object.prototype.hasOwnProperty.call(originalValues, field.name)) {
					if (field.type === 'checkbox') {
						field.checked = Boolean(originalValues[field.name]);
					} else {
						field.value = originalValues[field.name];
					}
				}
			});
			applySecurityState();
			updateIdentityUI();
			showToast('Changes discarded');
		});
	}

	if (form) {
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			if (!validateForm()) {
				return;
			}
			fields.forEach(function (field) {
				if (field.type === 'checkbox') {
					originalValues[field.name] = field.checked;
				} else {
					originalValues[field.name] = field.value;
				}
			});
			persistState();
			showToast('Profile changes saved');
		});
	}

	if (saveBtn) {
		saveBtn.addEventListener('click', function () {
			if (form) {
				form.requestSubmit();
			}
		});
	}

	if (changePasswordBtn) {
		changePasswordBtn.addEventListener('click', function () {
			securityState.passwordMeta = 'Last changed just now (frontend demo)';
			applySecurityState();
			persistState();
			showToast('Password updated in frontend demo mode');
		});
	}

	if (twoFaToggleBtn) {
		twoFaToggleBtn.addEventListener('click', function () {
			securityState.twoFaEnabled = !securityState.twoFaEnabled;
			applySecurityState();
			persistState();
			showToast('Two-factor authentication ' + (securityState.twoFaEnabled ? 'enabled' : 'disabled'));
		});
	}

	if (manageSessionsBtn) {
		manageSessionsBtn.addEventListener('click', function () {
			securityState.sessionCount = securityState.sessionCount > 1 ? 1 : 2;
			applySecurityState();
			persistState();
			showToast('Active sessions refreshed');
		});
	}

	restoreState();
})();
