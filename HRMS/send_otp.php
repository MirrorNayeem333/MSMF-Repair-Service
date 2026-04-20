<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

function sendOTP($email, $otp) {

    // ✅ EMAIL VALIDATION (IMPORTANT FIX)
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email";
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        // 🔵 SMTP SETTINGS
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // 🔴 YOUR GMAIL (SENDER EMAIL)
        $mail->Username = 'parvez6442cse@gmail.com';

        // 🔴 APP PASSWORD (16 digit from Google)
        $mail->Password = 'ooot swgz awzm oqcz';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // 🔵 SENDER INFO
        $mail->setFrom('parvez6442cse@gmail.com', 'HRMS System');

        // 🔵 RECEIVER EMAIL
        $mail->addAddress($email);

        // 🔵 EMAIL CONTENT
        $mail->isHTML(true);
        $mail->Subject = 'OTP Verification Code';
        $mail->Body = "
            <h2>OTP Verification</h2>
            <p>Your OTP is:</p>
            <h1>$otp</h1>
            <p>This OTP will expire soon.</p>
        ";

        // 🔵 SEND MAIL
        $mail->send();
        return true;

    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
}