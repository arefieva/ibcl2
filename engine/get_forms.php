<?php

unset($mail_attachments);
unset($error);

function get_question($lang)
{
  $a=round(rand(1,9));
  $b=round(rand(1,9));
  
  $number=array(1=>'один', 2=>'два', 3=>'три', 4=>'четыре', 5=>'пять', 6=>'шесть', 7=>'семь',8=>'восемь',9=>'девять');
  $number_eng=array(1=>'one', 2=>'two', 3=>'three', 4=>'four', 5=>'five', 6=>'six', 7=>'seven',8=>'eight',9=>'nine');
  $c=$a+$b;
  if($lang=='_eng'){
  $res=array($number_eng[$a],$number_eng[$b],md5($c),$c);
  }else{
  $res=array($number[$a],$number[$b],md5($c),$c);}
  return  $res;
} 

function doMailAttachFiles(){
  global $_FILES, $ent, $error;
  unset($res);
  $fileURL = $_FILES['file1'];
  $tempfile = $fileURL['tmp_name'];
  if(trim($tempfile)!=""){
    unset($r);
    $fs = filesize($tempfile) / 1024;
    if($fs > 5*1024) $error[] = "Размер файла 1 (".number_format($fs, 2)." кб) превышает допустимый максимум (".$ent['fileMax']." кб)";
    $r['path'] = $tempfile;
    $r['name'] = $fileURL['name'];
    $res[] = $r;
  }
  
  $fileURL = $_FILES['file2'];
  $tempfile = $fileURL['tmp_name'];
  if(trim($tempfile)!=""){
    unset($r);
    $fs = filesize($tempfile) / 1024;
    if($fs > $ent['fileMax']) $error[] = "Размер файла 2 (".number_format($fs, 2)." кб) превышает допустимый максимум (".$ent['fileMax']." кб)";
    $r['path'] = $tempfile;
    $r['name'] = $fileURL['name'];
    $res[] = $r;
  }
  $res['errors'] = $error;
  return $res;
}



function displayForm($form, $params){
  global $db, $id, $blade;
  $ent['id'] = $id;
  $ent['form'] = $form;
  $ent = my_array_merge($ent, populateFromPost());
  $question=get_question(language_suffix);
  $ent['ans']=$question[2];
  $ent['a']=$question[0];
  $ent['b']=$question[1];
  $ent['c']=$question[3];
  if(!empty($params)){
    $ent = array_merge($ent, $params);
  }
  return $blade->render('forms.form'.$form, $ent);
}

function doProcessForm($formid){
  global $ent, $mailSubject, $globalMailTo, $required, $act1, $email, $ajax;
  

  $required = explode(",", $required);
  $ok = 1;
  for ($i=0; $i<count($required); $i++){
    $r = trim(str_replace(",", "", $required[$i]));
    $p = strpos($r, "=");
    $p1 = strpos($r, "|");
    if($p) {
       // the "=" sign: if var1 is set, var 2 should also be set
      list($var1, $var2) = explode("=", $r);
      
      $var1 = trim($var1);
      $var2 = trim($var2);
      global $$var1, $$var2;

      if(trim($$var1)!=""&&trim($$var2)=="") $ok = 0;
    } else if($p1) { // the "|" sign: at least one of given vars should be set
      $vars = explode("|", $r);
      $ok1 = 0;
      for($j=0; $j<count($vars); $j++){
        $v = trim($vars[$j]);
        global $$v;
        if(trim($$v)!="") $ok1 = 1;
      }
      if($ok1!=1) $ok = 0;
    } else { // just check var
      global $$r;

      if(trim($$r)=="") $ok = 0;
    }

  }

  global $formProcessSuccess;

  if($ok!=1) { // display error message
    $res = parseTemplate(tplFromFile(dir_prefix."engine/templates/forms/error".language_suffix.".htm"), $ent);

    $formProcessSuccess = false;
  }
  else switch($act1) {

    case "doRegisterUser":	return doRegisterUser();
    				break;
    				
    case "doRecallPassword":	return doRecallPassword();
    				break;
     
    default: 			$ent = my_array_merge($ent, populateFromPost());

  				if($formid==1||$formid==2) $mail_attachments = doMailAttachFiles();
    				$error = $mail_attachments['errors'];
    				global $ans, $Answer;
    				if(md5($Answer)!=$ans) $error[] = "Неверный ответ на вопрос!";
    				if(count($error)>0) {
    				  $ent['message'] = join("<br><br>", $error);
    				  $res = parseTemplate(tplFromFile(dir_prefix."engine/templates/error.htm"), $ent);
    				  $formProcessSuccess = false;
  				} else {
            if($formid == 5){
              $Email = getSystemVariable($db, "globalMailTo");
              $mailSubject = 'Перезвоните мне';
              $ContactName = getSystemVariable($db, "site_name");
            }
            if($formid == 1){
              $ContactName = getSystemVariable($db, "site_name");
            }
  				  doMail($globalMailTo, $mailSubject, "<html><body>".nl2br(parseTemplate(tplFromFile(dir_prefix."engine/templates/forms/email".$formid.".htm"), $ent))."</body></html>", $Email, $ContactName, $mail_attachments);
  				  $res = parseTemplate(tplFromFile(dir_prefix."engine/templates/forms/ok.htm"), $ent);
  				  $formProcessSuccess = true;
  				}
  				break;
  }
  
  if($error && $ajax == "1")
    $res .= displayForm(1);
  else if($error && $ajax == "5")
    $res .= displayForm(5);

  if($ajax == "1")
    die($res);
  else if($ajax == "5")
    die($res);
  else 
    return $res;

}


function validateFormInput($required){
   $required = explode(",", $required);
  $ok = 1;
  for ($i=0; $i<count($required); $i++){
    $r = trim(str_replace(",", "", $required[$i]));
    $p = strpos($r, "=");
    $p1 = strpos($r, "|");
    if($p) {
      list($var1, $var2) = explode("=", $r);
      
      $var1 = trim($var1);
      $var2 = trim($var2);
      global $$var1, $$var2;
      if(trim($$var1)!=""&&trim($$var2)=="") $ok = 0;
    } else if($p1) {
      $vars = explode("|", $r);
      $ok1 = 0;
      for($j=0; $j<count($vars); $j++){
        $v = trim($vars[$j]);
        global $$v;
        if(trim($$v)!="") $ok1 = 1;
      }
      if($ok1!=1) $ok = 0;
    } else {
      global $$r;
      if(trim($$r)=="") $ok = 0;
    }
  }
  if($ok!=1)
    return false;
  return true;
}

?>