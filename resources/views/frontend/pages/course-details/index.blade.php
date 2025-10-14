@extends('frontend.master')

@section('content')


    @include('frontend.pages.course-details.breadcrumb')

    <!--======================================
        START COURSE DETAILS AREA
======================================-->
    <section class="course-details-area pb-20px">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 pb-5">
                    <div class="course-details-content-wrap pt-90px">

                        @include('frontend.pages.course-details.learn-section')


                        @include('frontend.pages.course-details.course-content')

                        @include('frontend.pages.course-details.student-bought')

                        @include('frontend.pages.course-details.instructor-about')

                        @include('frontend.pages.course-details.student-feedback')


                        @include('frontend.pages.course-details.review')


                    </div><!-- end course-details-content-wrap -->
                </div><!-- end col-lg-8 -->

                @include('frontend.pages.course-details.right-sidebar')

            </div><!-- end row -->
        </div><!-- end container -->
    </section><!-- end course-details-area -->

    <!-- Modal -->
    @include('frontend.pages.course-details.course-preview-modal')

    @include('frontend.pages.course-details.related-course')


    @include('frontend.pages.course-details.become-teacher')




    <div class="section-block"></div>






@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wishlist toggle functionality
    const wishlistBtn = document.querySelector('.wishlist-toggle-btn');
    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', function() {
            const courseId = this.getAttribute('data-course-id');
            const isInWishlist = this.getAttribute('data-in-wishlist') === 'true';
            
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="la la-spinner la-spin mr-1"></i> Processing...';
            this.disabled = true;
            
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
                    // Update button state
                    const icon = this.querySelector('i');
                    const text = this.querySelector('.wishlist-text');
                    
                    if (data.action === 'added') {
                        icon.className = 'la la-heart mr-1';
                        text.textContent = 'Remove from Wishlist';
                        this.setAttribute('data-in-wishlist', 'true');
                        this.classList.add('btn-danger');
                        this.classList.remove('btn-outline-danger');
                    } else {
                        icon.className = 'la la-heart-o mr-1';
                        text.textContent = 'Add to Wishlist';
                        this.setAttribute('data-in-wishlist', 'false');
                        this.classList.remove('btn-danger');
                        this.classList.add('btn-outline-danger');
                    }
                    
                    // Update wishlist count in navbar if exists
                    const wishlistCountElement = document.querySelector('.wishlist-count');
                    if (wishlistCountElement) {
                        wishlistCountElement.textContent = data.wishlist_count;
                    }
                    
                    // Show success message
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred', 'error');
            })
            .finally(() => {
                // Restore button state
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
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
@endpush
