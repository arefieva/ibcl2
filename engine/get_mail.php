<?php

require("class.phpmailer.php");

$mailer = new phpmailer();

function doMail($mailTo, $mailSubject, $mailBody, $mailFrom, $mailFromName, $attachments=NULL, $contentType="text/html"){
	global $mailer;

	$mailTo = str_replace('<br />', ',', str_replace(";", ",", trim($mailTo)));
	$mailFrom = explode("<br />", trim(str_replace(",", "<br />", str_replace(";", "<br />", $mailFrom))));
	$mailFrom = $mailFrom[0];

	$mailer->From			= $mailFrom;
	$mailer->FromName		= $mailFromName;
	$mailer->Mailer			= "mail";
	$mailer->Username		= "";
	$mailer->Password		= "";
	$mailer->Host			= "";
	$mailer->SMTPAuth		= false;
	$mailer->Subject		= $mailSubject;
	$mailer->ContentType	= $contentType;

	if($contentType=="text/plain") $mailer->Body = strip_tags(str_replace("&nbsp;", "", $mailBody));
	else $mailer->Body = $mailBody;

	$mailer->AddAddress($mailTo, "");
	//echo("mailing to $mailTo...<br>");
	for ($i=0; $i<count($attachments); $i++){
		$mailer->AddAttachment($attachments[$i]['path'], $attachments[$i]['name']);
	}

	if(!$mailer->Send()){
		$res = false;
		//die("error senging mail: ".$mailer->ErrorInfo."<hr>");
	}
	else $res = true;

	$mailer->ClearAddresses();
	$mailer->ClearAttachments();

	return $res;
}