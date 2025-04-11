<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/style_dash.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="d-flex">
    <div class="sidebar">
        <h2 >Chào mừng, {{ Auth::user()->fullname }}!</h2>
        <ul>
            <li><a href="{{ url('/users') }}">User</a></li>
            <li><a href="{{ url('/locations') }}">Location</a></li>
            <li><a href="{{ url('/google') }}">GoogleMaps</a></li>
            <li><a href="{{ url('/users-reviews') }}">User Review</a></li>
            <li><a href="{{ url('/upload') }}">File</a></li>
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
                </div>
            </nav>
            <div class="row">
                <div class="col-md-3">
                    <a href="{{ url('/users') }}" style="text-decoration: none; color: inherit;">
                        <div class="card text-center p-3 card-hover" style="border: 2px solid rgb(35, 64, 230); background-color:rgb(136, 177, 238);">
                            <i class="fas fa-users"></i>
                            <h5 style="margin: 0;">Users</h5>
                            <p>{{ $userCount }}</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ url('/users') }}" style="text-decoration: none; color: inherit;">
                        <div class="card text-center p-3 card-hover" style="border: 2px solid #ff4d4d; background-color:rgb(238, 143, 143);">
                            <i class="fas fa-map-marker-alt"></i>
                            <h5>Locations</h5>
                            <p>{{ $locationCount }}</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ url('/users') }}" style="text-decoration: none; color: inherit;">
                        <div class="card text-center p-3 card-hover" style="border: 2px solid rgb(230, 182, 25); background-color: #ffffe0;">
                            <i class="fas fa-star-half-alt"></i>
                            <h5>User-Review</h5>
                            <p>{{ $usersreviewCount }}</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <div class="card text-center p-3 card-hover" style="border: 2px solid rgb(20, 241, 68); background-color:rgb(155, 243, 148);">
                        <i class="fas fa-smile-beam"></i>
                        <h5>Hello</h5>
                        <p>Welcome to admin</p>
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
                        <h5>Locations Statistics</h5>
                        <div style="height: 350px; overflow-y: auto;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function getLast7Days() {
            const days = [];
            for (let i = 29; i >= 0; i--) {
                const d = new Date();
                d.setDate(d.getDate() - i);
                days.push(d.toISOString().split("T")[0]);
            }
            return days;
        }

        function loadChartData() {
            const requestCounts = JSON.parse(localStorage.getItem("askPlaceCounts")) || {};
            const labels = getLast7Days();
            const data = labels.map(date => requestCounts[date] || 0);

            const ctx = document.getElementById("earningsChart").getContext("2d");
            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Lượt nhấn Gửi",
                        data: data,
                        backgroundColor: "rgba(63, 61, 61, 0.6)",
                        borderColor: "rgb(12, 12, 12)",
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        loadChartData()
        //màu
        const generateColors = (count) => {
            const colors = [];
            for (let i = 0; i < count; i++) {
                const hue = Math.floor((360 / count) * i);
                colors.push(`hsl(${hue}, 70%, 60%)`);
            }
            return colors;
        };
        var ctx2 = document.getElementById('revenueChart').getContext('2d');

        // Tạo mảng tên các địa điểm và số lượng bài đánh giá từ dữ liệu backend
        var locationNames = @json($locationsReviews->pluck('name'));
        var locationCounts = @json($locationsReviews->pluck('user_reviews_count'));

        // Tạo biểu đồ tròn
        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: locationNames,
                datasets: [{
                    data: locationCounts,
                    backgroundColor: generateColors(locationNames.length),
                }]
            }, 
            options: {
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        labels: {
                            boxWidth: 10,
                            font: {
                                size: 10
                            }
                        }
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });
    </script>
</body>
</html>
