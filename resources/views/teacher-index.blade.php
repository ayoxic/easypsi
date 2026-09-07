@extends('layouts.app')

@php
    $labels = match ($locale) {
        'ar' => [
            'eyebrow' => 'دليل الأساتذة',
            'title' => 'اختر الأستاذ الذي تريده',
            'subtitle' => 'اختر أولاً المستوى، ثم الشعبة، ثم المادة للعثور بسرعة على الأستاذ المناسب.',
            'level' => 'المستوى',
            'track' => 'الشعبة',
            'subject' => 'المادة',
            'teacher' => 'اسم الأستاذ',
            'all_levels' => 'كل المستويات',
            'all_tracks' => 'كل الشعب',
            'all_subjects' => 'كل المواد',
            'placeholder' => 'اسم الأستاذ',
            'filter' => 'تصفية',
            'courses' => 'الدروس',
            'open' => 'فتح صفحة الأستاذ',
            'empty' => 'لا يوجد أستاذ مطابق لهذا الاختيار.',
        ],
        'en' => [
            'eyebrow' => 'Teacher directory',
            'title' => 'Choose the teacher you want',
            'subtitle' => 'Select the level first, then the track, then the subject to find the right teacher faster.',
            'level' => 'Level',
            'track' => 'Track',
            'subject' => 'Subject',
            'teacher' => 'Teacher name',
            'all_levels' => 'All levels',
            'all_tracks' => 'All tracks',
            'all_subjects' => 'All subjects',
            'placeholder' => 'Teacher name',
            'filter' => 'Filter',
            'courses' => 'courses',
            'open' => 'Open teacher page',
            'empty' => 'No teacher matches this selection.',
        ],
        default => [
            'eyebrow' => 'Annuaire des professeurs',
            'title' => 'Choisissez le professeur que vous voulez',
            'subtitle' => 'Choisissez d’abord le niveau, puis la filière, puis la matière pour trouver plus vite le bon professeur.',
            'level' => 'Niveau',
            'track' => 'Filière',
            'subject' => 'Matière',
            'teacher' => 'Nom du professeur',
            'all_levels' => 'Tous les niveaux',
            'all_tracks' => 'Toutes les filières',
            'all_subjects' => 'Toutes les matières',
            'placeholder' => 'Nom du professeur',
            'filter' => 'Filtrer',
            'courses' => 'cours',
            'open' => 'Ouvrir la page du professeur',
            'empty' => 'Aucun professeur ne correspond à cette sélection.',
        ],
    };

    $subjectKeys = $selectedLevel && $selectedTrack && isset($matrix[$selectedLevel]['tracks'][$selectedTrack]['subjects'])
        ? $matrix[$selectedLevel]['tracks'][$selectedTrack]['subjects']
        : array_keys($subjectOptions);
    if ($selectedSubject !== '' && ! in_array($selectedSubject, $subjectKeys, true)) {
        array_unshift($subjectKeys, $selectedSubject);
    }
@endphp

@php
    $bodyClass = 'teacher-directory-page';
@endphp

@section('content')
    <main class="student-shell teacher-directory-shell">
        @include('partials.student-topbar', [
            'topbarTitle' => $labels['title'],
            'routeName' => 'teacher.index.locale',
        ])

        <section class="glass-card teacher-directory-hero">
            <div class="teacher-directory-hero__copy">
                <span class="teacher-directory-hero__eyebrow">{{ $labels['eyebrow'] }}</span>
                <h1>{{ $labels['title'] }}</h1>
                <p>{{ $labels['subtitle'] }}</p>
            </div>

            <form class="teacher-directory-filters" method="GET" action="{{ route('teacher.index.locale', ['locale' => $locale]) }}">
                <div class="teacher-directory-filters__panel">
                    <div class="teacher-directory-filters__grid">
                    <label class="admin-form-field">
                        <span>{{ $labels['level'] }}</span>
                        <select name="level" onchange="this.form.submit()">
                            <option value="">{{ $labels['all_levels'] }}</option>
                            @foreach ($matrix as $levelKey => $levelData)
                                <option value="{{ $levelKey }}" @selected($selectedLevel === $levelKey)>
                                    {{ $levelData['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="admin-form-field">
                        <span>{{ $labels['track'] }}</span>
                        <select name="track" onchange="this.form.submit()">
                            <option value="">{{ $labels['all_tracks'] }}</option>
                            @if ($selectedLevel && isset($matrix[$selectedLevel]))
                                @foreach ($matrix[$selectedLevel]['tracks'] as $trackKey => $trackData)
                                    <option value="{{ $trackKey }}" @selected($selectedTrack === $trackKey)>
                                        {{ $trackData['label'] }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </label>

                    <label class="admin-form-field">
                        <span>{{ $labels['subject'] }}</span>
                        <select name="subject">
                            <option value="">{{ $labels['all_subjects'] }}</option>
                            @foreach ($subjectKeys as $subjectKey)
                                <option value="{{ $subjectKey }}" @selected($selectedSubject === $subjectKey)>
                                    {{ $subjectOptions[$subjectKey] ?? $subjectKey }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="admin-form-field">
                        <span>{{ $labels['teacher'] }}</span>
                        <input type="text" name="teacher" value="{{ $teacherName }}" placeholder="{{ $labels['placeholder'] }}">
                    </label>

                    <div class="teacher-directory-filters__actions">
                        <button class="primary-btn" type="submit">{{ $labels['filter'] }}</button>
                    </div>
                </div>
                </div>
            </form>
        </section>

        <section class="teacher-directory-results">
            @if ($teachers->isEmpty())
                <article class="glass-card teacher-directory-empty">
                    <p>{{ $labels['empty'] }}</p>
                </article>
            @else
                @foreach ($teachers as $teacher)
                    @php
                        $teacherLessons = $teacher->teacherLessons ?? collect();
                        $levelTags = [];
                        $subjectTags = [];

                        foreach ($teacherLessons as $lesson) {
                            if (filled($lesson->level_label) && ! in_array($lesson->level_label, $levelTags, true) && count($levelTags) < 2) {
                                $levelTags[] = $lesson->level_label;
                            }

                            if (filled($lesson->subject_label) && ! in_array($lesson->subject_label, $subjectTags, true) && count($subjectTags) < 2) {
                                $subjectTags[] = $lesson->subject_label;
                            }
                        }
                    @endphp

                    <article class="glass-card teacher-directory-card">
                        <div class="teacher-directory-card__avatar">
                            {{ strtoupper(mb_substr($teacher->name, 0, 1)) }}
                        </div>

                        <div class="teacher-directory-card__body">
                            <h2>{{ $teacher->name }}</h2>

                            <div class="teacher-directory-card__chips">
                                @foreach ($levelTags as $tag)
                                    <span class="ghost-pill ghost-pill--soft">{{ $tag }}</span>
                                @endforeach
                                @foreach ($subjectTags as $tag)
                                    <span class="ghost-pill">{{ $tag }}</span>
                                @endforeach
                            </div>

                            <p>{{ $labels['courses'] }} : {{ $teacher->lessons_count }}</p>
                        </div>

                        <a class="secondary-btn teacher-directory-card__action" href="{{ route('teacher.course.locale', ['locale' => $locale, 'teacher' => $teacher]) }}">
                            {{ $labels['open'] }}
                        </a>
                    </article>
                @endforeach
            @endif
        </section>
    </main>
@endsection
