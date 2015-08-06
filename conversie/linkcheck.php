<?php
  include("ckan.php");
  
  function is_available($url, $timeout = 30) {
    $ch = curl_init(); // get cURL handle

    // set cURL options
    $opts = array(CURLOPT_RETURNTRANSFER => true, // do not output to browser
                  CURLOPT_URL => $url,            // set URL
                  CURLOPT_NOBODY => true,           // do a HEAD request only
                  CURLOPT_TIMEOUT => $timeout);   // set timeout
    curl_setopt_array($ch, $opts); 

    curl_exec($ch); // do it!

    $code =  curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $is_available = $code == 200; // check if HTTP OK
    
    curl_close($ch); // close handle
    
    
    return $code; 
    // return $retval;
  }
  
  function max_length($txt, $max = 60){
      if(strlen($txt) > $max){
        return(substr($txt,0,$max - 2) . "..");
      } else {
          return $txt;
      }
  }
  
  //error_reporting(0);
?>
<html>
<head>
<title>AODS Link-checker</title>
<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
</head>
<body>
<h1>URLS met een andere response-code dan 200</h1>
<p>Let op: indien niet lokaal uitgevoerd, kunnen bestanden op amsterdamopendata.nl onterecht als 404 gezien worden.</p>
    <table>
        <thead>
            <tr><th>status</th><th>url</th><th>dataset</th><th>beschijving url</th></tr>
        </thead>
        <tbody>
    <?php
      
      $count = 0;
      $ckan = new CKAN();
      $ckan->getDatasets();
      foreach($ckan->datasets as $key => $set){
          foreach($set->res_url as $i => $url){
              $count++;
              $code = is_available($url);
              if($code != 200){
                print("<TR><TD><a onClick='$(\".row\").hide(); $(\"#row". $count ."\").show();'>&gt;</a></TD><TD>". $code ."</TD><TD><a href='". $url ."' target='_blank'>". max_length($url,60) . "</a></TD><TD>". $set->name ."</TD><TD>". $set->res_description[$i] . "</TR>");
                print("<TR id='row". $count ."' class='row' style='display:none'><TD colspan = 5>");
                print("<form onSubmit='$.post(\"setresource.php\", {name: \"". $key ."\", res: ". $i .", url: this.url". $count .".value, desc: this.desc". $count .".value}, function(data){
                 $(\"#feedback". $count ."\").html(data);});return false; '>");
                 print("URL: <input type='text' id='url". $count ."' value='". $url ."' SIZE=80><BR/>");
                 print("DESC: <input type='text' id='desc". $count ."' value='".  addslashes($set->res_description[$i]) ."' SIZE=80><BR/>");
                 print("<input type='submit' value='Change'><br/>");
                 print("</form>");
                 print("<div id='feedback". $count ."'></div>");
                print("</TD></TR>");
              }
          }
      }  
    ?>  

        </tbody>
    </table>
</body>
</html>

