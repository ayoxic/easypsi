@extends('layouts.app')

@php
    $bodyClass = 'teacher-space-page';
    $teacherDashboardLabels = match ($locale) {
        'ar' => [
            'manage_title' => 'إدارة دروس الأستاذ',
            'manage_text' => 'يمكنك اختيار عنوان موجود لإتمامه أو كتابة عنوان جديد لإنشاء درس جديد.',
            'courses_button' => 'الدروس',
            'delete_title' => 'حذف المحتوى المنشور',
            'delete_text' => 'قم بتصفية الدروس حسب اللغة والمستوى والشعبة والمادة والنوع لتجد المحتوى المراد حذفه بسرعة.',
            'locale' => 'اللغة',
            'level' => 'المستوى',
            'track' => 'الشعبة',
            'subject' => 'المادة',
            'type' => 'النوع',
            'all_locales' => 'كل اللغات',
            'all_levels' => 'كل المستويات',
            'all_tracks' => 'كل الشعب',
            'all_subjects' => 'كل المواد',
            'all_types' => 'كل الأنواع',
            'no_results' => 'لا يوجد محتوى مطابق لهذه الفلاتر.',
        ],
        'en' => [
            'manage_title' => 'Teacher course management',
            'manage_text' => 'You can choose an existing title to complete it or write a new one to create a new course.',
            'courses_button' => 'Courses',
            'delete_title' => 'Delete published content',
            'delete_text' => 'Filter lessons by language, level, track, subject, and type to quickly find the content you want to delete.',
            'locale' => 'Language',
            'level' => 'Level',
            'track' => 'Track',
            'subject' => 'Subject',
            'type' => 'Type',
            'all_locales' => 'All languages',
            'all_levels' => 'All levels',
            'all_tracks' => 'All tracks',
            'all_subjects' => 'All subjects',
            'all_types' => 'All types',
            'no_results' => 'No content matches these filters.',
        ],
        default => [
            'manage_title' => 'Gestion des cours du professeur',
            'manage_text' => 'Vous pouvez choisir un titre existant pour le compléter ou écrire un nouveau titre pour créer un nouveau cours.',
            'courses_button' => 'Cours',
            'delete_title' => 'Supprimer le contenu publié',
            'delete_text' => 'Filtrez les leçons par langue, niveau, filière, matière et type pour retrouver rapidement le contenu à supprimer.',
            'locale' => 'Langue',
            'level' => 'Niveau',
            'track' => 'Filière',
            'subject' => 'Matière',
            'type' => 'Type',
            'all_locales' => 'Toutes les langues',
            'all_levels' => 'Tous les niveaux',
            'all_tracks' => 'Toutes les filières',
            'all_subjects' => 'Toutes les matières',
            'all_types' => 'Tous les types',
            'no_results' => 'Aucun contenu ne correspond à ces filtres.',
        ],
    };

    $programOptions = collect($matrix)->map(function ($level, $levelKey) use ($subjectOptions) {
        return [
            'key' => $levelKey,
            'label' => $level['label'],
            'subjects' => collect($subjectOptions)->map(fn ($label, $key) => ['key' => $key, 'label' => $label])->values()->all(),
            'tracks' => collect($level['tracks'] ?? [])->map(function ($track, $trackKey) use ($subjectOptions) {
                return [
                    'key' => $trackKey,
                    'label' => $track['label'],
                    'subjects' => collect($subjectOptions)->map(fn ($label, $key) => ['key' => $key, 'label' => $label])->values()->all(),
                ];
            })->values()->all(),
        ];
    })->values()->all();

    $assetsPayload = [];
    foreach ($teacherLessons as $lesson) {
        foreach ($lesson->assets as $asset) {
            $quizPayload = null;
            if (filled($asset->quiz_body ?? null)) {
                $decoded = json_decode($asset->quiz_body, true);
                if (is_array($decoded)) {
                    $quizPayload = $decoded;
                    foreach (($quizPayload['questions'] ?? []) as $questionIndex => $question) {
                        if (! empty($question['question_image_path'])) {
                            $quizPayload['questions'][$questionIndex]['question_image_url'] = Storage::disk('public')->url($question['question_image_path']);
                        }
                        foreach (($question['choices'] ?? []) as $choiceIndex => $choice) {
                            if (! empty($choice['image_path'])) {
                                $quizPayload['questions'][$questionIndex]['choices'][$choiceIndex]['image_url'] = Storage::disk('public')->url($choice['image_path']);
                            }
                        }
                    }
                }
            }

            $assetsPayload[$lesson->id.'|'.$asset->part] = [
                'youtube_url' => $asset->youtube_url,
                'access_level' => $asset->access_level ?? 'free',
                'description_body' => $asset->description_body,
                'support_body' => $asset->support_body,
                'quiz_body' => $quizPayload ? json_encode($quizPayload, JSON_UNESCAPED_UNICODE) : ($asset->quiz_body ?? ''),
            ];
        }
    }
@endphp

@section('content')
    <main class="student-shell teacher-space-shell">
        @include('partials.student-topbar', [
            'topbarTitle' => $teacherPageCopy['teacher_space'],
            'routeName' => 'teacher.space.locale',
            'showPremiumButton' => false,
        ])

        @if (session('status'))
            <div class="form-flash teacher-space-flash">{{ session('status') }}</div>
        @endif

        <section class="student-page-card teacher-space-card">
            <div class="teacher-space-heading">
                <div class="teacher-space-heading__top">
                    <div class="teacher-space-heading__copy">
                        <span class="brand-chip">{{ $teacherPageCopy['teacher_space'] }}</span>
                        <h1>{{ $teacherDashboardLabels['manage_title'] }}</h1>
                        <p>{{ $teacherDashboardLabels['manage_text'] }}</p>
                    </div>

                    @if ($teacherUser)
                        <a class="secondary-btn teacher-space-heading__action" href="{{ route('teacher.course.locale', ['locale' => $locale, 'teacher' => $teacherUser]) }}">
                            {{ $teacherDashboardLabels['courses_button'] }}
                        </a>
                    @endif
                </div>
            </div>

            @if ($errors->any())
                <div class="form-errors">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form
                class="teacher-space-form"
                method="POST"
                action="{{ route('teacher.content.store', ['locale' => $locale]) }}"
                enctype="multipart/form-data"
                data-teacher-space-form
                data-teacher-content-form
                data-assets='@json($assetsPayload, JSON_UNESCAPED_UNICODE)'
                data-program-options='@json($programOptions, JSON_UNESCAPED_UNICODE)'
            >
                @csrf

                <section class="teacher-space-form__panel teacher-space-form__panel--filters glass-card">
                    <div class="teacher-space-form__grid">
                        <label class="student-form-field">
                            <span>Langue</span>
                            <select name="locale" data-teacher-locale-select>
                                @foreach ($availableLocales as $switchLocale => $localeData)
                                    <option value="{{ $switchLocale }}" @selected($locale === $switchLocale)>{{ $localeData['short'] }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="student-form-field">
                            <span>Niveau</span>
                            <select name="level" data-teacher-program-select>
                                <option value="">Choisissez le niveau</option>
                                @foreach ($matrix as $levelKey => $level)
                                    <option value="{{ $levelKey }}">{{ $level['label'] }}</option>
                                @endforeach
                            </select>
                        </label>

                        <div class="student-form-field" data-teacher-track-field>
                            <span>Filière <small data-teacher-track-hint hidden>— pas de filière pour ce niveau, passez à la matière</small></span>
                            <select name="track[]" multiple size="4" data-teacher-track-select hidden>
                                <option value="">Choisissez la filière</option>
                            </select>
                            <div class="teacher-track-options" data-teacher-track-options></div>
                        </div>

                        <label class="student-form-field">
                            <span>Matière</span>
                            <select name="subject" required data-teacher-subject-select>
                                <option value="">Choisissez la matière</option>
                            </select>
                        </label>

                        <label class="student-form-field">
                            <span>Titre existant</span>
                            <select name="existing_lesson" data-teacher-lesson-select>
                                <option value="">Choisissez un titre existant</option>
                                @foreach ($teacherLessons as $lesson)
                                    <option
                                        value="{{ $lesson->id }}"
                                        data-locale="{{ $lesson->locale }}"
                                        data-subject="{{ $lesson->subject_key }}"
                                        data-level="{{ $lesson->level_key }}"
                                        data-sort-order="{{ $lesson->sort_order ?? 0 }}"
                                    >
                                        {{ $lesson->title }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="student-form-field">
                            <span>Nouveau titre</span>
                            <input type="text" name="title" placeholder="Écrire le nouveau titre du cours">
                        </label>

                        <label class="student-form-field">
                            <span>Ordre <small>1, 2, 3… (vide = à la fin)</small></span>
                            <input type="number" name="sort_order" min="0" max="999" step="1" placeholder="Ex. 1" data-teacher-sort-input>
                        </label>

                        <label class="student-form-field">
                            <span>Partie</span>
                            <select name="part" data-teacher-space-part data-teacher-part-select>
                                <option value="course">{{ $teacherPageCopy['course'] }}</option>
                                <option value="exercise">{{ $teacherPageCopy['exercise'] }}</option>
                                <option value="quiz">{{ $teacherPageCopy['quiz'] }}</option>
                            </select>
                        </label>

                        <label class="student-form-field">
                            <span>Accès</span>
                            <select name="access_level" data-teacher-access-select>
                                <option value="free">{{ $teacherPageCopy['free'] }}</option>
                                <option value="premium">{{ $teacherPageCopy['premium'] }}</option>
                            </select>
                        </label>

                        <input type="hidden" name="level_key" value="" data-teacher-level-key>
                    </div>
                </section>

                <section class="teacher-space-form__panel teacher-space-form__panel--content glass-card" data-teacher-video-fields>
                    <div class="teacher-space-form__panel-head">
                        <span class="teacher-space-form__chip" data-teacher-space-chip>COURS</span>
                        <h2>Lien vidéo YouTube</h2>
                        <p>Collez un lien YouTube ou importez une vidéo, puis rédigez la description et le support affichés aux élèves.</p>
                    </div>

                    <div class="teacher-space-form__content-grid">
                        <label class="student-form-field">
                            <span>Lien vidéo YouTube</span>
                            <input type="url" name="youtube_url" placeholder="https://www.youtube.com/watch?v=...">
                        </label>

                        <label class="student-form-field">
                            <span>Fichier support</span>
                            <input type="file" name="support_file">
                        </label>

                        <label class="student-form-field teacher-space-form__field--full">
                            <span>{{ $teacherPageCopy['description'] }}</span>
                            <textarea name="description_body" rows="4"></textarea>
                        </label>

                        <label class="student-form-field teacher-space-form__field--full">
                            <span>{{ $teacherPageCopy['supports'] }}</span>
                            <textarea name="support_body" rows="4"></textarea>
                        </label>
                    </div>
                </section>

                <section class="teacher-space-form__panel teacher-space-form__panel--content glass-card" data-teacher-quiz-fields hidden>
                    <div class="teacher-space-form__panel-head">
                        <span class="teacher-space-form__chip">QUIZ</span>
                        <h2>Questions et réponses du quiz</h2>
                        <p>Ajoutez plusieurs questions. Chaque question peut contenir du texte ou une image, et chaque réponse peut aussi contenir du texte ou une image.</p>
                    </div>

                    <input type="hidden" name="quiz_body" value="" data-teacher-quiz-body>

                    <div
                        class="admin-quiz-builder admin-quiz-builder--questions"
                        data-teacher-quiz-builder
                        data-question-label="Question"
                        data-choice-label="Réponse"
                        data-correct-label="Bonne réponse"
                        data-add-choice-label="Ajouter une réponse"
                        data-add-question-label="Ajouter une question"
                        data-delete-question-label="Supprimer la question"
                        data-question-image-label="Image de la question"
                        data-choice-image-label="Image de la réponse"
                        data-keep-image-label="Image actuelle"
                    >
                        <div class="admin-quiz-builder__head">
                            <span>Questions du quiz</span>
                            <button class="secondary-btn admin-quiz-builder__add" type="button" data-teacher-add-question>Ajouter une question</button>
                        </div>
                        <div class="admin-quiz-question-list" data-teacher-quiz-question-list></div>
                    </div>
                </section>

                <div class="teacher-space-form__actions">
                    <button class="primary-btn" type="submit">{{ $teacherPageCopy['save'] }}</button>
                </div>
            </form>
        </section>

        <section
            class="teacher-space-delete-panel glass-card"
            data-teacher-delete-panel
            data-program-options='@json($programOptions, JSON_UNESCAPED_UNICODE)'
        >
            <div class="teacher-space-form__panel-head">
                <span class="teacher-space-form__chip">{{ $teacherPageCopy['delete'] }}</span>
                <h2>{{ $teacherDashboardLabels['delete_title'] }}</h2>
                <p>{{ $teacherDashboardLabels['delete_text'] }}</p>
            </div>

            <div class="teacher-space-delete-panel__filters teacher-space-form__grid">
                <label class="student-form-field">
                    <span>{{ $teacherDashboardLabels['locale'] }}</span>
                    <select data-teacher-delete-locale>
                        <option value="">{{ $teacherDashboardLabels['all_locales'] }}</option>
                        @foreach ($availableLocales as $switchLocale => $localeData)
                            <option value="{{ $switchLocale }}">{{ $localeData['short'] }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="student-form-field">
                    <span>{{ $teacherDashboardLabels['level'] }}</span>
                    <select data-teacher-delete-level>
                        <option value="">{{ $teacherDashboardLabels['all_levels'] }}</option>
                        @foreach ($matrix as $levelKey => $level)
                            <option value="{{ $levelKey }}">{{ $level['label'] }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="student-form-field">
                    <span>{{ $teacherDashboardLabels['track'] }}</span>
                    <select data-teacher-delete-track>
                        <option value="">{{ $teacherDashboardLabels['all_tracks'] }}</option>
                    </select>
                </label>

                <label class="student-form-field">
                    <span>{{ $teacherDashboardLabels['subject'] }}</span>
                    <select data-teacher-delete-subject>
                        <option value="">{{ $teacherDashboardLabels['all_subjects'] }}</option>
                        @foreach ($subjectOptions as $subjectKey => $subjectLabel)
                            <option value="{{ $subjectKey }}">{{ $subjectLabel }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="student-form-field">
                    <span>{{ $teacherDashboardLabels['type'] }}</span>
                    <select data-teacher-delete-type>
                        <option value="">{{ $teacherDashboardLabels['all_types'] }}</option>
                        <option value="course">{{ $teacherPageCopy['course'] }}</option>
                        <option value="exercise">{{ $teacherPageCopy['exercise'] }}</option>
                        <option value="quiz">{{ $teacherPageCopy['quiz'] }}</option>
                    </select>
                </label>
            </div>

            <p class="teacher-space-delete-panel__empty" data-teacher-delete-empty hidden>{{ $teacherDashboardLabels['no_results'] }}</p>
        </section>

        <section class="teacher-card-grid" data-teacher-delete-results hidden>
            @foreach ($teacherLessons as $lesson)
                @php
                    $baseLevelKey = str_contains((string) $lesson->level_key, '::')
                        ? Str::before((string) $lesson->level_key, '::')
                        : (string) $lesson->level_key;
                    $trackKey = str_contains((string) $lesson->level_key, '::')
                        ? trim((string) Str::after((string) $lesson->level_key, '::'))
                        : '';
                @endphp
                <article
                    class="payment-plan-card"
                    data-teacher-delete-card
                    data-locale="{{ $lesson->locale }}"
                    data-level="{{ $baseLevelKey }}"
                    data-track="{{ $trackKey }}"
                    data-subject="{{ $lesson->subject_key }}"
                >
                    <h3>{{ $lesson->title }}</h3>
                    <p>{{ $lesson->level_label }} • {{ $lesson->subject_label }}</p>
                    @foreach ($lesson->assets as $asset)
                        <div class="teacher-dashboard-asset-row" data-teacher-delete-asset data-type="{{ $asset->part }}">
                            <span>{{ ucfirst($asset->part) }} • {{ strtoupper($asset->access_level ?? 'free') }}</span>
                            <form method="POST" action="{{ route('teacher.content.delete', ['locale' => $locale, 'asset' => $asset]) }}">
                                @csrf
                                <button class="secondary-btn" type="submit">{{ $teacherPageCopy['delete'] }}</button>
                            </form>
                        </div>
                    @endforeach
                </article>
            @endforeach
        </section>
    </main>
@endsection
