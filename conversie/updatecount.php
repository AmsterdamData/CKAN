<?php
  include("ckan.php");
  $count_niet_meetellen = 64;
  
  //error_reporting(0);
  
  $ckan = new CKAN();
  $sets = $ckan->getSearch();
  $count_sets = $sets->count;
  $count_resources = -1 * $count_niet_meetellen;
  $sets->results = array_reverse($sets->results);
  foreach($sets->results as $set){
      $count_resources += count($set->res_description);
  }
  
  $f = fopen("/home/amsterdam/domains/tools.amsterdamopendata.nl/public_html/numbers.json","w");
  fwrite($f, json_encode(Array("sets" => $count_sets, "resources" => $count_resources)));
  fclose($f);
  
?>
