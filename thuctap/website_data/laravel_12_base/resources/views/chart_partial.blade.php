<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Giao Diện Người Dùng</title>
    <link rel="stylesheet" href="{{ asset('css/style_nguoidung.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
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
        </div>
    </div>
    
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

    <div class="container mt-3">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <strong>Biểu đồ phần trăm tốt và xấu của từng người</strong>
            </div>
            <div class="card-body">
                <div class="overflow-auto">
                    <div class="d-flex gap-4" style="flex-wrap: nowrap; white-space: nowrap;">
                        @foreach ($userLabels as $index => $label)
                            @php
                                $goodPercent = $goodPercents[$index];
                                $badPercent = $badPercents[$index];
                            @endphp
                            <div class="text-center">
                                <div style="height: 100px; display: flex; align-items: flex-end; justify-content: center;">
                                    <!-- Cột phần trăm tốt -->
                                    <div style="
                                        height: {{ $goodPercent }}%;
                                        width: 20px;
                                        background-color: #4bc0c0;
                                        border-radius: 4px;
                                        margin-right: 5px;
                                        " title="Tốt: {{ $goodPercent }}%">
                                    </div>
                                    <!-- Cột phần trăm xấu -->
                                    <div style="
                                        height: {{ $badPercent }}%;
                                        width: 20px;
                                        background-color: #f44336;
                                        border-radius: 4px;
                                        " title="Xấu: {{ $badPercent }}%">
                                    </div>
                                </div>
                                <div style="font-size: 12px">
                                    Tốt: {{ $goodPercent }}% Xấu: {{ $badPercent }}%
                                </div>
                                <div class="fw-bold mb-2">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-3">
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

    <div class="container mt-3">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <strong>Biểu đồ đánh giá từng người theo ngày</strong>
            </div>
            <div class="card-body">
                @php
                    $groupedReviews = $userReviewChartData->groupBy('date')->sortKeys();
                @endphp
                <div class="review-scroll-wrapper">
                    <div class="d-flex">
                        @foreach ($groupedReviews as $date => $reviews)
                            <div class="review-column" style="margin-right: 5px;">
                                <div class="fw-bold mb-2">{{ \Carbon\Carbon::parse($date)->format('d/m/y') }}</div>
                                <div class="d-flex gap-1">
                                    @foreach ($reviews as $review)
                                        @php
                                            $heightPercent = min(5, $review->star) * 20;
                                            $colorHue = 120 - $heightPercent * 2;
                                        @endphp
                                        <div class="review-item" style="margin-right: 1px;">
                                            <div style="height: 100px; display: flex; align-items: flex-end; justify-content: center;">
                                                <div style="
                                                    height: {{ $heightPercent }}%;
                                                    width: 8px;
                                                    background-color: hsl({{ $colorHue }}, 70%, 60%);
                                                    border-radius: 4px;
                                                    " title="{{ $review->username }} - {{ $review->star }}★">
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

    <!-- <pre>{{ json_encode($userReviewChartData, JSON_PRETTY_PRINT) }}</pre> -->
    <!-- <pre>{{ print_r($userReviewChartData, true) }}</pre> -->
</body>
</html>