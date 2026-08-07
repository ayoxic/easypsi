@php
    $footerText = match ($locale) {
        'fr' => 'Tous droits réservés.',
        'ar' => 'جميع الحقوق محفوظة.',
        default => 'All rights reserved.',
    };
@endphp

<footer class="student-footer">
    <p>EasyPsi {{ date('Y') }}. {{ $footerText }}</p>
</footer>
