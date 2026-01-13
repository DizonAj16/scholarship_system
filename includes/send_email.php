<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Common email styling for consistency
function getEmailStyles()
{
    return "
        .email-container { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
        .header { background-color: #8b0000; color: white; padding: 25px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 25px; background-color: #ffffff; }
        .info-box { background-color: #f8f9fa; border-left: 4px solid #007bff; padding: 18px; margin: 20px 0; border-radius: 4px; }
        .status-badge { display: inline-block; padding: 6px 15px; border-radius: 4px; font-weight: bold; margin: 5px 0; color: white; }
        .footer { background-color: #f1f1f1; padding: 20px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 8px 8px; border-top: 1px solid #ddd; }
        .button { display: inline-block; padding: 10px 25px; background-color: #8b0000; color: white; text-decoration: none; border-radius: 4px; margin: 10px 0; }
        .contact-info { background-color: #e8f4f8; padding: 15px; border-radius: 4px; margin: 20px 0; }
        ul, ol { padding-left: 20px; }
        li { margin-bottom: 8px; }
        .section-title { color: #8b0000; border-bottom: 2px solid #f0f0f0; padding-bottom: 8px; margin-top: 20px; }
        .highlight { background-color: #fff3cd; padding: 10px; border-radius: 4px; border-left: 4px solid #ffc107; }
    ";
}

function sendApplicationSubmissionEmail($applicantEmail, $applicantName, $applicationId, $scholarshipGrant, $semester = '', $schoolYear = '')
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
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
        $mail->Subject = 'Scholarship Application Received - Under Review';

        // Build semester info if available
        $semesterInfo = '';
        if (!empty($semester) && !empty($schoolYear)) {
            $semesterInfo = "<p><strong>Application Period:</strong> $semester $schoolYear</p>";
        }

        $currentDate = date('F j, Y, g:ia');

        $mail->Body = "<!DOCTYPE html>
        <html>
        <head>
            <style>" . getEmailStyles() . "</style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h2>ZPPSU Scholarship Application</h2>
                </div>
                
                <div class='content'>
                    <h3>Dear $applicantName,</h3>
                    
                    <p>Thank you for submitting your scholarship application to the Zamboanga Peninsula Polytechnic State University.</p>
                    
                    <div class='info-box'>
                        <h4>Application Summary</h4>
                        <p><strong>Application ID:</strong> $applicationId</p>
                        <p><strong>Scholarship Grant:</strong> $scholarshipGrant</p>
                        $semesterInfo
                        <p><strong>Submission Date:</strong> $currentDate</p>
                    </div>
                    
                    <h4>Current Status: <span class='status-badge' style='background-color: #ff9800;'>UNDER REVIEW</span></h4>
                    
                    <p>Your application has entered our review process. Here is what you can expect:</p>
                    
                    <div class='section-title'>Review Timeline</div>
                    <ul>
                        <li><strong>Initial Screening:</strong> 1-2 weeks</li>
                        <li><strong>Committee Review:</strong> 2-3 weeks</li>
                        <li><strong>Final Decision:</strong> 3-4 weeks from submission</li>
                    </ul>
                    
                    <div class='section-title'>Next Steps</div>
                    <ol>
                        <li>Document verification and completeness check</li>
                        <li>Eligibility assessment</li>
                        <li>Committee evaluation</li>
                        <li>Decision notification via email</li>
                    </ol>
                    
                    <div class='contact-info'>
                        <h5>Need Assistance?</h5>
                        <p><strong>ZPPSU Scholarship Office</strong><br>
                        Email: zppsuscholarshipoffice@gmail.com<br>
                        Website: <a href='http://yourwebsite.com'>Scholarship Portal</a></p>
                    </div>
                    
                    <p>You can track your application status by logging into your account on our scholarship portal.</p>
                    
                    <p>We appreciate your patience during the review process.</p>
                </div>
                
                <div class='footer'>
                    <p><strong>Zamboanga Peninsula Polytechnic State University</strong><br>
                    Office of Scholarship Programs</p>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>";

        if ($mail->send()) {
            return true;
        } else {
            return "Error: Mail not sent.";
        }
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function sendApprovalEmail($applicantEmail, $applicantName, $applicationId = '', $scholarshipGrant = '')
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
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
        $mail->Subject = 'Congratulations! Your Scholarship Application Has Been Approved';

        // Build application details if provided
        $applicationDetails = '';
        if (!empty($applicationId) && !empty($scholarshipGrant)) {
            $applicationDetails = "<div class='info-box'>
                <h4>Application Details</h4>
                <p><strong>Application ID:</strong> $applicationId</p>
                <p><strong>Scholarship Grant:</strong> $scholarshipGrant</p>
                <p><strong>Approval Date:</strong> " . date('F j, Y, g:ia') . "</p>
            </div>";
        }

        $mail->Body = "<!DOCTYPE html>
        <html>
        <head>
            <style>" . getEmailStyles() . "</style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header' style='background-color: #28a745;'>
                    <h2>Scholarship Application Approved!</h2>
                </div>
                
                <div class='content'>
                    <h3>Dear $applicantName,</h3>
                    
                    <p>We are delighted to inform you that your scholarship application has been <strong>APPROVED</strong>!</p>
                    
                    $applicationDetails
                    
                    <h4>Current Status: <span class='status-badge' style='background-color: #28a745;'>APPROVED</span></h4>
                    
                    <p>Congratulations on this achievement! The Scholarship Committee has reviewed your application and determined that you meet all the necessary criteria.</p>
                    
                    <div class='section-title'>Next Steps for Awardees</div>
                    <ol>
                        <li><strong>Orientation Session:</strong> You will receive an invitation to a mandatory scholarship orientation</li>
                        <li><strong>Document Submission:</strong> Additional documents may be required for processing</li>
                        <li><strong>Disbursement Schedule:</strong> Scholarship benefits will be disbursed according to the academic calendar</li>
                        <li><strong>Maintain Eligibility:</strong> Continue to meet the scholarship requirements throughout the semester</li>
                    </ol>
                    
                    <div class='section-title'>Important Information</div>
                    <ul>
                        <li>Scholarship benefits are subject to compliance with all terms and conditions</li>
                        <li>You must maintain satisfactory academic performance</li>
                        <li>Any changes in your enrollment status must be reported immediately</li>
                        <li>Scholarship may be revoked for violations of university policies</li>
                    </ul>
                    
                    <div class='contact-info'>
                        <h5>Scholarship Office Contact</h5>
                        <p>If you have any questions about your award, please contact:<br>
                        <strong>ZPPSU Scholarship Office</strong><br>
                        Email: zppsuscholarshipoffice@gmail.com<br>
                        Office Hours: Monday-Friday, 8:00 AM - 5:00 PM</p>
                    </div>
                    
                    <p>We wish you continued success in your academic journey at ZPPSU!</p>
                    
                    <a href='http://yourwebsite.com/my_applications.php' class='button'>View Your Application Status</a>
                </div>
                
                <div class='footer'>
                    <p><strong>Zamboanga Peninsula Polytechnic State University</strong><br>
                    Office of Scholarship Programs</p>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>";

        if ($mail->send()) {
            return true;
        } else {
            return "Error: Mail not sent.";
        }
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function sendPendingEmail($applicantEmail, $applicantName, $applicationId = '', $scholarshipGrant = '')
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
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
        $mail->Subject = 'Scholarship Application Status Update - Pending Review';

        // Build application details if provided
        $applicationDetails = '';
        if (!empty($applicationId) && !empty($scholarshipGrant)) {
            $applicationDetails = "<div class='info-box'>
                <h4>Application Details</h4>
                <p><strong>Application ID:</strong> $applicationId</p>
                <p><strong>Scholarship Grant:</strong> $scholarshipGrant</p>
                <p><strong>Status Updated:</strong> " . date('F j, Y, g:ia') . "</p>
            </div>";
        }

        $mail->Body = "<!DOCTYPE html>
        <html>
        <head>
            <style>" . getEmailStyles() . "</style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header' style='background-color: #ff9800;'>
                    <h2>Application Status Update</h2>
                </div>
                
                <div class='content'>
                    <h3>Dear $applicantName,</h3>
                    
                    <p>This is to inform you that your scholarship application status has been updated to <strong>PENDING REVIEW</strong>.</p>
                    
                    $applicationDetails
                    
                    <h4>Current Status: <span class='status-badge' style='background-color: #ff9800;'>PENDING REVIEW</span></h4>
                    
                    <p>The Scholarship Committee is currently reviewing all applications. This status update means your application is in queue for evaluation.</p>
                    
                    <div class='section-title'>Review Process Timeline</div>
                    <ul>
                        <li><strong>Stage 1:</strong> Initial documentation check (1-2 weeks)</li>
                        <li><strong>Stage 2:</strong> Eligibility verification (1-2 weeks)</li>
                        <li><strong>Stage 3:</strong> Committee deliberation (2-3 weeks)</li>
                        <li><strong>Stage 4:</strong> Final decision and notification</li>
                    </ul>
                    
                    <div class='section-title'>What You Can Do</div>
                    <ul>
                        <li>Ensure your contact information is current in our system</li>
                        <li>Check your email regularly for updates</li>
                        <li>Be prepared to provide additional information if requested</li>
                        <li>Monitor your application status through the portal</li>
                    </ul>
                    
                    <div class='contact-info'>
                        <h5>Contact Information</h5>
                        <p>If you need to update your application or have questions:<br>
                        <strong>ZPPSU Scholarship Office</strong><br>
                        Email: zppsuscholarshipoffice@gmail.com</p>
                    </div>
                    
                    <p>We appreciate your patience as we carefully review all applications. You will be notified as soon as a decision is made.</p>
                    
                    <a href='http://yourwebsite.com/my_applications.php' class='button'>Check Your Application Status</a>
                </div>
                
                <div class='footer'>
                    <p><strong>Zamboanga Peninsula Polytechnic State University</strong><br>
                    Office of Scholarship Programs</p>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>";

        if ($mail->send()) {
            return true;
        } else {
            return "Error: Mail not sent.";
        }
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function sendRejectionEmail($applicantEmail, $applicantName, $rejectionReason = '', $applicationId = '', $scholarshipGrant = '')
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
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
        $mail->Subject = 'Scholarship Application Status Update';

        // Build rejection reason section
        $reasonSection = '';
        if (!empty($rejectionReason)) {
            $reasonSection = "<div class='info-box' style='border-left-color: #dc3545;'>
                <h4>Review Notes</h4>
                <p><strong>Reason:</strong> $rejectionReason</p>
            </div>";
        }

        // Build application details if provided
        $applicationDetails = '';
        if (!empty($applicationId) && !empty($scholarshipGrant)) {
            $applicationDetails = "<div class='info-box'>
                <h4>Application Details</h4>
                <p><strong>Application ID:</strong> $applicationId</p>
                <p><strong>Scholarship Grant:</strong> $scholarshipGrant</p>
                <p><strong>Decision Date:</strong> " . date('F j, Y, g:ia') . "</p>
            </div>";
        }

        $mail->Body = "<!DOCTYPE html>
        <html>
        <head>
            <style>" . getEmailStyles() . "</style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header' style='background-color: #dc3545;'>
                    <h2>Scholarship Application Decision</h2>
                </div>
                
                <div class='content'>
                    <h3>Dear $applicantName,</h3>
                    
                    <p>After careful review by the Scholarship Committee, we regret to inform you that your application has been marked as <strong>NOT QUALIFIED</strong> for this cycle.</p>
                    
                    $applicationDetails
                    $reasonSection
                    
                    <h4>Current Status: <span class='status-badge' style='background-color: #dc3545;'>NOT QUALIFIED</span></h4>
                    
                    <p>We understand this news may be disappointing and want to assure you that all applications undergo thorough evaluation based on established criteria.</p>
                    
                    <div class='section-title'>Future Opportunities</div>
                    <ul>
                        <li>New scholarship cycles are announced each semester</li>
                        <li>You may reapply for future scholarship opportunities</li>
                        <li>Consider exploring other financial aid options</li>
                        <li>Check our website regularly for new programs</li>
                    </ul>
                    
                    <div class='section-title'>Improving Your Application</div>
                    <ul>
                        <li>Ensure all required documents are complete and legible</li>
                        <li>Submit applications before the deadline</li>
                        <li>Provide accurate and up-to-date information</li>
                        <li>Follow all application instructions carefully</li>
                    </ul>
                    
                    <div class='contact-info'>
                        <h5>Feedback and Inquiries</h5>
                        <p>If you would like feedback on your application or have questions:<br>
                        <strong>ZPPSU Scholarship Office</strong><br>
                        Email: zppsuscholarshipoffice@gmail.com<br>
                        Office Hours: Monday-Friday, 8:00 AM - 5:00 PM</p>
                    </div>
                    
                    <p>We appreciate your interest in ZPPSU scholarships and encourage you to apply for future opportunities.</p>
                    
                    <a href='http://yourwebsite.com/scholarship_form.php' class='button' style='background-color: #6c757d;'>View Other Scholarship Opportunities</a>
                </div>
                
                <div class='footer'>
                    <p><strong>Zamboanga Peninsula Polytechnic State University</strong><br>
                    Office of Scholarship Programs</p>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>";

        if ($mail->send()) {
            return true;
        } else {
            return "Error: Mail not sent.";
        }
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Optional: Function to send a generic notification email
function sendGenericNotificationEmail($applicantEmail, $applicantName, $subject, $message, $applicationId = '')
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
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
        $mail->Subject = $subject;

        // Build application details if provided
        $applicationDetails = '';
        if (!empty($applicationId)) {
            $applicationDetails = "<div class='info-box'>
                <h4>Application Reference</h4>
                <p><strong>Application ID:</strong> $applicationId</p>
            </div>";
        }

        $mail->Body = "<!DOCTYPE html>
        <html>
        <head>
            <style>" . getEmailStyles() . "</style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h2>ZPPSU Scholarship Notification</h2>
                </div>
                
                <div class='content'>
                    <h3>Dear $applicantName,</h3>
                    
                    $applicationDetails
                    
                    <div class='info-box'>
                        <h4>Message</h4>
                        <p>$message</p>
                    </div>
                    
                    <div class='contact-info'>
                        <h5>Contact Us</h5>
                        <p><strong>ZPPSU Scholarship Office</strong><br>
                        Email: zppsuscholarshipoffice@gmail.com</p>
                    </div>
                </div>
                
                <div class='footer'>
                    <p><strong>Zamboanga Peninsula Polytechnic State University</strong><br>
                    Office of Scholarship Programs</p>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>";

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