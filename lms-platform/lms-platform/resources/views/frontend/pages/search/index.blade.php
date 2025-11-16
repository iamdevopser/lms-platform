@extends('frontend.master')

@section('title', 'Search Results')

@section('content')
<div class="container py-5">
    <!-- Search Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        @if($query)
                            Search Results for "{{ $query }}"
                        @else
                            All Courses
                        @endif
                    </h2>
                    <p class="text-muted mb-0">
                        {{ $courses->total() }} courses found
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('search.advanced') }}" class="btn btn-outline-primary">
                        <i class="fas fa-filter me-1"></i>
                        Advanced Search
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Filters
                    </h5>
                </div>
                <div class="card-body">
                    <form id="searchFilters" method="GET" action="{{ route('frontend.search') }}">
                        <!-- Search Query -->
                        <div class="mb-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="q" class="form-control" value="{{ $query }}" placeholder="Search courses...">
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" id="categoryFilter">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $category == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subcategory Filter -->
                        <div class="mb-3">
                            <label class="form-label">Subcategory</label>
                            <select name="subcategory" class="form-select" id="subcategoryFilter">
                                <option value="">All Subcategories</option>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label">Price Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" name="price_min" class="form-control" value="{{ $price_min }}" placeholder="Min">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="price_max" class="form-control" value="{{ $price_max }}" placeholder="Max">
                                </div>
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="mb-3">
                            <label class="form-label">Minimum Rating</label>
                            <select name="rating" class="form-select">
                                <option value="">Any Rating</option>
                                <option value="4.5" {{ $rating == '4.5' ? 'selected' : '' }}>4.5+ Stars</option>
                                <option value="4.0" {{ $rating == '4.0' ? 'selected' : '' }}>4.0+ Stars</option>
                                <option value="3.5" {{ $rating == '3.5' ? 'selected' : '' }}>3.5+ Stars</option>
                                <option value="3.0" {{ $rating == '3.0' ? 'selected' : '' }}>3.0+ Stars</option>
                            </select>
                        </div>

                        <!-- Level Filter -->
                        <div class="mb-3">
                            <label class="form-label">Level</label>
                            <select name="level" class="form-select">
                                <option value="">All Levels</option>
                                <option value="beginner" {{ $level == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ $level == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ $level == 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                        </div>

                        <!-- Duration Filter -->
                        <div class="mb-3">
                            <label class="form-label">Duration</label>
                            <select name="duration" class="form-select">
                                <option value="">Any Duration</option>
                                <option value="0-2" {{ $duration == '0-2' ? 'selected' : '' }}>0-2 hours</option>
                                <option value="2-5" {{ $duration == '2-5' ? 'selected' : '' }}>2-5 hours</option>
                                <option value="5-10" {{ $duration == '5-10' ? 'selected' : '' }}>5-10 hours</option>
                                <option value="10+" {{ $duration == '10+' ? 'selected' : '' }}>10+ hours</option>
                            </select>
                        </div>

                        <!-- Sort Options -->
                        <div class="mb-3">
                            <label class="form-label">Sort By</label>
                            <select name="sort" class="form-select">
                                <option value="relevance" {{ $sort == 'relevance' ? 'selected' : '' }}>Relevance</option>
                                <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest</option>
                                <option value="popular" {{ $sort == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                <option value="rating" {{ $sort == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                                <option value="price_low" {{ $sort == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ $sort == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            </select>
                        </div>

                        <!-- Apply Filters Button -->
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-search me-1"></i>
                            Apply Filters
                        </button>

                        <!-- Clear Filters -->
                        <a href="{{ route('frontend.search') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i>
                            Clear All
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        <div class="col-lg-9">
            <!-- Results Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="text-muted">Showing {{ $courses->firstItem() ?? 0 }} - {{ $courses->lastItem() ?? 0 }} of {{ $courses->total() }} results</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted">View:</span>
                    <button class="btn btn-sm btn-outline-primary active" id="gridView">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary" id="listView">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Results Container -->
            <div id="searchResults" class="row">
                @forelse($courses as $course)
                    <div class="col-md-6 col-lg-4 mb-4 course-item">
                        <div class="card h-100 shadow-sm">
                            <div class="position-relative">
                                <img src="{{ asset('upload/course/' . $course->image) }}" 
                                     class="card-img-top" 
                                     alt="{{ $course->title }}"
                                     style="height: 200px; object-fit: cover;">
                                @if($course->discount_price)
                                    <div class="position-absolute top-0 start-0 p-2">
                                        <span class="badge bg-danger">
                                            {{ round((($course->price - $course->discount_price) / $course->price) * 100) }}% OFF
                                        </span>
                                    </div>
                                @endif
                                @auth
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <button class="btn btn-sm btn-outline-danger wishlist-toggle-btn" 
                                                data-course-id="{{ $course->id }}"
                                                data-in-wishlist="{{ auth()->user()->hasInWishlist($course->id) ? 'true' : 'false' }}"
                                                title="{{ auth()->user()->hasInWishlist($course->id) ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                                            <i class="la la-heart{{ auth()->user()->hasInWishlist($course->id) ? '' : '-o' }}"></i>
                                        </button>
                                    </div>
                                @endauth
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge bg-primary">{{ $course->category->name ?? 'Uncategorized' }}</span>
                                    @if($course->subCategory)
                                        <span class="badge bg-secondary">{{ $course->subCategory->name }}</span>
                                    @endif
                                </div>
                                
                                <h5 class="card-title">
                                    <a href="{{ route('course-details', $course->slug ?? $course->id) }}" class="text-decoration-none">
                                        {{ $course->title }}
                                    </a>
                                </h5>
                                <p class="card-text text-muted small">
                                    {{ Str::limit($course->description, 100) }}
                                </p>
                                
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $course->user->name ?? 'Unknown Instructor' }}
                                    </small>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="text-warning me-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= ($course->rating ?? 0) ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                        <small class="text-muted">({{ $course->rating ?? 0 }})</small>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $course->students_count ?? 0 }} students enrolled
                                    </small>
                                </div>
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            @if($course->discount_price)
                                                <span class="text-decoration-line-through text-muted">
                                                    ${{ number_format($course->price, 2) }}
                                                </span>
                                                <span class="h5 text-danger mb-0">
                                                    ${{ number_format($course->discount_price, 2) }}
                                                </span>
                                            @else
                                                <span class="h5 text-primary mb-0">
                                                    ${{ number_format($course->price, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <a href="{{ route('course-details', $course->slug ?? $course->id) }}" 
                                           class="btn btn-primary">
                                            <i class="fas fa-eye me-1"></i>
                                            View Course
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-search text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-muted mb-3">No courses found</h4>
                            <p class="text-muted mb-4">Try adjusting your search criteria or browse all courses.</p>
                            <a href="{{ route('frontend.courses') }}" class="btn btn-primary">
                                <i class="fas fa-th-large me-1"></i>
                                Browse All Courses
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($courses->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $courses->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category change handler
    document.getElementById('categoryFilter').addEventListener('change', function() {
        const categoryId = this.value;
        const subcategorySelect = document.getElementById('subcategoryFilter');
        
        // Clear subcategories
        subcategorySelect.innerHTML = '<option value="">All Subcategories</option>';
        
        if (categoryId) {
            // Fetch subcategories
            fetch(`{{ route('search.subcategories') }}?category_id=${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.subcategories.forEach(sub => {
                            const option = document.createElement('option');
                            option.value = sub.id;
                            option.textContent = sub.name;
                            subcategorySelect.appendChild(option);
                        });
                    }
                });
        }
    });

    // Wishlist toggle functionality
    document.querySelectorAll('.wishlist-toggle-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const courseId = this.getAttribute('data-course-id');
            const icon = this.querySelector('i');
            
            fetch('{{ route("wishlist.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ course_id: courseId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'added') {
                        icon.className = 'la la-heart';
                        this.setAttribute('data-in-wishlist', 'true');
                        this.title = 'Remove from Wishlist';
                        this.classList.add('btn-danger');
                        this.classList.remove('btn-outline-danger');
                    } else {
                        icon.className = 'la la-heart-o';
                        this.setAttribute('data-in-wishlist', 'false');
                        this.title = 'Add to Wishlist';
                        this.classList.remove('btn-danger');
                        this.classList.add('btn-outline-danger');
                    }
                    
                    // Update wishlist count in navbar
                    const wishlistCountElement = document.querySelector('.wishlist-count');
                    if (wishlistCountElement) {
                        wishlistCountElement.textContent = data.wishlist_count;
                    }
                }
            });
        });
    });

    // View toggle functionality
    document.getElementById('gridView').addEventListener('click', function() {
        document.getElementById('searchResults').className = 'row';
        this.classList.add('active');
        document.getElementById('listView').classList.remove('active');
    });

    document.getElementById('listView').addEventListener('click', function() {
        document.getElementById('searchResults').className = 'row list-view';
        this.classList.add('active');
        document.getElementById('gridView').classList.remove('active');
    });
});
</script>

<style>
.list-view .course-item {
    flex: 0 0 100%;
    max-width: 100%;
}

.list-view .card {
    flex-direction: row;
}

.list-view .card-img-top {
    width: 200px;
    height: 150px;
    object-fit: cover;
}

.list-view .card-body {
    flex: 1;
}

@media (max-width: 768px) {
    .list-view .card {
        flex-direction: column;
    }
    
    .list-view .card-img-top {
        width: 100%;
        height: 200px;
    }
}
</style>
@endsection 