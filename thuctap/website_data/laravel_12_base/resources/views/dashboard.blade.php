<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/style_admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
@extends('layouts.app')

@section('content')
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="{{ url('/category') }}">Category</a></li>
            <li><a href="{{ url('/users') }}">Users</a></li>
            <li><a href="{{ url('/posts') }}">Posts</a></li>
            <li><a href="{{ url('/pages') }}">Pages</a></li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </li>
        </ul>
    </div>

    <div class="main-content image-dashboard">
        <!-- Sửa đường dẫn ảnh -->
        <!-- <img src="{{ asset('images/maps.jpg') }}" alt="Dashboard Image" class="dashboard-img"> -->
        
        <div class="welcome-overlay">
            <h1>Welcome, {{ Auth::user()->name }}</h1>
            <p>This is your admin dashboard.</p>
        </div>
    </div>
</body>
@endsection
</html>
