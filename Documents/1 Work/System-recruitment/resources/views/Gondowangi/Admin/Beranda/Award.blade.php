@extends('Gondowangi.Admin.Layout.main')

@section('head')
    <!-- Boxicons CSS CDN -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        /* Enhanced Card Animations */
        .award-card {
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .award-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            border: 2px solid #007bff;
        }

        /* Image Animations */
        .award-image-container {
            position: relative;
            overflow: hidden;
            height: 200px;
        }

        .award-image {
            height: 100%;
            width: 100%;
            object-fit: cover;
            transition: all 0.6s ease;
            filter: brightness(0.95);
        }

        .award-card:hover .award-image {
            transform: scale(1.15) rotate(2deg);
            filter: brightness(1.1) saturate(1.2);
        }

        /* Overlay Animation */
        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(0,123,255,0.1), rgba(0,123,255,0.3));
            opacity: 0;
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .award-card:hover .image-overlay {
            opacity: 1;
        }

        .overlay-icon {
            color: white;
            font-size: 2.5rem;
            transform: scale(0);
            transition: transform 0.3s ease 0.1s;
        }

        .award-card:hover .overlay-icon {
            transform: scale(1);
        }

        /* Status Badge Animation */
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
            transition: all 0.3s ease;
            transform: scale(0.9);
        }

        .award-card:hover .status-badge {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        /* Card Body Animations */
        .card-body {
            transition: all 0.3s ease;
            position: relative;
        }

        .award-card:hover .card-body {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        }

        /* Card Title Animation */
        .card-title {
            transition: all 0.3s ease;
            position: relative;
        }

        .award-card:hover .card-title {
            color: #007bff;
            transform: translateX(5px);
        }

        .card-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #007bff, #28a745);
            transition: width 0.4s ease 0.1s;
        }

        .award-card:hover .card-title::after {
            width: 100%;
        }

        /* Button Group Animations */
        .btn-group-animated {
            transform: translateY(10px);
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .award-card:hover .btn-group-animated {
            transform: translateY(0);
            opacity: 1;
        }

        .btn-group-animated .btn {
            transition: all 0.2s ease;
            margin: 0 2px;
        }

        .btn-group-animated .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        /* Badge Animation */
        .badge-animated {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .badge-animated::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s ease;
        }

        .award-card:hover .badge-animated::before {
            left: 100%;
        }

        /* Statistics Cards Animation */
        .card-animate {
            transition: all 0.3s ease;
        }

        .card-animate:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .card-animate:hover .avatar-title {
            transform: scale(1.1) rotate(5deg);
            transition: transform 0.3s ease;
        }

        .counter-value {
            transition: all 0.3s ease;
        }

        .card-animate:hover .counter-value {
            transform: scale(1.1);
            color: #007bff;
        }

        /* Loading Animation for Images */
        .award-image-placeholder {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Pulse Animation for Empty State */
        .empty-state-icon {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.1); opacity: 1; }
        }

        /* Hover Effect for Search and Filter */
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
            transform: scale(1.02);
            transition: all 0.3s ease;
        }

        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
            transition: all 0.3s ease;
        }

        /* Button Hover Animations */
        .btn {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Modal Animation Enhancement */
        .modal.fade .modal-dialog {
            transform: scale(0.8) translateY(-50px);
            transition: all 0.3s ease;
        }

        .modal.show .modal-dialog {
            transform: scale(1) translateY(0);
        }

        /* Card Glow Effect */
        .award-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #007bff, #28a745, #ffc107, #dc3545);
            border-radius: 12px;
            opacity: 0;
            z-index: -1;
            transition: opacity 0.3s ease;
            background-size: 400% 400%;
            animation: gradient 4s ease infinite;
        }

        .award-card:hover::before {
            opacity: 0.7;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Text Animation */
        .card-text {
            transition: all 0.3s ease;
        }

        .award-card:hover .card-text {
            color: #495057;
            line-height: 1.6;
        }

        /* Award Date Animation */
        .award-date {
            transition: all 0.3s ease;
            position: relative;
        }

        .award-card:hover .award-date {
            color: #007bff;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Brand & Award Management</h4>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-animate shadow bg-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-white text-truncate mb-0">Total Awards</p>
                            <div class="d-flex align-items-end justify-content-between mt-2">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                        <span class="counter-value text-white" data-target="{{ $totalAwards }}"><strong>{{ $totalAwards }}</strong></span>
                                    </h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded fs-3">
                                        <i class="bx bx-trophy text-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6  mb-4">
            <div class="card card-animate shadow border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Active Awards</p>
                            <div class="d-flex align-items-end justify-content-between mt-2">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                        <span class="counter-value" data-target="{{ $totalAwards }}">{{ $totalAwards }}</span>
                                    </h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded fs-3">
                                        <i class="bx bx-star text-info"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6  mb-4">
            <div class="card card-animate shadow border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">This Year</p>
                            <div class="d-flex align-items-end justify-content-between mt-2">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                        <span class="counter-value" data-target="{{ $thisYearAwards }}">{{ $thisYearAwards }}</span>
                                    </h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded fs-3">
                                        <i class="bx bx-calendar text-warning"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate shadow border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Categories</p>
                            <div class="d-flex align-items-end justify-content-between mt-2">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                        <span class="counter-value" data-target="{{ $categories }}">{{ $categories }}</span>
                                    </h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded fs-3">
                                        <i class="bx bx-category text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row mt-5">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Awards & Achievements</h4>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAwardModal">
                                <i class="bx bx-plus me-1"></i>Add New Award
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">
                                <i class="bx bx-filter-alt me-1"></i>Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Options -->
                    <form method="GET" action="{{ route('awards.index') }}" id="filterForm">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <select class="form-select form-select-sm" name="year" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">All Years</option>
                                    @foreach($availableYears as $year)
                                        <option value="{{ $year }}" {{ $currentFilters['year'] == $year ? 'selected' : '' }}><strong>{{ $year }}</strong></option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" name="search" placeholder="Search awards..." value="{{ $currentFilters['search'] }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="bx bx-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Awards Grid -->
                    <div class="row">
                        @forelse($awards as $award)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card award-card h-100 {{ !$award->is_active ? 'inactive-award' : '' }}">
                                    <div class="award-image-container position-relative">
                                        @if($award->image_url)
                                            <img src="{{ $award->image_url }}" class="award-image {{ !$award->is_active ? 'grayscale' : '' }}" alt="{{ $award->award_name }}">
                                        @else
                                            <div class="award-image award-image-placeholder d-flex align-items-center justify-content-center bg-light {{ !$award->is_active ? 'grayscale' : '' }}">
                                                <i class="bx bx-image text-muted" style="font-size: 3rem;"></i>
                                            </div>
                                        @endif
                                        <div class="image-overlay {{ !$award->is_active ? 'opacity-50' : '' }}">
                                            <i class="bx bx-search-alt overlay-icon"></i>
                                        </div>
                                        <span class="badge bg-{{ $award->is_active ? 'success' : 'danger' }} status-badge">
                                            {{ $award->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        @if(!$award->is_active)
                                            <div class="inactive-overlay">
                                                <i class="bx bx-lock text-white" style="font-size: 2rem;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body {{ !$award->is_active ? 'opacity-75' : '' }}">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-0 {{ !$award->is_active ? 'text-muted' : '' }}">{{ $award->award_name }}</h5>
                                            <small class="text-muted award-date">{{ $award->award_date->format('M d, Y') }}</small>
                                        </div>
                                        <p class="text-muted small mb-2">{{ $award->awarding_body }}</p>
                                        <p class="card-text text-sm {{ !$award->is_active ? 'text-muted' : '' }}">{{ Str::limit($award->award_description, 100) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            @php
                                                $categoryClass = 'primary';
                                                $categoryLabel = 'Award';
                                                if (stripos($award->award_name, 'top brand') !== false) {
                                                    $categoryClass = 'primary';
                                                    $categoryLabel = 'Top Brand';
                                                } elseif (stripos($award->award_name, 'stellar') !== false) {
                                                    $categoryClass = 'warning';
                                                    $categoryLabel = 'Stellar';
                                                } elseif (stripos($award->award_name, 'excellence') !== false || stripos($award->award_name, 'achievement') !== false) {
                                                    $categoryClass = 'danger';
                                                    $categoryLabel = 'Excellence';
                                                } elseif (stripos($award->award_name, 'certification') !== false) {
                                                    $categoryClass = 'info';
                                                    $categoryLabel = 'Certification';
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $categoryClass }}-subtle text-{{ $categoryClass }} badge-animated {{ !$award->is_active ? 'opacity-50' : '' }}">{{ $categoryLabel }}</span>
                                            <div class="btn-group-sm btn-group-animated" role="group">
                                                <button type="button" class="btn btn-outline-primary btn-sm rounded me-2" title="Edit" 
                                                        onclick="editAward({{ $award->id }}, '{{ $award->award_name }}', '{{ $award->award_description }}', '{{ $award->award_date->format('Y-m-d') }}', '{{ $award->awarding_body }}', {{ $award->display_order }}, {{ $award->is_active ? 'true' : 'false' }})">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <!--<button type="button" class="btn btn-outline-info btn-sm rounded me-2" title="View" onclick="viewAward({{ $award->id }})">-->
                                                <!--    <i class="bx bx-show"></i>-->
                                                <!--</button>-->
                                                <!-- Toggle Active/Inactive Button -->
                                                <form method="POST" action="{{ route('awards.toggle-status', $award->id) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-{{ $award->is_active ? 'warning' : 'success' }} btn-sm rounded me-2" 
                                                            title="{{ $award->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="bx bx-{{ $award->is_active ? 'toggle-right' : 'toggle-left' }}"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('awards.destroy', $award->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this award?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded" title="Delete">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="bx bx-trophy text-muted empty-state-icon" style="font-size: 4rem;"></i>
                                    <h5 class="mt-3 text-muted">No Awards Found</h5>
                                    <p class="text-muted">No awards match your current filters. Try adjusting your search criteria.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Award Modal -->
<div class="modal fade" id="addAwardModal" tabindex="-1" aria-labelledby="addAwardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAwardModalLabel">Add New Award</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('awards.store') }}" enctype="multipart/form-data" id="addAwardForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="award_name" class="form-label">Award Title *</label>
                                <input type="text" class="form-control" name="award_name" id="award_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="award_date" class="form-label">Award Date *</label>
                                <input type="date" class="form-control" name="award_date" id="award_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="awarding_body" class="form-label">Organizer/Institution *</label>
                        <input type="text" class="form-control" name="awarding_body" id="awarding_body" required>
                    </div>
                    <div class="mb-3">
                        <label for="award_description" class="form-label">Description</label>
                        <textarea class="form-control" name="award_description" id="award_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Award Image</label>
                        <input type="file" class="form-control" name="image" id="image" accept="image/*">
                        <div class="form-text">Upload award certificate or photo (Max: 2MB)</div>
                    </div>
                    <div class="row">
                        <!--<div class="col-md-6">-->
                        <!--    <div class="mb-3">-->
                        <!--        <label for="display_order" class="form-label">Display Order</label>-->
                        <!--        <input type="number" class="form-control" name="display_order" id="display_order" min="1" value="1">-->
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<div class="col-md-6">-->
                        <!--    <div class="mb-3">-->
                        <!--        <div class="form-check mt-4">-->
                        <!--            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>-->
                        <!--            <label class="form-check-label" for="is_active">-->
                        <!--                Active-->
                        <!--            </label>-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--</div>-->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Award</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Award Modal -->
<div class="modal fade" id="editAwardModal" tabindex="-1" aria-labelledby="editAwardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAwardModalLabel">Edit Award</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="editAwardForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_award_name" class="form-label">Award Title *</label>
                                <input type="text" class="form-control" name="award_name" id="edit_award_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_award_date" class="form-label">Award Date *</label>
                                <input type="date" class="form-control" name="award_date" id="edit_award_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_awarding_body" class="form-label">Organizer/Institution *</label>
                        <input type="text" class="form-control" name="awarding_body" id="edit_awarding_body" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_award_description" class="form-label">Description</label>
                        <textarea class="form-control" name="award_description" id="edit_award_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_image" class="form-label">Award Image</label>
                        <input type="file" class="form-control" name="image" id="edit_image" accept="image/*">
                        <div class="form-text">Upload award certificate or photo (Max: 2MB)</div>
                    </div>
                    <div class="mb-3 pl-4">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                            <label class="form-check-label p-0" for="edit_is_active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Award</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image preview for add form
        document.getElementById('image').addEventListener('change', function(e) {
            validateImage(e.target);
        });
        
        // Image preview for edit form
        document.getElementById('edit_image').addEventListener('change', function(e) {
            validateImage(e.target);
        });
        
        function validateImage(input) {
            const file = input.files[0];
            if (file) {
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    input.value = '';
                    return;
                }
            }
        }

        // Add staggered animation for cards
        const cards = document.querySelectorAll('.award-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('fade-in-up');
        });

        // Counter animation for statistics
        const counters = document.querySelectorAll('.counter-value');
        const animateCounter = (counter) => {
            const target = parseInt(counter.getAttribute('data-target'));
            const increment = target / 50;
            let current = 0;
            
            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    counter.textContent = Math.ceil(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            updateCounter();
        };

        // Intersection Observer for counter animation
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target.querySelector('.counter-value');
                    if (counter && !counter.classList.contains('animated')) {
                        counter.classList.add('animated');
                        animateCounter(counter);
                    }
                }
            });
        });

        document.querySelectorAll('.card-animate').forEach(card => {
            observer.observe(card);
        });

        // Add loading animation for images
        const images = document.querySelectorAll('.award-image');
        images.forEach(img => {
            if (img.tagName === 'IMG') {
                img.addEventListener('load', function() {
                    this.style.opacity = '0';
                    setTimeout(() => {
                        this.style.transition = 'opacity 0.5s ease';
                        this.style.opacity = '1';
                    }, 100);
                });
            }
        });

        // Add ripple effect to buttons
        function createRipple(event) {
            const button = event.currentTarget;
            const circle = document.createElement('span');
            const diameter = Math.max(button.clientWidth, button.clientHeight);
            const radius = diameter / 2;

            circle.style.width = circle.style.height = `${diameter}px`;
            circle.style.left = `${event.clientX - button.offsetLeft - radius}px`;
            circle.style.top = `${event.clientY - button.offsetTop - radius}px`;
            circle.classList.add('ripple');

            const ripple = button.getElementsByClassName('ripple')[0];
            if (ripple) {
                ripple.remove();
            }

            button.appendChild(circle);
        }

        // Add ripple effect to all buttons
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', createRipple);
        });

        // Add smooth scroll for pagination
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetUrl = this.href;
                
                // Add loading animation
                document.body.style.cursor = 'wait';
                
                // Navigate after animation
                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 300);
            });
        });
    });
    
    function editAward(id, name, description, date, awardingBody, displayOrder, isActive) {
        document.getElementById('edit_award_name').value = name;
        document.getElementById('edit_award_description').value = description;
        document.getElementById('edit_award_date').value = date;
        document.getElementById('edit_awarding_body').value = awardingBody;
        // document.getElementById('edit_display_order').value = displayOrder;
        document.getElementById('edit_is_active').checked = isActive;
        
        // Set form action
        document.getElementById('editAwardForm').action = '/awards/' + id;
        
        // Show modal with animation
        const modal = new bootstrap.Modal(document.getElementById('editAwardModal'));
        modal.show();
        
        // Add form animation
        setTimeout(() => {
            document.querySelector('#editAwardModal .modal-content').style.transform = 'scale(1)';
        }, 150);
    }
    
    function viewAward(id) {
        // Add loading animation before redirect
        const button = event.target.closest('.btn');
        const originalHTML = button.innerHTML;
        
        button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
        button.disabled = true;
        
        setTimeout(() => {
            window.location.href = '/awards/' + id;
        }, 500);
    }
    
    function resetFilters() {
        // Add animation before redirect
        const button = event.target;
        const originalHTML = button.innerHTML;
        
        button.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i>Resetting...';
        button.disabled = true;
        
        setTimeout(() => {
            window.location.href = '{{ route("awards.index") }}';
        }, 800);
    }

    // Add custom CSS for additional animations
    const additionalStyles = `
        <style>
            .fade-in-up {
                animation: fadeInUp 0.6s ease forwards;
                opacity: 0;
                transform: translateY(30px);
            }

            @keyframes fadeInUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .ripple {
                position: absolute;
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 600ms linear;
                background-color: rgba(255, 255, 255, 0.7);
                pointer-events: none;
            }

            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }

            .btn {
                position: relative;
                overflow: hidden;
            }

            /* Enhanced loading animation */
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.9);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .loading-overlay.show {
                opacity: 1;
                visibility: visible;
            }

            .loading-spinner {
                width: 50px;
                height: 50px;
                border: 4px solid #f3f3f3;
                border-top: 4px solid #007bff;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* Enhanced form focus effects */
            .form-control:focus,
            .form-select:focus {
                border-color: #007bff;
                box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
                transform: scale(1.02);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            /* Card entrance animation */
            .award-card {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
                animation: cardEntrance 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            }

            @keyframes cardEntrance {
                0% {
                    opacity: 0;
                    transform: translateY(50px) scale(0.9);
                }
                60% {
                    opacity: 0.8;
                    transform: translateY(-10px) scale(1.02);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            /* Staggered animation for multiple cards */
            .award-card:nth-child(1) { animation-delay: 0.1s; }
            .award-card:nth-child(2) { animation-delay: 0.2s; }
            .award-card:nth-child(3) { animation-delay: 0.3s; }
            .award-card:nth-child(4) { animation-delay: 0.4s; }
            .award-card:nth-child(5) { animation-delay: 0.5s; }
            .award-card:nth-child(6) { animation-delay: 0.6s; }

            /* Enhanced modal animations */
            .modal.fade .modal-dialog {
                transform: perspective(1000px) rotateX(-20deg) scale(0.8);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .modal.show .modal-dialog {
                transform: perspective(1000px) rotateX(0deg) scale(1);
            }

            /* Floating animation for icons */
            .avatar-title i {
                animation: float 3s ease-in-out infinite;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-5px); }
            }

            /* Progress bar animation for loading */
            .progress-bar-animated {
                animation: progress-bar-stripes 1s linear infinite;
            }

            @keyframes progress-bar-stripes {
                0% { background-position: 1rem 0; }
                100% { background-position: 0 0; }
            }
        </style>
    `;

    // Inject additional styles
    document.head.insertAdjacentHTML('beforeend', additionalStyles);

    // Add loading overlay functionality
    function showLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = '<div class="loading-spinner"></div>';
        document.body.appendChild(overlay);
        
        setTimeout(() => {
            overlay.classList.add('show');
        }, 10);
        
        return overlay;
    }

    function hideLoadingOverlay(overlay) {
        overlay.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(overlay);
        }, 300);
    }
</script>
@endsection