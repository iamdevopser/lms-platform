@extends('frontend.master')
@section('content')
    <div class="container py-5">
        <h1>Become a Teacher</h1>
        <p>Fill out the form below to apply as a teacher on OnliNote.</p>
        @if(session('success'))
            <div class="alert alert-success" id="feedback-message" style="font-size:1.1rem; border-radius:8px; margin-bottom:24px;">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = '/';
                }, 5000);
            </script>
        @endif
        @if(session('error'))
            <div class="alert alert-danger" id="feedback-message" style="font-size:1.1rem; border-radius:8px; margin-bottom:24px;">
                {{ session('error') }}
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = '/';
                }, 5000);
            </script>
        @endif
        <form method="POST" action="{{ route('frontend.becomeInstructor.store') }}" enctype="multipart/form-data" class="row g-3 mt-4">
            @csrf
            <div class="col-md-6">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="col-md-6">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="col-md-6">
                <label for="expertise" class="form-label">Expertise / Subject Area</label>
                <input type="text" class="form-control" id="expertise" name="expertise" required>
            </div>
            <div class="col-12">
                <label for="bio" class="form-label">Short Bio</label>
                <textarea class="form-control" id="bio" name="bio" rows="3" required></textarea>
            </div>
            <div class="col-12">
                <label for="experience" class="form-label">Teaching Experience</label>
                <textarea class="form-control" id="experience" name="experience" rows="3" required></textarea>
            </div>
            <div class="col-12">
                <label for="topics-dropdown" class="form-label">Topics You Want to Teach</label>
                <div id="selected-topics" class="mb-2"></div>
                <div class="custom-dropdown mb-2" style="position:relative;">
                    <button type="button" class="btn btn-outline-secondary w-100" id="topics-dropdown-btn">Select Topics</button>
                    <div id="topics-options" class="custom-dropdown-menu" style="display:none; position:absolute; left:0; right:0; z-index:10; background:#fff; border:1px solid #ddd; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.08); max-height:250px; overflow-y:auto;">
                        <div class="p-2">
                            <div class="topic-option py-1 px-2" data-value="Math" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">Math</div>
                            <div class="topic-option py-1 px-2" data-value="Physics" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">Physics</div>
                            <div class="topic-option py-1 px-2" data-value="Chemistry" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">Chemistry</div>
                            <div class="topic-option py-1 px-2" data-value="Biology" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">Biology</div>
                            <div class="topic-option py-1 px-2" data-value="History" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">History</div>
                            <div class="topic-option py-1 px-2" data-value="Geography" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">Geography</div>
                            <div class="topic-option py-1 px-2" data-value="Literature" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">Literature</div>
                            <div class="topic-option py-1 px-2" data-value="English" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">English</div>
                            <div class="topic-option py-1 px-2" data-value="Computer Science" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">Computer Science</div>
                            <div class="topic-option py-1 px-2" data-value="Art" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">Art</div>
                            <div class="topic-option py-1 px-2" data-value="Romanian" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">Romanian</div>
                            <div class="topic-option py-1 px-2" data-value="Russian" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">Russian</div>
                            <div class="topic-option py-1 px-2" data-value="Turkish" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">Turkish</div>
                            <div class="topic-option py-1 px-2" data-value="German" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">German</div>
                            <div class="topic-option py-1 px-2" data-value="French" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">French</div>
                            <div class="topic-option py-1 px-2" data-value="Spanish" style="cursor:pointer; border-radius:16px; display:inline-block; margin:2px 6px 2px 0; background:#f1f3f6;">Spanish</div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="topics" id="topics-hidden" required>
                <small class="text-muted">You can select up to 10 topics.</small>
            </div>
            <div class="col-12">
                <label for="cv" class="form-label">Upload CV (PDF, DOC, DOCX)</label>
                <input class="form-control" type="file" id="cv" name="cv" accept=".pdf,.doc,.docx">
            </div>
            <div class="col-12">
                <label for="video" class="form-label">Link to a Presentation/Teaching Video (optional)</label>
                <input type="url" class="form-control" id="video" name="video">
            </div>
            <div class="col-12 form-check mb-3">
                <input class="form-check-input" type="checkbox" id="kvkk" name="kvkk" required>
                <label class="form-check-label" for="kvkk">
                    I have read and accept the <a href="#">terms and privacy policy</a>.
                </label>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Submit Application</button>
            </div>
        </form>
    </div>
@endsection
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" />
@endpush
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownBtn = document.getElementById('topics-dropdown-btn');
        const dropdownMenu = document.getElementById('topics-options');
        const options = dropdownMenu.querySelectorAll('.topic-option');
        const selectedContainer = document.getElementById('selected-topics');
        const hiddenInput = document.getElementById('topics-hidden');
        let selectedTopics = [];
        // Dropdown aç/kapa
        dropdownBtn.addEventListener('click', function(e) {
            dropdownMenu.style.display = dropdownMenu.style.display === 'none' ? 'block' : 'none';
        });
        // Dışarı tıklayınca kapat
        document.addEventListener('click', function(e) {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.style.display = 'none';
            }
        });
        // Seçim işlemi
        options.forEach(function(option) {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                const value = this.getAttribute('data-value');
                if (!selectedTopics.includes(value) && selectedTopics.length < 10) {
                    selectedTopics.push(value);
                    renderSelected();
                }
            });
        });
        // Badge silme
        selectedContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-topic')) {
                const idx = parseInt(e.target.getAttribute('data-idx'));
                selectedTopics.splice(idx, 1);
                renderSelected();
            }
        });
        function renderSelected() {
            selectedContainer.innerHTML = '';
            selectedTopics.forEach(function(topic, idx) {
                const badge = document.createElement('span');
                badge.className = 'badge rounded-pill bg-primary text-white me-2 mb-1';
                badge.style.fontSize = '1rem';
                badge.innerHTML = topic + ' <span class="remove-topic" style="cursor:pointer; margin-left:4px; color:#fff;" data-idx="'+idx+'">&times;</span>';
                selectedContainer.appendChild(badge);
            });
            hiddenInput.value = selectedTopics.join(',');
        }
    });
</script>
@endpush 