@extends('layouts.app')

@php
    $bodyClass = trim(($bodyClass ?? '').' payment-history-page');
    $user = $user ?? auth()->user();
    $historyCopy = match ($locale) {
        'ar' => [
            'title' => 'سجل الدفع',
            'subtitle' => 'ستجد هنا المدفوعات والولوجات التي تم تأكيدها لحسابك.',
            'current_access' => 'الولوج الحالي',
            'subscription' => 'الاشتراك',
            'level' => 'المستوى',
            'teacher' => 'الأستاذ',
            'subject' => 'المادة',
            'duration' => 'المدة',
            'expires' => 'تاريخ الانتهاء',
            'free' => 'مجاني',
            'premium' => 'بريميوم',
            'not_set' => 'غير محدد',
            'no_history_title' => 'لا يوجد اي دفع مسجل حاليا',
            'no_history_text' => 'المدفوعات التي يتم تأكيدها عبر واتساب ستظهر هنا.',
        ],
        'en' => [
            'title' => 'Payment history',
            'subtitle' => 'Find your confirmed payments and unlocked access here.',
            'current_access' => 'Current access',
            'subscription' => 'Subscription',
            'level' => 'Level',
            'teacher' => 'Teacher',
            'subject' => 'Subject',
            'duration' => 'Duration',
            'expires' => 'Expires',
            'free' => 'Free',
            'premium' => 'Premium',
            'not_set' => 'Not set',
            'no_history_title' => 'No payment recorded yet',
            'no_history_text' => 'Payments validated through WhatsApp will appear here.',
        ],
        default => [
            'title' => 'Historique des paiements',
            'subtitle' => 'Retrouvez ici vos paiements et vos accès validés.',
            'current_access' => 'Accès actuel',
            'subscription' => 'Abonnement',
            'level' => 'Niveau',
            'teacher' => 'Professeur',
            'subject' => 'Matière',
            'duration' => 'Durée',
            'expires' => 'Expiration',
            'free' => 'Gratuit',
            'premium' => 'Premium',
            'not_set' => 'Non défini',
            'no_history_title' => 'Aucun paiement enregistré pour le moment',
            'no_history_text' => 'Les paiements validés par WhatsApp apparaîtront ici.',
        ],
    };

    $durationLabels = [
        '1_month' => $locale === 'ar' ? 'شهر واحد' : ($locale === 'en' ? '1 month' : '1 mois'),
        '6_months' => $locale === 'ar' ? '6 اشهر' : ($locale === 'en' ? '6 months' : '6 mois'),
        '1_year' => $locale === 'ar' ? 'سنة واحدة' : ($locale === 'en' ? '1 year' : '1 an'),
    ];

    $subscriptionTier = $user?->subscription_tier === 'premium' ? 'premium' : 'free';
    $premiumLevelLabel = filled($user?->premium_level_key)
        ? str_replace(['::', '|', '-'], [' / ', ' + ', ' '], $user->premium_level_key)
        : $historyCopy['not_set'];
    $premiumTeacherLabel = filled($user?->premium_teacher_id)
        ? (\App\Models\User::where('role', 'teacher')->find($user->premium_teacher_id)?->name ?? $historyCopy['not_set'])
        : $historyCopy['not_set'];
    $subjectLabels = [
        'math' => $locale === 'ar' ? 'الرياضيات' : ($locale === 'en' ? 'Mathematics' : 'Mathématiques'),
        'physics' => $locale === 'ar' ? 'الفيزياء' : ($locale === 'en' ? 'Physics' : 'Physique'),
        'chemistry' => $locale === 'ar' ? 'الكيمياء' : ($locale === 'en' ? 'Chemistry' : 'Chimie'),
        'svt' => $locale === 'ar' ? 'علوم الحياة والأرض' : ($locale === 'en' ? 'Life and Earth Sciences' : 'SVT'),
        'french' => $locale === 'ar' ? 'الفرنسية' : ($locale === 'en' ? 'French' : 'Français'),
        'english' => $locale === 'ar' ? 'الإنجليزية' : ($locale === 'en' ? 'English' : 'Anglais'),
        'philosophy' => $locale === 'ar' ? 'الفلسفة' : ($locale === 'en' ? 'Philosophy' : 'Philosophie'),
        'economy' => $locale === 'ar' ? 'الاقتصاد' : ($locale === 'en' ? 'Economics' : 'Économie générale'),
        'accounting' => $locale === 'ar' ? 'المحاسبة' : ($locale === 'en' ? 'Accounting' : 'Comptabilité'),
        'organization' => $locale === 'ar' ? 'التنظيم الإداري' : ($locale === 'en' ? 'Business organization' : 'Organisation administrative'),
    ];
    $premiumSubjectLabel = filled($user?->premium_subject_key)
        ? ($subjectLabels[$user->premium_subject_key] ?? $user->premium_subject_key)
        : $historyCopy['not_set'];
@endphp

@section('content')
    <main class="student-shell">
        @include('partials.student-topbar', [
            'topbarTitle' => $historyCopy['title'],
            'routeName' => 'payment.history.locale',
            'brandHref' => route('teacher.index.locale', ['locale' => $locale]),
        ])

        <section class="student-page-card student-page-card--wide">
            <div class="student-page-head">
                <h1>{{ $historyCopy['title'] }}</h1>
                <p>{{ $historyCopy['subtitle'] }}</p>
            </div>

            <article class="admin-account-card">
                <div class="admin-account-card__summary">
                    <div class="admin-account-card__identity">
                        <strong>{{ $historyCopy['current_access'] }}</strong>
                        <span>{{ $user?->name }}</span>
                    </div>

                    <div class="admin-account-card__meta">
                        <span>
                            {{ $historyCopy['subscription'] }}
                            <strong>{{ $historyCopy[$subscriptionTier] }}</strong>
                        </span>
                        <span>
                            {{ $historyCopy['level'] }}
                            <strong>{{ $premiumLevelLabel }}</strong>
                        </span>
                        <span>
                            {{ $historyCopy['teacher'] }}
                            <strong>{{ $premiumTeacherLabel }}</strong>
                        </span>
                        <span>
                            {{ $historyCopy['subject'] }}
                            <strong>{{ $premiumSubjectLabel }}</strong>
                        </span>
                        <span>
                            {{ $historyCopy['duration'] }}
                            <strong>{{ $durationLabels[$user?->premium_duration] ?? $historyCopy['not_set'] }}</strong>
                        </span>
                        <span>
                            {{ $historyCopy['expires'] }}
                            <strong>{{ $user?->premium_expires_at?->format('Y-m-d') ?? '-' }}</strong>
                        </span>
                    </div>
                </div>
            </article>

            <div class="student-info-note">
                <strong>{{ $historyCopy['no_history_title'] }}</strong>
                <p>{{ $historyCopy['no_history_text'] }}</p>
            </div>
        </section>

        @include('partials.student-footer')
    </main>
@endsection
