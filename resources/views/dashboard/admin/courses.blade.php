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

<div class="modal-overlay hidden" id="createCourseModal">
    <div class="modal-content">
        <button class="modal-close" id="createCourseModalClose">&times;</button>
        <h2 class="modal-title">Create Course</h2>
        <form method="POST" action="{{ route('admin.courses.store') }}" id="createCourseForm" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="createTitle">Title</label>
                    <input type="text" name="title" id="createTitle" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="createCategory">Category</label>
                    <input type="text" name="category" id="createCategory" class="form-input" required>
                </div>
            </div>
            <button type="button" class="btn btn-outline btn-sm" id="generateWithAI" style="margin-bottom:12px">✨ Generate with AI</button>
            <div class="form-group">
                <label class="form-label" for="createDescription">Description</label>
                <textarea name="description" id="createDescription" class="form-input" rows="3" required></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="createPrice">Price (৳)</label>
                    <input type="number" step="0.01" min="0" name="price" id="createPrice" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="createInstructor">Instructor</label>
                    <select name="instructor_id" id="createInstructor" class="form-select" required>
                        <option value="">Select Instructor</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="createLevel">Level</label>
                    <input type="text" name="level" id="createLevel" class="form-input" placeholder="e.g. Beginner">
                </div>
                <div class="form-group">
                    <label class="form-label" for="createDuration">Duration</label>
                    <input type="text" name="duration" id="createDuration" class="form-input" placeholder="e.g. 10 hours">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="createThumbnail">Thumbnail</label>
                <input type="file" name="thumbnail" id="createThumbnail" class="form-input" accept=".png,.jpg,.jpeg">
                <div id="createThumbnailPreview" class="thumbnail-preview" style="margin-top:8px;display:none">
                    <img src="" alt="Thumbnail preview" style="max-width:200px;border-radius:6px;border:1px solid var(--color-border);">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="createTopics">Topics (one per line)</label>
                <textarea name="topics" id="createTopics" class="form-input" rows="4" placeholder="Introduction to Laravel and MVC&#10;Routing and Controllers&#10;Blade Templating Engine"></textarea>
            </div>
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" name="is_published" value="1" checked>
                    <span>Published</span>
                </label>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" id="createCourseModalCancel">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Course</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay hidden" id="editCourseModal">
    <div class="modal-content">
        <button class="modal-close" id="editCourseModalClose">&times;</button>
        <h2 class="modal-title">Edit Course</h2>
        <form method="POST" action="" id="editCourseForm" enctype="multipart/form-data">
            @csrf @method('PATCH')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="editTitle">Title</label>
                    <input type="text" name="title" id="editTitle" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="editCategory">Category</label>
                    <input type="text" name="category" id="editCategory" class="form-input" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="editDescription">Description</label>
                <textarea name="description" id="editDescription" class="form-input" rows="3" required></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="editPrice">Price (৳)</label>
                    <input type="number" step="0.01" min="0" name="price" id="editPrice" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="editInstructor">Instructor</label>
                    <select name="instructor_id" id="editInstructor" class="form-select" required>
                        <option value="">Select Instructor</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="editLevel">Level</label>
                    <input type="text" name="level" id="editLevel" class="form-input" placeholder="e.g. Beginner">
                </div>
                <div class="form-group">
                    <label class="form-label" for="editDuration">Duration</label>
                    <input type="text" name="duration" id="editDuration" class="form-input" placeholder="e.g. 10 hours">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="editThumbnail">Thumbnail</label>
                <input type="file" name="thumbnail" id="editThumbnail" class="form-input" accept=".png,.jpg,.jpeg">
                <div id="editThumbnailPreview" class="thumbnail-preview" style="margin-top:8px;display:none">
                    <img src="" alt="Current thumbnail" style="max-width:200px;border-radius:6px;border:1px solid var(--color-border);">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="editTopics">Topics (one per line)</label>
                <textarea name="topics" id="editTopics" class="form-input" rows="4" placeholder="Introduction to Laravel and MVC&#10;Routing and Controllers&#10;Blade Templating Engine"></textarea>
            </div>
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" name="is_published" value="1" id="editPublished">
                    <span>Published</span>
                </label>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" id="editCourseModalCancel">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Course</button>
            </div>
        </form>
    </div>
</div>

@include('partials.dashboard-scripts')

<script>
(function() {
    var createBtn = document.getElementById('createCourseBtn');
    var createModal = document.getElementById('createCourseModal');

    if (createBtn && createModal) {
        createBtn.addEventListener('click', function() {
            createModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            document.getElementById('createThumbnailPreview').style.display = 'none';
        });
    }

    document.getElementById('createThumbnail').addEventListener('change', function() {
        var preview = document.getElementById('createThumbnailPreview');
        var img = preview.querySelector('img');
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) { img.src = e.target.result; preview.style.display = ''; };
            reader.readAsDataURL(this.files[0]);
        } else {
            preview.style.display = 'none';
        }
    });

    document.getElementById('generateWithAI').addEventListener('click', function() {
        var title = document.getElementById('createTitle').value.trim();
        var category = document.getElementById('createCategory').value.trim();
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
            if (data.description) document.getElementById('createDescription').value = data.description;
            if (data.topics && data.topics.length) document.getElementById('createTopics').value = data.topics.join('\n');
            if (data.level) document.getElementById('createLevel').value = data.level;
            if (data.duration) document.getElementById('createDuration').value = data.duration;
        })
        .catch(function() { alert('AI generation failed. Is Ollama running?'); })
        .finally(function() { btn.disabled = false; btn.textContent = '✨ Generate with AI'; });
    });

    var editBtns = document.querySelectorAll('.btn-action-edit');
    var editModal = document.getElementById('editCourseModal');
    var editForm = document.getElementById('editCourseForm');

    editBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var course = JSON.parse(btn.getAttribute('data-course'));
            editForm.action = '/admin/courses/' + course.id;
            document.getElementById('editTitle').value = course.title;
            document.getElementById('editCategory').value = course.category;
            document.getElementById('editDescription').value = course.description;
            document.getElementById('editPrice').value = course.price;
            document.getElementById('editInstructor').value = course.instructor_id;
            document.getElementById('editLevel').value = course.level || '';
            document.getElementById('editDuration').value = course.duration || '';
            document.getElementById('editTopics').value = (course.topics || []).join('\n');
            document.getElementById('editPublished').checked = course.is_published;

            var preview = document.getElementById('editThumbnailPreview');
            var img = preview.querySelector('img');
            if (course.thumbnail) {
                img.src = '/storage/' + course.thumbnail;
                preview.style.display = '';
            } else {
                preview.style.display = 'none';
            }
            editModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    });

    function closeModal(modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.getElementById('createCourseModalClose').addEventListener('click', function() { closeModal(createModal); });
    document.getElementById('createCourseModalCancel').addEventListener('click', function() { closeModal(createModal); });
    document.getElementById('editCourseModalClose').addEventListener('click', function() { closeModal(editModal); });
    document.getElementById('editCourseModalCancel').addEventListener('click', function() { closeModal(editModal); });

    if (createModal) createModal.addEventListener('click', function(e) { if (e.target === createModal) closeModal(createModal); });
    if (editModal) editModal.addEventListener('click', function(e) { if (e.target === editModal) closeModal(editModal); });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (createModal && !createModal.classList.contains('hidden')) closeModal(createModal);
            if (editModal && !editModal.classList.contains('hidden')) closeModal(editModal);
        }
    });
})();
</script>

@endsection
