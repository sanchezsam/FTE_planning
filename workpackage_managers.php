<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_workpackage_managers($currentYear)
{
   $query="SELECT * FROM vw_workpackage_managers where YEAR(enddate)=$currentYear order by enddate desc ";
   return $query;
}


$currentYear=date("Y");

$colour0 = '#E3E3E3';
$colour1 = '#FFFFFF';
$termcount=0;
$previousName="";
$currentDate=date("Y/m/d");
$currentYear=date("Y");
if(isset($_GET['currentYear']))
{
     $currentYear=$_GET['currentYear'];
}

$query=get_workpackage_managers($currentYear);
$result=mysqli_query($conn,$query);



#$currentDate=strtotime($currentDate);
#$output_str.="<form id='yearform' method='post'>";
#$output_str="<table style='border:1px solid black;'>\n";
#$output_str.="<tr bgcolor ='#C1C1E8'>\n";
#$output_str.="<td valign='top'><b>View by Year</b></td>\n";
#$query="SELECT year(enddate) FROM vw_workpackage_managers group by year(enddate)";
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
function refreshPage(passValue,search){
//do something in this function with the value
 window.location="workpackage_managers.php?currentYear="+passValue
}
</script>

<?php













$query=get_workpackage_managers($currentYear);
$result=mysqli_query($conn,$query);

$colour0 = '#E3E3E3';
$colour1 = '#FFFFFF';
$font_color="black";
$termcount=0;
$previousWP="";
$currentDate=date("Y/m/d");
$currentDate=strtotime($currentDate);

$output_str="<table width = '900' hcolor='#FFFFCC' style='border:1px solid black;'>";
$output_str.="<tr bgcolor ='#C1C1E8'>";
$output_str.="<td valign='top'><b>Workpackage</b></td>";
$output_str.="<td valign='top'><b>Manager</b></td>";
$output_str.="<td valign='top'><b>Total Allowed FTEs</b></td>";
$output_str.="<td valign='top'><b>Start Date</b></td>";
$output_str.="<td valign='top'><b>End Date</b></td>";
$output_str.="</tr>";


while($row=mysqli_fetch_array($result))
{
   $manager_name=$row[0];
   $startdate=$row[1];
   $enddate=$row[2];
   $workpackage_name=$row[3];
   $forcasted_fte_total=$row[4];
   if(($previousWP != $workpackage_name))
   {
       #This is the first display for this term, calculate the tr background color ??
       $currentColor= ${'colour' .($termcount % 2)};
       #Make each row a link
       #if($currentDate>strtotime($enddate))
       #{
       #   $currentColor="#787878";
       #   $font_color="white";
       #}

       $output_str.="<tr bgcolor='$currentColor'><td width=210 valign='top'><font color='$font_color'>$workpackage_name</font></td>";
       $termcount++;
       $previousWP = $workpackage_name;
   }
   $output_str.="<td valign='top'><font color='$font_color'>$manager_name</font></td>\n";
   $output_str.="<td valign='top'><font color='$font_color'>$forcasted_fte_total</font></td>\n";
   $output_str.="<td valign='top'><font color='$font_color'>$startdate</font></td>\n";
   $output_str.="<td valign='top'><font color='$font_color'>$enddate</font></td>\n";
   $output_str.="</tr>";
}
$output_str.="</table>";


//TITLE
echo "<br><strong>Workpackage</strong> Managers<br><br>";
//CONTENTS
echo $output_str;

require 'template/footer.html';
