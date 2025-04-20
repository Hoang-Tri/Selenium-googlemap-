<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Giao Diện So Sánh</title>
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
                    <li class="nav-item">
                        <a href="{{ route('nguoidung.dashboard') }}" class="nav-link active" aria-current="page" href="#">Trang Chủ</a>
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
    <div class="container-fluid mt-3 px-4 ">
        <div class="mt-3">
            <button class="btn btn-warning" onclick="addExtraRequest()">Nạp tiền để gửi thêm yêu cầu</button>
            <p id="request-count">Số lần gửi còn lại hôm nay: ...</p>
        </div>
        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-12">
                    <div class="search-box p-4 shadow rounded bg-light">
                    <h3>So sánh địa điểm</h3>
                        <div class="row g-3 align-items-center">
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="placeA" placeholder="Nhập địa điểm A...">
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="placeB" placeholder="Nhập địa điểm B...">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100" onclick="askComparePlace()">Gửi</button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <p><strong>Phản hồi:</strong></p>
                            <div id="response-ask" class="response-box"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div id="chart-container-1" style="width: 50vh; height: 100%;"></div>
            </div>
            <div class="col-md-6">
                <div id="chart-container-2" style="width: 50vh; height: 100%x;"></div>
            </div>
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

        async function askComparePlace() {
            const placeA = document.getElementById("placeA").value.trim();
            const placeB = document.getElementById("placeB").value.trim();

            if (!placeA || !placeB) {
                alert("Vui lòng nhập cả hai địa điểm.");
                return;
            }

            if (!canSendRequest()) {
                alert("Bạn đã hết lượt gửi yêu cầu hôm nay!");
                return;
            }

            const responseElement = document.getElementById("response-ask");
            responseElement.innerText = "Đang gửi yêu cầu...";

            try {
                const [resA, resB] = await Promise.all([
                    fetch("http://localhost:60074/base/chat-ingestion/", {
                        method: "POST",
                        headers: {
                            "accept": "application/json",
                            "Content-Type": "application/x-www-form-urlencoded",
                            "API-Key": "gnqAYAVeDMR7dzocBfH5j89O4oXUPpEa"
                        },
                        body: new URLSearchParams({ Place: placeA })
                    }),
                    fetch("http://localhost:60074/base/chat-ingestion/", {
                        method: "POST",
                        headers: {
                            "accept": "application/json",
                            "Content-Type": "application/x-www-form-urlencoded",
                            "API-Key": "gnqAYAVeDMR7dzocBfH5j89O4oXUPpEa"
                        },
                        body: new URLSearchParams({ Place: placeB })
                    })
                ]);

                const dataA = await resA.json();
                const dataB = await resB.json();

                let html = "";

                if (resA.ok && resB.ok) {
                    const parsedA = JSON.parse(dataA.data);
                    const parsedB = JSON.parse(dataB.data);

                    const reviewsA = parsedA["Đánh giá địa điểm"];
                    const reviewsB = parsedB["Đánh giá địa điểm"];

                    // Nút xem tất cả biểu đồ
                    html += `
                        <div class="text-center mb-3">
                            <button onclick="loadAllCharts()" class="btn btn-primary">Xem thông tin</button>
                        </div>
                    `;

                    // Danh sách cho Place A
                    html += `
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-center">Phản hồi cho <strong>${placeA}</strong></h5>
                    `;

                    reviewsA.forEach(item => {
                        const chartContainerId = `chart-container-a-${item.ID}`;
                        html += `
                            <div class="card mb-2 p-2">
                                <div id="${chartContainerId}" class="mt-2" data-location-id="${item.ID}"></div>
                            </div>
                        `;
                    });

                    html += `
                            </div> <!-- end col A -->
                            <div class="col-md-6">
                                <h5 class="text-center">Phản hồi cho <strong>${placeB}</strong></h5>
                    `;

                    reviewsB.forEach(item => {
                        const chartContainerId = `chart-container-b-${item.ID}`;
                        html += `
                            <div class="card mb-2 p-2">
                                <div id="${chartContainerId}" class="mt-2" data-location-id="${item.ID}"></div>
                            </div>
                        `;
                    });

                    html += `
                            </div> <!-- end col B -->
                        </div> <!-- end row -->
                    `;
                    html += `
                        <h3 class="text-center">So sánh giữa <strong>${placeA}</strong> và <strong>${placeB}</strong></h3>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Thông tin</th>
                                    <th>${placeA}</th>
                                    <th>${placeB}</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    reviewsA.forEach((itemA, index) => {
                        const itemB = reviewsB[index]; 

                        html += `
                            <tr>
                                <td>Địa chỉ</td>
                                <td>${itemA.Address}</td>
                                <td>${itemB.Address}</td>
                            </tr>
                            <tr>
                                <td>Tỷ lệ đánh giá tốt (%)</td>
                                <td>${itemA.Good}%</td>
                                <td>${itemB.Good}%</td>
                            </tr>
                            <tr>
                                <td>Tỷ lệ đánh giá xấu (%)</td>
                                <td>${itemA.Bad}%</td>
                                <td>${itemB.Bad}%</td>
                            </tr>
                            <tr>
                                <td>Kết luận</td>
                                <td>${itemA.Conclusion}</td>
                                <td>${itemB.Conclusion}</td>
                            </tr>
                            <tr>
                                <td>Lý do</td>
                                <td>${itemA.Because}</td>
                                <td>${itemB.Because}</td>
                            </tr>
                            <tr>
                                <td>Hướng khắc phục</td>
                                <td>${itemA["Remedial direction"]}</td>
                                <td>${itemB["Remedial direction"]}</td>
                            </tr>
                        `;
                    });

                    html += `
                            </tbody>
                        </table>
                    `;
                    // Lưu lịch sử
                    addHistoryItem(`So sánh địa điểm: ${placeA} & ${placeB}`);

                    increaseRequestCount();

                    // Cập nhật số lượt gửi yêu cầu
                    let requestCounts = JSON.parse(localStorage.getItem("askPlaceCounts")) || {};
                    const today = new Date().toISOString().split("T")[0];
                    requestCounts[today] = (requestCounts[today] || 0) + 2;
                    localStorage.setItem("askPlaceCounts", JSON.stringify(requestCounts));
                } else {
                    html = "Không thể lấy dữ liệu cho một trong hai địa điểm.";
                }

                responseElement.innerHTML = html;

                if (!canSendRequest()) {
                    responseElement.innerHTML += "<p class='text-danger mt-2'>Bạn đã vượt quá số lần yêu cầu miễn phí trong ngày.</p>";
                }
                increaseRequestCount();

            } catch (error) {
                responseElement.innerText = "Lỗi khi gọi API: " + error;
            }
        }

        // Gắn ngoài hàm chính
        async function loadChart(location_id, containerId) {
            try {
                const res = await fetch(`/nguoidung/chart/${location_id}`);
                if (res.ok) {
                    const htmlFromServer = await res.text();
                    document.getElementById(containerId).innerHTML = htmlFromServer;
                } else {
                    console.error("Không thể lấy dữ liệu từ controller", res.status);
                }
            } catch (err) {
                console.error("Lỗi khi fetch dữ liệu từ controller", err);
            }
        }

        // Load tất cả biểu đồ
        function loadAllCharts() {
            const containers = document.querySelectorAll("div[data-location-id]");
            containers.forEach(div => {
                const id = div.getAttribute("data-location-id");
                const containerId = div.id;
                loadChart(id, containerId);
            });
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
    </script>
</body>
</html>
