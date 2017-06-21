<?php
  include("ckan.php");
?>
<html>
<head>
<title>AODS Link-updater</title>
<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
</head>
<body>
<table>
    <tr>
        <th>Title</th>
        <th>Name</th>
        <th>#/download/</th>
        <th>#other</th>
    </tr>
    
    <?php
      
      $count = 0;
      $ckan = new CKAN();
      $ckan->getDatasets();
      foreach($ckan->datasets as $key => $set){
          $ois = false;
          foreach($set->tags as $i => $tag){
              if($tag == "ois"){
                  $ois = true;
              }
          }
          $direct = 0;
          $download = 0;
          if($ois){
              foreach($set->res_url as $j => $url){
                  if(stripos($url, "ois.amsterdam.nl/download/") > 0){
                      $download++;
                  } else {
                      $direct++;
                  }
              }
              print("<tr><td>". $set->title ."</td><td>". $set->name ."</td><td>". $download ."</td><td>". $direct ."</td></tr>");
          }
      }
    ?>  
    </table>
    </body>
</html>

