<?php
  include("ckan.php");
      
  $ckan1 = new CKAN(CKAN_KEY);
  $ckan2 = new CKAN(CKAN3_KEY, CKAN3_URL);
  
  $names = $ckan1->getNames();
  
  $existing = $ckan2->getNames();
  
  $organizations = $ckan2->getOrganizations();
  $groups = $ckan2->getGroups();

  foreach($names as $name){
      if(!in_array($name, $existing)){
      
          //Get set from source
          $set = $ckan1->getSet($name);
          
          //Check if organization exists, if not create
          if(!in_array($set->organization->name, $organizations)){
              $ckan2->createOrganization($set->organization);
              $organizations[] = $set->organization->name;
          }

          //Check if group exists, if not create
          foreach($set->groups as $group){
              if(!in_array($group->name, $groups)){
                  $ckan2->createGroup($group);
                  $groups[] = $group->name;
              }
          }
          
          //Debugging some errors
          foreach($set->resources as $key => $s){
              if(strpos($set->resources[$key]->mimetype,";") > 0){
                $parts = explode(";", $set->resources[$key]->mimetype);
                $set->resources[$key]->mimetype = $parts[0]; //Quick & dirty; semicolon led to errors, and mimetype isn't used in standard CKAN
              }
              if($set->resources[$key]->csvlint_json){
                $set->resources[$key]->csvlint_json = "";
              }
          }
          
          $set->id = null;
          $set->notes = str_replace(array("\r", "\n", chr(13)), '', $set->notes);

          $ckan2->createDataset($set);
          //$ckan2->setTimes($name, strtotime($set->metadata_created), strtotime($set->metadata_modified)); //TODO: Nog te testen!
      } else {
      }
  }
?>
