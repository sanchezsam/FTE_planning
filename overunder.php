
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';



#$colour0 = '#E3E3E3';
#$colour1 = '#FFFFFF';
$termcount=0;
$previousWP="";
$currentDate=date("Y/m/d");
#$currentYear=date("Y");
#if(isset($_GET['currentYear']))
#{
#     $currentYear=$_GET['currentYear'];
#}
#
#$currentDate=strtotime($currentDate);
#$output_str="";
#$output_str.="<form id='yearform' method='post'>";
#$output_str="<table width = '432' style='border:1px solid black;'>\n";
#$output_str.="<tr bgcolor ='#C1C1E8'>\n";
#$output_str.="<td valign='top'><b>View by Year</b></td>\n";
#$query="SELECT year(enddate) FROM vw_fte_mapping group by year(enddate)";
#$year_result=mysqli_query($conn,$query);
#$output_str.="<td>";
#$output_str.="<select  onchange='refreshPage(this.value);' name='year[]' id='year' data-size='4' required='required' onchange='change()'>";
#$output_str.="<option value=''>Select</option>";
#while($row=mysqli_fetch_array($year_result))
#{
#   if($currentYear==$row[0])
#   {
#       $output_str.="<option value=$row[0] selected='true'>$row[0]</option>";
#   }
#   else
#   {
#       $output_str.="<option value=$row[0]>$row[0]</option>";
#   }
#}
#$output_str.="</select>";
#$output_str.="</td>";
#$output_str.="</tr>\n";
#$output_str.="</table>\n";
#$output_str.="</form>";
$output_str=drop_down_year($conn);
echo $output_str;
?>
<script>
function refreshPage(passValue){
//do something in this function with the value
 window.location="overunder.php?currentYear="+passValue
}
</script>


<?php


$output_str=over_or_under($conn,$currentYear);

#echo "<section>";
//TITLE
#echo "<br><strong>HPC</strong> All FTE's<br><br>";
//CONTENTS
echo $output_str;
#echo "</section>";

require 'template/footer.html';
