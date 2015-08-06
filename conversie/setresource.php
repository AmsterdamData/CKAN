<?php
function getIP()
{
    if (getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP"); 
    else if(getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR"); 
    else if(getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR"); 
    else $ip = "UNKNOWN"; 
    return $ip; 
} 

if(getIP() == "92.254.0.239" || getIP() == "46.226.58.62" || getIP() == "::1"){
  include("ckan.php");

  $ckan = new CKAN();
  //$ckan->getDatasets();
  ob_start();
  $result = $ckan->changeResource($_POST["name"], $_POST["res"], $_POST["url"], $_POST["desc"]);
  ob_end_clean();
  print("OK!");
  
} else {
  print("Niet geautoriseerd. (". getIP() .")");
}
?>
