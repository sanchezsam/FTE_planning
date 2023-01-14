
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
   $query="SELECT vw_fte_mapping.workpackage_name,vw_fte_mapping.staff_name,vw_staff_mapping.team_name,vw_staff_mapping.group_name,vw_fte_mapping.forcasted_amount,vw_fte_mapping.startdate,.vw_fte_mapping.enddate FROM vw_fte_mapping,vw_staff_mapping where year(vw_fte_mapping.enddate)='$currentYear' and vw_staff_mapping.group_name='$group' and vw_fte_mapping.staff_name=vw_staff_mapping.staff_name ORDER BY workpackage_name asc ,enddate desc";
   #echo $query;
   return $query;
}


#$colour0 = '#E3E3E3';
#$colour1 = '#FFFFFF';
$termcount=0;
$previousWP="";
$currentDate=date("Y/m/d");
$currentYear=date("Y");
if(isset($_GET['currentYear']))
{
     $currentYear=$_GET['currentYear'];
}
$query=get_all_ftes($currentYear);
$result=mysqli_query($conn,$query);

$currentDate=strtotime($currentDate);
$output_str="";
$output_str.="<form id='yearform' method='post'>";
$output_str="<table width = '900' style='border:1px solid black;'>\n";
$output_str.="<tr bgcolor ='#C1C1E8'>\n";
$output_str.="<td valign='top'><b>View by Year</b></td>\n";
$query="SELECT year(enddate) FROM vw_fte_mapping group by year(enddate)";
$year_result=mysqli_query($conn,$query);
$output_str.="<td>";
$output_str.="<select  onchange='refreshPage(this.value);' name='year[]' id='year' data-size='4' required='required' onchange='change()'>";
$output_str.="<option value=''>Select</option>";
while($row=mysqli_fetch_array($year_result))
{
   if($currentYear==$row[0])
   {
       $output_str.="<option value=$row[0] selected='true'>$row[0]</option>";
   }
   else
   {
       $output_str.="<option value=$row[0]>$row[0]</option>";
   }
}
$output_str.="</select>";
$output_str.="</td>";
$output_str.="</tr>\n";
$output_str.="</table>\n";
$output_str.="</form>";
echo $output_str;
?>
<script>
function refreshPage(passValue){
//do something in this function with the value
 window.location="allftesSYS.php?currentYear="+passValue
}
</script>


<?php
$output_str="<table width = '900' style='border:1px solid black;'>\n";
$output_str.="<tr bgcolor ='#C1C1E8'>\n";
$output_str.="<td valign='top'><b>Workpackage Name</b></td>\n";
$output_str.="<td valign='top'><b>Staff Name</b></td>\n";
$output_str.="<td valign='top'><b>Forcasted Amount</b></td>\n";
$output_str.="<td valign='top'><b>Start Date</b></td>\n";
$output_str.="<td valign='top'><b>End Date</b></td>\n";
$output_str.="</tr>\n";

$total=0;
$grand_total=0;

while($row=mysqli_fetch_array($result))
{
   $workpackage=$row[0];
   $staff_name=$row[1];
   $forcasted=$row[4];
   $startdate=$row[5];
   $enddate=$row[6];
   $font_color="black";
   $endFYI="$currentYear-$endFYIDate";
   if(($previousWP != $workpackage))
   {

       if($total>0)
       {
           $output_str.="<tr bgcolor='#b1fefe'>\n<td colspan='5' align='right'>WP Total :  $total</td></tr>\n";
           $total=0;
       }
       #This is the first display for this term, calculate the tr background color ??
       $currentColor= ${'colour' .($termcount % 2)};
       #if($currentDate>strtotime($enddate))
       if(strtotime($enddate)<strtotime($endFYI))
       {
            #$currentColor="#787878";
            #$font_color="white";
              $currentColor=$old_color;
              $font_color=$change_font_color;
       }

       $output_str.="<tr bgcolor='$currentColor'>\n<td width=210 valign='top'><font color='$font_color'>$workpackage</font></td>\n";
       $termcount++;
       $previousWP = $workpackage;
   }
   else
   {
      //if($currentDate>strtotime($enddate) && (!isset($_GET['currentYear'])))
      #if($currentDate>=strtotime($enddate))
      $prev_color=$currentColor;
      if(strtotime($enddate)<strtotime($endFYI))
      {
              #$currentColor="#787878";
              #$font_color="white";
              $currentColor=$old_color;
              $font_color=$change_font_color;
      }
      //else
      //{
      //  $currentColor= ${'colour' .($termcount-1 % 2)};
     // }
      $output_str.="<tr bgcolor=$currentColor>";
      $output_str.= "<td>&nbsp;</td>\n";
    }
   $total+=$forcasted;
   $grand_total+=$forcasted;
   $output_str.="<td valign='top'><font color='$font_color'>$staff_name</font></td>\n";
   $output_str.="<td valign='top' align='right'><font color='$font_color'>$forcasted</font></td>\n";
   $output_str.="<td valign='top'><font color='$font_color'>$startdate</font></td>\n";
   $output_str.="<td valign='top'><font color='$font_color'>$enddate</font></td>\n";
   $output_str.="</tr>\n";
   $currentColor=$prev_color;
}
$output_str.="<tr bgcolor='#b1fefe'>\n<td colspan='5' align='right'>WP Total :  $total</td></tr>\n";
$output_str.="<tr bgcolor='#b1fefe'>\n<td colspan='5' align='right'>Group Total :  $grand_total</td></tr>\n";
$output_str.="</table>\n";


//echo "<section>";
//TITLE
echo "<br><strong>HPC</strong> All FTE's<br><br>";
//CONTENTS
echo $output_str;
//echo "</section>";

require 'template/footer.html';
