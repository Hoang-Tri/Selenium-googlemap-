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
        </div>
    </div>
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
   
    <!-- Xu hướng đánh giá -->
    @php
        $groupedReviews = $userReviewChartData->groupBy('date')->sortKeys();
        @endphp
    @php
        $dates = $groupedReviews->keys()->sort()->values();
        $counts = $groupedReviews->map->count();
        $maxCount = max($counts->values()->toArray()) ?: 1;
        $widthPerPoint = 80;
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
</body>
</html>