<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset($settings['favicon_path'] ?? 'images/GMG.ico') }}">
    <title>Admin Dashboard</title>
    <meta name="description" content="Hệ thống đánh giá dữ liệu từ Google Maps, phân tích nhận xét người dùng và trực quan hóa thông tin bằng biểu đồ.">
    <meta name="keywords" content="Google Maps, đánh giá địa điểm, nhận xét người dùng, trực quan hóa, biểu đồ, phân tích dữ liệu">
     <!-- Thẻ Open Graph giúp tối ưu hóa khi chia sẻ trên các mạng xã hội như Facebook -->
    <meta property="og:title" content="Admin Dashboard">
    <meta property="og:description" content="Hệ thống đánh giá dữ liệu từ Google Maps, phân tích nhận xét người dùng và trực quan hóa thông tin bằng biểu đồ.">
    <meta property="og:image" content="{{ asset('images/GMG.ico') }}">
    <meta property="og:url" content="http://lht_2110421:8080/dashboard">

    <link rel="stylesheet" href="{{ asset('css/style_dash.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="d-flex">
    <button id="toggleSidebar">
        <i class="fa fa-bars"></i>
    </button>
        <div class="sidebar" id="sidebar">
            <h2>Chào mừng admin</h2>
            <ul id="menu">
                <a href="{{ url('/users') }}">
                    <i class="fas fa-users"></i> User
                </a>
                <a href="{{ url('/locations') }}">
                    <i class="fas fa-map-marker-alt"></i> Location
                </a>
                <a href="{{ url('/google') }}">
                    <i class="fas fa-globe"></i> GoogleMaps
                </a>
                <a href="{{ url('/users-reviews') }}">
                    <i class="fas fa-comment-dots"></i> User Review
                </a>
                <a href="{{ url('/upload') }}">
                    <i class="fas fa-upload"></i> File
                </a>
                <a href="{{ url('/settings') }}">
                    <i class="fas fa-cogs"></i> Setting
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </ul>
            <div class="contact-info">
                Contact: lehoangtri@gmail.com
            </div>
        </div>

        <div class="main-content">
            <nav class="navbar">
                <div class="container-fluid d-flex justify-content-between">
                    <div id="notification-icon" class="notification-icon" onclick="showNotifications()">
                        <i class="fa fa-bell"></i>
                        <span id="notification-count"></span>
                    </div>
                </div>

                <!-- Modal thông báo -->
                <div id="notification-modal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeModal()">&times;</span>
                        <div id="notifications"></div>
                    </div>
                </div>
            </nav>

            <div class="row">
                <div class="col-md-3">
                    <a href="{{ url('/users') }}" style="text-decoration: none; color: inherit;">
                        <div class="card text-center p-3 card-hover" style="border: 2px solid rgb(35, 64, 230); background-color:rgb(136, 177, 238);">
                            <i class="fas fa-users"></i>
                            <h5>Users</h5>
                            <p>{{ $userCount }}</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ url('/locations') }}" style="text-decoration: none; color: inherit;">
                        <div class="card text-center p-3 card-hover" style="border: 2px solid #ff4d4d; background-color:rgb(238, 143, 143);">
                            <i class="fas fa-map-marker-alt"></i>
                            <h5>Locations</h5>
                            <p>{{ $locationCount }}</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ url('/users-reviews') }}" style="text-decoration: none; color: inherit;">
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
                        <p><a href="{{ route('nguoidung.dashboard') }}" style="text-decoration: none; color: inherit;">Trang người dùng</a></p>
                        
                    </div>
                </div>
            </div>
            
            <div id="notifications"></div>
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
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });

        window.onload = function() {
            displayNotifications();
        };

        // Hàm hiển thị thông báo
        function displayNotifications() {
            const notifications = JSON.parse(localStorage.getItem("adminNotifications")) || [];
            const notificationsElement = document.getElementById("notifications");
            const notificationCountElement = document.getElementById("notification-count");

            if (notifications.length > 0) {
                let html = '<ul class="notification-list">';
                notifications.forEach((notification, index) => {
                    const location = notification.replace("Hãy cung cấp thông tin địa chỉ cho địa điểm: ", "");
                    html += `
                        <li class="notification-item">
                            <span class="notification-text">${notification}</span>
                            <div class="notification-buttons">
                                <button class="btn-ok" onclick="markAsRead(${index}, '${location}')">OK</button>
                                <button class="btn-delete" onclick="deleteNotification(${index})">Xóa</button>
                            </div>
                        </li>
                    `;
                });
                html += "</ul>";
                notificationsElement.innerHTML = html;
                notificationCountElement.textContent = notifications.length;
            } else {
                notificationsElement.innerHTML = "<p>Không có thông báo mới.</p>";
                notificationCountElement.textContent = "";
            }
        }

        function deleteNotification(index) {
            let notifications = JSON.parse(localStorage.getItem("adminNotifications")) || [];
            notifications.splice(index, 1);
            localStorage.setItem("adminNotifications", JSON.stringify(notifications));
            displayNotifications(); // Cập nhật lại hiển thị
        }

        function extractPlace(notification) {
            const match = notification.match(/địa điểm: (.+)/);
            return match ? match[1] : '';
        }

        function showNotifications() {
            document.getElementById("notification-modal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("notification-modal").style.display = "none";
        }

        function markAsRead(index, place) {
            let notifications = JSON.parse(localStorage.getItem("adminNotifications")) || [];
            notifications.splice(index, 1);
            localStorage.setItem("adminNotifications", JSON.stringify(notifications));

            displayNotifications();

            // Chuyển hướng sang trang /google với place là tham số trên URL
            window.location.href = `http://lht_2110421:8080/google?place=${encodeURIComponent(place)}`;
        }

        function saveNotification(place) {
            let notifications = JSON.parse(localStorage.getItem("adminNotifications")) || [];
            notifications.push(`Hãy cung cấp thông tin địa chỉ cho địa điểm: ${place}`);
            localStorage.setItem("adminNotifications", JSON.stringify(notifications));
        }

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
