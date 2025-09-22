<?php
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Email address verification function (keeping your original)
function isEmail($email) {
    return (preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email));
}

if (!defined("PHP_EOL")) define("PHP_EOL", "\r\n");

// Get and sanitize input data
$name     = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email    = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
$phone    = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
$comments = isset($_POST['comments']) ? trim(strip_tags($_POST['comments'])) : '';

// Validate inputs
$errors = [];

if (empty($name)) {
    $errors[] = 'Please enter your name';
}

if (empty($email)) {
    $errors[] = 'Please enter your email address';
} elseif (!isEmail($email)) {
    $errors[] = 'Please enter a valid email address';
}

if (empty($phone)) {
    $errors[] = 'Please enter your phone number';
}

if (empty($comments)) {
    $errors[] = 'Please enter your message';
}

// If there are errors, return them
if (!empty($errors)) {
    echo json_encode([
        'status' => 'error', 
        'message' => implode('<br>', $errors),
        'field' => 'general'
    ]);
    exit;
}

// Clean up comments
$comments = stripslashes($comments);

// Configuration - UPDATE THIS TO YOUR EMAIL
$address = "support@thesigmaskool.com"; // Change this to your email address
$e_subject = 'Sigma Website - New Contact Form Submission from ' . $name;

// Create professional email body
$e_body  = "=== SIGMA WEBSITE CONTACT FORM SUBMISSION ===" . PHP_EOL . PHP_EOL;
$e_body .= "Submission Date: " . date('Y-m-d H:i:s') . PHP_EOL;
$e_body .= "----------------------------------------" . PHP_EOL;
$e_body .= "Name: " . $name . PHP_EOL;
$e_body .= "Email: " . $email . PHP_EOL;
$e_body .= "Phone: " . $phone . PHP_EOL;
$e_body .= "----------------------------------------" . PHP_EOL;
$e_body .= "MESSAGE:" . PHP_EOL;
$e_body .= "----------------------------------------" . PHP_EOL;
$e_body .= wordwrap($comments, 70) . PHP_EOL;
$e_body .= "----------------------------------------" . PHP_EOL . PHP_EOL;
$e_body .= "=== END SUBMISSION ===";

// Headers for proper email formatting
$headers = "MIME-Version: 1.0" . PHP_EOL;
$headers .= "Content-Type: text/plain; charset=UTF-8" . PHP_EOL;
$headers .= "Content-Transfer-Encoding: quoted-printable" . PHP_EOL;
$headers .= "From: Sigma Website <noreply@sigmaskool.com>" . PHP_EOL;
$headers .= "Reply-To: " . $email . PHP_EOL;
$headers .= "X-Mailer: PHP/" . phpversion() . PHP_EOL;

// Send the email
$mail_sent = mail($address, $e_subject, $e_body, $headers);

// Return appropriate response
if ($mail_sent) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Thank you ' . $name . '! Your message has been sent successfully. We will contact you soon.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Sorry, there was a problem sending your message. Please try again or contact us directly.',
        'field' => 'general'
    ]);
}

exit;
?>