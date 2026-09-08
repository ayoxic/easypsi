<?php

use App\Models\TeacherLesson;
use App\Models\TeacherLessonAsset;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

$locales = config('easypsi.locales', []);

$resolveLocale = static function (?string $locale) use ($locales): string {
    return array_key_exists((string) $locale, $locales) ? $locale : 'fr';
};

$baseViewData = static function (string $locale) use ($locales): array {
    $copy = $locales[$locale] ?? $locales['fr'];

    return [
        'locale' => $locale,
        'availableLocales' => $locales,
        'htmlLang' => $copy['html_lang'] ?? $locale,
        'dir' => $copy['dir'] ?? 'ltr',
        'bodyClass' => $copy['body_class'] ?? '',
        'copy' => $copy,
        'whatsappNumber' => config('easypsi.whatsapp_number'),
        'plans' => config('easypsi.plans', []),
        'title' => $copy['title'] ?? 'EasyPsi',
    ];
};

Route::get('/healthz', static fn () => response('ok', 200));

$teacherSubjectOptions = static function (string $locale): array {
    return match ($locale) {
        'ar' => [
            'math' => 'الرياضيات',
            'physics' => 'الفيزياء',
            'chemistry' => 'الكيمياء',
            'svt' => 'علوم الحياة والأرض',
            'french' => 'الفرنسية',
            'english' => 'الإنجليزية',
            'philosophy' => 'الفلسفة',
            'economy' => 'الاقتصاد',
            'accounting' => 'المحاسبة',
            'organization' => 'التنظيم الإداري',
        ],
        'en' => [
            'math' => 'Mathematics',
            'physics' => 'Physics',
            'chemistry' => 'Chemistry',
            'svt' => 'Life and Earth Sciences',
            'french' => 'French',
            'english' => 'English',
            'philosophy' => 'Philosophy',
            'economy' => 'Economics',
            'accounting' => 'Accounting',
            'organization' => 'Business organization',
        ],
        default => [
            'math' => 'Mathématiques',
            'physics' => 'Physique',
            'chemistry' => 'Chimie',
            'svt' => 'SVT',
            'french' => 'Français',
            'english' => 'Anglais',
            'philosophy' => 'Philosophie',
            'economy' => 'Économie générale',
            'accounting' => 'Comptabilité',
            'organization' => 'Organisation administrative',
        ],
    };
};

$levelMatrix = static function (string $locale) use ($teacherSubjectOptions): array {
    $subjects = $teacherSubjectOptions($locale);

    return match ($locale) {
        'ar' => [
            'college-1ac' => ['label' => 'الأولى إعدادي', 'tracks' => []],
            'college-2ac' => ['label' => 'الثانية إعدادي', 'tracks' => []],
            'college-3ac' => ['label' => 'الثالثة إعدادي', 'tracks' => []],
            'tronc-commun' => [
                'label' => 'الجذع المشترك',
                'tracks' => [
                    'scientifique' => ['label' => 'علمي', 'subjects' => ['math', 'physics', 'chemistry', 'french', 'english']],
                    'experimental' => ['label' => 'علوم تجريبية', 'subjects' => ['math', 'physics', 'chemistry', 'svt', 'french', 'english']],
                ],
            ],
            '1ere-bac' => [
                'label' => 'الأولى باك',
                'tracks' => [
                    'science-math' => ['label' => 'علوم رياضية', 'subjects' => ['math', 'physics', 'chemistry', 'french', 'english', 'philosophy']],
                    'science-experimentale' => ['label' => 'علوم تجريبية', 'subjects' => ['math', 'physics', 'chemistry', 'svt', 'french', 'english', 'philosophy']],
                    'economie' => ['label' => 'اقتصاد', 'subjects' => ['economy', 'accounting', 'organization', 'math', 'french', 'english']],
                ],
            ],
            '2eme-bac' => [
                'label' => 'الثانية باك',
                'tracks' => [
                    'science-math' => ['label' => 'علوم رياضية', 'subjects' => ['math', 'physics', 'chemistry', 'french', 'english', 'philosophy']],
                    'science-physique' => ['label' => 'علوم فيزيائية', 'subjects' => ['math', 'physics', 'chemistry', 'svt', 'french', 'english', 'philosophy']],
                    'svt' => ['label' => 'علوم الحياة والأرض', 'subjects' => ['svt', 'physics', 'chemistry', 'math', 'french', 'english', 'philosophy']],
                    'economie' => ['label' => 'اقتصاد', 'subjects' => ['economy', 'accounting', 'organization', 'math', 'french', 'english']],
                ],
            ],
            'post-bac' => [
                'label' => 'ما بعد الباك',
                'tracks' => [
                    'preparation-concours' => ['label' => 'التحضير للمباريات', 'subjects' => ['math', 'physics', 'chemistry', 'french', 'english']],
                ],
            ],
        ],
        'en' => [
            'college-1ac' => ['label' => '1st AC', 'tracks' => []],
            'college-2ac' => ['label' => '2nd AC', 'tracks' => []],
            'college-3ac' => ['label' => '3rd AC', 'tracks' => []],
            'tronc-commun' => [
                'label' => 'Common core',
                'tracks' => [
                    'scientifique' => ['label' => 'Scientific', 'subjects' => ['math', 'physics', 'chemistry', 'french', 'english']],
                    'experimental' => ['label' => 'Experimental sciences', 'subjects' => ['math', 'physics', 'chemistry', 'svt', 'french', 'english']],
                ],
            ],
            '1ere-bac' => [
                'label' => '1st bac',
                'tracks' => [
                    'science-math' => ['label' => 'Math sciences', 'subjects' => ['math', 'physics', 'chemistry', 'french', 'english', 'philosophy']],
                    'science-experimentale' => ['label' => 'Experimental sciences', 'subjects' => ['math', 'physics', 'chemistry', 'svt', 'french', 'english', 'philosophy']],
                    'economie' => ['label' => 'Economics', 'subjects' => ['economy', 'accounting', 'organization', 'math', 'french', 'english']],
                ],
            ],
            '2eme-bac' => [
                'label' => '2nd bac',
                'tracks' => [
                    'science-math' => ['label' => 'Math sciences', 'subjects' => ['math', 'physics', 'chemistry', 'french', 'english', 'philosophy']],
                    'science-physique' => ['label' => 'Physics sciences', 'subjects' => ['math', 'physics', 'chemistry', 'svt', 'french', 'english', 'philosophy']],
                    'svt' => ['label' => 'Life and earth sciences', 'subjects' => ['svt', 'physics', 'chemistry', 'math', 'french', 'english', 'philosophy']],
                    'economie' => ['label' => 'Economics', 'subjects' => ['economy', 'accounting', 'organization', 'math', 'french', 'english']],
                ],
            ],
            'post-bac' => [
                'label' => 'Post-bac',
                'tracks' => [
                    'preparation-concours' => ['label' => 'Exam preparation', 'subjects' => ['math', 'physics', 'chemistry', 'french', 'english']],
                ],
            ],
        ],
        default => [
            'college-1ac' => ['label' => '1ère AC', 'tracks' => []],
            'college-2ac' => ['label' => '2ème AC', 'tracks' => []],
            'college-3ac' => ['label' => '3ème AC', 'tracks' => []],
            'tronc-commun' => [
                'label' => 'Tronc commun',
                'tracks' => [
                    'scientifique' => ['label' => 'Scientifique', 'subjects' => ['math', 'physics', 'chemistry', 'french', 'english']],
                    'experimental' => ['label' => 'Sciences expérimentales', 'subjects' => ['math', 'physics', 'chemistry', 'svt', 'french', 'english']],
                ],
            ],
            '1ere-bac' => [
                'label' => '1ère bac',
                'tracks' => [
                    'science-math' => ['label' => 'Sciences maths', 'subjects' => ['math', 'physics', 'chemistry', 'french', 'english', 'philosophy']],
                    'science-experimentale' => ['label' => 'Sciences expérimentales', 'subjects' => ['math', 'physics', 'chemistry', 'svt', 'french', 'english', 'philosophy']],
                    'economie' => ['label' => 'Économie', 'subjects' => ['economy', 'accounting', 'organization', 'math', 'french', 'english']],
                ],
            ],
            '2eme-bac' => [
                'label' => '2ème bac',
                'tracks' => [
                    'science-math' => ['label' => 'Sciences maths', 'subjects' => ['math', 'physics', 'chemistry', 'french', 'english', 'philosophy']],
                    'science-physique' => ['label' => 'Sciences physiques', 'subjects' => ['math', 'physics', 'chemistry', 'svt', 'french', 'english', 'philosophy']],
                    'svt' => ['label' => 'SVT', 'subjects' => ['svt', 'physics', 'chemistry', 'math', 'french', 'english', 'philosophy']],
                    'economie' => ['label' => 'Économie', 'subjects' => ['economy', 'accounting', 'organization', 'math', 'french', 'english']],
                ],
            ],
            'post-bac' => [
                'label' => 'Post-bac',
                'tracks' => [
                    'preparation-concours' => ['label' => 'Préparation concours', 'subjects' => ['math', 'physics', 'chemistry', 'french', 'english']],
                ],
            ],
        ],
    };
};

$youtubeEmbedUrl = static function (?string $url): ?string {
    if (blank($url)) {
        return null;
    }

    $trimmed = trim((string) $url);

    if (Str::contains($trimmed, 'youtube.com/embed/')) {
        return $trimmed;
    }

    preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|shorts/|embed/))([A-Za-z0-9_-]{6,})~', $trimmed, $matches);

    return filled($matches[1] ?? null)
        ? 'https://www.youtube.com/embed/'.$matches[1]
        : null;
};

$studentHasPremiumAccess = static function (?User $user, TeacherLesson $lesson): bool {
    if (! $user) {
        return false;
    }

    if ($user->role === 'admin' || $user->role === 'teacher') {
        return true;
    }

    if (! $user->hasActivePremium()) {
        return false;
    }

    return blank($user->premium_level_key) || $user->hasActivePremiumForLevel($lesson->level_key);
};

$teacherCopy = static function (string $locale): array {
    return match ($locale) {
        'ar' => [
            'index_title' => 'اختر الأستاذ الذي تريده',
            'index_subtitle' => 'اختر المستوى، ثم الشعبة، ثم المادة لتصل بسرعة إلى الأستاذ المناسب.',
            'teacher_space' => 'فضاء الأستاذ',
            'save' => 'حفظ المحتوى',
            'delete' => 'حذف',
            'free' => 'مجاني',
            'premium' => 'بريميوم',
            'back' => 'العودة إلى لائحة الأساتذة',
            'premium_locked' => 'هذا المحتوى متاح في البريميوم',
            'go_premium' => 'الذهاب إلى صفحة البريميوم',
            'description' => 'الوصف',
            'supports' => 'الدعائم',
            'quiz' => 'الاختبار',
            'course' => 'الدرس',
            'exercise' => 'التمرين',
            'teacher_page' => 'صفحة الأستاذ',
            'no_videos' => 'لا توجد فيديوهات لهذا الأستاذ بعد',
            'no_videos_text' => 'لم ينشر هذا الأستاذ أي فيديو بعد. جرب اختيارا آخر أو عد لاحقا.',
            'no_videos_for_filter' => 'لا توجد فيديوهات لهذا الأستاذ في هذا الاختيار',
            'reset_filters' => 'إعادة تعيين الاختيار',
        ],
        'en' => [
            'index_title' => 'Choose the teacher you want',
            'index_subtitle' => 'Choose the level, then the track, then the subject to find the right teacher faster.',
            'teacher_space' => 'Teacher space',
            'save' => 'Save content',
            'delete' => 'Delete',
            'free' => 'Free',
            'premium' => 'Premium',
            'back' => 'Back to teachers list',
            'premium_locked' => 'This content is available in premium',
            'go_premium' => 'Go to premium page',
            'description' => 'Description',
            'supports' => 'Supports',
            'quiz' => 'Quiz',
            'course' => 'Course',
            'exercise' => 'Exercise',
            'teacher_page' => 'Teacher page',
            'no_videos' => 'No videos from this teacher yet',
            'no_videos_text' => 'This teacher has not published any video yet. Try another selection or come back later.',
            'no_videos_for_filter' => 'No videos from this teacher for this selection',
            'reset_filters' => 'Reset selection',
        ],
        default => [
            'index_title' => 'Choisissez le professeur que vous voulez',
            'index_subtitle' => 'Choisissez le niveau, puis la filière, puis la matière pour trouver plus vite le bon professeur.',
            'teacher_space' => 'Espace professeur',
            'save' => 'Enregistrer le contenu',
            'delete' => 'Supprimer',
            'free' => 'Free',
            'premium' => 'Premium',
            'back' => 'Retour à la liste des professeurs',
            'premium_locked' => 'Ce contenu est disponible en premium',
            'go_premium' => 'Aller vers la page premium',
            'description' => 'Description',
            'supports' => 'Supports',
            'quiz' => 'Quiz',
            'course' => 'Cours',
            'exercise' => 'Exercice',
            'teacher_page' => 'Page du professeur',
            'no_videos' => 'Aucune vidéo de ce professeur pour le moment',
            'no_videos_text' => "Ce professeur n'a pas encore publié de vidéo. Essayez une autre sélection ou revenez plus tard.",
            'no_videos_for_filter' => 'Aucune vidéo faite par ce professeur pour cette sélection',
            'reset_filters' => 'Réinitialiser la sélection',
        ],
    };
};

Route::post('/language', function (Request $request) use ($resolveLocale) {
    $validated = $request->validate([
        'locale' => ['required', 'string'],
        'redirect_to' => ['nullable', 'url'],
    ]);

    $locale = $resolveLocale($validated['locale']);
    $request->session()->put('easypsi_locale', $locale);
    app()->setLocale($locale);

    return redirect()->to($validated['redirect_to'] ?? url()->previous() ?? route('welcome.locale', ['locale' => $locale]));
})->name('language.update');

Route::redirect('/', '/fr');

Route::get('/{locale}', function (string $locale) use ($resolveLocale, $baseViewData) {
    $locale = $resolveLocale($locale);
    $data = $baseViewData($locale);
    $data['title'] = 'EasyPsi';

    return view('welcome', $data);
})->name('welcome.locale');

Route::get('/{locale}/payment', function (string $locale) use ($resolveLocale, $baseViewData) {
    $locale = $resolveLocale($locale);
    $data = $baseViewData($locale);

    return view('payment', $data);
})->name('payment.locale');

Route::middleware('guest')->group(function () use ($resolveLocale, $baseViewData) {
    Route::get('/{locale}/login', function (string $locale) use ($resolveLocale, $baseViewData) {
        $locale = $resolveLocale($locale);
        return view('login', $baseViewData($locale));
    })->name('login.locale');

    Route::post('/{locale}/login', function (Request $request, string $locale) use ($resolveLocale) {
        $locale = $resolveLocale($locale);
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');
        $user = User::where('email', $credentials['email'])->first();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return back()->withErrors(['email' => config("easypsi.locales.$locale.login.verify_email_required")])->withInput();
        }

        if (! Auth::attempt($credentials, $remember)) {
            return back()->withErrors(['email' => __('auth.failed')])->withInput();
        }

        $request->session()->regenerate();

        $role = Auth::user()?->role;

        return match ($role) {
            'teacher' => redirect()->route('teacher.space.locale', ['locale' => $locale]),
            'admin' => redirect()->route('admin.locale', ['locale' => $locale]),
            default => redirect()->route('teacher.index.locale', ['locale' => $locale]),
        };
    })->name('login.submit');

    Route::get('/{locale}/register', function (string $locale) use ($resolveLocale, $baseViewData) {
        $locale = $resolveLocale($locale);
        $data = $baseViewData($locale);
        $data['registerRoles'] = match ($locale) {
            'ar' => ['student' => 'تلميذ', 'teacher' => 'أستاذ'],
            'en' => ['student' => 'Student', 'teacher' => 'Teacher'],
            default => ['student' => 'Élève', 'teacher' => 'Professeur'],
        };

        return view('register', $data);
    })->name('register.locale');

    Route::post('/{locale}/register', function (Request $request, string $locale) use ($resolveLocale) {
        $locale = $resolveLocale($locale);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'in:student,teacher'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'preferred_locale' => $locale,
            'subscription_tier' => 'free',
            'password' => $validated['password'],
        ]);

        event(new Registered($user));

        return redirect()->route('login.locale', ['locale' => $locale])->with('status', config("easypsi.locales.$locale.login.success_registered"));
    })->name('register.submit');

    Route::get('/{locale}/forgot-password', function (string $locale) use ($resolveLocale, $baseViewData) {
        $locale = $resolveLocale($locale);
        return view('forgot-password', $baseViewData($locale));
    })->name('password.request');

    Route::post('/{locale}/forgot-password', function (Request $request, string $locale) use ($resolveLocale) {
        $locale = $resolveLocale($locale);
        $request->validate(['email' => ['required', 'email']]);
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    })->name('password.email');

    Route::get('/{locale}/reset-password/{token}', function (string $locale, string $token, Request $request) use ($resolveLocale, $baseViewData) {
        $locale = $resolveLocale($locale);
        $data = $baseViewData($locale);
        $data['token'] = $token;
        $data['email'] = (string) $request->query('email');

        return view('reset-password', $data);
    })->name('password.reset');

    Route::post('/{locale}/reset-password', function (Request $request, string $locale) use ($resolveLocale) {
        $locale = $resolveLocale($locale);
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login.locale', ['locale' => $locale])->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    })->name('password.store');
});

Route::post('/{locale}/logout', function (Request $request, string $locale) use ($resolveLocale) {
    $locale = $resolveLocale($locale);
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('welcome.locale', ['locale' => $locale]);
})->middleware('auth')->name('logout.locale');

Route::middleware('auth')->group(function () use (
    $resolveLocale,
    $baseViewData,
    $teacherSubjectOptions,
    $levelMatrix,
    $teacherCopy,
    $youtubeEmbedUrl,
    $studentHasPremiumAccess
) {
    Route::get('/{locale}/verify-email', function (string $locale) use ($resolveLocale, $baseViewData) {
        $locale = $resolveLocale($locale);
        return view('verify-email', $baseViewData($locale));
    })->name('verification.notice');

    Route::get('/{locale}/verify-email/{id}/{hash}', function (EmailVerificationRequest $request, string $locale) {
        $request->fulfill();
        event(new Verified($request->user()));

        return redirect()->route('teacher.index.locale', ['locale' => $locale])->with('status', 'verified');
    })->middleware('signed')->name('verification.verify');

    Route::post('/{locale}/email/verification-notification', function (Request $request, string $locale) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::get('/{locale}/student-profile', function (Request $request, string $locale) use ($resolveLocale, $baseViewData) {
        $locale = $resolveLocale($locale);
        $data = $baseViewData($locale);
        $data['user'] = $request->user();

        return view('student-profile', $data);
    })->name('student.profile.locale');

    Route::patch('/profile', function (Request $request) {
        $user = $request->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'profile_photo' => ['nullable', 'image', 'max:5120'],
            'profile_photo_cropped' => ['nullable', 'string'],
        ]);

        if (filled($validated['profile_photo_cropped'] ?? null) && preg_match('/^data:image\/(\w+);base64,/', $validated['profile_photo_cropped'], $matches) === 1) {
            $extension = strtolower($matches[1]);
            $supportedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $extension = in_array($extension, $supportedExtensions, true) ? $extension : 'png';

            $imageData = base64_decode(substr($validated['profile_photo_cropped'], strpos($validated['profile_photo_cropped'], ',') + 1), true);

            if ($imageData !== false) {
                if (filled($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                $path = 'profile-photos/'.Str::uuid().'.'.$extension;
                Storage::disk('public')->put($path, $imageData);
                $validated['profile_photo_path'] = $path;
            }
        } elseif ($request->hasFile('profile_photo')) {
            if (filled($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $validated['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        unset($validated['profile_photo']);
        unset($validated['profile_photo_cropped']);

        $user->fill($validated);
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        return back()->with('status', 'profile-updated');
    })->name('profile.update');

    Route::put('/password', function (Request $request) {
        $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);
        $request->user()->update(['password' => $request->string('password')->toString()]);

        return back()->with('status', 'password-updated');
    })->name('password.update');

    Route::get('/{locale}/teachers', function (Request $request, string $locale) use ($resolveLocale, $baseViewData, $teacherSubjectOptions, $levelMatrix, $teacherCopy) {
        $locale = $resolveLocale($locale);
        $data = $baseViewData($locale);
        $copy = $teacherCopy($locale);
        $matrix = $levelMatrix($locale);
        $subjectOptions = $teacherSubjectOptions($locale);

        $query = TeacherLesson::query()->with('teacher');

        $selectedLevel = (string) $request->query('level');
        $selectedTrack = (string) $request->query('track');
        $selectedSubject = (string) $request->query('subject');
        $teacherName = trim((string) $request->query('teacher'));

        if ($selectedLevel !== '') {
            $query->forAudience($selectedLevel.($selectedTrack !== '' ? '::'.$selectedTrack : ''));
        }
        if ($selectedSubject !== '') {
            $query->where('subject_key', $selectedSubject);
        }
        if ($teacherName !== '') {
            $query->whereHas('teacher', fn ($q) => $q->where('name', 'like', '%'.$teacherName.'%'));
        }

        $teachers = User::where('role', 'teacher')
            ->whereHas('teacherLessons', function ($q) use ($selectedLevel, $selectedTrack, $selectedSubject, $teacherName) {
                if ($selectedLevel !== '') {
                    $q->forAudience($selectedLevel.($selectedTrack !== '' ? '::'.$selectedTrack : ''));
                }
                if ($selectedSubject !== '') {
                    $q->where('subject_key', $selectedSubject);
                }
                if ($teacherName !== '') {
                    $q->whereHas('teacher', fn ($teacherQuery) => $teacherQuery->where('name', 'like', '%'.$teacherName.'%'));
                }
            })
            ->with(['teacherLessons' => function ($q) {
                $q->orderBy('level_label')
                    ->orderBy('subject_label')
                    ->orderBy('title');
            }])
            ->withCount(['teacherLessons as lessons_count'])
            ->get();

        $data = array_merge($data, [
            'title' => 'EasyPsi | Teachers',
            'teacherPageCopy' => $copy,
            'matrix' => $matrix,
            'subjectOptions' => $subjectOptions,
            'teachers' => $teachers,
            'selectedLevel' => $selectedLevel,
            'selectedTrack' => $selectedTrack,
            'selectedSubject' => $selectedSubject,
            'teacherName' => $teacherName,
        ]);

        return view('teacher-index', $data);
    })->name('teacher.index.locale');

    Route::get('/{locale}/teachers/{teacher}', function (Request $request, string $locale, User $teacher) use ($resolveLocale, $baseViewData, $teacherCopy, $youtubeEmbedUrl, $studentHasPremiumAccess) {
        abort_unless($teacher->role === 'teacher', 404);

        $locale = $resolveLocale($locale);
        $copy = $teacherCopy($locale);
        $allLessons = TeacherLesson::with(['assets', 'teacher'])
            ->where('teacher_id', $teacher->id)
            ->orderByRaw('sort_order = 0')
            ->orderBy('sort_order')
            ->orderBy('level_label')
            ->orderBy('created_at')
            ->get()
            ->filter(function (TeacherLesson $lesson) use ($youtubeEmbedUrl): bool {
                $courseAsset = $lesson->assets->firstWhere('part', 'course');

                return $courseAsset && filled($youtubeEmbedUrl($courseAsset->youtube_url ?? null));
            })
            ->values();

        $requestedSubject = (string) $request->query('subject', '');
        $requestedLevel = (string) $request->query('level', '');
        $requestedLessonSlug = (string) $request->query('lesson', '');

        if ($allLessons->isEmpty()) {
            $data = array_merge($baseViewData($locale), [
                'title' => 'EasyPsi | '.$teacher->name,
                'teacherPageCopy' => $copy,
                'teacherUser' => $teacher,
                'allLessons' => $allLessons,
                'groupedLessons' => collect(),
                'selectedSubject' => $requestedSubject,
                'selectedLevel' => $requestedLevel,
                'activeLesson' => null,
                'activePart' => (string) $request->query('part', 'course'),
                'activeAsset' => new TeacherLessonAsset(['part' => 'course', 'access_level' => 'free']),
                'embedUrl' => null,
                'locked' => false,
                'isEmptyTeacher' => true,
                'isEmptyFilter' => false,
            ]);

            return view('teacher-course', $data);
        }

        $requestedLesson = $requestedLessonSlug !== ''
            ? $allLessons->firstWhere('slug', $requestedLessonSlug)
            : null;

        $fallbackLesson = $requestedLesson ?? $allLessons->first();

        $selectedSubject = $requestedSubject !== '' ? $requestedSubject : ($fallbackLesson->subject_key ?? '');
        if ($requestedLevel !== '' && $requestedSubject === '') {
            $selectedSubject = '';
        }
        $selectedLevel = $requestedLevel !== '' ? $requestedLevel : ($fallbackLesson->level_key ?? '');
        if (str_contains($selectedLevel, '|')) {
            $selectedLevel = explode('|', $selectedLevel, 2)[0];
        }

        $lessons = $allLessons
            ->filter(fn ($item) => $selectedSubject !== '')
            ->when($selectedSubject !== '', fn ($collection) => $collection->where('subject_key', $selectedSubject))
            ->when($selectedLevel !== '', fn ($collection) => $collection->filter(fn ($item) => \App\Support\LevelAudience::matches($item->level_key, $selectedLevel)))
            ->values();

        if ($lessons->isEmpty()) {
            $data = array_merge($baseViewData($locale), [
                'title' => 'EasyPsi | '.$teacher->name,
                'teacherPageCopy' => $copy,
                'teacherUser' => $teacher,
                'allLessons' => $allLessons,
                'groupedLessons' => collect(),
                'selectedSubject' => $selectedSubject,
                'selectedLevel' => $selectedLevel,
                'activeLesson' => null,
                'activePart' => (string) $request->query('part', 'course'),
                'activeAsset' => new TeacherLessonAsset(['part' => 'course', 'access_level' => 'free']),
                'embedUrl' => null,
                'locked' => false,
                'isEmptyTeacher' => false,
                'isEmptyFilter' => true,
            ]);

            return view('teacher-course', $data);
        }

        $lesson = $lessons->firstWhere('slug', $requestedLessonSlug) ?? $lessons->first();
        $part = (string) $request->query('part', 'course');
        $asset = $lesson->assets->firstWhere('part', $part) ?? $lesson->assets->first() ?? new TeacherLessonAsset([
            'part' => 'course',
            'access_level' => 'free',
        ]);

        if (($asset->part ?? '') !== 'quiz' && blank($youtubeEmbedUrl($asset->youtube_url ?? null))) {
            $part = 'course';
            $asset = $lesson->assets->firstWhere('part', 'course') ?? new TeacherLessonAsset([
                'part' => 'course',
                'access_level' => 'free',
            ]);
        }

        $locked = ($asset->access_level ?? 'free') === 'premium' && ! $studentHasPremiumAccess($request->user(), $lesson);

        $groupedLessons = $lessons->groupBy('level_label')->map(fn ($items) => $items->values());

        $data = array_merge($baseViewData($locale), [
            'title' => 'EasyPsi | '.$teacher->name,
            'teacherPageCopy' => $copy,
            'teacherUser' => $teacher,
            'allLessons' => $allLessons,
            'groupedLessons' => $groupedLessons,
            'selectedSubject' => $selectedSubject,
            'selectedLevel' => $selectedLevel,
            'activeLesson' => $lesson,
            'activePart' => $part,
            'activeAsset' => $asset,
            'embedUrl' => $youtubeEmbedUrl($asset->youtube_url ?? null),
            'locked' => $locked,
            'isEmptyTeacher' => false,
            'isEmptyFilter' => false,
        ]);

        return view('teacher-course', $data);
    })->name('teacher.course.locale');

    Route::post('/{locale}/course-chat', function (Request $request, string $locale) use ($resolveLocale) {
        $locale = $resolveLocale($locale);
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'title' => ['nullable', 'string', 'max:255'],
            'teacher' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:6000'],
            'support' => ['nullable', 'string', 'max:6000'],
            'quiz' => ['nullable', 'array', 'max:50'],
            'quiz.*' => ['string', 'max:500'],
        ]);

        $apiKey = env('GEMINI_API_KEY');
        if (! filled($apiKey)) {
            return response()->json(['error' => 'AI not configured'], 503);
        }

        $langName = match ($locale) {
            'ar' => 'Arabic',
            'en' => 'English',
            default => 'French',
        };
        $quizText = collect($validated['quiz'] ?? [])->take(10)->implode("\r\n- ");
        $system = "You are the lesson assistant for an online course platform. Lesson: ".($validated['title'] ?? '')." by ".($validated['teacher'] ?? '').". First use the lesson content below to answer, in {$langName}, in 2-4 short sentences. If the question goes beyond that content, answer from your own knowledge while staying consistent with the lesson topic and the student's level. Never invent quiz questions or premium details. Write all mathematics in plain readable text, never LaTeX: no dollar signs, no backslash commands. Write units like tr/min and rad/s, and formulas like ω = 2π x N / 60.\r\n\r\nDESCRIPTION:\r\n".($validated['description'] ?? '')."\r\n\r\nSUPPORT NOTES:\r\n".($validated['support'] ?? '')."\r\n\r\nQUIZ QUESTIONS:\r\n- ".$quizText;

        try {
            $response = Http::timeout(20)->withHeaders(['x-goog-api-key' => $apiKey, 'Content-Type' => 'application/json'])->post(
                'https://generativelanguage.googleapis.com/v1beta/models/'.env('GEMINI_MODEL', 'gemini-3.6-flash').':generateContent',
                [
                    'systemInstruction' => ['parts' => [['text' => $system]]],
                    'contents' => [['parts' => [['text' => $validated['message']]]]],
                    'generationConfig' => ['maxOutputTokens' => 1024, 'temperature' => 0.4],
                ]
            );
        } catch (\Throwable $e) {
            return response()->json(['error' => 'AI unreachable'], 502);
        }

        if (! $response->successful()) {
            return response()->json(['error' => 'AI error'], 502);
        }

        $reply = data_get($response->json(), 'candidates.0.content.parts.0.text');
        if (! filled($reply)) {
            return response()->json(['error' => 'Empty reply'], 502);
        }

        return response()->json(['reply' => trim($reply)]);
    })->middleware('throttle:15,1')->name('course.chat');

    Route::get('/{locale}/teacher-space', function (Request $request, string $locale) use ($resolveLocale, $baseViewData, $teacherSubjectOptions, $levelMatrix, $teacherCopy) {
        abort_unless(in_array($request->user()->role, ['teacher', 'admin'], true), 403);
    $locale = $resolveLocale($locale);
        $matrix = $levelMatrix($locale);
        $subjectOptions = $teacherSubjectOptions($locale);
        $teacher = $request->user()->role === 'teacher' ? $request->user() : User::where('role', 'teacher')->first();

        $lessons = TeacherLesson::with('assets')
            ->where('teacher_id', $teacher?->id)
            ->whereHas('assets')
            ->orderByRaw('sort_order = 0')
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get();

        return view('teacher-dashboard', array_merge($baseViewData($locale), [
            'title' => 'EasyPsi | Teacher Space',
            'teacherPageCopy' => $teacherCopy($locale),
            'matrix' => $matrix,
            'subjectOptions' => $subjectOptions,
            'teacherLessons' => $lessons,
            'teacherUser' => $teacher,
        ]));
    })->name('teacher.space.locale');

    Route::post('/{locale}/teacher-space/content', function (Request $request, string $locale) use ($resolveLocale, $teacherSubjectOptions, $levelMatrix) {
        abort_unless(in_array($request->user()->role, ['teacher', 'admin'], true), 403);
        $locale = $resolveLocale($locale);
        $subjectOptions = $teacherSubjectOptions($locale);
        $matrix = $levelMatrix($locale);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255', 'required_without:existing_lesson'],
            'existing_lesson' => ['nullable', 'integer'],
            'level' => ['required', 'string'],
            'track' => ['nullable', 'array'],
            'track.*' => ['nullable', 'string'],
            'subject' => ['required', 'string'],
            'part' => ['required', 'in:course,exercise,quiz'],
            'access_level' => ['required', 'in:free,premium'],
            'youtube_url' => ['nullable', 'url'],
            'description_body' => ['nullable', 'string'],
            'support_body' => ['nullable', 'string'],
            'quiz_body' => ['nullable', 'string'],
            'support_file' => ['nullable', 'file', 'max:10240'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        $levelKey = $validated['level'];
        abort_unless(isset($matrix[$levelKey]), 422, 'Niveau invalide.');
        $submittedTracks = array_values(array_unique(array_filter($validated['track'] ?? [])));
        $availableTracks = $matrix[$levelKey]['tracks'];
        if (($availableTracks && ! $submittedTracks) || array_diff($submittedTracks, array_keys($availableTracks))) {
            throw \Illuminate\Validation\ValidationException::withMessages(['track' => 'Choisissez au moins une filiere valide.']);
        }
        $validSubject = isset($subjectOptions[$validated['subject']]);
        foreach ($submittedTracks as $track) {
            $validSubject = $validSubject && in_array($validated['subject'], $availableTracks[$track]['subjects'], true);
        }
        if (! $validSubject) {
            throw \Illuminate\Validation\ValidationException::withMessages(['subject' => 'Choisissez une matiere commune aux filieres selectionnees.']);
        }
        $levelLabel = $matrix[$validated['level']]['label'] ?? $validated['level'];
        $selectedTracks = array_values(array_filter((array) ($validated['track'] ?? []), fn ($trackKey) => filled($trackKey) && isset($matrix[$validated['level']]['tracks'][$trackKey])));
        if ($selectedTracks !== []) {
            $levelKey = $validated['level'].'::'.implode('|', $selectedTracks);
            $trackLabels = array_map(fn ($trackKey) => $matrix[$validated['level']]['tracks'][$trackKey]['label'], $selectedTracks);
            $levelLabel .= ' / '.implode(' + ', $trackLabels);
        }

        $teacherId = $request->user()->role === 'teacher' ? $request->user()->id : User::where('role', 'teacher')->value('id');

        $lesson = null;
        if (filled($validated['existing_lesson'] ?? null)) {
            $lesson = TeacherLesson::where('id', $validated['existing_lesson'])
                ->where('teacher_id', $teacherId)
                ->first();
        }

        if (! $lesson) {
            $newSortOrder = $validated['sort_order'] ?? null;
            if ($newSortOrder === null || $newSortOrder === '') {
                $newSortOrder = (int) TeacherLesson::where('teacher_id', $teacherId)
                    ->where('locale', $locale)
                    ->where('level_key', $levelKey)
                    ->where('subject_key', $validated['subject'])
                    ->max('sort_order') + 1;
            }
            $lesson = TeacherLesson::create([
                'teacher_id' => $teacherId,
                'locale' => $locale,
                'slug' => Str::slug($validated['title']).'-'.Str::lower(Str::random(6)),
                'subject_key' => $validated['subject'],
                'subject_label' => $subjectOptions[$validated['subject']] ?? $validated['subject'],
                'level_key' => $levelKey,
                'level_label' => $levelLabel,
                'title' => $validated['title'],
                'sort_order' => (int) $newSortOrder,
            ]);
        } else {
            $lesson->update([
                'locale' => $locale,
                'subject_key' => $validated['subject'],
                'subject_label' => $subjectOptions[$validated['subject']] ?? $validated['subject'],
                'level_key' => $levelKey,
                'level_label' => $levelLabel,
                'title' => filled($validated['title'] ?? null) ? $validated['title'] : $lesson->title,
                'sort_order' => array_key_exists('sort_order', $validated) && $validated['sort_order'] !== null ? (int) $validated['sort_order'] : ($lesson->sort_order ?? 0),
            ]);
        }

        $quizBody = null;
        if ($validated['part'] === 'quiz') {
            $quizPayload = json_decode($validated['quiz_body'] ?? '', true);
            $questions = [];

            foreach ((array) data_get($quizPayload, 'questions', []) as $questionIndex => $question) {
                $questionRow = [
                    'question' => trim((string) data_get($question, 'question', '')),
                    'question_image_path' => data_get($question, 'question_image_path'),
                    'choices' => [],
                    'correct' => (string) data_get($question, 'correct', ''),
                ];

                $questionFile = data_get($request->file('quiz_question_images', []), $questionIndex);
                if ($questionFile) {
                    $path = $questionFile->store('teacher-quizzes/questions', 'public');
                    $questionRow['question_image_path'] = $path;
                    $questionRow['question_image_url'] = Storage::disk('public')->url($path);
                } elseif (! empty($questionRow['question_image_path'])) {
                    $questionRow['question_image_url'] = Storage::disk('public')->url($questionRow['question_image_path']);
                }

                foreach ((array) data_get($question, 'choices', []) as $choiceIndex => $choice) {
                    $choiceRow = [
                        'text' => trim((string) data_get($choice, 'text', '')),
                        'image_path' => data_get($choice, 'image_path'),
                    ];

                    $choiceFile = data_get($request->file('quiz_choice_images', []), $questionIndex.'.'.$choiceIndex);
                    if ($choiceFile) {
                        $path = $choiceFile->store('teacher-quizzes/choices', 'public');
                        $choiceRow['image_path'] = $path;
                        $choiceRow['image_url'] = Storage::disk('public')->url($path);
                    } elseif (! empty($choiceRow['image_path'])) {
                        $choiceRow['image_url'] = Storage::disk('public')->url($choiceRow['image_path']);
                    }

                    if ($choiceRow['text'] !== '' || ! empty($choiceRow['image_path'])) {
                        $questionRow['choices'][] = $choiceRow;
                    }
                }

                if ($questionRow['question'] !== '' || ! empty($questionRow['question_image_path']) || ! empty($questionRow['choices'])) {
                    $questions[] = $questionRow;
                }
            }

            $quizBody = json_encode(['questions' => $questions], JSON_UNESCAPED_UNICODE);
        }

        $assetData = [
            'part' => $validated['part'],
            'access_level' => $validated['access_level'],
            'content_type' => $validated['part'] === 'quiz' ? 'quiz' : 'youtube',
            'youtube_url' => $validated['part'] === 'quiz' ? null : ($validated['youtube_url'] ?? null),
            'description_body' => $validated['part'] === 'quiz' ? null : ($validated['description_body'] ?? null),
            'support_body' => $validated['part'] === 'quiz' ? null : ($validated['support_body'] ?? null),
            'quiz_body' => $validated['part'] === 'quiz' ? $quizBody : null,
        ];

        $existingAsset = $lesson->assets()->where('part', $validated['part'])->first();

        if ($request->hasFile('support_file')) {
            if ($existingAsset && filled($existingAsset->support_file_path ?? null)) {
                Storage::disk('public')->delete($existingAsset->support_file_path);
            }
            $assetData['support_file_path'] = $request->file('support_file')->store('teacher-supports', 'public');
        } elseif ($existingAsset && filled($existingAsset->support_file_path ?? null) && $validated['part'] !== 'quiz') {
            $assetData['support_file_path'] = $existingAsset->support_file_path;
        }

        $lesson->assets()->updateOrCreate(
            ['part' => $validated['part']],
            $assetData
        );

        return redirect()->route('teacher.space.locale', ['locale' => $locale])->with('status', 'Contenu enregistré.');
    })->name('teacher.content.store');

    Route::post('/{locale}/teacher-space/content/{asset}/delete', function (Request $request, string $locale, TeacherLessonAsset $asset) use ($resolveLocale) {
        abort_unless(in_array($request->user()->role, ['teacher', 'admin'], true), 403);
        $locale = $resolveLocale($locale);
        if (filled($asset->support_file_path ?? null)) {
            Storage::disk('public')->delete($asset->support_file_path);
        }
        $lesson = $asset->lesson;
        $asset->delete();

        if ($lesson && ! $lesson->assets()->exists()) {
            $lesson->delete();
        }

        return redirect()->route('teacher.space.locale', ['locale' => $locale])->with('status', 'Contenu supprimé.');
    })->name('teacher.content.delete');

    Route::get('/{locale}/admin', function (Request $request, string $locale) use ($resolveLocale, $baseViewData) {
        abort_unless($request->user()->role === 'admin', 403);
        $locale = $resolveLocale($locale);
        $search = trim((string) $request->query('student_search'));
        $users = User::query()->where('role', 'student');
        if ($search !== '') {
            $users->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%');
            });
        }

        $levelOptions = TeacherLesson::query()->select('level_key', 'level_label')->distinct()->get()
            ->flatMap(fn ($item) => \App\Support\LevelAudience::choices($item->level_key, $item->level_label))
            ->map(fn ($label, $key) => ['key' => $key, 'label' => $label])
            ->values()
            ->all();

        return view('admin', array_merge($baseViewData($locale), [
            'users' => $users->orderBy('name')->limit(10)->get(),
            'studentSearch' => $search,
            'adminStats' => [
                'students' => User::where('role', 'student')->count(),
                'premium' => User::where('role', 'student')->where('subscription_tier', 'premium')->count(),
            ],
            'levelOptions' => $levelOptions,
            'todayDate' => now()->format('Y-m-d'),
        ]));
    })->name('admin.locale');

    Route::post('/{locale}/admin/users/{user}/subscription', function (Request $request, string $locale, User $user) use ($resolveLocale) {
        abort_unless($request->user()->role === 'admin', 403);
    $locale = $resolveLocale($locale);
        $validated = $request->validate([
            'subscription_tier' => ['required', 'in:free,premium'],
            'premium_duration' => ['nullable', 'in:1_month,6_months,1_year'],
            'premium_level_key' => ['nullable', 'string', 'max:255'],
            'student_search' => ['nullable', 'string'],
        ]);

        $user->subscription_tier = $validated['subscription_tier'];
        $user->premium_level_key = $validated['subscription_tier'] === 'premium' ? ($validated['premium_level_key'] ?? null) : null;
        $user->premium_duration = $validated['subscription_tier'] === 'premium' ? ($validated['premium_duration'] ?? null) : null;
        $user->premium_granted_at = $validated['subscription_tier'] === 'premium' ? now() : null;
        $user->premium_expires_at = match ($validated['premium_duration'] ?? null) {
            '1_month' => now()->addMonth(),
            '6_months' => now()->addMonths(6),
            '1_year' => now()->addYear(),
            default => null,
        };
        $user->save();

        return redirect()->route('admin.locale', ['locale' => $locale, 'student_search' => $validated['student_search'] ?? null])->with('status', 'Abonnement mis à jour.');
    })->name('admin.users.subscription.update');
});
