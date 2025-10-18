<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · Add Product - VogueVault</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/admin/product.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/add-product.css') }}">
</head>
<body>
    <div class="layout">
        @include('admin.partials.sidebar', ['active' => 'products'])

        <main class="content">
            <header class="form-header">
                <div class="form-header__titles">
                    <h1 class="form-title">Add New Product</h1>
                    <p class="form-subtitle">Complete the details below to add this product to the catalog.</p>
                </div>
                <a href="{{ route('admin.products.index') }}" class="btn-secondary">
                    <i class="bi bi-arrow-left"></i>
                    Back to All Products
                </a>
            </header>

            @if ($errors->any())
                <div class="form-errors" role="alert">
                    <strong>Please review the following:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="form-card">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="product-form">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group @error('name') has-error @enderror">
                            <label for="name">Product Name<span>*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Example: Leather Chronograph Watch" required>
                            @error('name')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="short_description">Short Description</label>
                            <input type="text" id="short_description" name="short_description" value="{{ old('short_description') }}" placeholder="Short summary of the product">
                        </div>

                        <div class="form-group form-group--full">
                            <label for="description">Full Description</label>
                            <textarea id="description" name="description" rows="5" placeholder="Describe product details, materials, features, and more.">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group @error('price') has-error @enderror">
                            <label for="price">Price (Rp)<span>*</span></label>
                            <input type="number" id="price" name="price" value="{{ old('price') }}" min="0" step="0.01" placeholder="250000" required>
                            @error('price')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group @error('stock') has-error @enderror">
                            <label for="stock">Stock<span>*</span></label>
                            <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" placeholder="10" required>
                            @error('stock')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group @error('category_id') has-error @enderror">
                            <label for="category_id">Category<span>*</span></label>
                            <select id="category_id" name="category_id" required>
                                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group form-group--full">
                            <label for="images">Product Images</label>
                            <div id="image-drop-area" class="file-upload drop-area @error('images') has-error @enderror">
                                <div class="file-upload__label">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <div class="file-upload__text">
                                        <strong>Drag & drop</strong> images into this area or click to choose.
                                        <span>Formats: JPG, PNG · Up to 5 images · 4MB per image</span>
                                    </div>
                                </div>
                                <button type="button" class="btn-tertiary file-upload__button" id="browse-images">
                                    <i class="bi bi-folder2-open"></i>
                                    Choose Images
                                </button>
                                <input type="file" id="images" name="images[]" accept="image/*" multiple data-max-files="5">
                            </div>
                            <div class="image-preview" id="image-preview-list">
                                <p class="preview-placeholder">No images selected yet.</p>
                            </div>
                            <p class="help-text image-limit-notice" id="image-limit-notice" aria-live="polite"></p>
                            @error('images')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                            @error('images.*')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group form-group--full">
                            <label>Specifications</label>
                            <p class="help-text">Add specification info such as Material: Cotton, UV Protection: UV400. Great for details customers don't choose directly.</p>
                            <div id="specification-fields" class="attribute-list">
                                @php
                                    $specKeys = old('specification_keys', ['']);
                                    $specValues = old('specification_values', ['']);
                                @endphp
                                @foreach($specKeys as $index => $key)
                                    <div class="attribute-row">
                                        <input type="text" name="specification_keys[]" value="{{ $key }}" placeholder="Specification (e.g. Material)">
                                        <input type="text" name="specification_values[]" value="{{ $specValues[$index] ?? '' }}" placeholder="Value (e.g. Cotton)">
                                        <button type="button" class="attribute-remove" aria-label="Remove specification">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn-tertiary" id="add-specification">
                                <i class="bi bi-plus-circle"></i>
                                Add Specification
                            </button>
                            @error('specification_keys.*')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                            @error('specification_values.*')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group form-group--full">
                            <label>Variant Options</label>
                            <p class="help-text">Define variants customers can choose, such as Size with options "S, M, L" or Color with options "Red, Black". Separate each option with commas or line breaks.</p>
                            <div id="variant-fields" class="attribute-list">
                                @php
                                    $variantKeys = old('variant_keys', ['']);
                                    $variantValues = old('variant_values', ['']);
                                @endphp
                                @foreach($variantKeys as $index => $key)
                                    <div class="attribute-row">
                                        <input type="text" name="variant_keys[]" value="{{ $key }}" placeholder="Variant (e.g. Size)">
                                        <input type="text" name="variant_values[]" value="{{ $variantValues[$index] ?? '' }}" placeholder="List options (e.g. S, M, L)">
                                        <button type="button" class="attribute-remove" aria-label="Remove variant">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn-tertiary" id="add-variant">
                                <i class="bi bi-plus-circle"></i>
                                Add Variant
                            </button>
                            @error('variant_keys.*')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                            @error('variant_values.*')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.products.index') }}" class="btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn-primary">
                            <i class="bi bi-save"></i>
                            Save Product
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <script src="{{ asset('js/admin/add-product.js') }}"></script>
</body>
</html>
