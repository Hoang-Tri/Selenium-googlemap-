<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoogleMaps</title>
    <link rel="stylesheet" href="{{ asset('css/style_google.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
@include('layouts.header')
    <div class="d-flex">
        <!-- <div class="sidebar">
            <h2>User Page</h2>
            <ul>
                <li><a href="{{ url('/dashboard') }}">Admin Dashboard</a></li>
                <li><a href="{{ url('/category') }}">Category</a></li>
                <li><a href="{{ url('/posts') }}">Posts</a></li>
                <li><a href="{{ url('/pages') }}">Pages</a></li>
                <li><a href="{{ url('/google') }}">GoogleMaps</a></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
                    </form>
                </li>
            </ul>
            <div class="contact-info">
                Contact: lehoangtri@gmail.com
            </div> -->
        </div>
        <div class="main-content">
            <div class="row mt-4">
                <div class="d-flex justify-content-between">
                    <div class="search-box">
                        <h3>Tìm kiếm thông tin địa điểm</h3>
                        <div class="input-group">
                            <input type="text" id="keywords" placeholder="Nhập từ khóa tìm kiếm..." />
                            <button onclick="crawlPlaces()">Tìm kiếm</button>
                        </div>
                        <p><strong>Phản hồi:</strong></p>
                        <div id="response-crawl" class="response-box"></div>
                    </div>

                    <!-- Ô Đánh giá địa điểm -->
                    <div class="search-box">
                        <h3>Đánh giá địa điểm</h3>
                        <div class="input-group">
                            <input type="text" id="place" placeholder="Nhập tên địa điểm..." />
                            <button onclick="askPlace()">Gửi</button>
                        </div>
                        <p><strong>Phản hồi:</strong></p>
                        <div id="response-ask" class="response-box"></div>
                    </div>
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
    </div>

    <script>
        function updateDailyRequests() {
            let dailyRequests = JSON.parse(localStorage.getItem("dailyRequests")) || new Array(30).fill(0);
            let today = new Date().getDate(); // Lấy ngày hiện tại
            dailyRequests[today - 1] += 1; // Tăng số yêu cầu trong ngày hôm nay
            localStorage.setItem("dailyRequests", JSON.stringify(dailyRequests)); // Lưu lại vào localStorage
        }
        async function crawlPlaces() {
            const keywords = document.getElementById("keywords").value;
            const responseElement = document.getElementById("response-crawl");
            responseElement.innerText = "Đang tìm kiếm...";

            try {
                const res = await fetch("http://localhost:60074/base/start/", {
                    method: "POST",
                    headers: {
                        "accept": "application/json",
                        "Content-Type": "application/x-www-form-urlencoded",
                        "API-Key": "gnqAYAVeDMR7dzocBfH5j89O4oXUPpEa"
                    },
                    body: new URLSearchParams({ keywords: keywords })
                });

                const data = await res.json();

                if (res.ok) {
                    responseElement.innerText = "Dữ liệu đã được crawl thành công!";
                } else {
                    responseElement.innerText = "Lỗi: " + data.detail;
                }
            } catch (error) {
                responseElement.innerText = "Lỗi khi gọi API: " + error;
            }
        }

        async function askPlace() {
            const place = document.getElementById("place").value.trim();
            const responseElement = document.getElementById("response-ask");
            responseElement.innerText = "Đang gửi yêu cầu...";

            try {
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
                    responseElement.innerText = data.data;
                    updateDailyRequests();

                    // Thêm địa điểm vào lịch sử nếu có phản hồi thành công
                    if (place) {
                        addHistoryItem(`Đánh giá địa điểm: ${place}`);
                    }

                } else {
                    responseElement.innerText = "Lỗi: " + data.detail;
                }
            } catch (error) {
                responseElement.innerText = "Lỗi khi gọi API: " + error;
            }
        }

        function addHistoryItem(text) {
            const historyList = document.getElementById("history-list");
            const li = document.createElement("li");
            li.textContent = text;
            historyList.appendChild(li);

            // Lưu lịch sử vào localStorage
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

        // Tải lịch sử khi trang được tải
        window.addEventListener("DOMContentLoaded", loadHistory);
    </script>
</body>
</html>
