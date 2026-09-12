@extends('layouts.app')

@php
    $bodyClass = 'teacher-course-page';
    $isEmptyTeacher = $isEmptyTeacher ?? blank($activeLesson ?? null);
    $isEmptyFilter = $isEmptyFilter ?? false;
    $showEmptyState = $isEmptyTeacher || $isEmptyFilter || blank($activeLesson ?? null);
    $activeSubjectLabel = ($activeLesson->subject_label ?? null) ?: ($teacherPageCopy['subject'] ?? 'Matière');
    $localeLabel = strtoupper($locale);
    $activeLevelLabel = ($activeLesson->level_label ?? null) ?: ($teacherPageCopy['level'] ?? 'Niveau');
    $teacherCourseNav = match ($locale) {
        'ar' => [
            'teacher_space' => 'فضاء الأستاذ',
            'profile' => 'الملف الشخصي',
            'payment' => 'سجل الدفع',
            'logout' => 'تسجيل الخروج',
            'languages' => 'اللغات',
        ],
        'en' => [
            'teacher_space' => 'Teacher space',
            'profile' => 'Profil',
            'payment' => 'Payment history',
            'logout' => 'Logout',
            'languages' => 'Languages',
        ],
        default => [
            'teacher_space' => 'Espace professeur',
            'profile' => 'Profil',
            'payment' => 'Historique des paiements',
            'logout' => 'Déconnexion',
            'languages' => 'Langues',
        ],
    };
    $subjectChoices = collect($allLessons)
        ->when($selectedLevel !== '', fn ($items) => $items->filter(fn ($item) => \App\Support\LevelAudience::matches($item->level_key, $selectedLevel)))
        ->groupBy('subject_key')
        ->map(fn ($items) => $items->first())
        ->sortBy('subject_label');
    $levelChoices = collect($allLessons)
        ->flatMap(fn ($item) => \App\Support\LevelAudience::choices($item->level_key, $item->level_label))
        ->sort();
    $activeLevelLabel = $levelChoices->get($selectedLevel, $activeLevelLabel);
@endphp

@section('content')
    <main class="teacher-course-page-shell">
        <header class="student-topbar glass-card teacher-course-topbar">
            <a class="course-brand" href="{{ route('teacher.index.locale', ['locale' => $locale]) }}">
                <img src="{{ asset('logo.jpeg') }}" alt="EasyPsi logo" class="course-brand-logo">
            </a>

            <div class="teacher-course-topbar__pills">
                <div class="student-topbar-title is-active teacher-course-topbar__teacher">{{ $teacherUser->name }}</div>

                <div class="student-topbar-menu teacher-course-pill-menu" data-topbar-menu>
                    <button class="student-topbar-menu__toggle teacher-course-pill-menu__toggle teacher-course-pill-menu__toggle--active"
                            type="button"
                            aria-expanded="false"
                            data-topbar-menu-toggle>
                        <span class="teacher-course-pill-menu__eyebrow">{{ $teacherPageCopy['subject'] ?? 'Matière' }}</span>
                        <span class="teacher-course-pill-menu__value">{{ $activeSubjectLabel }}</span>
                    </button>

                    <div class="student-topbar-menu__dropdown teacher-course-pill-menu__dropdown" data-topbar-menu-dropdown>
                        @foreach ($subjectChoices as $subjectLesson)
                            @php
                                $targetLesson = collect($allLessons)
                                    ->when($selectedLevel !== '', fn ($items) => $items->filter(fn ($item) => \App\Support\LevelAudience::matches($item->level_key, $selectedLevel)))
                                    ->firstWhere('subject_key', $subjectLesson->subject_key) ?? $subjectLesson;
                            @endphp
                            <a class="student-topbar-menu__option{{ $selectedSubject === $subjectLesson->subject_key ? ' is-active' : '' }}"
                               href="{{ route('teacher.course.locale', ['locale' => $locale, 'teacher' => $teacherUser, 'subject' => $subjectLesson->subject_key, 'level' => $selectedLevel, 'lesson' => $targetLesson->slug, 'part' => $activePart]) }}">
                                {{ $subjectLesson->subject_label }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="student-topbar-menu teacher-course-pill-menu" data-topbar-menu>
                    <button class="student-topbar-menu__toggle teacher-course-pill-menu__toggle teacher-course-pill-menu__toggle--active"
                            type="button"
                            aria-expanded="false"
                            data-topbar-menu-toggle>
                        <span class="teacher-course-pill-menu__eyebrow">{{ $teacherPageCopy['level'] ?? 'Niveau' }}</span>
                        <span class="teacher-course-pill-menu__value">{{ $activeLevelLabel }}</span>
                    </button>

                    <div class="student-topbar-menu__dropdown teacher-course-pill-menu__dropdown" data-topbar-menu-dropdown>
                        @foreach ($levelChoices as $levelChoiceKey => $levelChoiceLabel)
                            @php
                                $targetLesson = collect($allLessons)
                                    ->when($selectedSubject !== '', fn ($items) => $items->where('subject_key', $selectedSubject))
                                    ->first(fn ($item) => \App\Support\LevelAudience::matches($item->level_key, $levelChoiceKey));
                            @endphp
                            <a class="student-topbar-menu__option{{ $selectedLevel === $levelChoiceKey ? ' is-active' : '' }}"
                               href="{{ route('teacher.course.locale', ['locale' => $locale, 'teacher' => $teacherUser, 'subject' => $selectedSubject, 'level' => $levelChoiceKey, 'lesson' => $targetLesson?->slug, 'part' => $activePart]) }}">
                                {{ $levelChoiceLabel }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="student-topbar-spacer"></div>

            @auth
                @if (auth()->user()->role !== 'student')
                    <a class="secondary-btn" href="{{ route('teacher.space.locale', ['locale' => $locale]) }}">{{ $teacherCourseNav['teacher_space'] }}</a>
                @endif
            @endauth

            @include('partials.locale-switcher', [
                'routeName' => 'teacher.course.locale',
                'localeMenuStyle' => 'dropdown',
                'localeMenuLabel' => $teacherCourseNav['languages'],
            ])

            @auth
                <div class="student-topbar-menu" data-topbar-menu>
                    <button class="student-avatar" type="button" aria-expanded="false" data-topbar-menu-toggle>
                        @if (filled(auth()->user()->profile_photo_path ?? null))
                            <img src="{{ Storage::disk('public')->url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" class="student-avatar__image">
                        @else
                            {{ strtoupper(Str::substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        @endif
                    </button>

                    <div class="student-topbar-menu__dropdown student-topbar-menu__dropdown--profile" data-topbar-menu-dropdown>
                        <a class="student-topbar-menu__option" href="{{ route('student.profile.locale', ['locale' => $locale]) }}">{{ $teacherCourseNav['profile'] }}</a>
                        <a class="student-topbar-menu__option" href="{{ route('payment.history.locale', ['locale' => $locale]) }}">{{ $teacherCourseNav['payment'] }}</a>
                        <form method="POST" action="{{ route('logout.locale', ['locale' => $locale]) }}">
                            @csrf
                            <button class="student-topbar-menu__option student-topbar-menu__button" type="submit">{{ $teacherCourseNav['logout'] }}</button>
                        </form>
                    </div>
                </div>
            @endauth
        </header>

        <section class="teacher-course-page-layout">
            @if ($showEmptyState)
                <section class="teacher-course-main-panel">
                    <div class="glass-card teacher-course-empty">
                        <span class="teacher-space-form__chip">{{ $teacherUser->name }}</span>
                        <h1>{{ $isEmptyFilter ? ($teacherPageCopy['no_videos_for_filter'] ?? 'Aucune vidéo faite par ce professeur pour cette sélection') : ($teacherPageCopy['no_videos'] ?? 'Aucune vidéo de ce professeur pour le moment') }}</h1>
                        <p>{{ $teacherPageCopy['no_videos_text'] ?? '' }}</p>
                        @if ($isEmptyFilter && $subjectChoices->isNotEmpty())
                            <p>{{ $locale === 'ar' ? 'اختر مادة لهذا المستوى' : ($locale === 'en' ? 'Choose a subject for this level' : 'Choisissez une matière pour ce niveau') }}</p>
                            @foreach ($subjectChoices as $subjectChoice)
                                <a class="secondary-btn" href="{{ route('teacher.course.locale', ['locale' => $locale, 'teacher' => $teacherUser, 'level' => $selectedLevel, 'subject' => $subjectChoice->subject_key]) }}">{{ $subjectChoice->subject_label }}</a>
                            @endforeach
                        @endif
                        <a class="secondary-btn" href="{{ route('teacher.course.locale', ['locale' => $locale, 'teacher' => $teacherUser]) }}">{{ $teacherPageCopy['reset_filters'] ?? 'Réinitialiser' }}</a>
                        <a class="primary-btn" href="{{ route('teacher.index.locale', ['locale' => $locale]) }}">{{ $teacherPageCopy['back'] ?? 'Retour' }}</a>
                    </div>
                </section>
            @else
            <aside class="teacher-course-sidebar-panel">
                @foreach ($groupedLessons as $groupLabel => $items)
                    <section class="teacher-course-level-card glass-card">
                        <div class="teacher-course-level-card__head">
                            <h3>{{ $groupLabel }}</h3>
                            <span>{{ $localeLabel }}</span>
                        </div>

                        <div class="teacher-course-level-card__body">
                            @foreach ($items as $lessonItem)
                                <a class="teacher-lesson-link{{ $activeLesson->id === $lessonItem->id ? ' is-active' : '' }}"
                                   href="{{ route('teacher.course.locale', ['locale' => $locale, 'teacher' => $teacherUser, 'subject' => $selectedSubject, 'level' => $selectedLevel, 'lesson' => $lessonItem->slug, 'part' => $activePart]) }}">
                                    <span class="teacher-lesson-link__index">{{ ($lessonItem->sort_order ?? 0) > 0 ? $lessonItem->sort_order : $loop->iteration }}</span>
                                    <span class="teacher-lesson-link__title">{{ $lessonItem->title }}</span>
                                </a>

                                @if ($activeLesson->id === $lessonItem->id)
                                    <div class="teacher-parts">
                                        @foreach (['course' => $teacherPageCopy['course'], 'quiz' => $teacherPageCopy['quiz'], 'exercise' => $teacherPageCopy['exercise']] as $partKey => $partLabel)
                                            @php
                                                $partAsset = $lessonItem->assets->firstWhere('part', $partKey);
                                                $partIsAvailable = $partAsset && ($partKey === 'quiz' || filled($partAsset->youtube_url ?? null));
                                            @endphp
                                            @if ($partIsAvailable)
                                                <a class="teacher-part-link{{ $activePart === $partKey ? ' is-active' : '' }}"
                                                   href="{{ route('teacher.course.locale', ['locale' => $locale, 'teacher' => $teacherUser, 'subject' => $selectedSubject, 'level' => $selectedLevel, 'lesson' => $lessonItem->slug, 'part' => $partKey]) }}">
                                                    <span class="teacher-part-link__index">{{ $loop->iteration }}</span>
                                                    <span class="teacher-part-link__label">{{ $partLabel }}</span>
                                                    <span class="teacher-part-link__badge">{{ strtoupper($partAsset->access_level ?? 'free') }}</span>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </aside>

            <section class="teacher-course-main-panel">
                <div class="teacher-course-main-panel__intro glass-card" data-od-id="course-hero">
                    @php
                        $heroPartLabel = match($activePart ?? 'course') {
                            'quiz' => $teacherPageCopy['quiz'] ?? 'Quiz',
                            'exercise' => $teacherPageCopy['exercise'] ?? 'Exercice',
                            default => $teacherPageCopy['course'] ?? 'Cours',
                        };
                    @endphp
                    <div class="course-hero__eyebrows">
                        <span class="course-hero__pill" data-od-id="course-hero-level">{{ $activeLesson->level_label }}</span>
                        <span class="course-hero__pill course-hero__pill--soft" data-od-id="course-hero-subject">{{ $activeSubjectLabel }}</span>
                        <span class="course-hero__locale">{{ $localeLabel }}</span>
                    </div>
                    <h1 data-od-id="course-hero-title">{{ $activeLesson->title }}</h1>
                    <div class="course-hero__meta">
                        <span class="course-hero__avatar" aria-hidden="true">{{ strtoupper(mb_substr($teacherUser->name ?? 'E', 0, 1)) }}</span>
                        <span class="course-hero__teacher" data-od-id="course-hero-teacher">{{ $teacherUser->name }}</span>
                        <span class="course-hero__dot" aria-hidden="true"></span>
                        <span class="course-hero__part">{{ $heroPartLabel }}</span>
                    </div>
                </div>

                @if ($locked)
                    <section class="teacher-premium-stage">
                        <span class="teacher-premium-stage__tag">PREMIUM</span>
                        <h2>{{ $teacherPageCopy['premium_locked'] }}</h2>
                        <p>{{ $locale === 'ar' ? 'يمكنك تصفح صفحة الأستاذ، لكن فتح هذا الجزء يتطلب اشتراكاً بريميوم للمستوى المناسب.' : ($locale === 'en' ? 'You can browse the teacher page, but opening this section requires premium access for the matching level.' : 'Vous pouvez consulter la page du professeur, mais l’ouverture de cette partie demande un accès premium pour le niveau correspondant.') }}</p>
                        <a class="primary-btn" href="{{ route('payment.locale', ['locale' => $locale, 'back' => request()->fullUrl(), 'teacher' => $teacherUser->id, 'subject' => $activeLesson->subject_key, 'level' => $activeLesson->level_key]) }}">{{ $teacherPageCopy['go_premium'] }}</a>
                    </section>
                @elseif (($activeAsset->part ?? '') === 'quiz')
                    <section class="teacher-quiz-stage glass-card">
                        <h2>{{ $teacherPageCopy['quiz'] }}</h2>
                        <p>{{ $activeAsset->quiz_body ?: 'Quiz à ajouter par le professeur.' }}</p>
                    </section>
                @else
                    <section class="teacher-video-stage">
                        @if ($embedUrl)
                            <iframe src="{{ $embedUrl }}" title="{{ $activeLesson->title }}" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
                        @else
                            <div class="teacher-video-stage__placeholder">{{ $locale === 'ar' ? 'الفيديو غير متاح' : ($locale === 'en' ? 'Video unavailable' : 'Vidéo indisponible') }}</div>
                        @endif
                    </section>

                    <section class="teacher-course-details glass-card">
                        <div class="teacher-course-details__block">
                            <h3>{{ $teacherPageCopy['description'] }}</h3>
                            <p>{{ filled($activeAsset->description_body) ? $activeAsset->description_body : '—' }}</p>
                        </div>

                        <div class="teacher-course-details__block">
                            <h3>{{ $teacherPageCopy['supports'] }}</h3>
                            <p>{{ filled($activeAsset->support_body) ? $activeAsset->support_body : '—' }}</p>
                            @if (! empty($activeAsset->support_file_path))
                                <a class="secondary-btn teacher-course-details__file"
                                   href="{{ Storage::disk('public')->url($activeAsset->support_file_path) }}"
                                   target="_blank"
                                   rel="noreferrer">
                                    {{ $locale === 'ar' ? 'فتح الملف' : ($locale === 'en' ? 'Open file' : 'Ouvrir le fichier') }}
                                </a>
                            @endif
                        </div>
                    </section>
                @endif
            </section>
            @endif
        </section>

        @if (isset($activeLesson) && filled($activeLesson))
            @include('partials.course-chatbot')
        @endif
    </main>
@endsection
