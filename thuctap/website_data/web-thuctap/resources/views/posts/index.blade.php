<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Post Management</title>
    <link rel="stylesheet" href="/WEB-GoogleMaps/public/css/style_admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   
</head>
<body>
@include('layouts.header')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Post Management</h2>
            <a href="?controller=post&action=create" class="btn btn-primary">+ Add New Post</a>
        </div>

        <table class="table table-striped table-hover shadow rounded">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Ví dụ dữ liệu giả - bạn thay bằng vòng lặp lấy từ model -->
                <tr>
                    <td>1</td>
                    <td>First Blog Post</td>
                    <td>John Doe</td>
                    <td>2025-03-20</td>
                    <td>
                        <a href="?controller=post&action=edit&id=1" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?controller=post&action=delete&id=1" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Second Post</td>
                    <td>Jane Smith</td>
                    <td>2025-03-19</td>
                    <td>
                        <a href="?controller=post&action=edit&id=2" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?controller=post&action=delete&id=2" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <!-- end -->
            </tbody>
        </table>
    </div>
</body>
</html>
