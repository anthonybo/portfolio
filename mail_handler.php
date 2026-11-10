<?php
require_once('email_config.php');
require_once('phpmailer/PHPMailer/src/Exception.php');
require_once('phpmailer/PHPMailer/src/PHPMailer.php');
require_once('phpmailer/PHPMailer/src/SMTP.php');

// Where contact messages are delivered.
const CONTACT_TO = 'anthonybo.dev@gmail.com';

function fail($text) {
    echo $text . ' Please contact me directly through my <a href="mailto:' . CONTACT_TO . '">email</a>';
    exit;
}

$email   = trim($_POST['email'] ?? '');
$name    = trim($_POST['name'] ?? '');
$message = trim($_POST['message'] ?? '');

// Strip line breaks from header fields so they cannot inject extra headers.
$name = preg_replace('/[\r\n]+/', ' ', $name);

if ($name === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail('Please fill in your name, a valid email and a message.');
}

// EMAIL_USER may be a bare Gmail username; the From header needs a full address.
$sender = strpos(EMAIL_USER, '@') === false ? EMAIL_USER . '@gmail.com' : EMAIL_USER;

$mail = new PHPMailer\PHPMailer\PHPMailer;
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = $sender;      // Gmail address that sends the message
$mail->Password = EMAIL_PASS;   // Google app password, not the account password
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
$mail->CharSet = 'UTF-8';

// Gmail only accepts mail sent as the signed-in account, so the visitor goes in Reply-To.
$mail->setFrom($sender, 'Anthony Bocchino website');
$mail->addAddress(CONTACT_TO, 'Anthony Bocchino');
$mail->addReplyTo($email, $name);

$safeName    = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$safeEmail   = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$safeMessage = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

$when = (new DateTime('now', new DateTimeZone('America/Phoenix')))->format('D, M j, Y \a\t g:i A') . ' (Arizona)';
$replySubject = rawurlencode('Re: your message on anthonybo.com');

$label = 'font:600 11px/1.4 -apple-system,Segoe UI,Helvetica,Arial,sans-serif;letter-spacing:.08em;text-transform:uppercase;color:#6b7280;padding:0 0 4px';
$value = 'font:400 16px/1.5 -apple-system,Segoe UI,Helvetica,Arial,sans-serif;color:#111827;padding:0 0 20px';

$body = <<<HTML
<div style="margin:0;padding:24px 12px;background:#f3f4f6">
  <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:560px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:10px">
    <tr><td style="padding:22px 28px;border-bottom:1px solid #e5e7eb;font:600 18px/1.3 -apple-system,Segoe UI,Helvetica,Arial,sans-serif;color:#111827">
      New message from your website
      <div style="font:400 13px/1.5 -apple-system,Segoe UI,Helvetica,Arial,sans-serif;color:#6b7280;padding-top:2px">anthonybo.com contact form &middot; {$when}</div>
    </td></tr>
    <tr><td style="padding:24px 28px 4px">
      <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
        <tr><td style="{$label}">Name</td></tr>
        <tr><td style="{$value}">{$safeName}</td></tr>
        <tr><td style="{$label}">Email</td></tr>
        <tr><td style="{$value}"><a href="mailto:{$safeEmail}" style="color:#1d4ed8;text-decoration:none">{$safeEmail}</a></td></tr>
        <tr><td style="{$label}">Message</td></tr>
        <tr><td style="font:400 16px/1.6 -apple-system,Segoe UI,Helvetica,Arial,sans-serif;color:#111827;padding:12px 16px;background:#f9fafb;border-left:3px solid #1d4ed8;border-radius:4px">{$safeMessage}</td></tr>
      </table>
    </td></tr>
    <tr><td style="padding:20px 28px 26px">
      <a href="mailto:{$safeEmail}?subject={$replySubject}" style="display:inline-block;padding:10px 18px;background:#1d4ed8;border-radius:6px;font:600 14px/1 -apple-system,Segoe UI,Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none">Reply to {$safeName}</a>
      <div style="font:400 12px/1.5 -apple-system,Segoe UI,Helvetica,Arial,sans-serif;color:#9ca3af;padding-top:12px">Replying to this email also goes straight to them.</div>
    </td></tr>
  </table>
</div>
HTML;

$mail->isHTML(true);
$mail->Subject = 'Message from anthonybo.com: ' . $name;
$mail->Body    = $body;
$mail->AltBody = "New message from your website (anthonybo.com)\n$when\n\nName: $name\nEmail: $email\n\nMessage:\n$message";

if (!$mail->send()) {
    error_log('contact form: ' . $mail->ErrorInfo);
    fail('Message could not be sent.');
}

echo 'Message has been sent';
