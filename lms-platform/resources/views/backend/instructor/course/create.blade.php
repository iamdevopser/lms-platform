@extends('backend.instructor.master')

@section('content')
<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Course</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Course</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-body">
            <!-- Progress Bar -->
            <div class="progress mb-4" style="height: 8px;">
                <div class="progress-bar" role="progressbar" style="width: 33.33%;" id="progressBar"></div>
            </div>

            <!-- Step Navigation -->
            <div class="d-flex justify-content-between mb-4">
                <div class="step-indicator active" data-step="1">
                    <div class="step-circle">1</div>
                    <span class="step-text">Course Info</span>
                </div>
                <div class="step-indicator" data-step="2">
                    <div class="step-circle">2</div>
                    <span class="step-text">Content Builder</span>
                </div>
                <div class="step-indicator" data-step="3">
                    <div class="step-circle">3</div>
                    <span class="step-text">Review & Publish</span>
                </div>
            </div>

            <form id="courseForm" method="post" action="{{ route('instructor.course.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="instructor_id" value="{{ auth()->user()->id }}" />

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Step 1: Course Info -->
                <div class="step-content active" id="step1">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="text-center">
                                <h4 class="text-muted mb-2">Course Information</h4>
                                <p class="text-muted">Start by providing basic information about your course</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="course_name" class="form-label">Course Name *</label>
                            <input type="text" class="form-control" name="course_name" id="course_name" 
                                   placeholder="Enter the course name" value="{{ old('course_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="course_title" class="form-label">Course Title *</label>
                            <input type="text" class="form-control" name="course_title" id="course_title" 
                                   placeholder="Enter the course title" value="{{ old('course_title') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Category *</label>
                            <select class="form-select" name="category_id" id="category_id" required>
                                <option value="" disabled selected>Select a category</option>
                                @foreach ($all_categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="subcategory_id" class="form-label">Subcategory *</label>
                            <select class="form-select" name="subcategory_id" id="subcategory_id" required disabled>
                                <option value="" disabled selected>Select a subcategory</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="level" class="form-label">Course Level *</label>
                            <select class="form-select" name="level" id="level" required>
                                <option value="" disabled selected>Select level</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="language" class="form-label">Language *</label>
                            <select class="form-select" name="language" id="language" required>
                                <option value="" disabled selected>Select language</option>
                                <option value="en">English</option>
                                <option value="tr">Turkish</option>
                                <option value="de">German</option>
                                <option value="fr">French</option>
                                <option value="es">Spanish</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="shortDescription" class="form-label">Short Description *</label>
                            <textarea class="form-control" name="shortDescription" id="shortDescription" 
                                      rows="3" placeholder="Brief description of your course" required>{{ old('shortDescription') }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="description" class="form-label">Full Description *</label>
                            <textarea class="form-control editor" name="description" id="description" 
                                      rows="6" placeholder="Detailed description of your course" required>{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Content Builder -->
                <div class="step-content" id="step2">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="text-center">
                                <h4 class="text-muted mb-2">Content Builder</h4>
                                <p class="text-muted">Add media, structure, and organize your course content</p>
                            </div>
                        </div>
                        
                        <!-- Media Section -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="bx bx-image"></i> Course Media</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Course Image *</label>
                                        <input type="file" class="form-control" name="image" id="image" accept="image/*" required>
                                        <div class="mt-2">
                                            <img src="" id="imagePreview" class="img-thumbnail" style="max-width: 200px; display: none;" />
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="video_url" class="form-label">Promo Video URL (YouTube) *</label>
                                        <input type="url" class="form-control" name="video_url" id="video_url" 
                                               placeholder="Enter YouTube video URL" value="{{ old('video_url') }}" required>
                                        <div class="mt-2">
                                            <iframe id="videoPreview" style="width: 100%; height: 200px; display: none;" 
                                                    frameborder="0" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Structure -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="bx bx-layout"></i> Course Structure</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="duration" class="form-label">Estimated Duration (hours) *</label>
                                        <input type="number" class="form-control" name="duration" id="duration" 
                                               placeholder="0.0" step="0.1" min="0.1" value="{{ old('duration') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="resources" class="form-label">Downloadable Resources</label>
                                        <input type="number" class="form-control" name="resources" id="resources" 
                                               placeholder="Number of resources" min="0" value="{{ old('resources', 0) }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="certificate" class="form-label">Certificate</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="certificateToggle" name="certificate" value="yes">
                                            <label class="form-check-label" for="certificateToggle">Provide completion certificate</label>
                                        </div>
                                        <input type="hidden" name="certificate" id="certificateHidden" value="no">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Goals -->
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="bx bx-target-lock"></i> Course Goals & Requirements</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="requirements" class="form-label">Requirements</label>
                                            <textarea class="form-control editor" name="requirements" id="requirements" 
                                                      rows="4" placeholder="What students need to know before taking this course">{{ old('requirements') }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="objectives" class="form-label">Learning Objectives</label>
                                            <textarea class="form-control editor" name="objectives" id="objectives" 
                                                      rows="4" placeholder="What students will learn from this course">{{ old('objectives') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label class="form-label">Course Goals</label>
                                        <div id="goalsContainer">
                                            <div class="goal-item d-flex align-items-center gap-2 mb-2">
                                                <input type="text" class="form-control" name="course_goals[]" placeholder="Enter course goal" />
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-goal" style="display: none;">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="addGoal">
                                            <i class="bx bx-plus"></i> Add Goal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Review & Publish -->
                <div class="step-content" id="step3">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="text-center">
                                <h4 class="text-muted mb-2">Review & Publish</h4>
                                <p class="text-muted">Review your course details and finalize publishing settings</p>
                            </div>
                        </div>
                        
                        <!-- Course Preview -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="bx bx-show"></i> Course Preview</h6>
                                </div>
                                <div class="card-body">
                                    <div id="coursePreview">
                                        <p class="text-muted">Complete previous steps to see preview</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Publishing Settings -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="bx bx-cog"></i> Publishing Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="selling_price" class="form-label">Price *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" name="selling_price" id="selling_price" 
                                                   placeholder="0.00" step="0.01" min="0" value="{{ old('selling_price') }}" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="discount_price" class="form-label">Discount Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" name="discount_price" id="discount_price" 
                                                   placeholder="0.00" step="0.01" min="0" value="{{ old('discount_price') }}">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Special Tags</label>
                                        <div class="space-y-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="bestseller" name="bestseller" value="yes">
                                                <label class="form-check-label" for="bestseller">Bestseller</label>
                                                <input type="hidden" name="bestseller" id="bestsellerHidden" value="no">
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="featured" name="featured" value="yes">
                                                <label class="form-check-label" for="featured">Featured</label>
                                                <input type="hidden" name="featured" id="featuredHidden" value="no">
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="highestrated" name="highestrated" value="yes">
                                                <label class="form-check-label" for="highestrated">Highest Rated</label>
                                                <input type="hidden" name="highestrated" id="highestratedHidden" value="no">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                        <i class="bx bx-chevron-left"></i> Back
                    </button>
                    <div>
                        <button type="button" class="btn btn-primary" id="nextBtn">
                            Next <i class="bx bx-chevron-right"></i>
                        </button>
                        <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                            <i class="bx bx-save"></i> Create Course
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.step-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    opacity: 0.5;
    transition: opacity 0.3s;
}

.step-indicator.active {
    opacity: 1;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 5px;
}

.step-text {
    font-size: 12px;
    text-align: center;
    max-width: 80px;
}

.step-content {
    display: none;
}

.step-content.active {
    display: block;
}

.goal-item {
    position: relative;
}

.goal-item:first-child .remove-goal {
    display: none !important;
}

.card {
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px 15px 0 0 !important;
    border: none;
}

.card-header h6 {
    margin: 0;
    font-weight: 600;
}

.space-y-2 > * + * {
    margin-top: 0.5rem;
}
</style>
@endsection

@push('scripts')
<script>
let currentStep = 1;
const totalSteps = 3;

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    updateProgress();
    updateNavigation();
    
    // Category change handler
    document.getElementById('category_id').addEventListener('change', function() {
        const categoryId = this.value;
        const subcategorySelect = document.getElementById('subcategory_id');
        
        if (categoryId) {
            fetch(`/instructor/get-subcategories/${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    subcategorySelect.innerHTML = '<option value="" disabled selected>Select a subcategory</option>';
                    data.forEach(item => {
                        subcategorySelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });
                    subcategorySelect.disabled = false;
                });
        } else {
            subcategorySelect.innerHTML = '<option value="" disabled selected>Select a subcategory</option>';
            subcategorySelect.disabled = true;
        }
    });

    // Image preview
    document.getElementById('image').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Video preview
    document.getElementById('video_url').addEventListener('input', function() {
        const videoUrl = this.value;
        const videoPreview = document.getElementById('videoPreview');
        
        if (videoUrl) {
            const videoId = extractYouTubeID(videoUrl);
            if (videoId) {
                videoPreview.src = `https://www.youtube.com/embed/${videoId}`;
                videoPreview.style.display = 'block';
            }
        } else {
            videoPreview.style.display = 'none';
        }
    });

    // Goals management
    document.getElementById('addGoal').addEventListener('click', function() {
        const container = document.getElementById('goalsContainer');
        const newGoal = document.createElement('div');
        newGoal.className = 'goal-item d-flex align-items-center gap-2 mb-2';
        newGoal.innerHTML = `
            <input type="text" class="form-control" name="course_goals[]" placeholder="Enter course goal" />
            <button type="button" class="btn btn-sm btn-outline-danger remove-goal">
                <i class="bx bx-trash"></i>
            </button>
        `;
        container.appendChild(newGoal);
        updateGoalButtons();
    });

    // Toggle switches
    document.querySelectorAll('.form-check-input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const hiddenInput = document.getElementById(this.name + 'Hidden');
            if (hiddenInput) {
                hiddenInput.value = this.checked ? 'yes' : 'no';
            }
        });
    });
});

// Navigation functions
document.getElementById('nextBtn').addEventListener('click', function() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            currentStep++;
            updateProgress();
            updateNavigation();
        }
    }
});

document.getElementById('prevBtn').addEventListener('click', function() {
    if (currentStep > 1) {
        currentStep--;
        updateProgress();
        updateNavigation();
    }
});

function updateProgress() {
    const progress = (currentStep / totalSteps) * 100;
    document.getElementById('progressBar').style.width = progress + '%';
    
    // Update step indicators
    document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
        if (index + 1 <= currentStep) {
            indicator.classList.add('active');
        } else {
            indicator.classList.remove('active');
        }
    });
}

function updateNavigation() {
    // Show/hide step content
    document.querySelectorAll('.step-content').forEach((content, index) => {
        if (index + 1 === currentStep) {
            content.classList.add('active');
        } else {
            content.classList.remove('active');
        }
    });

    // Update buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    prevBtn.style.display = currentStep > 1 ? 'block' : 'none';
    
    if (currentStep === totalSteps) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'block';
        generatePreview();
    } else {
        nextBtn.style.display = 'block';
        submitBtn.style.display = 'none';
    }
}

function validateCurrentStep() {
    const currentStepElement = document.getElementById(`step${currentStep}`);
    const requiredFields = currentStepElement.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    return isValid;
}

function generatePreview() {
    const preview = document.getElementById('coursePreview');
    const courseName = document.getElementById('course_name').value || 'Course Name';
    const courseTitle = document.getElementById('course_title').value || 'Course Title';
    const category = document.getElementById('category_id').selectedOptions[0]?.text || 'Category';
    const level = document.getElementById('level').selectedOptions[0]?.text || 'Level';
    const price = document.getElementById('selling_price').value || '0';
    const duration = document.getElementById('duration').value || '0';
    const language = document.getElementById('language').selectedOptions[0]?.text || 'Language';

    preview.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <img src="" id="previewImage" class="img-fluid rounded" style="max-width: 100%;" />
            </div>
            <div class="col-md-8">
                <h5>${courseName}</h5>
                <p class="text-muted">${courseTitle}</p>
                <div class="d-flex gap-3 mb-2">
                    <span class="badge bg-primary">${category}</span>
                    <span class="badge bg-info">${level}</span>
                    <span class="badge bg-secondary">${language}</span>
                </div>
                <div class="d-flex gap-3">
                    <span><i class="bx bx-time"></i> ${duration}h</span>
                    <span><i class="bx bx-dollar"></i> $${price}</span>
                </div>
            </div>
        </div>
    `;

    // Show image preview if available
    const imagePreview = document.getElementById('imagePreview');
    if (imagePreview.src) {
        document.getElementById('previewImage').src = imagePreview.src;
    }
}

function updateGoalButtons() {
    const goals = document.querySelectorAll('.goal-item');
    goals.forEach((goal, index) => {
        const removeBtn = goal.querySelector('.remove-goal');
        if (goals.length === 1) {
            removeBtn.style.display = 'none';
        } else {
            removeBtn.style.display = 'block';
        }
    });
}

// Remove goal event delegation
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-goal')) {
        e.target.closest('.goal-item').remove();
        updateGoalButtons();
    }
});

// YouTube ID extraction
function extractYouTubeID(url) {
    const regex = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
    const match = url.match(regex);
    return match ? match[1] : null;
}

// CKEditor initialization
$(document).ready(function() {
    if (typeof CKEDITOR !== 'undefined') {
        CKEDITOR.replace('description', { height: 200 });
        CKEDITOR.replace('requirements', { height: 150 });
        CKEDITOR.replace('objectives', { height: 150 });
    }
});
</script>
@endpush
