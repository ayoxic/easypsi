@extends('layouts.app')

@php
    $whatsappBase = 'https://wa.me/' . $whatsappNumber;
    $paymentCopy = match ($locale) {
        'fr' => [
            'title' => 'Paiement et abonnement',
            'subtitle' => 'Choisissez la formule qui vous convient pour accéder aux cours.',
            'monthly' => 'Mensuelle',
            'semiannual' => '6 mois',
            'annual' => 'Annuelle',
            'cta' => 'Choisir cette formule',
            'note' => 'Après votre choix, vous pouvez envoyer votre preuve de paiement via WhatsApp.',
            'tronc_commun' => 'Tarifs tronc commun',
            'tronc_commun_note' => 'Ces tarifs concernent le tronc commun ainsi que 1AC, 2AC et 3AC.',
            'autres_niveaux' => 'Tarifs autres niveaux',
            'autres_niveaux_note' => 'Ces tarifs concernent 1ère bac, 2ème bac et préparation de concours.',
        ],
        'ar' => [
            'title' => 'الدفع والاشتراك',
            'subtitle' => 'اختر الصيغة المناسبة للوصول الى الدروس.',
            'monthly' => 'شهرية',
            'semiannual' => '6 اشهر',
            'annual' => 'سنوية',
            'cta' => 'اختيار هذه الصيغة',
            'note' => 'بعد الاختيار يمكنك ارسال اثبات الدفع عبر واتساب.',
            'tronc_commun' => 'تعريفة الجذع المشترك',
            'tronc_commun_note' => 'هذه التعريفة تخص الجذع المشترك وكذلك 1AC و2AC و3AC.',
            'autres_niveaux' => 'تعريفة باقي المستويات',
            'autres_niveaux_note' => 'هذه التعريفة تخص الاولى باك والثانية باك والتحضير للمباريات.',
        ],
        default => [
            'title' => 'Payment and subscription',
            'subtitle' => 'Choose the plan that fits your learning pace.',
            'monthly' => 'Monthly',
            'semiannual' => '6 months',
            'annual' => 'Yearly',
            'cta' => 'Choose this plan',
            'note' => 'After your choice, you can send your payment proof through WhatsApp.',
            'tronc_commun' => 'Common core pricing',
            'tronc_commun_note' => 'These prices apply to common core as well as 1AC, 2AC, and 3AC.',
            'autres_niveaux' => 'Other levels pricing',
            'autres_niveaux_note' => 'These prices apply to 1st bac, 2nd bac, and exam preparation.',
        ],
    };

    $labels = [$paymentCopy['monthly'], $paymentCopy['semiannual'], $paymentCopy['annual']];

    $requestedLevel = (string) ($paymentContext['level'] ?? '');
    $targetPlanGroup = '';

    if ($requestedLevel !== '') {
        $targetPlanGroup = str_contains($requestedLevel, 'tronc') || str_contains($requestedLevel, 'college')
            ? 'tronc_commun'
            : 'autres_niveaux';
    }

    $displayPlanGroups = collect($plans)
        ->when($targetPlanGroup !== '', fn ($groups) => $groups->only($targetPlanGroup))
        ->map(function (array $groupPlans, string $groupKey) use ($labels) {
        return collect($groupPlans)->values()->map(function (array $plan, int $index) use ($labels): array {
            return array_merge($plan, [
                'display_name' => $labels[$index] ?? $plan['name'],
            ]);
        });
    });

    $paymentBackUrl = (string) request()->query('back', '');
    $appBaseUrl = url('/');

    if ($paymentBackUrl === '') {
        $paymentBackUrl = url()->previous();
    }

    if ($paymentBackUrl === url()->current() || ! str_starts_with($paymentBackUrl, $appBaseUrl)) {
        $paymentBackUrl = route('teacher.index.locale', ['locale' => $locale]);
    }

    if ($locale === 'ar') {
        $arabicDurations = [
            'tronc_commun' => ['ولوج 30 يوما', 'ولوج 180 يوما', 'ولوج 365 يوما'],
            'autres_niveaux' => ['ولوج 30 يوما', 'ولوج 180 يوما', 'ولوج 365 يوما'],
        ];

        $arabicDescriptions = [
            'tronc_commun' => [
                'تعريفة خاصة بمستوى الجذع المشترك.',
                'صيغة 6 اشهر خاصة بالجذع المشترك.',
                'صيغة سنوية خاصة بالجذع المشترك.',
            ],
            'autres_niveaux' => [
                'صالحة للاولى باك والثانية باك والتحضير للمباريات.',
                'صيغة 6 اشهر لباقي المستويات.',
                'صيغة سنوية لباقي المستويات.',
            ],
        ];

        $displayPlanGroups = $displayPlanGroups->map(function ($groupPlans, string $groupKey) use ($arabicDurations, $arabicDescriptions) {
            return $groupPlans->values()->map(function (array $plan, int $index) use ($groupKey, $arabicDurations, $arabicDescriptions): array {
                preg_match('/\d+/', $plan['price'], $matches);

                return array_merge($plan, [
                    'price' => ($matches[0] ?? '').' درهم',
                    'duration' => $arabicDurations[$groupKey][$index] ?? $plan['duration'],
                    'description' => $arabicDescriptions[$groupKey][$index] ?? $plan['description'],
                ]);
            });
        });
    }

    $paymentContextParts = collect($paymentContext ?? [])
        ->filter(fn ($value) => filled($value))
        ->map(fn ($value, $key) => ucfirst($key).': '.$value)
        ->implode(' | ');
@endphp

@section('content')
    <main class="student-shell">
        @include('partials.student-topbar', [
            'topbarTitle' => $paymentCopy['title'],
            'routeName' => 'payment.locale',
            'brandHref' => $paymentBackUrl,
            'whatsappBase' => $whatsappBase,
        ])

        <section class="student-page-card student-page-card--wide">
            <div class="student-page-head">
                <h1>{{ $paymentCopy['title'] }}</h1>
                <p>{{ $paymentCopy['subtitle'] }}</p>
            </div>

            @foreach ($displayPlanGroups as $groupKey => $groupPlans)
                <section class="payment-plan-section">
                    <div class="payment-plan-section__head">
                        <h2>{{ $paymentCopy[$groupKey] }}</h2>
                        <p>{{ $paymentCopy[$groupKey.'_note'] }}</p>
                    </div>

                    <div class="payment-plan-grid">
                        @foreach ($groupPlans as $plan)
                            <article class="payment-plan-card">
                                <p class="payment-plan-card__tag">{{ $plan['display_name'] }}</p>
                                <h2>{{ $plan['price'] }}</h2>
                                <p>{{ $plan['duration'] }}</p>
                                <p class="payment-plan-card__copy">{{ $plan['description'] }}</p>
                                <a
                                    class="primary-btn"
                                    href="{{ $whatsappBase }}?text={{ urlencode('Bonjour EasyPsi, je choisis la formule '.$plan['display_name'].' - '.$plan['price'].' ('.$paymentCopy[$groupKey].')'.($paymentContextParts !== '' ? ' | '.$paymentContextParts : '')) }}"
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    {{ $paymentCopy['cta'] }}
                                </a>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach

            <div class="student-info-note">
                <p>{{ $paymentCopy['note'] }}</p>
            </div>
        </section>

        @include('partials.student-footer')
    </main>
@endsection
