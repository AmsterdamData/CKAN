<?php
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
      $ckan = new CKAN("");
      $ckan->getDatasets();
      foreach($ckan->datasets as $key => $set){
          foreach($set->res_url as $i => $url){
              if(beginsWith($url, "http://os.amsterdam")){
                $ckan->changeResource($set->name, $i, str_replace("http://os.amsterdam", "http://ois.amsterdam", $url), $set->res_description[$i]);
              }
          }
      }
    ?>  
</body>
</html>

