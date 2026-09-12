@extends('layouts.app')

@php
    $pendingCopy = match ($locale) {
        'ar' => [
            'badge' => 'في انتظار التحقق',
            'title' => 'حساب الأستاذ قيد المراجعة',
            'body' => 'تم تأكيد بريدك الإلكتروني. سيقوم فريق EasyPsi بالتحقق من حسابك عبر واتساب قبل فتح فضاء الأستاذ.',
            'whatsapp' => 'التواصل عبر واتساب',
            'home' => 'العودة إلى الصفحة الرئيسية',
        ],
        'en' => [
            'badge' => 'Pending verification',
            'title' => 'Your teacher account is under review',
            'body' => 'Your email has been verified. The EasyPsi team will confirm your teacher account by WhatsApp before opening teacher access.',
            'whatsapp' => 'Contact on WhatsApp',
            'home' => 'Back to home',
        ],
        default => [
            'badge' => 'Validation en attente',
            'title' => 'Votre compte professeur est en cours de vérification',
            'body' => 'Votre email est confirmé. L’équipe EasyPsi va vérifier votre compte par WhatsApp avant d’ouvrir l’espace professeur.',
            'whatsapp' => 'Contacter sur WhatsApp',
            'home' => 'Retour à l’accueil',
        ],
    };
@endphp

@section('content')
    <main class="simple-login-shell">
        <section class="simple-login-card teacher-pending-card" dir="{{ $dir }}">
            <div class="simple-login-brand">
                <img src="{{ asset('logo.jpeg') }}" alt="EasyPsi logo" class="simple-login-logo">
            </div>

            <header class="simple-login-header">
                <span class="brand-chip">{{ $pendingCopy['badge'] }}</span>
                <h1>{{ $pendingCopy['title'] }}</h1>
                <p>{{ $pendingCopy['body'] }}</p>
            </header>

            <div class="teacher-pending-actions">
                <a class="whatsapp-btn" href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noreferrer">{{ $pendingCopy['whatsapp'] }}</a>
                <a class="secondary-btn" href="{{ route('welcome.locale', ['locale' => $locale]) }}">{{ $pendingCopy['home'] }}</a>
            </div>
        </section>
    </main>
@endsection
