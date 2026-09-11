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
                <h1>{{ $loginCopy['forgot_title'] }}</h1>
                <p>
                    <a href="{{ route('login.locale', ['locale' => $locale]) }}">{{ $loginCopy['submit'] }}</a>
                </p>
            </header>

            @if (session('status'))
                <div class="form-flash">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="form-errors">
                    <p>{{ $errors->first('email') ?: $errors->first() }}</p>
                </div>
            @endif

            <form class="simple-login-form" action="{{ route('password.email', ['locale' => $locale]) }}" method="post" novalidate>
                @csrf
                <div class="simple-field">
                    <label for="email">{{ $loginCopy['email'] }}</label>
                    <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required>
                </div>

                @if (config('services.recaptcha.enabled') && config('services.recaptcha.site_key'))
                    <div class="simple-captcha">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    </div>
                @endif

                <button class="simple-login-button" type="submit">{{ $loginCopy['forgot_submit'] }}</button>
            </form>
        </section>
    </main>
@endsection
