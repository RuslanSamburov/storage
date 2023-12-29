<?php

namespace Storage\Storage\Core;

use Storage\Storage\Application\Settings;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Helpers
{
    public static function get_fragment_path(string $fragment): string
    {
        global $base_path;
        return $base_path . '/src/Application/Views/' . $fragment . '.inc.php';
    }

    public static function array_find(string $key, mixed $value, array $array): mixed
    {
        foreach ($array as $arr) {
            if ($arr[$key] == $value) {
                return $arr;
            }
        }
        return false;
    }

    public static function show_errors(string $fld_name, array $form_data): void
    {
        if (isset($form_data['__errors'][$fld_name])) {
            echo '<p class="my-2 result-box result-box-error">' . $form_data['__errors'][$fld_name] . '</p>';
        }
    }

    public static function show_results(string $fld_name, array $form_data): void
    {
        if (isset($form_data['__results'][$fld_name])) {
            echo '<p class="my-2 result-box result-box-result">' . $form_data['__results'][$fld_name] . '</p>';
        }
    }

    public static function generateSymbols(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    public static function getTxt(string $txt): string|bool
    {
        global $base_path;
        $src = $base_path . 'txts/' . $txt . '.txt';
        return file_get_contents($src) ?? false;
    }

    public static function renderText(string $template, array $values): string
    {
        $patterns = [];
        $vals = [];
        foreach ($values as $key => $value) {
            $patterns[] = '/%' . $key . '%/iu';
            $vals[] = $value;
        }
        return preg_replace($patterns, $vals, $template);
    }

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
            $mail->Subject = self::renderText($subject, $values);
            $mail->Body = self::renderText($body, $values);

            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }
}
