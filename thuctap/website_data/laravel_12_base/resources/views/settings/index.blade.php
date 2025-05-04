<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset($settings['favicon_path'] ?? 'images/GMG.ico') }}">
    <title>Danh sách File CSV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        h2 {
            font-size: 1.5rem;
        }
        .form-label {
            font-weight: 600;
        }
        .form-control {
            transition: box-shadow 0.2s ease;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
    </style>

</head>
<body class="bg-light">
    @include('layouts.header')

    <div class="container mt-5">
        <div class="card shadow-lg rounded-4 border-0">
            <div class="card-body p-4">
                <h2 class="mb-4 text-primary fw-bold border-bottom pb-2">⚙️ Cấu hình trang web</h2>

                @if (session('success'))
                    <div class="alert alert-success rounded-3">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                    {{-- SEO & giao diện --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">🏷️ Tiêu đề trang (title)</label>
                        <input type="text" name="site_title" class="form-control" value="{{ $settings['site_title'] ?? '' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">📝 Mô tả trang (meta description)</label>
                        <textarea name="meta_description" class="form-control">{{ $settings['meta_description'] ?? '' }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">🔑 Từ khóa (meta keywords)</label>
                        <input type="text" name="meta_keywords" class="form-control" value="{{ $settings['meta_keywords'] ?? '' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">🌐 Favicon (ảnh .ico)</label>
                        <input type="file" name="favicon_path" class="form-control">
                        @if(!empty($settings['favicon_path']))
                            <p class="mt-2">Hiện tại: <img src="{{ asset($settings['favicon_path']) }}" width="32"></p>
                        @endif
                    </div>

                    <h2 class="mb-4 text-primary fw-bold border-bottom pb-2">⚙️ Cấu hình hệ thống AI</h2>

                    {{-- Mô hình sử dụng --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Mô hình sử dụng</label>
                        <div class="d-flex gap-4 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ai_provider" id="provider_openai" value="openai"
                                    @if(($settings['ai_provider'] ?? '') === 'openai') checked @endif>
                                <label class="form-check-label" for="provider_openai">OpenAI</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ai_provider" id="provider_gemini" value="gemini"
                                    @if(($settings['ai_provider'] ?? '') === 'gemini') checked @endif>
                                <label class="form-check-label" for="provider_gemini">Gemini</label>
                            </div>
                        </div>
                    </div>

                    {{-- API Key --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">🔑 API Key</label>
                        <input type="text" name="ai_api_key" class="form-control rounded-3 shadow-sm" value="{{ $settings['ai_api_key'] ?? '' }}" required>
                    </div>

                    {{-- Tên mô hình LLM --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">🧠 Tên model LLM</label>
                        <input type="text" name="model_llm" class="form-control rounded-3 shadow-sm" value="{{ $settings['model_llm'] ?? '' }}" required>
                    </div>

                    {{-- Số lượng tài liệu --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">📄 NUM DOC (không bắt buộc)</label>
                        <input type="number" name="num_doc" class="form-control rounded-3 shadow-sm" value="{{ $settings['num_doc'] ?? '' }}">
                    </div>

                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">💾 Lưu cấu hình</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
