<?php
error_reporting(E_ALL ^E_NOTICE);
set_time_limit(1800);
ini_set('memory_limit','256M');

include 'classes/simplexlsx.class.php';
include("ckan.php");
  
$ckan = new CKAN();

?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <title>Import</title>
</script>
</head>
<body>
<div id='main'>
    <div id='content'>

    <?php
class Importer{
    var $db;
    var $import;
    var $xlsx;
    var $columns;
    
    function Importer(){
        $this->checkUploads();
    }
    
    function checkUploads(){
        if (isset($_FILES['file'])) {
            $url = $_FILES['file']['tmp_name'];
            //$fname = substr($url, max(strrpos($url, "\\"),strrpos($url, "/")) + 1);
            
            list($name, $ext) = explode(".", $_FILES['file']['name']);
            $fname = $name . ".". $ext;
            $i = 0;
            while(file_exists("cache/". $fname)){
                $i++;
                $fname = $name . "(". $i .")".".".$ext;
            }
            
            move_uploaded_file($url, "cache/". $fname);
        }
    }
    

    function showStep1(){
        $this->xlsx = new SimpleXLSX($this->import->url);
        ?><h1>Importeren</h1>
        <form method="GET" action="import.php">
            <input type="hidden" name="id" value="<?php echo($this->import->id); ?>">
            <input type="hidden" name="step" value="1">
            <input type="hidden" name="process" value="true">
            <table>
                <tr>
                    <td>Data collection to add data to</td>
                    <td><select name='collection'>
                            <option value='buurt'>Buurten van Nederland</option>
                            <option value='gemeente'>Gemeentes van Nederland</option>
                            <option value='sec'>Financial Genes</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Worksheet</td><td><select name='worksheet'>
                        <?php
                            foreach($this->xlsx->sheetNames() as $num => $sheetname){
                                print("<OPTION value='". $num ."'>". $sheetname ."</OPTION>");
                            }
                        ?>
                    </select></td>
                </tr>
                <tr>
                    <td>Source</td><td><input type='text' name='source' size='40'></td>
                </tr>
                <tr>
                    <td>Year</td><td><input type='text' name='year' size='40' value='<?php echo(date("Y")); ?>'></td>
                </tr>
                <tr>
                    <td>Quarter</td><td><input type='text' name='quarter' size='40' value='0'></td>
                </tr>
                <tr>
                    <td>Date</td><td><input type='text' name='date' size='40' value='<?php echo(date("Y-m-d")); ?>'></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type='submit' value='Verder &gt;&gt;'></td>
                </tr>
            </table>
        </form>
        <?php
    }

    function processStep1(){
        $this->db->doUpdate("imports", Array("collection" => $_REQUEST["collection"],"worksheet" => $_REQUEST["worksheet"], "source" => $_REQUEST["source"], "year" => $_REQUEST["year"], "quarter" => $_REQUEST["quarter"], "date" => $_REQUEST["date"], "step" => 1), Array("id" => $this->import->id));
        $this->import = $this->db->returnFirst("SELECT * FROM imports WHERE id = ". $_REQUEST["id"]);
    }
    
    function showStep2(){
        $this->xlsx = new SimpleXLSX($this->import->url);
        $rows = $this->xlsx->rows($this->import->worksheet);
        $columns = $rows[0];
        
        $measure_rows = $this->db->getArray("SELECT * FROM ". safeSQL($this->import->collection) ."_measures");
        $measures = Array();
        foreach($measure_rows as $measure_row){
            $measures[$measure_row->id] = $measure_row;
        }
        
        ?><h1>Importeren</h1>
        <form method="POST" action="import.php">
            <input type="hidden" name="id" value="<?php echo($this->import->id); ?>">
            <input type="hidden" name="step" value="2">
            <input type="hidden" name="process" value="true">
            <table>
                <tr>
                    <td>Column containing ID</td><td><select name='column_id'>
                        <?php
                            foreach($columns as $num => $header){
                                print("<OPTION value='". $num ."'>". $header ."</OPTION>");
                            }
                        ?>
                    </select></td>
                </tr>
                <tr>
                    <td>Column containing title (only for new items)</td><td><select name='title_id'>
                        <?php
                            foreach($columns as $num => $header){
                                print("<OPTION value='". $num ."'>". $header ."</OPTION>");
                            }
                        ?>
                    </select></td>
                </tr>
                <?php
                    foreach($columns as $num => $header){
                        print("<TR><TD colspan=2><HR>Column <strong>" . $header ."</strong></TD></TR>");
                        if(array_key_exists($header, $measures) && $measures[$header]->type == "value"){ //ignore selectors
                            $display='block';
                            print("<TR><TD colspan=2>Known measure found: ". $measures[$header]->title ." (". $measures[$header]->category .")</TD></TR>");
                            print("<TR><TD colspan=2><input type='checkbox' name='column". $num ."' CHECKED onClick='$(\".col". $num ."\").toggle();'>Add data to dataset</TD></TR>");
                        } else {
                            $display ='none';
                            print("<TR><TD colspan=2><input type='checkbox' name='measure". $num ."' onClick='$(\".meas". $num ."\").toggle();'>Add measure</TD></TR>");
                            print("<TR class='meas". $num ."' style='display: none'><TD>Name</TD><TD><input type='text' name='name". $num ."' value='". $header ."'></TD></TR>");
                            print("<TR class='meas". $num ."' style='display: none'><TD>Source</TD><TD><input type='text' name='source". $num ."' value='". $this->import->source ."'></TD></TR>");
                            print("<TR><TD colspan=2><input type='checkbox' name='column". $num ."' onClick='$(\".col". $num ."\").toggle();'>Add data to dataset</TD></TR>");
                        }
                        print("<TR class='col". $num ."' style='display: ". $display ."'><TD>ID Measure</TD><TD><input type='text' name='id". $num ."' value='". $header ."'></TD></TR>");
                        print("<TR class='col". $num ."' style='display: ". $display ."'><TD>Year</TD><TD><input type='text' name='year". $num ."' value='". $this->import->year ."'></TD></TR>");
                        print("<TR class='col". $num ."' style='display: ". $display ."'><TD>Quarter</TD><TD><input type='text' name='quarter". $num ."' value='". $this->import->quarter ."'></TD></TR>");
                        print("<TR class='col". $num ."' style='display: ". $display ."'><TD>Date</TD><TD><input type='text' name='date". $num ."' value='". $this->import->date ."'></TD></TR>");
                        print("<TR class='col". $num ."' style='display: ". $display ."'><TD colspan=2><input type='radio' name='new". $num ."' value='ignore' CHECKED>Ignore new items<br/><input type='radio' name='new". $num ."' value='add'>Add new items</TD></TR>");
                    }
                ?>
            </table>
            <input type='submit' value="Next &gt;&gt;">
        </form>
        <?php
    }
    
    function processStep2(){
        $this->xlsx = new SimpleXLSX($this->import->url);
        $rows = $this->xlsx->rows($this->import->worksheet);
        $columns = $rows[0];
        
        $item_rows = $this->db->getArray("SELECT id FROM ". safeSQL($this->import->collection) ."_items");
        $items = Array();
        foreach($item_rows as $item_row){
            $items[] = $item_row->id;
        }
        
        foreach($columns as $num => $header){
            if($_REQUEST["measure". $num]){
                //TODO: Add Measure
                $this->db->doInsert(safeSQL($this->import->collection) ."_measures", Array("id" => $_REQUEST["id". $num], "title" =>  $_REQUEST["name". $num], "source" => $_REQUEST["source". $num], "type" => "value"));
                print("Added Measure: ". $_REQUEST["id". $num] ."</BR>");
            }
        }
        
        $values_table = safeSQL($this->import->collection) ."_values";
        
        foreach($rows as $count => $row){
            if($count == 0) continue; //Skip header row
            
            //Check if item exists
            $id = $row[$_REQUEST["column_id"]];
            if(in_array($id, $items)){
                $new_item = false;
            } else {
                $new_item = true;
            }
            
            foreach($row as $num => $cell){
                if($new_item){
                    if($_REQUEST["new". $num] == "ignore"){
                        continue;
                    } else {
                        //TODO: Add item
                        print("Add item: ". $id ."</BR>");
                        $new_item = false;
                    }
                }

                if($_REQUEST["column". $num]){
                    $value = str_replace(",",".",$cell);
                    if(is_numeric($value)){
                        $float = floatval($value);
                        
                        
                        $array = Array("measure" => $_REQUEST["id". $num], "id" => $id, "year" => $_REQUEST["year". $num], "quarter" => $_REQUEST["quarter". $num], "date" => $_REQUEST["date". $num], "year" => $_REQUEST["year". $num], "value" => $float);
                        //print_r($array);
                        $this->db->doUpsert($values_table, $array);
                        //print("<BR>");
                    }
                }
            }
            print("Completed row ". $count ." (id = ". $id .")<BR>");
        }
        $this->db->doUpdate("imports", Array("step" => 2), Array("id" => $this->import->id));
        $this->import = $this->db->returnFirst("SELECT * FROM imports WHERE id = ". $_REQUEST["id"]);
    }
    
    function showStep3(){
        print "OK!";
    }
    
    function getRowValue($name, $row){
        if(in_array($name, $this->columns)){
            return $row[array_search($name, $this->columns)];
        } else {
            return null;
        }
    }
    
    function import($url){
        global $ckan;
        
        $url = "cache/" . urldecode($_REQUEST["url"]);
        $this->xlsx = new SimpleXLSX($url);
        $rows = $this->xlsx->rows(1);
        $this->columns = $rows[0];
        $rows = array_slice($rows,1);
        
        $sets = Array();
        $set = Array();
        
        foreach($rows as $row){
            $name = $row[0];
            if(trim($name) == ""){
                if($set["name"]){
                    //Nieuwe resource toevoegen aan set.
                    $set["resources"][] = 
                        Array(
                            "url" => $this->getRowValue("resource_url", $row),
                            "name" => $this->getRowValue("resource_name", $row),
                            "description" => $this->getRowValue("resource_description", $row),
                            "resource_type" => "file"
                        );
                }
            } else {
                //Nieuwe dataset
                if($set["name"]){
                    //Voeg vorige set toe aan sets, en maak nieuwe set
                    $sets[] = $set;
                    $set = Array();
                }
                
                //Check of name al bestaat
                if(!$ckan->datasetExists($name)){
                  $set = Array(
                    "name" => $this->getRowValue("name", $row),
                    "title" => $this->getRowValue("title", $row),
                    "author" => $this->getRowValue("author", $row),
                    "author_email" => $this->getRowValue("author_email", $row),
                    "maintainer" => $this->getRowValue("maintainer", $row),
                    "maintainer_email" => $this->getRowValue("maintainer_email", $row),
                    "license_id" => $this->getRowValue("license_id", $row),
                    "notes" => $this->getRowValue("notes", $row),
                    "resources" => Array(
                        Array(
                            "url" => $this->getRowValue("resource_url", $row),
                            "name" => $this->getRowValue("resource_name", $row),
                            "description" => $this->getRowValue("resource_description", $row)
                        )
                    ),
                    "extras" => Array(
                        Array(
                            "key" => "contact_name",
                            "value" => $this->getRowValue("contact_name", $row)
                        ),
                        Array(
                            "key" => "contact_email",
                            "value" => $this->getRowValue("contact_email", $row)
                        ),
                        Array(
                            "key" => "website",
                            "value" => $this->getRowValue("contact_website", $row)
                        ),
                        Array(
                            "key" => "publication_date",
                            "value" => $this->getRowValue("publication_date", $row)
                        ),
                        Array(
                            "key" => "time_period_detail_level",
                            "value" => $this->getRowValue("time_period_detail_level", $row)
                        ),
                        Array(
                            "key" => "time_period_from",
                            "value" => $this->getRowValue("time_period_from", $row)
                        ),
                        Array(
                            "key" => "time_period_to",
                            "value" => $this->getRowValue("time_period_to", $row)
                        ),
                        Array(
                            "key" => "update_frequency",
                            "value" => $this->getRowValue("update_frequency", $row)
                        )
                      ),
                    "groups" => Array(
                        Array(
                            "name" => $this->getRowValue("group", $row)
                        )
                    ),
                    "tags" => Array()
                    );
                    
                    foreach(explode(",",$this->getRowValue("tags", $row)) as $tag){
                        $set["tags"][] = Array("name" => $tag);
                    }
                } else {
                    echo "<BR/>Er bestaat al een dataset met de naam '". $name ."'. Deze dataset is niet toegevoegd.<BR/>";
                }
            }
        }
        if($set["name"]){
            $sets[] = $set;
            //Add last set to sets;
        }

        
        foreach($sets as $set){
            $ckan->createDataset($set);
        }
    }
    
    function show(){

        if(trim($_REQUEST["aktie"]) <> ""){
            switch($_REQUEST["aktie"]){
                case "delete":
                    $url = "cache/" . urldecode($_REQUEST["url"]);
                    unlink($url);
                    break;
            }
        }
        
        if(trim($_REQUEST["url"]) <> "" && $_REQUEST["step"] > 0){
            switch($_REQUEST["step"]){
                case 1:
                default:
                    $this->import($_REQUEST["url"]);
                    break;
            }
        } else {
            ?>
            <h1>Upgeloade bestanden</h1>
            <table border="1" cellpadding="3" style="border-collapse: collapse">
            <tr><th>URL</th><th></th></tr>
            <?php
                if ($handle = opendir('cache/')) {
                    while (false !== ($entry = readdir($handle))) {
                        if ($entry != "." && $entry != "..") {
                            print("<TR>");
                            print("<TD><a href='import.php?url=". $entry ."'</a>". $entry ."</TD>");
                            print("<TD><a href='import.php?url=". $entry ."&step=1'>Importeren</a> - <a href='import.php?url=". $entry ."&aktie=delete'>Verwijderen</a></TD>");
                            print("</TR>");
                        }
                    }
                    closedir($handle);
                }
            ?>
            </table>
            <h1>Nieuw bestand</h1>
            <form method="post" enctype="multipart/form-data" action="import.php">
            *.XLSX <input type="file" name="file"  /><br/><input type="submit" value="Toevoegen" />
            </form>
            <?php
        }
    }
}

$importer = new Importer();
$importer->show();

?>
</div>
</div>
</body>
</html>