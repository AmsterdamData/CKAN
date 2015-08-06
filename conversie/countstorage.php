<?php
  include("ckan.php");
      
  $ckan = new CKAN();
  $sets = $ckan->getDatasets();
  
  function getFileSize($url){
    // Create a curl handle
    $ch = curl_init($url);
    $fh = fopen('/dev/null', 'w');
    curl_setopt($ch, CURLOPT_FILE, $fh);
    // Execute
    curl_exec($ch);

    // Check if any error occured
    if(!curl_errno($ch))
    {
    $info = curl_getinfo($ch);
    echo 'Size: ' . $info['download_content_length'];
    }
    // Close handle
    curl_close($ch);      
  }
  
  function remote_filesize($url) {
    $url = str_replace(" ","%20",$url);
    static $regex = '/^Content-Length: *+\K\d++$/im';
    if (!$fp = @fopen($url, 'rb')) {
        return false;
    }
    if (
        isset($http_response_header) &&
        preg_match($regex, implode("\n", $http_response_header), $matches)
    ) {
        return (int)$matches[0];
    }
    return strlen(stream_get_contents($fp));
  }
  
  
  $result = "<table><tr><th>Set</th><th>Name</th><th>Url</th><th>File size</th><tr>";
  foreach($ckan->datasets as $set){
      foreach($set->res_url as $resource){
          $check_against1 = strtolower("http://www.amsterdamopendata.nl/files/");
          $check_against2 = strtolower("http://www.amsterdamopendata.nl/documents/");
          if(substr(strtolower($resource),0,strlen($check_against1)) == $check_against1 || substr(strtolower($resource),0,strlen($check_against2)) == $check_against2){
            //print_r(stat($resource));
            //$fsize = filesize($resource);
            //getFileSize($resource);
            $fsize = remote_filesize($resource);
            $result .= "<tr><td>". $set->title ."</td><td>". $set->name ."</td><td>". $resource ."</td><td>". $fsize ."<td></tr>\n\r";
          }
      }
  }
  $result .= "</table>";
  print $result;
?>