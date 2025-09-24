<?php
// Start output buffering to prevent headers-already-sent issues
ob_start();

// Include PHPMailer classes
require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// --------------------------
// Process Form
// --------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitize inputs
    $name     = isset($_POST['name']) ? htmlspecialchars(strip_tags(trim($_POST['name']))) : '';
    $email    = isset($_POST['email']) ? htmlspecialchars(strip_tags(trim($_POST['email']))) : '';
    $phone    = isset($_POST['phone']) ? htmlspecialchars(strip_tags(trim($_POST['phone']))) : '';
    $comments = isset($_POST['comments']) ? htmlspecialchars(strip_tags(trim($_POST['comments']))) : '';

    // Validate inputs
    $errors = [];
    if (empty($name))   $errors[] = 'Please enter your name.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email.';
    if (empty($phone))  $errors[] = 'Please enter your phone number.';
    if (empty($comments)) $errors[] = 'Please enter your message.';

    if (!empty($errors)) {
        ob_end_clean();
        echo "❌ " . implode("<br>", $errors);
        exit();
    }

    // Detect if running on localhost
    $is_localhost = (
        $_SERVER['SERVER_NAME'] === 'localhost' ||
        $_SERVER['SERVER_ADDR'] === '127.0.0.1'
    );

    // --------------------------
    // Configure PHPMailer
    // --------------------------
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);

    // try {


    // } 
    // catch (Exception $e) {
    //     // ob_end_clean();
    //     // echo "❌ Message could not be sent. Error: {$mail->ErrorInfo}";
    //     // file_put_contents(__DIR__ . '/phpmailer.log', "Error: {$mail->ErrorInfo}\n", FILE_APPEND);
    //     header("Location: success.html", true, 302); // Redirect to success.html

    //     exit();
    // }
        if ($is_localhost) {
            // Localhost (XAMPP) → Use Gmail SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'thesigmaskool@gmail.com';
            $mail->Password   = 'ssvmbjuafolykcsd'; // Ensure this is your Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
        } else {
            // Live server (GoDaddy) → Use built-in mail
            $mail->isMail();
        }

        // Email Setup
        $mail->setFrom('thesigmaskool@gmail.com', 'Sigma Skool Website');
        $mail->addAddress('thesigmaskool@gmail.com', 'Sigma Support');
        $mail->addReplyTo($email, $name);

        $mail->Subject = "New Contact Form Submission from {$name}";
        $mail->Body = "
            <h2>Contact Request from Sigma Website</h2>
            <hr>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
<p><strong>Phone:</strong> {$phone}</p>
<hr>
<p><strong>Message:</strong></p>
<p>{$comments}</p>
";
$mail->AltBody = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\nMessage: {$comments}";
// Send mail
$mail->send();

// Redirect after successful email send
// ob_end_clean();
header("Location: success.html", true, 302); // Redirect to success.html
exit();
} 
// else {
//     ob_end_clean();
//     echo "⚠️ This script can only process form submissions.";
//     exit();
// }

// Ensure output buffer is cleared
// ob_end_flush();
?>