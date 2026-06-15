<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| AUTOLOAD (IMPORTANT)
|--------------------------------------------------------------------------
| Required for PHPMailer
*/
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

/*
|--------------------------------------------------------------------------
| 1. CONFIG CHECK
|--------------------------------------------------------------------------
*/
if (!function_exists('app_mail_is_configured')) {
    function app_mail_is_configured(): bool
    {
        return !empty(MAIL_HOST)
            && !empty(MAIL_PORT)
            && !empty(MAIL_USERNAME)
            && !empty(MAIL_PASSWORD)
            && !empty(MAIL_FROM_EMAIL);
    }
}

/*
|--------------------------------------------------------------------------
| 2. SMTP CORE SENDER (ONLY ONE SYSTEM)
|--------------------------------------------------------------------------
*/
if (!function_exists('app_send_smtp_mail')) {

    function app_send_smtp_mail(
        string $toEmail,
        string $subject,
        string $htmlBody,
        string $textBody = '',
        array $inlineAttachments = []
    ): array {

        try {

            if (!app_mail_is_configured()) {
                return [
                    'success' => false,
                    'error' => 'SMTP not configured'
                ];
            }

            $mail = new PHPMailer(true);

            /*
            |--------------------------------------------------------------------------
            | SMTP CONFIG
            |--------------------------------------------------------------------------
            */
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->Port       = MAIL_PORT;

            $mail->SMTPSecure = (MAIL_ENCRYPTION === 'ssl')
                ? PHPMailer::ENCRYPTION_SMTPS
                : PHPMailer::ENCRYPTION_STARTTLS;

            $mail->Timeout = MAIL_TIMEOUT ?? 15;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ]
            ];

            /*
            |--------------------------------------------------------------------------
            | SENDER INFO
            |--------------------------------------------------------------------------
            */
            $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
            $mail->addReplyTo(MAIL_REPLY_TO_EMAIL ?: MAIL_FROM_EMAIL, MAIL_FROM_NAME);
            $mail->addAddress($toEmail);

            /*
            |--------------------------------------------------------------------------
            | INLINE ATTACHMENTS (LOGO)
            |--------------------------------------------------------------------------
            */
            foreach ($inlineAttachments as $att) {
                if (!empty($att['content']) && !empty($att['cid'])) {
                    $mail->addStringEmbeddedImage(
                        $att['content'],
                        $att['cid'],
                        $att['filename'] ?? 'image.png',
                        'base64',
                        $att['content_type'] ?? 'image/png'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CONTENT
            |--------------------------------------------------------------------------
            */
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $textBody;

            $mail->send();

            return [
                'success' => true,
                'error' => ''
            ];

        } catch (Exception $e) {

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

/*
|--------------------------------------------------------------------------
| 3. MAIN OTP SENDER (CORE FUNCTION)
|--------------------------------------------------------------------------
*/
if (!function_exists('app_send_branded_otp_email')) {

    function app_send_branded_otp_email(string $toEmail, string $otp, array $copy = []): array
    {
        $safeOtp = preg_replace('/\D+/', '', $otp);

        $defaults = [
            'subject'       => 'OTP Verification',
            'eyebrow'       => MAIL_FROM_NAME,
            'preheader'     => 'Secure verification',
            'headline'      => 'Your OTP Code',
            'intro'         => 'Use this OTP to continue.',
            'code_label'    => 'Verification Code',
            'expires_label' => 'Valid for ' . (APP_OTP_TTL_SECONDS ?? 300) . ' sec',
            'body_note'     => 'Never share this code.',
            'reason_title'  => 'Why you received this',
            'reason_body'   => 'You requested verification.',
            'ignore_title'  => 'Not you?',
            'ignore_body'   => 'Ignore this email.',
        ];

        $copy = array_merge($defaults, $copy);

        $inlineAssets = app_get_email_inline_assets();
        $logoCid = $inlineAssets[0]['cid'] ?? '';

        $html = app_build_otp_email_html($safeOtp, $copy, $logoCid);
        $text = app_build_otp_email_text($safeOtp, $copy);

        return app_send_smtp_mail(
            $toEmail,
            $copy['subject'],
            $html,
            $text,
            $inlineAssets
        );
    }
}

/*
|--------------------------------------------------------------------------
| 4. LOGIN OTP
|--------------------------------------------------------------------------
*/
if (!function_exists('app_send_login_otp_email')) {

    function app_send_login_otp_email(string $toEmail, string $username): array
    {
        $otp = (string) random_int(100000, 999999);

        $result = app_send_branded_otp_email($toEmail, $otp, [
            'subject'  => 'Login OTP',
            'headline' => 'Login Verification',
            'intro'    => 'Enter OTP to complete login.',
            'reason_body' => 'Login attempt for ' . $username,
        ]);

        $result['otp'] = $otp;
        return $result;
    }
}

/*
|--------------------------------------------------------------------------
| 5. SIGNUP OTP
|--------------------------------------------------------------------------
*/
if (!function_exists('app_send_signup_otp_email')) {

    function app_send_signup_otp_email(string $toEmail): array
    {
        $otp = (string) random_int(100000, 999999);

        $result = app_send_branded_otp_email($toEmail, $otp, [
            'subject'  => 'Signup OTP',
            'headline' => 'Verify Signup',
            'intro'    => 'Complete signup using OTP.',
        ]);

        $result['otp'] = $otp;
        return $result;
    }
}

/*
|--------------------------------------------------------------------------
| 6. PASSWORD RESET OTP
|--------------------------------------------------------------------------
*/
if (!function_exists('app_send_password_reset_email')) {

    function app_send_password_reset_email(string $toEmail, string $resetKey): array
    {
        return app_send_branded_otp_email($toEmail, $resetKey, [
            'subject'  => 'Password Reset OTP',
            'headline' => 'Reset Password',
            'intro'    => 'Use OTP to reset password.',
        ]);
    }
}

/*
|--------------------------------------------------------------------------
| 7. INLINE LOGO
|--------------------------------------------------------------------------
*/
if (!function_exists('app_get_email_inline_assets')) {

    function app_get_email_inline_assets(): array
    {
        $logoPath = dirname(__DIR__, 2) . '/images/logo2.png';

        if (!is_file($logoPath)) {
            return [];
        }

        $content = file_get_contents($logoPath);

        return [
            [
                'filename' => 'logo.png',
                'content_type' => 'image/png',
                'cid' => 'logo_cid',
                'content' => $content
            ]
        ];
    }
}

/*
|--------------------------------------------------------------------------
| 8. HTML BUILDER (CLEAN STRUCTURE)
|--------------------------------------------------------------------------
*/
if (!function_exists('app_build_otp_email_html')) {

    function app_build_otp_email_html(string $otp, array $copy, string $logoCid = ''): string
    {
        $otp = htmlspecialchars($otp);

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>OTP</title>
        </head>

        <body style='margin:0;background:#f4f6fb;font-family:Arial;'>

        <div style='max-width:600px;margin:auto;background:#fff;padding:30px;border-radius:10px;'>

            <div style='text-align:center;'>
                " . ($logoCid ? "<img src='cid:$logoCid' width='60'>" : '') . "
                <h2>{$copy['headline']}</h2>
                <p>{$copy['intro']}</p>
            </div>

            <div style='text-align:center;margin:30px 0;font-size:40px;letter-spacing:8px;font-weight:bold;'>
                $otp
            </div>

            <p style='color:#555;text-align:center;'>
                {$copy['body_note']}
            </p>

        </div>

        </body>
        </html>";
    }
}

/*
|--------------------------------------------------------------------------
| 9. TEXT EMAIL
|--------------------------------------------------------------------------
*/
if (!function_exists('app_build_otp_email_text')) {

    function app_build_otp_email_text(string $otp, array $copy): string
    {
        return $copy['headline'] . "\n\nOTP: " . $otp;
    }
}
