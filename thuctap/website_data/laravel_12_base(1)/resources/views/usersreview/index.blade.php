<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Review Page</title>
    <link rel="stylesheet" href="{{ asset('css/style_google.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
@include('layouts.header')
    <div class="d-flex">
        <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Users Review</h2>
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
                    <th>Id Location</th>
                    <th>Users review</th>
                    <th>Data_llm</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usersreview as $review)
                    <tr>
                        <td>{{ $review->id }}</td>
                        <td>{{ $review->location ? $review->location->name : 'N/A' }}</td>
                        <td>{{ $review->user_review }}</td>
                        <td>{{ $review->data_llm }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
