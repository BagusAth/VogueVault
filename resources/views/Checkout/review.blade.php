<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - VogueVault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
</head>
<body>
    @include('partials.navbar')

    @php
        $placeholderImage = asset('images/placeholder_img.jpg');

        $formatCurrency = fn ($value) => 'Rp' . number_format($value, 0, ',', '.');

        $resolveProductImage = function ($product) use ($placeholderImage) {
            $rawImage = collect($product->images ?? [])->first();
            if (!$rawImage) {
                return $placeholderImage;
            }

            if (\Illuminate\Support\Str::startsWith($rawImage, ['http://', 'https://'])) {
                return $rawImage;
            }

            $cleanPath = ltrim($rawImage, '/');
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath)) {
                return asset('storage/' . $cleanPath);
            }

            if (file_exists(public_path($cleanPath))) {
                return asset($cleanPath);
            }

            return $placeholderImage;
        };
    @endphp

    <main class="checkout-page py-5">
        <div class="container-xl">
            <div class="checkout-intro">
                <div>
                    <p class="eyebrow">Final Step</p>
                    <h1>Review &amp; Confirm Your Order</h1>
                    <p class="lead">Pick your preferred address, make sure the order details look right, then choose the payment method that suits you best.</p>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger shadow-sm">
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4 align-items-start">
                <div class="col-12 col-xl-8">
                    <section class="card glass-card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="card-title">Shipping Address</h2>
                                <p class="text-muted small mb-0">Save multiple addresses and switch between them anytime.</p>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm {{ $errors->any() ? 'active' : '' }}" id="toggleAddressForm">
                                <i class="bi bi-plus-circle me-1"></i> Add New Address
                            </button>
                        </div>
                        <div class="card-body">
                            @if($addresses->isEmpty())
                                <div class="empty-state">
                                    <i class="bi bi-geo-alt"></i>
                                    <p class="mb-0">You don't have any saved addresses yet. Add your first shipping address.</p>
                                </div>
                            @else
                                <div class="address-list">
                                    @foreach($addresses as $address)
                                        <form action="{{ route('checkout.address.select') }}" method="POST" class="address-card {{ optional($activeAddress)->id === $address->id ? 'is-active' : '' }}">
                                            @csrf
                                            <input type="hidden" name="address_id" value="{{ $address->id }}">
                                            <header class="address-card__header">
                                                <div class="address-card__meta">
                                                    <span class="badge rounded-pill {{ $address->is_default ? 'text-bg-success' : 'text-bg-light' }}">
                                                        {{ $address->label ? e($address->label) : 'Address' }}
                                                    </span>
                                                    @if($address->is_default)
                                                        <span class="default-chip">Default</span>
                                                    @endif
                                                </div>
                                                <div class="address-card__tools">
                                                    <button type="button"
                                                        class="address-card__icon"
                                                        data-action="edit-address"
                                                        data-address-id="{{ $address->id }}"
                                                        data-address-label="{{ e($address->label ?? '') }}"
                                                        data-address-receiver="{{ e($address->receiver_name) }}"
                                                        data-address-phone="{{ e($address->phone) }}"
                                                        data-address-line="{{ e($address->address_line) }}"
                                                        data-address-city="{{ e($address->city) }}"
                                                        data-address-postal="{{ e($address->postal_code ?? '') }}"
                                                        data-address-default="{{ $address->is_default ? '1' : '0' }}"
                                                        title="Edit address">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="address-card__icon address-card__icon--danger"
                                                        data-action="delete-address"
                                                        data-delete-url="{{ route('checkout.address.delete', $address->id) }}"
                                                        data-address-name="{{ e($address->receiver_name) }}"
                                                        title="Delete address">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </header>
                                            <h3>{{ $address->receiver_name }}</h3>
                                            <p class="mb-1">{{ $address->phone }}</p>
                                            <p class="text-muted mb-3">{{ $address->address_line }}, {{ $address->city }}{{ $address->postal_code ? ', ' . $address->postal_code : '' }}</p>
                                            <div class="address-card__actions">
                                                <button type="submit" name="action" value="select" class="btn btn-primary btn-sm">
                                                    Use This Address
                                                </button>
                                                @if(!$address->is_default)
                                                    <button type="submit" name="action" value="make_default" class="btn btn-link btn-sm text-decoration-none">
                                                        Set as Default
                                                    </button>
                                                @endif
                                            </div>
                                        </form>
                                    @endforeach
                                </div>
                                <form id="deleteAddressForm" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif

                            <div class="address-form-wrapper collapse {{ $errors->any() ? 'show' : '' }}" id="newAddressForm">
                                <hr>
                                <h3 class="h5 mb-3" data-address-form-title>New Address</h3>
                                <form action="{{ route('checkout.address') }}" method="POST" class="address-form" id="addressForm">
                                    @csrf
                                    <input type="hidden" name="address_id" id="addressIdField" value="{{ old('address_id') }}">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Address Label <span class="text-muted">(Optional)</span></label>
                                            <input type="text" name="label" class="form-control" placeholder="Home, Office, etc." value="{{ old('label') }}">
                                            @error('label')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Recipient Name</label>
                                            <input type="text" name="receiver_name" class="form-control" value="{{ old('receiver_name') }}" required>
                                            @error('receiver_name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                                            @error('phone')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Full Address</label>
                                            <textarea name="address_line" rows="3" class="form-control" required>{{ old('address_line') }}</textarea>
                                            @error('address_line')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <input type="text" name="city" class="form-control" value="{{ old('city') }}" required>
                                            @error('city')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Postal Code</label>
                                            <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code') }}">
                                            @error('postal_code')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12 d-flex align-items-center">
                                            <input type="checkbox" class="form-check-input me-2" name="set_as_default" id="setAsDefault" {{ old('set_as_default') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="setAsDefault">Set as default address</label>
                                        </div>
                                        @error('set_as_default')
                                            <div class="col-12 text-danger small">{{ $message }}</div>
                                        @enderror
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary" data-address-submit>Save Address</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>

                    <section class="card glass-card">
                        <div class="card-header">
                            <h2 class="card-title mb-0">Order Details</h2>
                        </div>
                        <div class="card-body">
                            @if(isset($buyNow))
                                <article class="order-item">
                                    <img src="{{ $buyNow['image'] ?? $placeholderImage }}" alt="{{ $buyNow['product_name'] }}">
                                    <div>
                                        <h3>{{ $buyNow['product_name'] }}</h3>
                                        @if(!empty($buyNow['variant_summary']))
                                            <p class="text-muted mb-1">{{ $buyNow['variant_summary'] }}</p>
                                        @endif
                                        <p class="text-muted mb-1">Qty {{ $buyNow['quantity'] }} × {{ $formatCurrency($buyNow['price']) }}</p>
                                        <strong>{{ $formatCurrency($buyNow['subtotal']) }}</strong>
                                    </div>
                                </article>
                            @elseif($cart)
                                @foreach($cart->items as $item)
                                    @php
                                        $imageUrl = $resolveProductImage($item->product);
                                        $variants = $item->variant_labels ? implode(' · ', $item->variant_labels) : null;
                                    @endphp
                                    <article class="order-item">
                                        <img src="{{ $imageUrl }}" alt="{{ $item->product->name }}" onerror="this.onerror=null;this.src='{{ $placeholderImage }}';">
                                        <div>
                                            <h3>{{ $item->product->name }}</h3>
                                            @if($variants)
                                                <p class="text-muted mb-1">{{ $variants }}</p>
                                            @endif
                                            <p class="text-muted mb-1">Qty {{ $item->quantity }} × {{ $formatCurrency($item->unit_price) }}</p>
                                            <strong>{{ $formatCurrency($item->subtotal) }}</strong>
                                        </div>
                                    </article>
                                @endforeach
                            @endif
                        </div>
                    </section>
                </div>

                <aside class="col-12 col-xl-4">
                    <section class="card glass-card sticky-top summary-card">
                        <div class="card-body">
                            <h2 class="card-title mb-3">Payment Summary</h2>

                            <div class="shipping-summary mb-4">
                                <div class="shipping-header">
                                    <span class="badge rounded-pill text-bg-light">Active Address</span>
                                </div>
                                @if($activeAddress)
                                    <p class="mb-1 fw-semibold">{{ $activeAddress->receiver_name }}</p>
                                    <p class="mb-1 text-muted">{{ $activeAddress->phone }}</p>
                                    <p class="mb-0 text-muted">{{ $activeAddress->address_line }}, {{ $activeAddress->city }}{{ $activeAddress->postal_code ? ', ' . $activeAddress->postal_code : '' }}</p>
                                @else
                                    <p class="text-muted mb-0">Add a shipping address so we can deliver your order.</p>
                                @endif
                            </div>

                            <form action="{{ route('checkout.store') }}" method="POST" class="payment-form">
                                @csrf
                                <div class="payment-options mb-4">
                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="gopay" required>
                                        <span>
                                            <strong>GoPay</strong>
                                            <small>Your GoPay balance will be charged automatically</small>
                                        </span>
                                    </label>
                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="shopeepay">
                                        <span>
                                            <strong>ShopeePay</strong>
                                            <small>Scan or confirm via the Shopee app</small>
                                        </span>
                                    </label>
                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="qris">
                                        <span>
                                            <strong>QRIS</strong>
                                            <small>Pay through your preferred banking app</small>
                                        </span>
                                    </label>
                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="va">
                                        <span>
                                            <strong>Virtual Account</strong>
                                            <small>Complete payment via ATM or mobile banking</small>
                                        </span>
                                    </label>
                                </div>

                                <ul class="cost-breakdown list-unstyled mb-4">
                                    <li class="d-flex justify-content-between mb-2">
                                        <span>Subtotal</span>
                                        <span>{{ isset($buyNow) ? $formatCurrency($buyNow['subtotal']) : $formatCurrency($cart->subtotal) }}</span>
                                    </li>
                                    <li class="d-flex justify-content-between mb-2 text-muted">
                                        <span>Tax</span>
                                        <span>Calculated at payment</span>
                                    </li>
                                    <li class="d-flex justify-content-between mb-2 text-success">
                                        <span>Shipping</span>
                                        <span>Free</span>
                                    </li>
                                </ul>

                                <div class="total-line d-flex justify-content-between align-items-center mb-4">
                                    <span>Order Total</span>
                                    <strong>{{ isset($buyNow) ? $formatCurrency($buyNow['subtotal']) : $formatCurrency($cart->subtotal) }}</strong>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 btn-lg" {{ $activeAddress ? '' : 'disabled' }}>
                                    Continue to Payment
                                </button>
                            </form>
                            <p class="security-note text-muted small mt-3"><i class="bi bi-shield-check me-1"></i> Your payment is encrypted and secure. We never store your card or digital wallet details.</p>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const toggleButton = document.getElementById('toggleAddressForm');
        const addressFormWrapper = document.getElementById('newAddressForm');
        const addressForm = document.getElementById('addressForm');
        const addressFormTitle = document.querySelector('[data-address-form-title]');
        const submitButton = document.querySelector('[data-address-submit]');
        const addressIdField = document.getElementById('addressIdField');
        const labelField = addressForm?.querySelector('[name="label"]');
        const receiverField = addressForm?.querySelector('[name="receiver_name"]');
        const phoneField = addressForm?.querySelector('[name="phone"]');
        const addressLineField = addressForm?.querySelector('[name="address_line"]');
        const cityField = addressForm?.querySelector('[name="city"]');
        const postalField = addressForm?.querySelector('[name="postal_code"]');
        const defaultCheckbox = document.getElementById('setAsDefault');

        const initialState = {
            id: addressIdField?.value || '',
            label: labelField?.value || '',
            receiver: receiverField?.value || '',
            phone: phoneField?.value || '',
            address: addressLineField?.value || '',
            city: cityField?.value || '',
            postal: postalField?.value || '',
            isDefault: defaultCheckbox?.checked || false,
        };

        let isEditingAddress = Boolean(initialState.id);

        const setFormMode = (mode) => {
            isEditingAddress = mode === 'edit';
            if (addressFormTitle) {
                addressFormTitle.textContent = isEditingAddress ? 'Edit Address' : 'New Address';
            }
            if (submitButton) {
                submitButton.textContent = isEditingAddress ? 'Update Address' : 'Save Address';
            }
            if (toggleButton && addressFormWrapper?.classList.contains('show')) {
                toggleButton.classList.add('active');
                toggleButton.innerHTML = isEditingAddress
                    ? '<i class="bi bi-dash-circle me-1"></i> Close Editor'
                    : '<i class="bi bi-dash-circle me-1"></i> Close Address Form';
            }
        };

        const resetAddressForm = () => {
            if (!addressForm) {
                return;
            }
            setFormMode('new');
            if (addressIdField) addressIdField.value = '';
            if (labelField) labelField.value = initialState.label;
            if (receiverField) receiverField.value = initialState.receiver;
            if (phoneField) phoneField.value = initialState.phone;
            if (addressLineField) addressLineField.value = initialState.address;
            if (cityField) cityField.value = initialState.city;
            if (postalField) postalField.value = initialState.postal;
            if (defaultCheckbox) defaultCheckbox.checked = initialState.isDefault;
        };

        const fillAddressForm = (data) => {
            if (!addressForm) {
                return;
            }
            if (!addressFormWrapper?.classList.contains('show')) {
                addressFormWrapper?.classList.add('show');
            }
            if (addressIdField) addressIdField.value = data.id || '';
            if (labelField) labelField.value = data.label || '';
            if (receiverField) receiverField.value = data.receiver || '';
            if (phoneField) phoneField.value = data.phone || '';
            if (addressLineField) addressLineField.value = data.address || '';
            if (cityField) cityField.value = data.city || '';
            if (postalField) postalField.value = data.postal || '';
            if (defaultCheckbox) defaultCheckbox.checked = data.isDefault === '1';
            setFormMode('edit');
            syncToggleState();
        };

        const syncToggleState = () => {
            if (!toggleButton || !addressFormWrapper) {
                return;
            }
            const isOpen = addressFormWrapper.classList.contains('show');
            toggleButton.classList.toggle('active', isOpen);
            if (isOpen) {
                const label = isEditingAddress ? 'Close Editor' : 'Close Address Form';
                toggleButton.innerHTML = `<i class="bi bi-dash-circle me-1"></i> ${label}`;
            } else {
                toggleButton.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Add New Address';
            }
        };

        setFormMode(isEditingAddress ? 'edit' : 'new');
        syncToggleState();

        if (toggleButton && addressFormWrapper) {
            toggleButton.addEventListener('click', () => {
                if (addressFormWrapper.classList.contains('show') && isEditingAddress) {
                    resetAddressForm();
                }
                addressFormWrapper.classList.toggle('show');
                if (!addressFormWrapper.classList.contains('show')) {
                    resetAddressForm();
                }
                syncToggleState();
            });
        }

        document.querySelectorAll('[data-action="edit-address"]').forEach((button) => {
            button.addEventListener('click', () => {
                const dataset = button.dataset;
                fillAddressForm({
                    id: dataset.addressId || '',
                    label: dataset.addressLabel || '',
                    receiver: dataset.addressReceiver || '',
                    phone: dataset.addressPhone || '',
                    address: dataset.addressLine || '',
                    city: dataset.addressCity || '',
                    postal: dataset.addressPostal || '',
                    isDefault: dataset.addressDefault || '0',
                });
            });
        });

        const deleteForm = document.getElementById('deleteAddressForm');
        document.querySelectorAll('[data-action="delete-address"]').forEach((button) => {
            button.addEventListener('click', () => {
                const url = button.getAttribute('data-delete-url');
                if (!url || !deleteForm) {
                    return;
                }

                const name = button.getAttribute('data-address-name') || 'this address';

                Swal.fire({
                    title: 'Delete this address?',
                    text: `Remove ${name} from your saved addresses?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'No, keep it',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'swal-confirm',
                        cancelButton: 'swal-cancel',
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteForm.setAttribute('action', url);
                        deleteForm.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
