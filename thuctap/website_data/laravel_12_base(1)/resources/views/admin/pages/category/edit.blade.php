@extends('admin.template.theme')

@section('title', 'Edit Category')

@section('header', 'Edit Category')

@section('content')
    @if(session('noti'))
        <div class="alert alert-success">{{ session('noti') }}</div>
    @endif

    <form action="{{ route('category.update', $category->id) }}" method="POST">
        @csrf  

        <label>Title Category</label>
        <input class="form-control" type="text" name="title_cate" value="{{ $category->title_cate }}" required/>

        <button type="submit" class="btn btn-primary">Update Category</button>
    </form>
@endsection
