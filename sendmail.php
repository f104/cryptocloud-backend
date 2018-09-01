<?php

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
if (!isset($address) or !isset($subject)) {
    response(false, 'Config incorrect');
}
if (empty($_REQUEST)) {
    response(false, 'No data received');
}
if (!empty($_REQUEST['email']) or !empty($_REQUEST['text']) or !empty($_REQUEST['email3'])) {
    response(false, 'Spam detected');
}

$body = '';
foreach ($_REQUEST as $k => $v) {
    if (!empty($v)) {
        $body .= "$k: $v".PHP_EOL;
    }
}

$mail = new PHPMailer;
$mail->addAddress($address);
$mail->isHTML(false);
$mail->Subject = $subject;
$mail->Body = $body;
if (!$mail->send()) {
    response(false, "Mailer Error: " . $mail->ErrorInfo);
} else {
    response(true);
}