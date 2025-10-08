document.addEventListener('DOMContentLoaded', () => {
    const specificationContainer = document.getElementById('specification-fields');
    const addSpecificationBtn = document.getElementById('add-specification');
    const variantContainer = document.getElementById('variant-fields');
    const addVariantBtn = document.getElementById('add-variant');

    const fileInput = document.getElementById('images');
    const dropArea = document.getElementById('image-drop-area');
    const browseButton = document.getElementById('browse-images');
    const previewList = document.getElementById('image-preview-list');
    const limitNotice = document.getElementById('image-limit-notice');
    const maxFiles = fileInput ? parseInt(fileInput.dataset.maxFiles || '5', 10) : 5;

    let filesBuffer = [];

    const renderPreviews = () => {
        if (!previewList) return;

        previewList.innerHTML = '';

        if (!filesBuffer.length) {
            const placeholder = document.createElement('p');
            placeholder.className = 'preview-placeholder';
            placeholder.textContent = 'Belum ada gambar yang dipilih.';
            previewList.appendChild(placeholder);
            return;
        }

        filesBuffer.forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'preview-card';

            const img = document.createElement('img');
            const reader = new FileReader();
            reader.onload = (event) => {
                img.src = event.target?.result || '';
            };
            reader.readAsDataURL(file);

            const meta = document.createElement('div');
            meta.className = 'preview-card__meta';
            const sizeKb = Math.round(file.size / 1024);
            meta.textContent = `${file.name} · ${sizeKb} KB`;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'preview-card__remove';
            removeBtn.setAttribute('aria-label', 'Hapus gambar');
            removeBtn.innerHTML = '<i class="bi bi-x"></i>';
            removeBtn.addEventListener('click', () => {
                filesBuffer.splice(index, 1);
                syncInputFiles();
                renderPreviews();
                updateLimitNotice();
            });

            card.appendChild(img);
            card.appendChild(meta);
            card.appendChild(removeBtn);
            previewList.appendChild(card);
        });
    };

    const syncInputFiles = () => {
        if (!fileInput) return;
        const dataTransfer = new DataTransfer();
        filesBuffer.forEach((file) => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
    };

    const updateLimitNotice = (overflowCount = 0) => {
        if (!limitNotice) return;

        if (overflowCount > 0) {
            limitNotice.textContent = `Maksimal ${maxFiles} gambar. ${overflowCount} gambar tidak ditambahkan.`;
            return;
        }

        if (filesBuffer.length >= maxFiles && filesBuffer.length > 0) {
            limitNotice.textContent = `Maksimal ${maxFiles} gambar telah dipilih.`;
        } else if (!filesBuffer.length) {
            limitNotice.textContent = '';
        } else {
            limitNotice.textContent = `${maxFiles - filesBuffer.length} slot gambar masih tersedia.`;
        }
    };

    const applyFiles = (incomingFiles, { append = false } = {}) => {
        if (!incomingFiles?.length) {
            if (!append) {
                filesBuffer = [];
                syncInputFiles();
                renderPreviews();
                updateLimitNotice();
            }
            return;
        }

        const validFiles = Array.from(incomingFiles).filter((file) => file.type.startsWith('image/'));
        const baseLength = append ? filesBuffer.length : 0;
        if (!append) {
            filesBuffer = [];
        }

        validFiles.forEach((file) => {
            if (filesBuffer.length < maxFiles) {
                filesBuffer.push(file);
            }
        });

        const overflowCount = Math.max(baseLength + validFiles.length - maxFiles, 0);
        syncInputFiles();
        renderPreviews();
        updateLimitNotice(overflowCount);
    };

    dropArea?.addEventListener('dragenter', (event) => {
        event.preventDefault();
        dropArea.classList.add('dragover');
    });

    dropArea?.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropArea.classList.add('dragover');
    });

    dropArea?.addEventListener('dragleave', () => {
        dropArea.classList.remove('dragover');
    });

    dropArea?.addEventListener('drop', (event) => {
        event.preventDefault();
        dropArea.classList.remove('dragover');
        applyFiles(event.dataTransfer?.files, { append: true });
    });

    dropArea?.addEventListener('click', () => fileInput?.click());
    browseButton?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        fileInput?.click();
    });

    fileInput?.addEventListener('change', (event) => {
        applyFiles(event.target.files, { append: false });
    });

    renderPreviews();
    updateLimitNotice();

    const createSpecificationRow = (key = '', value = '') => {
        const row = document.createElement('div');
        row.className = 'attribute-row';

        const keyInput = document.createElement('input');
        keyInput.type = 'text';
        keyInput.name = 'specification_keys[]';
        keyInput.placeholder = 'Spesifikasi (cth: Material)';
        keyInput.value = key;

        const valueInput = document.createElement('input');
        valueInput.type = 'text';
        valueInput.name = 'specification_values[]';
        valueInput.placeholder = 'Nilai (cth: Katun)';
        valueInput.value = value;

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'attribute-remove';
        removeBtn.setAttribute('aria-label', 'Remove specification');
        removeBtn.innerHTML = '<i class="bi bi-x"></i>';

        removeBtn.addEventListener('click', () => {
            const container = row.parentElement;
            if (!container) {
                return;
            }

            if (container.children.length > 1) {
                container.removeChild(row);
            } else {
                keyInput.value = '';
                valueInput.value = '';
            }
        });

        row.appendChild(keyInput);
        row.appendChild(valueInput);
        row.appendChild(removeBtn);
        return row;
    };

    const createVariantRow = (key = '', value = '') => {
        const row = document.createElement('div');
        row.className = 'attribute-row';

        const keyInput = document.createElement('input');
        keyInput.type = 'text';
        keyInput.name = 'variant_keys[]';
        keyInput.placeholder = 'Varian (cth: Size)';
        keyInput.value = key;

        const valueInput = document.createElement('input');
        valueInput.type = 'text';
        valueInput.name = 'variant_values[]';
        valueInput.placeholder = 'Daftar opsi (cth: S, M, L)';
        valueInput.value = value;

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'attribute-remove';
        removeBtn.setAttribute('aria-label', 'Remove variant');
        removeBtn.innerHTML = '<i class="bi bi-x"></i>';

        removeBtn.addEventListener('click', () => {
            const container = row.parentElement;
            if (!container) {
                return;
            }

            if (container.children.length > 1) {
                container.removeChild(row);
            } else {
                keyInput.value = '';
                valueInput.value = '';
            }
        });

        row.appendChild(keyInput);
        row.appendChild(valueInput);
        row.appendChild(removeBtn);
        return row;
    };

    if (specificationContainer && specificationContainer.children.length === 0) {
        specificationContainer.appendChild(createSpecificationRow());
    }

    if (variantContainer && variantContainer.children.length === 0) {
        variantContainer.appendChild(createVariantRow());
    }

    addSpecificationBtn?.addEventListener('click', () => {
        specificationContainer.appendChild(createSpecificationRow());
    });

    addVariantBtn?.addEventListener('click', () => {
        variantContainer.appendChild(createVariantRow());
    });
});
