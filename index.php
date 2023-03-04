
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_all_ftes($currentYear)
{
   $group="HPC-SYS";
   #$currentDate=date("Y-m-d");
   #$currentYear=date("Y");
   #$query="SELECT workpackage_name,staff_name,forcasted_amount,startdate,enddate FROM vw_fte_mapping where year(enddate)='$currentYear' ORDER BY workpackage_name ASC";
   $query="SELECT vw_fte_mapping.workpackage_name,vw_fte_mapping.staff_name,vw_staff_mapping.team_name,vw_staff_mapping.group_name,vw_fte_mapping.forcasted_amount,vw_fte_mapping.startdate,.vw_fte_mapping.enddate FROM vw_fte_mapping,vw_staff_mapping where year(vw_fte_mapping.enddate)='$currentYear' and vw_staff_mapping.group_name='$group' and vw_fte_mapping.staff_id=vw_staff_mapping.staff_id ORDER BY workpackage_name asc ,enddate desc";
   #echo $query;
   return $query;
}


#$colour0 = '#E3E3E3';
#$colour1 = '#FFFFFF';
$termcount=0;
$previousWP="";
$output_str="";
$currentDate=date("Y/m/d");
$currentYear=date("Y");
#$currentYear=date("Y");
if(isset($_GET['currentYear']))
{
     $currentYear=$_GET['currentYear'];
}
#
#$currentDate=strtotime($currentDate);
#$output_str="";
#$output_str.="<form id='yearform' method='post'>";
#$output_str="<table width = '900' style='border:1px solid black;'>\n";
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
#$output_str=drop_down_year($conn);
#$query=get_all_ftes($currentYear);
#$result=mysqli_query($conn,$query);
echo $output_str;
?>
<script>
function refreshPage(passValue){
//do something in this function with the value
 window.location="index.php?currentYear="+passValue
}
</script>


<?php

//echo "<section>";
//TITLE
echo "<br><strong>HPC</strong> Dashboard<br><br>";
//CONTENTS
#get groups
$query="SELECT group_name FROM tbl_groups";
$group_result=mysqli_query($conn,$query);
$output_str="<font size='1'>\n";
#$output_str.="<table class='style1' width='150' style='border:3px solid black;'>\n";
$output_str.="<table class='style1' style='border:3px solid black;'>\n";
$output_str.="<tr bgcolor ='#C1C1E8'>\n";
$td_max=2;
$td_count=0;
while($row=mysqli_fetch_array($group_result))
{
    if($td_count==$td_max)
    {
      $output_str.="</tr>";
      $output_str.="<tr>";
      $td_count=0;
    }

    $output_str.="<td valign='top'>";
    $content=over_or_under_staff($conn,$currentYear,$row[0]);
    $output_str.="<table><tr bgcolor ='black'><td><font color='white'>$row[0]</font></td></tr></table>";
    $output_str.=$content;
    $output_str.="</td>";
    $td_count++;
    
}
$output_str.="</tr>";
$output_str.="</table>\n";
$output_str.="</font>\n";
echo $output_str;
//echo "</section>";

require 'template/footer.html';
