@extends('layouts.app')

@section('title', 'Manage Courses — KoreSearch')

@section('content')

<div class="dashboard-layout">

    @include('partials.admin-sidebar')

    <div class="dashboard-main">

        <div class="dash-header">
            <h1 class="dash-title">Manage Courses</h1>
            <p class="dash-subtitle">View and manage all courses on the platform</p>
        </div>

        <div class="dash-section">
            <div class="section-header">
                <h2 class="dash-section-title">All Courses ({{ $courses->total() }})</h2>
                <button class="btn btn-primary" id="createCourseBtn">+ Create Course</button>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>#</th>
                            <th>Title</th>
                            <th>Instructor</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Enrolled</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $course)
                            <tr>
                                <td>
                                    @if($course->thumbnail)
                                        <img src="/storage/{{ $course->thumbnail }}" alt="" class="course-thumb-sm">
                                    @else
                                        <div class="course-thumb-sm course-thumb-placeholder"></div>
                                    @endif
                                </td>
                                <td>{{ $course->id }}</td>
                                <td>
                                    <a href="{{ route('courses.show', $course->slug) }}">{{ $course->title }}</a>
                                </td>
                                <td>{{ $course->instructor->name ?? '—' }}</td>
                                <td>{{ $course->category }}</td>
                                <td>{{ $course->isFree() ? 'Free' : '৳'.number_format($course->price) }}</td>
                                <td>{{ $course->enrolled_count }}</td>
                                <td>
                                    <span class="status-badge {{ $course->is_published ? 'status-completed' : 'status-pending' }}">
                                        {{ $course->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-action btn-action-edit" data-course="{{ $course->toJson() }}" title="Edit Course">✏️</button>
                                        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Delete course?')" style="display:inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action btn-action-delete" title="Delete Course">🗑️</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $courses->links('vendor.pagination.custom') }}
            </div>
        </div>

    </div>
</div>

<div class="modal-overlay hidden" id="courseModal">
    <div class="modal-content modal-course">
        <button class="modal-close" id="courseModalClose">&times;</button>
        <div class="modal-course-header">
            <span class="modal-course-icon" id="courseModalIcon">📚</span>
            <h2 class="modal-title" id="courseModalTitle">Create Course</h2>
        </div>
        <form method="POST" action="{{ route('admin.courses.store') }}" id="courseForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="courseFormMethod" value="POST">

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="courseTitle">Title</label>
                    <input type="text" name="title" id="courseTitle" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="courseCategory">Category</label>
                    <input type="text" name="category" id="courseCategory" class="form-input" required>
                </div>
            </div>

            <button type="button" class="btn-ai" id="generateWithAI">✨ Generate with AI</button>

            <div class="form-group">
                <label class="form-label" for="courseDescription">Description</label>
                <textarea name="description" id="courseDescription" class="form-input" rows="3" required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="coursePrice">Price (৳)</label>
                    <input type="number" step="0.01" min="0" name="price" id="coursePrice" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="courseInstructor">Instructor</label>
                    <select name="instructor_id" id="courseInstructor" class="form-select" required>
                        <option value="">Select Instructor</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="courseLevel">Level</label>
                    <input type="text" name="level" id="courseLevel" class="form-input" placeholder="e.g. Beginner">
                </div>
                <div class="form-group">
                    <label class="form-label" for="courseDuration">Duration</label>
                    <input type="text" name="duration" id="courseDuration" class="form-input" placeholder="e.g. 10 hours">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="courseThumbnail">Thumbnail</label>
                    <input type="file" name="thumbnail" id="courseThumbnail" class="form-input" accept=".png,.jpg,.jpeg">
                    <div id="courseThumbnailPreview" class="thumbnail-preview" style="margin-top:8px;display:none">
                        <img src="" alt="Preview" style="max-width:200px;border-radius:6px;border:1px solid var(--color-border);">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="courseTopics">Topics (one per line)</label>
                    <textarea name="topics" id="courseTopics" class="form-input" rows="4" placeholder="Introduction to Laravel and MVC&#10;Routing and Controllers&#10;Blade Templating Engine"></textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" name="is_published" value="1" id="coursePublished" checked>
                    <span>Published</span>
                </label>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" id="courseModalCancel">Cancel</button>
                <button type="submit" class="btn btn-primary" id="courseModalSubmit">Create Course</button>
            </div>
        </form>
    </div>
</div>

@include('partials.dashboard-scripts')

<script>
(function() {
    var modal = document.getElementById('courseModal');
    var form = document.getElementById('courseForm');
    var methodInput = document.getElementById('courseFormMethod');
    var titleInput = document.getElementById('courseTitle');
    var categoryInput = document.getElementById('courseCategory');
    var descInput = document.getElementById('courseDescription');
    var priceInput = document.getElementById('coursePrice');
    var instructorSelect = document.getElementById('courseInstructor');
    var levelInput = document.getElementById('courseLevel');
    var durationInput = document.getElementById('courseDuration');
    var topicsInput = document.getElementById('courseTopics');
    var publishedCheck = document.getElementById('coursePublished');
    var thumbnailInput = document.getElementById('courseThumbnail');
    var preview = document.getElementById('courseThumbnailPreview');
    var previewImg = preview.querySelector('img');
    var modalTitle = document.getElementById('courseModalTitle');
    var modalIcon = document.getElementById('courseModalIcon');
    var submitBtn = document.getElementById('courseModalSubmit');

    function resetForm() {
        form.action = '{{ route('admin.courses.store') }}';
        methodInput.value = 'POST';
        form.querySelectorAll('input, textarea, select').forEach(function(el) {
            if (el.type === 'checkbox') el.checked = el.defaultChecked;
            else if (el.type === 'file')
            else el.value = '';
        });
        instructorSelect.value = '';
        preview.style.display = 'none';
        previewImg.src = '';
        modalTitle.textContent = 'Create Course';
        modalIcon.textContent = '📚';
        submitBtn.textContent = 'Create Course';
    }

    function fillForm(course) {
        form.action = '/admin/courses/' + course.id;
        methodInput.value = 'PATCH';
        titleInput.value = course.title;
        categoryInput.value = course.category;
        descInput.value = course.description;
        priceInput.value = course.price;
        instructorSelect.value = course.instructor_id;
        levelInput.value = course.level || '';
        durationInput.value = course.duration || '';
        topicsInput.value = (course.topics || []).join('\n');
        publishedCheck.checked = course.is_published;
        if (course.thumbnail) {
            previewImg.src = '/storage/' + course.thumbnail;
            preview.style.display = '';
        } else {
            preview.style.display = 'none';
        }
        modalTitle.textContent = 'Edit Course';
        modalIcon.textContent = '✏️';
        submitBtn.textContent = 'Update Course';
    }

    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModalFn() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.getElementById('createCourseBtn').addEventListener('click', function() {
        resetForm();
        openModal();
    });

    document.querySelectorAll('.btn-action-edit').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var course = JSON.parse(btn.getAttribute('data-course'));
            fillForm(course);
            openModal();
        });
    });

    thumbnailInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) { previewImg.src = e.target.result; preview.style.display = ''; };
            reader.readAsDataURL(this.files[0]);
        } else {
            preview.style.display = 'none';
        }
    });

    document.getElementById('generateWithAI').addEventListener('click', function() {
        var title = titleInput.value.trim();
        var category = categoryInput.value.trim();
        if (!title) { alert('Enter a course title first.'); return; }
        var btn = this;
        btn.disabled = true;
        btn.textContent = 'Generating...';
        fetch('{{ route('ai.suggest.course') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ title: title, category: category || null })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.description) descInput.value = data.description;
            if (data.topics && data.topics.length) topicsInput.value = data.topics.join('\n');
            if (data.level) levelInput.value = data.level;
            if (data.duration) durationInput.value = data.duration;
        })
        .catch(function() { alert('AI generation failed.'); })
        .finally(function() { btn.disabled = false; btn.textContent = '✨ Generate with AI'; });
    });

    document.getElementById('courseModalClose').addEventListener('click', closeModalFn);
    document.getElementById('courseModalCancel').addEventListener('click', closeModalFn);
    modal.addEventListener('click', function(e) { if (e.target === modal) closeModalFn(); });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModalFn();
    });
})();
</script>

@endsection
