@extends('Gondowangi.Admin.Layout.main')

@section('head')
    <!-- Chart.js -->
    
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    
    <style>
        .stats-card {
            transition: all 0.3s ease;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }
        .metric-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .trend-up {
            color: #28a745;
        }
        .trend-down {
            color: #dc3545;
        }
        .trend-neutral {
            color: #ffc107;
        }
        .page-section-card {
            border-left: 4px solid #007bff;
        }
        .product-brand-card {
            border-left: 4px solid #28a745;
        } 
        .top-pages-table {
            font-size: 0.9rem;
        }
        .progress-sm {
            height: 8px;
        }
        .pulse-animation {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .pulse-dot {
            width: 20px;
            height: 20px;
            background-color: #28a745;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
            }
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="font-weight-bold mb-0">Dashboard Analytics</h4>
                    <p class="text-muted mb-0">PT Gondowangi - Website Performance Overview</p>
                </div>
                <div class="d-flex">
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="mdi mdi-download"></i> Export Report
                    </button>
                    <button type="button" class="btn btn-sm btn-primary">
                        <i class="mdi mdi-refresh"></i> Refresh Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="row">
        <!-- Total Visitors -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 grid-margin stretch-card">
            <div class="card stats-card shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-title text-muted">Total Visitors</p>
                            <h3 class="font-weight-bold mb-2">{{ number_format($totalVisitors) }}</h3>
                            <p class="mb-0 {{ $visitorsGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="mdi {{ $visitorsGrowth >= 0 ? 'mdi-arrow-up' : 'mdi-arrow-down' }}"></i> 
                                {{ number_format(abs($visitorsGrowth), 1) }}% from yesterday
                            </p>
                        </div>
                        <div class="metric-icon text-primary">
                            <i class="mdi mdi-account-multiple"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Views -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 grid-margin stretch-card">
            <div class="card stats-card shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-title text-muted">Page Views</p>
                            <h3 class="font-weight-bold mb-2">{{ number_format($totalPageViews) }}</h3>
                            <p class="mb-0 {{ $pageViewsGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="mdi {{ $pageViewsGrowth >= 0 ? 'mdi-arrow-up' : 'mdi-arrow-down' }}"></i> 
                                {{ number_format(abs($pageViewsGrowth), 1) }}% from yesterday
                            </p>
                        </div>
                        <div class="metric-icon text-success">
                            <i class="mdi mdi-eye"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bounce Rate -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 grid-margin stretch-card">
            <div class="card stats-card shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-title text-muted">Bounce Rate</p>
                            <h3 class="font-weight-bold mb-2">{{ number_format($bounceRate, 1) }}%</h3>
                            <p class="mb-0 {{ $bounceRateGrowth <= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="mdi {{ $bounceRateGrowth <= 0 ? 'mdi-arrow-down' : 'mdi-arrow-up' }}"></i> 
                                {{ number_format(abs($bounceRateGrowth), 1) }}% from yesterday
                            </p>
                        </div>
                        <div class="metric-icon text-warning">
                            <i class="mdi mdi-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form Submissions -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 grid-margin stretch-card">
            <div class="card stats-card shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-title text-muted">Contact Forms</p>
                            <h3 class="font-weight-bold mb-2">{{ number_format($totalContactForms) }}</h3>
                            <p class="mb-0 text-dark">
                                <i class="mdi "></i> 
                                total keseluruhan
                            </p>
                        </div>
                        <div class="metric-icon text-info">
                            <i class="mdi mdi-email"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <h4 class="card-title mb-0">Website Traffic Overview</h4>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary active" data-period="7">7 Days</button>
                            <button type="button" class="btn btn-outline-secondary" data-period="30">30 Days</button>
                            <button type="button" class="btn btn-outline-secondary" data-period="90">90 Days</button>
                            <button type="button" class="btn btn-outline-secondary" data-period="custom">Custom Range</button>
                        </div>
                    </div>
                    
                    <!-- Custom Date Range Filter - Hidden by default -->
                    <div class="date-range-container" id="dateRangeContainer" style="display: none;">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3">Select Date Range:</h6>
                                <div class="date-input-group d-flex">
                                    <div class="d-flex align-items-center gap-2 mr-3" style="margin-right: 6px;">
                                        <label for="startDate" class="form-label mb-0 fw-bold">From:</label>
                                        <input type="date" class="form-control" id="startDate">
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mr-3" style="margin-right: 6px;">
                                        <label for="endDate" class="form-label mb-0 fw-bold">To:</label>
                                        <input type="date" class="form-control" id="endDate">
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm mr-3" id="applyDateRange" style="margin-right: 6px;">
                                        Apply Range
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm mr-3" id="resetDateRange" style="margin-right: 6px;">
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Loading indicator -->
                    <div class="text-center" id="loadingIndicator" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Loading chart data...</p>
                    </div>
                    
                    <div class="chart-container">
                        <canvas id="trafficChart"></canvas>
                    </div>
                    
                    <!-- Current Period Info -->
                    <div class="mt-3">
                        <small class="text-muted" id="periodInfo">Showing data for: Last 7 days</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Performance Analysis -->
    <div class="row">
        <!-- Page Performance by Section -->
        <div class="col-lg-9 grid-margin stretch-card">
           <div class="card page-section-card shadow">
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-lg-8">
                            <h4 class="card-title mb-4">Page Performance Analysis</h4>
                            <div class="table-responsive">
                                <table class="table table-hover top-pages-table" id="pagePerformanceTable">
                                    <thead>
                                        <tr>
                                            <th>Page</th>
                                            <th>Visitors</th>
                                            <th>Page Views</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pagePerformanceData as $index => $data)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @switch($data['label'])
                                                            @case('Beranda')
                                                                <i class="mdi mdi-home text-primary me-2"></i>
                                                                @break
                                                            @case('Tentang Kami')
                                                                <i class="mdi mdi-information text-info me-2"></i>
                                                                @break
                                                            @case('Berita')
                                                                <i class="mdi mdi-newspaper text-success me-2"></i>
                                                                @break
                                                            @case('Karir')
                                                                <i class="mdi mdi-briefcase text-warning me-2"></i>
                                                                @break
                                                            @case('Kontak Kami')
                                                                <i class="mdi mdi-phone text-primary me-2"></i>
                                                                @break
                                                        @endswitch
                                                        <span>{{ $data['label'] }}</span>
                                                    </div>
                                                </td>
                                                <td><strong>{{ $data['visitors'] }}</strong></td>
                                                <td>{{ $data['views'] }}</td>
                                                <td>
                                                    <span class="badge rounded" style="background-color: {{ $chartColors[$index] ?? '#6c757d' }}; color: white;">
                                                        {{ $data['percentage'] }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <h4 class="card-title mb-4">Page Views Distribution</h4>
                            <div class="chart-container" style="position: relative; height: 200px;">
                                <canvas id="pagePerformanceChart"></canvas>
                            </div>
                            <div class="mt-3">
                                <div class="legend-container">
                                    @foreach($pagePerformanceData as $index => $data)
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="legend-color me-2" 
                                                 style="width: 12px; height: 12px; border-radius: 50%; background-color: {{ $chartColors[$index] ?? '#6c757d' }};"></div>
                                            <small class="text-muted">{{ $data['label'] }}: {{ $data['views'] }} views</small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Traffic Sources -->
        <!-- Product Brand Performance -->
        <div class="col-lg-3 grid-margin stretch-card">
            <div class="card product-brand-card shadow">
                <div class="card-body">
                    <h4 class="card-title mb-4">Brand Performance</h4>
                    <div class="brand-stats">
                        @foreach($brands as $brand)
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="brand-icon me-3">
                                        <i class="mdi {{ $brand['icon'] }} {{ $brand['color'] }}"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $brand['name'] }}</h6>
                                        <p class="text-muted mb-0 small">{{ $brand['category'] }}</p>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-1">{{ $brand['views'] }}</h6>
                                    <p class="text-success mb-0 small">+{{ $brand['growth'] }}%</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Device & Browser Analytics -->
    <div class="row">
        <!-- Device Analytics -->
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title mb-4">Device Analytics</h4>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="deviceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Visitors -->
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title mb-4">Real-time Visitors</h4>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="font-weight-bold text-primary" id="active-visitors-count">{{ $activeVisitors }}</h2>
                            <p class="text-muted mb-0">Active users right now</p>
                        </div>
                        <div class="pulse-animation">
                            <div class="pulse-dot"></div>
                        </div>
                    </div>
                    
                    <div class="real-time-stats" id="visitors-per-page">
                        @foreach($visitorsPerPage as $key => $page)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>{{ $page['name'] }}</span>
                            <div class="d-flex align-items-center">
                                <span class="me-2">{{ $page['count'] }}</span>
                                <div class="progress progress-sm" style="width: 80px;">
                                    <div class="progress-bar 
                                        @if($key == 'beranda') bg-primary
                                        @elseif($key == 'tentangkami') bg-success
                                        @elseif($key == 'berita') bg-info
                                        @elseif($key == 'kontakkami') bg-warning
                                        @else bg-secondary
                                        @endif
                                    " style="width: {{ $page['percentage'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!--<div class="text-center mt-3">-->
                    <!--    <small class="text-muted">Last updated: <span id="last-updated">{{ now()->format('H:i:s') }}</span></small>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
    </div>

    <!-- Conversion & Goals -->
    <!--<div class="row">-->
    <!--    <div class="col-lg-12 grid-margin stretch-card">-->
    <!--        <div class="card shadow">-->
    <!--            <div class="card-body">-->
    <!--                <h4 class="card-title mb-4">Conversion Goals & Business Insights</h4>-->
    <!--                <div class="row">-->
    <!--                    <div class="col-lg-3 col-md-6">-->
    <!--                        <div class="text-center mb-4">-->
    <!--                            <div class="goal-circle mx-auto mb-3" style="width: 120px; height: 120px; position: relative;">-->
    <!--                                <canvas id="contactGoalChart" width="120" height="120"></canvas>-->
    <!--                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">-->
    <!--                                    <h4 class="mb-0">73%</h4>-->
    <!--                                    <small class="text-muted">Contact Goal</small>-->
    <!--                                </div>-->
    <!--                            </div>-->
    <!--                            <p class="text-muted mb-0">286 / 390 monthly target</p>-->
    <!--                        </div>-->
    <!--                    </div>-->
                        
    <!--                    <div class="col-lg-3 col-md-6">-->
    <!--                        <div class="text-center mb-4">-->
    <!--                            <div class="goal-circle mx-auto mb-3" style="width: 120px; height: 120px; position: relative;">-->
    <!--                                <canvas id="newsEngagementChart" width="120" height="120"></canvas>-->
    <!--                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">-->
    <!--                                    <h4 class="mb-0">89%</h4>-->
    <!--                                    <small class="text-muted">News Engagement</small>-->
    <!--                                </div>-->
    <!--                            </div>-->
    <!--                            <p class="text-muted mb-0">Avg. 4m 23s read time</p>-->
    <!--                        </div>-->
    <!--                    </div>-->
                        
    <!--                    <div class="col-lg-3 col-md-6">-->
    <!--                        <div class="text-center mb-4">-->
    <!--                            <div class="goal-circle mx-auto mb-3" style="width: 120px; height: 120px; position: relative;">-->
    <!--                                <canvas id="brandAwarenessChart" width="120" height="120"></canvas>-->
    <!--                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">-->
    <!--                                    <h4 class="mb-0">65%</h4>-->
    <!--                                    <small class="text-muted">Brand Awareness</small>-->
    <!--                                </div>-->
    <!--                            </div>-->
    <!--                            <p class="text-muted mb-0">Product page visits</p>-->
    <!--                        </div>-->
    <!--                    </div>-->
                        
    <!--                    <div class="col-lg-3 col-md-6">-->
    <!--                        <div class="text-center mb-4">-->
    <!--                            <div class="goal-circle mx-auto mb-3" style="width: 120px; height: 120px; position: relative;">-->
    <!--                                <canvas id="careerInterestChart" width="120" height="120"></canvas>-->
    <!--                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">-->
    <!--                                    <h4 class="mb-0">42%</h4>-->
    <!--                                    <small class="text-muted">Career Interest</small>-->
    <!--                                </div>-->
    <!--                            </div>-->
    <!--                            <p class="text-muted mb-0">Job page engagement</p>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data untuk chart
        const chartData = @json($pagePerformanceData);
        const chartColors = @json($chartColors);
        
        // Konfigurasi Chart
        const ctx = document.getElementById('pagePerformanceChart').getContext('2d');
        const pagePerformanceChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: chartData.map(item => item.label),
                datasets: [{
                    data: chartData.map(item => item.views),
                    backgroundColor: chartColors,
                    borderColor: chartColors.map(color => color + '80'), // Add transparency to border
                    borderWidth: 2,
                    hoverBorderWidth: 3,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // We'll use custom legend
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} views (${percentage}%)`;
                            }
                        },
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#fff',
                        borderWidth: 1
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000
                }
            }
        });
    
        // Optional: Add click event for interactivity
        ctx.onclick = function(evt) {
            const points = pagePerformanceChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
            if (points.length) {
                const firstPoint = points[0];
                const label = pagePerformanceChart.data.labels[firstPoint.index];
                const value = pagePerformanceChart.data.datasets[firstPoint.datasetIndex].data[firstPoint.index];
                console.log(`Clicked on ${label}: ${value} views`);
            }
        };
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Debug: Cek apakah Chart.js ter-load
    if (typeof Chart === 'undefined') {
        console.error('Chart.js library tidak ter-load!');
        return;
    }
    
    // Debug: Cek apakah canvas element ada
    const canvas = document.getElementById('deviceChart');
    if (!canvas) {
        console.error('Canvas element dengan ID "deviceChart" tidak ditemukan!');
        return;
    }
    
    // Data dummy untuk device statistics
    const realTimeDeviceStats = [
        {
            device_type: 'desktop',
            count: 1250,
            percentage: 45.5
        },
        {
            device_type: 'mobile',
            count: 1180,
            percentage: 43.0
        },
        {
            device_type: 'tablet',
            count: 315,
            percentage: 11.5
        }
    ];
    
    console.log('Menggunakan data dummy untuk device statistics');
    console.log('Real-time device stats:', realTimeDeviceStats);
    
    // Fungsi helper untuk mendapatkan data device
    function getDeviceData(deviceType) {
        const device = realTimeDeviceStats.find(d => d.device_type === deviceType);
        return device ? parseFloat(device.percentage) : 0;
    }
    
    function getDeviceCount(deviceType) {
        const device = realTimeDeviceStats.find(d => d.device_type === deviceType);
        return device ? parseInt(device.count) : 0;
    }
    
    // Data untuk chart
    const deviceData = {
        labels: ['Desktop', 'Mobile', 'Tablet'],
        datasets: [{
            label: 'Users',
            data: [
                getDeviceData('desktop'),
                getDeviceData('mobile'),
                getDeviceData('tablet')
            ],
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
            borderColor: ['#4e73df', '#1cc88a', '#36b9cc'],
            borderWidth: 1,
            borderRadius: 8,
            borderSkipped: false
        }]
    };
    
    console.log('Device data for chart:', deviceData);
    
    // Konfigurasi chart
    const config = {
        type: 'bar',
        data: deviceData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: { 
                    display: true,
                    text: 'Device Usage Statistics',
                    font: { size: 16 }
                },
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const deviceCounts = {
                                'Desktop': getDeviceCount('desktop'),
                                'Mobile': getDeviceCount('mobile'),
                                'Tablet': getDeviceCount('tablet')
                            };
                            return context.label + ': ' + deviceCounts[context.label] + ' users (' + context.parsed.y.toFixed(1) + '%)';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: { 
                        color: 'rgba(0, 0, 0, 0.1)' 
                    },
                    title: {
                        display: true,
                        text: 'Percentage (%)'
                    }
                },
                x: { 
                    grid: { display: false },
                    title: {
                        display: true,
                        text: 'Device Type'
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    };
    
    // Buat chart
    try {
        const ctx = canvas.getContext('2d');
        if (ctx) {
            const chart = new Chart(ctx, config);
            console.log('Chart berhasil dibuat dengan data dummy!', chart);
            
            // Tampilkan statistik di console
            const totalUsers = realTimeDeviceStats.reduce((sum, device) => sum + parseInt(device.count), 0);
            console.log('Total users:', totalUsers);
            
            console.log('=== Device Statistics (Dummy Data) ===');
            realTimeDeviceStats.forEach(device => {
                console.log(`${device.device_type}: ${device.count} users (${device.percentage}%)`);
            });
            
            // Fungsi untuk simulasi update data (opsional)
            window.updateDeviceChart = function(newData) {
                if (chart && newData) {
                    const updatedData = newData || [
                        { device_type: 'desktop', count: Math.floor(Math.random() * 1000) + 1000, percentage: Math.floor(Math.random() * 30) + 35 },
                        { device_type: 'mobile', count: Math.floor(Math.random() * 1000) + 1000, percentage: Math.floor(Math.random() * 30) + 35 },
                        { device_type: 'tablet', count: Math.floor(Math.random() * 500) + 200, percentage: Math.floor(Math.random() * 20) + 10 }
                    ];
                    
                    chart.data.datasets[0].data = [
                        updatedData.find(d => d.device_type === 'desktop').percentage,
                        updatedData.find(d => d.device_type === 'mobile').percentage,
                        updatedData.find(d => d.device_type === 'tablet').percentage
                    ];
                    chart.update();
                    console.log('Chart updated with new dummy data:', updatedData);
                }
            };
            
        } else {
            console.error('Tidak bisa mendapatkan 2D context dari canvas');
        }
    } catch (error) {
        console.error('Error membuat chart:', error);
    }
});
</script>

<!--Real-time penggunjung updates menggunakan JavaScript-->
<script>
    function updateRealTimeVisitors() {
        fetch('{{ route("admin.realtime-data") }}')
            .then(response => response.json())
            .then(data => {
                // Update total active visitors
                document.getElementById('active-visitors-count').textContent = data.activeVisitors;
                
                // Update visitors per page
                const visitorsPerPageContainer = document.getElementById('visitors-per-page');
                visitorsPerPageContainer.innerHTML = '';
                
                const pageColors = {
                    'beranda': 'bg-primary',
                    'tentangkami': 'bg-success',
                    'berita': 'bg-info',
                    'kontakkami': 'bg-warning',
                    'karir': 'bg-secondary'
                };
                
                Object.keys(data.visitorsPerPage).forEach(key => {
                    const page = data.visitorsPerPage[key];
                    const pageDiv = document.createElement('div');
                    pageDiv.className = 'd-flex justify-content-between align-items-center mb-3';
                    
                    pageDiv.innerHTML = `
                        <span>${page.name}</span>
                        <div class="d-flex align-items-center">
                            <span class="me-2">${page.count}</span>
                            <div class="progress progress-sm" style="width: 80px;">
                                <div class="progress-bar ${pageColors[key] || 'bg-secondary'}" 
                                     style="width: ${page.percentage}%"></div>
                            </div>
                        </div>
                    `;
                    
                    visitorsPerPageContainer.appendChild(pageDiv);
                });
                
                // Update last updated time
                document.getElementById('last-updated').textContent = data.lastUpdated;
            })
            .catch(error => {
                console.error('Error fetching real-time data:', error);
            });
    }
    
    // Update setiap 10 detik
    setInterval(updateRealTimeVisitors, 5000);
    
    // Initial load
    document.addEventListener('DOMContentLoaded', function() {
        updateRealTimeVisitors();
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let trafficChart;
    let currentPeriod = 7;
    let customStartDate = null;
    let customEndDate = null;
    
    // Set default dates (today and 30 days ago)
    const today = new Date();
    const thirtyDaysAgo = new Date(today);
    thirtyDaysAgo.setDate(today.getDate() - 30);
    
    document.getElementById('endDate').value = today.toISOString().split('T')[0];
    document.getElementById('startDate').value = thirtyDaysAgo.toISOString().split('T')[0];
    
    // Function to fetch data from database
    async function fetchTrafficData(period = 7, startDate = null, endDate = null) {
        try {
            // Show loading indicator
            document.getElementById('loadingIndicator').style.display = 'block';
            document.getElementById('trafficChart').style.display = 'none';
            
            const params = new URLSearchParams();
            params.append('period', period);
            
            if (startDate && endDate) {
                params.append('start_date', startDate);
                params.append('end_date', endDate);
            }
            
            const response = await fetch(`{{ route('admin.dashboard.traffic-data') }}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            // Hide loading indicator
            document.getElementById('loadingIndicator').style.display = 'none';
            document.getElementById('trafficChart').style.display = 'block';
            
            return data;
            
        } catch (error) {
            console.error('Error fetching traffic data:', error);
            
            // Hide loading indicator
            document.getElementById('loadingIndicator').style.display = 'none';
            document.getElementById('trafficChart').style.display = 'block';
            
            // Show error message
            alert('Failed to load traffic data. Please try again.');
            
            // Return empty data as fallback
            return {
                labels: [],
                visitorsData: [],
                pageViewsData: [],
                period: period,
                startDate: startDate,
                endDate: endDate
            };
        }
    }
    
    // Update period info text
    function updatePeriodInfo(period, startDate = null, endDate = null) {
        const periodInfoElement = document.getElementById('periodInfo');
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            periodInfoElement.textContent = `Showing data for: ${start.toLocaleDateString('en-US', options)} - ${end.toLocaleDateString('en-US', options)}`;
        } else {
            periodInfoElement.textContent = `Showing data for: Last ${period} days`;
        }
    }
    
    // Initialize chart with real data
    async function initChart(period = 7, startDate = null, endDate = null) {
        const data = await fetchTrafficData(period, startDate, endDate);
        const ctx = document.getElementById('trafficChart').getContext('2d');
        
        if (trafficChart) {
            trafficChart.destroy();
        }
        
        trafficChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Visitors',
                    data: data.visitorsData,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#007bff',
                    pointBorderColor: '#007bff',
                    pointHoverBackgroundColor: '#0056b3',
                    pointHoverBorderColor: '#0056b3'
                }, {
                    label: 'Page Views',
                    data: data.pageViewsData,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#28a745',
                    pointBorderColor: '#28a745',
                    pointHoverBackgroundColor: '#1e7e34',
                    pointHoverBorderColor: '#1e7e34'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: startDate && endDate ? 'Date Range' : 
                                  period === 7 ? 'Days' : 
                                  period === 30 ? 'Date' : 'Weeks'
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
        
        updatePeriodInfo(period, startDate, endDate);
    }
    
    // Initialize with 7 days
    initChart(7);
    
    // Handle preset filter button clicks
    document.querySelectorAll('.btn-group .btn[data-period]').forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.btn-group .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Add active class to clicked button
            this.classList.add('active');
            
            const period = this.getAttribute('data-period');
            
            if (period === 'custom') {
                // Show custom date range
                document.getElementById('dateRangeContainer').style.display = 'block';
                currentPeriod = 'custom';
            } else {
                // Hide custom date range and show preset period
                document.getElementById('dateRangeContainer').style.display = 'none';
                currentPeriod = parseInt(period);
                customStartDate = null;
                customEndDate = null;
                initChart(currentPeriod);
            }
        });
    });
    
    // Handle custom date range application
    document.getElementById('applyDateRange').addEventListener('click', async function() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        if (!startDate || !endDate) {
            alert('Please select both start and end dates.');
            return;
        }
        
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        if (start > end) {
            alert('Start date cannot be later than end date.');
            return;
        }
        
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        
        if (diffDays > 365) {
            alert('Date range cannot exceed 365 days.');
            return;
        }
        
        customStartDate = startDate;
        customEndDate = endDate;
        await initChart(diffDays, startDate, endDate);
    });
    
    // Handle reset date range
    document.getElementById('resetDateRange').addEventListener('click', function() {
        // Reset to default dates
        const today = new Date();
        const thirtyDaysAgo = new Date(today);
        thirtyDaysAgo.setDate(today.getDate() - 30);
        
        document.getElementById('endDate').value = today.toISOString().split('T')[0];
        document.getElementById('startDate').value = thirtyDaysAgo.toISOString().split('T')[0];
        
        // Reset to 7 days preset
        document.getElementById('dateRangeContainer').style.display = 'none';
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector('.btn-group .btn[data-period="7"]').classList.add('active');
        
        currentPeriod = 7;
        customStartDate = null;
        customEndDate = null;
        initChart(7);
    });
});
</script>
@endsection