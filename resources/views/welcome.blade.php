@extends('layouts.app')

@php
    $copy = match ($locale) {
        'ar' => [
            'nav_levels' => 'المستويات',
            'nav_how' => 'طريقة العمل',
            'nav_courses' => 'الدروس',
            'nav_reviews' => 'الآراء',
            'announcement' => 'منصة تعليمية حديثة للتعلم المنظم والتقدم الواضح',
            'title' => 'EasyPsi - لأن التعليم يستحق أن يكون سهلاً',
            'subtitle' => 'منصة تعليمية تساعد كل تلميذ على التقدم باختيار الأستاذ المناسب والمادة المناسبة والمسار المناسب.',
            'description' => 'شروحات واضحة، محتوى منظم، فيديوهات مركزة، تمارين متدرجة واختبارات تساعد على الفهم الجيد والتقدم بثقة.',
            'primary' => 'ابدأ الآن',
            'register' => 'أنشئ حسابك الآن',
            'secondary' => 'اختر الأستاذ',
            'space' => 'مساحتي',
            'stat_1_value' => '12+',
            'stat_1_label' => 'سنة خبرة',
            'stat_2_value' => '3',
            'stat_2_label' => 'لغات متاحة',
            'stat_3_value' => '100%',
            'stat_3_label' => 'تعلم منظم',
            'point_1' => 'اختيار الأستاذ حسب المستوى والمادة',
            'point_2' => 'فيديوهات وتمارين واختبارات في نفس المسار',
            'point_3' => 'تقدم أوضح للتلميذ وللأسرة',
            'levels_title' => 'مواكبة مناسبة لكل مستوى دراسي',
            'levels_subtitle' => 'كل مستوى يتقدم بإطار واضح، محفز وطموح.',
            'levels_pills' => ['الجذع المشترك', 'الأولى باك', 'الثانية باك', 'ما بعد الباك'],
            'journey_title' => 'كيف يتقدم التلميذ',
            'journey_subtitle' => 'مسار بسيط يساعد التلميذ على الفهم والتدرب والتحسن المستمر.',
            'step_1' => 'اختيار المستوى',
            'step_2' => 'فهم الدرس',
            'step_3' => 'التدرب والتحقق',
            'featured_title' => 'أمثلة من الدروس المتاحة',
            'featured_subtitle' => 'نفس المنهج الدراسي مع عرض واضح للتقدم وتنظيم المحتوى.',
            'reviews_title' => 'ما الذي يبحث عنه التلاميذ والأسر',
            'reviews_subtitle' => 'وضوح، تنظيم، ونتائج أفضل في جو تعليمي مطمئن.',
            'review_1' => 'أحب أن أراجع الدرس بهدوء ثم أنتقل مباشرة إلى التمارين لأتأكد أنني فهمت.',
            'review_2' => 'التقدم درساً بعد درس يساعدني على تنظيم المراجعة والاستمرار بانتظام.',
            'review_3' => 'نبحث قبل كل شيء عن مواكبة جادة وواضحة ومطمئنة لمساعدة ابننا على التقدم.',
            'cta_title' => 'ابدأ مع الأستاذ الذي يناسبك',
            'cta_body' => 'اختر مستواك ومادتك ثم استكشف الأساتذة والمحتويات المتاحة بسهولة.',
            'cta_primary' => 'الدخول إلى الأساتذة',
            'cta_secondary' => 'واتساب',
            'progression' => 'التقدم',
            'student_meta' => 'تلميذة',
            'student_meta_boy' => 'تلميذ',
            'parent_meta' => 'ولي أمر',
        ],
        'en' => [
            'nav_levels' => 'Levels',
            'nav_how' => 'How it works',
            'nav_courses' => 'Courses',
            'nav_reviews' => 'Reviews',
            'announcement' => 'A modern education platform for clear and structured progress',
            'title' => 'EasyPsi - Because education deserves to be easy',
            'subtitle' => 'An education platform that helps each student progress by choosing the right teacher, subject, and learning path.',
            'description' => 'Clear explanations, organized content, focused videos, progressive exercises, and quizzes that build confidence and results.',
            'primary' => 'Start now',
            'register' => 'Create your account',
            'secondary' => 'Choose a teacher',
            'space' => 'My space',
            'stat_1_value' => '12+',
            'stat_1_label' => 'Years of experience',
            'stat_2_value' => '3',
            'stat_2_label' => 'Languages available',
            'stat_3_value' => '100%',
            'stat_3_label' => 'Structured learning',
            'point_1' => 'Choose the teacher by level and subject',
            'point_2' => 'Videos, exercises, and quizzes in one path',
            'point_3' => 'Clearer progress for students and families',
            'levels_title' => 'Support adapted to each school level',
            'levels_subtitle' => 'Each level moves forward with a clear, motivating, and ambitious framework.',
            'levels_pills' => ['Common core', '1st bac', '2nd bac', 'Post-bac'],
            'journey_title' => 'How the student progresses',
            'journey_subtitle' => 'A simple path that helps the student understand, practice, and improve consistently.',
            'step_1' => 'Choose the level',
            'step_2' => 'Understand the course',
            'step_3' => 'Practice and validate',
            'featured_title' => 'Examples of available lessons',
            'featured_subtitle' => 'The same curriculum, with clear progression and organized content.',
            'reviews_title' => 'What students and families are looking for',
            'reviews_subtitle' => 'Clarity, structure, and better results in a reassuring learning environment.',
            'review_1' => 'I like reviewing the lesson calmly, then moving straight to exercises to make sure I understood.',
            'review_2' => 'Moving chapter by chapter helps me organize my revision and stay consistent.',
            'review_3' => 'We mainly look for serious, clear, and reassuring support to help our child progress.',
            'cta_title' => 'Start with the teacher that fits you',
            'cta_body' => 'Choose your level and subject, then browse available teachers and content easily.',
            'cta_primary' => 'Go to teachers',
            'cta_secondary' => 'WhatsApp',
            'progression' => 'Progression',
            'student_meta' => 'Student',
            'student_meta_boy' => 'Student',
            'parent_meta' => 'Parent',
        ],
        default => [
            'nav_levels' => 'Niveaux',
            'nav_how' => 'Fonctionnement',
            'nav_courses' => 'Cours',
            'nav_reviews' => 'Avis',
            'announcement' => 'Une plateforme d’éducation moderne pour apprendre avec méthode',
            'title' => 'EasyPsi - Parce que l’éducation mérite d’être facile',
            'subtitle' => 'Une plateforme éducative qui aide chaque élève à progresser en choisissant le bon professeur, la bonne matière et le bon parcours.',
            'description' => 'Des explications claires, un contenu structuré, des vidéos ciblées, des exercices progressifs et des quiz qui renforcent la confiance et les résultats.',
            'primary' => 'Commencer maintenant',
            'register' => 'S’inscrire maintenant',
            'secondary' => 'Choisir le professeur',
            'space' => 'Mon espace',
            'stat_1_value' => '12+',
            'stat_1_label' => 'ans d’expérience',
            'stat_2_value' => '3',
            'stat_2_label' => 'langues disponibles',
            'stat_3_value' => '100%',
            'stat_3_label' => 'apprentissage structuré',
            'point_1' => 'Cours classés par niveau et par filière',
            'point_2' => 'Exercices pour renforcer la méthode',
            'point_3' => 'Quiz pour valider les acquis',
            'levels_title' => 'Un accompagnement adapté à chaque niveau scolaire',
            'levels_subtitle' => 'Chaque niveau avance avec un cadre clair, motivant et ambitieux.',
            'levels_pills' => ['Tronc commun', '1ère bac', '2ème bac', 'Post-bac'],
            'journey_title' => 'Comment l’élève progresse',
            'journey_subtitle' => 'Un parcours simple pour comprendre, s’exercer et progresser avec régularité.',
            'step_1' => 'Choisir son niveau',
            'step_2' => 'Comprendre le cours',
            'step_3' => 'S’exercer et valider',
            'featured_title' => 'Des exemples de cours disponibles',
            'featured_subtitle' => 'Le même programme, avec une progression lisible et un contenu bien organisé.',
            'reviews_title' => 'Ce que les élèves et les familles recherchent',
            'reviews_subtitle' => 'De la clarté, de la méthode et de meilleurs résultats dans un cadre rassurant.',
            'review_1' => 'J’aime pouvoir revoir le cours calmement puis passer directement aux exercices pour vérifier si j’ai compris.',
            'review_2' => 'Le fait d’avancer chapitre par chapitre m’aide à mieux organiser mes révisions et à rester régulier.',
            'review_3' => 'Nous cherchons surtout un accompagnement sérieux, clair et rassurant pour soutenir la progression de notre enfant.',
            'cta_title' => 'Commencez avec le professeur qui vous correspond',
            'cta_body' => 'Choisissez votre niveau et votre matière, puis explorez les professeurs et les contenus disponibles en quelques clics.',
            'cta_primary' => 'Accéder aux professeurs',
            'cta_secondary' => 'WhatsApp',
            'progression' => 'Progression',
            'student_meta' => 'Élève',
            'student_meta_boy' => 'Élève',
            'parent_meta' => 'Parent d’élève',
        ],
    };

    $journeySteps = [
        ['number' => '01', 'title' => $copy['step_1']],
        ['number' => '02', 'title' => $copy['step_2']],
        ['number' => '03', 'title' => $copy['step_3']],
    ];

    $featuredCourses = [
        ['label' => 'Tronc commun', 'title' => 'La gravitation universelle', 'teacher' => 'EasyPsi', 'track' => 'Tronc commun', 'rating' => '4.9/5', 'focus' => 'Bases solides', 'progress' => 0],
        ['label' => '1ère bac', 'title' => 'Rotation d’un solide autour d’un axe fixe', 'teacher' => 'EasyPsi', 'track' => '1ère bac', 'rating' => '4.8/5', 'focus' => 'Sciences expérimentales', 'progress' => 0],
        ['label' => '2ème bac', 'title' => 'Ondes mécaniques progressives', 'teacher' => 'EasyPsi', 'track' => '2ème bac', 'rating' => '4.9/5', 'focus' => 'Sciences maths', 'progress' => 0],
        ['label' => 'Post-bac', 'title' => 'Stratégie ENSAM', 'teacher' => 'EasyPsi', 'track' => 'Post-bac', 'rating' => '4.7/5', 'focus' => 'Préparation concours', 'progress' => 0],
    ];

    $testimonials = [
        ['text' => $copy['review_1'], 'author' => 'Salma', 'meta' => $copy['student_meta']],
        ['text' => $copy['review_2'], 'author' => 'Yassine', 'meta' => $copy['student_meta_boy']],
        ['text' => $copy['review_3'], 'author' => $copy['parent_meta'], 'meta' => 'EasyPsi'],
    ];

    $spaceUrl = auth()->check()
        ? (auth()->user()->role === 'teacher'
            ? route('teacher.space.locale', ['locale' => $locale])
            : route('teacher.index.locale', ['locale' => $locale]))
        : route('login.locale', ['locale' => $locale]);

    $registerUrl = auth()->check()
        ? $spaceUrl
        : route('register.locale', ['locale' => $locale]);
@endphp

@section('content')
    <main class="page-shell welcome-masterpiece">
        <div class="welcome-masterpiece__shell">
            <header class="glass-card welcome-masterpiece__nav">
                <a class="welcome-masterpiece__brand" href="{{ route('welcome.locale', ['locale' => $locale]) }}">
                    <img src="{{ asset('logo.jpeg') }}" alt="EasyPsi logo" class="welcome-masterpiece__brand-logo">
                    <span class="welcome-masterpiece__brand-copy">
                        <strong>EasyPsi</strong>
                    </span>
                </a>

                <nav class="welcome-masterpiece__menu">
                    <a href="#levels">{{ $copy['nav_levels'] }}</a>
                    <a href="#journey">{{ $copy['nav_how'] }}</a>
                    <a href="#featured">{{ $copy['nav_courses'] }}</a>
                    <a href="#reviews">{{ $copy['nav_reviews'] }}</a>
                </nav>

                <div class="welcome-masterpiece__actions">
                    <a class="whatsapp-btn welcome-masterpiece__whatsapp" href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noreferrer">WhatsApp</a>
                    @include('partials.locale-switcher', ['routeName' => 'welcome.locale'])
                    <a class="secondary-btn welcome-masterpiece__login" href="{{ $spaceUrl }}">{{ $copy['space'] }}</a>
                    <a class="primary-btn welcome-masterpiece__nav-cta" href="{{ $registerUrl }}">{{ $copy['primary'] }}</a>
                </div>
            </header>

            <section class="welcome-masterpiece__hero">
                <div class="welcome-masterpiece__hero-copy">
                    <span class="brand-chip">{{ $copy['announcement'] }}</span>
                    <h1>{{ $copy['title'] }}</h1>
                    <h2>{{ $copy['subtitle'] }}</h2>

                    <div class="welcome-masterpiece__hero-description">
                        <p>{{ $copy['description'] }}</p>
                    </div>

                    <div class="student-hero-actions">
                        <a class="primary-btn" href="{{ $registerUrl }}">{{ $copy['primary'] }}</a>
                        <a class="secondary-btn" href="{{ route('teacher.index.locale', ['locale' => $locale]) }}">{{ $copy['secondary'] }}</a>
                    </div>

                    <div class="welcome-masterpiece__hero-stats">
                        <article class="welcome-masterpiece__hero-stat">
                            <strong>{{ $copy['stat_1_value'] }}</strong>
                            <span>{{ $copy['stat_1_label'] }}</span>
                        </article>
                        <article class="welcome-masterpiece__hero-stat">
                            <strong>{{ $copy['stat_2_value'] }}</strong>
                            <span>{{ $copy['stat_2_label'] }}</span>
                        </article>
                        <article class="welcome-masterpiece__hero-stat">
                            <strong>{{ $copy['stat_3_value'] }}</strong>
                            <span>{{ $copy['stat_3_label'] }}</span>
                        </article>
                    </div>

                </div>

                <div class="welcome-masterpiece__hero-visual">
                    <div class="welcome-course-preview">
                        <img src="{{ asset('images/welcome-course-preview.png') }}" alt="EasyPsi course preview" class="welcome-course-preview__image">
                        <div class="welcome-course-preview__glow"></div>
                    </div>
                </div>
            </section>

            <section id="levels" class="welcome-masterpiece__section">
                <div class="welcome-editorial">
                    <div class="welcome-editorial__copy welcome-editorial__copy--spotlight welcome-scroll-reveal welcome-scroll-reveal--left welcome-text-reveal">
                        <h2>{{ $copy['levels_title'] }}</h2>
                        <p>{{ $copy['levels_subtitle'] }}</p>
                        <ul class="welcome-level-pills">
                            @foreach ($copy['levels_pills'] as $pill)
                                <li class="welcome-level-pills__item">
                                    <span class="welcome-level-pills__dot"></span>
                                    <span>{{ $pill }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="welcome-editorial__visual welcome-editorial__visual--levels welcome-scroll-reveal welcome-scroll-reveal--right">
                        <div class="welcome-editorial__image-shell">
                            <img src="{{ asset('images/remote-student-1.png') }}" alt="Étudiant en apprentissage à distance" class="welcome-editorial__image">
                        </div>
                    </div>
                </div>
            </section>

            <section id="journey" class="welcome-masterpiece__section">
                <div class="welcome-masterpiece__section-head welcome-scroll-reveal welcome-text-reveal">
                    <h2>{{ $copy['journey_title'] }}</h2>
                    <p>{{ $copy['journey_subtitle'] }}</p>
                </div>

                <div class="welcome-learning-grid">
                    @foreach ($journeySteps as $step)
                        <article class="glass-card welcome-learning-step welcome-scroll-reveal">
                            <div class="welcome-learning-step__inline">
                                <span class="welcome-learning-step__number">{{ $step['number'] }}</span>
                                <h3>{{ $step['title'] }}</h3>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section id="featured" class="welcome-masterpiece__section">
                <div class="student-page-head">
                    <h2>
                        @if ($locale === 'ar')
                            دروس مختارة لتوضيح تقدم التلميذ
                        @elseif ($locale === 'en')
                            Selected lessons to illustrate student progress
                        @else
                            Des cours choisis pour illustrer la progression de l’élève
                        @endif
                    </h2>
                </div>

                <div class="teacher-directory-grid">
                    @foreach ($featuredCourses as $course)
                        <article class="glass-card payment-plan-card">
                            <div class="lesson-card-banner {{ $loop->even ? 'lesson-card-banner--orange' : ($loop->iteration === 4 ? 'lesson-card-banner--gold' : '') }}">
                                <div class="lesson-card-banner__content">
                                    <span class="lesson-card-banner__badge">{{ $course['label'] }}</span>
                                    <strong>{{ $course['rating'] }}</strong>
                                    <p>{{ $course['focus'] }}</p>
                                </div>
                            </div>
                            <h3>{{ $course['title'] }}</h3>
                            <p>{{ $course['track'] }}</p>
                            <div class="lesson-progress">
                                <div class="lesson-progress__meta">
                                    <span>{{ $copy['progression'] }}</span>
                                    <strong>{{ $course['progress'] }}%</strong>
                                </div>
                                <div class="lesson-progress__track">
                                    <span style="width: {{ $course['progress'] }}%;"></span>
                                </div>
                            </div>
                            <a class="secondary-btn" href="{{ route('teacher.index.locale', ['locale' => $locale]) }}">
                                @if ($locale === 'ar')
                                    الوصول إلى الدرس
                                @elseif ($locale === 'en')
                                    Access the course
                                @else
                                    Accéder au cours
                                @endif
                            </a>
                        </article>
                    @endforeach
                </div>
            </section>
            <section id="reviews" class="welcome-masterpiece__section">
                <div class="welcome-masterpiece__section-head welcome-scroll-reveal welcome-text-reveal">
                    <h2>{{ $copy['reviews_title'] }}</h2>
                </div>

                <div class="teacher-directory-grid">
                    @foreach ($testimonials as $review)
                        <article class="glass-card payment-plan-card welcome-scroll-reveal">
                            <div class="rating-stars">★★★★★</div>
                            <p>"{{ $review['text'] }}"</p>
                            <h3>{{ $review['author'] }}</h3>
                            <span>{{ $review['meta'] }}</span>
                        </article>
                    @endforeach
                </div>
            </section>

            <footer class="welcome-masterpiece__footer">
                <div class="welcome-masterpiece__footer-grid">
                    <div class="welcome-masterpiece__footer-brand">
                        <img src="{{ asset('logo.jpeg') }}" alt="EasyPsi logo">
                        <div>
                            <strong>EasyPsi</strong>
                        </div>
                    </div>

                    <div>
                        <h3>{{ $locale === 'ar' ? 'التنقل' : ($locale === 'en' ? 'Navigation' : 'Navigation') }}</h3>
                        <ul>
                            <li><a href="{{ route('welcome.locale', ['locale' => $locale]) }}">{{ $locale === 'ar' ? 'الرئيسية' : ($locale === 'en' ? 'Home' : 'Accueil') }}</a></li>
                            <li><a href="{{ route('login.locale', ['locale' => $locale]) }}">{{ $locale === 'ar' ? 'تسجيل الدخول' : ($locale === 'en' ? 'Login' : 'Connexion') }}</a></li>
                            <li><a href="{{ route('register.locale', ['locale' => $locale]) }}">{{ $locale === 'ar' ? 'إنشاء حساب' : ($locale === 'en' ? 'Create account' : 'Créer un compte') }}</a></li>
                            <li><a href="{{ route('payment.locale', ['locale' => $locale]) }}">{{ $locale === 'ar' ? 'بريميوم' : 'Premium' }}</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3>{{ $locale === 'ar' ? 'المسارات' : ($locale === 'en' ? 'Tracks' : 'Parcours') }}</h3>
                        <ul>
                            <li>Tronc commun</li>
                            <li>1ère bac</li>
                            <li>2ème bac</li>
                            <li>Post-bac</li>
                        </ul>
                    </div>

                    <div>
                        <h3>{{ $locale === 'ar' ? 'التواصل' : ($locale === 'en' ? 'Contact' : 'Contact') }}</h3>
                        <ul>
                            <li><a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noreferrer">WhatsApp</a></li>
                            <li><a href="mailto:contact@easypsi.ma">Email</a></li>
                            <li><a href="{{ $registerUrl }}">{{ $locale === 'ar' ? 'ابدأ' : ($locale === 'en' ? 'Start' : 'Commencer') }}</a></li>
                        </ul>
                    </div>
                </div>
            </footer>
        </div>
    </main>
@endsection

