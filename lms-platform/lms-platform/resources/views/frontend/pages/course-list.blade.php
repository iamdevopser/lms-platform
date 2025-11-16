@extends('frontend.master')

@section('title', 'All Courses | OnliNote')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar Filter Panel -->
        <div class="col-lg-3 mb-4">
            <div class="filter-sidebar p-4 bg-white rounded shadow-sm sticky-top">
                <h4 class="mb-4" style="font-weight:700;">Filter Courses</h4>
                <div id="filterPanel">
                    <!-- Course Type -->
                    <div class="filter-group mb-3">
                        <div class="filter-header" onclick="toggleFilter('type')">
                            <span>Course Type</span>
                        </div>
                        <div class="filter-options" id="filter-type-options" style="display:none;">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="courseTypeFilter" id="typeAll" value="" checked>
                                <label class="form-check-label" for="typeAll">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="courseTypeFilter" id="typeFree" value="free">
                                <label class="form-check-label" for="typeFree">Free Courses</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="courseTypeFilter" id="typePaid" value="paid">
                                <label class="form-check-label" for="typePaid">Paid Courses</label>
                            </div>
                        </div>
                    </div>
                    <!-- Subject (Category) -->
                    <div class="filter-group mb-3">
                        <div class="filter-header" onclick="toggleFilter('subject')">
                            <span>Subject</span>
                        </div>
                        <div class="filter-options" id="filter-subject-options" style="display:none;">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="categoryFilter" id="subjectAll" value="" checked>
                                <label class="form-check-label" for="subjectAll">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="categoryFilter" id="subjectMath" value="Mathematics">
                                <label class="form-check-label" for="subjectMath">Mathematics</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="categoryFilter" id="subjectScience" value="Science">
                                <label class="form-check-label" for="subjectScience">Science</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="categoryFilter" id="subjectLanguage" value="Language">
                                <label class="form-check-label" for="subjectLanguage">Language</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="categoryFilter" id="subjectProgramming" value="Programming">
                                <label class="form-check-label" for="subjectProgramming">Programming</label>
                            </div>
                        </div>
                    </div>
                    <!-- Level -->
                    <div class="filter-group mb-3">
                        <div class="filter-header" onclick="toggleFilter('level')">
                            <span>Level</span>
                        </div>
                        <div class="filter-options" id="filter-level-options" style="display:none;">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="levelFilter" id="levelAll" value="" checked>
                                <label class="form-check-label" for="levelAll">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="levelFilter" id="levelBeginner" value="Beginner">
                                <label class="form-check-label" for="levelBeginner">Beginner</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="levelFilter" id="levelIntermediate" value="Intermediate">
                                <label class="form-check-label" for="levelIntermediate">Intermediate</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="levelFilter" id="levelAdvanced" value="Advanced">
                                <label class="form-check-label" for="levelAdvanced">Advanced</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="levelFilter" id="levelK5" value="K-5">
                                <label class="form-check-label" for="levelK5">K-5</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="levelFilter" id="level68" value="6-8">
                                <label class="form-check-label" for="level68">6-8</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="levelFilter" id="level912" value="9-12">
                                <label class="form-check-label" for="level912">9-12</label>
                            </div>
                        </div>
                    </div>
                    <!-- Teacher -->
                    <div class="filter-group mb-3">
                        <div class="filter-header" onclick="toggleFilter('instructor')">
                            <span>Teacher</span>
                        </div>
                        <div class="filter-options" id="filter-instructor-options" style="display:none;">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="instructorFilter" id="instructorAll" value="" checked>
                                <label class="form-check-label" for="instructorAll">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="instructorFilter" id="instructorAyse" value="Prof. Ayşe Demir">
                                <label class="form-check-label" for="instructorAyse">Prof. Ayşe Demir</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="instructorFilter" id="instructorMehmet" value="Dr. Mehmet Kaya">
                                <label class="form-check-label" for="instructorMehmet">Dr. Mehmet Kaya</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="instructorFilter" id="instructorSarah" value="Sarah Johnson">
                                <label class="form-check-label" for="instructorSarah">Sarah Johnson</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="instructorFilter" id="instructorTarik" value="Prof. Tarik Yılmaz">
                                <label class="form-check-label" for="instructorTarik">Prof. Tarik Yılmaz</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="instructorFilter" id="instructorAhmet" value="Ahmet Özkan">
                                <label class="form-check-label" for="instructorAhmet">Ahmet Özkan</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="instructorFilter" id="instructorZeynep" value="Dr. Zeynep Arslan">
                                <label class="form-check-label" for="instructorZeynep">Dr. Zeynep Arslan</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="instructorFilter" id="instructorMaria" value="Maria Rodriguez">
                                <label class="form-check-label" for="instructorMaria">Maria Rodriguez</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="instructorFilter" id="instructorAlex" value="Alex Chen">
                                <label class="form-check-label" for="instructorAlex">Alex Chen</label>
                            </div>
                        </div>
                    </div>
                    <!-- Language -->
                    <div class="filter-group mb-3">
                        <div class="filter-header" onclick="toggleFilter('language')">
                            <span>Language</span>
                        </div>
                        <div class="filter-options" id="filter-language-options" style="display:none;">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="languageFilter" id="langAll" value="" checked>
                                <label class="form-check-label" for="langAll">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="languageFilter" id="langEnglish" value="English">
                                <label class="form-check-label" for="langEnglish">English</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="languageFilter" id="langTurkish" value="Turkish">
                                <label class="form-check-label" for="langTurkish">Turkish</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="languageFilter" id="langSpanish" value="Spanish">
                                <label class="form-check-label" for="langSpanish">Spanish</label>
                            </div>
                        </div>
                    </div>
                    <!-- Certificate -->
                    <div class="filter-group mb-3">
                        <div class="filter-header" onclick="toggleFilter('certificate')">
                            <span>Certificate</span>
                        </div>
                        <div class="filter-options" id="filter-certificate-options" style="display:none;">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="certificateFilter" id="certAll" value="" checked>
                                <label class="form-check-label" for="certAll">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="certificateFilter" id="certYes" value="yes">
                                <label class="form-check-label" for="certYes">With Certificate</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="certificateFilter" id="certNo" value="no">
                                <label class="form-check-label" for="certNo">Without Certificate</label>
                            </div>
                        </div>
                    </div>
                    <!-- Duration -->
                    <div class="filter-group mb-3">
                        <div class="filter-header" onclick="toggleFilter('duration')">
                            <span>Duration</span>
                        </div>
                        <div class="filter-options" id="filter-duration-options" style="display:none;">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="durationFilter" id="durationAll" value="" checked>
                                <label class="form-check-label" for="durationAll">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="durationFilter" id="durationShort" value="short">
                                <label class="form-check-label" for="durationShort">0-4 weeks</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="durationFilter" id="durationMedium" value="medium">
                                <label class="form-check-label" for="durationMedium">5-10 weeks</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="durationFilter" id="durationLong" value="long">
                                <label class="form-check-label" for="durationLong">10+ weeks</label>
                            </div>
                        </div>
                    </div>
                    <!-- Price Range -->
                    <div class="filter-group mb-3">
                        <div class="filter-header" onclick="toggleFilter('price')">
                            <span>Price Range</span>
                        </div>
                        <div class="filter-options" id="filter-price-options" style="display:none;">
                            <input type="range" class="form-control-range mt-2" id="priceRangeFilter" min="0" max="100" step="1" value="100">
                            <label for="priceRangeFilter" class="d-block mt-1">Max Price: <span id="priceRangeValue">$100</span></label>
                        </div>
                    </div>
                    <!-- Start Date -->
                    <div class="filter-group mb-3">
                        <div class="filter-header" onclick="toggleFilter('startdate')">
                            <span>Start Date</span>
                        </div>
                        <div class="filter-options" id="filter-startdate-options" style="display:none;">
                            <input type="date" class="form-control mt-2" id="startDateFilter">
                        </div>
                    </div>
                    <!-- Sort -->
                    <div class="filter-group mb-3">
                        <div class="filter-header" onclick="toggleFilter('sort')">
                            <span>Sort By</span>
                        </div>
                        <div class="filter-options" id="filter-sort-options" style="display:none;">
                            <select class="form-control mt-2" id="sortFilter">
                                <option value="">Default</option>
                                <option value="newest">Newest</option>
                                <option value="popular">Most Popular</option>
                                <option value="price-asc">Price: Low to High</option>
                                <option value="price-desc">Price: High to Low</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary w-100 mt-4" id="applyFiltersBtn">Show Results</button>
            </div>
        </div>
        <!-- Course Grid -->
        <div class="col-lg-9">
            <div class="row" id="courseGrid">
                @php
                    $allCourses = [
                        [
                            'title' => 'Introduction to Mathematics',
                            'instructor' => 'Prof. Ayşe Demir',
                            'duration' => '8 weeks',
                            'students' => '1,234',
                            'image' => 'https://placehold.co/300x200/27ae60/ffffff?text=Math',
                            'level' => 'Beginner',
                            'category' => 'Mathematics',
                            'type' => 'free',
                            'price' => 'Free'
                        ],
                        [
                            'title' => 'Basic Science Concepts',
                            'instructor' => 'Dr. Mehmet Kaya',
                            'duration' => '6 weeks',
                            'students' => '856',
                            'image' => 'https://placehold.co/300x200/3498db/ffffff?text=Science',
                            'level' => 'Beginner',
                            'category' => 'Science',
                            'type' => 'free',
                            'price' => 'Free'
                        ],
                        [
                            'title' => 'English Grammar Fundamentals',
                            'instructor' => 'Sarah Johnson',
                            'duration' => '10 weeks',
                            'students' => '2,156',
                            'image' => 'https://placehold.co/300x200/e74c3c/ffffff?text=English',
                            'level' => 'Beginner',
                            'category' => 'Language',
                            'type' => 'free',
                            'price' => 'Free'
                        ],
                        [
                            'title' => 'Advanced Mathematics',
                            'instructor' => 'Prof. Tarik Yılmaz',
                            'duration' => '12 weeks',
                            'students' => '567',
                            'image' => 'https://placehold.co/300x200/9b59b6/ffffff?text=Advanced+Math',
                            'level' => 'Advanced',
                            'category' => 'Mathematics',
                            'type' => 'paid',
                            'price' => '$49'
                        ],
                        [
                            'title' => 'Web Development Bootcamp',
                            'instructor' => 'Ahmet Özkan',
                            'duration' => '16 weeks',
                            'students' => '892',
                            'image' => 'https://placehold.co/300x200/f39c12/ffffff?text=Web+Dev',
                            'level' => 'Intermediate',
                            'category' => 'Programming',
                            'type' => 'paid',
                            'price' => '$79'
                        ],
                        [
                            'title' => 'Data Science for Kids',
                            'instructor' => 'Dr. Zeynep Arslan',
                            'duration' => '14 weeks',
                            'students' => '423',
                            'image' => 'https://placehold.co/300x200/1abc9c/ffffff?text=Data+Science',
                            'level' => 'Intermediate',
                            'category' => 'Science',
                            'type' => 'paid',
                            'price' => '$59'
                        ],
                        [
                            'title' => 'Spanish for Beginners',
                            'instructor' => 'Maria Rodriguez',
                            'duration' => '8 weeks',
                            'students' => '1,089',
                            'image' => 'https://placehold.co/300x200/e67e22/ffffff?text=Spanish',
                            'level' => 'Beginner',
                            'category' => 'Language',
                            'type' => 'free',
                            'price' => 'Free'
                        ],
                        [
                            'title' => 'Python Programming',
                            'instructor' => 'Alex Chen',
                            'duration' => '12 weeks',
                            'students' => '756',
                            'image' => 'https://placehold.co/300x200/34495e/ffffff?text=Python',
                            'level' => 'Intermediate',
                            'category' => 'Programming',
                            'type' => 'paid',
                            'price' => '$69'
                        ]
                    ];
                @endphp
                
                @foreach($allCourses as $course)
                    <div class="col-lg-4 col-md-6 mb-4 course-item" 
                         data-type="{{ $course['type'] }}" 
                         data-category="{{ $course['category'] }}" 
                         data-level="{{ $course['level'] }}"
                         data-instructor="{{ $course['instructor'] }}"
                         data-language="{{ $course['language'] ?? 'English' }}"
                         data-certificate="{{ $course['certificate'] ?? 'no' }}"
                         data-duration="{{ $course['duration_type'] ?? 'medium' }}"
                         data-student-count="{{ $course['students'] > 1000 ? 'popular' : 'regular' }}"
                         data-tag="{{ $course['tag'] ?? '' }}"
                         data-price="{{ is_numeric($course['price']) ? $course['price'] : ($course['price'] == 'Free' ? 0 : intval(str_replace(['$', 'TL'], '', $course['price']))) }}"
                         data-start-date="{{ $course['start_date'] ?? '' }}">
                        <div class="course-card h-100">
                            <div class="course-image">
                                <img src="{{ $course['image'] }}" class="img-fluid" alt="{{ $course['title'] }}">
                                <div class="course-overlay">
                                    @if($course['type'] == 'free')
                                        <span class="badge bg-success">Free</span>
                                    @else
                                        <span class="badge bg-danger">{{ $course['price'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="course-body">
                                <div class="course-category mb-2">
                                    <span class="badge bg-light text-dark">{{ $course['category'] }}</span>
                                    <span class="badge bg-info">{{ $course['level'] }}</span>
                                </div>
                                <h5 class="course-title">{{ $course['title'] }}</h5>
                                <p class="course-instructor text-muted">
                                    <i class="la la-user mr-1"></i>{{ $course['instructor'] }}
                                </p>
                                <div class="course-meta">
                                    <span class="meta-item">
                                        <i class="la la-clock-o mr-1"></i>{{ $course['duration'] }}
                                    </span>
                                    <span class="meta-item">
                                        <i class="la la-users mr-1"></i>{{ $course['students'] }} students
                                    </span>
                                </div>
                                <div class="course-action mt-3">
                                    @if($course['type'] == 'free')
                                        <a href="#" class="btn btn-outline-success btn-sm">Start Learning</a>
                                    @else
                                        <a href="#" class="btn btn-outline-danger btn-sm">Enroll Now</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- No Results Message -->
            <div id="noResults" class="text-center py-5" style="display: none;">
                <i class="la la-search la-3x text-muted mb-3"></i>
                <h4 class="text-muted">No courses found</h4>
                <p class="text-muted">Try adjusting your filters to find more courses.</p>
            </div>
        </div>
    </div>
</div>

<style>
.filter-sidebar {
    min-width: 220px;
    max-width: 100%;
    background: rgba(255,255,255,0.97);
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.10);
    backdrop-filter: blur(8px);
}
.filter-toggle {
    font-size: 1.08rem;
    font-weight: 600;
    color: #2c3e50;
    background: none;
    border: none;
    outline: none;
    padding: 0;
    margin-bottom: 0.5rem;
}
.filter-toggle:focus {
    text-decoration: none;
    outline: none;
}
.filter-group .collapse {
    margin-bottom: 0.5rem;
}
@media (max-width: 991.98px) {
    .filter-sidebar {
        margin-bottom: 2rem;
        position: static;
    }
}
</style>

<script>
function toggleFilter(key) {
    const el = document.getElementById('filter-' + key + '-options');
    if (el.style.display === 'none' || el.style.display === '') {
        document.querySelectorAll('.filter-options').forEach(opt => opt.style.display = 'none');
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Filter elements
    const courseTypeFilter = document.getElementById('courseTypeFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const levelFilter = document.getElementById('levelFilter');
    const instructorFilter = document.getElementById('instructorFilter');
    const languageFilter = document.getElementById('languageFilter');
    const certificateFilter = document.getElementById('certificateFilter');
    const durationFilter = document.getElementById('durationFilter');
    const studentCountFilter = document.getElementById('studentCountFilter');
    const tagFilter = document.getElementById('tagFilter');
    const priceRangeFilter = document.getElementById('priceRangeFilter');
    const startDateFilter = document.getElementById('startDateFilter');
    const sortFilter = document.getElementById('sortFilter');
    const courseItems = document.querySelectorAll('.course-item');
    const noResults = document.getElementById('noResults');
    const priceRangeValue = document.getElementById('priceRangeValue');
    const applyFiltersBtn = document.getElementById('applyFiltersBtn');

    // Accordion toggle
    document.querySelectorAll('.filter-toggle').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('data-target'));
            if (target.classList.contains('show')) {
                target.classList.remove('show');
            } else {
                document.querySelectorAll('.filter-group .collapse').forEach(c => c.classList.remove('show'));
                target.classList.add('show');
            }
        });
    });

    // Filtres only on button click
    applyFiltersBtn.addEventListener('click', filterCourses);
    priceRangeFilter.addEventListener('input', function() {
        priceRangeValue.textContent = '$' + priceRangeFilter.value;
    });

    function filterCourses() {
        const selectedType = courseTypeFilter.value;
        const selectedCategory = categoryFilter.value;
        const selectedLevel = levelFilter.value;
        const selectedInstructor = instructorFilter.value;
        const selectedLanguage = languageFilter.value;
        const selectedCertificate = certificateFilter.value;
        const selectedDuration = durationFilter.value;
        const selectedStudentCount = studentCountFilter.value;
        const selectedTag = tagFilter.value;
        const selectedPrice = parseInt(priceRangeFilter.value);
        const selectedStartDate = startDateFilter.value;
        const selectedSort = sortFilter.value;

        let visibleCourses = [];

        courseItems.forEach(item => {
            const type = item.getAttribute('data-type');
            const category = item.getAttribute('data-category');
            const level = item.getAttribute('data-level');
            const instructor = item.getAttribute('data-instructor');
            const language = item.getAttribute('data-language');
            const certificate = item.getAttribute('data-certificate');
            const duration = item.getAttribute('data-duration');
            const studentCount = item.getAttribute('data-student-count');
            const tag = item.getAttribute('data-tag');
            const price = parseInt(item.getAttribute('data-price'));
            const startDate = item.getAttribute('data-start-date');

            let show = true;
            if (selectedType && type !== selectedType) show = false;
            if (selectedCategory && category !== selectedCategory) show = false;
            if (selectedLevel && level !== selectedLevel) show = false;
            if (selectedInstructor && instructor !== selectedInstructor) show = false;
            if (selectedLanguage && language !== selectedLanguage) show = false;
            if (selectedCertificate && certificate !== selectedCertificate) show = false;
            if (selectedDuration && duration !== selectedDuration) show = false;
            if (selectedStudentCount && studentCount !== selectedStudentCount) show = false;
            if (selectedTag && tag !== selectedTag) show = false;
            if (price > selectedPrice) show = false;
            if (selectedStartDate && startDate && startDate < selectedStartDate) show = false;

            if (show) {
                item.classList.remove('hidden');
                visibleCourses.push(item);
            } else {
                item.classList.add('hidden');
            }
        });

        // Sıralama
        if (selectedSort && visibleCourses.length > 1) {
            let sorted = Array.from(visibleCourses);
            if (selectedSort === 'newest') {
                sorted.sort((a, b) => {
                    const dateA = a.getAttribute('data-start-date') || '';
                    const dateB = b.getAttribute('data-start-date') || '';
                    return dateB.localeCompare(dateA);
                });
            } else if (selectedSort === 'popular') {
                sorted.sort((a, b) => {
                    return parseInt(b.getAttribute('data-student-count') === 'popular' ? 1 : 0) - parseInt(a.getAttribute('data-student-count') === 'popular' ? 1 : 0);
                });
            } else if (selectedSort === 'price-asc') {
                sorted.sort((a, b) => parseInt(a.getAttribute('data-price')) - parseInt(b.getAttribute('data-price')));
            } else if (selectedSort === 'price-desc') {
                sorted.sort((a, b) => parseInt(b.getAttribute('data-price')) - parseInt(a.getAttribute('data-price')));
            }
            // DOM'da sıralama uygula
            const grid = document.getElementById('courseGrid');
            sorted.forEach(item => grid.appendChild(item));
        }

        // Show/hide no results message
        if (visibleCourses.length === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }
});
</script>
@endsection 