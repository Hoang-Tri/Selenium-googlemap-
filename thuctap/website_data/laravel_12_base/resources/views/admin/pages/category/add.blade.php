@extends('admin.template.theme')

@section('title', 'Add Category')

@section('header', 'Add New Category')

@section('content')
    @if(session('noti'))
        <div class="alert alert-success">
            {{ session('noti') }}
        </div>
    @endif

    <form action="{{ route('category.add_category') }}" method="post">
        @csrf  
        
        <label >Title Category</label>
        <input class = "form_control" type="text" name="title_cate" required/>
        
        <button type = "submit", class = "btn btn-primary">Add Category</button>
    </form>

@endsection
