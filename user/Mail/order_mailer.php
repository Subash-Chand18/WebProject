<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//Load Composer's autoloader (created by composer, not included with PHPMailer)
require 'vendor/autoload.php';
function mailer($to_email, $to_name, $subject, $message)
{
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings   
        $mail->SMTPDebug = 0;                   //Enable verbose debug output
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '729be990cf3d1c';
        $mail->Password = '3c154d60053249'; 

        //Recipients
        $mail->setFrom('elothoingstore@dlms.dev.np', 'Dipa Bist');
        //$mail->addAddress('subashchand31@gmail.com', 'Subash Chand');     //Add a recipient
        $mail->addAddress($to_email, $to_name);
        $mail->addReplyTo('no-reply@nast.edu.np', 'Nast college');

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}