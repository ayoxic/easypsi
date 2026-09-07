@extends('layouts.app')

@php
    $text = match ($locale) {
        'ar' => [
            'title' => 'إدارة الطلاب والاشتراكات',
            'subtitle' => 'هذه الصفحة مخصصة لمتابعة حسابات الطلاب وتفعيل الاشتراكات فقط.',
            'students' => 'الطلاب',
            'premium' => 'بريميوم النشط',
            'directory' => 'صفحة الأساتذة',
            'search_student' => 'ابحث عن طالب بالاسم أو البريد أو الهاتف',
            'search' => 'بحث',
            'subscription' => 'الاشتراك الحالي',
            'current_level' => 'المستوى المسموح',
            'current_duration' => 'المدة الحالية',
            'expires_at' => 'نهاية الاشتراك',
            'duration' => 'مدة بريميوم',
            'duration_1_month' => 'شهر واحد',
            'duration_6_months' => '6 أشهر',
            'duration_1_year' => 'سنة واحدة',
            'level_scope' => 'المستوى المسموح',
            'computed_end' => 'تاريخ النهاية المحسوب',
            'today_base' => 'يبدأ الحساب من اليوم',
            'not_set' => 'غير محدد',
            'free' => 'مجاني',
            'premium_label' => 'بريميوم',
            'update_access' => 'تحديث',
            'no_students' => 'لا يوجد طلاب حالياً',
            'status' => 'الحالة',
            'phone' => 'الهاتف',
        ],
        'en' => [
            'title' => 'Student and subscription management',
            'subtitle' => 'This page is only for monitoring students and managing subscriptions.',
            'students' => 'Students',
            'premium' => 'Active premium',
            'directory' => 'Teachers page',
            'search_student' => 'Search a student by name, email, or phone',
            'search' => 'Search',
            'subscription' => 'Current subscription',
            'current_level' => 'Allowed level',
            'current_duration' => 'Current duration',
            'expires_at' => 'End date',
            'duration' => 'Premium duration',
            'duration_1_month' => '1 month',
            'duration_6_months' => '6 months',
            'duration_1_year' => '1 year',
            'level_scope' => 'Allowed level',
            'computed_end' => 'Calculated end date',
            'today_base' => 'Calculated from today',
            'not_set' => 'Not set',
            'free' => 'Free',
            'premium_label' => 'Premium',
            'update_access' => 'Update',
            'no_students' => 'No students found',
            'status' => 'Status',
            'phone' => 'Phone',
        ],
        default => [
            'title' => 'Gestion des étudiants et des abonnements',
            'subtitle' => 'Cette page sert uniquement à suivre les étudiants et gérer les abonnements.',
            'students' => 'Étudiants',
            'premium' => 'Premium actifs',
            'directory' => 'Page des professeurs',
            'search_student' => 'Rechercher un étudiant par nom, email ou téléphone',
            'search' => 'Rechercher',
            'subscription' => 'Abonnement actuel',
            'current_level' => 'Niveau autorisé',
            'current_duration' => 'Durée actuelle',
            'expires_at' => 'Fin d’abonnement',
            'duration' => 'Durée premium',
            'duration_1_month' => '1 mois',
            'duration_6_months' => '6 mois',
            'duration_1_year' => '1 an',
            'level_scope' => 'Niveau autorisé',
            'computed_end' => 'Date de fin calculée',
            'today_base' => 'Calculée à partir d’aujourd’hui',
            'not_set' => 'Non défini',
            'free' => 'Free',
            'premium_label' => 'Premium',
            'update_access' => 'Mettre à jour',
            'no_students' => 'Aucun étudiant trouvé',
            'status' => 'Statut',
            'phone' => 'Téléphone',
        ],
    };

    $durationLabelMap = [
        '1_month' => $text['duration_1_month'],
        '6_months' => $text['duration_6_months'],
        '1_year' => $text['duration_1_year'],
    ];

    $levelLabelMap = collect($levelOptions ?? [])->mapWithKeys(fn (array $levelOption): array => [
        $levelOption['key'] => $levelOption['label'],
    ])->all();

    $brandLabel = match ($locale) {
        'ar' => 'إدارة EasyPsi',
        'en' => 'EasyPsi Admin',
        default => 'Admin EasyPsi',
    };
@endphp

@section('content')
    <main class="page-shell admin-shell">
        <header class="admin-topbar glass-card">
            <div class="course-brand">
                <div>
                    <strong>{{ $brandLabel }}</strong>
                </div>
            </div>

            @include('partials.locale-switcher', ['routeName' => 'admin.locale'])

            <div class="topbar-actions">
                <a class="secondary-btn" href="{{ route('teacher.index.locale', ['locale' => $locale]) }}">{{ $text['directory'] }}</a>
            </div>
        </header>

        <section class="admin-hero glass-card">
            <div class="admin-hero-copy">
                <span class="brand-chip">{{ $brandLabel }}</span>
                <h1 class="section-title">{{ $text['title'] }}</h1>
                <p>{{ $text['subtitle'] }}</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    {{ $text['students'] }}
                    <strong>{{ $adminStats['students'] }}</strong>
                </div>
                <div class="stat-card">
                    {{ $text['premium'] }}
                    <strong>{{ $adminStats['premium'] }}</strong>
                </div>
            </div>
        </section>

        @if (session('status'))
            <div class="form-flash">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="form-errors">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section class="admin-grid admin-grid--dashboard">
            <article class="admin-panel admin-panel--full">
                <div class="admin-panel-heading admin-panel-heading--accounts">
                    <span class="admin-section-chip">{{ $text['students'] }}</span>
                    <h2 class="panel-title">{{ $text['title'] }}</h2>
                </div>

                <form class="admin-search" method="GET" action="{{ route('admin.locale', ['locale' => $locale]) }}">
                    <input type="text" name="student_search" value="{{ $studentSearch }}" placeholder="{{ $text['search_student'] }}">
                    <button class="secondary-btn" type="submit">{{ $text['search'] }}</button>
                </form>

                <div class="admin-account-list">
                    @forelse ($users as $user)
                        @php
                            $currentLevelLabel = $levelLabelMap[$user->premium_level_key] ?? $text['not_set'];
                            $currentDurationLabel = $durationLabelMap[$user->premium_duration] ?? $text['not_set'];
                            $selectedDuration = old('user_id') == $user->id ? old('premium_duration') : ($user->premium_duration ?? '');
                            $selectedScope = old('user_id') == $user->id ? old('premium_level_key') : ($user->premium_level_key ?? '');
                            $selectedTier = old('user_id') == $user->id ? old('subscription_tier') : ($user->subscription_tier ?? 'free');
                        @endphp

                        <article class="admin-account-card">
                            <div class="admin-account-card__summary">
                                <div class="admin-account-card__identity">
                                    <strong>{{ $user->name }}</strong>
                                    <span>{{ $user->email }}</span>
                                    <span>{{ $text['phone'] }} : {{ $user->phone ?: '-' }}</span>
                                </div>

                                <div class="admin-account-card__meta">
                                    <div class="admin-meta-chip">
                                        <span>{{ $text['subscription'] }}</span>
                                        <strong>{{ ucfirst($user->subscription_tier ?? 'free') }}</strong>
                                    </div>
                                    <div class="admin-meta-chip">
                                        <span>{{ $text['current_duration'] }}</span>
                                        <strong>{{ $currentDurationLabel }}</strong>
                                    </div>
                                    <div class="admin-meta-chip">
                                        <span>{{ $text['current_level'] }}</span>
                                        <strong>{{ $currentLevelLabel }}</strong>
                                    </div>
                                    <div class="admin-meta-chip">
                                        <span>{{ $text['expires_at'] }}</span>
                                        <strong>{{ $user->premium_expires_at?->format('Y-m-d') ?? '-' }}</strong>
                                    </div>
                                </div>
                            </div>

                            <form
                                class="admin-subscription-form"
                                method="POST"
                                action="{{ route('admin.users.subscription.update', ['locale' => $locale, 'user' => $user]) }}"
                                data-admin-subscription-form
                                data-today="{{ $todayDate }}"
                            >
                                @csrf
                                <input type="hidden" name="student_search" value="{{ $studentSearch }}">
                                <input type="hidden" name="user_id" value="{{ $user->id }}">

                                <div class="admin-subscription-form__grid">
                                    <label class="field">
                                        <span>{{ $text['subscription'] }}</span>
                                        <select name="subscription_tier" data-subscription-tier>
                                            <option value="free" @selected($selectedTier === 'free')>{{ $text['free'] }}</option>
                                            <option value="premium" @selected($selectedTier === 'premium')>{{ $text['premium_label'] }}</option>
                                        </select>
                                    </label>

                                    <label class="field" data-premium-duration-field>
                                        <span>{{ $text['duration'] }}</span>
                                        <select name="premium_duration" data-subscription-duration>
                                            <option value="">{{ $text['not_set'] }}</option>
                                            <option value="1_month" @selected($selectedDuration === '1_month')>{{ $text['duration_1_month'] }}</option>
                                            <option value="6_months" @selected($selectedDuration === '6_months')>{{ $text['duration_6_months'] }}</option>
                                            <option value="1_year" @selected($selectedDuration === '1_year')>{{ $text['duration_1_year'] }}</option>
                                        </select>
                                    </label>

                                    <label class="field" data-premium-level-field>
                                        <span>{{ $text['level_scope'] }}</span>
                                        <select name="premium_level_key" data-subscription-level>
                                            <option value="">{{ $text['not_set'] }}</option>
                                            @foreach ($levelOptions as $levelOption)
                                                <option value="{{ $levelOption['key'] }}" @selected($selectedScope === $levelOption['key'])>
                                                    {{ $levelOption['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>

                                    <div class="admin-subscription-preview" data-subscription-preview>
                                        <span>{{ $text['computed_end'] }}</span>
                                        <strong data-subscription-preview-date>{{ $user->premium_expires_at?->format('Y-m-d') ?? '-' }}</strong>
                                        <small>{{ $text['today_base'] }}</small>
                                    </div>
                                </div>

                                <div class="admin-subscription-form__footer">
                                    <button class="secondary-btn" type="submit">{{ $text['update_access'] }}</button>
                                </div>
                            </form>
                        </article>
                    @empty
                        <div class="admin-row admin-row-content">
                            <span>{{ $text['no_students'] }}</span>
                            <span>-</span>
                            <span>-</span>
                            <span class="ghost-pill">-</span>
                        </div>
                    @endforelse
                </div>
            </article>
        </section>
    </main>
@endsection
