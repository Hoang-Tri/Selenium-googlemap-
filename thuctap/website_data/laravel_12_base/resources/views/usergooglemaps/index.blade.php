<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset($settings['favicon_path'] ?? 'images/GMG.ico') }}">
    <title>GoogleMaps</title>
    <link rel="stylesheet" href="{{ asset('css/style_google.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
@include('layouts.header')
    <div class="main-content">
        <div class="row mt-4">
            <div class="d-flex justify-content-between">
                <div class="search-box">
                    <h3 style= "text-align: center; font-weight: 600;">Tìm kiếm thông tin địa điểm</h3>
                    <div class="input-group">
                        <input type="text" id="keywords" placeholder="Nhập từ khóa tìm kiếm..." />
                        <button onclick="crawlPlaces()">Tìm kiếm</button>
                    </div>
                    <p><strong>Phản hồi:</strong></p>
                    <div id="response-crawl" class="response-box"></div>
                    <button id="downloadBtn" class="btn btn-success mt-3" style="display:none" onclick="downloadCSV()">Tải về CSV</button>
                </div>
                <div class="search-box">
                    <h3 style= "text-align: center; font-weight: 600;">Đánh giá địa điểm</h3>
                    <div class="input-group">
                        <input type="text" id="place" placeholder="Nhập tên địa điểm..." />
                        <button onclick="askPlace()">Gửi</button>
                    </div>
                    <p><strong>Phản hồi:</strong></p>
                    <div id="response-ask" class="response-box"></div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <div class="history-box mx-auto">
                    <h4>Lịch sử tìm kiếm</h4>
                    <ul id="search-history-list"></ul>
                    <button class="btn btn-danger mt-2" onclick="clearSearchHistory()">Xóa toàn bộ lịch sử tìm kiếm</button>
                </div>

                    <!-- Ô Đánh giá địa điểm -->
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
            let today = new Date().getDate(); 
            dailyRequests[today - 1] += 1; 
            localStorage.setItem("dailyRequests", JSON.stringify(dailyRequests)); 
        }

        let searchResults = [];

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
                    
                    searchResults = data.results || [];  
                    if (!place) addSearchHistoryItem(keywords);

                    addSearchHistoryItem(keywords);

                    localStorage.setItem("successMessage", `Đã có dữ liệu của địa điểm: ${keywords}`);
                } else {
                    responseElement.innerText = "Lỗi: " + data.detail;
                }
            } catch (error) {
                responseElement.innerText = "Lỗi khi gọi API: " + error;
            }
        }

        window.addEventListener("DOMContentLoaded", function () {
            const urlParams = new URLSearchParams(window.location.search);
            const place = urlParams.get("place");

            if (place) {
                document.getElementById("keywords").value = place;
                crawlPlaces();  // Gọi hàm crawl
            }
        });

        window.onload = function () {
            loadSearchHistory();
        };

         // Thêm từ khóa tìm kiếm vào lịch sử
        function addSearchHistoryItem(keyword) {
            const historyList = document.getElementById("search-history-list");
            const li = document.createElement("li");
            li.textContent = keyword;
            historyList.appendChild(li);

            // Lưu lịch sử tìm kiếm vào localStorage
            let history = JSON.parse(localStorage.getItem("searchHistory")) || [];
            history.push(keyword);
            localStorage.setItem("searchHistory", JSON.stringify(history));
        }

        // Tải lịch sử tìm kiếm
        function loadSearchHistory() {
            const historyList = document.getElementById("search-history-list");
            const history = JSON.parse(localStorage.getItem("searchHistory")) || [];

            history.forEach(item => {
                const li = document.createElement("li");
                li.textContent = item;
                historyList.appendChild(li);
            });
        }

        // Xóa lịch sử tìm kiếm
        function clearSearchHistory() {
            localStorage.removeItem("searchHistory");
            document.getElementById("search-history-list").innerHTML = ''; // Xóa danh sách lịch sử hiển thị
        }

        // Tạo file CSV từ dữ liệu tìm kiếm
        function downloadCSV() {
            if (searchResults.length === 0) {
                alert("Không có dữ liệu để tải về!");
                return;
            }

            const csvRows = [];
            const headers = Object.keys(searchResults[0]); // Tạo header từ các khóa trong dữ liệu
            csvRows.push(headers.join(',')); // Chuyển header thành chuỗi CSV

            // Duyệt qua dữ liệu và thêm vào csvRows
            searchResults.forEach(row => {
                const values = headers.map(header => row[header]);
                csvRows.push(values.join(','));
            });

            // Tạo file Blob từ chuỗi CSV
            const csvFile = new Blob([csvRows.join('\n')], { type: 'text/csv' });

            // Tạo link tải file CSV
            const downloadLink = document.createElement('a');
            downloadLink.href = URL.createObjectURL(csvFile);
            downloadLink.download = 'search_results.csv';  // Đặt tên file tải về
            downloadLink.click();
        }

        async function askPlace() {
            const place = document.getElementById("place").value.trim();
            const formattedPlace = place.replace(/\s+/g, '_');

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
                    body: new URLSearchParams({ Place: formattedPlace })
                });

                const data = await res.json();

                if (res.ok) {
                    // responseElement.innerText = data.data;
                    try {
                        const parsed = JSON.parse(data.data);
                        const reviews = parsed["Đánh giá địa điểm"].filter(item =>
                            item.Place.trim().toLowerCase() === formattedPlace .trim().toLowerCase()
                        );
                        let html = "";

                        reviews.forEach(item => {
                            html += `
                            <div class="card">
                                <h3>🏞️ ${item.Place.replaceAll('_', ' ')}</h3>
                                <p><strong>📍 Địa chỉ:</strong> ${item.Address}</p>
                                <p><strong>✅ Kết luận:</strong> ${item.Conclusion}</p>
                                <p><strong>💬 Lý do:</strong> ${item.Because}</p>
                                <p><strong>🛠️ Hướng khắc phục:</strong> ${item["Remedial direction"]}</p>
                            </div>
                            `;
                        });
                        if (parsed["SoSanhChung"]) {
                            const comparison = parsed["SoSanhChung"];
                            html += `<div class="comparison-box">
                                <h3>📊 So sánh chung</h3>
                                <ul>
                                    ${comparison.Ranking.map(item => `<li>${item}</li>`).join("")}
                                </ul>
                                <p><strong>🏆 Địa điểm được đề xuất:</strong> ${comparison.RecommendedPlace}</p>
                                <p><strong>📌 Lý do:</strong> ${comparison.Reason}</p>
                            </div>`;
                        }
                        responseElement.innerHTML = html;
                    } catch (e) {
                        responseElement.innerText = "Không thể đọc dữ liệu từ hệ thống.";
                    }

                    let requestCounts = JSON.parse(localStorage.getItem("askPlaceCounts")) || {};
                    const today = new Date().toISOString().split("T")[0]; // YYYY-MM-DD
                    requestCounts[today] = (requestCounts[today] || 0) + 1;
                    localStorage.setItem("askPlaceCounts", JSON.stringify(requestCounts));

                    // Thêm vào lịch sử
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
        function updateDailyRequests() {
            const today = new Date();
            const day = today.getDate() - 1; // Chỉ số từ 0-29

            let dailySubmitRequests = JSON.parse(localStorage.getItem("dailySubmitRequests")) || new Array(30).fill(0);
            dailySubmitRequests[day] = (dailySubmitRequests[day] || 0) + 1;

            localStorage.setItem("dailySubmitRequests", JSON.stringify(dailySubmitRequests));
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
        function clearHistory() {
            // Xoá dữ liệu trong localStorage
            localStorage.removeItem("placeHistory");

            // Xoá các phần tử hiển thị trong HTML
            const historyList = document.getElementById("history-list");
            historyList.innerHTML = '';
        }
        // Tải lịch sử khi trang được tải
        window.addEventListener("DOMContentLoaded", loadHistory);
    </script>
</body>
</html>
