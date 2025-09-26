@extends('Gondowangi.Main.main')

@section('head')
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $news->meta_description ?? Str::limit($news->excerpt, 160) }}">
    <meta property="og:title" content="{{ $news->title }}">
    <meta property="og:description" content="{{ $news->meta_description ?? Str::limit($news->excerpt, 160) }}">
    <meta property="og:image" content="{{ asset($news->featured_image) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
    
    <style>
        .news-detail-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .news-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .news-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }
        
        .news-meta {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .news-meta span {
            color: #666;
            font-size: 0.9rem;
        }
        
        .news-tags {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .news-tags .tag {
            background: #f0f0f0;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            color: #666;
        }
        
        .news-tags .category-tag {
            background: rgba(255, 206, 0, 0.3);
            color: #0E6A39;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .news-featured-image {
            width: 100%;
            max-width: 800px;
            height: 400px;
            object-fit: cover;
           
            margin: 0 auto 30px;
            display: block;
        }
        
        .news-content {
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.8;
            font-size: 1.1rem;
            color: #333;
        }
        
        .news-content p {
            margin-bottom: 20px;
        }
        
        .news-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .related-news {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #eee;
        }
        
        .related-news-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 30px;
            color: #333;
        }
        
        .related-news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .related-news-card {
            border: 1px solid #eee;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        
        .related-news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .related-news-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .related-news-card-content {
            padding: 20px;
        }
        
        .related-news-card-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        
        .related-news-card-excerpt {
            color: #666;
            margin-bottom: 10px;
        }
        
        .related-news-card-meta {
            font-size: 0.9rem;
            color: #999;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #007bff;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .back-button:hover {
            text-decoration: underline;
        }
        
        .share-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 30px 0;
        }
        
        .share-button {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 0.9rem;
            transition: opacity 0.3s ease;
        }
        
        .share-button:hover {
            opacity: 0.8;
            color: white;
        }
        
        .share-instagram { background: #E1306C; }
        .share-tiktok    { background: #000000; }
        .share-linkedin  { background: #0077B5; }
        .share-youtube   { background: #FF0000; }

        
        @media (max-width: 768px) {
            .news-title {
                font-size: 2rem;
            }
            
            .news-meta {
                flex-direction: column;
                gap: 10px;
            }
            
            .news-featured-image {
                height: 250px;
            }
            
            .news-content {
                font-size: 1rem;
            }
        }
        
        footer{
            background-color: #0B5932 !important;
        }
        
        .beritaclient{
            color: #4d5d6d !important;
        }
    </style>
@endsection

@section('content')
<div class="news-detail-container mt-5">
    <!-- Back Button -->
    <!--<a href="{{ route('berita.index') }}" class="back-button">-->
    <!--    <i class="bx bx-arrow-back"></i>-->
    <!--    Kembali ke Berita-->
    <!--</a>-->
    
    <!-- News Header -->
    <div class="news-header">
        <div class="news-meta">
            <span><i class="bx bx-user"></i> {{ $news->author->name ?? 'Admin' }}</span>
            
            <span><i class="bx bx-show"></i> {{ $news->formatted_views }} views</span>
        </div>
        
        @if($news->category || $news->tags)
            <div class="news-tags">
                @if($news->category)
                    <span class="category-tag"><strong>{{ $news->category->category_name }}</strong></span>
                @endif
                @if($news->tags)
                    @foreach(explode(',', $news->tags) as $tag)
                        <span class="tag">{{ trim($tag) }}</span>
                    @endforeach
                @endif
                
                <span style="color: #0E6A39;"> {{ $news->published_at->format('F j, Y') }}</span>
            </div>
        @endif
        
        <h1 class="news-title">{{ $news->title }}</h1>
    </div>
    
    <!-- Featured Image -->
    @if($news->featured_image)
        <img src="{{ asset($news->featured_image) }}" alt="{{ $news->title }}" class="news-featured-image rounded">
    @endif
    
    <!-- News Content -->
    <div class="news-content">
        {!! $news->content !!}
    </div>
    
    <!-- Share Buttons -->
    <div class="share-buttons">
        <a href="https://www.instagram.com/" 
           target="_blank" class="share-button share-instagram">
            <i class="bx bxl-instagram"></i> Instagram
        </a>
        <a href="https://www.tiktok.com/" 
           target="_blank" class="share-button share-tiktok">
            <i class="bx bxl-tiktok"></i> TikTok
        </a>
        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" 
           target="_blank" class="share-button share-linkedin">
            <i class="bx bxl-linkedin"></i> LinkedIn
        </a>
        <a href="https://www.youtube.com/" 
           target="_blank" class="share-button share-youtube">
            <i class="bx bxl-youtube"></i> YouTube
        </a>
    </div>
    
    <!-- Related News -->
    @if($relatedNews->count() > 0)
        <div class="related-news">
            <h2 class="related-news-title">Berita Terkait</h2>
            <div class="related-news-grid">
                @foreach($relatedNews as $related)
                    <div class="related-news-card" onclick="window.location.href='{{ route('berita.show', $related->slug) }}'">
                        <img src="{{ $related->thumbnail_url }}" alt="{{ $related->title }}" loading="lazy">
                        <div class="related-news-card-content">
                            <h3 class="related-news-card-title">{{ Str::limit($related->title, 80) }}</h3>
                            <p class="related-news-card-excerpt">{{ Str::limit($related->excerpt, 100) }}</p>
                            <div class="related-news-card-meta">
                                <span>{{ $related->published_at->format('F j, Y') }}</span>
                                <span> • {{ $related->formatted_views }} views</span>
                            </div>
                        </div>
                    </div>
                    <!--<div class="related-news-card" onclick="window.location.href='{{ route('berita.show', $related->slug) }}'">-->
                    <!--    <img src="{{ $related->thumbnail_url }}" alt="{{ $related->title }}" loading="lazy">-->
                    <!--    <div class="related-news-card-content">-->
                    <!--        <h3 class="related-news-card-title">{{ Str::limit($related->title, 80) }}</h3>-->
                    <!--        <p class="related-news-card-excerpt">{{ Str::limit($related->excerpt, 100) }}</p>-->
                    <!--        <div class="related-news-card-meta">-->
                    <!--            <span>{{ $related->published_at->format('F j, Y') }}</span>-->
                    <!--            <span> • {{ $related->formatted_views }} views</span>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for back button
    const backButton = document.querySelector('.back-button');
    if (backButton) {
        backButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.history.back();
        });
    }
    
    // Track reading progress (optional)
    let readingProgress = 0;
    const content = document.querySelector('.news-content');
    
    if (content) {
        window.addEventListener('scroll', function() {
            const contentHeight = content.offsetHeight;
            const contentTop = content.offsetTop;
            const scrollPosition = window.pageYOffset;
            const windowHeight = window.innerHeight;
            
            if (scrollPosition > contentTop && scrollPosition < contentTop + contentHeight) {
                const progress = Math.min(100, Math.max(0, 
                    ((scrollPosition - contentTop + windowHeight) / contentHeight) * 100
                ));
                
                if (progress > readingProgress) {
                    readingProgress = progress;
                    
                    // Send reading progress to server (optional)
                    if (progress > 50 && !window.halfRead) {
                        window.halfRead = true;
                        // You can send an AJAX request here to track engagement
                    }
                }
            }
        });
    }
    
    // Image lazy loading fallback
    const images = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(function(img) {
            imageObserver.observe(img);
        });
    }
});
</script>
@endsection