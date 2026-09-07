@extends('layouts.app')

@php
    $whatsappBase = 'https://wa.me/' . $whatsappNumber;
    $profileCopy = match ($locale) {
        'fr' => [
            'title' => 'Mon profile',
            'subtitle' => 'Mettez à jour vos informations et votre mot de passe.',
            'info_title' => 'Informations personnelles',
            'info_text' => 'Modifiez votre nom, email et téléphone.',
            'photo' => 'Photo de profil',
            'photo_text' => 'Choisissez une image, déplacez-la dans le cadre et ajustez le zoom avant d’enregistrer.',
            'zoom' => 'Zoom',
            'crop_note' => 'La photo sera enregistrée en carré.',
            'security_title' => 'Sécurité',
            'security_text' => 'Changez votre mot de passe pour protéger votre compte.',
            'name' => 'Nom',
            'email' => 'Email',
            'phone' => 'Téléphone',
            'save' => 'Enregistrer',
            'current_password' => 'Mot de passe actuel',
            'new_password' => 'Nouveau mot de passe',
            'confirm_password' => 'Confirmer le mot de passe',
            'saved_profile' => 'Profile mis à jour.',
            'saved_password' => 'Mot de passe mis à jour.',
        ],
        'ar' => [
            'title' => 'الملف الشخصي',
            'subtitle' => 'قم بتحديث معلوماتك وكلمة المرور.',
            'info_title' => 'المعلومات الشخصية',
            'info_text' => 'عدل الاسم والبريد الالكتروني والهاتف.',
            'photo' => 'الصورة الشخصية',
            'photo_text' => 'اختر صورة ثم حركها داخل الإطار وعدل التكبير قبل الحفظ.',
            'zoom' => 'التكبير',
            'crop_note' => 'سيتم حفظ الصورة بشكل مربع.',
            'security_title' => 'الامان',
            'security_text' => 'غير كلمة المرور لحماية حسابك.',
            'name' => 'الاسم',
            'email' => 'البريد الالكتروني',
            'phone' => 'الهاتف',
            'save' => 'حفظ',
            'current_password' => 'كلمة المرور الحالية',
            'new_password' => 'كلمة المرور الجديدة',
            'confirm_password' => 'تأكيد كلمة المرور',
            'saved_profile' => 'تم تحديث الملف الشخصي.',
            'saved_password' => 'تم تحديث كلمة المرور.',
        ],
        default => [
            'title' => 'My profile',
            'subtitle' => 'Update your information and password.',
            'info_title' => 'Personal information',
            'info_text' => 'Edit your name, email, and phone number.',
            'photo' => 'Profile photo',
            'photo_text' => 'Choose an image, move it inside the frame, and adjust the zoom before saving.',
            'zoom' => 'Zoom',
            'crop_note' => 'The photo will be saved as a square.',
            'security_title' => 'Security',
            'security_text' => 'Change your password to keep your account safe.',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'save' => 'Save',
            'current_password' => 'Current password',
            'new_password' => 'New password',
            'confirm_password' => 'Confirm password',
            'saved_profile' => 'Profile updated.',
            'saved_password' => 'Password updated.',
        ],
    };
@endphp

@section('content')
    <main class="student-shell">
        @include('partials.student-topbar', [
            'topbarTitle' => $profileCopy['title'],
            'routeName' => 'teacher.index.locale',
            'whatsappBase' => $whatsappBase,
        ])

        <section class="student-page-card">
            <div class="student-page-head">
                <h1>{{ $profileCopy['title'] }}</h1>
                <p>{{ $profileCopy['subtitle'] }}</p>
            </div>

            <div class="profile-grid">
                <article class="profile-section-card">
                    <h2>{{ $profileCopy['info_title'] }}</h2>
                    <p>{{ $profileCopy['info_text'] }}</p>

                    @if (session('status') === 'profile-updated')
                        <div class="student-form-success">{{ $profileCopy['saved_profile'] }}</div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" class="student-form-grid" enctype="multipart/form-data" data-profile-photo-form>
                        @csrf
                        @method('patch')

                        <div class="profile-photo-field">
                            <div class="profile-photo-field__preview" data-profile-photo-preview>
                                @if (filled($user->profile_photo_path))
                                    <img src="{{ Storage::disk('public')->url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="profile-photo-field__image" data-profile-photo-image>
                                @else
                                    <span class="profile-photo-field__initial" data-profile-photo-initial>{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                @endif
                            </div>

                            <div class="profile-photo-field__controls">
                                <p class="profile-photo-field__text">{{ $profileCopy['photo_text'] }}</p>
                                <label class="student-form-field profile-photo-field__input">
                                    <span>{{ $profileCopy['photo'] }}</span>
                                    <input type="file" name="profile_photo" accept="image/png,image/jpeg,image/jpg,image/webp" data-profile-photo-input>
                                    @error('profile_photo')
                                        <small>{{ $message }}</small>
                                    @enderror
                                </label>
                                <label class="student-form-field profile-photo-field__zoom">
                                    <span>{{ $profileCopy['zoom'] }}</span>
                                    <input type="range" min="1" max="3" step="0.01" value="1" data-profile-photo-zoom disabled>
                                </label>
                                <p class="profile-photo-field__hint">{{ $profileCopy['crop_note'] }}</p>
                                <input type="hidden" name="profile_photo_cropped" value="" data-profile-photo-cropped>
                            </div>
                        </div>

                        <label class="student-form-field">
                            <span>{{ $profileCopy['name'] }}</span>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <small>{{ $message }}</small>
                            @enderror
                        </label>

                        <label class="student-form-field">
                            <span>{{ $profileCopy['email'] }}</span>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <small>{{ $message }}</small>
                            @enderror
                        </label>

                        <label class="student-form-field">
                            <span>{{ $profileCopy['phone'] }}</span>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <small>{{ $message }}</small>
                            @enderror
                        </label>

                        <button class="primary-btn" type="submit">{{ $profileCopy['save'] }}</button>
                    </form>
                </article>

                <article class="profile-section-card">
                    <h2>{{ $profileCopy['security_title'] }}</h2>
                    <p>{{ $profileCopy['security_text'] }}</p>

                    @if (session('status') === 'password-updated')
                        <div class="student-form-success">{{ $profileCopy['saved_password'] }}</div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}" class="student-form-grid">
                        @csrf
                        @method('put')

                        <label class="student-form-field">
                            <span>{{ $profileCopy['current_password'] }}</span>
                            <input type="password" name="current_password" required>
                            @if ($errors->updatePassword->has('current_password'))
                                <small>{{ $errors->updatePassword->first('current_password') }}</small>
                            @endif
                        </label>

                        <label class="student-form-field">
                            <span>{{ $profileCopy['new_password'] }}</span>
                            <input type="password" name="password" required>
                            @if ($errors->updatePassword->has('password'))
                                <small>{{ $errors->updatePassword->first('password') }}</small>
                            @endif
                        </label>

                        <label class="student-form-field">
                            <span>{{ $profileCopy['confirm_password'] }}</span>
                            <input type="password" name="password_confirmation" required>
                            @if ($errors->updatePassword->has('password_confirmation'))
                                <small>{{ $errors->updatePassword->first('password_confirmation') }}</small>
                            @endif
                        </label>

                        <button class="primary-btn" type="submit">{{ $profileCopy['save'] }}</button>
                    </form>
                </article>
            </div>
        </section>

        @include('partials.student-footer')
    </main>
@endsection
