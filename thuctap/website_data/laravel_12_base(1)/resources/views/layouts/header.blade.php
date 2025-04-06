<!-- views/layouts/header.php -->
<header style="background-color: #2c2f33; color: white; padding: 15px;">
    <h1 style="margin: 0;">Admin Dashboard</h1>
    <nav>
        <a href="{{ url('/dashboard') }}" style="color: white;margin-right: 15px; ">Dashboard</a>
        <a href="{{ url('/users') }}" style="color: white; margin-right: 15px;">Users</a>
        <a href="{{ url('/locations') }}" style="color: white; margin-right: 15px;">Location</a>
        <a href="{{ url('/google') }}" style="color: white; margin-right: 15px;">GoogleMaps</a>
        <a href="{{ url('/users-reviews') }}" style="color: white; margin-right: 15px;">User Review</a>
        <a href="{{ url('/upload') }}" style="color: white; margin-right: 15px;">UploadFile</a>
    </nav>
</header>
