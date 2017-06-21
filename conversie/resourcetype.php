<?php
  include("ckan.php");
  
  function max_length($txt, $max = 60){
      if(strlen($txt) > $max){
        return(substr($txt,0,$max - 2) . "..");
      } else {
          return $txt;
      }
  }
  
  //error_reporting(E_ALL);
?>
<html>
<head>
<title>AODS Resource Typesetter</title>
<style>
tr, td, th{
    text-align: left;
    padding-right: 10px;
}
</style>
</head>
<body>
<?php
    exit();
    $ckan = new CKAN();

    $json = json_decode(file_get_contents("resourcetype.json"));
    if(json_last_error()) print(json_last_error());
    $set = null;
    
    foreach($json as $row){
        if($set->name != $row->name){
            if($set){
                print("<BR>Save ". $set->name);
                $ckan->setSet($set);
            }
            print("<BR>Load ". $row->name);
            $set = $ckan->getSet($row->name);
        }
        $set->resources[$row->num]->type = $row->type;
    }
    ?>  
</body>
</html>

