<?php
session_start();
include('dbcon.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

function sendmail_verify($name, $email, $verify_token)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->SMTPAuth   = true;
        $mail->Host       = 'smtp.gmail.com'; // Correct SMTP host
        $mail->Username   = 'ignacioagie87@gmail.com';
        $mail->Password   = 'yzry sevt rqct uyvh';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use constant
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('ignacioagie87@gmail.com', 'Your Company Name'); // Change name to a generic one
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';

        $email_template = "
        <h2>You have registered</h2>
        <h5>Verify your email by clicking the link below</h5>
        <br/><br/>
        <a href='http://localhost/Register%20Email%20Vertification/verify-email.php?token=$verify_token'>Click Me</a>";

        $mail->Body = $email_template;

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if (isset($_POST['register_btn'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $verify_token = md5(rand());

    // Check if email already exists
    $check_email_query = "SELECT email FROM users WHERE email='$email' LIMIT 1";
    $check_email_query_run = mysqli_query($con, $check_email_query);

    if (mysqli_num_rows($check_email_query_run) > 0) {
        $_SESSION['status'] = "Email Address already exists";
        header("Location: register.php");
    } else {
        // Insert user data
        $query = "INSERT INTO users (name, phone, address, email, password, verify_token) VALUES ('$name', '$phone', '$address', '$email', '$password', '$verify_token')";
        $query_run = mysqli_query($con, $query);

        if ($query_run) {
            sendmail_verify($name, $email, $verify_token);
            $_SESSION['status'] = "Registration successful! Please verify your email address.";
            header("Location: register.php");
        } else {
            $_SESSION['status'] = "Registration failed";
            header("Location: register.php");
        }
    }
}
?>
