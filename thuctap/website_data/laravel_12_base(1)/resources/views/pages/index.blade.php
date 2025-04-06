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
                            <input type="text" class="form-control" name="title_page" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Author</label>
                            <input type="text" class="form-control" name="author_page" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea class="form-control" name="content_page" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status_page">
                                <option value="Draft">Draft</option>
                                <option value="Published">Published</option>
                                <option value="Pending">Pending</option>
                            </select>
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
                <th>Author</th>
                <th>Content</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pages as $page)
                <tr>
                    <td>{{ $page->id_page }}</td>
                    <td>{{ $page->title_page }}</td>
                    <td>{{ $page->slug_page }}</td>
                    <td>{{ $page->author_page }}</td>
                    <td>{{ $page->content_page }}</td>
                    <td>{{ $page->status_page }}</td>
                    <td>
                    <button class="btn btn-sm btn-warning editPageBtn"
                        data-bs-toggle="modal" data-bs-target="#editPageModal"
                        data-id="{{ $page->id_page }}" 
                        data-title="{{ $page->title_page }}"
                        data-slug="{{ $page->slug_page }}"
                        data-author="{{ $page->author_page }}"
                        data-content="{{ $page->content_page }}"
                        data-status="{{ $page->status_page }}">
                        Edit
                    </button>

                    <button class="btn btn-sm btn-danger deletePageBtn"
                        data-bs-toggle="modal" data-bs-target="#deletePageModal"
                        data-id="{{ $page->id_page }}">
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
    <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPageForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editPageId" name="id_page">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" id="editPageTitle" name="title_page" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Author</label>
                        <input type="text" id="editPageAuthor" name="author_page" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" id="editPageContent" name="content_page" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="editPageStatus" name="status_page">
                            <option value="Draft">Draft</option>
                            <option value="Published">Published</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal: Xóa Page -->
<div class="modal fade" id="deletePageModal" tabindex="-1" aria-labelledby="deletePageModalLabel" aria-hidden="true">
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
                <form id="deletePageForm" method="POST">
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
            document.getElementById('editPageAuthor').value = this.dataset.author;
            document.getElementById('editPageContent').value = this.dataset.content;
            document.getElementById('editPageStatus').value = this.dataset.status;
            document.getElementById('editPageForm').action = `/pages/${this.dataset.id}`;
            
        });
    });

    // Xử lý nút "Delete"
    document.querySelectorAll('.deletePageBtn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('deletePageForm').action = `/pages/${this.dataset.id}`;
        });
    });
});
</script>

</body>
</html>
