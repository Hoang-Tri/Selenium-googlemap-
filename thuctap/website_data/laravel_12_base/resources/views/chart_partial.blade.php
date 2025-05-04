<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset($settings['favicon_path'] ?? 'images/GMG.ico') }}">
    <title>Trang Giao Diện Người Dùng</title>
    <link rel="stylesheet" href="{{ asset('css/style_nguoidung.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .word-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            padding: 10px;
            border-radius: 10px;
            background-color: #f5f5f5;
            margin-bottom: 30px;
        }

        .word {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: bold;
            transition: transform 0.2s ease-in-out;
        }

        .word:hover {
            transform: scale(1.2);
            opacity: 0.9;
        }

        .word.good {
            background-color: #d4f4d7;
            color: #1b5e20;
        }

        .word.bad {
            background-color: #fddede;
            color: #b71c1c;
        }
        /*  */
    </style>
</head>
<body>
    <!-- box chỉ số đánh giá -->
    <div id="charts-reviews-container" >        
        <div class="row text-center mb-4">
            <div class="col-md-4 mb-3">
                <div class="p-4 bg-info text-white rounded shadow">
                    <h5>Trung bình sao</h5>
                    <h2><i class="fas fa-star text-warning"></i> {{ $averageRating }}</h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-4 bg-primary text-white rounded shadow">
                    <h5>Tích Cực</h5>
                    <h2><i class="fas fa-thumbs-up"></i> {{ number_format($phan_tram_tot, 2) }}%</h2>
                </div> 
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-4 bg-danger text-white rounded shadow">
                    <h5>Tiêu Cực</h5>
                    <h2><i class="fas fa-thumbs-down"></i> {{ number_format($phan_tram_xau, 2) }}%</h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-4 bg-success text-white rounded shadow">
                    <h5>Tổng số bình luận hài lòng</h5>
                    <h2><i class="fas fa-smile"></i> {{ ($totalGoodCount) }}</h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-4 bg-dark text-white rounded shadow">
                    <h5>Tổng số bình luận không hài lòng</h5>
                    <h2><i class="fas fa-frown"></i> {{ ($totalBadCount) }}</h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-4 bg-warning text-white rounded shadow">
                    <h5>Điểm cảm xúc trung bình</h5>
                    <h2><i class="fas fa-meh"></i> {{ ($sentimentScore ) }}</h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-4 bg-light text-dark rounded shadow">
                    <h5>Đặc sắc </h5>
                    <h2><i class="text-warning"></i> {{ $ratingPercentages['Đặc sắc'] ?? 0 }}%</h2>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="p-4 bg-light text-dark rounded shadow">
                    <h5>Trung tính </h5>
                    <h2><i class="text-warning"></i> {{ $ratingPercentages['Trung tính'] ?? 0 }}%</h2>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="p-4 bg-light text-dark rounded shadow">
                    <h5>Tệ </h5>
                    <h2><i class="text-dark"></i> {{ $ratingPercentages['Tệ'] ?? 0 }}%</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Top 5 chỉ số cảm xúc -->
    <!-- <h2 class="title">Top 5 Người Dùng Có Chỉ Số Cảm Xúc AI Cao Nhất</h2>
    <div class="hexagon-container">
        <div class="hexagon">
            @foreach ($labels as $index => $user)
                @php
                    $angle = 360 / count($labels) * $index;
                    $radius = 100; // bán kính vòng tròn của các user
                    $x = cos(deg2rad($angle - 90)) * $radius; // Tính toán tọa độ X
                    $y = sin(deg2rad($angle - 90)) * $radius; // Tính toán tọa độ Y
                @endphp
                <div class="hex-point" style="--x: {{ $x }}px; --y: {{ $y }}px;">
                    <div class="user-circle">
                        <div class="value">{{ $counts[$index] }}</div>
                        <div class="label">{{ $user }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div> -->
     <!--Độ tin cậy và NPS  -->
    <div class="row">
        @php
            $trustColor = $trustScore >= 80 ? '#4CAF50' : ($trustScore >= 50 ? '#FFC107' : '#f44336');
            $trustPercent = min($trustScore, 100) . '%';
        @endphp
        <div class="col-md-6 mb-3">
            <div class="circle-score-box" style="--circle-color: {{ $trustColor }}; --circle-percent: {{ $trustPercent }};">
                <div class="circle-label">
                    Điểm Tin Cậy<br>
                    <i class="fas fa-shield-alt"></i> {{ number_format($trustScore) }}/100
                </div>
                <div class="circle-tooltip">
                    Mức độ tin cậy hiện tại: {{ number_format($trustScore) }}
                </div>
            </div>
        </div>

        @php
            $color = $npsScore >= 50 ? '#4CAF50' : ($npsScore >= 0 ? '#FF9800' : '#f44336');
            $percent = min(abs($npsScore), 100) . '%';
            $npsName = "Đánh giá NPS"; 
        @endphp

        <div class="col-md-6 mb-3">
            <div class="nps-box" style="--nps-color: {{ $color }}; --nps-percent: {{ $percent }};">
                <div class="nps-label">{{ $npsLabel }}</div>
                <div class="nps-name">{{ $npsName }}</div> 
                <div class="nps-tooltip">NPS: {{ round($npsScore, 1) }}</div>
            </div>
        </div>
    </div>

    <div class="chart-row mt-3">
        <!-- Biểu đồ Mâu thuẫn -->
        <div class="chart-col-20">
            <div class="chart-card">
                <div class="chart-wrapper-chart">
                    <div class="chart-bars">
                        <div class="chart-col-container">
                            <div class="chart-bar chart-bar-normal" style="height: {{ $noConflictCount * 2 }}px;"></div>
                            <span class="chart-label">Bình thường</span>
                            <span class="chart-value">{{ $noConflictCount }}</span>
                        </div>
                        <div class="chart-col-container">
                            <div class="chart-bar chart-bar-conflict" style="height: {{ $conflictCount * 5 }}px;"></div>
                            <span class="chart-label">Mâu thuẫn</span>
                            <span class="chart-value">{{ $conflictCount }}</span>
                        </div>
                    </div>
                    <div class="chart-title">Cảm xúc</div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ Từ Tốt -->
        <div class="chart-col-40">
            <div class="chart-card">
                <div class="chart-wrapper-chart">
                    <div class="chart-bars word-chart">
                        @foreach ($listWords_chart['tot'] as $word => $count)
                            <div class="chart-col-container">
                                <span class="chart-word">{{ $word }}</span>
                                <div class="chart-bar chart-bar-good" style="height: {{ $count * 10 }}px;"></div>
                                <span class="chart-value">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="chart-title">Từ tốt</div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ Từ Xấu -->
        <div class="chart-col-40">
            <div class="chart-card">
                <div class="chart-wrapper-chart">
                    <div class="chart-bars word-chart">
                        @foreach ($listWords_chart['xau'] as $word => $count)
                            <div class="chart-col-container">
                                <span class="chart-word">{{ $word }}</span>
                                <div class="chart-bar chart-bar-bad" style="height: {{ $count * 10 }}px;"></div>
                                <span class="chart-value">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="chart-title">Từ xấu</div>
                </div>
            </div>
        </div>
    </div>
    <!-- word cloud -->
    <div class="container mt-3">
        <div class="card shadow">
            <div class="word-cloud-container">
                <div class="card-header bg-success text-white">
                    <strong>Từ tích cực</strong>
                </div>
                <div class="word-cloud-box">
                @php $loopCount = 0; @endphp
                    @foreach ($listWords['tot'] as $word => $count)
                        @php
                            $fontSize = 12 + $count * 5;
                            $top = rand(0, 250);
                            $left = rand(0, 250);
                            $rotate = rand(-45, 45);
                            $extraClass = $loopCount >= 20 ? 'd-none extra-tot' : '';
                            $loopCount++;
                        @endphp
                        <span class="good {{ $extraClass }}" style="
                            font-size: {{ $fontSize }}px;
                            top: {{ $top }}px;
                            left: {{ $left }}px;
                            transform: rotate({{ $rotate }}deg);
                            color: white;
                        ">
                            {{ $word }}
                        </span>
                    @endforeach
                    <button class="btn btn-link p-0 mt-2" onclick="toggleExtra('extra-tot', this)">Xem thêm</button>
                </div>

                <div class="card-header bg-danger text-white">
                    <strong>Từ tiêu cực</strong>
                </div>
                <div class="word-cloud-box">
                @php $loopCount = 0; @endphp
                    @foreach ($listWords['xau'] as $word => $count)
                        @php
                            $fontSize = 12 + $count * 5;
                            $top = rand(0, 250);
                            $left = rand(0, 250);
                            $rotate = rand(-45, 45);
                            $extraClass = $loopCount >= 20 ? 'd-none extra-xau' : '';
                            $loopCount++;
                        @endphp
                        <span class="bad {{ $extraClass }}" style="
                            font-size: {{ $fontSize }}px;
                            top: {{ $top }}px;
                            left: {{ $left }}px;
                            transform: rotate({{ $rotate }}deg);
                            color: white;
                        ">
                            {{ $word }}
                        </span>
                    @endforeach
                    <button class="btn btn-link p-0 mt-2" onclick="toggleExtra('extra-xau', this)">Xem thêm</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Line chart tốt xấu -->
    @php
        $maxUsers = count($userLabels);
        $height = 200;
        $padding = 40;
        $widthPerPoint = 15; 
    @endphp

    <div class="container chart-users-percent mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <strong>Biểu đồ mức độ hài lòng và không hài lòng</strong>
                <div>
                    <span class="badge bg-info me-2">Tốt</span>
                    <span class="badge bg-danger">Xấu</span>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container-user">
                    <svg width="{{ $maxUsers * $widthPerPoint + $padding * 2 }}" height="{{ $height + $padding * 2 }}">
                        {{-- Trục Y (0 - 100%) --}}
                        @for ($i = 0; $i <= 100; $i += 20)
                            @php
                                $y = $padding + $height - ($i / 100) * $height;
                            @endphp
                            <text x="5" y="{{ $y + 4 }}" class="y-label-user">{{ $i }}%</text>
                            <line x1="{{ $padding }}" y1="{{ $y }}" x2="{{ $maxUsers * $widthPerPoint + $padding }}" y2="{{ $y }}" stroke="#ccc" stroke-dasharray="2,2"/>
                        @endfor

                        {{-- Polyline tốt (xanh) --}}
                        @php $pointsGood = ''; @endphp
                        @foreach ($goodPercents as $i => $val)
                            @php
                                $x = $padding + $i * $widthPerPoint;
                                $y = $padding + $height - ($val / 100) * $height;
                                $pointsGood .= "$x,$y ";
                            @endphp
                        @endforeach
                        <polyline points="{{ trim($pointsGood) }}" class="line-good" />

                        {{-- Polyline xấu (đỏ) --}}
                        @php $pointsBad = ''; @endphp
                        @foreach ($badPercents as $i => $val)
                            @php
                                $x = $padding + $i * $widthPerPoint;
                                $y = $padding + $height - ($val / 100) * $height;
                                $pointsBad .= "$x,$y ";
                            @endphp
                        @endforeach
                        <polyline points="{{ trim($pointsBad) }}" class="line-bad" />

                        {{-- Các điểm và tên người dùng --}}
                        @foreach ($userLabels as $i => $label)
                            @php
                                $x = $padding + $i * $widthPerPoint;
                                $yGood = $padding + $height - ($goodPercents[$i] / 100) * $height;
                                $yBad = $padding + $height - ($badPercents[$i] / 100) * $height;
                            @endphp
                            <circle cx="{{ $x }}" cy="{{ $yGood }}" r="4" class="circle-good" title="Tốt: {{ $goodPercents[$i] }}%"/>
                            <circle cx="{{ $x }}" cy="{{ $yBad }}" r="4" class="circle-bad" title="Xấu: {{ $badPercents[$i] }}%"/>
                            <text x="{{ $x }}" y="{{ $padding + $height + 15 }}" class="x-label-user" transform="rotate(45, {{ $x }}, {{ $padding + $height + 15 }})">{{ $label }}</text>
                        @endforeach
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <!-- Tỷ lệ cảm xúc sao/comment -->
    <div class="container mt-3">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <strong>Trực quan hóa đánh giá người dùng dựa trên tỷ lệ cảm xúc</strong>
            </div>
            <div class="review-scroll-wrapper">
                <div class="review-grid">
                    @foreach ($rawreviews as $rawreview)
                        <div class="review-box {{ $rawreview['mau_thuan'] ? 'mau-thuan' : '' }}">
                            <div class="stars">
                                Gốc:
                                @for ($i = 0; $i < floor($rawreview['original_star']); $i++)
                                    ★
                                @endfor
                            </div>
                            <div class="stars">
                                LLM:
                                @for ($i = 0; $i < floor($rawreview['adjusted_star']); $i++)
                                    ★
                                @endfor
                            </div>

                            <div class="chart-bar">
                                <div class="bar good" style="width: {{ $rawreview['percent_good'] }}%" data-tooltip="{{ $rawreview['percent_good'] }}% tốt"></div>
                                <div class="bar bad" style="width: {{ $rawreview['percent_bad'] }}%" data-tooltip="{{ $rawreview['percent_bad'] }}% xấu"></div>
                            </div>

                            <div class="review-text">"{{ $rawreview['review'] }}"</div>

                            @if ($rawreview['mau_thuan'])
                                <p class="conflict">⚠️ Mâu thuẫn sao & nội dung!</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <!-- Đánh giá sao theo ngày -->
    <div class="container mt-3">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <strong>Biểu đồ đánh giá sao theo ngày</strong>
                <div class="mt-2">
                    <span class="badge" style="background-color: hsl(120, 70%, 60%); color: white;">5★</span>
                    <span class="badge" style="background-color: hsl(90, 70%, 60%); color: white;">4★</span>
                    <span class="badge" style="background-color: hsl(60, 70%, 60%); color: white;">3★</span>
                    <span class="badge" style="background-color: hsl(30, 70%, 60%); color: white;">2★</span>
                    <span class="badge" style="background-color: hsl(0, 70%, 60%); color: white;">1★</span>
                </div>
            </div>
            <div class="card-body">
                @php
                    $groupedReviews = $userReviewChartData->groupBy('date')->sortKeys();
                @endphp
                <div style="overflow: hidden;">
                    <div class="d-flex" style="flex-wrap: nowrap; overflow: hidden;">
                        @foreach ($groupedReviews as $date => $reviews)
                            <div class="review-column text-center" style="flex: 0 0 calc(100% / {{ count($groupedReviews) }}); margin-right: 2px; font-size: 10px;">
                                <div class="fw-bold mb-2">{{ \Carbon\Carbon::parse($date)->format('d/m/y') }}</div>
                                <div class="d-flex justify-content-center gap-1">
                                    @foreach ($reviews as $review)
                                        @php
                                            $heightPercent = min(5, $review->star) * 20;
                                            $colorHue = 120 - (5 - $review->star) * 30;
                                        @endphp
                                        <div class="review-item" style="margin-right: 1px;">
                                            <div style="height: 100px; display: flex; align-items: flex-end; justify-content: center;">
                                                <div style="
                                                    height: {{ $heightPercent }}%;
                                                    width: 4px;
                                                    background-color: hsl({{ $colorHue }}, 70%, 60%);
                                                    border-radius: 2px;
                                                    cursor: pointer;"
                                                    title="{{ $review->username }} - {{ $review->star }}★">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Xu hướng đánh giá -->
    @php
        $dates = $groupedReviews->keys()->sort()->values();
        $counts = $groupedReviews->map->count();
        $maxCount = max($counts->values()->toArray()) ?: 1;
        $widthPerPoint = 50;
        $height = 200;
        $padding = 40;

        // Tối ưu trục Y: Nếu quá nhiều giá trị, tăng bước
        if ($maxCount <= 10) {
            $stepY = 1;
        } elseif ($maxCount <= 20) {
            $stepY = 2;
        } elseif ($maxCount <= 50) {
            $stepY = 5;
        } elseif ($maxCount <= 100) {
            $stepY = 10;
        } else {
            $stepY = ceil($maxCount / 10);
        }
    @endphp

    <div class="container chart-card">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <strong>Biểu đồ xu hướng đánh giá</strong>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <svg width="{{ count($dates) * $widthPerPoint + $padding * 2 }}" height="{{ $height + $padding * 2 }}">
                    {{-- Trục Y và nhãn --}}
                        @for ($i = 0; $i <= $maxCount; $i += $stepY)
                            @php
                                $y = $padding + $height - ($i / $maxCount) * $height;
                            @endphp
                            <text x="5" y="{{ $y + 4 }}" class="y-label">{{ $i }}</text>
                            <line x1="{{ $padding }}" y1="{{ $y }}" x2="{{ count($dates) * $widthPerPoint + $padding }}" y2="{{ $y }}" stroke="#ccc" stroke-dasharray="2,2"/>
                        @endfor

                        {{-- Chuỗi polyline --}}
                        @php $points = ""; @endphp
                        @foreach ($dates as $i => $date)
                            @php
                                $x = $padding + $i * $widthPerPoint;
                                $count = $groupedReviews[$date]->count();
                                $y = $padding + $height - ($count / $maxCount) * $height;
                                $points .= "$x,$y ";
                            @endphp
                        @endforeach
                        <polyline points="{{ trim($points) }}" class="line-path"/>

                        {{-- Vẽ các điểm và nhãn ngày --}}
                        @foreach ($dates as $i => $date)
                            @php
                                $x = $padding + $i * $widthPerPoint;
                                $count = $groupedReviews[$date]->count();
                                $y = $padding + $height - ($count / $maxCount) * $height;
                            @endphp
                            <circle cx="{{ $x }}" cy="{{ $y }}" r="4" class="circle-point"/>
                            <text x="{{ $x }}" y="{{ $padding + $height + 15 }}" class="x-label">{{ \Carbon\Carbon::parse($date)->format('d/m') }}</text>
                        @endforeach
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <!-- trung bình sao theo ngày và năm -->
    <div class="container mt-3">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <strong>Biểu đồ đánh giá trung bình theo ngày và năm</strong>
                <div class="mt-2">
                    <span class="badge" style="background-color: hsl(120, 70%, 60%); color: white;">5★</span>
                    <span class="badge" style="background-color: hsl(90, 70%, 60%); color: white;">4★</span>
                    <span class="badge" style="background-color: hsl(60, 70%, 60%); color: white;">3★</span>
                    <span class="badge" style="background-color: hsl(30, 70%, 60%); color: white;">2★</span>
                    <span class="badge" style="background-color: hsl(0, 70%, 60%); color: white;">1★</span>
                </div>
            </div>
            <div class="card-body">
                @php
                    $avgByDate = $userReviewChartData->groupBy('date')->map(fn($group) => $group->avg('star'))->toArray();
                    ksort($avgByDate);
                    $avgByYear = $userReviewChartData->groupBy(fn($r) => \Carbon\Carbon::parse($r->date)->format('Y'))
                                    ->map(fn($group) => $group->avg('star'))->toArray();
                    ksort($avgByYear);
                @endphp

                <div class="d-flex flex-wrap justify-content-between gap-4">
                    <!-- Theo ngày -->
                    <div style="flex: 1; min-width: 320px;">
                        <h5 class="text-center">Biểu đồ trung bình theo ngày</h5>
                        <div class="d-flex flex-column gap-2 mb-2">
                            @foreach ($avgByDate as $date => $avg)
                                @php
                                    $widthPercent = ($avg / 5) * 100;
                                    $colorHue = 120 - (5 - $avg) * 30;
                                    $label = \Carbon\Carbon::parse($date)->format('d/m/y');
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div style="width: 50px; font-size: 11px;">{{ $label }}</div>
                                    <div style="flex: 1; background-color: #eee; height: 12px; border-radius: 5px; margin: 0 8px; position: relative;">
                                        <div style="width: {{ $widthPercent }}%; height: 100%; background-color: hsl({{ $colorHue }}, 70%, 60%); border-radius: 5px;"
                                            title="Ngày {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} - {{ number_format($avg, 2) }} ★">
                                        </div>
                                    </div>
                                    <div style="width: 40px; font-size: 11px; text-align: right;">{{ number_format($avg, 1) }}★</div>
                                </div>
                            @endforeach
                        </div>
                        <!-- Trục sao -->
                        <div class="d-flex" style="margin-left: 58px; margin-top: 4px;">
                            @for ($i = 1; $i <= 5; $i++)
                                <div style="flex: 1; text-align: center; font-size: 10px;">{{ $i }}★</div>
                            @endfor
                        </div>
                    </div>

                    <!-- Theo năm -->
                    <div style="flex: 1; min-width: 320px;">
                        <h5 class="text-center">Biểu đồ trung bình theo năm</h5>
                        <div class="d-flex flex-column gap-2 mb-2">
                            @foreach ($avgByYear as $year => $avg)
                                @php
                                    $widthPercent = ($avg / 5) * 100;
                                    $colorHue = 120 - (5 - $avg) * 30;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div style="width: 50px; font-size: 11px;">{{ $year }}</div>
                                    <div style="flex: 1; background-color: #eee; height: 12px; border-radius: 5px; margin: 0 8px; position: relative;">
                                        <div style="width: {{ $widthPercent }}%; height: 100%; background-color: hsl({{ $colorHue }}, 70%, 60%); border-radius: 5px;"
                                            title="Năm {{ $year }} - {{ number_format($avg, 2) }} ★">
                                        </div>
                                    </div>
                                    <div style="width: 40px; font-size: 11px; text-align: right;">{{ number_format($avg, 1) }}★</div>
                                </div>
                            @endforeach
                        </div>
                        <!-- Trục sao -->
                        <div class="d-flex" style="margin-left: 58px; margin-top: 4px;">
                            @for ($i = 1; $i <= 5; $i++)
                                <div style="flex: 1; text-align: center; font-size: 10px;">{{ $i }}★</div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- line tháng/nam -->
    <h2>Biểu đồ đánh giá theo tháng và năm</h2>
    <div style="margin-bottom: 10px;">
        <span style="display: inline-block; width: 15px; height: 15px; background-color: #28a745; margin-right: 5px;"></span>
        <span style="margin-right: 20px;">Tích cực</span>
        <span style="display: inline-block; width: 15px; height: 15px; background-color: #dc3545; margin-right: 5px;"></span>
        <span>Tiêu cực</span>
    </div>

    <div class="chart-wrapper">

        {{-- Biểu đồ theo tháng --}}
        <div class="chart-container-month">
            <svg width="90%" height="300">
                @php
                    $maxValue = max(array_map(function($item) { 
                        return max($item->tong_tu_tot, $item->tong_tu_xau); 
                    }, $thongKeThang->toArray()));
                    $step = ($maxValue > 100) ? 20 : 10;
                    $chartHeight = 200;
                @endphp

                {{-- Trục Y --}}
                @for ($i = 0; $i <= $maxValue; $i += $step)
                    @php
                        $y = $chartHeight - ($i / $maxValue) * $chartHeight;
                    @endphp
                    <text x="5" y="{{ $y + 4 }}" class="y-label">{{ $i }}</text>
                    <line x1="40" y1="{{ $y }}" x2="100%" y2="{{ $y }}" stroke="#ccc" stroke-dasharray="2,2"/>
                @endfor

                {{-- Đường từ tốt (xanh) --}}
                @php $pointsGood = ''; @endphp
                @foreach ($thongKeThang as $i => $item)
                    @php
                        $x = 38 + $i *45;
                        $y = $chartHeight - ($item->tong_tu_tot / $maxValue) * $chartHeight;
                        $pointsGood .= "$x,$y ";
                    @endphp
                @endforeach
                <polyline points="{{ trim($pointsGood) }}" style="fill: none; stroke: #28a745; stroke-width: 2"/>

                {{-- Đường từ xấu (đỏ) --}}
                @php $pointsBad = ''; @endphp
                @foreach ($thongKeThang as $i => $item)
                    @php
                        $x = 35 + $i * 50;
                        $y = $chartHeight - ($item->tong_tu_xau / $maxValue) * $chartHeight;
                        $pointsBad .= "$x,$y ";
                    @endphp
                @endforeach
                <polyline points="{{ trim($pointsBad) }}" style="fill: none; stroke: #dc3545; stroke-width: 2"/>

                {{-- Các điểm tròn --}}
                @foreach ($thongKeThang as $i => $item)
                    @php
                        $x = 35 + $i * 50;
                        $yGood = $chartHeight - ($item->tong_tu_tot / $maxValue) * $chartHeight;
                        $yBad = $chartHeight - ($item->tong_tu_xau / $maxValue) * $chartHeight;
                    @endphp
                    <circle cx="{{ $x }}" cy="{{ $yGood }}" r="5" fill="#28a745" />
                    <circle cx="{{ $x }}" cy="{{ $yBad }}" r="5" fill="#dc3545" />
                @endforeach

                {{-- Nhãn trục X (tháng/năm) --}}
                @foreach ($thongKeThang as $i => $item)
                    @php
                        $x = 40 + $i * 50;
                        $label = $item->thang . '/' . $item->nam;
                    @endphp
                    <text x="{{ $x }}" y="230" class="x-label" text-anchor="middle">{{ $label }}</text>
                @endforeach
            </svg>
        </div>

        {{-- Biểu đồ theo năm --}}
        <div class="chart-container-year">
            <svg width="90%" height="300">
                @php
                    $maxValue = max(array_map(function($item) { 
                        return max($item->tong_tu_tot, $item->tong_tu_xau); 
                    }, $thongKeNam->toArray()));
                    $step = ($maxValue > 100) ? 20 : 10;
                    $chartHeight = 200;
                @endphp

                {{-- Trục Y --}}
                @for ($i = 0; $i <= $maxValue; $i += $step)
                    @php
                        $y = $chartHeight - ($i / $maxValue) * $chartHeight;
                    @endphp
                    <text x="5" y="{{ $y + 4 }}" class="y-label">{{ $i }}</text>
                    <line x1="40" y1="{{ $y }}" x2="100%" y2="{{ $y }}" stroke="#ccc" stroke-dasharray="2,2"/>
                @endfor

                {{-- Đường từ tốt --}}
                @php $pointsGood = ''; @endphp
                @foreach ($thongKeNam as $i => $item)
                    @php
                        $x = 30 + $i * 70;
                        $y = $chartHeight - ($item->tong_tu_tot / $maxValue) * $chartHeight;
                        $pointsGood .= "$x,$y ";
                    @endphp
                @endforeach
                <polyline points="{{ trim($pointsGood) }}" style="fill: none; stroke: #28a745; stroke-width: 2"/>

                {{-- Đường từ xấu --}}
                @php $pointsBad = ''; @endphp
                @foreach ($thongKeNam as $i => $item)
                    @php
                        $x = 40 + $i * 80;
                        $y = $chartHeight - ($item->tong_tu_xau / $maxValue) * $chartHeight;
                        $pointsBad .= "$x,$y ";
                    @endphp
                @endforeach
                <polyline points="{{ trim($pointsBad) }}" style="fill: none; stroke: #dc3545; stroke-width: 2"/>

                {{-- Các điểm tròn --}}
                @foreach ($thongKeNam as $i => $item)
                    @php
                        $x = 40 + $i * 80;
                        $yGood = $chartHeight - ($item->tong_tu_tot / $maxValue) * $chartHeight;
                        $yBad = $chartHeight - ($item->tong_tu_xau / $maxValue) * $chartHeight;
                    @endphp
                    <circle cx="{{ $x }}" cy="{{ $yGood }}" r="5" fill="#28a745" />
                    <circle cx="{{ $x }}" cy="{{ $yBad }}" r="5" fill="#dc3545" />
                @endforeach

                {{-- Nhãn năm --}}
                @foreach ($thongKeNam as $i => $item)
                    @php
                        $x = 40 + $i * 80;
                    @endphp
                    <text x="{{ $x }}" y="230" text-anchor="middle">{{ $item->nam }}</text>
                @endforeach
            </svg>
        </div>
    </div>

    <script>
        function toggleExtra(className, btn) {
            const elements = document.querySelectorAll('.' + className);
            elements.forEach(el => el.classList.toggle('d-none'));
            btn.innerText = btn.innerText === 'Xem thêm' ? 'Ẩn bớt' : 'Xem thêm';
        }
    </script>
</body>
</html>