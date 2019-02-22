<?php

//===============================================================
function highlightEmails($string, $linkClass=""){
  if($linkClass!="") $linkClass=" class=\"".$linkClass."\"";
  while(ereg("([a-zA-Z0-9_\-])+(\.([a-zA-Z0-9_\-])+)*@((\[(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5]))\]))|((([a-zA-Z0-9])+(([\-])+([a-zA-Z0-9])+)*\.)+([a-zA-Z])+(([\-])+([a-zA-Z0-9])+)*))", $string, $regs)) {
    $string = str_replace($regs[0], "<a ".$linkClass." href=\"".encode_everything("mailto:".$regs[0])."\">".encode_everything($regs[0])."</a>", $string);
  }
  return $string;
}
//===============================================================
function encode_everything($string){
   $encoded = "";
   for ($n=0;$n<strlen($string);$n++){
       $check = htmlentities($string[$n],ENT_QUOTES);
      $string[$n] == $check ? $encoded .= "&#".ord($string[$n]).";" : $encoded .= $check;
   }
   return $encoded;
}
//===============================================================
function processTable($what){
  $id = str_replace("#", "", $what[0]);
  $align = str_replace("#", "", $what[1]);
  $width = str_replace("#", "", $what[2]);

  if($align=="c") $align=" align=center";
  else if($align=="l") $align=" align=left";
  else if($align=="r") $align=" align=right";
  else $align="";

  return getElementTable($id, $align, $width);
}
//===============================================================
function processElement($element, $type){
      switch($type){
        case "pic":	$res = processPic($element);
         		break;
         		
        case "cat":	$res = processCatalogue($element);
         		break;

        case "pcat":	$res = processPicCategory($element);
         		break;

        case "link":	$res = processLink($element);
         		break;
         		         		
        case "news":	$res = processNews($element);
         		break;

        case "reviews":  $res = displayReviews($element);
            break;

        case "projects":  $res = processProjects($element);
            break;
         		
        case "articles":	$res = processArticles($element);
         		break;
				
		    case "spec":	$res = displaySpecial(1000, "cat_special.htm");
         		break;
         		
        case "feedback":	$res = processFeedback($element);
         		break;		
         		
        case "sublinks":	$res = processSubLinks($element);
         		break;
         		
        case "faq":	$res = processFaq();
         		break;

        case "form":	$res = displayForm($element);
         		break;
		 
		    case "hCart":	$res = displayHCart();
         		break;    
         }
      return $res;
    }
//===============================================================
function replaceSpecial($what, $type){
do {
  $found = false;
  $pos = strpos($what, "<#".$type);
  if($pos>0||$pos===0) {
    $found    = true;
    $str1     = substr($what, $pos);
    $str1     = substr($str1, 0 , strpos($str1, "#>")+2);
    $str2     = str_replace("<#".$type."#", "", $str1);
    $str2     = str_replace("#>", "", $str2);
    $command  = explode("#", $str2);

    $res = processElement($command, $type);

    $what = str_replace($str1, $res, $what);
  }
} while ($found);
return $what;
}
//===============================================================
function doHighlightKeywords($what, $h_bold, $h_normal){
	global $debug;
	$keywords = file(dir_prefix."engine/keywords_highlight".language_suffix.".txt");
	$replaced = false;
	for ($i=0; $i<count($keywords); $i++) {
      
      $kw = trim(str_replace($n, "", str_replace($r, "", $keywords[$i])));
      if($kw!="") {
        $replaced = true;
        $what = preg_replace("/([- (>.,])($kw)([- )\?:<.,;])/iu","\\1<h".$h_normal.">\\2</h".$h_normal.">\\3", $what);
        
      }
    }
    if($replaced) {
        $what1 = "";
        $current = "";
        $tag = "";
        $tag_open = false;
        $inside_bold = 0;
        $start_pos = 0;
        for ($j=0; $j<strlen($what); $j++) {
          $c = substr($what, $j, 1);
          $what1.=$c;
          if($c=="<") {
            $tag = "";
            $tag_open = true;
            $start_pos = $j;
          }
          else if ($c==">") {
            $tag_open = false;
            $tag.=$c;
            if($debug==1) echo("got tag: <b>".htmlspecialchars($tag)."</b>; inside_bold=$inside_bold<br>");
            if(strtoupper(str_replace(" ", "", $tag))=="<B>"||strtoupper(str_replace(" ", "", $tag))=="<STRONG>"||strtoupper(str_replace(" ", "", $tag))=="<H6>"||strtoupper(str_replace(" ", "", $tag))=="<H".$h_bold.">") $inside_bold = 1;
            else if(strtoupper(str_replace(" ", "", $tag))=="</B>"||strtoupper(str_replace(" ", "", $tag))=="</STRONG>"||strtoupper(str_replace(" ", "", $tag))=="</H6>"||strtoupper(str_replace(" ", "", $tag))=="</H".$h_bold.">") $inside_bold = 0;
            
            if((strtoupper($tag)=="<H".$h_normal.">"||strtoupper($tag)=="</H".$h_normal.">")&&$inside_bold==1){
              // replace normal with bold
              if($debug==1) echo("changing $h_normal to $h_bold in <b>".htmlspecialchars(substr($what1, $start_pos, strlen($tag)))."</b><br><br>");
              $what2 = substr($what1, 0, $start_pos);
              $what3 = str_replace($h_normal, $h_bold, substr($what1, $start_pos, strlen($tag)));
              $what1 = $what2.$what3;
            }
            
          }
          if($tag_open) $tag.=$c;
        }
        $what = $what1;
    }
    return $what;
}
//===============================================================
function processText($what, $highlight=0){
  global $separator, $debug;
  $n = "\n";
  $r = "\r";

  $what = str_replace($r, "", $what);
  $what = str_replace("<#sep#>", $separator, $what);
  $what = replaceSpecial($what, 'link');  
  $what = replaceSpecial($what, 'sublinks');
  $what = replaceSpecial($what, 'pic');
  $what = replaceSpecial($what, 'news');
  $what = replaceSpecial($what, 'reviews');
  $what = replaceSpecial($what, 'projects');
  $what = replaceSpecial($what, 'articles');
  $what = replaceSpecial($what, 'spec');
  $what = replaceSpecial($what, 'cat');
  $what = replaceSpecial($what, 'faq');
  $what = replaceSpecial($what, 'pcat');
  $what = replaceSpecial($what, 'feedback');
  $what = replaceSpecial($what, 'hCart');
  $what = replaceSpecial($what, 'form');
  $what = preg_replace("/<p[^>]*><\/p>/iu", "", $what);
  $what = preg_replace("/<p[^>]*> <\/p>/iu", "", $what);
  $what = str_replace("class=\"\"", "", $what);
  $what = str_replace("class=\"mceVisualAid\"", "", $what);
  $what = str_replace("class=mceVisualAid", "", $what);
  $what = str_replace(" mceVisualAid", "", $what);
  $what = str_replace("mceVisualAid", "", $what);
  $what = preg_replace("/<table([^>]*) border=1([^>]*)>/iu", "<table\\1 class=\"tbl\"\\2>", $what);
  $what = preg_replace("/<table([^>]*) border=\"1\"([^>]*)>/iu", "<table\\1 class=\"tbl\"\\2>", $what);

  return $what;
}