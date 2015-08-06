<?php
  include("ckan.php");
  include("../../AODS/AODS.php");
  include(dirname(__FILE__) ."/settings.php");
      
  $ckan = new CKAN();
  
  $ckan->getDatasets();
  $ckan->saveToCSV(true, DATA_FOLDER . "data-datasets.csv");
  $ckan->saveToCSV(false, DATA_FOLDER . "data-onderwerpen.csv");
  
  $AODS = new AODS();
  $AODS->upload(DATA_FOLDER . "data-datasets.csv");
  $AODS->upload(DATA_FOLDER . "data-onderwerpen.csv");
?>
