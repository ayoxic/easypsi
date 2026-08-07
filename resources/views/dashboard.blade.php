@php
    $locale = auth()->user()?->preferred_locale ?? 'ar';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="text-lg font-semibold">{{ __('Welcome back, :name', ['name' => auth()->user()->name]) }}</p>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        {{ __('Your Laravel Breeze profile area is ready, and your existing EasyPsi pages are still available below.') }}
                    </p>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <a
                    href="{{ route('course.locale', ['locale' => $locale]) }}"
                    class="rounded-lg bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:bg-gray-800"
                >
                    <p class="text-sm font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Student Area') }}</p>
                    <p class="mt-2 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Open course page') }}</p>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ __('Continue in your preferred locale.') }}</p>
                </a>

                <a
                    href="{{ route('admin.locale', ['locale' => $locale]) }}"
                    class="rounded-lg bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:bg-gray-800"
                >
                    <p class="text-sm font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Admin Area') }}</p>
                    <p class="mt-2 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Open admin preview') }}</p>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ __('Review the current management screen.') }}</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
