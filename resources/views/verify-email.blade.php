@extends('layouts.app')

@php
    $loginCopy = $copy['login'];
@endphp

@section('content')
    <main class="simple-login-shell">
        <section class="simple-login-card" dir="{{ $dir }}">
            <div class="simple-login-topbar">
                @include('partials.locale-switcher', ['routeName' => 'verification.notice'])
            </div>

            <div class="simple-login-brand">
                <img src="{{ asset('logo.jpeg') }}" alt="EasyPsi logo" class="simple-login-logo">
            </div>

            <header class="simple-login-header">
                <h1>{{ $loginCopy['verify_email_title'] }}</h1>
                <p>{{ $loginCopy['verify_email_note'] }}</p>
            </header>

            @if (session('status'))
                <div class="form-flash">{{ session('status') }}</div>
            @endif

            <form class="simple-login-form" action="{{ route('verification.send', ['locale' => $locale]) }}" method="post">
                @csrf
                <button class="simple-login-button" type="submit">{{ $loginCopy['verify_email_resend'] }}</button>
            </form>
        </section>
    </main>
@endsection
