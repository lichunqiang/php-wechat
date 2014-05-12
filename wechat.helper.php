<?php
function debug_print($var, $nfo='DEBUG', $htm=false, $ret=false) { 
    $var_str = print_r($var, true); 
    if ($htm !== false) { $var_str = htmlentities($var_str); } 
    $outstr = ''; 
    $outstr = '<p>--------- <strong>'.$nfo.'</strong> ---------</p>'."\n"; 
    $outstr .= '<pre style="margin:18px 4px; padding:6px; text-align:left; background:#DEDEDE; color:#000099;">'."\n"; 
    $outstr .= $var_str."\n"; 
    $outstr .= '</pre>'."\n"; 
	$outstr .= '<p>--------- <strong>'.$nfo.' END</strong> ---------</p>'."\n";
    if ($ret !== false) { $result = $outstr; } 
    else { print $outstr; $result = true; } 
    return $result; 
}

function formatbytes($file, $type)
{
   switch($type){
      case "KB":
         $filesize = filesize($file) * .0009765625; // bytes to KB
      break;
      case "MB":
         $filesize = (filesize($file) * .0009765625) * .0009765625; // bytes to MB
      break;
      case "GB":
         $filesize = ((filesize($file) * .0009765625) * .0009765625) * .0009765625; // bytes to GB
      break;
   }
   if($filesize <= 0){
      return $filesize = 'unknown file size';}
   else{return round($filesize, 2).' '.$type;}
}