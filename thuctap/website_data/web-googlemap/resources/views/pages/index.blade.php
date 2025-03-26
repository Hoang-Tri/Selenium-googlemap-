<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Page Management</title>
    <link rel="stylesheet" href="/WEB-GoogleMaps/public/css/style_admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   
</head>
<body>
@include('layouts.header')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Page Management</h2>
            <a href="?controller=page&action=create" class="btn btn-primary">+ Add New Page</a>
        </div>

        <table class="table table-striped table-hover shadow rounded">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Page Title</th>
                    <th>Slug</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Ví dụ dữ liệu mẫu - bạn thay bằng dữ liệu động từ Model -->
                <tr>
                    <td>1</td>
                    <td>About Us</td>
                    <td>about-us</td>
                    <td>2025-03-18</td>
                    <td>
                        <a href="?controller=page&action=edit&id=1" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?controller=page&action=delete&id=1" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this page?')">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Contact</td>
                    <td>contact</td>
                    <td>2025-03-17</td>
                    <td>
                        <a href="?controller=page&action=edit&id=2" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?controller=page&action=delete&id=2" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this page?')">Delete</a>
                    </td>
                </tr>
                <!-- end -->
            </tbody>
        </table>
    </div>
</body>
</html>
