@extends('layouts.app')

@php
    $labels = match ($locale) {
        'ar' => [
            'eyebrow' => 'دليل الأساتذة',
            'title' => 'ابحث عن أستاذ للبدء',
            'subtitle' => 'اختر أولاً المستوى، ثم الشعبة، ثم المادة للعثور بسرعة على الأستاذ المناسب.',
            'level' => 'المستوى',
            'track' => 'الشعبة',
            'subject' => 'المادة',
            'teacher' => 'اسم الأستاذ',
            'search' => 'بحث',
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
            'title' => 'Search for a teacher to get started',
            'subtitle' => 'Select the level first, then the track, then the subject to find the right teacher faster.',
            'level' => 'Level',
            'track' => 'Track',
            'subject' => 'Subject',
            'teacher' => 'Teacher name',
            'search' => 'Search',
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
            'title' => 'Rechercher un professeur pour commencer',
            'subtitle' => 'Choisissez d’abord le niveau, puis la filière, puis la matière pour trouver plus vite le bon professeur.',
            'level' => 'Niveau',
            'track' => 'Filière',
            'subject' => 'Matière',
            'teacher' => 'Nom du professeur',
            'search' => 'Rechercher',
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
    <style>
        body.teacher-directory-page .student-shell.teacher-directory-shell {
            width: min(100%, 1540px);
            padding-inline: 18px;
        }

        body.teacher-directory-page .student-topbar {
            background:
                radial-gradient(circle at 8% 0%, rgba(255, 255, 255, 0.28), transparent 30%),
                linear-gradient(135deg, #e56d00 0%, #ff8a00 54%, #ffb02e 100%) !important;
            box-shadow: 0 18px 44px rgba(184, 100, 0, 0.18) !important;
        }

        body.teacher-directory-page .student-topbar .student-topbar-menu__toggle,
        body.teacher-directory-page .student-topbar .student-avatar {
            background: rgba(255, 255, 255, 0.18) !important;
            border-color: rgba(255, 255, 255, 0.42) !important;
            color: #ffffff !important;
        }

        body.teacher-directory-page .student-topbar .student-topbar-menu--languages .student-topbar-menu__toggle {
            background: #ffffff !important;
            color: #8f4600 !important;
        }

        body.teacher-directory-page .teacher-directory-hero {
            padding: 34px 36px 36px;
        }

        body.teacher-directory-page .teacher-directory-hero__copy h1 {
            max-width: none;
            color: #0b4ea2 !important;
            white-space: nowrap;
        }

        body.teacher-directory-page .teacher-directory-filters__panel {
            display: grid;
            gap: 16px;
        }

        body.teacher-directory-page .teacher-directory-search-row {
            display: grid;
            grid-template-columns: minmax(260px, 1fr) minmax(150px, 220px);
            gap: 12px;
            align-items: end;
        }

        body.teacher-directory-page .teacher-directory-filters__grid {
            grid-template-columns: repeat(3, minmax(180px, 1fr)) minmax(150px, 220px);
        }

        body.teacher-directory-page .teacher-directory-filters .admin-form-field select:focus,
        body.teacher-directory-page .teacher-directory-filters .admin-form-field input:focus {
            border-color: rgba(229, 109, 0, 0.52) !important;
            box-shadow: 0 0 0 4px rgba(255, 138, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
        }

        body.teacher-directory-page .teacher-directory-filters__actions .primary-btn {
            background: linear-gradient(135deg, #e56d00, #ff8a00) !important;
            box-shadow: 0 14px 28px rgba(229, 109, 0, 0.22) !important;
            color: #ffffff !important;
        }

        body.teacher-directory-page .teacher-directory-card__avatar {
            background:
                radial-gradient(circle at 30% 24%, rgba(255, 255, 255, 0.28), transparent 34%),
                linear-gradient(135deg, #0b4ea2, #082f6f) !important;
            box-shadow: 0 14px 24px rgba(7, 47, 115, 0.2) !important;
        }

        body.teacher-directory-page .teacher-directory-card__action {
            border-color: rgba(255, 138, 0, 0.28) !important;
            color: #8f4600 !important;
        }

        body.teacher-directory-page .teacher-directory-card__action:hover {
            background: #e56d00 !important;
            border-color: #e56d00 !important;
            color: #ffffff !important;
        }

        @media (max-width: 760px) {
            body.teacher-directory-page {
                overflow-x: hidden;
            }

            body.teacher-directory-page .student-shell.teacher-directory-shell {
                padding-inline: 6px;
                width: 100%;
            }

            body.teacher-directory-page .teacher-directory-hero {
                padding: 18px 14px;
                overflow: hidden;
            }

            body.teacher-directory-page .teacher-directory-hero__copy {
                display: grid;
                align-content: center;
                gap: 10px;
                min-height: 96px;
            }

            body.teacher-directory-page .teacher-directory-search-row,
            body.teacher-directory-page .teacher-directory-filters__grid {
                grid-template-columns: 1fr;
            }

            body.teacher-directory-page .teacher-directory-card {
                grid-template-columns: 58px minmax(0, 1fr);
                align-items: center;
            }

            body.teacher-directory-page .teacher-directory-card__body {
                min-width: 0;
            }

            body.teacher-directory-page .teacher-directory-card__body h2 {
                margin: 0;
                font-size: clamp(1.28rem, 6vw, 1.55rem);
            }

            body.teacher-directory-page .teacher-directory-card__chips,
            body.teacher-directory-page .teacher-directory-card__body p,
            body.teacher-directory-page .teacher-directory-card__action {
                grid-column: 1 / -1;
            }

            body.teacher-directory-page .teacher-directory-hero__copy h1 {
                max-width: 100%;
                font-size: clamp(0.95rem, 4vw, 1.25rem);
                line-height: 1.12;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: clip;
                margin: 0;
            }

            body.teacher-directory-page .teacher-directory-filters__panel,
            body.teacher-directory-page .teacher-directory-filters .admin-form-field,
            body.teacher-directory-page .teacher-directory-filters .admin-form-field select,
            body.teacher-directory-page .teacher-directory-filters .admin-form-field input,
            body.teacher-directory-page .teacher-directory-filters__actions,
            body.teacher-directory-page .teacher-directory-filters__actions .primary-btn {
                width: 100%;
                max-width: 100%;
                min-width: 0;
                box-sizing: border-box;
            }
        }
    </style>

    <main class="student-shell teacher-directory-shell">
        @include('partials.student-topbar', [
            'routeName' => 'teacher.index.locale',
        ])

        <section class="glass-card teacher-directory-hero">
            <div class="teacher-directory-hero__copy">
                <span class="teacher-directory-hero__eyebrow">{{ $labels['eyebrow'] }}</span>
                <h1>{{ $labels['title'] }}</h1>
            </div>

            <form class="teacher-directory-filters" method="GET" action="{{ route('teacher.index.locale', ['locale' => $locale]) }}">
                <div class="teacher-directory-filters__panel">
                    <div class="teacher-directory-search-row">
                        <label class="admin-form-field teacher-directory-search-field">
                            <span>{{ $labels['teacher'] }}</span>
                            <input type="text" name="teacher" value="{{ $teacherName }}" placeholder="{{ $labels['placeholder'] }}">
                        </label>

                        <div class="teacher-directory-filters__actions teacher-directory-search-action">
                            <button class="primary-btn" type="submit">{{ $labels['search'] }}</button>
                        </div>
                    </div>

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
