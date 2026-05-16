@extends('layouts.app')

@section('title', 'Featured Courses — Admin | KoreSearch')

@section('content')

<div class="dashboard-layout">
    @include('partials.admin-sidebar')

    <div class="dashboard-main">
        <div class="dash-header">
            <h1 class="dash-title">Featured Courses</h1>
            <p class="dash-subtitle">Select up to 6 courses to display on the homepage</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.settings.featured.update') }}">
            @csrf
            @method('PATCH')

            <div class="card" style="padding:24px;">
                <div class="form-group">
                    <label class="form-label">Select Featured Courses (max 6)</label>
                    <p class="form-hint" style="margin-bottom:16px;">Choose the courses you want to feature on the home page. Deselect all for random selection.</p>

                    <div class="featured-courses-grid">
                        @php $selectedIds = $featured->course_ids ?? []; @endphp
                        @forelse($courses as $course)
                            <label class="featured-course-item {{ in_array($course->id, $selectedIds) ? 'selected' : '' }}">
                                <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                                    {{ in_array($course->id, $selectedIds) ? 'checked' : '' }}
                                    class="featured-course-checkbox">
                                <img src="{{ $course->thumbnail ?? 'https://placehold.co/80x60' }}"
                                    alt="" class="featured-course-thumb"
                                    onerror="this.src='https://placehold.co/80x60'">
                                <div class="featured-course-info">
                                    <span class="featured-course-title">{{ $course->title }}</span>
                                    <span class="featured-course-meta">{{ $course->category }} — ৳{{ number_format($course->price) }}</span>
                                </div>
                            </label>
                        @empty
                            <p class="empty-state">No published courses available.</p>
                        @endforelse
                    </div>

                    <div style="margin-top:20px;">
                        {{ $courses->links('vendor.pagination.custom') }}
                    </div>

                    @error('course_ids')<span class="field-error">{{ $message }}</span>@enderror
                    <span id="courseCountMsg" class="form-hint" style="margin-top:12px;display:block;"></span>
                </div>

                <div class="form-actions" style="margin-top:24px;">
                    <button type="submit" class="btn btn-primary">Save Featured Courses</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var STORAGE_KEY = 'featured_selected';
    var checkboxes = document.querySelectorAll('.featured-course-checkbox');
    var msg = document.getElementById('courseCountMsg');
    var form = document.querySelector('form');
    var max = 6;

    function getSelected() {
        try { return new Set(JSON.parse(sessionStorage.getItem(STORAGE_KEY) || '[]')); } catch(e) { return new Set(); }
    }

    function saveSelected(selected) {
        try { sessionStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(selected))); } catch(e) {}
    }

    function updateCount(showToastMsg) {
        var count = getSelected().size;
        if (msg) {
            if (count === 0) {
                msg.textContent = 'None selected — random courses will be shown on the homepage.';
            } else if (count >= max) {
                msg.textContent = 'Maximum ' + max + ' courses selected. Uncheck one to select another.';
            } else {
                msg.textContent = count + ' of ' + max + ' selected.';
            }
        }
        if (showToastMsg && count >= max && typeof showToast === 'function') {
            showToast('Maximum ' + max + ' courses selected. Uncheck one to select another.', 'warning');
        }
    }

    function restoreCheckboxes() {
        var selected = getSelected();
        checkboxes.forEach(function(cb) {
            if (selected.has(cb.value)) {
                cb.checked = true;
                cb.parentElement.classList.add('selected');
            } else {
                cb.checked = false;
                cb.parentElement.classList.remove('selected');
            }
        });
    }

    if (sessionStorage.getItem(STORAGE_KEY) === null) {
        var initial = new Set();
        checkboxes.forEach(function(cb) { if (cb.checked) initial.add(cb.value); });
        saveSelected(initial);
    }

    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', function() {
            var selected = getSelected();
            if (this.checked) {
                if (selected.size >= max) {
                    this.checked = false;
                    this.parentElement.classList.remove('selected');
                    if (typeof showToast === 'function') {
                        showToast('You can select up to ' + max + ' courses only.', 'warning');
                    }
                    return;
                }
                selected.add(this.value);
            } else {
                selected.delete(this.value);
            }
            saveSelected(selected);
            this.parentElement.classList.toggle('selected');
            updateCount(true);
        });
    });

    form.addEventListener('submit', function() {
        var selected = getSelected();
        var visibleIds = new Set(Array.from(checkboxes).map(function(cb) { return cb.value; }));
        selected.forEach(function(id) {
            if (!visibleIds.has(id)) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'course_ids[]';
                input.value = id;
                form.appendChild(input);
            }
        });
    });

    restoreCheckboxes();
    updateCount(false);
})();
</script>
@endpush

@include('partials.dashboard-scripts')

@endsection
