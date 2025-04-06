<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/style_dash.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="d-flex">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="{{ url('/users') }}">User</a></li>
            <li><a href="{{ url('/locations') }}">Location</a></li>
            <li><a href="{{ url('/google') }}">GoogleMaps</a></li>
            <li><a href="{{ url('/users-reviews') }}">User Review</a></li>
            <li><a href="{{ url('/upload') }}">UploadFile</a></li>
            <!-- <li><a href="{{ url('/category') }}">Category</a></li> -->
            <!-- <li><a href="{{ url('/posts') }}">Posts</a></li>
            <li><a href="{{ url('/pages') }}">Pages</a></li> -->
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </li>
        </ul>
        <div class="contact-info">
            Contact: lehoangtri@gmail.com
        </div>
    </div>
        <div class="main-content">
            <nav class="navbar">
                <div class="container-fluid d-flex justify-content-between">
                    <div class="search-box">
                        <input type="text" placeholder="Search...">
                        <i class="fas fa-search"></i>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary"><i class="fas fa-user"></i> Profile</button>
                        <!-- <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt"></i> Logout</button>
                        </form>
                        -->
                    </div>
                </div>
            </nav>
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center p-3" style="background-color: #f0f8ff; border: 2px solid #1e90ff; color: #333; border-radius: 8px;"  >
                        <h5>Users</h5>
                        <p>{{ $userCount }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center p-3" style="background-color:rgb(248, 176, 176); border: 2px solid rgb(240, 25, 25); color: #333; border-radius: 8px;">
                        <h5>Locations</h5>
                        <p>{{ $locationCount }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center p-3" style="background-color:rgb(251, 255, 214); border: 2px solid rgb(214, 250, 11); color: #333; border-radius: 8px;">
                        <h5>User-Review</h5>
                        <p>{{ $usersreviewCount }}</p>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card p-3">
                        <h5>Requests Sent Overview</h5>
                        <canvas id="earningsChart"></canvas>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3">
                        <h5>Posts Statistics</h5>
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Initialize the array to store daily requests for 30 days
        let dailySearchRequests = JSON.parse(localStorage.getItem("dailySearchRequests")) || new Array(30).fill(0); // Dữ liệu tìm kiếm
        let dailySubmitRequests = JSON.parse(localStorage.getItem("dailySubmitRequests")) || new Array(30).fill(0); // Dữ liệu gửi yêu cầu

        // Lấy đối tượng biểu đồ
        var ctx = document.getElementById('earningsChart').getContext('2d');

        // Tạo biểu đồ đường với 2 đường line
        let chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: Array.from({ length: 30 }, (_, i) => (i + 1).toString()), // Labels từ 1 đến 30
                datasets: [
                    {
                        label: 'Gửi yêu cầu',
                        data: dailySubmitRequests, // Dữ liệu gửi yêu cầu
                        borderColor: 'blue', // Màu xanh cho gửi yêu cầu
                        fill: false
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        min: 0, // Đảm bảo trục tung không có giá trị âm
                        ticks: {
                            stepSize: 1,
                            beginAtZero: true,
                        }
                    }
                }
            }
        });
        var ctx2 = document.getElementById('revenueChart').getContext('2d');

        // Dữ liệu mẫu cho các Location
        var locationNames = ['Bến Ninh Kiều', 'Lotte Mart', 'Trung_Nguyên_E-coffee', 'Trung_Nguyên_E-Coffee_71-73A6_Hung_Phu_1', 'Minh_Long_-_Showroom_Quận_3'];
        var locationCounts = [120, 80, 50, 150];  // Giả lập số lượng bài viết

        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: locationNames, // Tên các Location
                datasets: [{
                    data: locationCounts, // Số lượng bài viết cho mỗi Location
                    backgroundColor: ['blue', 'green', 'red', 'orange'], // Màu sắc
                }]
            }
        });
    </script>
</body>
</html>
