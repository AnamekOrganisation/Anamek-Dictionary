<?php

/**
 * Email Helper (WordPress-like)
 * Works without SMTP or configuration
 */
class Email {

    /**
     * Send email using PHP mail()
     */
    public static function send($to, $subject, $message) {

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            error_log('Invalid email: ' . $to);
            return false;
        }

        // Detect domain automatically
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $domain = preg_replace('/^www\./', '', $host);

        $fromEmail = 'no-reply@' . $domain;
        $fromName  = 'Amawal';

        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . $fromName . ' <' . $fromEmail . '>';
        $headers[] = 'Reply-To: ' . $fromEmail;
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        $html = self::template($message);

        // Suppress warning but capture result
        $sent = @mail($to, $subject, $html, implode("\r\n", $headers));

        if (!$sent) {
            error_log('Email send failed to: ' . $to);
        }

        return $sent;
    }

    /**
     * Email HTML template
     */
    private static function template($content) {
        return '
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
</head>
<body style="margin:0;padding:0;background:#f4f4f4;">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;margin:20px auto;font-family:Arial,sans-serif;">
<tr>
<td style="padding:20px;background:#f99417;color:#fff;text-align:center;">
<h2 style="margin:0;">Anamek</h2>
<p style="margin:5px 0 0;">Le dictionnaire collaboratif de la langue amazighe.</p>
</td>
</tr>
<tr>
<td style="padding:30px;color:#333;">
' . $content . '
</td>
</tr>
<tr>
<td style="padding:15px;text-align:center;color:#777;font-size:12px;">
Cet email a été envoyé automatiquement.<br>
Si vous ne l\'avez pas demandé, ignorez-le.
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>';
    }
}
