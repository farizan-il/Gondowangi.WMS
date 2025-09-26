<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Manajemen Karyawan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
    <script>
        window.routes = {
            candidateData: "{{ route('admin.kandidat.data') }}",
            filterOptions: "{{ route('admin.kandidat.filter.options') }}",
            updateStatus: "{{ route('admin.kandidat.update.status', '') }}",
            
        };
    </script>
    <style>
        :root {
            --primary-black: #000000;
            --primary-green: #0E6A39;
            --secondary-green: #6EAA36;
            --accent-yellow: #FFCE00;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: #0f6d3b;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            z-index: 1000;
            transition: width 0.3s ease;
            overflow: hidden;
        }
        
        .sidebar.minimized {
            width: var(--sidebar-collapsed-width);
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            white-space: nowrap;
            overflow: hidden;
        }
        
        .sidebar.minimized .sidebar-header {
            padding: 15px 10px;
        }
        
        .sidebar-header h4 {
            color: white;
            margin: 0;
            transition: opacity 0.3s ease;
        }
        
        .sidebar.minimized .sidebar-header h4 .sidebar-text {
            opacity: 0;
            visibility: hidden;
        }
        
        .sidebar-header i {
            transition: font-size 0.3s ease;
        }
        
        .sidebar.minimized .sidebar-header i {
            font-size: 1.8rem;
        }
        
        .nav-link {
            color: white !important;
            padding: 15px 20px;
            border-radius: 0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            white-space: nowrap;
            text-decoration: none;
        }
        
        .sidebar.minimized .nav-link {
            padding: 15px 0;
            justify-content: center;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: var(--secondary-green);
            color: white !important;
            border-left: 4px solid var(--accent-yellow);
        }
        
        .nav-link i {
            min-width: 20px;
            margin-right: 10px;
            text-align: center;
            transition: margin 0.3s ease;
        }
        
        .sidebar.minimized .nav-link i {
            margin-right: 0;
        }
        
        .sidebar-text {
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        
        .sidebar.minimized .sidebar-text {
            opacity: 0;
            visibility: hidden;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }
        
        .main-content.sidebar-minimized {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            z-index: 999;
        }
        
        .navbar.sidebar-minimized {
            margin-left: var(--sidebar-collapsed-width);
            left: var(--sidebar-collapsed-width);
        }
        
        .content-wrapper {
            margin-top: 80px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            color: white;
        }
        
        .stat-card.yellow {
            background: linear-gradient(135deg, var(--accent-yellow), #FFD700);
            color: var(--primary-black);
        }
        
        .stat-card.black {
            background: linear-gradient(135deg, var(--primary-black), #333);
            color: white;
        }
        
        .btn-primary {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-green);
            border-color: var(--secondary-green);
        }
        
        .btn-warning {
            background-color: var(--accent-yellow);
            border-color: var(--accent-yellow);
            color: var(--primary-black);
        }
        
        .table thead {
            background-color: var(--primary-green);
            color: white;
        }
        
        .badge-status {
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.8em;
        }
        
        .badge-pending {
            background-color: var(--accent-yellow);
            color: var(--primary-black);
        }
        
        .badge-approved {
            background-color: var(--secondary-green);
            color: white;
        }
        
        .badge-rejected {
            background-color: #dc3545;
            color: white;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .sidebar-toggle {
            top: 15px;
            margin-right: 45px;
            background: var(--primary-green);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1001;
        }
        
        .sidebar-toggle:hover {
            background: var(--secondary-green);
            transform: scale(1.1);
        }
        
        .sidebar-toggle:focus {
            outline: none;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .main-content, .navbar {
                margin-left: 0;
            }
            
            .navbar {
                left: 0;
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }
        
        :root {
            --primary-black: #000000;
            --primary-green: #0E6A39;
            --secondary-green: #6EAA36;
            --accent-yellow: #FFCE00;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Sidebar Styling */
        .sidebar {
            background: #0f6d3b;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            z-index: 1000;
            transition: width 0.3s ease;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            color: white;
        }

        /* Modal Styling */
        .modal-header {
            background-color: var(--primary-green);
            color: white;
        }

        .modal-body {
            font-size: 14px;
            line-height: 1.6;
        }

        .modal-body h5 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .modal-footer {
            border-top: 1px solid #ddd;
        }

        .badge-status {
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.8em;
        }

        .badge-pending {
            background-color: var(--accent-yellow);
            color: var(--primary-black);
        }

        .badge-approved {
            background-color: var(--secondary-green);
            color: white;
        }
        
        .badge-save {
            background-color: #175b91;
            color: white;
        }

        .badge-rejected {
            background-color: #dc3545;
            color: white;
        }
        
        /* Modal Styles */
        .employee-modal .modal-dialog {
            max-width: 95%;
            margin: 15px auto;
        }
        
        .employee-modal .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 80px rgba(0, 0, 0, 0.12);
            background: #ffffff;
        }
        
        .employee-modal .modal-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border-radius: 16px 16px 0 0;
            padding: 20px 24px;
            border: none;
            position: relative;
        }
        
        .employee-modal .modal-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .employee-modal .btn-close {
            filter: brightness(0) invert(1);
            font-size: 1.1rem;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        
        .employee-modal .btn-close:hover {
            opacity: 1;
        }
        
        .employee-modal .modal-body {
            padding: 0;
            background: #f8fafc;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        /* Employee Info Card - Clean Design */
        .employee-info-card {
            background: white;
            border-radius: 12px;
            margin: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }
        
        /* Section Styling - Improved Readability */
        .info-section {
            padding: 24px;
            border-bottom: 1px solid #f1f5f9;
            position: relative;
        }
        
        .info-section:last-child {
            border-bottom: none;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f1f5f9;
        }
        
        .section-icon {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 1rem;
            flex-shrink: 0;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.025em;
        }
        
        /* Grid Layout - Better Responsiveness */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
            margin-top: 4px;
        }
        
        /* Info Items - Enhanced Visual Hierarchy */
        .info-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .info-item:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }
        
        .info-item.full-width {
            grid-column: 1 / -1;
        }
        
        .info-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.875rem;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .info-value {
            color: #0f172a;
            font-size: 1rem;
            font-weight: 500;
            line-height: 1.5;
            word-break: break-word;
        }
        
        /* Special Highlighting */
        .salary-highlight {
            background: linear-gradient(135deg, #059669, #10b981) !important;
            color: white !important;
            border: none !important;
        }
        
        .salary-highlight .info-label,
        .salary-highlight .info-value {
            color: white !important;
        }
        
        .salary-highlight:hover {
            background: linear-gradient(135deg, #047857, #059669) !important;
        }
        
        /* Status Badges - Improved Design */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #f59e0b;
        }
        
        .status-approved {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }
        
        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }
        
        /* Array Data - Clean List Design */
        .array-data {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin-top: 8px;
        }
        
        .array-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 8px;
            border-left: 3px solid #4f46e5;
            font-size: 0.875rem;
            line-height: 1.6;
        }
        
        .array-item:last-child {
            margin-bottom: 0;
        }
        
        .array-item strong {
            color: #374151;
            font-weight: 600;
        }
        
        /* Modal Footer - Professional Buttons */
        .employee-modal .modal-footer {
            background: white;
            border-top: 1px solid #e2e8f0;
            padding: 20px 24px;
            border-radius: 0 0 16px 16px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .btn-modern {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-modern:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .btn-modern:active {
            transform: translateY(0);
        }
        
        .btn-pdf {
            background: #dc2626;
            color: white;
        }
        
        .btn-pdf:hover {
            background: #b91c1c;
        }
        
        .btn-approve {
            background: #059669;
            color: white;
        }
        
        .btn-approve:hover {
            background: #047857;
        }
        
        .btn-reject {
            background: #dc2626;
            color: white;
        }
        
        .btn-reject:hover {
            background: #b91c1c;
        }
        
        .btn-close-modal {
            background: #6b7280;
            color: white;
        }
        
        .btn-close-modal:hover {
            background: #4b5563;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .employee-modal .modal-dialog {
                max-width: 98%;
                margin: 8px auto;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .section-header {
                flex-direction: row;
                text-align: left;
            }
            
            .employee-modal .modal-footer {
                flex-direction: column;
            }
            
            .btn-modern {
                width: 100%;
                justify-content: center;
            }
            
            .info-section {
                padding: 16px;
            }
        }
        
        /* Scrollbar Styling */
        .employee-modal .modal-body::-webkit-scrollbar {
            width: 6px;
        }
        
        .employee-modal .modal-body::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .employee-modal .modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .employee-modal .modal-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Loading Animation */
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f1f5f9;
            border-top: 4px solid #4f46e5;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* CSS untuk button Mungkin */
        .btn-maybe {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
        }
        
        .btn-maybe:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(245, 158, 11, 0.3);
        }
        
        .btn-maybe:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
        }
        
        /* Status badge untuk status maybe */
        .status-maybe {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-block;
        }

    </style>
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            /*background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);*/
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        .content-wrapper {
            padding: 20px;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .stat-card.success {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        }
        
        .stat-card.warning {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
        }
        
        .stat-card.info {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
        }
        
        .stat-card.danger {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
        }
        
        .stat-icon {
            font-size: 3rem;
            opacity: 0.8;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .map-container {
            height: 400px;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .badge-status {
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        
        .badge-pending { background-color: #ffc107; color: #000; }
        .badge-approved { background-color: #28a745; color: #fff; }
        .badge-rejected { background-color: #dc3545; color: #fff; }
        .badge-save { background-color: #6c757d; color: #fff; }
        
        .activity-item {
            padding: 15px;
            border-left: 4px solid #667eea;
            margin-bottom: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .trend-up { color: #28a745; }
        .trend-down { color: #dc3545; }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
        }
        
        .filter-container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
        }
        .badge-pending { background-color: #ffc107; color: #000; }
        .badge-approved { background-color: #28a745; color: #fff; }
        .badge-rejected { background-color: #dc3545; color: #fff; }
        .badge-save { background-color: #17a2b8; color: #fff; }
        .score-excellent { color: #28a745; font-weight: bold; }
        .score-good { color: #ffc107; font-weight: bold; }
        .score-average { color: #fd7e14; font-weight: bold; }
        .score-poor { color: #dc3545; font-weight: bold; }
        .filter-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #495057;
        }
        .clear-filters {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .clear-filters:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        .filter-stats {
            background: white;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-item {
            text-align: center;
            padding: 10px;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4>
                <i class="fas fa-user-shield me-2"></i>
                <span class="sidebar-text">Admin Panel</span>
            </h4>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-section="dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.kandidat.lolos') }}">
                    <i class="fas fa-user-check"></i>
                    <span class="sidebar-text">Kandidat Lolos</span>
                </a>
            </li>

            <li class="nav-item mt-4">
                <a class="nav-link" href="/" >
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="sidebar-text">Keluar</span>
                </a>
            </li>
            
            <li class="nav-item mt-4 text-center align-items-center">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-chevron-left" id="toggleIcon"></i>
                </button>
            </li>
        </ul>
    </nav>

    <!-- Top Navbar -->
    <nav class="p-0 m-0" id="navbar"></nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="content-wrapper mt-1">
            
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">Dashboard Kandidat</h3>
                            <p class="text-muted mb-0">Overview data kandidat dan lowongan kerja</p>
                        </div>
                        <div>
                            <button class="btn btn-outline-primary me-2" onclick="refreshData()">
                                <i class="fas fa-refresh me-1"></i>Refresh
                            </button>
                            <a class="btn btn-danger" href="/">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 opacity-75">Total Kandidat</h6>
                                <h2 class="mb-0" id="totalKandidat">{{ $totalKaryawan }}</h2>
                                <small class="trend-up" style="color: white;">
                                    <i class="fas fa-arrow-up me-1"></i>{{ $baruHariIni }} baru hari ini
                                </small>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card warning">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 opacity-75">Menunggu Review</h6>
                                <h2 class="mb-0" id="menungguReview">{{ $menungguVerifikasi }}</h2>
                                <small>
                                    <i class="fas fa-clock me-1"></i>Perlu tindakan
                                </small>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card success">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 opacity-75">Diterima</h6>
                                <h2 class="mb-0" id="diterima">{{ $terverifikasi }}</h2>
                                <small class="trend-up">
                                    <i class="fas fa-arrow-up me-1"></i>Bulan ini
                                </small>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card info">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 opacity-75">Lowongan Aktif</h6>
                                <h2 class="mb-0" id="lowonganAktif">{{ $lowonganAktif }}</h2>
                                <small>
                                    <i class="fas fa-briefcase me-1"></i>Sedang dibuka
                                </small>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <!-- Aplikasi per Lowongan -->
                <div class="col-xl-8 col-lg-7">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Aplikasi per Lowongan</h6>
                            <select class="form-select w-50" id="positionSelect" aria-label="Pilih Lowongan">
                                <option value="">Semua Lowongan</option>
                                @foreach($lowonganList as $lowongan)
                                    <option value="{{ $lowongan->id }}">{{ $lowongan->position_title }}</option>
                                @endforeach
                            </select>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary active" onclick="changeChartView('bar')">Bar</button>
                                <button class="btn btn-outline-primary" onclick="changeChartView('line')">Line</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="lowonganChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Status Distribution -->
                <div class="col-xl-4 col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h6>Distribusi Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map and Quick Stats -->
            <div class="row mb-4">
                <!-- Indonesia Map -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Distribusi Kandidat per Kota</h6>
                            <div>
                                <button class="btn btn-sm btn-outline-primary" onclick="showCityView()">
                                    <i class="fas fa-eye me-1"></i>Detail Kota
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="indonesiaMap" class="map-container"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Top Cities -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h6>Top 5 Kota</h6>
                            <button class="btn btn-sm btn-outline-primary" onclick="showAllCitiesChart()"> 
                                <i class="fas fa-chart-pie me-1"></i>Tampilkan Semua
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="topCities">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <!--<div class="row mb-4">-->
            <!--    <div class="col-xl-4 col-md-6">-->
            <!--        <div class="card">-->
            <!--            <div class="card-body text-center">-->
            <!--                <div class="stat-icon text-primary mb-3">-->
            <!--                    <i class="fas fa-percentage"></i>-->
            <!--                </div>-->
            <!--                <h3 class="text-primary" id="conversionRate">36.2%</h3>-->
            <!--                <p class="text-muted mb-0">Tingkat Penerimaan</p>-->
            <!--                <small class="text-success">-->
            <!--                    <i class="fas fa-arrow-up"></i> +2.1% dari bulan lalu-->
            <!--                </small>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
                
            <!--    <div class="col-xl-4 col-md-6">-->
            <!--        <div class="card">-->
            <!--            <div class="card-body text-center">-->
            <!--                <div class="stat-icon text-info mb-3">-->
            <!--                    <i class="fas fa-clock"></i>-->
            <!--                </div>-->
            <!--                <h3 class="text-info" id="avgProcessTime">4.2</h3>-->
            <!--                <p class="text-muted mb-0">Rata-rata Hari Proses</p>-->
            <!--                <small class="text-success">-->
            <!--                    <i class="fas fa-arrow-down"></i> -0.8 hari lebih cepat-->
            <!--                </small>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
                
            <!--    <div class="col-xl-4 col-md-12">-->
            <!--        <div class="card">-->
            <!--            <div class="card-body text-center">-->
            <!--                <div class="stat-icon text-warning mb-3">-->
            <!--                    <i class="fas fa-star"></i>-->
            <!--                </div>-->
            <!--                <h3 class="text-warning" id="qualityScore">8.7</h3>-->
            <!--                <p class="text-muted mb-0">Skor Kualitas Kandidat</p>-->
            <!--                <small class="text-success">-->
            <!--                    <i class="fas fa-arrow-up"></i> +0.3 poin-->
            <!--                </small>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->

            <!-- Recent Activities & Data Table -->
            <div class="row">
                <div class="col-12">
                    <!-- Advanced Filter Section -->
                    <div class="filter-container border">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <h5>Filter Kandidat</h5>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <!-- Search by Name/Email -->
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="searchName" placeholder="Masukkan nama atau email...">
                            </div>
                            
                            <!-- Position Filter -->
                            <div class="col-md-3">
                                <select class="form-select" id="filterPosition">
                                    <option value="">Semua Posisi</option>
                                </select>
                            </div>
                            
                            <!-- Location Filter -->
                            <div class="col-md-3">
                                <select class="form-select" id="filterLocation">
                                    <option value="">Semua Lokasi</option>
                                </select>
                            </div>
                            
                            <!-- Status Filter -->
                            <div class="col-md-3">
                                <select class="form-select" id="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="pending">Proses</option>
                                    <option value="approved">Diterima</option>
                                    <option value="rejected">Ditolak</option>
                                    <option value="save">Disimpan</option>
                                </select>
                            </div>
            
                            <div class="col-md-3">
                                <select class="form-select" id="filterExperience">
                                    <option value="">Semua Pengalaman Kerja</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <select class="form-select" id="filterEducation">
                                    <option value="">Semua Perguruan Tinggi</option>
                                </select>
                            </div>
            
                            <!-- Date Range Filter -->
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="dateFrom" placeholder="Tanggal Dari">
                            </div>
                            
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="dateTo" placeholder="Tanggal Sampai">
                            </div>
                            
                            <!-- Action Buttons -->
                            <!--<div class="col-12 mt-3">-->
                            <!--    <button class="btn btn-outline-primary me-2" onclick="applyFilters()">-->
                            <!--        <i class="fas fa-search me-1"></i>Terapkan Filter-->
                            <!--    </button>-->
                            <!--    <button class="btn btn-outline-danger" onclick="clearAllFilters()">-->
                            <!--        <i class="fas fa-eraser me-1"></i>Bersihkan Filter-->
                            <!--    </button>-->
                            <!--</div>-->
                        </div>
                    </div>
            
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Aktivitas & Data Kandidat Terbaru</h6>
                            <div>
                                <button class="btn btn-primary btn-sm me-2" onclick="exportData()">
                                    <i class="fas fa-download me-1"></i>Export
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="candidateTable">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Posisi</th>
                                            <th>Lokasi</th>
                                            <th>Tanggal Daftar</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="candidateTableBody">
                                        <!-- Will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <!-- Data Table Section (Hidden by default) -->
            <div id="datatable-section" style="display: none;">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2><i class="fas fa-table me-2"></i>Data Karyawan</h2>
                            <button class="btn btn-secondary" onclick="showDashboard()">
                                <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
                            </button>
                        </div>
                    </div>
                </div>
    
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-lg-2 col-md-6 mb-2">
                        <select class="form-select" id="statusFilter">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <select class="form-select" id="experienceFilter">
                        <option value="">Semua Pengalaman</option>
                        <option value="fresh_graduate">Fresh Graduate</option>
                        <option value="0-1">0-1 Tahun</option>
                        <option value="1-3">1-3 Tahun</option>
                        <option value="3-5">3-5 Tahun</option>
                        <option value="5+">5+ Tahun</option>
                    </select>

                    <div class="col-lg-2 col-md-6 mb-2">
                        <input type="date" class="form-control" id="dateFilter">
                    </div>
                    <div class="col-lg-4 col-md-8 mb-2">
                        <input type="text" class="form-control" id="searchFilter" placeholder="Cari nama atau email...">
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <button class="btn btn-primary w-100" onclick="applyFilters()">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                    </div>
                </div>
    
                <!-- Data Table - Updated Header -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="employeeTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Lengkap</th>
                                        <th>Email</th>
                                        <th>Telepon</th>
                                        <th>Posisi Dilamar</th>
                                        <th>Pengalaman</th>
                                        <th>Tanggal Daftar</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <!-- Data akan diisi dengan JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="candidateModal" tabindex="-1" aria-labelledby="candidateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="candidateModalLabel">Detail Kandidat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img id="candidatePhoto" src="" alt="Foto Kandidat" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <h5 id="candidateName"></h5>
                            <p id="candidateEmail" class="text-muted"></p>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Posisi:</strong>
                                    <p id="candidatePosition"></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Lokasi:</strong>
                                    <p id="candidateLocation"></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>No. Telepon:</strong>
                                    <p id="candidatePhone"></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Tanggal Lahir:</strong>
                                    <p id="candidateBirthDate"></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Gaji Terakhir:</strong>
                                    <p id="candidateLastSalary"></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Gaji Diharapkan:</strong>
                                    <p id="candidateExpectedSalary"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Pendidikan -->
                    <div class="mb-4">
                        <h6><i class="fas fa-graduation-cap me-2"></i>Pendidikan</h6>
                        <div id="candidateEducation"></div>
                    </div>
                    
                    <!-- Pengalaman Kerja -->
                    <div class="mb-4">
                        <h6><i class="fas fa-briefcase me-2"></i>Pengalaman Kerja</h6>
                        <div id="candidateExperience"></div>
                    </div>
                    
                    <!-- Informasi Tambahan -->
                    <div class="mb-4">
                        <h6><i class="fas fa-info-circle me-2"></i>Informasi Tambahan</h6>
                        <p id="candidateAdditionalInfo"></p>
                    </div>
                    
                    <!-- CV -->
                    <div class="mb-3">
                        <h6><i class="fas fa-file-pdf me-2"></i>CV</h6>
                        <a id="candidateCV" href="#" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-1"></i>Download CV
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-between w-100">
                        <div>
                            <button type="button" class="btn btn-success" onclick="updateStatus('Lanjut')">
                                <i class="fas fa-check me-1"></i>Lanjut
                            </button>
                            <button type="button" class="btn btn-danger" onclick="updateStatus('Ditolak')">
                                <i class="fas fa-times me-1"></i>Tolak
                            </button>
                            <button type="button" class="btn btn-warning" onclick="updateStatus('Simpan')">
                                <i class="fas fa-bookmark me-1"></i>Simpan
                            </button>
                        </div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal untuk Pie Chart -->
    <div class="modal fade" id="allCitiesModal" tabindex="-1" aria-labelledby="allCitiesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allCitiesModalLabel">
                        <i class="fas fa-chart-pie me-2"></i>Distribusi Kandidat Semua Kota
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Chart Container -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <canvas id="citiesPieChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                    
                    <!-- Statistics Summary -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-primary" id="totalCitiesCount">0</h5>
                                    <small class="text-muted">Total Kota</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-success" id="totalCandidatesCount">0</h5>
                                    <small class="text-muted">Total Kandidat</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detailed List -->
                    <div class="mt-4">
                        <h6>Detail per Kota:</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kota</th>
                                        <th>Jumlah Kandidat</th>
                                        <th>Persentase</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="citiesDetailTable">
                                    <!-- Will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="downloadChartData()">
                        <i class="fas fa-download me-1"></i>Download Data
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Detail Kota -->
    <div class="modal fade" id="cityDetailModal" tabindex="-1" aria-labelledby="cityDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cityDetailModalLabel">Detail Kota</h5>
                </div>
                <div class="modal-body">
                    <div id="cityDetailContent">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Modal Detail Karyawan - Enhanced Version -->
    <div class="modal fade employee-modal" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeModalLabel">
                        <i class="fas fa-user-circle"></i>
                        Detail Kandidat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Loading spinner -->
                    <div class="loading-spinner" id="loadingSpinner">
                        <div class="spinner"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modern btn-pdf" onclick="downloadPDF()">
                        <i class="fas fa-file-pdf"></i>
                        Download PDF
                    </button>
                    <button type="button" class="btn btn-modern btn-close-modal" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Tutup
                    </button>
                    <!-- Button Mungkin - New Addition -->
                    <button type="button" class="btn btn-modern btn-maybe" onclick="maybeEmployee()" id="maybeBtn" style="display: none;">
                        <i class="fas fa-bookmark"></i>
                        Mungkin
                    </button>
                    <button type="button" class="btn btn-modern btn-approve" onclick="approveEmployee()" id="approveBtn" style="display: none;">
                        <i class="fas fa-check"></i>
                        Setujui
                    </button>
                    <button type="button" class="btn btn-modern btn-reject" onclick="rejectEmployee()" id="rejectBtn" style="display: none;">
                        <i class="fas fa-times"></i>
                        Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let currentCandidateId = null;
        
        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadFilterOptions();
            loadCandidateData();
        });
        
        // Load filter options
        function loadFilterOptions() {
            fetch('{{ route("admin.kandidat.filter.options") }}')
                .then(response => response.json())
                .then(data => {
                    // Populate position filter
                    const positionSelect = document.getElementById('filterPosition');
                    data.positions.forEach(position => {
                        const option = document.createElement('option');
                        option.value = position.id;
                        option.textContent = position.position_title;
                        positionSelect.appendChild(option);
                    });
                    
                    // Populate location filter
                    const locationSelect = document.getElementById('filterLocation');
                    data.locations.forEach(location => {
                        const option = document.createElement('option');
                        option.value = location;
                        option.textContent = location;
                        locationSelect.appendChild(option);
                    });
                    
                    // Populate experience filter
                    const experienceSelect = document.getElementById('filterExperience');
                    data.experiences.forEach(experience => {
                        const option = document.createElement('option');
                        option.value = experience;
                        option.textContent = experience;
                        experienceSelect.appendChild(option);
                    });
                    
                    // Populate education filter
                    const educationSelect = document.getElementById('filterEducation');
                    data.educations.forEach(education => {
                        const option = document.createElement('option');
                        option.value = education;
                        option.textContent = education;
                        educationSelect.appendChild(option);
                    });
                });
        }
        
        // Load candidate data
        function loadCandidateData() {
            const params = new URLSearchParams({
                search: document.getElementById('searchName').value,
                position: document.getElementById('filterPosition').value,
                location: document.getElementById('filterLocation').value,
                status: document.getElementById('filterStatus').value,
                experience: document.getElementById('filterExperience').value,
                education: document.getElementById('filterEducation').value,
                date_from: document.getElementById('dateFrom').value,
                date_to: document.getElementById('dateTo').value
            });
        
            fetch(`{{ route("admin.kandidat.data") }}?${params}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('candidateTableBody');
                    tbody.innerHTML = '';
                    
                    data.data.forEach(candidate => {
                        const row = document.createElement('tr');
                        
                        // Status badge
                        let statusBadge = '';
                        switch(candidate.status) {
                            case 'Pending':
                                statusBadge = '<span class="badge bg-warning">Proses</span>';
                                break;
                            case 'Lanjut':
                                statusBadge = '<span class="badge bg-info">Lolos</span>';
                                break;
                            case 'Diterima':
                                statusBadge = '<span class="badge bg-success">Diterima</span>';
                                break;
                            case 'Ditolak':
                                statusBadge = '<span class="badge bg-danger">Ditolak</span>';
                                break;
                            case 'Simpan':
                                statusBadge = '<span class="badge bg-info">Disimpan</span>';
                                break;
                            default:
                                statusBadge = '<span class="badge bg-secondary">Unknown</span>';
                        }
                        
                        row.innerHTML = `
                            <td>${candidate.nama}</td>
                            <td>${candidate.email}</td>
                            <td>${candidate.posisi}</td>
                            <td>${candidate.lokasi}</td>
                            <td>${candidate.tanggal_daftar}</td>
                            <td>${statusBadge}</td>
                           
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="showCandidateDetail('${candidate.id}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        `;
                        
                        tbody.appendChild(row);
                    });
                });
        }
        
        // Show candidate detail modal
        function showCandidateDetail(candidateId) {
            // Find candidate data
            const params = new URLSearchParams({
                search: document.getElementById('searchName').value,
                position: document.getElementById('filterPosition').value,
                location: document.getElementById('filterLocation').value,
                status: document.getElementById('filterStatus').value,
                experience: document.getElementById('filterExperience').value,
                education: document.getElementById('filterEducation').value,
                date_from: document.getElementById('dateFrom').value,
                date_to: document.getElementById('dateTo').value
            });
        
            fetch(`{{ route("admin.kandidat.data") }}?${params}`)
                .then(response => response.json())
                .then(data => {
                    const candidate = data.data.find(c => c.id === candidateId);
                    if (candidate) {
                        currentCandidateId = candidateId;
                        
                        // Populate modal
                        document.getElementById('candidateName').textContent = candidate.nama;
                        document.getElementById('candidateEmail').textContent = candidate.email;
                        document.getElementById('candidatePosition').textContent = candidate.posisi;
                        document.getElementById('candidateLocation').textContent = candidate.lokasi;
                        document.getElementById('candidatePhone').textContent = candidate.no_telepon || 'N/A';
                        document.getElementById('candidateBirthDate').textContent = candidate.tanggal_lahir || 'N/A';
                        document.getElementById('candidateLastSalary').textContent = candidate.gaji_terakhir || 'N/A';
                        document.getElementById('candidateExpectedSalary').textContent = candidate.gaji_diharapkan || 'N/A';
                        document.getElementById('candidateAdditionalInfo').textContent = candidate.informasi_tambahan || 'Tidak ada informasi tambahan';
                        
                        // Photo
                        // const photoElement = document.getElementById('candidatePhoto');
                        // if (candidate.foto) {
                        //     photoElement.src = candidate.foto;
                        // } else {
                        //     photoElement.src = '/images/default-avatar.png';
                        // }
                        
                        const photoElement = document.getElementById('candidatePhoto');
                        if (candidate.foto) {
                            photoElement.src = `/foto/${candidate.foto}`;
                        } else {
                            photoElement.src = '/images/default-avatar.png';
                        }
                        
                        // CV
                        // const cvElement = document.getElementById('candidateCV');
                        // if (candidate.cv) {
                        //     cvElement.href = candidate.cv;
                        //     cvElement.style.display = 'inline-block';
                        // } else {
                        //     cvElement.style.display = 'none';
                        // }
                        
                        // Di bagian CV
                        const cvElement = document.getElementById('candidateCV');
                        if (candidate.cv) {
                            cvElement.href = `/cv/${candidate.cv}`;
                            cvElement.style.display = 'inline-block';
                        } else {
                            cvElement.style.display = 'none';
                        }
                        
                        // Education
                        const educationDiv = document.getElementById('candidateEducation');
                        if (candidate.pendidikan_formal) {
                            educationDiv.innerHTML = '';
                            Object.values(candidate.pendidikan_formal).forEach(edu => {
                                const eduCard = document.createElement('div');
                                eduCard.className = 'card mb-2';
                                eduCard.innerHTML = `
                                    <div class="card-body">
                                        <h6 class="card-title">${edu.nama_sekolah}</h6>
                                        <p class="card-text">
                                            <strong>Jenjang:</strong> ${edu.jenjang}<br>
                                            <strong>Tahun:</strong> ${edu.tahun_masuk} - ${edu.tahun_keluar}<br>
                                            <strong>Nilai:</strong> ${edu.nilai}
                                        </p>
                                    </div>
                                `;
                                educationDiv.appendChild(eduCard);
                            });
                        } else {
                            educationDiv.innerHTML = '<p>Tidak ada data pendidikan</p>';
                        }
                        
                        // Experience
                        const experienceDiv = document.getElementById('candidateExperience');
                        if (candidate.pengalaman_kerja) {
                            experienceDiv.innerHTML = '';
                            Object.values(candidate.pengalaman_kerja).forEach(exp => {
                                const expCard = document.createElement('div');
                                expCard.className = 'card mb-2';
                                expCard.innerHTML = `
                                    <div class="card-body">
                                        <h6 class="card-title">${exp.nama_perusahaan}</h6>
                                        <p class="card-text">
                                            <strong>Jabatan:</strong> ${exp.jabatan}<br>
                                            <strong>Periode:</strong> ${exp.masa_kerja_dari} - ${exp.masa_kerja_sampai}<br>
                                            <strong>Uraian Pekerjaan:</strong><br>
                                            ${exp.uraian_pekerjaan.replace(/\n/g, '<br>')}<br>
                                            <strong>Alasan Berhenti:</strong> ${exp.alasan_berhenti}
                                        </p>
                                    </div>
                                `;
                                experienceDiv.appendChild(expCard);
                            });
                        } else {
                            experienceDiv.innerHTML = '<p>Tidak ada data pengalaman kerja</p>';
                        }
                        
                        // Show modal
                        const modal = new bootstrap.Modal(document.getElementById('candidateModal'));
                        modal.show();
                    }
                });
        }
        
        // Update candidate status
        function updateStatus(status) {
            if (!currentCandidateId) return;
            
            fetch(`{{ route("admin.kandidat.update.status", "") }}/${currentCandidateId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Status berhasil diperbarui');
                    loadCandidateData();
                    bootstrap.Modal.getInstance(document.getElementById('candidateModal')).hide();
                }
            });
        }
        
        // Apply filters
        function applyFilters() {
            loadCandidateData();
        }
        
        // Clear all filters
        function clearAllFilters() {
            document.getElementById('searchName').value = '';
            document.getElementById('filterPosition').value = '';
            document.getElementById('filterLocation').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterExperience').value = '';
            document.getElementById('filterEducation').value = '';
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            loadCandidateData();
        }
        
        // Export data
        function exportData() {
            // Implementation for data export
            alert('Fitur export sedang dikembangkan');
        }
    </script>
    
    <script>
        // Candidate Management JavaScript
        class CandidateManager {
            constructor() {
                this.currentCandidateId = null;
                this.currentCandidateData = null;
                this.init();
            }
        
            init() {
                this.bindEvents();
                this.loadFilterOptions();
                this.loadCandidateData();
            }
        
            bindEvents() {
                // Filter events
                document.getElementById('searchName').addEventListener('input', 
                    this.debounce(() => this.loadCandidateData(), 500)
                );
        
                // Real-time filter on change
                const filterElements = [
                    'filterPosition', 'filterLocation', 'filterStatus', 
                    'filterExperience', 'filterEducation', 'dateFrom', 'dateTo'
                ];
                
                filterElements.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.addEventListener('change', () => this.loadCandidateData());
                    }
                });
            }
        
            debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Helper function to convert storage path to public path
            convertToPublicPath(storagePath) {
                if (!storagePath) {
                    console.log('No storage path provided');
                    return '/images/default-avatar.png';
                }
                
                console.log('Original storage path:', storagePath);
                
                let publicPath = storagePath;
                
                // Remove any leading slash first
                if (publicPath.startsWith('/')) {
                    publicPath = publicPath.substring(1);
                }
                
                // Remove 'storage/app/public/' prefix if exists
                if (publicPath.startsWith('storage/app/public/')) {
                    publicPath = publicPath.replace('storage/app/public/', '');
                }
                // Remove 'storage/' prefix if exists (this is the main case)
                else if (publicPath.startsWith('storage/')) {
                    publicPath = publicPath.replace('storage/', '');
                }
                
                // Ensure path starts with '/'
                if (!publicPath.startsWith('/')) {
                    publicPath = '/' + publicPath;
                }
                
                console.log('Final public path:', publicPath);
                return publicPath;
            }
        
            async loadFilterOptions() {
                try {
                    const response = await fetch(window.routes.filterOptions);
                    const data = await response.json();
                    
                    this.populateFilterOptions(data);
                } catch (error) {
                    console.error('Error loading filter options:', error);
                }
            }
        
            populateFilterOptions(data) {
                // Populate position filter
                const positionSelect = document.getElementById('filterPosition');
                positionSelect.innerHTML = '<option value="">Semua Posisi</option>';
                data.positions.forEach(position => {
                    const option = document.createElement('option');
                    option.value = position.id;
                    option.textContent = position.position_title;
                    positionSelect.appendChild(option);
                });
                
                // Populate location filter
                const locationSelect = document.getElementById('filterLocation');
                locationSelect.innerHTML = '<option value="">Semua Lokasi</option>';
                data.locations.forEach(location => {
                    const option = document.createElement('option');
                    option.value = location;
                    option.textContent = location;
                    locationSelect.appendChild(option);
                });
                
                // Populate experience filter
                const experienceSelect = document.getElementById('filterExperience');
                experienceSelect.innerHTML = '<option value="">Semua Pengalaman Kerja</option>';
                data.experiences.forEach(experience => {
                    const option = document.createElement('option');
                    option.value = experience;
                    option.textContent = experience;
                    experienceSelect.appendChild(option);
                });
                
                // Populate education filter
                const educationSelect = document.getElementById('filterEducation');
                educationSelect.innerHTML = '<option value="">Semua Perguruan Tinggi</option>';
                data.educations.forEach(education => {
                    const option = document.createElement('option');
                    option.value = education;
                    option.textContent = education;
                    educationSelect.appendChild(option);
                });
            }
        
            getFilterParams() {
                return new URLSearchParams({
                    search: document.getElementById('searchName').value,
                    position: document.getElementById('filterPosition').value,
                    location: document.getElementById('filterLocation').value,
                    status: document.getElementById('filterStatus').value,
                    experience: document.getElementById('filterExperience').value,
                    education: document.getElementById('filterEducation').value,
                    date_from: document.getElementById('dateFrom').value,
                    date_to: document.getElementById('dateTo').value
                });
            }
        
            async loadCandidateData() {
                try {
                    const params = this.getFilterParams();
                    const response = await fetch(`${window.routes.candidateData}?${params}`);
                    const data = await response.json();
                    
                    this.renderCandidateTable(data.data);
                } catch (error) {
                    console.error('Error loading candidate data:', error);
                    this.showError('Gagal memuat data kandidat');
                }
            }
        
            renderCandidateTable(candidates) {
                const tbody = document.getElementById('candidateTableBody');
                tbody.innerHTML = '';
                
                if (candidates.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data kandidat yang ditemukan</td>
                        </tr>
                    `;
                    return;
                }
                
                candidates.forEach(candidate => {
                    const row = document.createElement('tr');
                    row.innerHTML = this.renderCandidateRow(candidate);
                    tbody.appendChild(row);
                });
            }
        
            renderCandidateRow(candidate) {
                const statusBadge = this.getStatusBadge(candidate.status);
                const scoreColor = this.getScoreColor(candidate.score);
                
                // Debug: Log candidate data
                console.log('Candidate data:', candidate);
                console.log('Original foto path:', candidate.foto);
                
                // Convert storage path to public path
                const photoPath = this.convertToPublicPath(candidate.foto);
                console.log('Final photo path:', photoPath);
                
                return `
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="${photoPath}" 
                                 class="rounded-circle me-2" 
                                 style="width: 32px; height: 32px; object-fit: cover;"
                                 onerror="console.error('Image failed to load:', this.src); this.src='/images/default-avatar.png'">
                            <span>${candidate.nama}</span>
                        </div>
                    </td>
                    <td>${candidate.email}</td>
                    <td>
                        <span class="badge bg-light text-dark">${candidate.posisi}</span>
                    </td>
                    <td>${candidate.lokasi}</td>
                    <td>${candidate.tanggal_daftar}</td>
                    <td>${statusBadge}</td>
                    
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-primary" 
                                    onclick="candidateManager.showCandidateDetail('${candidate.id}')"
                                    title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            
                        </div>
                    </td>
                `;
            }
        
            getStatusBadge(status) {
                const badges = {
                    'Pending': '<span class="badge bg-warning">Proses</span>',
                    'Lanjut': '<span class="badge bg-info">Lolos</span>',
                    'Diterima': '<span class="badge bg-success">Diterima</span>',
                    'Ditolak': '<span class="badge bg-danger">Ditolak</span>',
                    'Simpan': '<span class="badge bg-info">Disimpan</span>'
                };
                return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
            }
        
            getScoreColor(score) {
                if (score >= 80) return 'bg-success';
                if (score >= 60) return 'bg-warning';
                if (score >= 40) return 'bg-info';
                return 'bg-danger';
            }
        
            async showCandidateDetail(candidateId) {
                try {
                    const params = this.getFilterParams();
                    const response = await fetch(`${window.routes.candidateData}?${params}`);
                    const data = await response.json();
                    
                    const candidate = data.data.find(c => c.id === candidateId);
                    if (candidate) {
                        this.currentCandidateId = candidateId;
                        this.currentCandidateData = candidate;
                        this.populateModal(candidate);
                        this.showModal();
                    }
                } catch (error) {
                    console.error('Error loading candidate detail:', error);
                    this.showError('Gagal memuat detail kandidat');
                }
            }
        
            populateModal(candidate) {
                // Basic info
                document.getElementById('candidateName').textContent = candidate.nama;
                document.getElementById('candidateEmail').textContent = candidate.email;
                document.getElementById('candidatePosition').textContent = candidate.posisi;
                document.getElementById('candidateLocation').textContent = candidate.lokasi;
                document.getElementById('candidatePhone').textContent = candidate.no_telepon || 'N/A';
                document.getElementById('candidateBirthDate').textContent = candidate.tanggal_lahir || 'N/A';
                document.getElementById('candidateLastSalary').textContent = this.formatCurrency(candidate.gaji_terakhir);
                document.getElementById('candidateExpectedSalary').textContent = this.formatCurrency(candidate.gaji_diharapkan);
                document.getElementById('candidateAdditionalInfo').textContent = candidate.informasi_tambahan || 'Tidak ada informasi tambahan';
                
                // Photo - Convert storage path to public path
                const photoElement = document.getElementById('candidatePhoto');
                const photoPath = this.convertToPublicPath(candidate.foto);
                photoElement.src = photoPath;
                photoElement.onerror = function() { this.src = '/images/default-avatar.png'; };
                
                // CV - Convert storage path to public path if needed
                const cvElement = document.getElementById('candidateCV');
                if (candidate.cv) {
                    const cvPath = this.convertToPublicPath(candidate.cv);
                    cvElement.href = cvPath;
                    cvElement.style.display = 'inline-block';
                } else {
                    cvElement.style.display = 'none';
                }
                
                // Education
                this.populateEducation(candidate.pendidikan_formal);
                
                // Experience
                this.populateExperience(candidate.pengalaman_kerja);
            }
        
            populateEducation(educationData) {
                const educationDiv = document.getElementById('candidateEducation');
                educationDiv.innerHTML = '';
                
                if (educationData && Object.keys(educationData).length > 0) {
                    Object.values(educationData).forEach(edu => {
                        const eduCard = document.createElement('div');
                        eduCard.className = 'card mb-2';
                        eduCard.innerHTML = `
                            <div class="card-body">
                                <h6 class="card-title">${edu.nama_sekolah}</h6>
                                <p class="card-text">
                                    <strong>Jenjang:</strong> ${edu.jenjang}<br>
                                    <strong>Tahun:</strong> ${edu.tahun_masuk} - ${edu.tahun_keluar}<br>
                                    <strong>Nilai:</strong> ${edu.nilai}
                                </p>
                            </div>
                        `;
                        educationDiv.appendChild(eduCard);
                    });
                } else {
                    educationDiv.innerHTML = '<p class="text-muted">Tidak ada data pendidikan</p>';
                }
            }
        
            populateExperience(experienceData) {
                const experienceDiv = document.getElementById('candidateExperience');
                experienceDiv.innerHTML = '';
                
                if (experienceData && Object.keys(experienceData).length > 0) {
                    Object.values(experienceData).forEach(exp => {
                        const expCard = document.createElement('div');
                        expCard.className = 'card mb-2';
                        expCard.innerHTML = `
                            <div class="card-body">
                                <h6 class="card-title">${exp.nama_perusahaan}</h6>
                                <p class="card-text">
                                    <strong>Jabatan:</strong> ${exp.jabatan}<br>
                                    <strong>Periode:</strong> ${this.formatDate(exp.masa_kerja_dari)} - ${this.formatDate(exp.masa_kerja_sampai)}<br>
                                    <strong>Uraian Pekerjaan:</strong><br>
                                    <div class="ms-3">${exp.uraian_pekerjaan.replace(/\n/g, '<br>')}</div>
                                    <strong>Alasan Berhenti:</strong> ${exp.alasan_berhenti}
                                </p>
                            </div>
                        `;
                        experienceDiv.appendChild(expCard);
                    });
                } else {
                    experienceDiv.innerHTML = '<p class="text-muted">Tidak ada data pengalaman kerja</p>';
                }
            }
        
            showModal() {
                const modal = new bootstrap.Modal(document.getElementById('candidateModal'));
                modal.show();
            }
        
            async updateStatus(status) {
                if (!this.currentCandidateId) return;
                
                try {
                    const response = await fetch(`${window.routes.updateStatus}/${this.currentCandidateId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ status: status })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.showSuccess('Status berhasil diperbarui');
                        this.loadCandidateData();
                        bootstrap.Modal.getInstance(document.getElementById('candidateModal')).hide();
                    } else {
                        this.showError('Gagal memperbarui status');
                    }
                } catch (error) {
                    console.error('Error updating status:', error);
                    this.showError('Gagal memperbarui status');
                }
            }
        
            async quickUpdateStatus(candidateId, status) {
                if (!confirm(`Apakah Anda yakin ingin mengubah status kandidat ini?`)) return;
                
                try {
                    const response = await fetch(`${window.routes.updateStatus}/${candidateId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ status: status })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.showSuccess('Status berhasil diperbarui');
                        this.loadCandidateData();
                    } else {
                        this.showError('Gagal memperbarui status');
                    }
                } catch (error) {
                    console.error('Error updating status:', error);
                    this.showError('Gagal memperbarui status');
                }
            }
        
            applyFilters() {
                this.loadCandidateData();
            }
        
            clearAllFilters() {
                document.getElementById('searchName').value = '';
                document.getElementById('filterPosition').value = '';
                document.getElementById('filterLocation').value = '';
                document.getElementById('filterStatus').value = '';
                document.getElementById('filterExperience').value = '';
                document.getElementById('filterEducation').value = '';
                document.getElementById('dateFrom').value = '';
                document.getElementById('dateTo').value = '';
                this.loadCandidateData();
            }
        
            exportData() {
                const params = this.getFilterParams();
                window.open(`${window.routes.exportData}?${params}`, '_blank');
            }
        
            formatCurrency(amount) {
                if (!amount) return 'N/A';
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);
            }
        
            formatDate(dateString) {
                if (!dateString) return 'N/A';
                return new Date(dateString).toLocaleDateString('id-ID');
            }
        
            showSuccess(message) {
                // You can use any toast library here
                alert(message);
            }
        
            showError(message) {
                // You can use any toast library here
                alert(message);
            }
        }
        
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            window.candidateManager = new CandidateManager();
        });
        
        // Global functions for backward compatibility
        function applyFilters() {
            window.candidateManager.applyFilters();
        }
        
        function clearAllFilters() {
            window.candidateManager.clearAllFilters();
        }
        
        function exportData() {
            window.candidateManager.exportData();
        }
        
        function updateStatus(status) {
            window.candidateManager.updateStatus(status);
        }
    </script>
    
    <script>
        // Variabel global untuk chart
        let lowonganChart = null;
        let statusChart = null;
        let currentChartType = 'bar';
        
        // Inisialisasi chart saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            
            // Event listener untuk dropdown posisi
            document.getElementById('positionSelect').addEventListener('change', function() {
                loadChartData();
            });
        });
        
        // Function untuk inisialisasi chart
        function initializeCharts() {
            // Inisialisasi chart aplikasi per lowongan
            const lowonganCtx = document.getElementById('lowonganChart').getContext('2d');
            lowonganChart = new Chart(lowonganCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Jumlah Aplikasi',
                        data: [],
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        
            // Inisialisasi chart status distribution
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            statusChart = new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 205, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 205, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        
            // Load data awal
            loadChartData();
        }
        
        // Function untuk load data chart
        function loadChartData() {
            const positionId = document.getElementById('positionSelect').value;
            
            // Load data aplikasi per lowongan
            let applicationUrl = positionId ? 
                '{{ route("admin.kandidat.applications.by.position") }}?position_id=' + positionId :
                '{{ route("admin.kandidat.all.applications.chart") }}';
            
            fetch(applicationUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateLowonganChart(data.data);
                    }
                })
                .catch(error => {
                    console.error('Error loading application data:', error);
                });
        
            // Load data distribusi status
            let statusUrl = positionId ? 
                '{{ route("admin.kandidat.status.distribution") }}?position_id=' + positionId :
                '{{ route("admin.kandidat.all.status.distribution") }}';
            
            fetch(statusUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateStatusChart(data.data);
                    }
                })
                .catch(error => {
                    console.error('Error loading status data:', error);
                });
        }
        
        // Function untuk update chart aplikasi per lowongan
        function updateLowonganChart(data) {
            const labels = data.map(item => item.month);
            const counts = data.map(item => item.count);
            
            lowonganChart.data.labels = labels;
            lowonganChart.data.datasets[0].data = counts;
            lowonganChart.update();
        }
        
        // Function untuk update chart status distribution
        function updateStatusChart(data) {
            const labels = data.map(item => item.label);
            const values = data.map(item => item.value);
            
            statusChart.data.labels = labels;
            statusChart.data.datasets[0].data = values;
            statusChart.update();
        }
        
        // Function untuk mengubah tipe chart (bar/line)
        function changeChartView(type) {
            currentChartType = type;
            
            // Update button states
            document.querySelectorAll('.btn-group button').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Update chart type
            lowonganChart.config.type = type;
            
            // Update styling berdasarkan tipe chart
            if (type === 'line') {
                lowonganChart.data.datasets[0].backgroundColor = 'rgba(54, 162, 235, 0.2)';
                lowonganChart.data.datasets[0].fill = true;
                lowonganChart.data.datasets[0].tension = 0.4;
            } else {
                lowonganChart.data.datasets[0].backgroundColor = 'rgba(54, 162, 235, 0.6)';
                lowonganChart.data.datasets[0].fill = false;
                lowonganChart.data.datasets[0].tension = 0;
            }
            
            lowonganChart.update();
        }
    </script>
    
    
    // <script>
    //     // Extended Dummy Data
    //     const dummyData = {
    //         candidates: [
    //             {id: 1, nama: 'Ahmad Rizki', email: 'ahmad.rizki@email.com', posisi: 'Frontend Developer', lokasi: 'Jakarta', tanggal: '2024-06-25', status: 'pending', score: 8.5},
    //             {id: 2, nama: 'Siti Nurhaliza', email: 'siti.nur@email.com', posisi: 'UI/UX Designer', lokasi: 'Bandung', tanggal: '2024-06-24', status: 'approved', score: 9.2},
    //             {id: 3, nama: 'Budi Santoso', email: 'budi.santoso@email.com', posisi: 'Backend Developer', lokasi: 'Surabaya', tanggal: '2024-06-23', status: 'pending', score: 7.8},
    //             {id: 4, nama: 'Maya Puspita', email: 'maya.puspita@email.com', posisi: 'Product Manager', lokasi: 'Yogyakarta', tanggal: '2024-06-22', status: 'save', score: 8.9},
    //             {id: 5, nama: 'Doni Pratama', email: 'doni.pratama@email.com', posisi: 'DevOps Engineer', lokasi: 'Medan', tanggal: '2024-06-21', status: 'rejected', score: 6.5},
    //             {id: 6, nama: 'Lestari Wati', email: 'lestari.wati@email.com', posisi: 'Data Analyst', lokasi: 'Makassar', tanggal: '2024-06-20', status: 'approved', score: 8.7},
    //             {id: 7, nama: 'Andi Setiawan', email: 'andi.setiawan@email.com', posisi: 'Mobile Developer', lokasi: 'Denpasar', tanggal: '2024-06-19', status: 'pending', score: 8.1},
    //             {id: 8, nama: 'Rini Susanti', email: 'rini.susanti@email.com', posisi: 'QA Engineer', lokasi: 'Palembang', tanggal: '2024-06-18', status: 'approved', score: 8.3},
    //             {id: 9, nama: 'Fauzi Rahman', email: 'fauzi.rahman@email.com', posisi: 'Frontend Developer', lokasi: 'Jakarta', tanggal: '2024-06-17', status: 'pending', score: 7.2},
    //             {id: 10, nama: 'Indah Permata', email: 'indah.permata@email.com', posisi: 'UI/UX Designer', lokasi: 'Bandung', tanggal: '2024-06-16', status: 'save', score: 9.5},
    //             {id: 11, nama: 'Bayu Setiawan', email: 'bayu.setiawan@email.com', posisi: 'Backend Developer', lokasi: 'Surabaya', tanggal: '2024-06-15', status: 'approved', score: 8.8},
    //             {id: 12, nama: 'Dewi Sartika', email: 'dewi.sartika@email.com', posisi: 'Data Analyst', lokasi: 'Jakarta', tanggal: '2024-06-14', status: 'rejected', score: 5.9}
    //         ],
            
    //         lowonganData: {
    //             labels: ['Frontend Developer', 'UI/UX Designer', 'Backend Developer', 'Product Manager', 'DevOps Engineer', 'Data Analyst', 'Mobile Developer', 'QA Engineer'],
    //             data: [45, 32, 38, 25, 18, 28, 35, 22]
    //         },
            
    //         provinsiData: [
    //             {nama: 'DKI Jakarta', kandidat: 89, lat: -6.2088, lng: 106.8456},
    //             {nama: 'Jawa Barat', kandidat: 67, lat: -6.9175, lng: 107.6191},
    //             {nama: 'Jawa Timur', kandidat: 54, lat: -7.2504, lng: 112.7688},
    //             {nama: 'Jawa Tengah', kandidat: 43, lat: -7.1510, lng: 110.1403},
    //             {nama: 'Sumatera Utara', kandidat: 32, lat: 3.5952, lng: 98.6722},
    //             {nama: 'Sulawesi Selatan', kandidat: 28, lat: -5.1477, lng: 119.4327},
    //             {nama: 'Bali', kandidat: 25, lat: -8.4095, lng: 115.1889},
    //             {nama: 'Sumatera Selatan', kandidat: 19, lat: -3.3194, lng: 104.9147}
    //         ]
    //     };

    //     // Global variables
    //     let filteredCandidates = [...dummyData.candidates];
    //     let candidateTable;
        
    //     // Initialize Dashboard
    //     document.addEventListener('DOMContentLoaded', function() {
    //         initializeCharts();
    //         initializeMap();
    //         initializeFilters();
    //         populateTable();
    //         populateTopProvinces();
    //         updateStatistics();
    //     });

    //     // Initialize filter dropdowns
    //     function initializeFilters() {
    //         // Populate position filter
    //         const positions = [...new Set(dummyData.candidates.map(c => c.posisi))];
    //         const positionSelect = document.getElementById('filterPosition');
    //         if (positionSelect) {
    //             positions.forEach(position => {
    //                 const option = document.createElement('option');
    //                 option.value = position;
    //                 option.textContent = position;
    //                 positionSelect.appendChild(option);
    //             });
    //         }

    //         // Populate location filter
    //         const locations = [...new Set(dummyData.candidates.map(c => c.lokasi))];
    //         const locationSelect = document.getElementById('filterLocation');
    //         if (locationSelect) {
    //             locations.forEach(location => {
    //                 const option = document.createElement('option');
    //                 option.value = location;
    //                 option.textContent = location;
    //                 locationSelect.appendChild(option);
    //             });
    //         }

    //         // Add event listeners for real-time filtering
    //         const searchName = document.getElementById('searchName');
    //         const filterPosition = document.getElementById('filterPosition');
    //         const filterLocation = document.getElementById('filterLocation');
    //         const filterStatus = document.getElementById('filterStatus');
    //         const dateFrom = document.getElementById('dateFrom');
    //         const dateTo = document.getElementById('dateTo');
    //         const minScore = document.getElementById('minScore');
    //         const maxScore = document.getElementById('maxScore');

    //         if (searchName) searchName.addEventListener('input', applyFilters);
    //         if (filterPosition) filterPosition.addEventListener('change', applyFilters);
    //         if (filterLocation) filterLocation.addEventListener('change', applyFilters);
    //         if (filterStatus) filterStatus.addEventListener('change', applyFilters);
    //         if (dateFrom) dateFrom.addEventListener('change', applyFilters);
    //         if (dateTo) dateTo.addEventListener('change', applyFilters);
    //         if (minScore) minScore.addEventListener('input', applyFilters);
    //         if (maxScore) maxScore.addEventListener('input', applyFilters);
    //     }

    //     // Apply filters
    //     function applyFilters() {
    //         const searchName = document.getElementById('searchName')?.value.toLowerCase() || '';
    //         const filterPosition = document.getElementById('filterPosition')?.value || '';
    //         const filterLocation = document.getElementById('filterLocation')?.value || '';
    //         const filterStatus = document.getElementById('filterStatus')?.value || '';
    //         const dateFrom = document.getElementById('dateFrom')?.value || '';
    //         const dateTo = document.getElementById('dateTo')?.value || '';
    //         const minScore = parseFloat(document.getElementById('minScore')?.value || '0');
    //         const maxScore = parseFloat(document.getElementById('maxScore')?.value || '10');

    //         filteredCandidates = dummyData.candidates.filter(candidate => {
    //             // Name/Email filter
    //             if (searchName && 
    //                 !candidate.nama.toLowerCase().includes(searchName) && 
    //                 !candidate.email.toLowerCase().includes(searchName)) {
    //                 return false;
    //             }

    //             // Position filter
    //             if (filterPosition && candidate.posisi !== filterPosition) {
    //                 return false;
    //             }

    //             // Location filter
    //             if (filterLocation && candidate.lokasi !== filterLocation) {
    //                 return false;
    //             }

    //             // Status filter
    //             if (filterStatus && candidate.status !== filterStatus) {
    //                 return false;
    //             }

    //             // Date range filter
    //             const candidateDate = new Date(candidate.tanggal);
    //             if (dateFrom && candidateDate < new Date(dateFrom)) {
    //                 return false;
    //             }
    //             if (dateTo && candidateDate > new Date(dateTo)) {
    //                 return false;
    //             }

    //             // Score range filter
    //             if (candidate.score < minScore || candidate.score > maxScore) {
    //                 return false;
    //             }

    //             return true;
    //         });

    //         populateTable();
    //         updateStatistics();
    //     }

    //     // Clear all filters
    //     function clearAllFilters() {
    //         const elements = [
    //             'searchName', 'filterPosition', 'filterLocation', 'filterStatus',
    //             'dateFrom', 'dateTo', 'minScore', 'maxScore'
    //         ];

    //         elements.forEach(id => {
    //             const element = document.getElementById(id);
    //             if (element) element.value = '';
    //         });
            
    //         filteredCandidates = [...dummyData.candidates];
    //         populateTable();
    //         updateStatistics();
    //     }

    //     // Update statistics
    //     function updateStatistics() {
    //         const total = filteredCandidates.length;
    //         const pending = filteredCandidates.filter(c => c.status === 'pending').length;
    //         const approved = filteredCandidates.filter(c => c.status === 'approved').length;
    //         const rejected = filteredCandidates.filter(c => c.status === 'rejected').length;
    //         const saved = filteredCandidates.filter(c => c.status === 'save').length;
    //         const avgScore = total > 0 ? (filteredCandidates.reduce((sum, c) => sum + c.score, 0) / total).toFixed(1) : '0.0';

    //         // Update elements if they exist
    //         const totalElement = document.getElementById('totalCandidates') || document.getElementById('totalKandidat');
    //         const pendingElement = document.getElementById('pendingCandidates') || document.getElementById('menungguReview');
    //         const approvedElement = document.getElementById('approvedCandidates') || document.getElementById('diterima');
    //         const avgScoreElement = document.getElementById('avgScore');

    //         if (totalElement) totalElement.textContent = total;
    //         if (pendingElement) pendingElement.textContent = pending;
    //         if (approvedElement) approvedElement.textContent = approved;
    //         if (avgScoreElement) avgScoreElement.textContent = avgScore;
    //     }

    //     // Get score class for styling
    //     function getScoreClass(score) {
    //         if (score >= 9) return 'score-excellent';
    //         if (score >= 8) return 'score-good';
    //         if (score >= 7) return 'score-average';
    //         return 'score-poor';
    //     }

    //     // Updated Populate Table Function
    //     function populateTable() {
    //         const tbody = document.getElementById('candidateTableBody');
    //         if (!tbody) return;

    //         tbody.innerHTML = '';

    //         filteredCandidates.forEach(candidate => {
    //             const statusClass = {
    //                 'pending': 'badge-pending',
    //                 'approved': 'badge-approved',
    //                 'save': 'badge-save'
    //             }[candidate.status];

    //             const statusText = {
    //                 'pending': 'Proses',
    //                 'approved': 'Diterima',
    //                 'save': 'Disimpan'
    //             }[candidate.status];

    //             const scoreClass = getScoreClass(candidate.score);

    //             const row = `
    //                 <tr>
    //                     <td><strong>${candidate.nama}</strong></td>
    //                     <td>${candidate.email}</td>
    //                     <td><span class="badge bg-secondary">${candidate.posisi}</span></td>
    //                     <td><i class="fas fa-map-marker-alt me-1"></i>${candidate.lokasi}</td>
    //                     <td>${new Date(candidate.tanggal).toLocaleDateString('id-ID')}</td>
    //                     <td><span class="badge badge-status ${statusClass}">${statusText}</span></td>
    //                     <td><span class="${scoreClass}"><strong>${candidate.score}</strong></span></td>
    //                     <td>
    //                         <div class="btn-group">
    //                             <button class="btn btn-sm btn-outline-primary" onclick="viewCandidate(${candidate.id})" title="Lihat Detail">
    //                                 <i class="fas fa-eye"></i>
    //                             </button>
    //                             ${candidate.status === 'pending' ? `
    //                                 <button class="btn btn-sm btn-outline-success" onclick="approveCandidate(${candidate.id})" title="Terima">
    //                                     <i class="fas fa-check"></i>
    //                                 </button>
    //                                 <button class="btn btn-sm btn-outline-warning" onclick="saveCandidate(${candidate.id})" title="Simpan">
    //                                     <i class="fas fa-bookmark"></i>
    //                                 </button>
    //                             ` : ''}
    //                         </div>
    //                     </td>
    //                 </tr>
    //             `;
    //             tbody.innerHTML += row;
    //         });

    //         // Initialize/Reinitialize DataTable
    //         if (!candidateTable) {
    //             candidateTable = $('#candidateTable').DataTable({
    //                 responsive: true,
    //                 pageLength: 10,
    //                 searching: false,
    //                 language: {
    //                     url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json'
    //                 },
    //                 order: [[6, 'desc']]
    //             });
    //         } else {
    //             // Refresh DataTable tanpa destroy
    //             candidateTable.clear().draw();
    //         }

    //     }

    //     // Initialize Charts
    //     function initializeCharts() {
    //         // Lowongan Chart
    //         const ctxLowongan = document.getElementById('lowonganChart');
    //         if (ctxLowongan) {
    //             window.lowonganChart = new Chart(ctxLowongan.getContext('2d'), {
    //                 type: 'bar',
    //                 data: {
    //                     // labels: dummyData.lowonganData.labels,
    //                     datasets: [{
    //                         label: 'Jumlah Aplikasi',
    //                         // data: dummyData.lowonganData.data,
    //                         backgroundColor: 'rgba(102, 126, 234, 0.8)',
    //                         borderColor: 'rgba(102, 126, 234, 1)',
    //                         borderWidth: 2,
    //                         borderRadius: 8
    //                     }]
    //                 },
    //                 options: {
    //                     responsive: true,
    //                     maintainAspectRatio: false,
    //                     plugins: {
    //                         legend: {
    //                             display: false
    //                         }
    //                     },
    //                     scales: {
    //                         y: {
    //                             beginAtZero: true,
    //                             grid: {
    //                                 color: 'rgba(0,0,0,0.1)'
    //                             }
    //                         },
    //                         x: {
    //                             grid: {
    //                                 display: false
    //                             }
    //                         }
    //                     }
    //                 }
    //             });
    //         }

    //         // Status Chart
    //         const ctxStatus = document.getElementById('statusChart');
    //         if (ctxStatus) {
    //             new Chart(ctxStatus.getContext('2d'), {
    //                 type: 'doughnut',
    //                 data: {
    //                     labels: ['Proses', 'Diterima', 'Ditolak', 'Mungkin'],
    //                     datasets: [{
    //                         data: [0, 0, 0, 0],
    //                         backgroundColor: [
    //                             '#ffc107',
    //                             '#28a745',
    //                             '#dc3545',
    //                             '#6c757d'
    //                         ],
    //                         borderWidth: 0
    //                     }]
    //                 },
    //                 options: {
    //                     responsive: true,
    //                     maintainAspectRatio: false,
    //                     plugins: {
    //                         legend: {
    //                             position: 'bottom'
    //                         }
    //                     }
    //                 }
    //             });
    //         }
    //     }

    //     // Initialize Map
    //     function initializeMap() {
    //         const mapElement = document.getElementById('indonesiaMap');
    //         if (!mapElement) return;

    //         const map = L.map('indonesiaMap').setView([-2.5, 118], 5);
            
    //         L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //             attribution: '© OpenStreetMap contributors'
    //         }).addTo(map);

    //         // Add markers for provinces
    //         dummyData.provinsiData.forEach(provinsi => {
    //             const marker = L.circleMarker([provinsi.lat, provinsi.lng], {
    //                 radius: Math.max(provinsi.kandidat / 5, 5),
    //                 fillColor: '#667eea',
    //                 color: '#fff',
    //                 weight: 2,
    //                 opacity: 1,
    //                 fillOpacity: 0.8
    //             }).addTo(map);

    //             marker.bindPopup(`
    //                 <strong>${provinsi.nama}</strong><br>
    //                 Kandidat: ${provinsi.kandidat} orang
    //             `);
    //         });
    //     }

    //     // Populate Top Provinces
    //     function populateTopProvinces() {
    //         const container = document.getElementById('topProvinces');
    //         if (!container) return;

    //         const sortedProvinces = [...dummyData.provinsiData]
    //             .sort((a, b) => b.kandidat - a.kandidat)
    //             .slice(0, 5);

    //         container.innerHTML = sortedProvinces.map((provinsi, index) => `
    //             <div class="d-flex justify-content-between align-items-center mb-3">
    //                 <div class="d-flex align-items-center">
    //                     <div class="badge badge-primary rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
    //                         ${index + 1}
    //                     </div>
    //                     <div>
    //                         <h6 class="mb-0">${provinsi.nama}</h6>
    //                         <small class="text-muted">${provinsi.kandidat} kandidat</small>
    //                     </div>
    //                 </div>
    //                 <div class="progress" style="width: 60px; height: 8px;">
    //                     <div class="progress-bar" style="width: ${(provinsi.kandidat / dummyData.provinsiData[0].kandidat) * 100}%"></div>
    //                 </div>
    //             </div>
    //         `).join('');
    //     }

    //     // Chart Functions
    //     function changeChartView(type) {
    //         if (window.lowonganChart) {
    //             window.lowonganChart.config.type = type;
    //             window.lowonganChart.update();
                
    //             // Update button states
    //             document.querySelectorAll('.btn-group button').forEach(btn => {
    //                 btn.classList.remove('active');
    //             });
    //             event.target.classList.add('active');
    //         }
    //     }

    //     // Action Functions
    //     function viewCandidate(id) {
    //         const candidate = dummyData.candidates.find(c => c.id === id);
    //         if (candidate) {
    //             alert(`Melihat detail kandidat: ${candidate.nama}\nEmail: ${candidate.email}\nPosisi: ${candidate.posisi}\nScore: ${candidate.score}`);
    //         }
    //     }

    //     function approveCandidate(id) {
    //         const candidate = dummyData.candidates.find(c => c.id === id);
    //         if (candidate && confirm(`Apakah Anda yakin ingin menerima kandidat ${candidate.nama}?`)) {
    //             candidate.status = 'approved';
    //             applyFilters();
    //             showNotification(`Kandidat ${candidate.nama} berhasil disetujui!`, 'success');
    //         }
    //     }

    //     function saveCandidate(id) {
    //         const candidate = dummyData.candidates.find(c => c.id === id);
    //         if (candidate && confirm(`Apakah Anda yakin ingin menyimpan kandidat ${candidate.nama}?`)) {
    //             candidate.status = 'save';
    //             applyFilters();
    //             showNotification(`Kandidat ${candidate.nama} telah disimpan!`, 'info');
    //         }
    //     }

    //     function rejectCandidate(id) {
    //         const candidate = dummyData.candidates.find(c => c.id === id);
    //         if (candidate && confirm(`Apakah Anda yakin ingin menolak kandidat ${candidate.nama}?`)) {
    //             candidate.status = 'rejected';
    //             applyFilters();
    //             showNotification(`Kandidat ${candidate.nama} telah ditolak.`, 'warning');
    //         }
    //     }

    //     function refreshData() {
    //         // Simulate data refresh
    //         const refreshIcon = document.querySelector('.btn-outline-primary i');
    //         if (refreshIcon) {
    //             refreshIcon.classList.add('fa-spin');
    //             setTimeout(() => {
    //                 refreshIcon.classList.remove('fa-spin');
    //                 showNotification('Data berhasil diperbarui!', 'success');
    //             }, 1000);
    //         }
    //     }

    //     function exportData() {
    //         showNotification('Fitur export data akan segera tersedia!', 'info');
    //     }

    //     function addNewCandidate() {
    //         showNotification('Form tambah kandidat akan segera dibuka!', 'info');
    //     }

    //     function showProvinceView() {
    //         // Show detailed province modal or page
    //         const modalContent = `
    //             <div class="modal fade" id="provinceModal" tabindex="-1">
    //                 <div class="modal-dialog modal-lg">
    //                     <div class="modal-content">
    //                         <div class="modal-header">
    //                             <h5 class="modal-title">Detail Kandidat per Provinsi</h5>
    //                             <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    //                         </div>
    //                         <div class="modal-body">
    //                             <div class="table-responsive">
    //                                 <table class="table table-striped">
    //                                     <thead>
    //                                         <tr>
    //                                             <th>Provinsi</th>
    //                                             <th>Total Kandidat</th>
    //                                             <th>Diterima</th>
    //                                             <th>Proses</th>
    //                                             <th>Ditolak</th>
    //                                             <th>Tingkat Penerimaan</th>
    //                                         </tr>
    //                                     </thead>
    //                                     <tbody>
    //                                         ${dummyData.provinsiData.map(provinsi => {
    //                                             const accepted = Math.floor(provinsi.kandidat * 0.4);
    //                                             const pending = Math.floor(provinsi.kandidat * 0.3);
    //                                             const rejected = provinsi.kandidat - accepted - pending;
    //                                             const rate = ((accepted / provinsi.kandidat) * 100).toFixed(1);
                                                
    //                                             return `
    //                                             <tr>
    //                                                 <td><strong>${provinsi.nama}</strong></td>
    //                                                 <td>${provinsi.kandidat}</td>
    //                                                 <td><span class="text-success">${accepted}</span></td>
    //                                                 <td><span class="text-warning">${pending}</span></td>
    //                                                 <td><span class="text-danger">${rejected}</span></td>
    //                                                 <td><strong>${rate}%</strong></td>
    //                                             </tr>
    //                                             `;
    //                                         }).join('')}
    //                                     </tbody>
    //                                 </table>
    //                             </div>
    //                         </div>
    //                     </div>
    //                 </div>
    //             </div>
    //         `;
            
    //         document.body.insertAdjacentHTML('beforeend', modalContent);
    //         const modal = new bootstrap.Modal(document.getElementById('provinceModal'));
    //         modal.show();
            
    //         // Remove modal from DOM after hiding
    //         document.getElementById('provinceModal').addEventListener('hidden.bs.modal', function() {
    //             this.remove();
    //         });
    //     }

    //     // Search functionality
    //     function searchCandidates(query) {
    //         const searchNameInput = document.getElementById('searchName');
    //         if (searchNameInput) {
    //             searchNameInput.value = query;
    //             applyFilters();
    //         }
    //     }

    //     // Bulk actions
    //     function bulkAction(action) {
    //         const selectedCandidates = Array.from(document.querySelectorAll('input[name="candidateSelect"]:checked'))
    //             .map(cb => parseInt(cb.value));
            
    //         if (selectedCandidates.length === 0) {
    //             alert('Pilih kandidat terlebih dahulu!');
    //             return;
    //         }
            
    //         if (confirm(`${action} ${selectedCandidates.length} kandidat yang dipilih?`)) {
    //             selectedCandidates.forEach(id => {
    //                 const candidate = dummyData.candidates.find(c => c.id === id);
    //                 if (candidate) {
    //                     candidate.status = action.toLowerCase();
    //                 }
    //             });
                
    //             applyFilters();
    //             showNotification(`Berhasil melakukan ${action} pada ${selectedCandidates.length} kandidat!`, 'success');
    //         }
    //     }

    //     // Notification system
    //     function showNotification(message, type = 'info') {
    //         const notification = document.createElement('div');
    //         notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    //         notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    //         notification.innerHTML = `
    //             ${message}
    //             <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    //         `;
            
    //         document.body.appendChild(notification);
            
    //         // Auto remove after 5 seconds
    //         setTimeout(() => {
    //             if (notification.parentNode) {
    //                 notification.remove();
    //             }
    //         }, 5000);
    //     }

    //     // Mobile responsive adjustments
    //     function adjustForMobile() {
    //         if (window.innerWidth <= 768) {
    //             // Hide some columns on mobile
    //             const table = document.getElementById('candidateTable');
    //             if (table) {
    //                 const headers = table.querySelectorAll('th');
    //                 const cells = table.querySelectorAll('td');
                    
    //                 // Hide email and score columns on mobile
    //                 [1, 6].forEach(index => {
    //                     if (headers[index]) headers[index].style.display = 'none';
    //                 });
                    
    //                 cells.forEach((cell, i) => {
    //                     if ((i + 1) % 8 === 2 || (i + 1) % 8 === 7) { // Email and Score columns
    //                         cell.style.display = 'none';
    //                     }
    //                 });
    //             }
    //         }
    //     }

    //     // Auto-refresh data every 5 minutes
    //     setInterval(function() {
    //         updateStatistics();
    //     }, 300000);

    //     // Initialize tooltips
    //     document.addEventListener('DOMContentLoaded', function() {
    //         var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    //         var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    //             return new bootstrap.Tooltip(tooltipTriggerEl);
    //         });
    //     });

    //     // Performance monitoring
    //     function trackPerformance() {
    //         const performanceData = {
    //             loadTime: performance.now(),
    //             userAgent: navigator.userAgent,
    //             screenResolution: `${screen.width}x${screen.height}`,
    //             timestamp: new Date().toISOString()
    //         };
            
    //         console.log('Dashboard Performance:', performanceData);
    //     }

    //     // Event listeners
    //     window.addEventListener('load', trackPerformance);
    //     window.addEventListener('resize', adjustForMobile);
    //     window.addEventListener('load', adjustForMobile);
    // </script>
    
    
    <script>
        // Data kota dari database
        const kotaData = @json($kotaData);
        let citiesPieChart = null;
        
        // Initialize Map
        function initializeMap() {
            const mapElement = document.getElementById('indonesiaMap');
            if (!mapElement) return;
    
            const map = L.map('indonesiaMap').setView([-2.5, 118], 5);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
    
            // Add markers for cities dari database
            kotaData.forEach(kota => {
                const marker = L.circleMarker([kota.lat, kota.lng], {
                    radius: Math.max(kota.kandidat / 3, 8),
                    fillColor: '#667eea',
                    color: '#fff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(map);
    
                marker.bindPopup(`
                    <strong>${kota.nama}</strong><br>
                    Kandidat: ${kota.kandidat} orang<br>
                    <button class="btn btn-sm btn-primary mt-2" onclick="showCityDetail('${kota.nama}')">
                        Lihat Detail
                    </button>
                `);
            });
        }
        
        // Populate Top Cities dari database
        function populateTopCities() {
            const container = document.getElementById('topCities');
            if (!container) return;
    
            // Sort berdasarkan jumlah kandidat
            const sortedCities = [...kotaData]
                .sort((a, b) => b.kandidat - a.kandidat)
                .slice(0, 5);
    
            if (sortedCities.length === 0) {
                container.innerHTML = '<p class="text-muted text-center">Tidak ada data kandidat</p>';
                return;
            }
    
            const maxKandidat = sortedCities[0].kandidat;
    
            container.innerHTML = sortedCities.map((kota, index) => `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="badge badge-primary rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background: #667eea;">
                            ${index + 1}
                        </div>
                        <div>
                            <h6 class="mb-0">${kota.nama}</h6>
                            <small class="text-muted">${kota.kandidat} kandidat</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="progress me-2" style="width: 60px; height: 8px;">
                            <div class="progress-bar bg-primary" style="width: ${(kota.kandidat / maxKandidat) * 100}%; background: #667eea !important;"></div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" onclick="showCityDetail('${kota.nama}')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }
    
        // Function untuk menampilkan pie chart semua kota
        function showAllCitiesChart() {
            if (kotaData.length === 0) {
                alert('Tidak ada data untuk ditampilkan');
                return;
            }
    
            // Sort data berdasarkan jumlah kandidat
            const sortedData = [...kotaData].sort((a, b) => b.kandidat - a.kandidat);
            
            // Hitung total kandidat
            const totalKandidates = sortedData.reduce((sum, kota) => sum + kota.kandidat, 0);
            
            // Update summary statistics
            document.getElementById('totalCitiesCount').textContent = sortedData.length;
            document.getElementById('totalCandidatesCount').textContent = totalKandidates.toLocaleString('id-ID');
            
            // Generate colors for pie chart
            const colors = generateColors(sortedData.length);
            
            // Prepare chart data
            const chartData = {
                labels: sortedData.map(kota => kota.nama),
                datasets: [{
                    data: sortedData.map(kota => kota.kandidat),
                    backgroundColor: colors,
                    borderColor: '#fff',
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            };
    
            // Create pie chart
            const ctx = document.getElementById('citiesPieChart').getContext('2d');
            
            // Destroy existing chart if it exists
            if (citiesPieChart) {
                citiesPieChart.destroy();
            }
    
            citiesPieChart = new Chart(ctx, {
                type: 'pie',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed;
                                    const percentage = ((value / totalKandidates) * 100).toFixed(1);
                                    return `${context.label}: ${value} kandidat (${percentage}%)`;
                                }
                            }
                        }
                    },
                    onClick: (event, elements) => {
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const cityName = sortedData[index].nama;
                            showCityDetail(cityName);
                        }
                    }
                }
            });
    
            // Populate detailed table
            populateCitiesTable(sortedData, totalKandidates);
    
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('allCitiesModal'));
            modal.show();
        }
    
        // Function untuk generate warna untuk pie chart
        function generateColors(count) {
            const baseColors = [
                '#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b',
                '#fa709a', '#fee140', '#ff6b6b', '#4ecdc4', '#45b7d1',
                '#96ceb4', '#feca57', '#ff9ff3', '#54a0ff', '#5f27cd'
            ];
            
            const colors = [];
            for (let i = 0; i < count; i++) {
                if (i < baseColors.length) {
                    colors.push(baseColors[i]);
                } else {
                    // Generate random color for additional cities
                    colors.push(`hsl(${(i * 137.508) % 360}, 70%, 60%)`);
                }
            }
            return colors;
        }
    
        // Function untuk populate table detail
        function populateCitiesTable(data, total) {
            const tableBody = document.getElementById('citiesDetailTable');
            
            const tableHtml = data.map((kota, index) => {
                const percentage = ((kota.kandidat / total) * 100).toFixed(1);
                return `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="color-indicator me-2" style="width: 12px; height: 12px; background-color: ${generateColors(data.length)[index]}; border-radius: 50%;"></div>
                                ${kota.nama}
                            </div>
                        </td>
                        <td>
                            <strong>${kota.kandidat.toLocaleString('id-ID')}</strong>
                        </td>
                        <td>
                            <span class="badge bg-primary">${percentage}%</span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="showCityDetail('${kota.nama}')">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
            
            tableBody.innerHTML = tableHtml;
        }
    
        // Function untuk download data
        function downloadChartData() {
            const sortedData = [...kotaData].sort((a, b) => b.kandidat - a.kandidat);
            const totalKandidates = sortedData.reduce((sum, kota) => sum + kota.kandidat, 0);
            
            // Create CSV content
            let csvContent = "No,Nama Kota,Jumlah Kandidat,Persentase\n";
            sortedData.forEach((kota, index) => {
                const percentage = ((kota.kandidat / totalKandidates) * 100).toFixed(1);
                csvContent += `${index + 1},"${kota.nama}",${kota.kandidat},${percentage}%\n`;
            });
            
            // Create and download file
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", `distribusi_kandidat_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    
        // Function untuk menampilkan modal detail kota
        function showCityView() {
            // Generate list semua kota untuk dipilih
            const cityList = kotaData.map(kota => `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title">${kota.nama}</h6>
                            <p class="card-text text-muted">${kota.kandidat} kandidat</p>
                            <button class="btn btn-primary btn-sm" onclick="showCityDetail('${kota.nama}')">
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
    
            document.getElementById('cityDetailModalLabel').textContent = 'Pilih Kota';
            document.getElementById('cityDetailContent').innerHTML = `
                <div class="row">
                    ${cityList}
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('cityDetailModal'));
            modal.show();
        }
    
        // Function untuk menampilkan detail kota spesifik
        function showCityDetail(cityName) {
            document.getElementById('cityDetailModalLabel').textContent = `Detail Kota: ${cityName}`;
            document.getElementById('cityDetailContent').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>
            `;
        
            const modal = new bootstrap.Modal(document.getElementById('cityDetailModal'));
            modal.show();
        
            // Fetch data dari controller menggunakan route yang benar
            fetch(`{{ route('admin.kandidat.city-details') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    kota: cityName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                renderCityDetail(data);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('cityDetailContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Gagal memuat data: ${error.message}
                    </div>
                `;
            });
        }
    
        // Function untuk render detail kota
        function renderCityDetail(data) {
            const statusLabels = {
                'pending': 'Menunggu Verifikasi',
                'approved': 'Disetujui',
                'rejected': 'Ditolak',
                'interview': 'Interview',
                'hired': 'Diterima'
            };
    
            const statusBadges = {
                'pending': 'warning',
                'approved': 'success',
                'rejected': 'danger',
                'interview': 'info',
                'hired': 'primary'
            };
    
            // Generate statistik status
            const statusStatsHtml = Object.entries(data.status_stats || {}).map(([status, count]) => {
                const label = statusLabels[status] || status;
                const badge = statusBadges[status] || 'secondary';
                return `
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-${badge}">${count}</h3>
                                <p class="mb-0">${label}</p>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
    
            // Generate posisi populer
            const posisiPopulerHtml = data.posisi_populer?.map(posisi => `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${posisi.posisilamaran?.position_title || 'Posisi tidak tersedia'}
                    <span class="badge bg-primary rounded-pill">${posisi.total}</span>
                </li>
            `).join('') || '<li class="list-group-item">Tidak ada data posisi</li>';
    
            // Generate list kandidat
            const kandidatListHtml = data.kandidat_list?.map(kandidat => {
                const status = kandidat.status || 'pending';
                const badge = statusBadges[status] || 'secondary';
                const statusLabel = statusLabels[status] || status;
                
                return `
                    <tr>
                        <td>${kandidat.nama}</td>
                        <td>${kandidat.email}</td>
                        <td>${kandidat.no_telepon || '-'}</td>
                        <td>${kandidat.posisilamaran?.position_title || '-'}</td>
                        <td>
                            <span class="badge bg-${badge}">${statusLabel}</span>
                        </td>
                        <td>Rp ${kandidat.gaji_diharapkan ? new Intl.NumberFormat('id-ID').format(kandidat.gaji_diharapkan) : '-'}</td>
                        <td>${new Date(kandidat.created_at).toLocaleDateString('id-ID')}</td>
                    </tr>
                `;
            }).join('') || '<tr><td colspan="7" class="text-center">Tidak ada data kandidat</td></tr>';
    
            const content = `
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Statistik Kandidat</h6>
                        <div class="row">
                            ${statusStatsHtml}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Posisi Populer</h6>
                        <ul class="list-group">
                            ${posisiPopulerHtml}
                        </ul>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-money-bill-wave"></i> 
                                Rata-rata gaji diharapkan: Rp ${data.avg_gaji || 0}
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <h6>Daftar Kandidat (${data.total_kandidat} orang)</h6>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>No. Telepon</th>
                                        <th>Posisi Dilamar</th>
                                        <th>Status</th>
                                        <th>Gaji Diharapkan</th>
                                        <th>Tanggal Daftar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${kandidatListHtml}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
    
            document.getElementById('cityDetailContent').innerHTML = content;
        }
        
        // Initialize saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            initializeMap();
            populateTopCities();
        });
    </script>
    
    
    <!--Script untuk Toggle Sidebar-->
    <script>
        function toggleSidebar() {
            console.log('Toggle sidebar clicked');
            const sidebar = document.getElementById('sidebar');
            const navbar = document.getElementById('navbar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (sidebar && navbar && mainContent && toggleIcon) {
                sidebar.classList.toggle('minimized');
                navbar.classList.toggle('sidebar-minimized');
                mainContent.classList.toggle('sidebar-minimized');
                
                if (sidebar.classList.contains('minimized')) {
                    toggleIcon.className = 'fas fa-chevron-right';
                } else {
                    toggleIcon.className = 'fas fa-chevron-left';
                }
                console.log('Sidebar toggled successfully');
            } else {
                console.error('One or more elements not found');
            }
        }
    </script>
    
    <script>
        let employeeData = [];
        let currentEmployee = null;
        
        // Enhanced Format Rupiah Function
        function formatRupiah(amount) {
            if (!amount || amount === '-' || amount === '' || amount === null || amount === undefined) {
                return '<span class="text-muted">Tidak disebutkan</span>';
            }
            
            let number = amount.toString().replace(/[^\d.-]/g, '');
            
            if (isNaN(number) || number === '') {
                return '<span class="text-muted">Tidak valid</span>';
            }
            
            const formatted = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(parseInt(number));
            
            return `<strong style="color: #059669;">${formatted}</strong>`;
        }
        
        // Enhanced Format Date Function
        function formatDate(dateString) {
            if (!dateString) return '<span class="text-muted">Tidak disebutkan</span>';
            
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) {
                    return '<span class="text-muted">Format tanggal tidak valid</span>';
                }
                
                return date.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            } catch (error) {
                return '<span class="text-muted">Error parsing date</span>';
            }
        }
        
        // Enhanced Render Array Data Function
        function renderArrayData(data, title) {
            if (!data || (Array.isArray(data) && data.length === 0)) {
                return `
                    <div class="info-item full-width">
                        <div class="info-label">${title}</div>
                        <div class="info-value">
                            <span class="text-muted">Tidak ada data yang tersedia</span>
                        </div>
                    </div>
                `;
            }
            
            // Handle string data that might be JSON
            if (typeof data === 'string') {
                try {
                    data = JSON.parse(data);
                } catch (e) {
                    return `
                        <div class="info-item full-width">
                            <div class="info-label">${title}</div>
                            <div class="info-value">${data}</div>
                        </div>
                    `;
                }
            }
            
            // Handle non-array objects
            if (!Array.isArray(data)) {
                if (typeof data === 'object') {
                    let html = `
                        <div class="info-item full-width">
                            <div class="info-label">${title}</div>
                            <div class="array-data">
                                <div class="array-item">
                    `;
                    
                    Object.keys(data).forEach(key => {
                        html += `<div><strong>${key}:</strong> ${data[key] || '-'}</div>`;
                    });
                    
                    html += `</div></div></div>`;
                    return html;
                }
                
                return `
                    <div class="info-item full-width">
                        <div class="info-label">${title}</div>
                        <div class="info-value">${data}</div>
                    </div>
                `;
            }
            
            // Handle array data
            let html = `
                <div class="info-item full-width">
                    <div class="info-label">${title} <span class="badge bg-primary ms-2">${data.length} item</span></div>
                    <div class="array-data">
            `;
            
            data.forEach((item, index) => {
                html += `<div class="array-item">`;
                html += `<div style="font-weight: 600; color: #4f46e5; margin-bottom: 4px;">#${index + 1}</div>`;
                
                if (typeof item === 'object' && item !== null) {
                    Object.keys(item).forEach(key => {
                        const value = item[key];
                        if (value && value !== '') {
                            html += `<div><strong>${key}:</strong> ${value}</div>`;
                        }
                    });
                } else {
                    html += `<div>${item}</div>`;
                }
                html += `</div>`;
            });
            
            html += `</div></div>`;
            return html;
        }
        
        // Enhanced Show Employee Detail Function
        function showEmployeeDetail(employeeId) {
            // Show loading spinner
            const loadingSpinner = document.getElementById('loadingSpinner');
            const modalBody = document.getElementById('modalBody');
            
            if (loadingSpinner) {
                loadingSpinner.style.display = 'flex';
            }
            
            // Show modal immediately
            const modal = new bootstrap.Modal(document.getElementById('employeeModal'));
            modal.show();
            
            $.ajax({
                url: `/admin/employee/${employeeId}`,
                method: 'GET',
                timeout: 10000, // 10 second timeout
                success: function(employee) {
                    currentEmployee = employee;
                    
                    // Hide loading spinner
                    if (loadingSpinner) {
                        loadingSpinner.style.display = 'none';
                    }
                    
                    modalBody.innerHTML = `
                        <div class="employee-info-card">
                            <!-- Quick Summary Section -->
                            <div class="info-section" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <h6 class="section-title">Ringkasan Kandidat</h6>
                                </div>
                                <div class="info-grid">
                                    <div class="info-item" style="background: white; border: 2px solid #4f46e5;">
                                        <div class="info-label">Nama Lengkap</div>
                                        <div class="info-value" style="font-size: 1.2rem; font-weight: 700; color: #4f46e5;">
                                            ${employee.nama_lengkap || '-'}
                                        </div>
                                    </div>
                                    <div class="info-item" style="background: white;">
                                        <div class="info-label">Posisi Dilamar</div>
                                        <div class="info-value" style="font-weight: 600;">
                                            ${employee.posisi_dilamar || '-'}
                                        </div>
                                    </div>
                                    <div class="info-item salary-highlight">
                                        <div class="info-label">Gaji Diharapkan</div>
                                        <div class="info-value" style="color: white;><strong>${formatRupiah(employee.gaji_diharapkan)}</strong></div>
                                    </div>
                                    <div class="info-item" style="background: white;">
                                        <div class="info-label">Status Aplikasi</div>
                                        <div class="info-value">
                                            <span class="status-badge status-${employee.status}">
                                                ${employee.status === 'approved' ? 'Disetujui' : 
                                                  employee.status === 'rejected' ? 'Ditolak' : 
                                                  employee.status === 'maybe' ? 'Mungkin' : 'Menunggu Review'}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
        
                            <!-- Personal Information Section -->
                            <div class="info-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <h6 class="section-title">Data Personal</h6>
                                </div>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">Email</div>
                                        <div class="info-value">${employee.email || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">No. Telepon</div>
                                        <div class="info-value">${employee.no_telepon || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Tempat, Tanggal Lahir</div>
                                        <div class="info-value">${employee.tempat_lahir || '-'}, ${formatDate(employee.tanggal_lahir)}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Agama</div>
                                        <div class="info-value">${employee.agama || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Status Pernikahan</div>
                                        <div class="info-value">${employee.status_pernikahan || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Tinggi/Berat Badan</div>
                                        <div class="info-value">${employee.tinggi_badan || '-'} cm / ${employee.berat_badan || '-'} kg</div>
                                    </div>
                                    <div class="info-item full-width">
                                        <div class="info-label">Alamat KTP</div>
                                        <div class="info-value">${employee.alamat_ktp || '-'}</div>
                                    </div>
                                    <div class="info-item full-width">
                                        <div class="info-label">Alamat Tinggal</div>
                                        <div class="info-value">${employee.alamat_tinggal || '-'}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Job Information Section -->
                            <div class="info-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <h6 class="section-title">Informasi Karir</h6>
                                </div>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">Bidang Pekerjaan Diminati</div>
                                        <div class="info-value">${employee.bidang_pekerjaan_diminati || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Jabatan Diminati</div>
                                        <div class="info-value">${employee.jabatan_diminati || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Gaji Terakhir</div>
                                        <div class="info-value">${formatRupiah(employee.gaji_terakhir)}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Tunjangan Diharapkan</div>
                                        <div class="info-value">${formatRupiah(employee.tunjangan_diharapkan)}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Tunjangan Terakhir</div>
                                        <div class="info-value">${formatRupiah(employee.tunjangan_terakhir)}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Kapan Mulai Kerja</div>
                                        <div class="info-value">${employee.kapan_mulai_kerja || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Tanggal Mendaftar</div>
                                        <div class="info-value">${formatDate(employee.created_at)}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Education & Skills Section -->
                            <div class="info-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <h6 class="section-title">Pendidikan & Keahlian</h6>
                                </div>
                                <div class="info-grid">
                                    ${renderArrayData(employee.pendidikan_formal, 'Pendidikan Formal')}
                                    ${renderArrayData(employee.pendidikan_non_formal, 'Pendidikan Non-Formal')}
                                    <div class="info-item">
                                        <div class="info-label">Kemampuan Bahasa Inggris</div>
                                        <div class="info-value">${employee.bahasa_inggris || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Bahasa Asing Lain</div>
                                        <div class="info-value">${employee.bahasa_asing_lain || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Kemampuan Komputer</div>
                                        <div class="info-value">${employee.kemampuan_komputer || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Keterampilan Khusus</div>
                                        <div class="info-value">${employee.keterampilan_lain || '-'}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Work Experience Section -->
                            <div class="info-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="fas fa-history"></i>
                                    </div>
                                    <h6 class="section-title">Riwayat Pekerjaan</h6>
                                </div>
                                <div class="info-grid">
                                    ${renderArrayData(employee.pengalaman_kerja, 'Pengalaman Kerja')}
                                </div>
                            </div>
                            
                            <!-- Family Information Section -->
                            <div class="info-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <h6 class="section-title">Data Keluarga</h6>
                                </div>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">Nama Ayah</div>
                                        <div class="info-value">${employee.nama_ayah || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Pekerjaan Ayah</div>
                                        <div class="info-value">${employee.pekerjaan_ayah || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Nama Ibu</div>
                                        <div class="info-value">${employee.nama_ibu || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Pekerjaan Ibu</div>
                                        <div class="info-value">${employee.pekerjaan_ibu || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Nama Pasangan</div>
                                        <div class="info-value">${employee.nama_pasangan || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Jumlah Anak</div>
                                        <div class="info-value">${employee.jumlah_anak || '0'}</div>
                                    </div>
                                    ${renderArrayData(employee.data_saudara, 'Data Saudara')}
                                    ${renderArrayData(employee.data_anak, 'Data Anak')}
                                </div>
                            </div>
                            
                            <!-- Additional Information Section -->
                            <div class="info-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <h6 class="section-title">Informasi Tambahan</h6>
                                </div>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">Hobi</div>
                                        <div class="info-value">${employee.hobi || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Kegiatan Waktu Luang</div>
                                        <div class="info-value">${employee.kegiatan_waktu_luang || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Prestasi/Karya</div>
                                        <div class="info-value">${employee.prestasi_karya || '-'}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Riwayat Penyakit</div>
                                        <div class="info-value">${employee.riwayat_penyakit || '-'}</div>
                                    </div>
                                    ${renderArrayData(employee.referensi, 'Referensi')}
                                    ${renderArrayData(employee.kontak_darurat, 'Kontak Darurat')}
                                    <div class="info-item full-width">
                                        <div class="info-label">Catatan Tambahan</div>
                                        <div class="info-value">${employee.informasi_tambahan || 'Tidak ada catatan tambahan'}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Show/hide approve/reject buttons based on status
                    updateActionButtons(employee.status);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading employee detail:', error);
                    
                    // Hide loading spinner
                    if (loadingSpinner) {
                        loadingSpinner.style.display = 'none';
                    }
                    
                    modalBody.innerHTML = `
                        <div class="employee-info-card">
                            <div class="info-section text-center" style="padding: 40px;">
                                <div style="color: #dc2626; font-size: 3rem; margin-bottom: 16px;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <h5 style="color: #374151; margin-bottom: 8px;">Gagal Memuat Data</h5>
                                <p style="color: #6b7280;">Terjadi kesalahan saat memuat detail karyawan. Silakan coba lagi.</p>
                                <button type="button" class="btn btn-modern btn-approve" onclick="showEmployeeDetail(${employeeId})" style="margin-top: 16px;">
                                    <i class="fas fa-redo"></i>
                                    Coba Lagi
                                </button>
                            </div>
                        </div>
                    `;
                }
            });
        }
        
        // Function to update action buttons visibility
        function updateActionButtons(status) {
            const approveBtn = document.getElementById('approveBtn');
            const rejectBtn = document.getElementById('rejectBtn');
            const maybeBtn = document.getElementById('maybeBtn');
            
            if (status === 'pending') {
                approveBtn.style.display = 'inline-flex';
                rejectBtn.style.display = 'inline-flex';
                maybeBtn.style.display = 'inline-flex';
            } else {
                approveBtn.style.display = 'none';
                rejectBtn.style.display = 'none';
                maybeBtn.style.display = 'none';
            }
        }
        
        // Enhanced function to handle empty or null values
        function safeValue(value, fallback = 'Tidak disebutkan') {
            if (value === null || value === undefined || value === '' || value === '-') {
                return `<span class="text-muted">${fallback}</span>`;
            }
            return value;
        }
        
        // Function to highlight important information
        function highlightImportantInfo(employee) {
            const importantFields = [];
            
            // Check for missing important information
            if (!employee.email) importantFields.push('Email tidak tersedia');
            if (!employee.no_telepon) importantFields.push('Nomor telepon tidak tersedia');
            if (!employee.gaji_diharapkan) importantFields.push('Ekspektasi gaji tidak disebutkan');
            if (!employee.pengalaman_kerja || employee.pengalaman_kerja.length === 0) {
                importantFields.push('Tidak ada pengalaman kerja');
            }
            
            return importantFields;
        }
        
        // Function to calculate candidate score (for admin analysis)
        function calculateCandidateScore(employee) {
            let score = 0;
            let maxScore = 100;
            
            // Personal info completeness (20 points)
            const personalFields = ['nama_lengkap', 'email', 'no_telepon', 'alamat_ktp'];
            const completedPersonal = personalFields.filter(field => employee[field] && employee[field] !== '').length;
            score += (completedPersonal / personalFields.length) * 20;
            
            // Education (25 points)
            if (employee.pendidikan_formal && employee.pendidikan_formal.length > 0) score += 15;
            if (employee.pendidikan_non_formal && employee.pendidikan_non_formal.length > 0) score += 10;
            
            // Work experience (30 points)
            if (employee.pengalaman_kerja && employee.pengalaman_kerja.length > 0) {
                score += Math.min(employee.pengalaman_kerja.length * 10, 30);
            }
            
            // Skills (15 points)
            if (employee.kemampuan_komputer) score += 5;
            if (employee.bahasa_inggris && employee.bahasa_inggris !== 'Tidak bisa') score += 5;
            if (employee.keterampilan_lain) score += 5;
            
            // Salary expectation reasonableness (10 points)
            if (employee.gaji_diharapkan && employee.gaji_terakhir) {
                const expected = parseInt(employee.gaji_diharapkan.toString().replace(/[^\d]/g, ''));
                const last = parseInt(employee.gaji_terakhir.toString().replace(/[^\d]/g, ''));
                if (expected <= last * 1.5) score += 10; // Reasonable expectation
            }
            
            return Math.round(score);
        }
        
        // Download PDF Function
        function downloadPDF() {
            if (!currentEmployee) {
                alert('Tidak ada data karyawan yang dipilih');
                return;
            }
            
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // Set font
            doc.setFont('helvetica');
            
            // Helper function untuk menangani text wrapping
            function addWrappedText(doc, text, x, y, maxWidth, lineHeight = 5) {
                if (!text || text === '-' || text === '') {
                    doc.text('-', x, y);
                    return y + lineHeight;
                }
                
                const lines = doc.splitTextToSize(text.toString(), maxWidth);
                lines.forEach(line => {
                    if (y > 280) {
                        doc.addPage();
                        y = 20;
                    }
                    doc.text(line, x, y);
                    y += lineHeight;
                });
                return y;
            }
            
            // Helper function untuk render array data
            function renderArrayDataPDF(data, title, startY) {
                let yPos = startY;
                
                if (yPos > 250) {
                    doc.addPage();
                    yPos = 20;
                }
                
                doc.setFontSize(12);
                doc.setTextColor(102, 126, 234);
                doc.text(title.toUpperCase(), 20, yPos);
                yPos += 8;
                
                doc.setFontSize(9);
                doc.setTextColor(51, 51, 51);
                
                if (!data || (Array.isArray(data) && data.length === 0)) {
                    doc.text('• Tidak ada data', 25, yPos);
                    return yPos + 8;
                }
                
                // Parse jika data berupa string JSON
                if (typeof data === 'string') {
                    try {
                        data = JSON.parse(data);
                    } catch (e) {
                        yPos = addWrappedText(doc, `• ${data}`, 25, yPos, 165);
                        return yPos + 3;
                    }
                }
                
                if (!Array.isArray(data)) {
                    yPos = addWrappedText(doc, `• ${JSON.stringify(data)}`, 25, yPos, 165);
                    return yPos + 3;
                }
                
                data.forEach((item, index) => {
                    if (yPos > 275) {
                        doc.addPage();
                        yPos = 20;
                    }
                    
                    if (typeof item === 'object' && item !== null) {
                        doc.text(`${index + 1}.`, 25, yPos);
                        yPos += 5;
                        
                        Object.keys(item).forEach(key => {
                            if (yPos > 275) {
                                doc.addPage();
                                yPos = 20;
                            }
                            
                            const value = item[key] || '-';
                            const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            yPos = addWrappedText(doc, `   ${label}: ${value}`, 30, yPos, 160);
                        });
                        yPos += 3;
                    } else {
                        yPos = addWrappedText(doc, `${index + 1}. ${item}`, 25, yPos, 165);
                    }
                });
                
                return yPos + 5;
            }
            
            // Title
            doc.setFontSize(18);
            doc.setTextColor(51, 51, 51);
            doc.text('DETAIL KARYAWAN', 105, 20, { align: 'center' });
            
            // Line under title
            doc.setLineWidth(0.5);
            doc.line(20, 25, 190, 25);
            
            let yPosition = 35;
            
            // Personal Information
            doc.setFontSize(14);
            doc.setTextColor(102, 126, 234);
            doc.text('INFORMASI PERSONAL', 20, yPosition);
            yPosition += 10;
            
            doc.setFontSize(10);
            doc.setTextColor(51, 51, 51);
            
            const personalInfo = [
                `Nama Lengkap: ${currentEmployee.nama_lengkap || '-'}`,
                `Email: ${currentEmployee.email || '-'}`,
                `No. Telepon: ${currentEmployee.no_telepon || '-'}`,
                `Tempat, Tanggal Lahir: ${currentEmployee.tempat_lahir || '-'}, ${formatDate(currentEmployee.tanggal_lahir)}`,
                `Alamat KTP: ${currentEmployee.alamat_ktp || '-'}`,
                `Alamat Tinggal: ${currentEmployee.alamat_tinggal || '-'}`,
                `Agama: ${currentEmployee.agama || '-'}`,
                `Status Pernikahan: ${currentEmployee.status_pernikahan || '-'}`,
                `Tinggi/Berat Badan: ${currentEmployee.tinggi_badan || '-'} cm / ${currentEmployee.berat_badan || '-'} kg`,
                `Golongan Darah: ${currentEmployee.golongan_darah || '-'}`
            ];
            
            personalInfo.forEach(info => {
                if (yPosition > 280) {
                    doc.addPage();
                    yPosition = 20;
                }
                yPosition = addWrappedText(doc, info, 20, yPosition, 170);
            });
            
            yPosition += 5;
            
            // Job Information
            if (yPosition > 250) {
                doc.addPage();
                yPosition = 20;
            }
            
            doc.setFontSize(14);
            doc.setTextColor(102, 126, 234);
            doc.text('INFORMASI PEKERJAAN', 20, yPosition);
            yPosition += 10;
            
            doc.setFontSize(10);
            doc.setTextColor(51, 51, 51);
            
            const jobInfo = [
                `Posisi Dilamar: ${currentEmployee.posisi_dilamar || '-'}`,
                `Bidang Pekerjaan Diminati: ${currentEmployee.bidang_pekerjaan_diminati || '-'}`,
                `Jabatan Diminati: ${currentEmployee.jabatan_diminati || '-'}`,
                `Gaji Diharapkan: ${formatRupiah(currentEmployee.gaji_diharapkan)}`,
                `Gaji Terakhir: ${formatRupiah(currentEmployee.gaji_terakhir)}`,
                `Tunjangan Diharapkan: ${formatRupiah(currentEmployee.tunjangan_diharapkan)}`,
                `Tunjangan Terakhir: ${formatRupiah(currentEmployee.tunjangan_terakhir)}`,
                `Kapan Mulai Kerja: ${currentEmployee.kapan_mulai_kerja || '-'}`,
                `Status: ${currentEmployee.status === 'approved' ? 'Disetujui' : 
                          currentEmployee.status === 'rejected' ? 'Ditolak' : 'Menunggu'}`,
                `Tanggal Daftar: ${formatDate(currentEmployee.created_at)}`
            ];
            
            jobInfo.forEach(info => {
                if (yPosition > 280) {
                    doc.addPage();
                    yPosition = 20;
                }
                yPosition = addWrappedText(doc, info, 20, yPosition, 170);
            });
            
            yPosition += 10;
            
            // Education & Skills - DENGAN ARRAY DATA LENGKAP
            yPosition = renderArrayDataPDF(currentEmployee.pendidikan_formal, 'Pendidikan Formal', yPosition);
            yPosition = renderArrayDataPDF(currentEmployee.pendidikan_non_formal, 'Pendidikan Non-Formal', yPosition);
            
            if (yPosition > 250) {
                doc.addPage();
                yPosition = 20;
            }
            
            doc.setFontSize(12);
            doc.setTextColor(102, 126, 234);
            doc.text('KETERAMPILAN', 20, yPosition);
            yPosition += 8;
            
            doc.setFontSize(10);
            doc.setTextColor(51, 51, 51);
            
            const skillsInfo = [
                `Bahasa Inggris: ${currentEmployee.bahasa_inggris || '-'}`,
                `Bahasa Asing Lain: ${currentEmployee.bahasa_asing_lain || '-'}`,
                `Kemampuan Komputer: ${currentEmployee.kemampuan_komputer || '-'}`,
                `Keterampilan Lain: ${currentEmployee.keterampilan_lain || '-'}`
            ];
            
            skillsInfo.forEach(info => {
                if (yPosition > 280) {
                    doc.addPage();
                    yPosition = 20;
                }
                yPosition = addWrappedText(doc, info, 20, yPosition, 170);
            });
            
            yPosition += 10;
            
            // PENGALAMAN KERJA - SECTION YANG PALING PENTING
            yPosition = renderArrayDataPDF(currentEmployee.pengalaman_kerja, 'Pengalaman Kerja / Riwayat Pekerjaan', yPosition);
            
            // AKTIVITAS SOSIAL
            yPosition = renderArrayDataPDF(currentEmployee.aktivitas_sosial, 'Aktivitas Sosial', yPosition);
            
            // Family Information
            if (yPosition > 230) {
                doc.addPage();
                yPosition = 20;
            }
            
            doc.setFontSize(14);
            doc.setTextColor(102, 126, 234);
            doc.text('INFORMASI KELUARGA', 20, yPosition);
            yPosition += 10;
            
            doc.setFontSize(10);
            doc.setTextColor(51, 51, 51);
            
            const familyInfo = [
                `Nama Ayah: ${currentEmployee.nama_ayah || '-'}`,
                `Pekerjaan Ayah: ${currentEmployee.pekerjaan_ayah || '-'}`,
                `Pendidikan Ayah: ${currentEmployee.pendidikan_ayah || '-'}`,
                `Tanggal Lahir Ayah: ${formatDate(currentEmployee.tanggal_lahir_ayah)}`,
                `Nama Ibu: ${currentEmployee.nama_ibu || '-'}`,
                `Pekerjaan Ibu: ${currentEmployee.pekerjaan_ibu || '-'}`,
                `Pendidikan Ibu: ${currentEmployee.pendidikan_ibu || '-'}`,
                `Tanggal Lahir Ibu: ${formatDate(currentEmployee.tanggal_lahir_ibu)}`,
                `Nama Pasangan: ${currentEmployee.nama_pasangan || '-'}`,
                `Jumlah Anak: ${currentEmployee.jumlah_anak || '-'}`
            ];
            
            familyInfo.forEach(info => {
                if (yPosition > 280) {
                    doc.addPage();
                    yPosition = 20;
                }
                yPosition = addWrappedText(doc, info, 20, yPosition, 170);
            });
            
            yPosition += 5;
            
            // Data Saudara dan Anak
            yPosition = renderArrayDataPDF(currentEmployee.data_saudara, 'Data Saudara', yPosition);
            yPosition = renderArrayDataPDF(currentEmployee.data_anak, 'Data Anak', yPosition);
            
            // REFERENSI DAN KONTAK DARURAT
            yPosition = renderArrayDataPDF(currentEmployee.referensi, 'Referensi', yPosition);
            yPosition = renderArrayDataPDF(currentEmployee.kontak_darurat, 'Kontak Darurat', yPosition);
            
            // Additional Information
            if (yPosition > 230) {
                doc.addPage();
                yPosition = 20;
            }
            
            doc.setFontSize(14);
            doc.setTextColor(102, 126, 234);
            doc.text('INFORMASI TAMBAHAN', 20, yPosition);
            yPosition += 10;
            
            doc.setFontSize(10);
            doc.setTextColor(51, 51, 51);
            
            const additionalInfo = [
                `Hobi: ${currentEmployee.hobi || '-'}`,
                `Kegiatan Waktu Luang: ${currentEmployee.kegiatan_waktu_luang || '-'}`,
                `Prestasi/Karya: ${currentEmployee.prestasi_karya || '-'}`,
                `Riwayat Penyakit: ${currentEmployee.riwayat_penyakit || '-'}`,
                `Fasilitas Terakhir: ${currentEmployee.fasilitas_terakhir || '-'}`,
                `Fasilitas Lain: ${currentEmployee.fasilitas_lain || '-'}`,
                `BPJS Ketenagakerjaan: ${currentEmployee.bpjs_tk || '-'}`,
                `BPJS Kesehatan: ${currentEmployee.bpjs_kesehatan || '-'}`,
                `NPWP: ${currentEmployee.npwp || '-'}`
            ];
            
            additionalInfo.forEach(info => {
                if (yPosition > 280) {
                    doc.addPage();
                    yPosition = 20;
                }
                yPosition = addWrappedText(doc, info, 20, yPosition, 170);
            });
            
            // SIM Data (Array)
            yPosition = renderArrayDataPDF(currentEmployee.sim, 'Data SIM', yPosition);
            
            // Kesediaan dan Preferensi Kerja
            if (yPosition > 200) {
                doc.addPage();
                yPosition = 20;
            }
            
            doc.setFontSize(12);
            doc.setTextColor(102, 126, 234);
            doc.text('KESEDIAAN & PREFERENSI KERJA', 20, yPosition);
            yPosition += 8;
            
            doc.setFontSize(10);
            doc.setTextColor(51, 51, 51);
            
            const workPreferences = [
                `Fasilitas Diharapkan: ${currentEmployee.fasilitas_diharapkan || '-'}`,
                `Jaminan Diharapkan: ${currentEmployee.jaminan_diharapkan || '-'}`,
                `Lain-lain Diharapkan: ${currentEmployee.lain_diharapkan || '-'}`,
                `Kesediaan Medical Checkup: ${currentEmployee.kesediaan_medical_checkup || '-'}`,
                `Kesediaan Tes Psikologi: ${currentEmployee.kesediaan_psikologi || '-'}`,
                `Kesediaan Masa Percobaan: ${currentEmployee.kesediaan_masa_percobaan || '-'}`,
                `Kesediaan Perjalanan Dinas: ${currentEmployee.kesediaan_perjalanan_dinas || '-'}`,
                `Maksimum Hari Dinas: ${currentEmployee.maksimum_hari_dinas || '-'} hari`,
                `Kesediaan Pindah Kota: ${currentEmployee.kesediaan_pindah_kota || '-'}`
            ];
            
            workPreferences.forEach(info => {
                if (yPosition > 280) {
                    doc.addPage();
                    yPosition = 20;
                }
                yPosition = addWrappedText(doc, info, 20, yPosition, 170);
            });
            
            // Kesediaan Penempatan (Array)
            yPosition = renderArrayDataPDF(currentEmployee.kesediaan_penempatan, 'Kesediaan Penempatan', yPosition);
            
            // Media Sosial
            if (yPosition > 230) {
                doc.addPage();
                yPosition = 20;
            }
            
            doc.setFontSize(12);
            doc.setTextColor(102, 126, 234);
            doc.text('MEDIA SOSIAL', 20, yPosition);
            yPosition += 8;
            
            doc.setFontSize(10);
            doc.setTextColor(51, 51, 51);
            
            const socialMedia = [
                `Facebook: ${currentEmployee.facebook || '-'}`,
                `Twitter: ${currentEmployee.twitter || '-'}`,
                `LinkedIn: ${currentEmployee.linkedin || '-'}`,
                `Instagram: ${currentEmployee.instagram || '-'}`,
                `TikTok: ${currentEmployee.tiktok || '-'}`,
                `Media Sosial Lain: ${currentEmployee.medsos_lain || '-'}`
            ];
            
            socialMedia.forEach(info => {
                if (yPosition > 280) {
                    doc.addPage();
                    yPosition = 20;
                }
                yPosition = addWrappedText(doc, info, 20, yPosition, 170);
            });
            
            // Informasi Tambahan dengan text wrapping
            if (currentEmployee.informasi_tambahan && currentEmployee.informasi_tambahan !== '-') {
                yPosition += 5;
                if (yPosition > 250) {
                    doc.addPage();
                    yPosition = 20;
                }
                
                doc.setFontSize(12);
                doc.setTextColor(102, 126, 234);
                doc.text('CATATAN TAMBAHAN', 20, yPosition);
                yPosition += 8;
                
                doc.setFontSize(10);
                doc.setTextColor(51, 51, 51);
                yPosition = addWrappedText(doc, currentEmployee.informasi_tambahan, 20, yPosition, 170);
            }
            
            // Footer pada setiap halaman
            const pageCount = doc.internal.getNumberOfPages();
            for (let i = 1; i <= pageCount; i++) {
                doc.setPage(i);
                doc.setFontSize(8);
                doc.setTextColor(128, 128, 128);
                doc.text(`Halaman ${i} dari ${pageCount}`, 105, 290, { align: 'center' });
                doc.text(`Dicetak pada: ${new Date().toLocaleDateString('id-ID')}`, 190, 290, { align: 'right' });
                doc.text(`PT. Gondowangi Tradisional Kosmetika`, 20, 290);
            }
            
            // Save the PDF
            const fileName = `FDP_${currentEmployee.nama_lengkap || 'Unknown'}_${currentEmployee.jabatan_diminati || '-'}.pdf`;
            doc.save(fileName);
        }
        
        // CSRF Token untuk Ajax Requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
    
        // Show Dashboard
        function showDashboard() {
            document.getElementById('dashboard-section').style.display = 'block';
            document.getElementById('datatable-section').style.display = 'none';
        }
    
        // Show DataTable
        function showDataTable() {
            document.getElementById('dashboard-section').style.display = 'none';
            document.getElementById('datatable-section').style.display = 'block';
            loadEmployeeTable();
        }
    
        // Load Employee Table from Database
        function loadEmployeeTable() {
            // Show loading
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '<tr><td colspan="8" class="text-center">Loading...</td></tr>';
    
            $.ajax({
                url: '/admin/employee-data',
                method: 'GET',
                success: function(response) {
                    employeeData = response;
                    renderEmployeeTable(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading employee data:', error);
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>';
                }
            });
        }
    
        // Render Employee Table
        function renderEmployeeTable(data) {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';
        
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center">Tidak ada data karyawan</td></tr>';
                return;
            }
        
            data.forEach((employee, index) => {
                const statusClass = employee.status === 'approved' ? 'badge-approved' :  employee.status === 'rejected' ? 'badge-rejected' : 'badge-pending';
                const statusText = employee.status === 'approved' ? 'Approved' :  employee.status === 'rejected' ? 'Rejected' : 'Pending';
                const experienceCategory = getExperienceCategory(employee.pengalaman_kerja); // Ambil kategori pengalaman
        
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${employee.nama_lengkap || '-'}</td>
                        <td>${employee.email || '-'}</td>
                        <td>${employee.no_telepon || '-'}</td>
                        <td>${employee.posisi_dilamar || '-'}</td>
                        <td>${experienceCategory}</td>  <!-- Tampilkan kategori pengalaman -->
                        <td>${new Date(employee.created_at).toLocaleDateString('id-ID')}</td>
                        <td><span class="badge badge-status ${statusClass}">${statusText}</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1" onclick="showEmployeeDetail('${employee.id}')">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${employee.status === 'pending' ? `
                                <button class="btn btn-sm btn-success me-1" onclick="updateStatus('${employee.id}', 'approved')">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="updateStatus('${employee.id}', 'rejected')">
                                    <i class="fas fa-times"></i>
                                </button>
                            ` : ''}
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        
            // Initialize/Reinitialize DataTable
            if ($.fn.DataTable.isDataTable('#employeeTable')) {
                $('#employeeTable').DataTable().destroy();
            }
            $('#employeeTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json'
                }
            });
        }
        
        // Update Status via AJAX
        function updateStatus(employeeId, status) {
            if (confirm(`Apakah Anda yakin ingin mengubah status menjadi ${status}?`)) {
                $.ajax({
                    url: `/admin/employee/${employeeId}/status`,
                    method: 'PUT',
                    data: {
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            loadEmployeeTable(); // Reload table
                            location.reload(); // Simple reload to update stats
                        } else {
                            alert(`Gagal mengubah status: ${response.message || 'Tidak ada pesan dari server.'}`);
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.error('Error updating status:', {
                            statusCode: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            textStatus: textStatus,
                            errorThrown: errorThrown
                        });
        
                        let errorMessage = 
                            `Terjadi kesalahan saat mengubah status.\n\n` +
                            `Kode Status: ${xhr.status} (${xhr.statusText})\n` +
                            `Jenis Error: ${textStatus}\n` +
                            `Pesan Error: ${errorThrown}\n\n`;
        
                        // Jika server mengirimkan JSON error, coba tampilkan
                        try {
                            let jsonResponse = JSON.parse(xhr.responseText);
                            if (jsonResponse.message) {
                                errorMessage += `Pesan Server: ${jsonResponse.message}\n`;
                            }
                        } catch (e) {
                            // Jika bukan JSON, tampilkan raw response
                            errorMessage += `Response Server: ${xhr.responseText || '-'}\n`;
                        }
        
                        alert(errorMessage);
                    }
                });
            }
        }

    
        // Approve Employee from Modal
        function approveEmployee() {
            if (currentEmployee) {
                updateStatus(currentEmployee.id, 'approved');
                bootstrap.Modal.getInstance(document.getElementById('employeeModal')).hide();
            }
        }
        
        // Maybe Employee from Modal
        function maybeEmployee() {
            if (currentEmployee) {
                updateStatus(currentEmployee.id, 'save');
                bootstrap.Modal.getInstance(document.getElementById('employeeModal')).hide();
            }
        }
    
        // Reject Employee from Modal
        function rejectEmployee() {
            if (currentEmployee) {
                updateStatus(currentEmployee.id, 'rejected');
                bootstrap.Modal.getInstance(document.getElementById('employeeModal')).hide();
            }
        }
    
        // Apply Filters
        function applyFilters() {
            const status = document.getElementById('statusFilter').value;
            const experience = document.getElementById('experienceFilter').value;
            const date = document.getElementById('dateFilter').value;
            const search = document.getElementById('searchFilter').value;
        
            const params = new URLSearchParams();
            if (status) params.append('status', status);
            if (experience) params.append('experience', experience);
            if (date) params.append('date', date);
            if (search) params.append('search', search);
        
            $.ajax({
                url: `/admin/employee-data?${params.toString()}`,
                method: 'GET',
                success: function(response) {
                    renderEmployeeTable(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error applying filters:', error);
                    alert('Error applying filters');
                }
            });
        }

    
        // View Employee (from recent activities)
        function viewEmployee(employeeId) {
            showEmployeeDetail(employeeId);
        }
    
        // Approve Employee from List (from recent activities)
        function approveEmployeeFromList(employeeId) {
            updateStatus(employeeId, 'approved');
        }
    
        // Sidebar Navigation
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        // Toggle Sidebar Function
        function toggleSidebar() {
            console.log('Toggle sidebar clicked');
            const sidebar = document.getElementById('sidebar');
            const navbar = document.getElementById('navbar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (sidebar && navbar && mainContent && toggleIcon) {
                sidebar.classList.toggle('minimized');
                navbar.classList.toggle('sidebar-minimized');
                mainContent.classList.toggle('sidebar-minimized');
                
                if (sidebar.classList.contains('minimized')) {
                    toggleIcon.className = 'fas fa-chevron-right';
                } else {
                    toggleIcon.className = 'fas fa-chevron-left';
                }
                console.log('Sidebar toggled successfully');
            } else {
                console.error('One or more elements not found');
            }
        }
    
        // Alternative event listener setup
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleSidebar();
                });
                console.log('Toggle button event listener added');
            } else {
                console.error('Toggle button not found');
            }
        });
    
        // Mobile Sidebar Toggle (if needed)
        document.addEventListener('DOMContentLoaded', function() {
            const mobileToggle = document.getElementById('mobileSidebarToggle');
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    const sidebar = document.getElementById('sidebar');
                    if (window.innerWidth <= 768) {
                        sidebar.style.marginLeft = sidebar.style.marginLeft === '0px' ? '-250px' : '0px';
                    }
                });
            }
        });
    
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof initCharts === 'function') {
                initCharts();
            }
        });
    </script>
    
    <script>
        // Function to calculate experience summary
        function calculateExperience(pengalamanKerja) {
            if (!pengalamanKerja || pengalamanKerja.length === 0) {
                return 'Fresh Graduate';
            }
            
            let totalYears = 0;
            let totalMonths = 0;
            
            pengalamanKerja.forEach(exp => {
                const startDate = new Date(exp.tanggal_mulai || exp.dari || '');
                const endDate = exp.tanggal_selesai || exp.sampai ? new Date(exp.tanggal_selesai || exp.sampai) : new Date();
                
                if (startDate && !isNaN(startDate.getTime())) {
                    const diffTime = Math.abs(endDate - startDate);
                    const diffMonths = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 30));
                    totalMonths += diffMonths;
                }
            });
            
            totalYears = Math.floor(totalMonths / 12);
            const remainingMonths = totalMonths % 12;
            
            if (totalYears === 0 && remainingMonths === 0) {
                return 'Fresh Graduate';
            } else if (totalYears === 0) {
                return `${remainingMonths} bulan`;
            } else if (remainingMonths === 0) {
                return `${totalYears} tahun`;
            } else {
                return `${totalYears} tahun ${remainingMonths} bulan`;
            }
        }
        
        // Function to get experience category for filtering
        function getExperienceCategory(pengalamanKerja) {
            if (!pengalamanKerja || pengalamanKerja.length === 0) {
                return 'fresh_graduate';
            }
            
            let totalMonths = 0;
            
            pengalamanKerja.forEach(exp => {
                const startDate = new Date(exp.tanggal_mulai || exp.dari || '');
                const endDate = exp.tanggal_selesai || exp.sampai ? new Date(exp.tanggal_selesai || exp.sampai) : new Date();
                
                if (startDate && !isNaN(startDate.getTime())) {
                    const diffTime = Math.abs(endDate - startDate);
                    const diffMonths = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 30));
                    totalMonths += diffMonths;
                }
            });
            
            const totalYears = totalMonths / 12;
            
            if (totalYears === 0) return 'fresh_graduate';
            if (totalYears <= 1) return '0-1';
            if (totalYears <= 3) return '1-3';
            if (totalYears <= 5) return '3-5';
            return '5+';
        }
        
        // Updated Render Employee Table
        function renderEmployeeTable(data) {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center">Tidak ada data karyawan</td></tr>';
                return;
            }
        
            data.forEach((employee, index) => {
                const statusClass = employee.status === 'approved' ? 'badge-approved' :  employee.status === 'rejected' ? 'badge-rejected' : employee.status === 'save' ? 'badge-save' : 'badge-pending';
                const statusText = employee.status === 'approved' ? 'Diterima' :  employee.status === 'rejected' ? 'Ditolak' :  employee.status === 'save' ? 'Disimpan' : 'Proses';
                
                // Calculate experience
                const experience = calculateExperience(employee.pengalaman_kerja);
                
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${employee.nama_lengkap || '-'}</td>
                        <td>${employee.email || '-'}</td>
                        <td>${employee.no_telepon || '-'}</td>
                        <td>${employee.posisi_dilamar || '-'}</td>
                        <td><span class="badge bg-info text-dark">${experience}</span></td>
                        <td>${new Date(employee.created_at).toLocaleDateString('id-ID')}</td>
                        <td><span class="badge badge-status ${statusClass}">${statusText}</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1" onclick="showEmployeeDetail('${employee.id}')">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${employee.status === 'pending' ? `
                                <button class="btn btn-sm btn-success me-1" onclick="updateStatus('${employee.id}', 'approved')">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="updateStatus('${employee.id}', 'rejected')">
                                    <i class="fas fa-times"></i>
                                </button>
                             ` : ''}
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        
            // Initialize/Reinitialize DataTable
            if ($.fn.DataTable.isDataTable('#employeeTable')) {
                $('#employeeTable').DataTable().destroy();
            }
            $('#employeeTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json'
                }
            });
        }
        
        // Updated Apply Filters
        function applyFilters() {
            const status = document.getElementById('statusFilter').value;
            const date = document.getElementById('dateFilter').value;
            const search = document.getElementById('searchFilter').value;
            const experience = document.getElementById('experienceFilter').value;  // Ambil nilai filter pengalaman
        
            const params = new URLSearchParams();
            if (status) params.append('status', status);
            if (date) params.append('date', date);
            if (search) params.append('search', search);
            if (experience) params.append('experience', experience);  // Kirimkan parameter pengalaman
        
            $.ajax({
                url: `/admin/employee-data?${params.toString()}`,
                method: 'GET',
                success: function(response) {
                    renderEmployeeTable(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error applying filters:', error);
                    alert('Error applying filters');
                }
            });
        }

    </script>

</body>
</html>