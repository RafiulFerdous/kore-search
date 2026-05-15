@extends('layouts.app')

@section('title', 'Hero Settings — KoreSearch')

@section('content')

<div class="dashboard-layout">

    @include('partials.admin-sidebar')

    <div class="dashboard-main">

        <div class="dash-header">
            <h1 class="dash-title">Hero Settings</h1>
            <p class="dash-subtitle">Customize the homepage hero section</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="dash-section">
            <form method="POST" action="{{ route('admin.settings.hero.update') }}">
                @csrf @method('PATCH')

                <div class="form-group">
                    <label class="form-label" for="heroTitle">Title</label>
                    <input type="text" name="title" id="heroTitle" class="form-input @error('title') is-error @enderror"
                           value="{{ old('title', $hero->title) }}" required>
                    @error('title')<span class="field-error">{{ $message }}</span>@enderror
                    <p class="form-hint">You can use HTML like <code>&lt;span class="highlight"&gt;text&lt;/span&gt;</code></p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="heroSubtitle">Subtitle</label>
                    <textarea name="subtitle" id="heroSubtitle" class="form-input @error('subtitle') is-error @enderror"
                              rows="3" required>{{ old('subtitle', $hero->subtitle) }}</textarea>
                    @error('subtitle')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="heroImage">Hero Image URL</label>
                    <input type="url" name="hero_image" id="heroImage" class="form-input @error('hero_image') is-error @enderror"
                           value="{{ old('hero_image', $hero->hero_image) }}" placeholder="https://placehold.co/560x400/1F3864/ffffff?text=Learn+Online">
                    @error('hero_image')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Statistics</label>
                    <p class="form-hint" style="margin-bottom:14px">Actual DB counts shown as badges — edit the text values freely</p>
                    @php
                        $originalCounts = [
                            'Students'    => $totalStudents,
                            'Courses'     => $totalCourses,
                            'Instructors' => $totalInstructors,
                        ];
                    @endphp
                    <div id="statsContainer">
                        @if(old('stats', $hero->stats))
                            @foreach(old('stats', $hero->stats) as $i => $stat)
                                <div class="stat-card">
                                    <div class="stat-card-body">
                                        <div class="stat-field-group">
                                            <label class="stat-field-label">Count</label>
                                            <input type="text" name="stats[{{ $i }}][count]" class="form-input stat-input"
                                                   value="{{ $stat['count'] ?? '' }}" placeholder="e.g. 540+">
                                        </div>
                                        <div class="stat-field-group">
                                            <label class="stat-field-label">Label</label>
                                            <input type="text" name="stats[{{ $i }}][label]" class="form-input stat-input"
                                                   value="{{ $stat['label'] ?? '' }}" placeholder="e.g. Students">
                                        </div>
                                        @if(isset($originalCounts[$stat['label']]))
                                            <div class="stat-original-badge" title="Actual count in database">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                                                <span>{{ $originalCounts[$stat['label']] }}</span>
                                            </div>
                                        @endif
                                        <button type="button" class="stat-remove-btn" title="Remove stat">&times;</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" class="btn btn-outline btn-sm" id="addStatBtn" style="margin-top:8px">+ Add Stat</button>
                </div>

                <div class="modal-actions" style="margin-top:24px">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>

    </div>
</div>

@include('partials.dashboard-scripts')

<script>
(function() {
    var container = document.getElementById('statsContainer');
    var addBtn = document.getElementById('addStatBtn');
    var index = container ? container.children.length : 0;

    if (addBtn && container) {
        addBtn.addEventListener('click', function() {
            var card = document.createElement('div');
            card.className = 'stat-card';
            card.innerHTML =
                '<div class="stat-card-body">' +
                    '<div class="stat-field-group">' +
                        '<label class="stat-field-label">Count</label>' +
                        '<input type="text" name="stats[' + index + '][count]" class="form-input stat-input" placeholder="e.g. 540+">' +
                    '</div>' +
                    '<div class="stat-field-group">' +
                        '<label class="stat-field-label">Label</label>' +
                        '<input type="text" name="stats[' + index + '][label]" class="form-input stat-input" placeholder="e.g. Students">' +
                    '</div>' +
                    '<button type="button" class="stat-remove-btn" title="Remove stat">&times;</button>' +
                '</div>';
            container.appendChild(card);
            index++;
            card.querySelector('.stat-remove-btn').addEventListener('click', function() { card.remove(); });
        });
    }

    if (container) {
        container.querySelectorAll('.stat-remove-btn').forEach(function(btn) {
            btn.addEventListener('click', function() { btn.parentElement.parentElement.remove(); });
        });
    }
})();
</script>

@endsection
