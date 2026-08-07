@extends('layouts.app')

@php
    $bodyClass = 'teacher-space-page';

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
        ])

        @if (session('status'))
            <div class="form-flash teacher-space-flash">{{ session('status') }}</div>
        @endif

        <section class="student-page-card teacher-space-card">
            <div class="teacher-space-heading">
                <span class="brand-chip">{{ $teacherPageCopy['teacher_space'] }}</span>
                <h1>Gestion des cours du professeur</h1>
                <p>Vous pouvez choisir un titre existant pour le compléter ou écrire un nouveau titre pour créer un nouveau cours.</p>
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

                        <label class="student-form-field">
                            <span>Filière</span>
                            <select name="track" data-teacher-track-select>
                                <option value="">Choisissez la filière</option>
                            </select>
                        </label>

                        <label class="student-form-field">
                            <span>Matière</span>
                            <select name="subject" data-teacher-subject-select>
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

        <section class="teacher-card-grid">
            @foreach ($teacherLessons as $lesson)
                <article class="payment-plan-card">
                    <h3>{{ $lesson->title }}</h3>
                    <p>{{ $lesson->level_label }} • {{ $lesson->subject_label }}</p>
                    @foreach ($lesson->assets as $asset)
                        <div class="teacher-dashboard-asset-row">
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
