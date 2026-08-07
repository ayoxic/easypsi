<?php

namespace App\Notifications\Auth;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    use Queueable;

    public function toMail($notifiable): MailMessage
    {
        $locale = $notifiable->preferred_locale ?? config('app.locale', 'en');
        $copy = $this->contentFor($locale);
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject($copy['subject'])
            ->view('emails.auth.verify-email', [
                'title' => $copy['title'],
                'preheader' => $copy['preheader'],
                'greeting' => $copy['greeting'],
                'intro' => $copy['intro'],
                'supportText' => $copy['support'],
                'actionText' => $copy['action'],
                'outro' => $copy['outro'],
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
                'subject' => 'EasyPsi | Vérification de votre adresse email',
                'title' => 'Vérification de votre adresse email',
                'preheader' => 'Un lien sécurisé vous attend pour activer votre compte EasyPsi.',
                'greeting' => 'Bonjour,',
                'intro' => 'Vous recevez cet email parce qu\'un nouveau compte EasyPsi a été créé avec cette adresse email.',
                'support' => 'Pour protéger votre accès et finaliser l\'activation de votre compte, utilisez le bouton ci-dessous.',
                'action' => 'Vérifier mon adresse email',
                'outro' => 'Ce lien de vérification expirera automatiquement après un certain délai de sécurité.',
                'ignore' => 'Si vous n\'êtes pas à l\'origine de cette inscription, aucune action supplémentaire n\'est nécessaire.',
                'footer' => 'Besoin d\'aide ? Vous pouvez recontacter l\'équipe EasyPsi.',
                'dir' => 'ltr',
                'lang' => 'fr',
            ],
            'ar' => [
                'subject' => 'EasyPsi | تأكيد بريدك الإلكتروني',
                'title' => 'تأكيد بريدك الإلكتروني',
                'preheader' => '.EasyPsi يوجد رابط آمن لتفعيل حسابك على منصة ',
                'greeting' => 'مرحبا،',
                'intro' => ' تم إنشاؤه باستخدام هذا البريد الإلكتروني -EasyPsi- لقد تلقيت هذه الرسالة لأن حسابًا جديدًا على منصة ',
                'support' => 'استخدم الزر التالي لتأكيد بريدك الإلكتروني -EasyPsi- لحماية حسابك وإكمال تفعيله على منصة',
                'action' => 'تأكيد بريدي الإلكتروني',
                'outro' => 'سينتهي رابط التحقق هذا تلقائيا بعد مدة أمان محددة.',
                'ignore' => 'إذا لم تكن أنت من قام بإنشاء هذا الحساب، فلا يلزم اتخاذ أي إجراء إضافي.',
                'footer' => '-EasyPsi- إذا احتجت إلى المساعدة، يمكنك التواصل مع فريق منصة .',
                'dir' => 'rtl',
                'lang' => 'ar',
            ],
            default => [
                'subject' => 'EasyPsi | Verify your email address',
                'title' => 'Verify your email address',
                'preheader' => 'A secure link is ready so you can activate your EasyPsi account.',
                'greeting' => 'Hello,',
                'intro' => 'You are receiving this email because a new EasyPsi account was created with this email address.',
                'support' => 'To secure your access and finish activating your account, use the button below.',
                'action' => 'Verify my email address',
                'outro' => 'This verification link will expire automatically after a security time window.',
                'ignore' => 'If you did not create this account, no further action is required.',
                'footer' => 'Need help? You can contact the EasyPsi team at any time.',
                'dir' => 'ltr',
                'lang' => 'en',
            ],
        };
    }
}
