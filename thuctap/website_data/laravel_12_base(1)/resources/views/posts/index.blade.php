<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Post Management</title>
    <link rel="stylesheet" href="{{ asset('css/style_user.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

@include('layouts.header')

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Post Management</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPostModal">
            + Add New Post
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Bảng danh sách bài viết -->
    <table class="table table-striped table-hover shadow rounded">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Status</th> <!-- Thêm cột trạng thái -->
                <th>Contentt</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($posts as $post)
                <tr>
                    <td>{{ $post->id_post }}</td>
                    <td>{{ $post->title_post }}</td>
                    <td>{{ $post->author }}</td>
                    <td>{{ $post->category->title_cate ?? 'N/A' }}</td> <!-- Hiển thị danh mục -->
                    <td>{{ $post->status_post }}</td> <!-- Hiển thị trạng thái -->
                    <td>{{ $post->content_post }}</td>
                    <td>
                        <button class="btn btn-sm btn-warning editPostBtn"
                            data-bs-toggle="modal" data-bs-target="#editPostModal"
                            data-id="{{ $post->id_post }}" data-title="{{ $post->title_post }}"
                            data-author="{{ $post->author }}" data-category="{{ $post->id_cate }}"
                            data-status="{{ $post->status_post }}" data-content="{{ $post->content_post }}">
                            Edit
                        </button>

                        <button class="btn btn-sm btn-danger deletePostBtn"
                            data-bs-toggle="modal" data-bs-target="#deletePostModal"
                            data-id="{{ $post->id_post }}">
                            Delete
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Thêm bài viết -->
<div class="modal fade" id="addPostModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
            <form action="{{ route('posts.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="title_post" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Author</label>
                    <input type="text" class="form-control" name="author" required>
                </div>
            
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="id_cate" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id_cate }}">{{ $category->title_cate }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-control" name="status_post" required>
                        <option value="Draft">Draft</option>
                        <option value="Published">Published</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea class="form-control" name="content_post" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-success">Save</button>
            </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chỉnh sửa bài viết -->
<div class="modal fade" id="editPostModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPostForm"  method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editPostId" name="id_post">
                    
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" id="editTitle" name="title_post" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Author</label>
                        <input type="text" class="form-control" id="editAuthor" name="author" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="id_cate" id="editCategoryId" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id_cate }}">{{ $category->title_cate }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="editStatus" name="status_post">
                            <option value="Draft">Draft</option>
                            <option value="Published">Published</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" id="editContent" name="content_post" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete post -->
<div class="modal fade" id="deletePostModal" tabindex="-1" aria-labelledby="deletePostModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this post?</p>
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


</body>
</html>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.editPostBtn').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('editPostId').value = this.dataset.id;
                document.getElementById('editTitle').value = this.dataset.title;
                document.getElementById('editAuthor').value = this.dataset.author;
                const categoryId = this.dataset.category;
                document.querySelector(`#editCategoryId option[value="${categoryId}"]`).selected = true;
                document.getElementById('editContent').textContent = this.dataset.content;
                document.getElementById('editStatus').value = this.dataset.status;

                // Cập nhật action form
                document.getElementById('editPostForm').action = `/posts/${this.dataset.id}`;
            });
        });

        document.querySelectorAll('.deletePostBtn').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('deletePostForm').action = `/posts/${this.dataset.id}`;
            });
        });
    });
</script>

</body>
</html>
