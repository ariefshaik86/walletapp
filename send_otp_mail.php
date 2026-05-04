<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

function sendOTP($toEmail, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'in-v3.mailjet.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'b8d23200a66835781bcb9b0c77ed9ea9';   // 🔁 Replace this
        $mail->Password   = '3adf856228fadb1766a4dab527554b49';   // 🔁 Replace this
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('wallet_app1@outlook.com', 'WalletApp');  // 🔁 Replace
        $mail->addAddress($toEmail);

        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "Your OTP is: $otp";

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}
