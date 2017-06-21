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
<title>AODS Updater</title>
<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
</head>
<body>

    <?php
      
      $count = 0;
      $ckan = new CKAN(CKAN_KEY);
      $ckan->getDatasets();
      foreach($ckan->datasets as $key => $set){
          print("<BR><h1>". $set->name ."</h1>");
          $dataset = $ckan->getSet($set->name);
          /*
          //Step 1: fill contact and publisher with maintainer and author
          $dataset->contact_name = $dataset->maintainer;
          $dataset->contact_email = $dataset->maintainer_email;
          $dataset->publisher = $dataset->author;
          $dataset->publisher_email = $dataset->author_email;
          $dataset->theme = $dataset->groups[0]->display_name;
          $dataset->dataclassificatie = "Open";
          */
          //Step 2: remove maintainer and author
          $dataset->maintainer = "";
          $dataset->maintainer_email = "";
          $dataset->author = "";
          $dataset->author_email = "";
           
          $ckan->setSet($dataset);   
          //exit();
      }
    ?>  
</body>
</html>

