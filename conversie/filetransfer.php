<?php
  include("ckan.php");
  
  function beginsWith($txt, $begin){
      if(strtolower(substr($txt,0, strlen($begin))) == strtolower($begin)) return true;
      return false;
  }
        
  $ckan = new CKAN(CKAN3_KEY, CKAN3_URL);
  $ckan->getDatasets();
  
  foreach($ckan->datasets as $key => $set){
      foreach($set->res_url as $i => $url){
          if(beginsWith($url, "https://files.datapress.com/amsterdam/")){
            //print("<BR>". $set->name . " - <a href='". $url . "'>". $url ."</a>");
            $dir = "../data/". $set->name;
            $parts = explode("/",$url);
            $fname = $parts[count($parts)-1];            
            
            /*
            if (!file_exists($dir)) {
                 mkdir($dir, 0777, true);
            }

            $f = fopen($dir ."/". $fname,"w");
            fwrite($f, file_get_contents($url));
            fclose($f);
            */
              
            $ckan->changeResource($set->name, $i, "http://open.datapunt.amsterdam.nl/uploads/". $set->name ."/". $fname, $set->res_name[$i]);
            //exit();
          }
      }
  }
 ?>
