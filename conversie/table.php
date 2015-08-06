<?php
  include("ckan.php");
  //error_reporting(0);
  
  $ckan = new CKAN();
  $sets = $ckan->getSearch($_REQUEST["q"]);
  if($_REQUEST["show"]){
      $show = $_REQUEST["show"];
  } else {
      $show = 5;
  }
  $count_sets = $sets->count;
  $count_resources = 0;
  $sets->results = array_reverse($sets->results);
  foreach($sets->results as $set){
      $count_resources += count($set->res_description);
  }
  
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>CKAN Widget - <?php echo $_REQUEST["q"]; ?></title>
<link rel="stylesheet" type="text/css" href="widget.css">
</head>
<body>
    <?php
    $count = 0;
        foreach($sets->results as $set){
            echo "<HR>";
            echo "<h1>". $set->title ."</h1>";
            echo "<P>". $set->notes ."</P>";
            echo "<br/><b>Metadata</b>";
            echo "<table>";
            echo "<tr><td>Titel</td><td>". $set->title ."</td></tr>";
            echo "<tr><td>Beschrijving</td><td>". $set->author ."</td></tr>";
            echo "<tr><td>Licentie</td><td>". $set->license_id ."</td></tr>";
            echo "<tr><td>Datum vrijgave</td><td>". date("d-m-Y", strtotime($set->publication_date)) ."</td></tr>";
            echo "<tr><td>Categorieën</td><td>". implode(", ",$set->groups) ."</td></tr>";
            echo "<tr><td>Tags</td><td>". implode(", ", $set->tags) ."</td></tr>";
            echo "<tr><td>Versie</td><td>". $set->version ."</td></tr>";
            echo "<tr><td>Tijdsperiode vanaf</td><td>". $set->extras->time_period_from ."</td></tr>";
            echo "<tr><td>Tijdsperiode tot</td><td>". $set->extras->time_period_to ."</td></tr>";
            echo "<tr><td>Tijdsperiode detailniveau</td><td>". $set->extras->time_period_detail_level ."</td></tr>";
            echo "<tr><td>Frequentie van update</td><td>". $set->extras->update_frequency ."</td></tr>";
            echo "<tr><td>Geografisch gebied</td><td>&nbsp;</td></tr>";
            echo "<tr><td>Geografisch detailniveau</td><td>&nbsp;</td></tr>";
            echo  "</table>";
            
            echo "<br/><b>Bestanden</b>";
            foreach($set->res_url as $key => $url){
                echo "<br/><table>";
                echo "<tr><td>Titel</td><td>". $set->res_description[$key] ."</td></tr>";
                echo "<tr><td>Toelichting</td><td>&nbsp;</td></tr>";
                echo "<tr><td>URL</td><td>". $url ."</td></tr>";
                echo "<tr><td>Bestandsformaat</td><td>". $set->res_format[$key] ."</td></tr>";
                echo "<tr><td>Taal</td><td>Nederlands</td></tr>";
                echo "</table>";
            }
            
            echo "<br/><b>Contactpersonen</b>";
            echo "<table>";
            
            echo "<tr><td>Eigenaar data</td><td>". $set->author ."</td></tr>";
            echo "<tr><td>Beheerder data</td><td>". $set->maintainer ."</td></tr>";
            echo "<tr><td>Naam contactpersoon</td><td>". $set->extras->contact_name ."</td></tr>";
            echo "<tr><td>Functie contactpersoon</td><td>&nbsp;</td></tr>";
            echo "<tr><td>Contact e-mail</td><td>". $set->extras->contact_email ."</td></tr>";
            echo "<tr><td>Contacttelefoon</td><td>&nbsp;</td></tr>";
            echo "<tr><td>Website (algemene info)</td><td>". $set->extras->website ."</td></tr>";
            echo "<tr><td>Website (specifieke info)</td><td>&nbsp;</td></tr>";
            echo "</table>";
            
            /*
            echo "<PRE>";
            print_r($set);
            echo "</PRE>";
            */
        }
    ?>
</body>
</html>
