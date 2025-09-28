const productContainer = document.querySelector('.product-container');
const variantButtons = document.querySelectorAll('.variant-option');
const variantLabel = document.getElementById('variantLabel');
const selectedVariant = document.getElementById('selectedVariant');
const thumbnails = document.querySelectorAll('.thumbnail');
const mainImage = document.getElementById('productMainImage');
const previewImage = document.getElementById('selectedImagePreview');
const qtyInput = document.getElementById('quantity');
const subtotal = document.getElementById('subtotal');

const basePrice = productContainer ? parseFloat(productContainer.dataset.basePrice || '0') : 0;
const placeholderImage = productContainer ? productContainer.dataset.placeholder : '/images/placeholder_img.jpg';

if (mainImage) {
    mainImage.addEventListener('error', () => {
        mainImage.src = placeholderImage;
    });
}

if (previewImage) {
    previewImage.addEventListener('error', () => {
        previewImage.src = placeholderImage;
    });
}

variantButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
        variantButtons.forEach((item) => item.classList.remove('active'));
        btn.classList.add('active');
        if (variantLabel) {
            variantLabel.textContent = btn.dataset.variant;
        }
        if (selectedVariant) {
            selectedVariant.textContent = btn.dataset.variant;
        }
    });
});

thumbnails.forEach((thumb) => {
    thumb.addEventListener('click', () => {
        thumbnails.forEach((item) => item.classList.remove('active'));
        thumb.classList.add('active');
        const img = thumb.querySelector('img');
        if (img && mainImage) {
            mainImage.src = img.src;
        }
        if (img && previewImage) {
            previewImage.src = img.src;
        }
    });
});

document.querySelectorAll('.qty-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
        if (!qtyInput) return;

        let value = parseInt(qtyInput.value, 10);
        if (btn.dataset.action === 'plus') {
            value += 1;
        } else if (btn.dataset.action === 'minus' && value > 1) {
            value -= 1;
        }
        qtyInput.value = value;
        updateSubtotal(value);
    });
});

function updateSubtotal(quantity) {
    if (!subtotal) return;

    const total = basePrice * quantity;
    const fractionDigits = Number.isInteger(basePrice) ? 0 : 2;
    const formatted = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: fractionDigits,
        maximumFractionDigits: fractionDigits,
    }).format(total);
    subtotal.textContent = `Rp ${formatted}`;
}

updateSubtotal(parseInt(qtyInput?.value || '1', 10));

const tabButtons = document.querySelectorAll('[data-tab]');
tabButtons.forEach((button) => {
    button.addEventListener('click', () => {
        tabButtons.forEach((btn) => btn.classList.remove('active'));
        button.classList.add('active');

        document.querySelectorAll('.tab-pane-content').forEach((pane) => pane.classList.add('d-none'));
        const target = document.getElementById(`tab-${button.dataset.tab}`);
        if (target) {
            target.classList.remove('d-none');
        }
    });
});
