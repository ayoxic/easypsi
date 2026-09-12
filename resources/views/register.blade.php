@extends('layouts.app')

@php
    $registerCopy = $copy['login'];
    $roleLabel = match ($locale) {
        'ar' => 'نوع الحساب',
        'en' => 'Account type',
        default => 'Type de compte',
    };
    $existingAccountLabel = match ($locale) {
        'ar' => 'لدي حساب بالفعل',
        'en' => 'I already have an account',
        default => 'J’ai déjà un compte',
    };
@endphp

@section('content')
    <main class="simple-login-shell">
        <section class="simple-login-card" dir="{{ $dir }}">
            <div class="simple-login-topbar">
                @include('partials.locale-switcher', ['routeName' => 'register.locale'])
            </div>

            <div class="simple-login-brand">
                <img src="{{ asset('logo.jpeg') }}" alt="EasyPsi logo" class="simple-login-logo">
            </div>

            <header class="simple-login-header">
                <h1>{{ $registerCopy['register_title'] }}</h1>
                <p>
                    <a href="{{ route('login.locale', ['locale' => $locale]) }}">{{ $existingAccountLabel }}</a>
                </p>
            </header>

            @if ($errors->any())
                <div class="form-errors">
                    <p>{{ $errors->first('email') ?: $errors->first('password') ?: $errors->first() }}</p>
                </div>
            @endif

            <form class="simple-login-form" action="{{ route('register.submit', ['locale' => $locale]) }}" method="post" novalidate>
                @csrf
                <div class="simple-field">
                    <label for="name">{{ $registerCopy['name'] }}</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                </div>

                <div class="simple-field">
                    <label for="email">{{ $registerCopy['email'] }}</label>
                    <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required>
                </div>

                <div class="simple-field">
                    <label for="phone">{{ $registerCopy['phone'] }}</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}">
                </div>

                <div class="simple-field">
                    <label for="role">{{ $roleLabel }}</label>
                    <select id="role" name="role" class="simple-select" required>
                        @foreach (($registerRoles ?? ['student' => 'Élève', 'teacher' => 'Professeur']) as $roleValue => $roleText)
                            <option value="{{ $roleValue }}" @selected(old('role', 'student') === $roleValue)>{{ $roleText }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="simple-field">
                    <label for="password">{{ $registerCopy['password'] }}</label>
                    <div class="simple-password-wrap">
                        <button
                            class="simple-password-toggle"
                            type="button"
                            aria-label="{{ $registerCopy['show_password'] }}"
                            data-password-toggle
                            data-show-label="{{ $registerCopy['show_password'] }}"
                            data-hide-label="{{ $registerCopy['password'] }}"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 5C6.8 5 2.5 8.1 1 12c1.5 3.9 5.8 7 11 7s9.5-3.1 11-7c-1.5-3.9-5.8-7-11-7Zm0 11.2A4.2 4.2 0 1 1 12 7.8a4.2 4.2 0 0 1 0 8.4Zm0-6.6a2.4 2.4 0 1 0 0 4.8 2.4 2.4 0 0 0 0-4.8Z" fill="currentColor"/>
                            </svg>
                        </button>
                        <input id="password" name="password" type="password" required>
                    </div>
                </div>

                <div class="simple-field">
                    <label for="password_confirmation">{{ $registerCopy['confirm_password'] }}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required>
                </div>

                @if (config('services.recaptcha.enabled') && config('services.recaptcha.site_key'))
                    <div class="simple-captcha">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    </div>
                @endif

                <button class="simple-login-button" type="submit">{{ $registerCopy['register_submit'] }}</button>
            </form>
        </section>
    </main>
@endsection
