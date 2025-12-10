<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function sendApprovalEmail($applicantEmail, $applicantName, $applicationStatus)
{
    $mail = new PHPMailer(true);  // Instantiate PHPMailer

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'scholarshipofficezppsu@gmail.com';  // Your email address
        $mail->Password = 'kmecdkhsrigdjjds';  // App password generated in Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('scholarshipofficezppsu@gmail.com', 'ZPPSU Scholarship Office');  // Sender's email
        $mail->addAddress($applicantEmail, $applicantName);  // Recipient's email

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Application Status Update';
        $mail->Body = "<h2>Dear $applicantName,</h2>
        <p>We are pleased to inform you that your scholarship application has been <strong>approved</strong>.</p>
        <p>Thank you for taking the time to apply for this opportunity. We appreciate your interest and wish you success in your academic journey.</p>
        <p>Should you have any further questions or require assistance, please feel free to contact us.</p>
        <p>Best regards,</p>
        <p><strong>ZPPSU Office of Scholarship Programs</strong></p>";
        // Attempt to send the email
        if ($mail->send()) {
            return true;  // Return true if email is sent successfully
        } else {
            return "Error: Mail not sent.";
        }
    } catch (Exception $e) {
        // Catch any exception thrown by PHPMailer and return the error message
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>