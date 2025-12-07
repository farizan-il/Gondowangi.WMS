@extends('Approval-app.Layout.main-admin')
@section('head')
<style>
    :root {
        --primary-color: #0e6a39;
        --primary-light: #1a8047;
        --primary-dark: #0a5229;
    }

    .admin-card {
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        background: white;
        margin-bottom: 1.5rem;
    }

    .table-admin th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background: #f8f9fa;
        padding: 1rem;
    }

    .table-admin td {
        padding: 1rem;
        vertical-align: middle;
    }

    .badge-action {
        font-size: 0.75rem;
        padding: 0.35em 0.8em;
        border-radius: 20px;
        background-color: #e9ecef;
        color: #495057;
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Activity Logs</h5>
                </div>
                <ul class="breadcrumb">
                    <!--  -->
                    <li class="breadcrumb-item active">Activity Logs</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h5 class="mb-0">System Activity Logs</h5>
                    
                    <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        
                        <select name="action" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                            <option value="">All Actions</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ $action }}
                                </option>
                            @endforeach
                        </select>

                        @if(request('search') || request('action'))
                            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-admin table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td style="white-space: nowrap;">
                                    {{ $log->created_at->format('d M Y') }}
                                    <br>
                                    <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                </td>
                                <td>
                                    @if($log->user)
                                        <strong>{{ $log->user->nama }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $log->user->department->nama ?? '-' }}</small>
                                    @else
                                        <span class="text-muted">Unknown User ({{ $log->user_id }})</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-action">{{ $log->action }}</span>
                                </td>
                                <td>
                                    {{ $log->description }}
                                </td>
                                <td>
                                    <span class="text-muted font-monospace small">{{ $log->ip_address }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-history fa-2x mb-3"></i>
                                        <p class="mb-0">No activity logs found.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="p-3 border-top">
                    {{ $logs->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
