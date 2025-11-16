<?php
$categories = getCategories();
?>

<header class="header-menu-area bg-white">
    <div class="header-top pr-150px pl-150px border-bottom border-bottom-gray py-1">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="header-widget">
                        <ul class="generic-list-item d-flex flex-wrap align-items-center fs-14">
                            <li class="d-flex align-items-center pr-3 mr-3 border-right border-right-gray"><i
                                    class="la la-phone mr-1"></i><a href="tel:00123456789"> (00) 123 456 789</a>
                            </li>
                            <li class="d-flex align-items-center"><i class="la la-envelope-o mr-1"></i><a
                                    href="mailto:contact@onlynote.com"> contact@onlynote.com</a></li>
                        </ul>
                    </div><!-- end header-widget -->
                </div><!-- end col-lg-6 -->
                <div class="col-lg-6">
                    <div class="header-widget d-flex flex-wrap align-items-center justify-content-end">
                        <div class="theme-picker d-flex align-items-center">
                            <button class="theme-picker-btn dark-mode-btn" title="Dark mode">
                                <svg id="moon" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                </svg>
                            </button>
                            <button class="theme-picker-btn light-mode-btn" title="Light mode">
                                <svg id="sun" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="5"></circle>
                                    <line x1="12" y1="1" x2="12" y2="3"></line>
                                    <line x1="12" y1="21" x2="12" y2="23"></line>
                                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                    <line x1="1" y1="12" x2="3" y2="12"></line>
                                    <line x1="21" y1="12" x2="23" y2="12"></line>
                                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                                </svg>
                            </button>
                        </div>

                        @if (!auth()->user())
                            <ul
                                class="generic-list-item d-flex flex-wrap align-items-center fs-14 border-left border-left-gray pl-3 ml-3">
                                <li class="d-flex align-items-center pr-3 mr-3 border-right border-right-gray"><i
                                        class="la la-sign-in mr-1"></i><a href="{{ route('login') }}"> Login</a></li>
                                <li class="d-flex align-items-center"><i class="la la-user mr-1"></i><a
                                        href="{{ route('register') }}"> Register</a></li>
                            </ul>
                        @else
                            <div class="user-profile-dropdown">
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <div class="user-avatar me-2">
                                            <img src="{{ asset('frontend/images/small-avatar-1.jpg') }}" alt="User Avatar" class="rounded-circle" style="width: 32px; height: 32px;">
                                        </div>
                                        <span class="user-name">{{ auth()->user()->name }}</span>
                                        <i class="la la-angle-down ms-2"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                        <li class="dropdown-header">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('frontend/images/small-avatar-1.jpg') }}" alt="User Avatar" class="rounded-circle me-3" style="width: 40px; height: 40px;">
                                                <div>
                                                    <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                                                    <small class="text-muted">{{ auth()->user()->email }}</small>
                                                </div>
                                            </div>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.profile') }}">
                                                <i class="la la-user me-2"></i>Profile
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.setting') }}">
                                                <i class="la la-cog me-2"></i>Account Setting
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <i class="la la-credit-card me-2"></i>Subscription/Plan
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <i class="la la-graduation-cap me-2"></i>My Courses
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <i class="la la-envelope me-2"></i>Messages/Inbox
                                            </a>
                                        </li>
                                        @if (auth()->user()->role == 'instructor')
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <i class="la la-dollar me-2"></i>Earn/Payment
                                            </a>
                                        </li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        @if (auth()->user()->role == 'user')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                                    <i class="la la-dashboard me-2"></i>Dashboard
                                                </a>
                                            </li>
                                        @endif
                                        @if (auth()->user()->role == 'admin')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                                    <i class="la la-dashboard me-2"></i>Admin Panel
                                                </a>
                                            </li>
                                        @endif
                                        @if (auth()->user()->role == 'instructor')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('instructor.dashboard') }}">
                                                    <i class="la la-dashboard me-2"></i>Instructor Panel
                                                </a>
                                            </li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="la la-sign-out me-2"></i>Sign Out
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endif





                    </div><!-- end header-widget -->
                </div><!-- end col-lg-6 -->
            </div><!-- end row -->
        </div><!-- end container-fluid -->
    </div><!-- end header-top -->
    <div class="header-menu-content pr-150px pl-150px bg-white">
        <div class="container-fluid">
            <div class="main-menu-content">
                <a href="#" class="down-button"><i class="la la-angle-down"></i></a>
                <div class="row align-items-center">
                    <div class="col-lg-2">
                        <div class="logo-box">
                            <a href="{{ route('frontend.home') }}" class="logo">
                                <img src="{{asset('frontend/images/onlynote-logo.svg')}}" alt="OnlyNote Logo" style="height: 50px; width: auto;" onerror="this.src='{{asset('frontend/images/onlynote-logo.png')}}'">
                            </a>
                            <div class="user-btn-action">
                                <div class="search-menu-toggle icon-element icon-element-sm shadow-sm mr-2"
                                    data-toggle="tooltip" data-placement="top" title="Search">
                                    <i class="la la-search"></i>
                                </div>
                                <div class="off-canvas-menu-toggle cat-menu-toggle icon-element icon-element-sm shadow-sm mr-2"
                                    data-toggle="tooltip" data-placement="top" title="Category menu">
                                    <i class="la la-th-large"></i>
                                </div>
                                <div class="off-canvas-menu-toggle main-menu-toggle icon-element icon-element-sm shadow-sm"
                                    data-toggle="tooltip" data-placement="top" title="Main menu">
                                    <i class="la la-bars"></i>
                                </div>
                            </div>
                        </div>
                    </div><!-- end col-lg-2 -->
                    <div class="col-lg-10">
                        <div class="menu-wrapper">
                            <div class="menu-category">
                                <ul>
                                    <li>
                                        <a href="{{ route('frontend.search') }}">All Courses <i class="la la-angle-down fs-12"></i></a>
                                        <ul class="cat-dropdown-menu">
                                            @foreach($categories as $item)
                                            <li>
                                                <a href="{{ route('frontend.search') }}?category={{ $item->id }}">{{$item->name}} <i
                                                        class="la la-angle-right"></i></a>
                                                <ul class="sub-menu">
                                                    @foreach ($item->subcategory as $data)
                                                    <li><a href="{{ route('frontend.search') }}?subcategory={{ $data->id }}">{{$data->name}}</a></li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                </ul>
                            </div><!-- end menu-category -->
                            <form method="GET" action="{{ route('frontend.search') }}" class="search-form">
                                <div class="form-group mb-0 position-relative">
                                    <input class="form-control form--control pl-3" 
                                           type="text" 
                                           name="q"
                                           id="searchInput"
                                           placeholder="Search for courses, categories, instructors..."
                                           autocomplete="off">
                                    <span class="la la-search search-icon"></span>
                                    
                                    <!-- Search Suggestions Dropdown -->
                                    <div class="search-suggestions" id="searchSuggestions" style="display: none;">
                                        <div class="suggestions-content">
                                            <!-- Suggestions will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <nav class="main-menu">
                                <ul>
                                    <li>
                                        <a href="/">Home</a>
                                    </li>
                                    <li>
                                        <a href="#">Features <i class="la la-angle-down fs-12"></i></a>
                                        <ul class="cat-dropdown-menu">
                                            <li>
                                                <a href="{{ route('frontend.blog') }}">Blog</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('frontend.about') }}">About</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('frontend.contact') }}">Contact</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('frontend.becomeInstructor') }}">Become an Instructor</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('pricing') }}">Pricing</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul><!-- end ul -->
                            </nav><!-- end main-menu -->



                            <!-- Wishlist -->
                            @auth
                                <div class="shop-cart mr-4">
                                    <ul>
                                        <li>
                                            <a href="{{ route('user.wishlist.index') }}" class="shop-cart-btn d-flex align-items-center">
                                                <i class="la la-heart"></i>
                                                <span class="product-count wishlist-count" style="margin-left: 5px">
                                                    {{ auth()->user()->wishlist()->count() }}
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <div class="shop-cart mr-4">
                                    <ul>
                                        <li>
                                            <a href="{{ route('login') }}" class="shop-cart-btn d-flex align-items-center">
                                                <i class="la la-heart"></i>
                                                <span class="product-count" style="margin-left: 5px">0</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endauth


                            <div class="shop-cart mr-4" id='cart'>

                                <!--ajax loaded for cart frontend.pages.home.partial.cart  -->

                            </div><!-- end shop-cart -->















                        </div><!-- end menu-wrapper -->
                    </div><!-- end col-lg-10 -->
                </div><!-- end row -->
            </div>
        </div><!-- end container-fluid -->
    </div><!-- end header-menu-content -->


    <div class="off-canvas-menu custom-scrollbar-styled main-off-canvas-menu">
        <div class="off-canvas-menu-close main-menu-close icon-element icon-element-sm shadow-sm"
            data-toggle="tooltip" data-placement="left" title="Close menu">
            <i class="la la-times"></i>
        </div><!-- end off-canvas-menu-close -->
        <ul class="generic-list-item off-canvas-menu-list pt-90px">
            <li>
                <a href="{{ route('frontend.home') }}">Home</a>
            </li>
            <li>
                <a href="{{ route('frontend.search') }}">All Courses</a>
            </li>
            @if (!auth()->check())
                <li>
                    <a href="{{ route('login') }}">Login</a>
                </li>
                <li>
                    <a href="{{ route('register') }}">Register</a>
                </li>
            @else
                <li>
                    <a href="{{ route('user.dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ route('user.profile') }}">Profile</a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 text-danger">
                            Logout
                        </button>
                    </form>
                </li>
            @endif
            <li>
                <a href="{{ route('frontend.contact') }}">Contact Us</a>
            </li>
        </ul>
    </div><!-- end off-canvas-menu -->




</header><!-- end header-menu-area -->

<!-- Search Suggestions JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    const suggestionsContent = document.querySelector('.suggestions-content');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                searchSuggestions.style.display = 'none';
                return;
            }

            // Set timeout to avoid too many requests
            searchTimeout = setTimeout(() => {
                fetchSuggestions(query);
            }, 300);
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                searchSuggestions.style.display = 'none';
            }
        });

        // Show suggestions when focusing on input
        searchInput.addEventListener('focus', function() {
            const query = this.value.trim();
            if (query.length >= 2) {
                searchSuggestions.style.display = 'block';
            }
        });
    }

    function fetchSuggestions(query) {
        fetch(`{{ route('search.suggestions') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.suggestions.length > 0) {
                    displaySuggestions(data.suggestions);
                    searchSuggestions.style.display = 'block';
                } else {
                    searchSuggestions.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error fetching suggestions:', error);
                searchSuggestions.style.display = 'none';
            });
    }

    function displaySuggestions(suggestions) {
        suggestionsContent.innerHTML = '';
        
        suggestions.forEach(suggestion => {
            const item = document.createElement('div');
            item.className = 'suggestion-item';
            item.innerHTML = `
                <a href="${suggestion.url}" class="d-flex align-items-center p-2 text-decoration-none">
                    <i class="${suggestion.icon} me-2 text-muted"></i>
                    <span>${suggestion.title}</span>
                    <small class="text-muted ms-auto">${suggestion.type}</small>
                </a>
            `;
            suggestionsContent.appendChild(item);
        });
    }
});
</script>

<!-- Bootstrap Dropdown JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple dropdown toggle functionality
    const userDropdown = document.getElementById('userDropdown');
    const dropdownMenu = userDropdown.nextElementSibling;
    
    if (userDropdown && dropdownMenu) {
        userDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });

        // Close dropdown when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdownMenu.classList.remove('show');
            }
        });
    }
});
</script>

<style>
.search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 4px 4px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
}

.suggestions-content {
    padding: 0;
}

.suggestion-item {
    border-bottom: 1px solid #f0f0f0;
}

.suggestion-item:last-child {
    border-bottom: none;
}

.suggestion-item a {
    color: #333;
    transition: background-color 0.2s;
}

.suggestion-item a:hover {
    background-color: #f8f9fa;
    color: #333;
}

.suggestion-item i {
    width: 16px;
    text-align: center;
}

/* User Profile Dropdown Styles */
.user-profile-dropdown {
    margin-left: 15px;
    position: relative;
}

.user-profile-dropdown .btn {
    background: transparent;
    border: 1px solid #e0e0e0;
    border-radius: 25px;
    padding: 8px 16px;
    color: #333;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.user-profile-dropdown .btn:hover {
    background: #f8f9fa;
    border-color: #007bff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.user-profile-dropdown .btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.user-avatar img {
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.user-name {
    font-weight: 500;
    margin: 0 8px;
}

.dropdown-menu {
    border: none;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    padding: 8px 0;
    min-width: 280px;
    margin-top: 8px;
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    z-index: 9999;
    background: white;
}

.dropdown-menu.show {
    display: block;
    animation: fadeInDown 0.3s ease;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown-header {
    padding: 12px 20px;
    background: #f8f9fa;
    border-radius: 12px 12px 0 0;
}

.dropdown-header h6 {
    color: #333;
    font-weight: 600;
}

.dropdown-header small {
    color: #6c757d;
}

.dropdown-item {
    padding: 10px 20px;
    color: #333;
    transition: all 0.2s ease;
    border-radius: 8px;
    margin: 2px 8px;
}

.dropdown-item:hover {
    background: #f8f9fa;
    color: #007bff;
    transform: translateX(5px);
}

.dropdown-item i {
    width: 16px;
    text-align: center;
    margin-right: 8px;
}

.dropdown-divider {
    margin: 8px 0;
    border-color: #e9ecef;
}

.dropdown-item.text-danger:hover {
    background: #fff5f5;
    color: #dc3545;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .user-profile-dropdown .btn {
        padding: 6px 12px;
        font-size: 13px;
    }
    
    .user-name {
        display: none;
    }
    
    .dropdown-menu {
        min-width: 250px;
    }
}
</style>

