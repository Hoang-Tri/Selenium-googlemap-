<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset($settings['favicon_path'] ?? 'images/GMG.ico') }}">
    <title>{{ $settings['site_title'] ?? 'Trang Giao Diên Người Dùng' }}</title>
    <meta name="description" content="{{ $settings['meta_description'] ?? 'Hệ thống đánh giá dữ liệu từ Google Maps...' }}">
    <meta name="keywords" content="{{ $settings['meta_keywords'] ?? 'Google Maps, đánh giá địa điểm, nhận xét người dùng, trực quan hóa, biểu đồ' }}">
     <!-- Thẻ Open Graph giúp tối ưu hóa khi chia sẻ trên các mạng xã hội như Facebook -->
    <meta property="og:title" content="Admin Dashboard">
    <meta property="og:description" content="Hệ thống đánh giá dữ liệu từ Google Maps, phân tích nhận xét người dùng và trực quan hóa thông tin bằng biểu đồ.">
    <meta property="og:image" content="{{ asset('images/GMG.ico') }}">
    <meta property="og:url" content="http://lht_2110421:8080/nguoidung/dashboard">

    <link rel="stylesheet" href="{{ asset('css/style_nguoidung.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <!-- Thanh điều hướng (Navbar) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <h2 class="navbar-brand">Chào mừng, {{ Auth::user()->fullname }}!</h2>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Trang Chủ</a>
                    </li> -->
                    <li class="nav-item">
                        <a href="{{ route('nguoidung.sosanh') }}" class="nav-link active" aria-current="page" href="#">So sánh</a>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link active" data-bs-toggle="modal" data-bs-target="#infoModal">
                            Giới thiệu
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link active" data-bs-toggle="modal" data-bs-target="#contactModal">
                            Liên hệ
                        </button>
                    </li>
                    <li>
                        <div>
                            <button class="btn nav-link active" data-bs-toggle="modal" data-bs-target="#userProfileModal">
                                <i class="fas fa-user"></i> Profile
                            </button>
                        </div>
                    </li>
                    <li>
                        <div>
                            <button type="button" class="btn nav-link active" data-bs-toggle="modal" data-bs-target="#updateProfileModal">
                                Cập nhật thông tin
                            </button>
                        </div>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link active"> Logout</button>
                        </form>
                        
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestModalLabel">Nạp thêm lượt yêu cầu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="random-code" style="font-weight: bold;"></p>
                <input type="number" class="form-control" id="request-quantity" placeholder="Nhập số lượt yêu cầu thêm" min="1" />
                <input type="text" class="form-control mt-2" id="code-input" placeholder="Nhập mã xác nhận" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="verifyCodeAndAddRequests()">Xác nhận</button>
            </div>
            </div>
        </div>
    </div>

    <!-- Modal profile -->
    <div class="modal fade" id="userProfileModal" tabindex="-1" aria-labelledby="userProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userProfileModalLabel">Thông tin người dùng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <p><strong>Tên đăng nhập:</strong> {{ Auth::user()->username }}</p>
                <p><strong>Họ tên:</strong> {{ Auth::user()->fullname }}</p>
                <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                <p><strong>Vai trò:</strong> {{ Auth::user()->level == 1 ? 'admin' : 'user' }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
            </div>
        </div>
    </div>

    <!-- model cập nghật user -->
    <div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="updateProfileForm" method="POST" action="{{ route('user.update', Auth::user()->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateProfileModalLabel">Cập nhật thông tin người dùng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label><strong>Tên đăng nhập:</strong></label>
                            <input type="text" name="username" class="form-control" value="{{ Auth::user()->username }}" required>
                        </div>
                        <div class="mb-3">
                            <label><strong>Họ tên:</strong></label>
                            <input type="text" name="fullname" class="form-control" value="{{ Auth::user()->fullname }}" required>
                        </div>
                        <div class="mb-3">
                            <label><strong>Email:</strong></label>
                            <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label><strong>Mật khẩu mới:</strong></label>
                            <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới nếu muốn đổi">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal giới thiệu -->
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">Giới thiệu về cách sử dụng và chương trình dùng thử</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>Cách sử dụng:</h5>
                    <p>Đây là trang để bạn đánh giá các địa điểm. Bạn có thể tìm kiếm và gửi yêu cầu đánh giá cho các địa điểm mà bạn quan tâm.</p>
                    <h5>Chương trình dùng thử:</h5>
                    <p>Bạn có thể sử dụng tối đa 3 yêu cầu mỗi ngày miễn phí. Nếu bạn muốn gửi thêm yêu cầu, bạn có thể nạp tiền để mua thêm lượt yêu cầu.</p>
                    <h5>Nạp tiền:</h5>
                    <p>Để nạp tiền và mua thêm lượt yêu cầu, vui lòng nhấn vào nút "Nạp tiền" dưới đây. Mỗi lần nạp, bạn sẽ có thêm một số lượt yêu cầu bổ sung.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Liên hệ -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel">Liên hệ với chúng tôi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="contactForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên</label>
                            <input type="text" class="form-control" id="name" placeholder="Lê Hoàng Trí" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="tea@gmail.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" placeholder="0123456789" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" id="address" placeholder="An Bình - Ninh Kiều - Cần Thơ" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-success" onclick="submitContactForm()">Gửi thông tin</button>
                </div>
            </div>
        </div>
    </div>

    <div class="slogan-container">
       <h2>Trải nghiệm tốt bắt đầu từ quyết định đúng</h2>
    </div>

    <div class="container mt-4">
        <div class="mt-3">
            <button class="btn btn-warning" onclick="openRequestModal()">Thêm lượt yêu cầu</button>
            <p id="request-count">Số lần gửi còn lại hôm nay: ...</p>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="search-box">
                    <h3>Đánh giá địa điểm</h3>
                    @if($recentPlaces->count())
                        <div class="mb-2">
                            <small class="text-muted">Gợi ý địa điểm gần đây:</small>
                            <ul class="list-unstyled mb-2">
                                @foreach ($recentPlaces as $place)
                                    <li>
                                        <a href="javascript:void(0)" onclick="document.getElementById('place').value = '{{ $place }}'" class="text-primary">
                                            {{ $place }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="place" placeholder="Nhập tên địa điểm..." />
                        <button class="btn btn-primary" onclick="askPlace()">Gửi</button>
                    </div>
                    <p><strong>Phản hồi:</strong></p>
                    <div id="response-ask" class="response-box"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="search-box">
                    <h3>Biểu đồ đánh giá tổng quan</h3>
                    <canvas id="gptChart"></canvas>
                </div>
            </div>
        </div>
        
        <div id="chart-container">
            <!-- Nội dung biểu đồ sẽ chèn vào đây -->
        </div>
    <!-- lịch sử -->
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Lịch sử tìm kiếm địa điểm</strong>
                @if (!empty($history))
                    <form method="POST" action="{{ route('nguoidung.clearHistory') }}">
                        @csrf
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa toàn bộ lịch sử không?')">Xóa lịch sử</button>
                    </form>
                @endif
            </div>
            <div class="card-body">
                @if (!empty($history))
                    <ul class="list-group">
                        @foreach ($history as $item)
                            <li class="list-group-item">{{ $item }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>Chưa có lịch sử tìm kiếm.</p>
                @endif
            </div>
        </div>
    </div>
    <script>
        const MAX_REQUESTS_PER_DAY = 3;

        function getTodayKey() {
            return new Date().toISOString().split('T')[0];
        }

        function getRequestData() {
            return JSON.parse(localStorage.getItem("requestData")) || { counts: {}, extra: 0 };
        }

        function saveRequestData(data) {
            localStorage.setItem("requestData", JSON.stringify(data));
        }

        function canSendRequest() {
            const today = getTodayKey();
            const data = getRequestData();
            return (data.counts[today] || 0) < MAX_REQUESTS_PER_DAY || data.extra > 0;
        }

        function increaseRequestCount() {
            const today = getTodayKey();
            const data = getRequestData();

            const usedToday = data.counts[today] || 0;

            if (usedToday < MAX_REQUESTS_PER_DAY) {
                data.counts[today] = usedToday + 1; // Dùng lượt miễn phí
            } else if (data.extra > 0) {
                data.extra--; // Dùng lượt mua thêm
            } else {
                // Không có lượt, không làm gì cả
                alert("Bạn đã hết lượt!");
                return;
            }

            saveRequestData(data);
            updateRemainingRequestsDisplay();
        }

        function openRequestModal() {
            // Generate random 8-digit code
            const randomCode = generateRandomCode();
            document.getElementById("random-code").innerText = `Mã xác nhận: ${randomCode}`;

            // Store the random code in localStorage
            localStorage.setItem("randomCode", randomCode);

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('requestModal'));
            modal.show();
        }

        function generateRandomCode() {
            return Math.floor(10000000 + Math.random() * 90000000);  // Random 8-digit number
        }

        function verifyCodeAndAddRequests() {
            const enteredCode = document.getElementById("code-input").value;
            const storedCode = localStorage.getItem("randomCode");
            const quantity = parseInt(document.getElementById("request-quantity").value, 10);

            if (enteredCode === storedCode && quantity > 0) {
                // Add the specified number of requests
                const data = getRequestData();
                data.extra += quantity;
                saveRequestData(data);
                updateRemainingRequestsDisplay();
                alert(`${quantity} lượt yêu cầu đã được thêm thành công!`);
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('requestModal'));
                modal.hide();
            } else {
                alert("Mã xác nhận không đúng hoặc số lượng yêu cầu không hợp lệ. Vui lòng thử lại.");
            }
        }

        function updateRemainingRequestsDisplay() {
            const data = getRequestData();
            const today = getTodayKey();
            const remainingRequests = Math.max(0, MAX_REQUESTS_PER_DAY - (data.counts[today] || 0));
            const extraRequests = data.extra;
            document.getElementById("request-count").innerText = `Số lần gửi còn lại hôm nay: ${remainingRequests + extraRequests}`;
        }

        async function askPlace() {
            const place = document.getElementById("place").value.trim();
            if (!place) {
                alert("Vui lòng nhập tên địa điểm.");
                return;
            }
            const formattedPlace = place.replace(/\s+/g, '_');
            if (!canSendRequest()) {
                alert("Bạn đã hết lượt gửi yêu cầu hôm nay!");
                return;
            }
            const responseElement = document.getElementById("response-ask");
            responseElement.innerText = "Đang gửi yêu cầu...";

            try {
                // Request to your external API to get reviews and other data
                const res = await fetch("http://localhost:60074/base/chat-ingestion/", {
                    method: "POST",
                    headers: {
                        "accept": "application/json",
                        "Content-Type": "application/x-www-form-urlencoded",
                        "API-Key": "gnqAYAVeDMR7dzocBfH5j89O4oXUPpEa"
                    },
                    body: new URLSearchParams({ Place: formattedPlace  })
                });

                const data = await res.json();
                if (res.ok) {
                    try {
                        const parsed = JSON.parse(data.data);
                        function normalizeString(str) {
                            return str
                                .trim()
                                .toLowerCase()
                                .normalize("NFD")                  
                                .replace(/[\u0300-\u036f]/g, '')   
                                .replace(/[^a-z0-9]/g, '')         // BỎ luôn cả gạch dưới, space, ký tự đặc biệt
                        }

                        const normalizedInput = normalizeString(place);
                        const reviews = parsed["Đánh giá địa điểm"].filter(item =>
                            normalizedInput === normalizeString(item.Place)
                        );
                        
                        let html = ""; 
                        reviews.forEach(item => {
                            html += `
                                <div class="card">
                                    <button onclick="loadChart(${item.ID})">Xem thông tin</button>
                                </div>
                            `;
                        });
                        responseElement.innerHTML = html;

                    } catch (err) {
                        console.error("Lỗi khi xử lý dữ liệu phản hồi", err);
                    }
                    
                    let requestCounts = JSON.parse(localStorage.getItem("askPlaceCounts")) || {};
                    const today = new Date().toISOString().split("T")[0]; // YYYY-MM-DD
                    requestCounts[today] = (requestCounts[today] || 0) + 1;
                    localStorage.setItem("askPlaceCounts", JSON.stringify(requestCounts));

                    // Thêm vào lịch sử
                    if (place) {
                        // addHistoryItem(`Đánh giá địa điểm: ${place}`);
                        await fetch("/save-history", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                            },
                            body: JSON.stringify({ place }),
                        });
                    }
                    if (!canSendRequest()) {
                        responseBox.innerHTML = "Bạn đã vượt quá số lần yêu cầu miễn phí trong ngày. Hãy nạp tiền để gửi thêm yêu cầu.";
                        return;
                    }
                    increaseRequestCount();
                } else {
                    console.log(data)
                    responseElement.innerHTML = `<div>Không tìm thấy thông tin của ${place}. Đã gửi yêu cầu cho admin, vui lòng thử lại sau</div>`;
                    let notifications = JSON.parse(localStorage.getItem("adminNotifications")) || [];
                    notifications.push(`Hãy cung cấp thông tin địa chỉ cho địa điểm: ${place}`);
                    localStorage.setItem("adminNotifications", JSON.stringify(notifications));
                                    
                    return;
                }
                window.loadChart = async function(location_id) {
                    console.log("loadChart được gọi với location_id:", location_id);
                    if (location_id) {
                        try {
                            const res = await fetch(`/nguoidung/chart/${location_id}`);
                            if (res.ok) {
                                const htmlFromServer = await res.text();
                                document.getElementById("chart-container").innerHTML = htmlFromServer;
                            } else {
                                console.error("Không thể lấy dữ liệu từ controller", res.status);
                            }
                        } catch (err) {
                            console.error("Lỗi khi fetch dữ liệu từ controller", err);
                        }
                    } else {
                        console.error("location_id không hợp lệ");
                    }
                };
            } catch (error) {
                responseElement.innerText = "Lỗi khi gọi API: " + error;
            }
        }

        window.addEventListener("DOMContentLoaded", function () {
            const message = localStorage.getItem("successMessage");
            if (message) {
                
                alert(message);  

                localStorage.removeItem("successMessage");
            }
        });

        window.onload = function () {
            updateRemainingRequestsDisplay();
        }

        // Biểu đồ đánh giá
        const chartData = @json($chartData);

        const ctx = document.getElementById('gptChart').getContext('2d');

        const labels = chartData.length > 0 ? chartData.map(item => item.name) : [];
        const dataTot = chartData.length > 0 ? chartData.map(item => item.phan_tram_tot) : [];
        const dataXau = chartData.length > 0 ? chartData.map(item => item.phan_tram_xau) : [];

        const gptChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '% Tốt',
                        data: dataTot,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)'
                    },
                    {
                        label: '% Xấu',
                        data: dataXau,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { 
                        display: true, 
                        text: chartData.length > 0 ? 'Đánh giá cho địa điểm đã chọn' : 'Chưa có địa điểm được chọn' 
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
