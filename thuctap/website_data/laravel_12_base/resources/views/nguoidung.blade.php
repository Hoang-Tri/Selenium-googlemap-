<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Giao Diện Người Dùng</title>
    <link rel="stylesheet" href="{{ asset('css/style_nguoidung.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

    <div class="modal fade" id="extraRequestModal" tabindex="-1" aria-labelledby="extraRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="extraRequestModalLabel">Yêu cầu thêm lượt gửi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form gửi yêu cầu thêm -->
                <form action="https://formspree.io/f/your-form-id" method="POST">
                    <label for="email">Email của bạn:</label>
                    <input type="email" id="email" name="email" required class="form-control">
                    <br>
                    <label for="username">Tên của bạn:</label>
                    <input type="text" id="username" name="username" required class="form-control">
                    <br>
                    <label for="quantity">Số lượt muốn thêm:</label>
                    <input type="number" id="quantity" name="quantity" required class="form-control">
                    <br>
                    <label for="reason">Lý do:</label>
                    <textarea id="reason" name="reason" required class="form-control"></textarea>
                    <br>
                    <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
                </form>
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
                    <button type="button" class="btn btn-warning" onclick="addExtraRequest()">Nạp tiền</button>
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
            <!-- <button class="btn btn-warning" onclick="addExtraRequest()">Nạp tiền để gửi thêm yêu cầu</button> -->
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#extraRequestModal">Nạp tiền để gửi thêm yêu cầu</button>
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
    
        <div class="row mt-4">
            <div class="history-box mx-auto">
                <h4>Lịch sử đánh giá</h4>
                <ul id="history-list"></ul>
                <button class="btn btn-danger mt-2" onclick="clearHistory()">Xóa toàn bộ lịch sử</button>
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
            data.counts[today] = (data.counts[today] || 0) + 1;
            if (data.counts[today] > MAX_REQUESTS_PER_DAY) {
                data.extra--;
            }
            saveRequestData(data);
            updateRemainingRequestsDisplay();
        }

        function addExtraRequest() {
            const data = getRequestData();
            data.extra += 1;
            saveRequestData(data);
            updateRemainingRequestsDisplay();
            alert("Bạn đã nạp thành công 1 yêu cầu thêm!");
        }

        async function askPlace() {
            const place = document.getElementById("place").value.trim();
            if (!place) {
                alert("Vui lòng nhập tên địa điểm.");
                return;
            }
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
                    body: new URLSearchParams({ Place: place })
                });

                const data = await res.json();

                if (res.ok) {
                    try {
                        const parsed = JSON.parse(data.data);
                        const reviews = parsed["Đánh giá địa điểm"];
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
                        addHistoryItem(`Đánh giá địa điểm: ${place}`);
                    }
                    if (!canSendRequest()) {
                        responseBox.innerHTML = "Bạn đã vượt quá số lần yêu cầu miễn phí trong ngày. Hãy nạp tiền để gửi thêm yêu cầu.";
                        return;
                    }
                    increaseRequestCount();
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
        function updateRemainingRequestsDisplay() {
            const today = getTodayKey();
            const data = getRequestData();
            const count = data.counts[today] || 0;
            const extra = data.extra || 0;
            const remaining = Math.max(0, MAX_REQUESTS_PER_DAY - count) + extra;

            const counterElement = document.getElementById("request-count");
            if (counterElement) {
                counterElement.textContent = `Số lần gửi còn lại hôm nay: ${remaining}`;
            }
        }

        function addHistoryItem(text) {
            const historyList = document.getElementById("history-list");
            const li = document.createElement("li");
            li.textContent = text;
            historyList.appendChild(li);

            let history = JSON.parse(localStorage.getItem("placeHistory")) || [];
            history.push(text);
            localStorage.setItem("placeHistory", JSON.stringify(history));
        }

        function loadHistory() {
            const historyList = document.getElementById("history-list");
            const history = JSON.parse(localStorage.getItem("placeHistory")) || [];

            history.forEach(item => {
                const li = document.createElement("li");
                li.textContent = item;
                historyList.appendChild(li);
            });
        }

        function clearHistory() {
            localStorage.removeItem("placeHistory");
            document.getElementById("history-list").innerHTML = "";
        }

        window.onload = function () {
            loadHistory();
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
        document.addEventListener('DOMContentLoaded', function () {
            const rawData = @json($userReviewChartData->toArray());

            // ==== Biểu đồ ====
            const filteredData = rawData.filter(item => item.star > 0);
            const allDates = [...new Set(filteredData.map(item => item.date))].sort();
            const groupedByUser = {};

            filteredData.forEach(item => {
                const userLabel = `User ${item.id}`;
                if (!groupedByUser[userLabel]) {
                    groupedByUser[userLabel] = {};
                }
                groupedByUser[userLabel][item.date] = item.star;
            });

            const datasets = Object.entries(groupedByUser).map(([userLabel, dateStars], index) => {
                const data = allDates.map(date => dateStars[date] || 0); 
                return {
                    label: userLabel,
                    data: data,
                    backgroundColor: `hsl(${index * 60}, 70%, 60%)`
                };
            });

            const ctxUser = document.getElementById('userReviewChart').getContext('2d');
            new Chart(ctxUser, {
                type: 'bar',
                data: {
                    labels: allDates,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        title: {
                            display: true,
                            text: 'Biểu đồ đánh giá của người dùng'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5,
                            title: {
                                display: true,
                                text: 'Số sao'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Ngày đánh giá'
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
