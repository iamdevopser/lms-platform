@extends('frontend.master')

@section('title', 'My Wishlist')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-heart text-danger me-2"></i>
                    My Wishlist
                </h2>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger" id="clearWishlistBtn">
                        <i class="fas fa-trash me-1"></i>
                        Clear All
                    </button>
                    <a href="{{ route('cart') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-1"></i>
                        View Cart
                    </a>
                </div>
            </div>

            @if($wishlistItems->count() > 0)
                <div class="row" id="wishlistContainer">
                    @foreach($wishlistItems as $item)
                        <div class="col-md-6 col-lg-4 mb-4 wishlist-item" data-course-id="{{ $item->course_id }}">
                            <div class="card h-100 shadow-sm">
                                <div class="position-relative">
                                    <img src="{{ asset('upload/course/' . $item->course->image) }}" 
                                         class="card-img-top" 
                                         alt="{{ $item->course->title }}"
                                         style="height: 200px; object-fit: cover;">
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <button class="btn btn-sm btn-danger remove-wishlist-btn" 
                                                data-course-id="{{ $item->course_id }}"
                                                title="Remove from wishlist">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    @if($item->course->discount_price)
                                        <div class="position-absolute top-0 start-0 p-2">
                                            <span class="badge bg-danger">
                                                {{ round((($item->course->price - $item->course->discount_price) / $item->course->price) * 100) }}% OFF
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <span class="badge bg-primary">{{ $item->course->category->name ?? 'Uncategorized' }}</span>
                                        @if($item->course->subCategory)
                                            <span class="badge bg-secondary">{{ $item->course->subCategory->name }}</span>
                                        @endif
                                    </div>
                                    
                                    <h5 class="card-title">{{ $item->course->title }}</h5>
                                    <p class="card-text text-muted small">
                                        {{ Str::limit($item->course->description, 100) }}
                                    </p>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            {{ $item->course->user->name ?? 'Unknown Instructor' }}
                                        </small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-1">
                                            <div class="text-warning me-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= ($item->course->rating ?? 0) ? '' : '-o' }}"></i>
                                                @endfor
                                            </div>
                                            <small class="text-muted">({{ $item->course->rating ?? 0 }})</small>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $item->course->students_count ?? 0 }} students enrolled
                                        </small>
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                @if($item->course->discount_price)
                                                    <span class="text-decoration-line-through text-muted">
                                                        ${{ number_format($item->course->price, 2) }}
                                                    </span>
                                                    <span class="h5 text-danger mb-0">
                                                        ${{ number_format($item->course->discount_price, 2) }}
                                                    </span>
                                                @else
                                                    <span class="h5 text-primary mb-0">
                                                        ${{ number_format($item->course->price, 2) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <small class="text-muted">
                                                Added {{ $item->added_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary move-to-cart-btn" 
                                                    data-course-id="{{ $item->course_id }}">
                                                <i class="fas fa-shopping-cart me-1"></i>
                                                Move to Cart
                                            </button>
                                            <a href="{{ route('course-details', $item->course->slug ?? $item->course->id) }}" 
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>
                                                View Course
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $wishlistItems->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-heart text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-muted mb-3">Your wishlist is empty</h4>
                    <p class="text-muted mb-4">Start exploring courses and add them to your wishlist!</p>
                    <a href="{{ route('frontend.home') }}" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>
                        Browse Courses
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage">Are you sure you want to perform this action?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmAction">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove from wishlist
    document.querySelectorAll('.remove-wishlist-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const courseId = this.getAttribute('data-course-id');
            const courseTitle = this.closest('.wishlist-item').querySelector('.card-title').textContent;
            
            showConfirmModal(
                `Are you sure you want to remove "${courseTitle}" from your wishlist?`,
                () => removeFromWishlist(courseId)
            );
        });
    });

    // Move to cart
    document.querySelectorAll('.move-to-cart-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const courseId = this.getAttribute('data-course-id');
            const courseTitle = this.closest('.wishlist-item').querySelector('.card-title').textContent;
            
            showConfirmModal(
                `Move "${courseTitle}" to cart and remove from wishlist?`,
                () => moveToCart(courseId)
            );
        });
    });

    // Clear all wishlist
    document.getElementById('clearWishlistBtn').addEventListener('click', function() {
        showConfirmModal(
            'Are you sure you want to clear your entire wishlist? This action cannot be undone.',
            clearWishlist
        );
    });

    function showConfirmModal(message, action) {
        document.getElementById('confirmMessage').textContent = message;
        document.getElementById('confirmAction').onclick = action;
        new bootstrap.Modal(document.getElementById('confirmModal')).show();
    }

    function removeFromWishlist(courseId) {
        fetch('{{ route("user.wishlist.remove") }}', {
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
                const item = document.querySelector(`[data-course-id="${courseId}"]`);
                item.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => {
                    item.remove();
                    updateWishlistCount(data.wishlist_count);
                    checkEmptyWishlist();
                }, 300);
                
                showToast('Course removed from wishlist', 'success');
            } else {
                showToast(data.message || 'Error removing course', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function moveToCart(courseId) {
        fetch('{{ route("user.wishlist.move-to-cart") }}', {
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
                const item = document.querySelector(`[data-course-id="${courseId}"]`);
                item.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => {
                    item.remove();
                    updateWishlistCount(data.wishlist_count);
                    checkEmptyWishlist();
                }, 300);
                
                showToast('Course moved to cart', 'success');
            } else {
                showToast(data.message || 'Error moving course', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function clearWishlist() {
        fetch('{{ route("user.wishlist.clear") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('wishlistContainer').innerHTML = `
                    <div class="col-12 text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-heart text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="text-muted mb-3">Your wishlist is empty</h4>
                        <p class="text-muted mb-4">Start exploring courses and add them to your wishlist!</p>
                        <a href="{{ route('frontend.home') }}" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>
                            Browse Courses
                        </a>
                    </div>
                `;
                updateWishlistCount(0);
                showToast('Wishlist cleared successfully', 'success');
            } else {
                showToast(data.message || 'Error clearing wishlist', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function updateWishlistCount(count) {
        // Update wishlist count in navbar if exists
        const wishlistCountElement = document.querySelector('.wishlist-count');
        if (wishlistCountElement) {
            wishlistCountElement.textContent = count;
        }
    }

    function checkEmptyWishlist() {
        const items = document.querySelectorAll('.wishlist-item');
        if (items.length === 0) {
            location.reload(); // Reload to show empty state
        }
    }

    function showToast(message, type) {
        // Simple toast implementation
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
});
</script>

<style>
@keyframes fadeOut {
    from { opacity: 1; transform: scale(1); }
    to { opacity: 0; transform: scale(0.9); }
}

.wishlist-item {
    transition: all 0.3s ease;
}

.wishlist-item:hover {
    transform: translateY(-5px);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection 