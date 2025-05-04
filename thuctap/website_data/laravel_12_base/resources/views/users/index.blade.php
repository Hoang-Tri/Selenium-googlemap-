<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="{{ asset($settings['favicon_path'] ?? 'images/GMG.ico') }}">
    <title>User Management</title>
    <link rel="stylesheet" href="{{ asset('css/style_user.css') }}">
    <script src="{{ asset('js/user_management.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    @include('layouts.header')

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>User Management</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                + Add New User
            </button>
        </div>

        <!-- Modal Thêm User -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="fullname" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-success">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal xem lịch sử -->
        <div class="modal fade" id="userHistoryModal" tabindex="-1" aria-labelledby="userHistoryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content shadow rounded">
                <div class="modal-header">
                    <h5 class="modal-title" id="userHistoryModalLabel">Lịch sử tìm kiếm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <h6 class="mb-3"><strong>Người dùng:</strong> <span id="historyUsername"></span></h6>
                    <ul id="historyList" class="list-group">
                    <!-- Lịch sử sẽ được chèn ở đây -->
                    </ul>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
                </div>
            </div>
        </div>

        <!-- Bảng hiển thị danh sách user -->
        <table class="table table-striped table-hover shadow rounded">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->fullname }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <button class="btn btn-sm btn-info viewHistoryBtn"
                            data-bs-toggle="modal" data-bs-target="#userHistoryModal"
                            data-history='@json($user->history ? json_decode($user->history, true) : [])'
                            data-username="{{ $user->username }}">
                            Xem lịch sử
                        </button>
                        <button class="btn btn-sm btn-warning editUserBtn"
                            data-bs-toggle="modal" data-bs-target="#editUserForm"
                            data-id="{{ $user->id }}" 
                            data-name="{{ $user->username }}" 
                            data-fullname="{{ $user->fullname }}" 
                            data-email="{{ $user->email }}">
                            Edit
                        </button>
                        @if ($user->level != 1)
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                    Delete
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Edit User -->
    <div class="modal fade" id="editUserForm" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editUserId">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="editUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editFullname" name="fullname" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <!-- Có thể thêm sửa password nếu muốn -->
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Delete User -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user?</p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
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
        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.editUserBtn');
            const editForm = document.getElementById('editUserForm');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const fullname = this.getAttribute('data-fullname');
                    const email = this.getAttribute('data-email');

                    // Set form values
                    document.getElementById('editUserId').value = id;
                    document.getElementById('editUsername').value = name;
                    document.getElementById('editFullname').value = fullname;
                    document.getElementById('editEmail').value = email;

                    // Cập nhật action của form
                    editForm.action = `/users/${id}`;
                });
            });

            // Xử lý khi nhấn nút "Delete"
            document.querySelectorAll(".deleteUserBtn").forEach(button => {
                button.addEventListener("click", function () {
                    const userId = this.getAttribute("data-id");
                    document.getElementById("deleteUserForm").action = `/users/${userId}`;
                });
            });
        });
        document.querySelectorAll('.viewHistoryBtn').forEach(button => {
            button.addEventListener('click', function () {
                const username = this.getAttribute('data-username');
                const history = JSON.parse(this.getAttribute('data-history') || '[]');
                const historyList = document.getElementById('historyList');

                document.getElementById('historyUsername').textContent = username;
                historyList.innerHTML = '';

                if (history.length === 0) {
                    historyList.innerHTML = '<li class="list-group-item">Chưa có lịch sử tìm kiếm.</li>';
                } else {
                    history.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item';
                        li.textContent = item;
                        historyList.appendChild(li);
                    });
                }
            });
        });

    </script>
</body>
</html>
