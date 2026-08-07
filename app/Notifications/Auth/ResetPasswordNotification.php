<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $token,
        protected ?string $preferredLocale = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $this->preferredLocale ?? $notifiable->preferred_locale ?? config('app.locale', 'en');
        $copy = $this->contentFor($locale);
        $expire = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire');
        $url = route('password.reset', [
            'locale' => $locale,
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject($copy['subject'])
            ->view('emails.auth.reset-password', [
                'title' => $copy['title'],
                'preheader' => $copy['preheader'],
                'greeting' => $copy['greeting'],
                'intro' => $copy['intro'],
                'supportText' => $copy['support'],
                'actionText' => $copy['action'],
                'outro' => str_replace(':count', (string) $expire, $copy['outro']),
                'ignoreText' => $copy['ignore'],
                'footerText' => $copy['footer'],
                'buttonUrl' => $url,
                'brandLogoUrl' => 'https://i.pinimg.com/736x/64/a7/8e/64a78e1c707256a58ae5ad1e87160ad9.jpg',
                'dir' => $copy['dir'],
                'lang' => $copy['lang'],
            ]);
    }

    protected function contentFor(string $locale): array
    {
        return match ($locale) {
            'fr' => [
                'subject' => 'EasyPsi | Réinitialisation de votre mot de passe',
                'title' => 'Réinitialisation de votre mot de passe',
                'preheader' => 'Un lien sécurisé vous attend pour choisir un nouveau mot de passe.',
                'greeting' => 'Bonjour,',
                'intro' => 'Vous recevez cet email parce qu\'une demande de réinitialisation du mot de passe a été effectuée pour votre compte.',
                'support' => 'Pour protéger votre accès à EasyPsi, utilisez le bouton ci-dessous pour définir un nouveau mot de passe.',
                'action' => 'Réinitialiser mon mot de passe',
                'outro' => 'Ce lien expirera dans :count minutes.',
                'ignore' => 'Si vous n\'êtes pas à l\'origine de cette demande, aucune action supplémentaire n\'est nécessaire.',
                'footer' => 'Besoin d\'aide ? Vous pouvez recontacter l\'équipe EasyPsi.',
                'dir' => 'ltr',
                'lang' => 'fr',
            ],
            'ar' => [
                'subject' => 'EasyPsi | إعادة تعيين كلمة المرور',
                'title' => 'إعادة تعيين كلمة المرور',
                'preheader' => 'يوجد رابط آمن لاختيار كلمة مرور جديدة لحسابك.',
                'greeting' => 'مرحبا،',
                'intro' => 'لقد تلقيت هذا البريد لأننا تلقينا طلبا لإعادة تعيين كلمة المرور الخاصة بحسابك.',
                'support' => 'لحماية حسابك ، استخدم الزر التالي لاختيار كلمة مرور جديدة.',
                'action' => 'إعادة تعيين كلمة المرور',
                'outro' => 'ستنتهي صلاحية هذا الرابط خلال :60 دقيقة.',
                'ignore' => 'إذا لم تطلب إعادة تعيين كلمة المرور، فلا يلزم اتخاذ أي إجراء.',
                'footer' => 'EasyPsi إذا احتجت إلى المساعدة، يمكنك التواصل مع فريق .',
                'dir' => 'rtl',
                'lang' => 'ar',
            ],
            default => [
                'subject' => 'EasyPsi | Reset your password',
                'title' => 'Password reset request',
                'preheader' => 'A secure link is ready so you can choose a new password.',
                'greeting' => 'Hello,',
                'intro' => 'You are receiving this email because we received a password reset request for your account.',
                'support' => 'To keep your EasyPsi account secure, use the button below to set a new password.',
                'action' => 'Reset my password',
                'outro' => 'This password reset link will expire in :count minutes.',
                'ignore' => 'If you did not request a password reset, no further action is required.',
                'footer' => 'Need help? You can contact the EasyPsi team at any time.',
                'dir' => 'ltr',
                'lang' => 'en',
            ],
        };
    }
}
