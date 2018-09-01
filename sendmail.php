<?php

use PHPMailer\PHPMailer\PHPMailer;
require_once dirname(__FILE__) . '/vendor/autoload.php';

$success = FALSE;
$message = '';

function response(bool $success, string $msg = '') {
    echo json_encode(['success' => $success, 'msg' => $msg]);
    die;
}

$config = dirname(__FILE__) . '/config.inc.php';
if (!file_exists($config)) {
    response(false, 'Config file not found');
}
require $config;
if (!isset($address_to) or !isset($address_from) or !isset($subject)) {
    response(false, 'Config incorrect');
}
if (empty($_REQUEST)) {
    response(false, 'No data received');
}
if (!empty($_REQUEST['email']) or !empty($_REQUEST['text']) or !empty($_REQUEST['email3'])) {
    response(false, 'Spam detected');
}

$body = '';
foreach (['name' => 'name', 'currency' => 'currency', 'email2' => 'email', 'location' => 'location', 'amount' => 'amount'] as $k => $name) {
    if (!empty($_REQUEST[$k])) {
        $body .= "$name: {$_REQUEST[$k]}".PHP_EOL;
    }
}

$mail = new PHPMailer;
$mail->addAddress($address_to);
$mail->setFrom($address_from);
$mail->isHTML(false);
$mail->Subject = $subject;
$mail->Body = $body;
if (!$mail->send()) {
    response(false, "Mailer Error: " . $mail->ErrorInfo);
} else {
    response(true);
}