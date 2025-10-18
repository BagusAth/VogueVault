document.addEventListener('DOMContentLoaded', () => {
	const formCard = document.getElementById('categoryFormCard');
	const toggleButtons = document.querySelectorAll('[data-action="toggle-form"]');
	const cancelButtons = document.querySelectorAll('[data-action="cancel-create"]');
	const deleteButtons = document.querySelectorAll('[data-action="delete-category"]');
	const alerts = document.querySelectorAll('.alert');
	const deleteModal = document.getElementById('confirmDeleteModal');
	const modalCategoryLabel = deleteModal?.querySelector('[data-modal-category]');
	const confirmDeleteBtn = deleteModal?.querySelector('[data-action="confirm-delete"]');
	const cancelDeleteBtn = deleteModal?.querySelector('[data-action="cancel-delete"]');
	let pendingDeleteForm = null;
	let modalClosingTimeout;
	let lastDeleteTrigger = null;

	const dismissAlert = (alert) => {
		if (!alert || alert.classList.contains('is-exiting')) {
			return;
		}

		alert.classList.remove('is-entering');
		alert.classList.add('is-exiting');

		const removeAlert = () => {
			alert.removeEventListener('animationend', removeAlert);
			alert.remove();
		};

		alert.addEventListener('animationend', removeAlert, { once: true });
		setTimeout(removeAlert, 400);
	};

	alerts.forEach((alert) => {
		requestAnimationFrame(() => {
			alert.classList.add('is-entering');
		});

		const autoDismiss = parseInt(alert.dataset.autoDismiss || '0', 10);
		if (autoDismiss > 0) {
			setTimeout(() => dismissAlert(alert), autoDismiss);
		}

		const closeBtn = alert.querySelector('[data-action="dismiss-alert"]');
		if (closeBtn) {
			closeBtn.addEventListener('click', () => dismissAlert(alert));
		}
	});

	const showForm = () => {
		if (formCard) {
			formCard.classList.remove('is-hidden');
			const nameInput = formCard.querySelector('input[name="name"]');
			if (nameInput) {
				nameInput.focus();
			}
		}
	};

	const hideForm = () => {
		if (formCard) {
			formCard.classList.add('is-hidden');
			const form = formCard.querySelector('form');
			if (form) {
				form.reset();
			}
		}
	};

	toggleButtons.forEach((button) => {
		button.addEventListener('click', () => {
			if (formCard?.classList.contains('is-hidden')) {
				showForm();
			} else {
				hideForm();
			}
		});
	});

	cancelButtons.forEach((button) => {
		button.addEventListener('click', hideForm);
	});

	deleteButtons.forEach((button) => {
		button.addEventListener('click', (event) => {
			event.preventDefault();
			const categoryName = button.getAttribute('data-category-name') || 'this category';
			const targetFormId = button.getAttribute('data-target-form');
			const form = targetFormId ? document.getElementById(targetFormId) : button.closest('form');

			if (!form || !deleteModal) {
				return;
			}

			if (modalClosingTimeout) {
				clearTimeout(modalClosingTimeout);
				modalClosingTimeout = undefined;
			}

			lastDeleteTrigger = button;

			pendingDeleteForm = form;
			if (modalCategoryLabel) {
				modalCategoryLabel.textContent = categoryName;
			}

			deleteModal.classList.remove('is-hiding');
			deleteModal.classList.add('is-visible');
			deleteModal.setAttribute('aria-hidden', 'false');
			document.body.classList.add('modal-open');

			requestAnimationFrame(() => {
				confirmDeleteBtn?.focus();
			});
		});
	});

	const closeDeleteModal = () => {
		if (!deleteModal || !deleteModal.classList.contains('is-visible')) {
			return;
		}

		deleteModal.classList.remove('is-visible');
		deleteModal.classList.add('is-hiding');
		deleteModal.setAttribute('aria-hidden', 'true');
		document.body.classList.remove('modal-open');

		modalClosingTimeout = window.setTimeout(() => {
			deleteModal.classList.remove('is-hiding');
			modalClosingTimeout = undefined;
		}, 320);

		pendingDeleteForm = null;

		if (lastDeleteTrigger) {
			const triggerToFocus = lastDeleteTrigger;
			lastDeleteTrigger = null;
			requestAnimationFrame(() => {
				if (typeof triggerToFocus.focus === 'function') {
					triggerToFocus.focus();
				}
			});
		}
	};

	confirmDeleteBtn?.addEventListener('click', () => {
		const formToSubmit = pendingDeleteForm;
		closeDeleteModal();
		if (formToSubmit) {
			formToSubmit.submit();
		}
	});

	cancelDeleteBtn?.addEventListener('click', () => {
		closeDeleteModal();
	});

	deleteModal?.addEventListener('click', (event) => {
		if (event.target === deleteModal) {
			closeDeleteModal();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && deleteModal?.classList.contains('is-visible')) {
			event.preventDefault();
			closeDeleteModal();
		}
	});
});
