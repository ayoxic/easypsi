<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
</head>
<body style="margin:0; padding:0; background:linear-gradient(180deg, #fff6cc 0%, #f7fbff 100%); font-family:Arial, Helvetica, sans-serif; color:#10213a;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0;">
        {{ $preheader }}
    </div>
    <div style="padding:34px 16px;">
        <div style="max-width:620px; margin:0 auto; background-color:#ffffff; border-radius:28px; overflow:hidden; box-shadow:0 22px 56px rgba(15, 23, 42, 0.12);">
            <div style="background:linear-gradient(135deg, #f7c948 0%, #275db5 100%); padding:28px 32px 24px; color:#ffffff;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="vertical-align:middle; width:64px;">
                            <img src="{{ $brandLogoUrl }}" alt="EasyPsi logo" style="display:block; width:40px; height:40px; object-fit:cover; border-radius:12px; background-color:#ffffff; box-shadow:0 8px 18px rgba(16, 33, 58, 0.18);">
                        </td>
                        <td style="vertical-align:middle; padding-{{ $dir === 'rtl' ? 'right' : 'left' }}:6px;">
                            <div style="display:inline-block; margin-bottom:14px; padding:8px 14px; border-radius:999px; background-color:rgba(255,255,255,0.18); color:#fff6cf; font-size:12px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase;">
                                EasyPsi
                            </div>
                            <h1 style="margin:0; max-width:440px; font-size:31px; line-height:1.12; font-weight:700; color:#ffffff;">{{ $title }}</h1>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="padding:30px 32px 34px;">
                <div style="width:74px; height:4px; border-radius:999px; background:linear-gradient(90deg, #e8c44f 0%, #2d6fd2 100%); margin-bottom:26px;"></div>
                <p style="margin:0 0 18px; font-size:16px; line-height:1.72; color:#21324d;">{{ $greeting }}</p>
                <p style="margin:0 0 18px; font-size:16px; line-height:1.72; color:#21324d;">{{ $intro }}</p>
                <p style="margin:0 0 0; font-size:16px; line-height:1.72; color:#4c5f7d;">{{ $supportText }}</p>

                <div style="margin:34px 0 28px;">
                    <a href="{{ $buttonUrl }}" style="display:inline-block; background:linear-gradient(135deg, #1d56b0 0%, #2c71d4 100%); color:#ffffff; text-decoration:none; padding:15px 24px; min-width:222px; text-align:center; border-radius:999px; font-size:15px; font-weight:700; box-shadow:0 12px 24px rgba(29, 86, 176, 0.22);">
                        {{ $actionText }}
                    </a>
                </div>

                <p style="margin:0 0 16px; font-size:15px; line-height:1.75; color:#4c5f7d;">{{ $outro }}</p>
                <p style="margin:0 0 26px; font-size:15px; line-height:1.75; color:#4c5f7d;">{{ $ignoreText }}</p>
                <p style="margin:0 0 28px; font-size:14px; line-height:1.75; color:#6b7280;">{{ $footerText }}</p>

                <p style="margin:0; font-size:13px; line-height:1.7; color:#4e7fd1; word-break:break-all;">
                    {{ $buttonUrl }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
