<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset($settings['favicon_path'] ?? 'images/GMG.ico') }}">
    <title>Trang Giao Diện So Sánh</title>
    <link rel="stylesheet" href="{{ asset('css/style_nguoidung.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

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
            <button class="btn btn-warning" onclick="openRequestModal()">Thêm lượt yêu cầu</button>
            <p id="request-count">Số lần gửi còn lại hôm nay: ...</p>
        </div>
        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-12">
                    <div class="search-box p-4 shadow rounded bg-light">
                    <h3>So sánh địa điểm</h3>
                        <div class="row g-3 align-items-center">
                            <div class="col-md-5 position-relative">
                                <input type="text" class="form-control" id="placeA" placeholder="Nhập địa điểm A..." autocomplete="off">
                                <div class="dropdown-menu w-100" id="suggestionsA"></div>
                            </div>

                            <div class="col-md-5 position-relative">
                                <input type="text" class="form-control" id="placeB" placeholder="Nhập địa điểm B..." autocomplete="off">
                                <div class="dropdown-menu w-100" id="suggestionsB"></div>
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

        async function askComparePlace() {
            const placeA = document.getElementById("placeA").value.trim();
            const placeB = document.getElementById("placeB").value.trim();

            if (!placeA || !placeB) {
                alert("Vui lòng nhập cả hai địa điểm.");
                return;
            }

            const formattedPlaceA = placeA.replace(/\s+/g, '_');
            const formattedPlaceB = placeB.replace(/\s+/g, '_');

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
                        body: new URLSearchParams({ Place: formattedPlaceA })
                    }),
                    fetch("http://localhost:60074/base/chat-ingestion/", {
                        method: "POST",
                        headers: {
                            "accept": "application/json",
                            "Content-Type": "application/x-www-form-urlencoded",
                            "API-Key": "gnqAYAVeDMR7dzocBfH5j89O4oXUPpEa"
                        },
                        body: new URLSearchParams({ Place: formattedPlaceB })
                    })
                ]);

                const dataA = await resA.json();
                const dataB = await resB.json();

                let html = "";

                if (resA.ok && resB.ok) {
                    const parsedA = JSON.parse(dataA.data);
                    const parsedB = JSON.parse(dataB.data);

                    function normalizeString(str) {
                        function normalizeString(str) {
                            return str
                                .trim()
                                .toLowerCase()
                                .normalize("NFD")                  
                                .replace(/[\u0300-\u036f]/g, '')   
                                .replace(/[^a-z0-9]/g, '')         // BỎ luôn cả gạch dưới, space, ký tự đặc biệt
                        }
                    }
                    const normalizedInputA = normalizeString(formattedPlaceA);
                    const normalizedInputB = normalizeString(formattedPlaceB);

                    const reviewsA = parsedA["Đánh giá địa điểm"].filter(item =>
                        normalizeString(item.Place) === normalizedInputA
                    );

                    const reviewsB = parsedB["Đánh giá địa điểm"].filter(item =>
                        normalizeString(item.Place) === normalizedInputB
                    )

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

                    const maxLength = Math.max(reviewsA.length, reviewsB.length);
                    for (let i = 0; i < maxLength; i++) {
                        const itemA = reviewsA[i] || {};
                        const itemB = reviewsB[i] || {};

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
                    };

                    html += `
                            </tbody>
                        </table>
                    `;

                    // Lưu lịch sử
                    await fetch("/save-history", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        },
                        body: JSON.stringify({ place: `So sánh địa điểm: ${placeA} & ${placeB}` }),
                    });

                    increaseRequestCount();

                    // Cập nhật số lượt gửi yêu cầu
                    let requestCounts = JSON.parse(localStorage.getItem("askPlaceCounts")) || {};
                    const today = new Date().toISOString().split("T")[0];
                    requestCounts[today] = (requestCounts[today] || 0) + 2;
                    localStorage.setItem("askPlaceCounts", JSON.stringify(requestCounts));
                } else {
                    html = "<p>Không thể lấy dữ liệu cho một trong hai địa điểm. Đã gửi yêu cầu cho admin xử lý.</p>";
                    let notifications = JSON.parse(localStorage.getItem("adminNotifications")) || [];
                    if (!resA.ok) notifications.push(`Hãy cung cấp thông tin địa chỉ cho địa điểm: ${placeA}`);
                    if (!resB.ok) notifications.push(`Hãy cung cấp thông tin địa chỉ cho địa điểm: ${placeB}`);
                    localStorage.setItem("adminNotifications", JSON.stringify(notifications));
                }

                responseElement.innerHTML = html;

                if (!canSendRequest()) {
                    responseElement.innerHTML += "<p class='text-danger mt-2'>Bạn đã vượt quá số lần yêu cầu miễn phí trong ngày.</p>";
                }

            } catch (error) {
                responseElement.innerText = "Lỗi khi gọi API: " + error;
            }
        }

        // Gắn ngoài hàm chính
        async function loadChart(location_id, containerId) {
            try {
                const res = await fetch(`/nguoidung/chart-sosanh/${location_id}`);
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

        $(document).ready(function() {
            function bindAutocomplete(inputId, dropdownId) {
                const input = $('#' + inputId);
                const dropdown = $('#' + dropdownId);

                input.on('input focus', function() {
                    let query = input.val().trim();
                    if (query.length === 0) {
                        dropdown.removeClass('show');
                        return;
                    }

                    $.ajax({
                        url: '/places/search',
                        method: 'GET',
                        data: { query: query },  // phải khớp với tên biến trong Laravel controller
                        success: function(data) {
                            dropdown.empty();
                            if (data.length > 0) {
                                data.forEach(function(place) {
                                    dropdown.append(`<button class="dropdown-item" type="button">${place.name}</button>`);
                                });
                                dropdown.addClass('show');
                            } else {
                                dropdown.removeClass('show');
                            }
                        }
                    });
                });

                dropdown.on('click', '.dropdown-item', function() {
                    input.val($(this).text());
                    dropdown.removeClass('show');
                });

                // Ẩn dropdown khi click ra ngoài
                $(document).on('click', function(e) {
                    if (!input.is(e.target) && !dropdown.is(e.target) && dropdown.has(e.target).length === 0) {
                        dropdown.removeClass('show');
                    }
                });
            }

            bindAutocomplete('placeA', 'suggestionsA');
            bindAutocomplete('placeB', 'suggestionsB');
        });

        // Load tất cả biểu đồ
        function loadAllCharts() {
            const containers = document.querySelectorAll("div[data-location-id]");
            containers.forEach(div => {
                const id = div.getAttribute("data-location-id");
                const containerId = div.id;
                loadChart(id, containerId);
            });
        }

        window.onload = function () {
            updateRemainingRequestsDisplay();
        }
    </script>
</body>
</html>
