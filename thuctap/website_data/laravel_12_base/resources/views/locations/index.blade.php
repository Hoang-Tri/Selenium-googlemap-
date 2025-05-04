<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset($settings['favicon_path'] ?? 'images/GMG.ico') }}">
    <title>Location Page</title>
    <link rel="stylesheet" href="{{ asset('css/style_google.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .truncate-text {
            display: -webkit-box;
            -webkit-line-clamp: 3; /* Số dòng hiển thị */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-height: 4.5em; /* Chiều cao tương ứng 3 dòng */
            position: relative;
        }
    </style>
</head>
<body>
@include('layouts.header')
    <div class="d-flex">
        <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Locations</h2>
        </div>

        @if(session('noti'))
            <div class="alert alert-success">
                {{ session('noti') }}
            </div>
        @endif

        <table class="table table-striped table-hover shadow rounded">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Data_llm</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($locations as $locaion)
                    <tr>
                        <td>{{ $locaion->id }}</td>
                        <td>{{ $locaion->name }}</td>
                        <td>{{ $locaion->address }}</td>
                        <td>
                            <div class="truncate-text" id="short-text-{{ $locaion->id }}">
                                {{ $locaion->data_llm }}
                            </div>
                            <a href="javascript:void(0);" onclick="toggleFullText({{ $locaion->id }})" id="toggle-btn-{{ $locaion->id }}">Xem thêm</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script>
        function toggleFullText(id) {
            const textDiv = document.getElementById(`short-text-${id}`);
            const toggleBtn = document.getElementById(`toggle-btn-${id}`);

            if (textDiv.classList.contains("truncate-text")) {
                textDiv.classList.remove("truncate-text");
                toggleBtn.innerText = "Thu gọn";
            } else {
                textDiv.classList.add("truncate-text");
                toggleBtn.innerText = "Xem thêm";
            }
        }
    </script>
</body>
</html>
