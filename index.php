<?php

# /?email=email&body=body&title=title&name=name

spl_autoload_register(function ($class) {
    $baseDirs = [
        'PHPMailer\\PHPMailer\\' => 'PHPMailer/'
    ];
    foreach ($baseDirs as $prefix => $baseDir) {
        $prefixLength = strlen($prefix);
        if (strncmp($prefix, $class, $prefixLength) === 0) {
            $relativeClass = substr($class, $prefixLength);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

date_default_timezone_set('Asia/Ho_Chi_Minh');
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;

if (isset($_REQUEST['email']) && isset($_REQUEST['body'])) {
    $email = $_REQUEST['email'];
    $body = $_REQUEST['body'];
    $title = $_REQUEST['title'];
    $name = $_REQUEST['name'];
    echo sendEmail($email, $body, $title, $name);
} else {
    echo json_encode(["status" => 500, "message" => "Email failed to send: {$mail->ErrorInfo}"]);
    exit;
}

function sendEmail($email, $body, $title, $name)
{

    $mail_user = "xxxx@gmail.com";
    $mail_pass = "xxxx";
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $mail_user;
        $mail->Password   = $mail_pass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom($mail_user, $name);
        $mail->addAddress($email);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body    = $body;
        $mail->send();
        $now = date('Y-m-d H:i:s');
        return json_encode(["status" => 200, "message" => "Email sent successfully", "time" => $now]);
    } catch (Exception $e) {
        return json_encode(["status" => 500, "message" => "Email failed to send: {$mail->ErrorInfo}"]);
    }
}
