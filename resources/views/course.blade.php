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
            'message' => 'This page has been replaced by the teachers page.',
            'button' => 'Go to teachers',
        ],
        default => [
            'title' => 'EasyPsi',
            'message' => 'Cette page a été remplacée par la page des professeurs.',
            'button' => 'Voir les professeurs',
        ],
    };
@endphp

@section('content')
    <main class="student-shell">
        <section class="student-page-card">
            <div class="student-page-head">
                <h1>{{ $courseCopy['title'] }}</h1>
                <p>{{ $courseCopy['message'] }}</p>
            </div>
            <a class="primary-btn" href="{{ route('teacher.index.locale', ['locale' => $locale]) }}">{{ $courseCopy['button'] }}</a>
        </section>
    </main>
@endsection
