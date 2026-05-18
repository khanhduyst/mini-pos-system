<?php
require_once 'PHPMailer/Exception.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHelper
{
    public static function send($toEmail, $toName, $subject, $body)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'lkdffst@gmail.com';
            $mail->Password   = 'ecec lsex iuvj fubq';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('lkdffst@gmail.com', 'MINI POS SYSTEM');
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $mail->Body    = $body;

            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }
}