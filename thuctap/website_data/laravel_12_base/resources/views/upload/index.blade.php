<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách File CSV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    @include('layouts.header')


    <h1>Danh sách địa điểm có đánh giá</h1>

<ul class="list-group">
    @foreach($locations as $location)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ $location->name }} - 
            <a href="{{ url('/download/' . $location->location_id) }}" class="btn btn-sm btn-primary">
                Tải về
            </a>
        </li>
    @endforeach
</ul>
</body>
</html>
