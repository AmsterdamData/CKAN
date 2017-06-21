<?php
  require_once 'csv.php';
  include(dirname(__FILE__) ."/settings.php");
  set_time_limit(300);
  
  class CKAN{
      var $url;
      var $key;
      var $names;
      var $datasets;
      var $themas;
      var $categorien;
           
      function CKAN($key = CKAN_KEY, $url = CKAN_URL, $data_url = DATA_URL){
          $this->key = $key;
          $this->url = $url;
          $this->dataUrl = $data_url;
          
          $this->themas = Array(          
            "zorg-welzijn" => "Zorg & Welzijn",
            "economie-haven" => "Economie & Werk",
            "wonen-leefomgeving" => "Ruimte",
            "openbare-orde-veiligheid" => "Veiligheid",
            "stedelijke-ontwikkeling" => "Ruimte",
            "geografie" => "Basisinformatie",
            "bestuur-en-organisatie" => "Democratie & Transparantie",
            "energie" => "Energie",
            "bevolking" => "Basisinformatie",
            "openbare-ruimte-groen" => "Ruimte",
            "verkeer-infrastructuur" => "Mobiliteit",
            "toerisme-cultuur" => "Toerisme & Cultuur",
            "sport-recreatie" => "Toerisme & Cultuur",
            "educatie-jeugd-diversiteit" => "Onderwijs & Jeugd",
            "verkiezingen" => "Democratie & Transparantie",
            "milieu-water" => "Energie",
            "dienstverlening" => "Democratie & Transparantie",
            "werk-inkomen" => "Economie & Werk"
            );

          
          $this->categorien = Array(          
            "zorg-welzijn" => "Zorg & Welzijn",
            "economie-haven" => "Economie & Haven",
            "wonen-leefomgeving" => "Wonen & Leefomgeving",
            "openbare-orde-veiligheid" => "Openbare orde & Veiligheid",
            "stedelijke-ontwikkeling" => "Stedelijke ontwikkeling",
            "geografie" => "Geografie",
            "bestuur-en-organisatie" => "Bestuur & Organisatie",
            "energie" => "Energie",
            "bevolking" => "Bevolking",
            "openbare-ruimte-groen" => "Openbare ruimte & Groen",
            "verkeer-infrastructuur" => "Verkeer & infrastructuur",
            "toerisme-cultuur" => "Toerisme & Cultuur",
            "sport-recreatie" => "Sport & Recreatie",
            "educatie-jeugd-diversiteit" => "Onderwijs, Jeugd & Diversiteit",
            "verkiezingen" => "Verkiezingen",
            "milieu-water" => "Milieu & Water",
            "dienstverlening" => "Dienstverlening",
            "werk-inkomen" => "Werk & Inkomen"
            );

          
      }
      
      function getDatasets(){
        $this->names = json_decode(file_get_contents($this->url ."search/dataset?all_fields=1&offset=0&limit=1000"));
        foreach($this->names->results as $result){
            $this->datasets[$result->name] = $result;
        }
        /*
        print("<PRE>");
        print_r($this->datasets);
        print("</PRE>");
        */
        //No need for populateDatasets anymore! 
        //$this->populateDatasets();
      }
      
      function getNames(){
          $result = json_decode(file_get_contents($this->url ."3/action/package_list"));
          if($result->success == 1){
            return $result->result;
          }
      }
      
      function getOrganizations(){
          $result = json_decode(file_get_contents($this->url ."3/action/organization_list"));
          if($result->success == 1){
            return $result->result;
          }
      }
      
      function getGroups(){
          $result = json_decode(file_get_contents($this->url ."3/action/group_list"));
          if($result->success == 1){
            return $result->result;
          }
      }
      
      function populateDatasets(){
          $this->datasets = Array();
          foreach($this->names as $name){
              $this->datasets[$name] = json_decode(file_get_contents($this->url ."rest/dataset/". $name));
          }
      }
      
      function setExists($name){
          $result = false;
          foreach($this->datasets as $key => $set){
              if(strtolower($key) == strtolower($name)) $result = true;
          }
          return $result;
      }
      
      function getSearch($q = "", $num = 250){
          $num = intval($num);
          if(!$num > 0) $num = 250;
          return json_decode(file_get_contents($this->url ."search/dataset?q=". urlencode($q) ."&all_fields=1&order_by=metadata_modified&offset=0&limit=".$num));
      }
      
  
      function saveToCSV($extend_to_resources = true, $filename = null){
          if(!$filename){
            $csv = new CSV("csv/datasets-". date("YmdHis")."-". max(0,$extend_to_resources) .".csv");
          } else {
            $csv = new CSV($filename);
          }
        
        if($extend_to_resources){
            $csv->addArrayHeader(Array("Thema", "Categorie", "Naam", "Titel", "Beschrijving",  "Tags", "Eigenaar", "Eigenaar e-mail", "Contactpersoon", "Contact e-mail", "Aangemaakt", "Gewijzigd", "Licentie", "Dataset url", "Dataset beschrijving", "Bestandsformaat"));
        } else {
            $csv->addArrayHeader(Array("Thema", "Categorie", "Naam", "Titel", "Beschrijving",  "Tags", "Eigenaar", "Eigenaar e-mail", "Contactpersoon", "Contact e-mail", "Aangemaakt", "Gewijzigd", "Licentie", "Directe url", "Aantal datasets", "Datasets"));
        }

        foreach($this->datasets as $name => $dataset){
            if($extend_to_resources){
                foreach($dataset->res_name as $key => $description){
                    $thema = $this->themas[$dataset->groups[0]];
                    $categorie = $this->categorien[$dataset->groups[0]];
                    $item = Array(
                        $thema,
                        $categorie,
                        $dataset->name,
                        $dataset->title,
                        $dataset->notes,
                        implode(" ", $dataset->tags),
                        $dataset->author,
                        $dataset->author_email,
                        $dataset->maintainer,
                        $dataset->maintainer_email,
                        $dataset->metadata_created,
                        $dataset->metadata_modified,
                        $dataset->license_id,
                        $dataset->res_url[$key],
                        $description,
                        $dataset->res_format[$key]
                    );
                    $csv->addArray($item);
                }
            } else {
            //Only datasets

                $thema = $this->themas[$dataset->groups[0]];
                $categorie = $this->categorien[$dataset->groups[0]];
                $url = $this->dataUrl . "?dataset=" . $dataset->name;
                
                $count = 0;
                $sets = "";
                $splitter = "";
                //print("<PRE>"); print_r($dataset); exit();
                foreach($dataset->res_name as $key => $description){
                    $count++;
                    $sets .= $splitter . $description;
                    $splitter = ", ";
                }
                
                $item = Array(
                    $thema,
                    $categorie,
                    $dataset->name,
                    $dataset->title,
                    $dataset->notes,
                    implode(" ", $dataset->tags),
                    $dataset->author,
                    $dataset->author_email,
                    $dataset->maintainer,
                    $dataset->maintainer_email,
                    $dataset->metadata_created,
                    $dataset->metadata_modified,
                    $dataset->license_id,
                    $url,
                    $count,
                    $sets
                );
                $csv->addArray($item);
            }
        }
        $csv->write();
    }
    
    function displayDetails($set){
        /*
        $dataset = json_decode(file_get_contents(CKAN_URL ."rest/dataset/". $set));
        print("<PRE>");
        print_r($dataset);
        print("</PRE>");
        */
        
        $data = '{"id": "'. $set .'"}'; 
        $tuCurl = curl_init(); 
        curl_setopt($tuCurl, CURLOPT_URL, $this->url . "3/action/package_show"); 
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Authorization: ". $this->key)); 
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);

        $tuData = curl_exec($tuCurl); 
        
        if(!curl_errno($tuCurl)){ 
            $json = json_decode($tuData);
            $dataset = $json->result;
            print("<HR><PRE>");
            print_r($dataset);
            print("</PRE><HR>");
          /*
          $info = curl_getinfo($tuCurl); 
          echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url']; 
          */
        } else { 
          echo 'Curl error: ' . curl_error($tuCurl); 
        } 

        curl_close($tuCurl); 
    }

    function changeOwner($set, $ownerName, $ownerMail){
        $data = '{"id": "'. $set .'"}'; 
        $tuCurl = curl_init(); 
        curl_setopt($tuCurl, CURLOPT_URL, $this->url . "3/action/package_show"); 
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Authorization: ". $this->key)); 
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);

        $tuData = curl_exec($tuCurl); 
        
        if(!curl_errno($tuCurl)){ 
            $json = json_decode($tuData);
            $dataset = $json->result;
        } else { 
          echo 'Curl error: ' . curl_error($tuCurl); 
        } 
        curl_close($tuCurl); 

        
        
        $dataset->maintainer = $ownerName;
        $dataset->maintainer_email = $ownerMail;
        
        $data = json_encode($dataset); 
        print_r($data);
        
        $tuCurl = curl_init(); 
        curl_setopt($tuCurl, CURLOPT_URL, $this->url . "3/action/package_update"); 
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Authorization: ". $this->key)); 
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);

        $tuData = curl_exec($tuCurl); 
        if(!curl_errno($tuCurl)){ 
          $info = curl_getinfo($tuCurl); 
          echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url']; 
        } else { 
          echo 'Curl error: ' . curl_error($tuCurl); 
        } 

        curl_close($tuCurl); 
    }
    
    function getSet($set){
        $data = '{"id": "'. $set .'"}'; 
        $tuCurl = curl_init(); 
        curl_setopt($tuCurl, CURLOPT_URL, $this->url . "3/action/package_show"); 
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Authorization: ". $this->key)); 
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);

        $tuData = curl_exec($tuCurl); 
        
        if(!curl_errno($tuCurl)){ 
            $json = json_decode($tuData);
            $dataset = $json->result;
        } else { 
          echo 'Curl error: ' . curl_error($tuCurl); 
        } 
        curl_close($tuCurl); 
        return $dataset;
    }
    
    function setSet($set){
        //print_r($this->key);
        //print("<PRE>");
        //print_r($set);
        $data = json_encode($set);
        //print("<HR>");
        //print_r($data);
        //print("<HR>");
        //exit();
        
        $tuCurl = curl_init(); 
        curl_setopt($tuCurl, CURLOPT_URL, $this->url . "3/action/package_update"); 
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, urlencode($data)); 
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Authorization: ". $this->key)); 
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
        $tuData = curl_exec($tuCurl); 
        if(!curl_errno($tuCurl)){ 
          $info = curl_getinfo($tuCurl); 
          echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url']; 
        } else { 
          echo 'Curl error: ' . curl_error($tuCurl); 
        } 
        curl_close($tuCurl); 
    }
        
    
    function setLastUpdated($set, $time){
        $dataset = $this->getSet($set);
        $dataset->metadata_modified = date("Y-m-d\TH:i:s", $time);      
        $this->setSet($dataset);
    }
    
    function setCreated($set, $time){
        $dataset = $this->getSet($set);
        $dataset->metadata_created = date("Y-m-d\TH:i:s", $time);
        $this->setSet($dataset);
    }
    
    function setTimes($set, $created, $lastupdated){
        $dataset = $this->getSet($set);
        $dataset->metadata_created = date("Y-m-d\TH:i:s.000000", $created);
        $dataset->metadata_modified = date("Y-m-d\TH:i:s.000000", $lastupdated);
        $this->setSet($dataset);
    }
    
    function changeResource($name, $res_id, $url, $description){
        $dataset = $this->getSet($name);
        
        $dataset->resources[$res_id]->url = $url;
        $dataset->resources[$res_id]->name = $description;
        //print("<PRE>"); print_r($dataset); print("</PRE>");
        
        $this->setSet($dataset);    
    }
    
    function setResourceTypes($set, $type = "file"){
        $dataset = $this->getSet($set);
        
        foreach($dataset->resources as $key => $resource){
            $resource->resource_type = $type;
            $dataset->resources[$key] = $resource;
        }
        
        $this->setSet($dataset);
    }
 
     
    function deleteDataset($set){
        $data = '{"id": "'. $set . '"}'; 
        $tuCurl = curl_init(); 
        curl_setopt($tuCurl, CURLOPT_URL, $this->url . "3/action/package_delete"); 
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Authorization: ". $this->key)); 
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);

        $tuData = curl_exec($tuCurl); 
        if(!curl_errno($tuCurl)){ 
          $info = curl_getinfo($tuCurl); 
          echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url']; 
        } else { 
          echo 'Curl error: ' . curl_error($tuCurl); 
        } 
        curl_close($tuCurl); 
        echo $tuData; 
    }

    function createDataset($set){
        $data = json_encode($set, JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); // JSON_UNESCAPED_UNICODE);
        $data = str_replace(";","",$data); //(*%FCV(^BR%$^&% is this really needed to work??
        
        print("<PRE>");
        print($data);
        print("</PRE>");
                
        $tuCurl = curl_init(); 
        curl_setopt($tuCurl, CURLOPT_URL, $this->url . "3/action/package_create"); 
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Authorization: ". $this->key)); 
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);

        $tuData = curl_exec($tuCurl); 
        if(!curl_errno($tuCurl)){ 
          $info = curl_getinfo($tuCurl); 
          print("<PRE>"); print_r($info); print("</PRE>");
          //echo 'Took ' . $info['total_time'] . ' sec. to create dataset <i>'. $set["name"] .'</i> (' . $info['url'] .')'; 
        } else {
          echo 'Curl error: ' . curl_error($tuCurl); 
        }

        curl_close($tuCurl); 
    }
    
    function createOrganization($org){
        $data = json_encode($org, JSON_PRETTY_PRINT | JSON_HEX_AMP); // | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
                
        $tuCurl = curl_init(); 
        curl_setopt($tuCurl, CURLOPT_URL, $this->url . "3/action/organization_create"); 
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Authorization: ". $this->key)); 
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);

        $tuData = curl_exec($tuCurl); 
        if(!curl_errno($tuCurl)){ 
          $info = curl_getinfo($tuCurl); 
          print("<PRE>"); print_r($info); print("</PRE>");
          //echo 'Took ' . $info['total_time'] . ' sec. to create dataset <i>'. $set["name"] .'</i> (' . $info['url'] .')'; 
        } else {
          echo 'Curl error: ' . curl_error($tuCurl); 
        }

        curl_close($tuCurl); 
    }

    
    function createGroup($group){
        $data = json_encode($group, JSON_PRETTY_PRINT | JSON_HEX_AMP); // | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
                
        $tuCurl = curl_init(); 
        curl_setopt($tuCurl, CURLOPT_URL, $this->url . "3/action/group_create"); 
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Authorization: ". $this->key)); 
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);

        $tuData = curl_exec($tuCurl); 
        if(!curl_errno($tuCurl)){ 
          $info = curl_getinfo($tuCurl); 
          print("<PRE>"); print_r($info); print("</PRE>");
          //echo 'Took ' . $info['total_time'] . ' sec. to create dataset <i>'. $set["name"] .'</i> (' . $info['url'] .')'; 
        } else {
          echo 'Curl error: ' . curl_error($tuCurl); 
        }

        curl_close($tuCurl); 
    }
    
    function datasetExists($name){
        $check = json_decode(file_get_contents($this->url ."search/dataset?name=". $name));
        if($check->count > 0){
            return true;
        } else {
            return false;
        }
    }

}
//CKAN API documentation: http://docs.ckan.org/en/latest/apiv3.html
?>