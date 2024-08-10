<?php

namespace Solmer\Storage\Core;

use Solmer\Storage\Application\Models\Users;

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
            $mail->Host = env('SMTP_SERVER');
            $mail->SMTPAuth = true;
            $mail->Username = env('SMTP_GMAIL');
            $mail->Password = env('SMTP_PASSWORD');
            $mail->SMTPSecure = env('ENCRYPTION_SMTPS');
            $mail->Port = env('SMTP_PORT');

            $mail->SMTPSecure = 'tls';

            $mail->setFrom(env('SMTP_GMAIL'), 'Storage');
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
