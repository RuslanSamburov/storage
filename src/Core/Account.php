<?php

namespace Storage\Storage\Core;

use Storage\Storage\Application\Models\Users;

use Storage\Storage\Application\Settings;
use PHPMailer\PHPMailer\{PHPMailer, Exception};

class Account
{
    public static function sendMail(
        string $to,
        string $subject = '',
        string $body = '',
        array $values = [],
    ): bool {
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = Settings::SMTP_SERVER;
            $mail->SMTPAuth = true;
            $mail->Username = Settings::SMTP_GMAIL;
            $mail->Password = Settings::SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = Settings::SMTP_PORT;

            $mail->SMTPSecure = 'tls';

            $mail->setFrom(Settings::SMTP_GMAIL, 'Storage');
            $mail->addAddress($to);

            $mail->isHtml(true);
            $mail->Subject = Texts::renderText($subject, $values);
            $mail->Body = Texts::renderText($body, $values);

            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function activationSend(
        string $to,
        int $id,
        string $token,
    ): bool {
        $values = [
            'url' => 'http://' . $_SERVER['SERVER_NAME'] . '/activation/' . $id . '/' . $token,
        ];
        return self::sendMail($to, Texts::getTxt('register_subject'), Texts::getTxt('register_body'), $values);
    }

    public static function setUser(int $id): void
    {
        $_SESSION['current_user'] = $id;
    }

    public static function getCurrentUser(): int|bool
    {
        return $_SESSION['current_user'] ?? false;
    }

    public static function getUser(
        string $value,
        string $keyField = 'id',
        string $fields = '*',
        array $links = [],
    ): array {
        $users = new Users();
        return $users->get($value, $keyField, $fields, $links);
    }

    public static function unsetUser(): void
    {
        unset($_SESSION['current_user']);
    }

    public static function logout(): void
    {
        self::unsetUser();
        Response::redirect('/login');
    }
}
