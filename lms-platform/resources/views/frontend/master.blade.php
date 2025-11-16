<!DOCTYPE html>
<html lang="en">

<head>

    <title>OnlyNote - Learning Management System</title>

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('frontend/images/favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('frontend/images/favicon.svg') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- inject:css -->

    @include('frontend.section.link')

    <!-- end inject -->

    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 50%, #80deea 100%);
            min-height: 100vh;
            box-shadow: inset 0 0 50px rgba(0, 150, 136, 0.1);
        }
        
        .header-menu-area {
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1000;
        }
        
        .header-menu-area.scrolled {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(15px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .header-menu-area.scrolled .header-menu-content {
            background: rgba(255, 255, 255, 0.98) !important;
        }
        
        .header-menu-area.scrolled .header-top {
            background: rgba(255, 255, 255, 0.98) !important;
        }
        
        .bg-light {
            background-color: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(5px);
        }
        
        .bg-white {
            background-color: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(5px);
        }
        
        /* Navbar'ın her zaman görünür olması için */
        .header-menu-area {
            position: sticky;
            top: 0;
            z-index: 1000;
        }
    </style>

</head>

<body>

    <!-- start cssload-loader -->
    @include('frontend.section.preloader')

    <!--START HEADER AREA-->
    @include('frontend.section.header')

    @yield('content')


    <!--START COURSE First AREA-->



    <!--START COURSE AREA-->



    <!--START FUNFACT AREA -->



    <!--START CTA AREA-->

    <!--START TESTIMONIAL AREA-->


    <div class="section-block"></div>

    <!--START ABOUT AREA-->



    <div class="section-block"></div>

    <!--START REGISTER AREA-->


    <div class="section-block"></div>

    <!--START CLIENT-LOGO AREA -->




    <!--START BLOG AREA -->




    <!----START GET STARTED AREA---->



    <!---subscribe-area------->



    <!---footer-area--->
    @include('frontend.section.footer')


    <!-- start scroll top -->
    <div id="scroll-top">
        <i class="la la-arrow-up" title="Go top"></i>
    </div>


    <!---tooltip--->

    @include('frontend.section.tooltip')


    <!-- template js files -->
    @include('frontend.section.script')
    
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header-menu-area');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>
    @yield('scripts')
</body>

</html>
