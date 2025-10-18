<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin · Categories - VogueVault</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
	<link rel="stylesheet" href="{{ asset('css/admin/manageCategories.css') }}">
@php
	use Illuminate\Support\Str;
@endphp
</head>
<body>
	<div class="layout">
		@include('admin.partials.sidebar', ['active' => 'categories'])

		<main class="content">
			<header class="content-header">
				<div>
					<h1 class="content-title">Manage Categories</h1>
					<p class="content-subtitle">Organize the categories your customers browse.</p>
				</div>
				<button class="btn-add" type="button" data-action="toggle-form">
					<i class="bi bi-plus"></i> New Category
				</button>
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

			<section id="categoryFormCard" class="category-form-card {{ $errors->any() ? '' : 'is-hidden' }}">
				<form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="form-header">
						<h2>Create Category</h2>
						<button type="button" class="btn-icon" data-action="cancel-create" title="Close form">
							<i class="bi bi-x-lg"></i>
						</button>
					</div>
					<div class="form-grid">
						<label class="form-field">
							<span>Name</span>
							<input type="text" name="name" value="{{ old('name') }}" required>
						</label>
						<label class="form-field">
							<span>Status</span>
							<div class="switch-input">
								<input type="hidden" name="is_active" value="0">
								<input type="checkbox" id="new_category_is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
								<label for="new_category_is_active">Active</label>
							</div>
						</label>
					</div>
					<label class="form-field">
						<span>Description</span>
						<textarea name="description" rows="4" placeholder="Describe the category">{{ old('description') }}</textarea>
					</label>
					<label class="form-field">
						<span>Image</span>
						<div class="file-drop" data-dropzone>
							<input type="file" name="image" accept="image/*" data-dropzone-input>
							<button type="button" class="file-drop-body" data-dropzone-trigger>
								<span class="file-drop-icon"><i class="bi bi-cloud-arrow-up"></i></span>
								<span class="file-drop-text">Drag &amp; drop or <span class="file-drop-link">browse</span> to upload</span>
								<span class="file-drop-hint">PNG, JPG, atau WEBP hingga 2&nbsp;MB</span>
							</button>
							<div class="file-drop-preview" data-dropzone-preview hidden>
								<img src="" alt="Selected preview" data-dropzone-preview-img>
								<div class="file-drop-meta">
									<span class="file-drop-name" data-dropzone-filename>No file selected</span>
									<button type="button" class="file-drop-remove" data-dropzone-remove>Remove</button>
								</div>
							</div>
						</div>
						<span class="field-hint">Anda juga dapat menempelkan gambar langsung ke area unggah.</span>
					</label>

					<div class="form-actions">
						<button type="submit" class="btn-primary">Save Category</button>
						<button type="button" class="btn-secondary" data-action="cancel-create">Cancel</button>
					</div>
				</form>
			</section>

			<section class="category-table-card">
				<header class="table-header">
					<h2>All Categories</h2>
					<span class="count-pill">{{ $categories->total() }} total</span>
				</header>

				@if($categories->isEmpty())
					<div class="empty-state">
						<i class="bi bi-collection"></i>
						<p>No categories yet. Create your first one to organize products.</p>
					</div>
				@else
					<div class="table-wrapper">
						<table class="categories-table">
							<thead>
								<tr>
									<th>Name</th>
									<th>Description</th>
									<th>Status</th>
									<th>Updated</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach($categories as $category)
									@php
										$imageUrl = $category->display_image_url ?? $placeholderImage;
										$description = Str::limit(strip_tags((string) $category->description), 80) ?: 'No description provided.';
									@endphp
									<tr>
										<td>
											<div class="category-meta">
												<div class="category-thumb">
													<img src="{{ $imageUrl }}" alt="{{ $category->name }}" onerror="this.onerror=null;this.src='{{ $placeholderImage }}';">
												</div>
												<div>
													<div class="category-name">{{ $category->name }}</div>
													<div class="category-id">#{{ $category->id }}</div>
												</div>
											</div>
										</td>
										<td>{{ $description }}</td>
										<td>
											<span class="status-pill {{ $category->is_active ? 'active' : 'inactive' }}">
												{{ $category->is_active ? 'Active' : 'Inactive' }}
											</span>
										</td>
										<td>{{ $category->updated_at?->format('d M Y') }}</td>
										<td class="actions">
											<a href="{{ route('admin.categories.edit', $category) }}" class="btn-icon" title="Edit category">
												<i class="bi bi-pencil"></i>
											</a>
											<form id="delete-category-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category) }}" method="POST">
												@csrf
												@method('DELETE')
												<button type="button" class="btn-icon danger" data-action="delete-category" data-target-form="delete-category-{{ $category->id }}" data-category-name="{{ $category->name }}" title="Delete category">
													<i class="bi bi-trash"></i>
												</button>
											</form>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>

					<div class="pagination-wrapper">
						{{ $categories->links() }}
					</div>
				@endif
			</section>
		</main>
	</div>

	<div id="confirmDeleteModal" class="modal-overlay" aria-hidden="true" role="dialog" aria-modal="true">
		<div class="modal-card" role="document">
			<div class="modal-icon">
				<i class="bi bi-exclamation-lg"></i>
			</div>
			<h2 class="modal-title">Delete Category?</h2>
			<p class="modal-message">
				Are you sure you want to delete <span class="modal-category" data-modal-category>this category</span>?
				Products assigned to it will also be removed.
			</p>
			<div class="modal-actions">
				<button type="button" class="btn-danger" data-action="confirm-delete">Delete</button>
				<button type="button" class="btn-secondary" data-action="cancel-delete">Cancel</button>
			</div>
		</div>
	</div>

	<script src="{{ asset('js/admin/manageCategories.js') }}" defer></script>
</body>
</html>
