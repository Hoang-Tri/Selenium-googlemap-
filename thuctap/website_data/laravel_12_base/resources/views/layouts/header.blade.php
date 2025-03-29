<!-- views/layouts/header.php -->
<header style="background-color: #2c2f33; color: white; padding: 15px;">
    <h1 style="margin: 0;">Admin Dashboard</h1>
    <nav>
        <a href="{{ url('/dashboard') }}" style="color: white;margin-right: 15px; ">Dashboard</a>
        <a href="{{ url('/users') }}" style="color: white; margin-right: 15px;">Users</a>
        <a href="{{ url('/posts') }}" style="color: white; margin-right: 15px;">Posts</a>
        <a href="{{ url('/pages') }}" style="color: white;">Pages</a>
        
    </nav>
</header>

