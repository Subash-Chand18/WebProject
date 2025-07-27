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
        $mail->Username = '16419e58453544';
        $mail->Password = '37e11ef18c6f9f'; 

        //Recipients
        $mail->setFrom('dipa@gmail.com', 'Dipa Bist');
        //$mail->addAddress('deepa@gmail.com', 'Deepa Bist');     //Add a recipient
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