<?php
$okay = 0;
// function to parse and convert to transfer format
function parser($contents){
    $rows = preg_split( '/\r\n|\r|\n/', $contents );
    $axis = explode(',',$rows[0]);
    
    $string1 = "axis=[";
    foreach ($axis as $item){
        $string1.="'".$item."',";
    }
    $string1 = substr_replace($string1, '', -1);
    $string1.="];";

//get the headings and throw them out
    $cols=sizeof($axis);
    $diction = array($cols);
    for($i=0;$i<$cols;$i++){
        $diction[$i] = array();
    }

//build a dictionary and throw out the map
    $string2 = "values=[";
    for($i = 1;$i<sizeof($rows)-1;$i++){
        $string2.="[";
        $vals = explode(',',$rows[$i]);
        for($j=0;$j<$cols;$j++){
            $pos = array_search($vals[$j],$diction[$j]);
            if ($pos === false){
                $string2 .=sizeof($diction[$j]).",";
                $diction[$j][sizeof($diction[$j])]=$vals[$j];
            }
            else{
                $string2 .=$pos.",";
            }
        }
        $string2 = substr_replace($string2, '', -1);
        $string2.="],";
    }
    $string2 = substr_replace($string2, '', -1);
    $string2.="];";
    
//read dictionary created and throw out
    $string3= "diction=[";
    foreach($diction as $dict){
        $string3.="[";
        foreach($dict as $entry){
            $string3.="'".$entry."',";
        }
        $string3 = substr_replace($string3, '', -1);
        $string3.="],";
    }
    $string3 = substr_replace($string3, '', -1);
    $string3.= "];";
    
    return $string1.$string2.$string3;
}

//if a file is recieved then it is opened and sent for parsing
if(isset($_POST['typ'])) {
    foreach($_FILES as $treat){
        $temp = explode(".", $treat["name"]);
        if (($treat["size"] < 10000000) && end($temp)=="csv")
        {
            if ($treat["error"] > 0)
            {
                echo "Return Code: " . $treat["error"] . "<br>";
            }
            else
            {
                $filename = $treat["tmp_name"];
                $handle = fopen($filename, "r");
                $contents = fread($handle, filesize($filename));
                fclose($handle);
                $okay = 1;
                $filename =$treat["name"];
                $handle = fopen($filename,"w");
                fwrite($handle,parser($contents));
                fclose($handle);
            }
        }
        else
        {
            echo "Invalid file or Tooooo large";
        }
    }
}

//if get variables arrive then the cummulitive results are shown
elseif(isset($_GET['source'])){
    $filename=$_GET['source'];
    $okay=2;
    $gets=array();
    foreach($_GET as $get)
    $gets[sizeof($gets)]=$get;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>VisualCSV</title>
    <style type="text/css">
        table, th, td{
            border-collapse:collapse;
            border:1px solid black;
        }
        th, td{
            padding:5px;
        }
    </style>
    <script src="d3.v3.min.js" charset="utf-8"></script>
    <script src="visualcsv.js" type="text/javascript"></script>
</head>
<body>
    <h1>VisualCSV</h1>
    <div id="results"></div>
    <form action="./" method="post" enctype="multipart/form-data">
        <label for="file">Choose a csv file to upload:</label>
        <input type="file" name="file" id="file"><br>
        <input type="submit" name="submit" value="Submit">
        <input type="hidden" name="typ" value="upload">
    </form>
</body>
<script type="text/javascript">
    <?php
        if($okay>0){
            $handle = fopen($filename, "r");
            $contents = fread($handle, filesize($filename));
            echo($contents );
            fclose($handle);
            echo "source = '".$filename."';";
            if($okay==1){
                echo "window.onload=function(){drawtable();showconsole();};";
            }
            else{
                $string1="charts=[";
                for($i=1;$i<sizeof($gets);$i++){
                    $string1.= "[".$gets[$i]."],";
                }
                $string1 = substr_replace($string1, '', -1);
                $string1.="];";
                echo $string1;
                echo "window.onload=function(){drawtable();showcharts();};";
            }
        }
    ?>
</script>
</html>
