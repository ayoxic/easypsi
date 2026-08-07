@extends('layouts.app')

@php
    $loginCopy = $copy['login'];
@endphp

@section('content')
    <main class="simple-login-shell">
        <section class="simple-login-card" dir="{{ $dir }}">
            <div class="simple-login-topbar">
                @include('partials.locale-switcher', ['routeName' => 'password.request'])
            </div>

            <div class="simple-login-brand">
                <img src="{{ asset('logo.jpeg') }}" alt="EasyPsi logo" class="simple-login-logo">
            </div>

            <header class="simple-login-header">
                <h1>{{ $loginCopy['reset_title'] }}</h1>
                <p>
                    <a href="{{ route('login.locale', ['locale' => $locale]) }}">{{ $loginCopy['submit'] }}</a>
                </p>
            </header>

            @if ($errors->any())
                <div class="form-errors">
                    <p>{{ $errors->first('email') ?: $errors->first('password') ?: $errors->first() }}</p>
                </div>
            @endif

            <form class="simple-login-form" action="{{ route('password.store', ['locale' => $locale]) }}" method="post" novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="simple-field">
                    <label for="email">{{ $loginCopy['email'] }}</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required>
                </div>

                <div class="simple-field">
                    <label for="password">{{ $loginCopy['password'] }}</label>
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
                        <input id="password" name="password" type="password" required>
                    </div>
                </div>

                <div class="simple-field">
                    <label for="password_confirmation">{{ $loginCopy['confirm_password'] }}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required>
                </div>

                <button class="simple-login-button" type="submit">{{ $loginCopy['reset_submit'] }}</button>
            </form>
        </section>
    </main>
@endsection
