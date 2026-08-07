@php
    $routeParameters = request()->route()?->parameters() ?? [];
    $queryParameters = request()->query();
@endphp

@if (($localeMenuStyle ?? null) === 'dropdown')
    <nav class="locale-switcher locale-switcher--menu" aria-label="Language switcher">
        <div class="student-topbar-menu student-topbar-menu--languages" data-topbar-menu>
            <button class="student-topbar-menu__toggle" type="button" aria-expanded="false" data-topbar-menu-toggle>
                {{ $localeMenuLabel ?? 'Languages' }}
            </button>

            <div class="student-topbar-menu__dropdown" data-topbar-menu-dropdown>
                @foreach ($availableLocales as $switchLocale => $switchCopy)
                    @php
                        $localeLabel = $locale === 'ar'
                            ? match ($switchLocale) {
                                'fr' => 'الفرنسية',
                                'en' => 'الإنجليزية',
                                'ar' => 'العربية',
                                default => $switchCopy['name'] ?? $switchCopy['short'],
                            }
                            : ($switchCopy['name'] ?? $switchCopy['short']);
                    @endphp
                    <a
                        class="student-topbar-menu__option{{ $locale === $switchLocale ? ' is-active' : '' }}"
                        href="{{ route($routeName, array_merge($routeParameters, ['locale' => $switchLocale], array_merge($queryParameters, ! empty($localeQueryOverrides ?? []) ? collect($localeQueryOverrides)->mapWithKeys(fn ($value, $key) => [$key => $value === '__CURRENT_SWITCH_LOCALE__' ? $switchLocale : $value])->all() : []))) }}"
                    >
                        {{ $localeLabel }}
                    </a>
                @endforeach
            </div>
        </div>
    </nav>
@else
    <nav class="locale-switcher" aria-label="Language switcher">
        @foreach ($availableLocales as $switchLocale => $switchCopy)
            <a
                class="locale-pill {{ $locale === $switchLocale ? 'is-active' : '' }}"
                href="{{ route($routeName, array_merge($routeParameters, ['locale' => $switchLocale], array_merge($queryParameters, ! empty($localeQueryOverrides ?? []) ? collect($localeQueryOverrides)->mapWithKeys(fn ($value, $key) => [$key => $value === '__CURRENT_SWITCH_LOCALE__' ? $switchLocale : $value])->all() : []))) }}"
            >
                {{ $switchCopy['short'] }}
            </a>
        @endforeach
    </nav>
@endif
