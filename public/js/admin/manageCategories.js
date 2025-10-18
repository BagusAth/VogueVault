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
	const dropzones = document.querySelectorAll('[data-dropzone]');
	let pendingDeleteForm = null;
	let modalClosingTimeout;
	let lastDeleteTrigger = null;

	// Handle drag & drop image uploads for create / edit forms
	dropzones.forEach((zone) => {
		const input = zone.querySelector('[data-dropzone-input]');
		const trigger = zone.querySelector('[data-dropzone-trigger]');
		const preview = zone.querySelector('[data-dropzone-preview]');
		const previewImg = zone.querySelector('[data-dropzone-preview-img]');
		const fileNameLabel = zone.querySelector('[data-dropzone-filename]');
		const removeBtn = zone.querySelector('[data-dropzone-remove]');
		let objectUrl;

		if (!input) {
			return;
		}

		const clearPreview = () => {
			if (objectUrl) {
				URL.revokeObjectURL(objectUrl);
				objectUrl = undefined;
			}
			zone.classList.remove('has-file');
			if (preview) {
				preview.hidden = true;
			}
			if (previewImg) {
				previewImg.removeAttribute('src');
			}
			if (fileNameLabel) {
				fileNameLabel.textContent = 'No file selected';
			}
			input.value = '';
		};

		const assignFile = (file, originalList) => {
			if (!file || !file.type?.startsWith('image/')) {
				clearPreview();
				return;
			}

			let assigned = false;
			if (typeof DataTransfer !== 'undefined') {
				try {
					const dataTransfer = new DataTransfer();
					dataTransfer.items.add(file);
					input.files = dataTransfer.files;
					assigned = true;
				} catch (error) {
					assigned = false;
				}
			}
			if (!assigned && originalList) {
				try {
					input.files = originalList;
					assigned = true;
				} catch (error) {
					assigned = false;
				}
			}
			if (!assigned) {
				clearPreview();
				return;
			}

			zone.classList.add('has-file');
			if (preview) {
				preview.hidden = false;
			}
			if (fileNameLabel) {
				fileNameLabel.textContent = file.name;
			}
			if (previewImg) {
				if (objectUrl) {
					URL.revokeObjectURL(objectUrl);
				}
				objectUrl = URL.createObjectURL(file);
				previewImg.src = objectUrl;
			}
		};

		const handleFiles = (files, originalList) => {
			if (files && files.length > 0) {
				assignFile(files[0], originalList ?? files);
			}
		};

		trigger?.addEventListener('click', () => {
			input.click();
		});

		input.addEventListener('change', () => {
			handleFiles(input.files);
		});

		removeBtn?.addEventListener('click', (event) => {
			event.preventDefault();
			clearPreview();
		});

		const endDrag = () => zone.classList.remove('is-dragging');

		['dragenter', 'dragover'].forEach((type) => {
			zone.addEventListener(type, (event) => {
				event.preventDefault();
				zone.classList.add('is-dragging');
			});
		});

		['dragleave', 'dragend'].forEach((type) => {
			zone.addEventListener(type, (event) => {
				if (event.currentTarget !== zone) {
					return;
				}
				if (event.type === 'dragleave') {
					const nextTarget = event.relatedTarget;
					if (nextTarget && zone.contains(nextTarget)) {
						return;
					}
				}
				endDrag();
			});
		});

		zone.addEventListener('drop', (event) => {
			event.preventDefault();
			endDrag();
			const droppedFiles = event.dataTransfer?.files;
			handleFiles(droppedFiles, droppedFiles);
		});

		zone.addEventListener('paste', (event) => {
			const files = event.clipboardData?.files;
			if (files && files.length > 0) {
				event.preventDefault();
				handleFiles(files, files);
			}
		});

		const parentForm = zone.closest('form');
		if (parentForm) {
			parentForm.addEventListener('reset', () => {
				requestAnimationFrame(clearPreview);
			});
		}
	});

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
