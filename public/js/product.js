document.addEventListener('DOMContentLoaded', () => {
    const productContainer = document.querySelector('.product-container');
    if (!productContainer) return;

    const basePrice = parseFloat(productContainer.dataset.basePrice || '0');
    const stock = parseInt(productContainer.dataset.stock || '0', 10);
    const placeholderImage = productContainer.dataset.placeholder || '/images/placeholder_img.jpg';

    const mainImage = document.getElementById('productMainImage');
    const previewImage = document.getElementById('selectedImagePreview');
    const qtyInput = document.getElementById('quantity');
    const subtotalEl = document.getElementById('subtotal');
    const cartQty = document.getElementById('cartQuantity');
    const buyQty = document.getElementById('buyNowQuantity');

    // Image thumbnail functionality
    document.querySelectorAll('.thumbnail-strip .thumbnail').forEach(thumb => {
        thumb.addEventListener('click', function() {
            document.querySelectorAll('.thumbnail-strip .thumbnail.active').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const newSrc = this.querySelector('img').src;
            if (mainImage) mainImage.src = newSrc;
            if (previewImage) previewImage.src = newSrc;
        });
    });

    // Tab switching functionality
    const tabButtons = Array.from(document.querySelectorAll('.tab-buttons .tab-toggle'));
    const tabPanes = Array.from(document.querySelectorAll('.tab-pane-content'));

    if (tabButtons.length && tabPanes.length) {
        const setActiveTab = (tabId) => {
            tabButtons.forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-tab') === tabId);
            });

            tabPanes.forEach(pane => {
                const isActive = pane.id === `tab-${tabId}`;
                pane.classList.toggle('d-none', !isActive);
                if (isActive) {
                    pane.removeAttribute('hidden');
                } else {
                    pane.setAttribute('hidden', 'hidden');
                }
            });
        };

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetTab = button.getAttribute('data-tab');
                if (!targetTab) {
                    return;
                }
                setActiveTab(targetTab);
            });
        });

        const initiallyActive = tabButtons.find(btn => btn.classList.contains('active')) || tabButtons[0];
        if (initiallyActive) {
            setActiveTab(initiallyActive.getAttribute('data-tab'));
        }
    }

    // Variant selection handling without page refresh
    const variantOptionElements = Array.from(document.querySelectorAll('.variant-option'));
    const summaryTargets = Array.from(document.querySelectorAll('.variant-summary-text'));
    const variantBlocks = Array.from(document.querySelectorAll('.variant-group-block'));
    const cartVariantField = document.getElementById('cartVariantsPayload');
    const buyVariantField = document.getElementById('buyNowVariantsPayload');

    const getActiveVariantSelections = () => {
        const selections = {};

        variantBlocks.forEach(block => {
            const groupContainer = block.querySelector('.variant-group');
            const activeOption = block.querySelector('.variant-option.active');
            if (!groupContainer || !activeOption) {
                return;
            }

            const groupKey = groupContainer.dataset.group;
            const optionValue = activeOption.dataset.value;

            if (groupKey && optionValue) {
                selections[groupKey] = optionValue;
            }
        });

        return selections;
    };

    const buildSummaryText = () => {
        const parts = [];

        variantBlocks.forEach(block => {
            const titleEl = block.querySelector('.variant-group-title');
            const activeOption = block.querySelector('.variant-option.active');
            if (titleEl && activeOption) {
                const label = titleEl.textContent.trim();
                const value = activeOption.textContent.trim();
                if (label && value) {
                    parts.push(`${label}: ${value}`);
                }
            }
        });

        return parts.length ? parts.join(' · ') : 'Pilih varian';
    };

    const updateSummaryDisplays = () => {
        const summaryText = buildSummaryText();
        summaryTargets.forEach(target => {
            target.textContent = summaryText;
        });
    };

    const updateVariantPayloadFields = () => {
        const selections = getActiveVariantSelections();
        const serialized = JSON.stringify(selections);

        if (cartVariantField) {
            cartVariantField.value = serialized;
        }

        if (buyVariantField) {
            buyVariantField.value = serialized;
        }

        return selections;
    };

    const updateVariantQueryParams = () => {
        const url = new URL(window.location.href);
        const activeSelections = updateVariantPayloadFields();

        Object.entries(activeSelections).forEach(([groupKey, optionValue]) => {
            url.searchParams.set(`variant[${groupKey}]`, optionValue);
        });

        // Remove stale variant selections from URL
        const toRemove = [];
        url.searchParams.forEach((value, key) => {
            const match = key.match(/^variant\[(.+)\]$/);
            if (match) {
                const groupKey = match[1];
                if (!Object.prototype.hasOwnProperty.call(activeSelections, groupKey)) {
                    toRemove.push(key);
                }
            }
        });
        toRemove.forEach(key => url.searchParams.delete(key));

        window.history.replaceState({}, '', url.toString());
    };

    const handleVariantSelection = (event, optionEl) => {
        event.preventDefault();

        const groupContainer = optionEl.closest('.variant-group');
        if (!groupContainer) {
            return;
        }

        groupContainer.querySelectorAll('.variant-option.active').forEach(active => {
            active.classList.remove('active');
        });

        optionEl.classList.add('active');
        updateSummaryDisplays();
        updateVariantQueryParams();
    };

    if (variantOptionElements.length) {
        variantOptionElements.forEach(optionEl => {
            optionEl.addEventListener('click', (event) => handleVariantSelection(event, optionEl));
            optionEl.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    handleVariantSelection(event, optionEl);
                }
            });
        });

        // Ensure summaries are accurate on initial load
        updateSummaryDisplays();
        updateVariantQueryParams();
    }

    // Quantity control functionality
    const syncQuantityFields = () => {
        if (!qtyInput) return;
        const value = qtyInput.value;
        if (cartQty) cartQty.value = value;
        if (buyQty) buyQty.value = value;
    };

    const updateSubtotal = () => {
        if (qtyInput && subtotalEl) {
            const quantity = parseInt(qtyInput.value, 10) || 1;
            const newSubtotal = basePrice * quantity;
            subtotalEl.textContent = `Rp ${newSubtotal.toLocaleString('id-ID')}`;
        }
        syncQuantityFields();
    };

    document.querySelectorAll('.qty-btn').forEach(button => {
        button.addEventListener('click', function() {
            if (!qtyInput) return;
            let quantity = parseInt(qtyInput.value, 10);
            const action = this.dataset.action;

            if (action === 'plus') {
                if (quantity < stock) {
                    quantity++;
                }
            } else if (action === 'minus') {
                if (quantity > 1) {
                    quantity--;
                }
            }
            qtyInput.value = quantity;
            updateSubtotal();
        });
    });

    // Fallback for images that fail to load
    document.querySelectorAll('img').forEach(img => {
        img.addEventListener('error', function() {
            this.src = placeholderImage;
        });
    });

    // Initial call to set subtotal
    updateSubtotal();
});
