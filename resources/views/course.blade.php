@extends('layouts.app')

@section('content')
    <main class="student-shell">
        <section class="student-page-card">
            <div class="student-page-head">
                <h1>EasyPsi</h1>
                <p>Cette page a été remplacée par la page des professeurs.</p>
            </div>
            <a class="primary-btn" href="{{ route('teacher.index.locale', ['locale' => $locale]) }}">Voir les professeurs</a>
        </section>
    </main>
@endsection
