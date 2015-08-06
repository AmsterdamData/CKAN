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
<div id='widget'>
    <div class='padding'>
        <div id='header'>
            <h2>Open Data <?php echo $_REQUEST["q"]; ?></h2>
            <div id='count'><?php echo $count_sets; ?> onderwerpen, <?php echo $count_resources; ?> datasets</div>
        </div>
        <div id='list'>
            <ul class='datasets'>
                <?php
                $count = 0;
                    foreach($sets->results as $set){
                        echo "<li><a href='http://www.amsterdamopendata.nl/web/guest/data?dataset=". $set->name ."' target='_blank'>". $set->title ."</a></li>";
                        $count++;
                        if($count >= $show) break;
                    }
                ?>
            </ul>
        </div>
    </div>
    <div class="crease_holder">
        <div class="crease_outer">
            <div class="crease">
                <div class="crease_inner haslink"><a href="http://www.amsterdamopendata.nl/data?searchvalue=<?php echo $_REQUEST["q"]; ?>" target='_blank'>Meer</a></div>
            </div>
        </div>
    </div>    
</div>
</body>
</html>
