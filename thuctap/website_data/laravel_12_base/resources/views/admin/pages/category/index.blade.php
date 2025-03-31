<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Category Management</title>
    <link rel="stylesheet" href="{{ asset('css/style_user.css') }}">
    <script src="{{ asset('js/category_management.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head> 
<body>
@include('layouts.header')

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Category Management</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                + Add New Category
            </button>
        </div>

        @if(session('noti'))
            <div class="alert alert-success">
                {{ session('noti') }}
            </div>
        @endif

        <table class="table table-striped table-hover shadow rounded">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->id_cate }}</td>
                        <td>{{ $category->title_cate }}</td>
                        <td>{{ $category->link_cate }}</td>
                        <td>
                            @if ($category->status == 1)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ date('d/m/Y H:i', strtotime($category->created_at_cate)) }}</td>
                        <td>
                        <button class="btn btn-sm btn-warning editCategoryBtn"
                            data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                            data-id="{{ $category->id_cate }}" 
                            data-title="{{ $category->title_cate }}"
                            data-status="{{ $category->status }}">
                            Edit
                        </button>

                            <button class="btn btn-sm btn-danger deleteCategoryBtn"
                                data-bs-toggle="modal" data-bs-target="#deleteCategoryModal"
                                data-id="{{ $category->id_cate }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Add Category -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('category.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title_cate" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <input type="checkbox" name="status" value="1"> Active
                        </div>
                        <button type="submit" class="btn btn-success">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Category -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editCategoryId" name="id_cate">

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" id="editTitle" name="title_cate" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <input type="checkbox" id="editStatus" name="status" value="1"> Active
                        </div>
                        <button type="submit" class="btn btn-success">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Delete Category -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this category?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteCategoryForm" method="POST">
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
            document.querySelectorAll(".editCategoryBtn").forEach(button => {
                button.addEventListener("click", function () {
                    const categoryId = this.getAttribute("data-id");
                    const title = this.getAttribute("data-title");
                    const status = this.getAttribute("data-status");

                    console.log("Editing category:", { categoryId, title, status }); // Debug xem dữ liệu có đúng không

                    document.querySelector("#editCategoryId").value = categoryId;
                    document.querySelector("#editTitle").value = title;
                    document.querySelector("#editStatus").checked = (status == 1); // Nếu 1 thì checked

                    document.getElementById("editCategoryForm").action = `/category/${categoryId}`;
                });
            });

            document.querySelectorAll(".deleteCategoryBtn").forEach(button => {
                button.addEventListener("click", function () {
                    const categoryId = this.getAttribute("data-id");
                    document.getElementById("deleteCategoryForm").action = `/category/${categoryId}`;
                });
            });
        });
    </script>
</body>
</html>