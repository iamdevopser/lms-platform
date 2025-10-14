<div class="col-lg-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column align-items-center text-center">
                <img id="photoPreview" src="{{  auth()->user()->photo ? asset(auth()->user()->photo) :  asset('backend/assets/images/avatars/avatar-2.png')}}" alt="Admin" class="rounded-circle p-1 bg-primary" width="110" height="110">
                <div class="mt-3">
                    <h4>{{auth()->user()->name}}</h4>

                    <p class="text-muted font-size-sm">{{auth()->user()->email}}</p>
                    <button class="btn btn-primary">Follow</button>
                    <button class="btn btn-outline-primary">Message</button>
                </div>
            </div>
            <hr class="my-4" />
            <!-- Instructor Sidebar Menu -->
            <ul class="list-group list-group-flush text-start">
                <li class="list-group-item fw-bold"><span class="me-2">🧑‍🏫</span> Instructor Dashboard
                    <ul class="list-unstyled ms-4 mt-2">
                        <li><a href="#">Dashboard Home</a></li>
                        <li><a href="#">Notifications</a></li>
                        <li><a href="#">Calendar</a></li>
                        <li><a href="#">Messages</a></li>
                    </ul>
                </li>
                <li class="list-group-item fw-bold"><span class="me-2">📚</span> Course Management
                    <ul class="list-unstyled ms-4 mt-2">
                        <li><a href="#">All My Courses</a></li>
                        <li><a href="#">Create New Course</a></li>
                        <li><a href="#">Draft Courses</a></li>
                        <li><a href="#">Published Courses</a></li>
                        <li><a href="#">Pricing & Coupons</a></li>
                        <li><a href="#">Preview Settings</a></li>
                        <li><a href="#">Course Feedback</a></li>
                    </ul>
                </li>
                <li class="list-group-item fw-bold"><span class="me-2">👩‍🎓</span> Student Management
                    <ul class="list-unstyled ms-4 mt-2">
                        <li><a href="#">Registered Students</a></li>
                        <li><a href="#">Student Progress</a></li>
                        <li><a href="#">Certificate Status</a></li>
                    </ul>
                </li>
                <li class="list-group-item fw-bold"><span class="me-2">🎥</span> Live Session
                    <ul class="list-unstyled ms-4 mt-2">
                        <li><a href="#">Schedule Live Session</a></li>
                    </ul>
                </li>
                <li class="list-group-item fw-bold"><span class="me-2">📈</span> Analytics & Reports
                    <ul class="list-unstyled ms-4 mt-2">
                        <li><a href="#">Course Performance</a></li>
                        <li><a href="#">Earnings Reports</a></li>
                        <li><a href="#">Visits & Registrations</a></li>
                        <li><a href="#">Student Engagement</a></li>
                    </ul>
                </li>
                <li class="list-group-item fw-bold"><span class="me-2">💰</span> My Earnings
                    <ul class="list-unstyled ms-4 mt-2">
                        <li><a href="#">Earnings</a></li>
                        <li><a href="#">Payment Settings</a></li>
                        <li><a href="#">My Coupons / Promotions</a></li>
                    </ul>
                </li>
                <li class="list-group-item fw-bold"><span class="me-2">⚙️</span> Settings
                    <ul class="list-unstyled ms-4 mt-2">
                        <li><a href="#">Profile Settings</a></li>
                        <li><a href="#">Account Settings</a></li>
                        <li><a href="#">Language / Theme</a></li>
                        <li><a href="#">Teacher Documents</a></li>
                    </ul>
                </li>
                <li class="list-group-item fw-bold"><span class="me-2">🛠️</span> Help & Support
                    <ul class="list-unstyled ms-4 mt-2">
                        <li><a href="#">FAQ / Help Center</a></li>
                        <li><a href="#">Create Support Ticket</a></li>
                        <li><a href="#">Community Forum</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
