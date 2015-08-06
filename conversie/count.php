<?php
  $numbers = json_decode(file_get_contents("numbers.json"),true);
  
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Aantallen datasets</title>
<style>
    *{
        font-family: Arial;
        font-size: 14px;
    }
    body {
        margin: 0px;
        padding: 0px;
    }
    div.first{
        float: left;
        width: 130px;
        height: 44px;
        margin-right: 20px;
        padding-top: 5px;
    }
    
    div.count {
        background: #EBEBEB;
        float: left;
        width: 100px;
        height: 44px;
        padding: 5px 10px;
        text-align: center;
        margin-left: 20px;
    }
    span.head{
        font-size: 24px;
        font-weight: bold;
    }
    span.sub{
        font-size: 12px;
    }
    
</style>
</head>
<body>
<div class='first'>
Op dit moment in de datacatalogus: 
</div>
<div class='count'>
    <span class='head'>18</span>
    <br/><span class='sub'>Thema's</span>
</div>
<div class='count'>
    <span class='head'><?php echo $numbers["sets"]; ?></span>    
    <br/><span class='sub'>Onderwerpen</span>
</div>
<div class='count'>
    <span class='head'><?php echo $numbers["resources"]; ?></span>    
    <br/><span class='sub'>Datasets</span>
</div>
</body>
</html>
