<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/style_user.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    @include('layouts.header')

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>User Management</h2>
            <a href="?controller=user&action=create" class="btn btn-primary">+ Add New User</a>
        </div>

        <table class="table table-striped table-hover shadow rounded">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>john@example.com</td>
                    <td>Admin</td>
                    <td>
                    <a href="?controller=post&action=edit&id=1" class="btn btn-sm btn-warning">Edit</a>
                    <a href="?controller=post&action=delete&id=1" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Jane Smith</td>
                    <td>jane@example.com</td>
                    <td>User</td>
                    <td>
                        <a href="?controller=post&action=edit&id=2" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?controller=post&action=delete&id=2" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <!-- End Example -->
            </tbody>
        </table>
    </div>
</body>
</html>
