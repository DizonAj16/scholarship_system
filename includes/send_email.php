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
        $mail->Username = 'zppsuscholarshipoffice@gmail.com';
        $mail->Password = 'llinaayecimnuzni';  // App password generated in Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('zppsuscholarshipoffice@gmail.com', 'ZPPSU Scholarship Office');  // Sender's email
        $mail->addAddress($applicantEmail, $applicantName);  // Recipient's email

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Application Status Update - Approved';
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

function sendPendingEmail($applicantEmail, $applicantName)
{
    $mail = new PHPMailer(true);

    try {
        // Server settings (same as approval email)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'zppsuscholarshipoffice@gmail.com';
        $mail->Password = 'llinaayecimnuzni';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('zppsuscholarshipoffice@gmail.com', 'ZPPSU Scholarship Office');
        $mail->addAddress($applicantEmail, $applicantName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Application Status Update - Pending Review';
        $mail->Body = "<h2>Dear $applicantName,</h2>
        <p>Your scholarship application status has been updated to <strong>pending review</strong>.</p>
        <p>Our committee is currently reviewing your application. We will notify you once a final decision has been made.</p>
        <p>Please be patient as we carefully evaluate all applications. You can check your application status anytime through the scholarship portal.</p>
        <p>Should you have any further questions or require assistance, please feel free to contact us.</p>
        <p>Best regards,</p>
        <p><strong>ZPPSU Office of Scholarship Programs</strong></p>";

        if ($mail->send()) {
            return true;
        } else {
            return "Error: Mail not sent.";
        }
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function sendRejectionEmail($applicantEmail, $applicantName, $rejectionReason = '')
{
    $mail = new PHPMailer(true);

    try {
        // Server settings (same as approval email)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'zppsuscholarshipoffice@gmail.com';
        $mail->Password = 'llinaayecimnuzni';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('zppsuscholarshipoffice@gmail.com', 'ZPPSU Scholarship Office');
        $mail->addAddress($applicantEmail, $applicantName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Application Status Update - Not Qualified';

        $reasonText = '';
        if (!empty($rejectionReason)) {
            $reasonText = "<p><strong>Reason:</strong> $rejectionReason</p>";
        }

        $mail->Body = "<h2>Dear $applicantName,</h2>
        <p>After careful review, we regret to inform you that your scholarship application has been marked as <strong>not qualified</strong>.</p>
        $reasonText
        <p>We appreciate the time and effort you put into your application and encourage you to apply for future scholarship opportunities.</p>
        <p>If you have any questions about this decision or would like feedback on your application, please don't hesitate to contact our office.</p>
        <p>We wish you the best in your academic endeavors.</p>
        <p>Best regards,</p>
        <p><strong>ZPPSU Office of Scholarship Programs</strong></p>";

        if ($mail->send()) {
            return true;
        } else {
            return "Error: Mail not sent.";
        }
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>