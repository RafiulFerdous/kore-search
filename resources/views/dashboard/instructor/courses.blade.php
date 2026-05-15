@extends('layouts.app')

@section('title', 'My Courses — Instructor | KoreSearch')

@section('content')

<div class="dashboard-layout">
    @include('partials.instructor-sidebar')

    <div class="dashboard-main">
        <div class="dash-header">
            <h1 class="dash-title">My Courses</h1>
            <p class="dash-subtitle">Manage your courses</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-header">
            <button class="btn btn-primary" id="showCreateModalBtn">+ Upload New Course</button>
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Enrolled</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td>{{ $course->id }}</td>
                            <td>
                                <a href="{{ route('courses.show', $course->slug) }}">{{ $course->title }}</a>
                            </td>
                            <td>{{ $course->category }}</td>
                            <td>{{ $course->isFree() ? 'Free' : '৳'.number_format($course->price) }}</td>
                            <td>{{ $course->enrolled_count }}</td>
                            <td>{{ $course->rating ? number_format($course->rating, 1) : '—' }}</td>
                            <td>
                                <span class="status-badge {{ $course->is_published ? 'status-completed' : 'status-pending' }}">
                                    {{ $course->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('instructor.courses.destroy', $course) }}" onsubmit="return confirm('Delete this course?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-row">No courses yet. Upload your first course!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            {{ $courses->links() }}
        </div>
    </div>
</div>

<div class="modal-overlay hidden" id="createCourseModal">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <h2 class="modal-title">Upload New Course</h2>
            <button type="button" class="modal-close" id="closeModalBtn">&times;</button>
        </div>
        <form method="POST" action="{{ route('instructor.courses.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="title">Course Title</label>
                        <input type="text" name="title" id="title" class="form-input" value="{{ old('title') }}" required>
                        @error('title')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="category">Category</label>
                        <select name="category" id="category" class="form-select" required>
                            <option value="">Select Category</option>
                            <option value="Backend">Backend</option>
                            <option value="Frontend">Frontend</option>
                            <option value="Database">Database</option>
                            <option value="Design">Design</option>
                            <option value="DevOps">DevOps</option>
                        </select>
                        @error('category')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" id="description" class="form-textarea" maxlength="500" rows="4" required>{{ old('description') }}</textarea>
                    @error('description')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="price">Price (BDT)</label>
                        <input type="number" name="price" id="price" class="form-input" value="{{ old('price', 0) }}" min="0" step="0.01" required>
                        @error('price')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="thumbnail">Thumbnail (JPG/PNG, max 2MB)</label>
                        <input type="file" name="thumbnail" id="thumbnail" class="form-input-file" accept="image/jpeg,image/png">
                        @error('thumbnail')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelModalBtn">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload Course</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var showBtn = document.getElementById('showCreateModalBtn');
    var modal = document.getElementById('createCourseModal');
    var closeBtn = document.getElementById('closeModalBtn');
    var cancelBtn = document.getElementById('cancelModalBtn');

    if (showBtn && modal) {
        showBtn.addEventListener('click', function() { modal.classList.remove('hidden'); });
    }
    if (closeBtn && modal) {
        closeBtn.addEventListener('click', function() { modal.classList.add('hidden'); });
    }
    if (cancelBtn && modal) {
        cancelBtn.addEventListener('click', function() { modal.classList.add('hidden'); });
    }
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) modal.classList.add('hidden');
        });
    }
})();
</script>
@endpush

@include('partials.dashboard-scripts')
@endsection
