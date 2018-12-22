<?php


class Mail
{

public static function send($to, $subject, $template, $params)
{
    global $gl_config;
    /**
     * This example shows settings to use when sending via Google's Gmail servers.
     * This uses traditional id & password authentication - look at the gmail_xoauth.phps
     * example to see how to use XOAUTH2.
     * The IMAP section shows how to save this message to the 'Sent Mail' folder using IMAP commands.
     */
//Import PHPMailer classes into the global namespace
    $viewManager = new RenderEmail();
//Create a new PHPMailer instance
    $mail = new PHPMailer\PHPMailer\PHPMailer();
//Tell PHPMailer to use SMTP
    $mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
    $mail->SMTPDebug =0;
//Set the hostname of the mail server
    $mail->Host = $gl_config['email']['host'];
// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6
//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
    $mail->Port = $gl_config['email']['port'];
//Set the encryption system to use - ssl (deprecated) or tls
    $mail->SMTPSecure = $gl_config['email']['secure'];
//Whether to use SMTP authentication
    $mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
    $mail->Username =$gl_config['email']['login'];
//Password to use for SMTP authentication
    $mail->Password = $gl_config['email']['password'];
//Set who the message is to be sent from
    $mail->setFrom( $gl_config['email']['from'],  $gl_config['email']['from_name']);
//Set an alternative reply-to address
    $mail->addAddress($to);
//Set who the message is to be sent to
//Set the subject line
    $mail->Subject = $subject;
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
    $viewManager->initVariable($params);
   $html =  $viewManager->render('template/'.$template.".html", false);
    $mail->msgHTML($html);
//Replace the plain text body with one created manually
    $mail->AltBody = 'This is a plain-text message body';
//Attach an image file
//send the message, check for errors
   $mail->send();
}
}