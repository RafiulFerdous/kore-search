@extends('layouts.app')

@section('title', 'Instructor Dashboard — KoreSearch')

@section('content')

<div class="dashboard-layout">

    @include('partials.dashboard-sidebar', ['navItems' => [
        ['icon' => '📚', 'label' => 'My Courses', 'section' => 'section-courses'],
        ['icon' => '➕', 'label' => 'Upload Course', 'section' => 'section-upload'],
    ]])

    <div class="dashboard-main">

        <div class="dash-header">
            <h1 class="dash-title">Instructor Dashboard</h1>
            <p class="dash-subtitle">Welcome back, {{ Auth::user()->name }}</p>
        </div>

        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-info">
                    <div class="stat-number">{{ $courses->count() }}</div>
                    <div class="stat-label">My Courses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👨‍🎓</div>
                <div class="stat-info">
                    <div class="stat-number">{{ $totalStudents }}</div>
                    <div class="stat-label">Students</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-info">
                    <div class="stat-number">৳{{ number_format($totalRevenue) }}</div>
                    <div class="stat-label">Revenue</div>
                </div>
            </div>
        </div>

        <section class="dash-section" id="section-courses">
            <h2 class="dash-section-title">My Courses</h2>
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
        </section>

        <section class="dash-section hidden" id="section-upload">
            <h2 class="dash-section-title">Upload New Course</h2>
            <form method="POST" action="{{ route('instructor.courses.store') }}" enctype="multipart/form-data" class="upload-form">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="title">Course Title</label>
                        <input type="text" name="title" id="title" class="form-input @error('title') is-error @enderror" value="{{ old('title') }}" required>
                        @error('title')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="category">Category</label>
                        <select name="category" id="category" class="form-select @error('category') is-error @enderror" required>
                            <option value="">Select Category</option>
                            <option value="Backend" {{ old('category') === 'Backend' ? 'selected' : '' }}>Backend</option>
                            <option value="Frontend" {{ old('category') === 'Frontend' ? 'selected' : '' }}>Frontend</option>
                            <option value="Database" {{ old('category') === 'Database' ? 'selected' : '' }}>Database</option>
                            <option value="Design" {{ old('category') === 'Design' ? 'selected' : '' }}>Design</option>
                            <option value="DevOps" {{ old('category') === 'DevOps' ? 'selected' : '' }}>DevOps</option>
                        </select>
                        @error('category')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Description</label>
                    <textarea
                        name="description"
                        id="description"
                        class="form-textarea @error('description') is-error @enderror"
                        maxlength="500"
                        rows="4"
                        required
                    >{{ old('description') }}</textarea>
                    <span class="char-count" id="descCounter">500 characters remaining</span>
                    @error('description')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="price">Price (BDT)</label>
                        <input type="number" name="price" id="price" class="form-input @error('price') is-error @enderror" value="{{ old('price', 0) }}" min="0" step="0.01" required>
                        @error('price')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="thumbnail">Thumbnail (JPG/PNG, max 2MB)</label>
                        <input type="file" name="thumbnail" id="thumbnail" class="form-input-file" accept="image/jpeg,image/png">
                        @error('thumbnail')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Upload Course</button>
            </form>
        </section>

    </div>
</div>

@include('partials.dashboard-scripts')

@endsection
