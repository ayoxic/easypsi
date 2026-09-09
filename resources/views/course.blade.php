@extends('layouts.app')

@php
    $courseCopy = match ($locale) {
        'ar' => [
            'title' => 'EasyPsi',
            'message' => 'تم استبدال هذه الصفحة بصفحة الأساتذة.',
            'button' => 'الذهاب إلى الأساتذة',
        ],
        'en' => [
            'title' => 'EasyPsi',
            'legacy_heading' => 'Course presentation and quick start',
            'message' => 'This page has been replaced by the teachers page.',
            'payment' => 'Payment by WhatsApp',
            'button' => 'Go to teachers',
        ],
        default => [
            'title' => 'EasyPsi',
            'legacy_heading' => 'Presentation du cours et demarrage rapide',
            'message' => 'Cette page a ete remplacee par la page des professeurs.',
            'payment' => 'Paiement par WhatsApp',
            'button' => 'Voir les professeurs',
        ],
    };
@endphp

@section('content')
    <main class="student-shell">
        <section class="student-page-card">
            <div class="student-page-head">
                <h1>{{ $courseCopy['title'] }}</h1>
                @isset($courseCopy['legacy_heading'])
                    <h2>{{ $courseCopy['legacy_heading'] }}</h2>
                @endisset
                <p>{{ $courseCopy['message'] }}</p>
                @isset($courseCopy['payment'])
                    <p>{{ $courseCopy['payment'] }}</p>
                @endisset
            </div>
            <a class="primary-btn" href="{{ route('teacher.index.locale', ['locale' => $locale]) }}">{{ $courseCopy['button'] }}</a>
        </section>
    </main>
@endsection
