// Provider Management page frontend interactions

(function () {
	'use strict';

	var page = window.location.search || '';
	if (page.indexOf('page=providermanagement') === -1) {
		return;
	}

	var actionButtons = document.querySelectorAll('.pm-action-btn');
	if (!actionButtons.length) {
		return;
	}

	var reviewModal = document.getElementById('pmReviewModal');
	var closeModalBtn = document.getElementById('pmCloseModalBtn');
	var modalApproveBtn = document.getElementById('pmModalApproveBtn');
	var modalRejectBtn = document.getElementById('pmModalRejectBtn');
	var selectedRow = null;

	function setText(id, value) {
		var node = document.getElementById(id);
		if (node) {
			node.textContent = value || '-';
		}
	}

	function openModalForRow(row) {
		if (!reviewModal || !row) {
			return;
		}
		selectedRow = row;
		var reviewBtn = row.querySelector('.pm-review[data-approval-request-id]');
		var approvalRequestId = reviewBtn ? reviewBtn.getAttribute('data-approval-request-id') : '0';
		setText('pmProviderName', row.getAttribute('data-provider'));
		setText('pmProviderEmail', row.getAttribute('data-email'));
		setText('pmProviderSpecialization', row.getAttribute('data-specialization'));
		setText('pmProviderApplied', row.getAttribute('data-applied'));
		setText('pmProviderExperience', row.getAttribute('data-experience'));
		setText('pmProviderDocs', row.getAttribute('data-docs'));
		setText('pmProviderNote', row.getAttribute('data-note'));

		var modalRejectRequestId = document.getElementById('pmModalRejectRequestId');
		var modalApproveRequestId = document.getElementById('pmModalApproveRequestId');
		if (modalRejectRequestId) {
			modalRejectRequestId.value = approvalRequestId || '0';
		}
		if (modalApproveRequestId) {
			modalApproveRequestId.value = approvalRequestId || '0';
		}

		reviewModal.classList.add('open');
		reviewModal.setAttribute('aria-hidden', 'false');
	}

	function closeModal() {
		if (!reviewModal) {
			return;
		}
		reviewModal.classList.remove('open');
		reviewModal.setAttribute('aria-hidden', 'true');
		selectedRow = null;
	}

	function applyDecision(row, action) {
		if (!row) {
			return;
		}

		row.classList.remove('pm-row-reviewed', 'pm-row-approved', 'pm-row-rejected');
		var statusCell = row.querySelector('.status-pending, .status-active, .status-inactive');

		if (action === 'review') {
			row.classList.add('pm-row-reviewed');
			showToast('Application opened for review');
			return;
		}

		if (action === 'approve' || action === 'reject') {
			if (statusCell) {
				statusCell.textContent = action === 'approve' ? 'Approving...' : 'Rejecting...';
			}
			showToast('Submitting decision...');
		}
	}

	function showToast(message) {
		var toast = document.createElement('div');
		toast.className = 'pm-toast';
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
		}, 1700);
	}

	actionButtons.forEach(function (btn) {
		btn.addEventListener('click', function () {
			var action = btn.getAttribute('data-action');
			var row = btn.closest('tr');
			if (!row) {
				return;
			}

			if (action === 'review') {
				applyDecision(row, 'review');
				openModalForRow(row);
				return;
			}

			applyDecision(row, action);
		});
	});

	if (closeModalBtn) {
		closeModalBtn.addEventListener('click', closeModal);
	}

	if (reviewModal) {
		reviewModal.addEventListener('click', function (event) {
			if (event.target && event.target.getAttribute('data-close') === 'true') {
				closeModal();
			}
		});
	}

	if (modalApproveBtn) {
		modalApproveBtn.addEventListener('click', function () {
			applyDecision(selectedRow, 'approve');
		});
	}

	if (modalRejectBtn) {
		modalRejectBtn.addEventListener('click', function () {
			applyDecision(selectedRow, 'reject');
		});
	}

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape' && reviewModal && reviewModal.classList.contains('open')) {
			closeModal();
		}
	});
})();
