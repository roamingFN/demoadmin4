<?php

function format_email($info, $format){

	//set the root
	$root = $_SERVER['DOCUMENT_ROOT'].'/china-express/signup_template';

	//grab the template content
	$template = file_get_contents($root.'/signup_template.'.$format);
			
	//replace all the tags
	$template = ereg_replace('{USERNAME}', $info['username'], $template);
	$template = ereg_replace('{EMAIL}', $info['email'], $template);
	$template = ereg_replace('{KEY}', $info['key'], $template);
	$template = ereg_replace('{SITEPATH}','http://www.narutoroyal.com/china-express/', $template);
		
	//return the html of the template
	return $template;

}

function format_email_otp($info, $format){

	//set the root
	$root = $_SERVER['DOCUMENT_ROOT'].'/china-express/otp_template';

	//grab the template content
	$template = file_get_contents($root.'/otp_template.'.$format);
			
	//replace all the tags
	$template = ereg_replace('{USERNAME}', $info['username'], $template);
	$template = ereg_replace('{EMAIL}', $info['email'], $template);
	$template = ereg_replace('{KEY}', $info['key'], $template);
	$template = ereg_replace('{REF}', $info['ref'], $template);
	$template = ereg_replace('{SITEPATH}','http://www.narutoroyal.com/china-express/', $template);
		
	//return the html of the template
	return $template;

}

//send the welcome letter
function send_email($info){
		
	//format each email
	$body = format_email($info,'html');
	$body_plain_txt = format_email($info,'txt');

	//setup the mailer
	$transport = Swift_MailTransport::newInstance();
	$mailer = Swift_Mailer::newInstance($transport);
	$message = Swift_Message::newInstance();
	$message ->setSubject('Email Confirmation - ยืนยันอีเมล์เพื่อเข้าใช้งานเว็บไซด์ China Express');
	$message ->setFrom(array('noreply@china-express.com' => 'China Express'));
	$message ->setTo(array($info['email'] => $info['username']));
	
	$message ->setBody($body_plain_txt);
	$message ->addPart($body, 'text/html');
			
	$result = $mailer->send($message);
	
	return $result;
	
}

//send the welcome letter
function send_email_otp($info){
		
	//format each email
	$body = format_email_otp($info,'html');
	$body_plain_txt = format_email_otp($info,'txt');

	//setup the mailer
	$transport = Swift_MailTransport::newInstance();
	$mailer = Swift_Mailer::newInstance($transport);
	$message = Swift_Message::newInstance();
	$message ->setSubject('รหัส OTP สำหรับการทำรายการในเว็บไซด์ China Express');
	$message ->setFrom(array('noreply@china-express.com' => 'China Express'));
	$message ->setTo(array($info['email'] => $info['username']));
	
	$message ->setBody($body_plain_txt);
	$message ->addPart($body, 'text/html');
			
	$result = $mailer->send($message);
	
	return $result;
	
}

//cleanup the errors
function show_errors($action){

	$error = false;

	if(!empty($action['result'])){
	
		$error = "<ul class=\"alert $action[result]\">"."\n";

		if(is_array($action['text'])){
	
			//loop out each error
			foreach($action['text'] as $text){
			
				$error .= "<li><p>$text</p></li>"."\n";
			
			}	
		
		}else{
		
			//single error
			$error .= "<li><p>$action[text]</p></li>";
		
		}
		
		$error .= "</ul>"."\n";
		
	}

	return $error;

}