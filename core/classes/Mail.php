<?php


class Mail
{

    public static function send($to, $subject, $template, $params)
    {
        $gl_config = Tools::getConfig();

        $viewManager = new RenderEmail();
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->SMTPDebug = 0;

        if ($gl_config['email']['smtp'] === true)
        {
            $mail->isSMTP();
            $mail->Host = $gl_config['email']['host'];
            $mail->Port = $gl_config['email']['port'];
            $mail->SMTPSecure = $gl_config['email']['secure'];
            if ($gl_config['email']['smtpsecure'] === true) {
                $mail->SMTPAuth = true;
            }
            $mail->Password =  $gl_config['email']['password'];
            $mail->Username = $gl_config['email']['login'];
            $mail->Password = $gl_config['email']['password'];
        }

        $mail->setFrom($gl_config['email']['from'], $gl_config['email']['from_name']);
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $viewManager->initVariable($params);
        $html = $viewManager->render('template/' . $template . ".html", false);
        $mail->msgHTML($html);
        $mail->send();
    }
}
