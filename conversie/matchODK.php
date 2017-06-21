<?php
  include("ckan.php");

  function matchCKAN($ckan, $url){
      foreach($ckan->datasets as $key => $dataset){
          foreach($dataset->res_url as $res_url){
              if(stripos($res_url, $url) > 0){
                  return $dataset->title;
              }
          }
      }
      return "-";
  }

  $ckan = new CKAN("");
  $ckan->getDatasets();
  
  $odk = file_get_contents("https://kaart.amsterdam.nl/datasets");
  preg_match_all("/<li class=\"text\">\s*<a href=\"datasets\/datasets-item(.*)\">(.*)<\/a>\s*<\/li>/i", $odk, $matches, PREG_SET_ORDER);
?>

<table>
    <tr>
        <th>Dataset</th>
        <th>Link</th>
        <th>CKAN Match</th>
    </tr>

    <?php  
  foreach($matches as $match){
      print("<tr>");
      print("<td>". $match[2] ."</td>");
      print("<td><a href='https://kaart.amsterdam.nl/datasets/datasets-item". $match[1] ."' target='_blank'>". $match[1] ."</td>");
      print("<td>". matchCKAN($ckan, $match[1]) ."</td>");
      print("</tr>");
  }
?>
</table>


