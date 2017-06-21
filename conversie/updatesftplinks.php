<?php
  include("settings.php");
  include("ckan.php");
  
  function beginsWith($txt, $begin){
      if(strtolower(substr($txt,0, strlen($begin))) == strtolower($begin)) return true;
      return false;
  }
  
  //error_reporting(0);
?>
<html>
<head>
<title>AODS Link-updater</title>
<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
</head>
<body>

    <?php
      
      $count = 0;
      $ckan = new CKAN(CKAN_KEY);
      $ckan->getDatasets();
      foreach($ckan->datasets as $key => $set){
          foreach($set->res_url as $i => $url){
              if(beginsWith($url, "http://213.187.241.90/")){
                  print("<HR>". $url."<BR/>");
                  $ckan->changeResource($set->name, $i, str_replace("http://213.187.241.90/", "http://open.datapunt.amsterdam.nl/", $url), $set->res_name[$i]);
                  //$ckan->changeResource($set->name, $i, str_replace("http://www.os.amsterdam.nl/", "http://www.ois.amsterdam.nl/", $url), $set->res_name[$i]);

                  //exit();
              }
          }
      }
    ?>  
</body>
</html>

