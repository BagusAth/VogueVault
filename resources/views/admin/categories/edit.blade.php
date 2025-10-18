<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category · VogueVault Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/admin/manageCategories.css') }}">
</head>
<body>
    <div class="layout">
        @include('admin.partials.sidebar', ['active' => 'categories'])

        <main class="content">
            <header class="content-header">
                <div>
                    <h1 class="content-title">Edit Category</h1>
                    <p class="content-subtitle">Update the information customers see for this category.</p>
                </div>
                <a href="{{ route('admin.categories.index') }}" class="btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Categories
                </a>
            </header>

            @if(session('status'))
                <div class="alert success" role="status" data-auto-dismiss="6000">
                    <div class="alert-body">
                        <span class="alert-icon"><i class="bi bi-check-circle-fill"></i></span>
                        <div class="alert-text">
                            <strong>Success</strong>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                    <button type="button" class="alert-close" data-action="dismiss-alert" aria-label="Close alert">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert error" role="alert">
                    <div class="alert-body">
                        <span class="alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
                        <div class="alert-text">
                            <strong>Perhatian</strong>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="alert-close" data-action="dismiss-alert" aria-label="Tutup peringatan">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            @endif

            @php
                $currentImage = $category->display_image_url ?? $placeholderImage;
                $storedImage = $category->image;
            @endphp

            <section class="category-form-card">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-grid">
                        <label class="form-field">
                            <span>Name</span>
                            <input type="text" name="name" value="{{ old('name', $category->name) }}" required>
                        </label>
                        <label class="form-field">
                            <span>Status</span>
                            <div class="switch-input">
                                <input type="checkbox" id="edit_category_is_active" name="is_active" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label for="edit_category_is_active">Active</label>
                            </div>
                        </label>
                    </div>

                    <label class="form-field">
                        <span>Description</span>
                        <textarea name="description" rows="4" placeholder="Describe the category">{{ old('description', $category->description) }}</textarea>
                    </label>

                    <div class="form-grid">
                        <label class="form-field">
                            <span>Update Image</span>
                            <input type="file" name="image" accept="image/*">
                            <span class="field-hint">Upload a new image to replace the existing one. Leave empty to keep current.</span>
                        </label>
                        <div class="form-field">
                            <span>Current Image</span>
                            <div class="current-image">
                                <img src="{{ $currentImage }}" alt="{{ $category->name }}" onerror="this.onerror=null;this.src='{{ $placeholderImage }}';">
                                @if($storedImage)
                                    <span class="current-image-path">Stored as: {{ $storedImage }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Save Changes</button>
                        <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
