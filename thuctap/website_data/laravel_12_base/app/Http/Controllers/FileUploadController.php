<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\File; // Model để lưu thông tin file vào DB

class FileUploadController extends Controller
{
    // Hiển thị trang upload file
    public function index()
    {
        return view('upload.index');
    }

    // Xử lý upload file
    public function upload(Request $request)
    {
        // Kiểm tra file có hợp lệ không
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,docx|max:10240', // Giới hạn kích thước và kiểu file
        ]);

        // Lưu file vào thư mục public/uploads
        $path = $request->file('file')->store('uploads', 'public');

        // Lưu thông tin file vào cơ sở dữ liệu (Model File)
        $file = new File();
        $file->name = $request->file('file')->getClientOriginalName();
        $file->path = $path;
        $file->save();

        // Trả về thông báo thành công
        return back()->with('success', 'File uploaded successfully!');
    }
}
