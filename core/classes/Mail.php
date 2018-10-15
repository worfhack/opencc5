<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Mail
{
	static public function Send( $subject, $to, $content,Twig_Environment $twig , $cc = '', $cci = '',  $from = false, $fromName = false, $dir = false)
	{


        $template  =  $twig->loadTemplate('mail.html');


        $parameters  =[
            'content'=>$content,
        ];





        $body = $template->renderBlock('body_mail', $parameters);
		try{
			$mail = new PHPMailer();
			$mail->IsHTML(true);
			// TODO no reply static value
			$mail->From = 'contact@fastpasttours.fr';
			$mail->FromName = 'contact@fastpasttours.fr';
			$mail->CharSet = 'UTF-8';
			
			if(is_array($to))
			{
				foreach($to as $recipient) {
					$tos = explode(';', $recipient);
					foreach($tos as $to_mail)
						$mail->AddAddress($to_mail);
				}
			}
			else {
				$tos = explode(';', $to);
				foreach($tos as $to_mail)
					$mail->AddAddress($to_mail);
			}
			$mail->Subject    = ($subject);
			$mail->AltBody    = strip_tags (str_replace('<br />', "\n", $body)); 
			$mail->Body		  = $body;

			$mail->Send();
			return true;
		} catch (phpmailerException $e) {
			Log::write($initiator = 'Mail_Send', $log = $e->errorMessage());
			return false;
		} catch (Exception $e) {
			Log::write($initiator = 'Mail_Send', $log = $e->getMessage());
			return false;
		}

	}
	

}
