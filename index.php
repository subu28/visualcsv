<?php
//check for any post variables. if there are any then check for csv file. explode the data by newline and then further by commas.
$okay = 0;
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
    
    $string2 = "values=[";
    for($i = 1;$i<sizeof($rows)-1;$i++){   //assumed that csv has newline at the end always                     //for  every row
        $string2.="[";
        $vals = explode(',',$rows[$i]);                         //the data is seperated
        for($j=0;$j<$cols;$j++){                                //for each seperate data
            $pos = array_search($vals[$j],$diction[$j]);        //check if value is in diction
            if ($pos === false){                                //if it is not there 
                $string2 .=sizeof($diction[$j]).",";
                $diction[$j][sizeof($diction[$j])]=$vals[$j];   //add to diction
            }
            else{
                $string2 .=$pos.",";                       // if its there, just note its position in diction
            }
        }
        $string2 = substr_replace($string2, '', -1);
        $string2.="],";
    }
    $string2 = substr_replace($string2, '', -1);
    $string2.="];";
    
    
    
    //read diction and throw out values.
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
                $okay = 1;
                $filename =$treat["name"];
                $handle = fopen($filename,"w");
                fwrite($handle,parser($contents));
                fclose($handle);
            }
        }
        else
        {
            echo "Invalid file";
        }
    }
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
        if($okay==1){
            $handle = fopen($filename, "r");
            $contents = fread($handle, filesize($filename));
            echo($contents );
        }
    ?>
    window.onload=function(){drawtable()};
</script>
</html>
