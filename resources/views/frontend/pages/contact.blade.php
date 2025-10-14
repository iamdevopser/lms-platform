@extends('frontend.master')
@section('content')
    <div class="container py-5">
        <h1>Contact Us</h1>
        <p>Fill out the form below to contact us. We will get back to you as soon as possible.</p>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('frontend.contact.store') }}" class="row g-3 mt-4">
            @csrf
            <div class="col-md-6">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="col-12">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" class="form-control" id="subject" name="subject" required>
            </div>
            <div class="col-12">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Send Message</button>
            </div>
        </form>
    </div>
@endsection 