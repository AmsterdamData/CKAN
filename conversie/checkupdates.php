<?php
  include("ckan.php");
  include("../../AODS/AODS.php");
  include(dirname(__FILE__) ."/settings.php");
      
  $ckan = new CKAN();
  $sets = $ckan->getDatasets();

  $AODS = new AODS();  

  date_default_timezone_set('CET');  
  $result = "<table><tr><th>Set</th><th>Url</th><th>Set time</th><th>File time</th><th>Status</th><tr>";
  foreach($ckan->datasets as $set){
      foreach($set->res_url as $resource){
          $check_against = strtolower("http://open.datapunt.amsterdam.nl/");
          if(substr(strtolower($resource),0,strlen($check_against)) == $check_against){
            //File is saved on amsterdamopendata.nl/files/   
            $fname = str_ireplace($check_against,"",$resource);
            $ftime = $AODS->getFiletime($fname);
            
            //print("<BR>Checking status for: ".$fname);
            //print("<BR>File time: ". date("Y-m-d H:i:s", $ftime));
            //print("<BR><PRE>"); print_r($set); print("</PRE>");
            
            $stime = strtotime($set->metadata_modified);
            
            //print("<BR>Entry time: ". date("Y-m-d H:i:s", $stime));
            $result .= "<tr><td>". $set->title ."</td><td>". $fname ."</td><td>". date("Y-m-d H:i:s", $stime) ."</td><td>". date("Y-m-d H:i:s", $ftime) ."<td><td>";
            if(date("Ymd", $ftime) <> date("Ymd", $stime) && $ftime > $stime){
                ob_start();
                $ckan->setLastUpdated($set->name, $ftime);
                ob_end_clean();
                $result .= "Updated to ". date("Y-m-d", $ftime);
                $set->metadata_modified = date("Y-m-d H:i:s", $ftime);
            } else {
                $result .= "OK";
            }
            $result .= "</td></tr>\n\r";
          }
      }
  }   
  
print($result);
?>