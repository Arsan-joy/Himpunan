<?php
require_once __DIR__ . '/mailer/Exception.php';
require_once __DIR__ . '/mailer/PHPMailer.php';
require_once __DIR__ . '/mailer/SMTP.php';

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

function send_reset_email(string $to, string $resetUrl): bool
{
    $host = trim((string)env('MAIL_HOST', ''));
    $username = trim((string)env('MAIL_USERNAME', ''));
    $password = (string)env('MAIL_PASSWORD', '');
    $from = trim((string)env('MAIL_FROM', $username));
    $fromName = trim((string)env('MAIL_FROM_NAME', 'HMTA ITERA'));
    $port = (int)env('MAIL_PORT', 465);

    if ($host === '' || $username === '' || $password === '' || $from === '') {
        error_log('[mailer] Konfigurasi SMTP belum lengkap.');
        return false;
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->Port = $port;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->SMTPSecure = $port === 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($from, $fromName);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password HMTA ITERA';
        $safeUrl = htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8');
        $mail->Body = '<p>Gunakan tautan berikut untuk reset password:</p>'
            . '<p><a href="' . $safeUrl . '">' . $safeUrl . '</a></p>'
            . '<p>Tautan berlaku 30 menit dan hanya dapat digunakan sekali.</p>';
        $mail->AltBody = "Gunakan tautan berikut untuk reset password:\n" . $resetUrl
            . "\nTautan berlaku 30 menit dan hanya dapat digunakan sekali.";
        $mail->send();
        return true;
    } catch (MailException $e) {
        error_log('[mailer] Gagal kirim reset email: ' . $e->getMessage());
        return false;
    }
}
