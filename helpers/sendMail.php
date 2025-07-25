<?php
require_once __DIR__ . '/../config/mail.php';

function sendMail(string $to, string $toName, string $subject, string $htmlMessage, string $altText = ''): bool
{
    $mail = configureMailer();

    try {
        $mail->addAddress($to, $toName);
        $mail->Subject = $subject;
        $mail->Body    = $htmlMessage;
        $mail->AltBody = $altText ?: strip_tags($htmlMessage);

        return $mail->send();
    } catch (Exception $e) {
        error_log("[SEND MAIL ERROR] " . $mail->ErrorInfo);
        return false;
    }
}
