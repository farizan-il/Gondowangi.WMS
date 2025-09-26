@extends('Gondowangi.Admin.Layout.main')

@section('head')
<style>
    .post-thumbnail {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .quote-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }
    .quote-card::before {
        content: '"';
        font-size: 120px;
        position: absolute;
        top: -20px;
        left: 20px;
        opacity: 0.2;
        font-family: serif;
    }
    .quote-text {
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 15px;
        position: relative;
        z-index: 1;
    }
    .quote-author {
        text-align: right;
        font-style: italic;
        font-size: 14px;
        opacity: 0.9;
    }
    .status-badge {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 12px;
    }
    .status-published {
        background-color: #d4edda;
        color: #155724;
    }
    .status-draft {
        background-color: #fff3cd;
        color: #856404;
    }
    .status-archived {
        background-color: #f8d7da;
        color: #721c24;
    }
    .action-buttons {
        display: flex;
        gap: 5px;
    }
    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .card-modern {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .card-header-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0 !important;
        padding: 20px;
    }
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .stats-number {
        font-size: 2rem;
        font-weight: bold;
        color: #667eea;
    }
    .stats-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Quote Management Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-vs-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            <div class="card card-modern">
                <div class="card-header card-header-modern d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Quote Management</h4>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#quoteModal">
                        <i class="mdi mdi-plus"></i> Edit Quote
                    </button>
                </div>
                <div class="card-body">
                    <div class="quote-card">
                        <div class="quote-text">
                            {{ $quote ? $quote->quote_text : 'Kami percaya kekayaan alam Indonesia memiliki potensi luar biasa untuk kecantikan dan kesehatan, dan kami ingin membagikan manfaatnya melalui produk alami berkualitas.' }}
                        </div>
                        <div class="quote-author">— {{ $quote ? $quote->author : 'Liem Soedarno' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quote Edit Modal -->
<div class="modal fade" id="quoteModal" tabindex="-1" role="dialog" aria-labelledby="quoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quoteModalLabel">Edit Quote</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.postingan.update-quote') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="quote_text">Quote Text</label>
                        <textarea class="form-control" id="quote_text" name="quote_text" rows="5" required>{{ $quote ? $quote->quote_text : 'Kami percaya kekayaan alam Indonesia memiliki potensi luar biasa untuk kecantikan dan kesehatan, dan kami ingin membagikan manfaatnya melalui produk alami berkualitas.' }}</textarea>
                        @error('quote_text')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="author">Author</label>
                        <input type="text" class="form-control" id="author" name="author" value="{{ $quote ? $quote->author : 'Liem Soedarno' }}" required>
                        @error('author')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Quote</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto close alert after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
</script>
@endsection