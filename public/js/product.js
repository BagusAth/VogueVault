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
    const tabButtons = Array.from(document.querySelectorAll('.tab-buttons [data-tab]'));
    if (tabButtons.length) {
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                tabButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                document.querySelectorAll('.tab-pane-content').forEach(pane => {
                    pane.classList.add('d-none');
                });

                const activePane = document.getElementById(`tab-${tabId}`);
                if (activePane) {
                    activePane.classList.remove('d-none');
                }
            });
        });
    }

    // Quantity control functionality
    const updateSubtotal = () => {
        if (!qtyInput || !subtotalEl) return;
        const quantity = parseInt(qtyInput.value, 10);
        const newSubtotal = basePrice * quantity;
        subtotalEl.textContent = `Rp ${newSubtotal.toLocaleString('id-ID')}`;
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
