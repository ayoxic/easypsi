@extends('layouts.app')

@php
    $whatsappBase = 'https://wa.me/' . $whatsappNumber;
    $loginCopy = $copy['login'];
@endphp

@section('content')
    <main class="simple-login-shell">
        <section class="simple-login-card" dir="{{ $dir }}">
            <div class="simple-login-topbar">
                @include('partials.locale-switcher', ['routeName' => 'login.locale'])
            </div>

            <div class="simple-login-brand">
                <img src="{{ asset('logo.jpeg') }}" alt="EasyPsi logo" class="simple-login-logo">
            </div>

            <header class="simple-login-header">
                <h1>{{ $loginCopy['title'] }}</h1>
                <p>
                    {{ $loginCopy['subtitle_prefix'] }}
                    <a href="{{ route('register.locale', ['locale' => $locale]) }}">{{ $loginCopy['subtitle_link'] }}</a>
                </p>
            </header>

            @if (session('status'))
                <div class="form-flash">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="form-errors">
                    <p>{{ $errors->first('email') ?: $errors->first('password') ?: $errors->first() }}</p>
                </div>
            @endif

            <form class="simple-login-form" action="{{ route('login.submit', ['locale' => $locale]) }}" method="post" novalidate>
                @csrf
                <div class="simple-field">
                    <label for="email">{{ $loginCopy['email'] }}</label>
                    <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required>
                </div>

                <div class="simple-field">
                    <div class="simple-field-top">
                        <label for="password">{{ $loginCopy['password'] }}</label>
                        <a href="{{ route('password.request', ['locale' => $locale]) }}">
                            {{ $loginCopy['forgot'] }}
                        </a>
                    </div>

                    <div class="simple-password-wrap">
                        <button
                            class="simple-password-toggle"
                            type="button"
                            aria-label="{{ $loginCopy['show_password'] }}"
                            data-password-toggle
                            data-show-label="{{ $loginCopy['show_password'] }}"
                            data-hide-label="{{ $loginCopy['password'] }}"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 5C6.8 5 2.5 8.1 1 12c1.5 3.9 5.8 7 11 7s9.5-3.1 11-7c-1.5-3.9-5.8-7-11-7Zm0 11.2A4.2 4.2 0 1 1 12 7.8a4.2 4.2 0 0 1 0 8.4Zm0-6.6a2.4 2.4 0 1 0 0 4.8 2.4 2.4 0 0 0 0-4.8Z" fill="currentColor"/>
                            </svg>
                        </button>
                        <input id="password" name="password" type="password" autocomplete="current-password" required>
                    </div>
                </div>

                <label class="simple-remember">
                    <input id="remember" name="remember" type="checkbox" {{ old('remember', '1') ? 'checked' : '' }}>
                    <span>{{ $loginCopy['remember'] }}</span>
                </label>

                <button class="simple-login-button" type="submit">{{ $loginCopy['submit'] }}</button>
            </form>
        </section>
    </main>
@endsection
