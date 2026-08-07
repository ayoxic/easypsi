@extends('layouts.app')

@php
    $bodyClass = 'teacher-course-page';
    $activeSubjectLabel = $activeLesson->subject_label ?: 'Matière';
    $localeLabel = strtoupper($locale);
    $activeLevelLabel = $activeLesson->level_label ?: 'Niveaux';
    $subjectChoices = $groupedLessons
        ->flatten()
        ->groupBy('subject_label')
        ->map(fn ($items) => $items->first())
        ->sortKeys();
    $levelChoices = $groupedLessons
        ->flatten()
        ->groupBy('level_label')
        ->map(fn ($items) => $items->first())
        ->sortKeys();
@endphp

@section('content')
    <main class="teacher-course-page-shell">
        <header class="student-topbar glass-card teacher-course-topbar">
            <a class="course-brand" href="{{ route('teacher.index.locale', ['locale' => $locale]) }}">
                <img src="{{ asset('logo.jpeg') }}" alt="EasyPsi logo" class="course-brand-logo">
            </a>

            <div class="teacher-course-topbar__pills">
                <div class="student-topbar-title is-active">{{ $teacherUser->name }}</div>

                <div class="student-topbar-menu teacher-course-pill-menu" data-topbar-menu>
                    <button class="student-topbar-menu__toggle teacher-course-pill-menu__toggle teacher-course-pill-menu__toggle--active"
                            type="button"
                            aria-expanded="false"
                            data-topbar-menu-toggle>
                        {{ $activeSubjectLabel }}
                    </button>

                    <div class="student-topbar-menu__dropdown teacher-course-pill-menu__dropdown" data-topbar-menu-dropdown>
                        @foreach ($subjectChoices as $subjectLesson)
                            <a class="student-topbar-menu__option{{ $activeLesson->subject_label === $subjectLesson->subject_label ? ' is-active' : '' }}"
                               href="{{ route('teacher.course.locale', ['locale' => $locale, 'teacher' => $teacherUser, 'lesson' => $subjectLesson->slug, 'part' => 'course']) }}">
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
                        {{ $activeLevelLabel }}
                    </button>

                    <div class="student-topbar-menu__dropdown teacher-course-pill-menu__dropdown" data-topbar-menu-dropdown>
                        @foreach ($levelChoices as $levelLesson)
                            <a class="student-topbar-menu__option{{ $activeLesson->level_label === $levelLesson->level_label ? ' is-active' : '' }}"
                               href="{{ route('teacher.course.locale', ['locale' => $locale, 'teacher' => $teacherUser, 'lesson' => $levelLesson->slug, 'part' => 'course']) }}">
                                {{ $levelLesson->level_label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="student-topbar-spacer"></div>

            <a class="primary-btn" href="{{ route('payment.locale', ['locale' => $locale]) }}">{{ $teacherPageCopy['go_premium'] }}</a>

            @include('partials.locale-switcher', [
                'routeName' => 'teacher.course.locale',
                'localeMenuStyle' => 'dropdown',
                'localeMenuLabel' => 'Langues',
            ])

            @auth
                <div class="student-topbar-menu" data-topbar-menu>
                    <button class="student-avatar" type="button" aria-expanded="false" data-topbar-menu-toggle>
                        {{ strtoupper(Str::substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </button>

                    <div class="student-topbar-menu__dropdown student-topbar-menu__dropdown--profile" data-topbar-menu-dropdown>
                        <a class="student-topbar-menu__option" href="{{ route('student.profile.locale', ['locale' => $locale]) }}">Profile</a>
                        <a class="student-topbar-menu__option" href="{{ route('payment.locale', ['locale' => $locale]) }}">Paiement</a>
                        <form method="POST" action="{{ route('logout.locale', ['locale' => $locale]) }}">
                            @csrf
                            <button class="student-topbar-menu__option student-topbar-menu__button" type="submit">Déconnexion</button>
                        </form>
                    </div>
                </div>
            @endauth
        </header>

        <section class="teacher-course-page-layout">
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
                                   href="{{ route('teacher.course.locale', ['locale' => $locale, 'teacher' => $teacherUser, 'lesson' => $lessonItem->slug]) }}">
                                    <span class="teacher-lesson-link__index">{{ $loop->iteration }}</span>
                                    <span class="teacher-lesson-link__title">{{ $lessonItem->title }}</span>
                                </a>

                                @if ($activeLesson->id === $lessonItem->id)
                                    <div class="teacher-parts">
                                        @foreach (['course' => $teacherPageCopy['course'], 'quiz' => $teacherPageCopy['quiz'], 'exercise' => $teacherPageCopy['exercise']] as $partKey => $partLabel)
                                            @php
                                                $partAsset = $lessonItem->assets->firstWhere('part', $partKey);
                                            @endphp
                                            @if ($partAsset)
                                                <a class="teacher-part-link{{ $activePart === $partKey ? ' is-active' : '' }}"
                                                   href="{{ route('teacher.course.locale', ['locale' => $locale, 'teacher' => $teacherUser, 'lesson' => $lessonItem->slug, 'part' => $partKey]) }}">
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
                <div class="teacher-course-main-panel__intro">
                    <p>{{ $activeLesson->level_label }}</p>
                    <h1>{{ $activeLesson->title }}</h1>
                    <span>{{ $teacherUser->name }}</span>
                </div>

                @if ($locked)
                    <section class="teacher-premium-stage">
                        <span class="teacher-premium-stage__tag">PREMIUM</span>
                        <h2>{{ $teacherPageCopy['premium_locked'] }}</h2>
                        <p>Vous pouvez consulter la page du professeur, mais l’ouverture de cette partie demande un accès premium pour le niveau correspondant.</p>
                        <a class="primary-btn" href="{{ route('payment.locale', ['locale' => $locale]) }}">{{ $teacherPageCopy['go_premium'] }}</a>
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
                            <div class="teacher-video-stage__placeholder">Vidéo indisponible</div>
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
                                    Ouvrir le fichier
                                </a>
                            @endif
                        </div>
                    </section>
                @endif
            </section>
        </section>
    </main>
@endsection
