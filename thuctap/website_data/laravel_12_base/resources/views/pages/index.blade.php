<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Page Management</title>
    <link rel="stylesheet" href="{{ asset('css/style_user.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>
@include('layouts.header')

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Page Management</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPageModal">
            + Add New Page
        </button>
    </div>

    <!-- Modal: Thêm Page -->
    <div class="modal fade" id="addPageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('pages.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control" name="slug" required>
                        </div>
                        <button type="submit" class="btn btn-success">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách Pages -->
    <table class="table table-striped table-hover shadow rounded">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Page Title</th>
                <th>Slug</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pages as $page)
                <tr>
                    <td>{{ $page->id }}</td>
                    <td>{{ $page->title }}</td>
                    <td>{{ $page->slug }}</td>
                    <td>
                        <button class="btn btn-sm btn-warning editPageBtn"
                            data-bs-toggle="modal" data-bs-target="#editPageModal"
                            data-id="{{ $page->id }}" data-title="{{ $page->title }}"
                            data-slug="{{ $page->slug }}">
                            Edit
                        </button>

                        <button class="btn btn-sm btn-danger deletePostBtn"
                            data-bs-toggle="modal" data-bs-target="#deletePostModal"
                            data-id="{{ $page->id }}">
                            Delete
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal: Sửa Page -->
<div class="modal fade" id="editPageModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editPageForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editPageId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" id="editPageTitle" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" id="editPageSlug" name="slug" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="deletePostModal" tabindex="-1" aria-labelledby="deletePostModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this page?</p>
            </div>
            <div class="modal-footer">
                <form id="deletePostForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Xử lý nút "Edit"
    document.querySelectorAll('.editPageBtn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('editPageId').value = this.dataset.id;
            document.getElementById('editPageTitle').value = this.dataset.title;
            document.getElementById('editPageSlug').value = this.dataset.slug;
            document.getElementById('editPageForm').action = `/pages/${this.dataset.id}`;
        });
    });

    // Xử lý nút "Delete"
    document.querySelectorAll('.deletePostBtn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('deleteUserForm').action = `/posts/${this.dataset.id}`;
        });
    });
});
</script>

</body>
</html>
