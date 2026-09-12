@php
    $menuProfile = match ($locale) {
        'ar' => [
            'profile' => 'الملف الشخصي',
            'payment' => 'سجل الدفع',
            'logout' => 'تسجيل الخروج',
            'languages' => 'اللغات',
        ],
        'en' => [
            'profile' => 'Profile',
            'payment' => 'Payment history',
            'logout' => 'Logout',
            'languages' => 'Languages',
        ],
        default => [
            'profile' => 'Profile',
            'payment' => 'Historique des paiements',
            'logout' => 'Déconnexion',
            'languages' => 'Langues',
        ],
    };
@endphp

<header class="student-topbar glass-card">
    <a class="course-brand" href="{{ $brandHref ?? route($routeName ?? 'teacher.index.locale', ['locale' => $locale]) }}">
        <img src="{{ asset('logo.jpeg') }}" alt="EasyPsi logo" class="course-brand-logo">
    </a>

    @if (! empty($topbarTitle ?? null))
        <div class="student-topbar-title">{{ $topbarTitle }}</div>
    @endif

    <div class="student-topbar-spacer"></div>

    @include('partials.locale-switcher', [
        'routeName' => $routeName ?? 'teacher.index.locale',
        'localeMenuStyle' => 'dropdown',
        'localeMenuLabel' => $menuProfile['languages'],
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
                <a class="student-topbar-menu__option" href="{{ route('student.profile.locale', ['locale' => $locale]) }}">{{ $menuProfile['profile'] }}</a>
                <a class="student-topbar-menu__option" href="{{ route('payment.history.locale', ['locale' => $locale]) }}">{{ $menuProfile['payment'] }}</a>
                <form method="POST" action="{{ route('logout.locale', ['locale' => $locale]) }}">
                    @csrf
                    <button class="student-topbar-menu__option student-topbar-menu__button" type="submit">{{ $menuProfile['logout'] }}</button>
                </form>
            </div>
        </div>
    @endauth
</header>
