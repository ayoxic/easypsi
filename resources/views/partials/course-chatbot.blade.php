@php
    $chatLocale = $locale ?? 'fr';
    $chatCopy = match ($chatLocale) {
        'ar' => [
            'label' => 'مساعد الدرس',
            'welcome' => 'مرحباً! اسألني عن هذا الدرس وسأجيب من محتوى الأستاذ.',
            'placeholder' => 'اكتب سؤالك…',
            'send' => 'إرسال',
            'close' => 'إغلاق',
            'open' => 'فتح المساعد',
            'fallback' => 'لم أجد الإجابة في محتوى الدرس. جرّب صياغة أخرى أو شاهد الفيديو والوصف.',
            'suggest_summary' => 'لخّص الدرس',
            'suggest_quiz' => 'هل يوجد اختبار؟',
            'suggest_file' => 'ملف الدعم',
            'source_course' => 'من الوصف',
            'source_support' => 'من الدعم',
            'source_quiz' => 'من الاختبار',
            'premium_note' => 'هذا المحتوى يتطلب اشتراكاً مميزاً لعرض التفاصيل الكاملة.',
        ],
        'en' => [
            'label' => 'Lesson assistant',
            'welcome' => 'Hi! Ask me about this lesson and I will answer from the teacher’s content.',
            'placeholder' => 'Type your question…',
            'send' => 'Send',
            'close' => 'Close',
            'open' => 'Open assistant',
            'fallback' => 'I could not find the answer in the lesson content. Try rephrasing, or check the video and description.',
            'suggest_summary' => 'Summarize the lesson',
            'suggest_quiz' => 'Is there a quiz?',
            'suggest_file' => 'Support file',
            'source_course' => 'From the description',
            'source_support' => 'From the support notes',
            'source_quiz' => 'From the quiz',
            'premium_note' => 'This content requires a premium subscription to see full details.',
        ],
        default => [
            'label' => 'Assistant du cours',
            'welcome' => 'Salut ! Pose-moi une question sur ce cours, je réponds à partir du contenu du professeur.',
            'placeholder' => 'Écris ta question…',
            'send' => 'Envoyer',
            'close' => 'Fermer',
            'open' => 'Ouvrir l’assistant',
            'fallback' => 'Je n’ai pas trouvé la réponse dans le contenu du cours. Reformule ou regarde la vidéo et la description.',
            'suggest_summary' => 'Résumer le cours',
            'suggest_quiz' => 'Y a-t-il un quiz ?',
            'suggest_file' => 'Fichier support',
            'source_course' => 'D’après la description',
            'source_support' => 'D’après le support',
            'source_quiz' => 'D’après le quiz',
            'premium_note' => 'Ce contenu demande un accès premium pour voir tous les détails.',
        ],
    };

    $chatQuizQuestions = [];
    $rawQuiz = $activeAsset->quiz_body ?? null;
    if (is_string($rawQuiz) && $rawQuiz !== '') {
        $decodedQuiz = json_decode($rawQuiz, true);
        if (is_array($decodedQuiz)) {
            $rawQuiz = $decodedQuiz;
        }
    }
    if (is_array($rawQuiz)) {
        foreach ((array) ($rawQuiz['questions'] ?? []) as $q) {
            $text = trim((string) ($q['question'] ?? ''));
            if ($text !== '') {
                $chatQuizQuestions[] = $text;
            }
        }
    }

    $chatContext = [
        'locale' => $chatLocale,
        'title' => $activeLesson->title ?? '',
        'teacher' => $teacherUser->name ?? '',
        'part' => $activePart ?? 'course',
        'locked' => (bool) ($locked ?? false),
        'description' => (string) ($activeAsset->description_body ?? ''),
        'support' => (string) ($activeAsset->support_body ?? ''),
        'hasFile' => filled($activeAsset->support_file_path ?? null),
        'quiz' => $chatQuizQuestions,
        'strings' => $chatCopy,
    ];
@endphp

<div class="course-chat" data-course-chatbot data-endpoint="{{ route('course.chat', ['locale' => $chatLocale]) }}" data-csrf="{{ csrf_token() }}">
    <script type="application/json" data-course-chat-context>{!! str_replace('</', '<\/', json_encode($chatContext, JSON_UNESCAPED_UNICODE)) !!}</script>
    <button class="course-chat__fab" type="button" data-course-chat-toggle aria-expanded="false" aria-label="{{ $chatCopy['open'] }}">
        <svg viewBox="0 0 48 48" width="30" height="30" aria-hidden="true"><line x1="24" y1="15" x2="24" y2="8" stroke="#0b4ea2" stroke-width="3" stroke-linecap="round"/><circle cx="24" cy="6" r="3.4" fill="#ff8a00"/><rect x="4" y="22" width="5" height="9" rx="2.5" fill="#0b4ea2"/><rect x="39" y="22" width="5" height="9" rx="2.5" fill="#0b4ea2"/><rect x="8" y="14" width="32" height="25" rx="10" fill="#0b4ea2"/><circle cx="17.5" cy="24.5" r="3.4" fill="#fff"/><circle cx="30.5" cy="24.5" r="3.4" fill="#fff"/><circle cx="17.5" cy="24.5" r="1.6" fill="#172554"/><circle cx="30.5" cy="24.5" r="1.6" fill="#172554"/><path d="M18 31.5q6 5 12 0" stroke="#ff8a00" stroke-width="2.6" fill="none" stroke-linecap="round"/></svg>
    </button>

    <section class="course-chat__panel" data-course-chat-panel hidden aria-label="{{ $chatCopy['label'] }}">
        <header class="course-chat__head">
            <span class="course-chat__identity">
                <span class="course-chat__avatar" aria-hidden="true"><svg viewBox="0 0 48 48" width="22" height="22" aria-hidden="true"><line x1="24" y1="15" x2="24" y2="8" stroke="#0b4ea2" stroke-width="3.4" stroke-linecap="round"/><circle cx="24" cy="6" r="3.4" fill="#ff8a00"/><rect x="4" y="22" width="5" height="9" rx="2.5" fill="#0b4ea2"/><rect x="39" y="22" width="5" height="9" rx="2.5" fill="#0b4ea2"/><rect x="8" y="14" width="32" height="25" rx="10" fill="#0b4ea2"/><circle cx="17.5" cy="24.5" r="3.4" fill="#fff"/><circle cx="30.5" cy="24.5" r="3.4" fill="#fff"/><circle cx="17.5" cy="24.5" r="1.6" fill="#172554"/><circle cx="30.5" cy="24.5" r="1.6" fill="#172554"/><path d="M18 31.5q6 5 12 0" stroke="#ff8a00" stroke-width="2.8" fill="none" stroke-linecap="round"/></svg></span>
                <span class="course-chat__title">{{ $chatCopy['label'] }}</span>
            </span>
            <button class="course-chat__close" type="button" data-course-chat-toggle aria-label="{{ $chatCopy['close'] }}">&times;</button>
        </header>

        <div class="course-chat__messages" data-course-chat-messages role="log" aria-live="polite"></div>

        <div class="course-chat__suggests" data-course-chat-suggests>
            <button type="button" data-course-chat-ask="summary">{{ $chatCopy['suggest_summary'] }}</button>
            <button type="button" data-course-chat-ask="quiz">{{ $chatCopy['suggest_quiz'] }}</button>
            <button type="button" data-course-chat-ask="file">{{ $chatCopy['suggest_file'] }}</button>
        </div>

        <form class="course-chat__form" data-course-chat-form>
            <input type="text" name="message" placeholder="{{ $chatCopy['placeholder'] }}" autocomplete="off" maxlength="500" data-course-chat-input>
            <button class="course-chat__send" type="submit">{{ $chatCopy['send'] }}</button>
        </form>
    </section>
</div>
