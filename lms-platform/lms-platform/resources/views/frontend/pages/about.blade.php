@extends('frontend.master')

@section('title', 'About | OnliNote')

@section('content')
<section class="about-section py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-4 mb-md-0">
                <img src="/frontend/images/k12-hero.png" alt="K-12 Digital Education" class="img-fluid rounded shadow">
            </div>
            <div class="col-md-6">
                <h2 class="mb-3" style="font-family:inherit; font-size:2.2rem; font-weight:700;">Safe and Fun Digital Education for K-12 Students with OnliNote!</h2>
                <p class="lead" style="font-size:1.15rem; font-family:inherit;">OnliNote is a modern digital learning platform specially designed for all students from elementary to high school (K-12). Our goal is to ensure that children and young people study in a safe, fun, and effective way.</p>
                <ul class="list-unstyled mb-4" style="font-size:1.08rem; font-family:inherit;">
                    <li class="mb-2"><i class="la la-check text-success"></i> <strong>Safe and Supervised Environment:</strong> Advanced control and reporting tools for parents, safe content for students.</li>
                    <li class="mb-2"><i class="la la-check text-success"></i> <strong>Elementary, Middle, and High School Curriculum:</strong> Math, Science, Language, Social Studies, and much more!</li>
                    <li class="mb-2"><i class="la la-check text-success"></i> <strong>Fun and Interactive Lessons:</strong> Gamified activities, animated videos, interactive quizzes.</li>
                    <li class="mb-2"><i class="la la-check text-success"></i> <strong>Expert Teachers:</strong> High-quality content with experienced teachers and educators.</li>
                    <li class="mb-2"><i class="la la-check text-success"></i> <strong>Access from Anywhere:</strong> Available 24/7 on computer, tablet, and smartphone.</li>
                </ul>
                <p style="font-size:1.08rem; font-family:inherit;">OnliNote helps students love learning, progress at their own pace, and achieve success. For parents, it offers a transparent system where they can instantly track their children's development.</p>
                <a href="/contact" class="btn btn-primary mt-3">Contact Us</a>
            </div>
        </div>
    </div>
</section>
<section class="py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="p-4 bg-white rounded shadow h-100">
                    <i class="la la-child la-3x text-primary mb-3"></i>
                    <h5 style="font-family:inherit; font-size:1.2rem; font-weight:600;">For Students</h5>
                    <p style="font-size:1.05rem; font-family:inherit;">Gamified lessons, rewards, fun quizzes, and the opportunity to learn together with friends.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="p-4 bg-white rounded shadow h-100">
                    <i class="la la-users la-3x text-success mb-3"></i>
                    <h5 style="font-family:inherit; font-size:1.2rem; font-weight:600;">For Parents</h5>
                    <p style="font-size:1.05rem; font-family:inherit;">Instantly track your child's progress, easily view homework and achievement reports.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="p-4 bg-white rounded shadow h-100">
                    <i class="la la-chalkboard-teacher la-3x text-warning mb-3"></i>
                    <h5 style="font-family:inherit; font-size:1.2rem; font-weight:600;">For Teachers</h5>
                    <p style="font-size:1.05rem; font-family:inherit;">Create lessons with modern tools, monitor student progress, and share interactive materials.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 