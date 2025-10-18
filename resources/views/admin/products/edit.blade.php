<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · Edit Product - VogueVault</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/admin/product.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/add-product.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="layout">
    @include('admin.partials.sidebar', ['active' => 'products'])

    <main class="content">
        <header class="form-header">
            <div class="form-header__titles">
                <h1 class="form-title">Edit Product</h1>
                <p class="form-subtitle">Ubah detail produk berikut, lalu simpan perubahan.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to All Products
            </a>
        </header>

        {{-- Error Validation --}}
        @if ($errors->any())
            <div class="form-errors" role="alert">
                <strong>Periksa kembali isian berikut:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="form-card">
            <form action="{{ route('admin.products.update', $product->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="product-form">

                @csrf
                @method('PUT')

                <div class="form-grid">
                    {{-- Product Name --}}
                    <div class="form-group">
                        <label for="name">Product Name<span>*</span></label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name', $product->name) }}"
                               placeholder="Contoh: Leather Chronograph Watch"
                               required>
                    </div>

                    {{-- Short Description --}}
                    <div class="form-group">
                        <label for="short_description">Short Description</label>
                        <input type="text" id="short_description" name="short_description"
                               value="{{ old('short_description', $product->short_description) }}"
                               placeholder="Deskripsi singkat produk">
                    </div>

                    {{-- Full Description --}}
                    <div class="form-group form-group--full">
                        <label for="description">Full Description</label>
                        <textarea id="description" name="description" rows="5"
                                  placeholder="Tuliskan detail produk, material, fitur, dan lainnya.">{{ old('description', $product->description) }}</textarea>
                    </div>

                    {{-- Price --}}
                    <div class="form-group">
                        <label for="price">Price (Rp)<span>*</span></label>
                        <input type="number" id="price" name="price"
                               value="{{ old('price', $product->price) }}"
                               min="0" step="0.01" required>
                    </div>

                    {{-- Stock --}}
                    <div class="form-group">
                        <label for="stock">Stock<span>*</span></label>
                        <input type="number" id="stock" name="stock"
                               value="{{ old('stock', $product->stock) }}"
                               min="0" required>
                    </div>

                    {{-- Category --}}
                    <div class="form-group">
                        <label for="category_id">Category<span>*</span></label>
                        <select id="category_id" name="category_id" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                        {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Product Images --}}
                    <div class="form-group form-group--full">
                        <label for="images">Product Images</label>

                        <div class="image-preview" id="image-preview-list">
                            @if($product->images && count($product->images) > 0)
                                @foreach($product->images as $index => $image)
                                    @php
                                        $imagePath = Str::startsWith($image, 'http')
                                            ? $image
                                            : asset('storage/' . ltrim($image, '/'));
                                    @endphp
                                    <div class="image-box">
                                        <img src="{{ $imagePath }}" alt="Product Image" class="preview-image"
                                            onclick="document.getElementById('fileInput{{ $index }}').click();">

                                        <!-- Tombol hapus -->
                                        <button type="button" class="delete-btn" title="Hapus gambar"
                                                onclick="removeImage(this)">
                                            &times;
                                        </button>

                                        <input type="file" id="fileInput{{ $index }}"
                                            name="images_existing[{{ $index }}]"
                                            accept="image/*" class="hidden-input"
                                            onchange="previewSingleImage(event, this)">
                                    </div>
                                @endforeach
                            @else
                                <p class="preview-placeholder">Belum ada gambar yang diunggah.</p>
                            @endif

                            {{-- Tambah Gambar Baru --}}
                            <div class="add-image-box" onclick="document.getElementById('newImages').click();">
                                <i class="bi bi-plus-circle"></i>
                                <p>Tambah Gambar</p>
                                <input type="file" id="newImages" name="images[]" accept="image/*" multiple hidden
                                       onchange="previewNewImages(event)">
                            </div>
                        </div>
                    </div>

                    {{-- Additional Attributes --}}
                    <div class="form-group form-group--full">
                        <label>Additional Attributes</label>
                        <p class="help-text">
                            Tambahkan pasangan atribut seperti Size: Large, Color: Navy.
                            Biarkan kosong jika tidak diperlukan.
                        </p>

                        <div id="attribute-fields" class="attribute-list">
                            @php
                                $attributes = $product->attributes ?? [];
                                if (empty($attributes)) $attributes = ['' => ''];
                            @endphp

                            @foreach($attributes as $key => $value)
                                @php
                                    $displayValue = is_array($value) ? implode(', ', $value) : $value;
                                @endphp
                                <div class="attribute-row">
                                    <input type="text" name="attribute_keys[]" value="{{ $key }}"
                                           placeholder="Attribute (cth: Size)">
                                    <input type="text" name="attribute_values[]" value="{{ $displayValue }}"
                                           placeholder="Value (cth: Large)">
                                    <button type="button" class="attribute-remove" aria-label="Remove attribute">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="btn-tertiary" id="add-attribute">
                            <i class="bi bi-plus-circle"></i> Add Attribute
                        </button>
                    </div>
                </div>

                {{-- Form Actions (Kanan Bawah) --}}
                <div class="form-actions"
                     style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 40px; padding-right: 10px;">
                    <a href="{{ route('admin.products.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-save"></i> Save Product
                    </button>
                </div>
            </form>
        </section>
    </main>
</div>

{{-- Script Preview & Attribute --}}
<script>
    function previewSingleImage(event, input) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                input.previousElementSibling.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    function removeImage(button) {
    const box = button.closest('.image-box');
    box.remove();
    // Kalau mau juga hapus di database nanti, bisa kirim AJAX request dari sini
    }


    function previewNewImages(event) {
        const files = event.target.files;
        const previewList = document.getElementById('image-preview-list');

        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const box = document.createElement('div');
                box.classList.add('image-box');
                box.innerHTML = `<img src="${e.target.result}" class="preview-image">`;
                previewList.insertBefore(box, document.querySelector('.add-image-box'));
            };
            reader.readAsDataURL(file);
        });
    }

    document.getElementById('add-attribute').addEventListener('click', function () {
        const container = document.getElementById('attribute-fields');
        const row = document.createElement('div');
        row.classList.add('attribute-row');
        row.innerHTML = `
            <input type="text" name="attribute_keys[]" placeholder="Attribute (cth: Size)">
            <input type="text" name="attribute_values[]" placeholder="Value (cth: Large)">
            <button type="button" class="attribute-remove" aria-label="Remove attribute">
                <i class="bi bi-x"></i>
            </button>
        `;
        container.appendChild(row);
    });

    document.getElementById('attribute-fields').addEventListener('click', function (e) {
        if (e.target.closest('.attribute-remove')) {
            e.target.closest('.attribute-row').remove();
        }
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Produk berhasil diperbarui!',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        }).then(() => {
            window.location.href = "{{ route('admin.products.index') }}";
        });
    @endif
</script>

{{-- Inline Image Box Style --}}
<style>
    .image-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .image-box,
    .add-image-box {
        width: 100px;
        height: 100px;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        background-color: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-box:hover img {
        opacity: 0.7;
    }

    .delete-btn {
        position: absolute;
        top: 6px;
        right: 6px;
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        font-size: 18px;
        line-height: 20px;
        opacity: 0;
        cursor: pointer;
        transition: opacity 0.2s ease;
    }

    .image-box:hover .delete-btn {
        opacity: 1;
    }

    .delete-btn:hover {
        background-color: rgba(255, 0, 0, 0.7);
    }

    .preview-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.3s ease;
    }

    .hidden-input {
        display: none;
    }

    .add-image-box {
        border: 2px dashed #a7b9ad;
        color: #4f9462;
        border-radius: 10px;
        flex-direction: column;
        font-size: 0.85rem;
        transition: all 0.2s;
        background-color: #f8faf9;
        text-align: center;
    }

    .add-image-box:hover {
        background-color: #f0f7f2;
        border-color: #4f9462;
    }

    .add-image-box i {
        font-size: 1.6rem;
        margin-bottom: 4px;
    }

    .add-image-box p {
        margin: 0;
        line-height: 1.2;
    }
</style>
</body>
</html>
