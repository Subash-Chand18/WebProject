<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//Load Composer's autoloader (created by composer, not included with PHPMailer)
require 'vendor/autoload.php';
function mailer($message)
{
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;   
        $mail->SMTPDebug = 0;                   //Enable verbose debug output
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '16419e58453544';
        $mail->Password = '37e11ef18c6f9f';                               //SMTP password
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption

        //Recipients
        $mail->setFrom('dipa@gmail.com', 'Dipa Bist');
        $mail->addAddress('deepa@gmail.com', 'Deepa Bist');     //Add a recipient
        $mail->addReplyTo('no-reply@nast.edu.np', 'Nast college');

        

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Here is the subject from forgot pass';
        $mail->Body    = $message;

        $mail->send();
        //echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}