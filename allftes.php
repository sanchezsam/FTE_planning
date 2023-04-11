
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/lib.php';
require 'template/header.html';

function get_all_ftes($currentYear,$group)
{
   #$query="SELECT vw_fte_mapping.workpackage_name,vw_fte_mapping.staff_name,vw_staff_mapping.team_name,vw_staff_mapping.group_name,vw_fte_mapping.forcasted_amount,vw_fte_mapping.startdate,.vw_fte_mapping.enddate FROM vw_fte_mapping,vw_staff_mapping where year(vw_fte_mapping.enddate)='$currentYear' and vw_staff_mapping.group_name='$group' and vw_fte_mapping.staff_id=vw_staff_mapping.staff_id ORDER BY workpackage_name asc ,enddate desc";
   $query="SELECT CONCAT(project,' ',task) as workpackage,
                     tbl_wp_staff.name,
                     funded_percent,
                     tbl_wp_info.startdate,
                     tbl_wp_info.enddate
              FROM `tbl_wp_staff`,tbl_wp_info,tbl_staff_info 
              where tbl_wp_staff.wp_id=tbl_wp_info.wp_id 
              and tbl_staff_info.znumber=tbl_wp_staff.znumber 
              and tbl_staff_info.group_name ='$group' 
              and YEAR(tbl_wp_staff.enddate)=$currentYear
              and YEAR(tbl_staff_info.enddate)=$currentYear
              group by workpackage, tbl_wp_staff.name
              order by project,task,name;";
echo $query;
   return $query;
}


$termcount=0;
$previousWP="";
$currentDate=date("Y/m/d");
$currentYear=date("Y");
$group="";

if(isset($_POST["submit"]))
{

     if(isset($_POST['group_name']))
     {
        $group= $_POST['group_name'];
     }
     if(isset($_POST['year']))
     {
          $currentYear=$_POST['year'][0];
     }

}

$where="currentYear=$currentYear";
$page="allftes";
$output_str=drop_down_year_with_group($conn,$page,$where);
$query=get_all_ftes($currentYear,$group);
$result=mysqli_query($conn,$query);
echo $output_str;
?>
<script>
function refreshPage(passValue){
//do something in this function with the value
   window.location="allftes.php?currentYear="+passValue
}
</script>


<?php
$output_str="<table id='dataTable' width = '900' style='border:1px solid black;'>\n";
$header="$group $currentYear FTE's";
$output_str.=display_table_header($header,5);
$output_str.="<tr bgcolor ='#C1C1E8'>\n";
$output_str.="<td style='background-color:$column_color' valign='top'><b>Workpackage Name</b></td>\n";
$output_str.="<td style='background-color:$column_color' valign='top'><b>Staff Name</b></td>\n";
$output_str.="<td style='background-color:$column_color' valign='top'><b>Forcasted Amount</b></td>\n";
$output_str.="<td style='background-color:$column_color' valign='top'><b>Start Date</b></td>\n";
$output_str.="<td style='background-color:$column_color' valign='top'><b>End Date</b></td>\n";
$output_str.="</tr>\n";

$total=0;
$grand_total=0;
$prev_color="";
while($row=mysqli_fetch_array($result))
{
   $workpackage=$row[0];
   $staff_name=$row[1];
   $forcasted=$row[2];
   $startdate=$row[3];
   $enddate=$row[4];
   $font_color="black";
   $endFYI="$currentYear-$endFYIDate";
   if(($previousWP != $workpackage))
   {

       if($total>0)
       {
           #$output_str.="<tr bgcolor='#b1fefe'>\n<td colspan='5' align='right'>WP Total :  $total</td></tr>\n";
           #$total=0;
           $output_str.="<tr bgcolor='#b1fefe'>\n";
           $output_str.= "<td style='background-color:$totals_color'>&nbsp;</td>\n";
           $output_str.= "<td style='background-color:$totals_color'>&nbsp;</td>\n";
           $output_str.= "<td style='background-color:$totals_color'>&nbsp;</td>\n";
           $output_str.= "<td style='background-color:$totals_color'>&nbsp;</td>\n";
           $output_str.="<td style='background-color:$totals_color'>WP Total :  $total</td></tr>\n";
           $total=0;
       }
       #This is the first display for this term, calculate the tr background color ??
       $currentColor= ${'colour' .($termcount % 2)};
       #if(strtotime($enddate)<strtotime($endFYI))
       #{
       #       $currentColor=$old_color;
       #       $font_color=$change_font_color;
       #}

       $output_str.="<tr bgcolor='$currentColor'>\n<td style='background-color:$currentColor' width=210 valign='top'><font color='$font_color'>$workpackage</font></td>\n";
       $termcount++;
       $previousWP = $workpackage;
   }
   else
   {
      $output_str.="<tr bgcolor=$currentColor>";
      $output_str.= "<td style='background-color:$currentColor'>&nbsp;</td>\n";
    }

   $total+=$forcasted;
   $grand_total+=$forcasted;
   $prev_color=$currentColor;
   if(strtotime($enddate)<strtotime($endFYI))
   {
          $currentColor=$old_color;
          $font_color=$change_font_color;
   }
   $output_str.="<td style='background-color:$currentColor' valign='top'><font color='$font_color'>$staff_name</font></td>\n";
   $output_str.="<td style='background-color:$currentColor' valign='top' align='right'><font color='$font_color'>$forcasted</font></td>\n";
   $output_str.="<td style='background-color:$currentColor' valign='top'><font color='$font_color'>$startdate</font></td>\n";
   $output_str.="<td style='background-color:$currentColor' valign='top'><font color='$font_color'>$enddate</font></td>\n";
   $output_str.="</tr>\n";
   $currentColor=$prev_color;
}

$output_str.="<tr bgcolor='#b1fefe'>\n";
$output_str.= "<td style='background-color:$totals_color' >&nbsp;</td>\n";
$output_str.= "<td style='background-color:$totals_color' >&nbsp;</td>\n";
$output_str.= "<td style='background-color:$totals_color' >&nbsp;</td>\n";
$output_str.= "<td style='background-color:$totals_color' >&nbsp;</td>\n";
$output_str.="<td style='background-color:$totals_color' >WP Total :  $total</td></tr>\n";

$output_str.="<tr bgcolor='#b1fefe'>\n";
$output_str.= "<td style='background-color:$totals_color'>&nbsp;</td>\n";
$output_str.= "<td style='background-color:$totals_color'>&nbsp;</td>\n";
$output_str.= "<td style='background-color:$totals_color'>&nbsp;</td>\n";
$output_str.= "<td style='background-color:$totals_color'>&nbsp;</td>\n";
$output_str.="<td  style='background-color:$totals_color'>Team Total :  $grand_total</td></tr>\n";
$output_str.="</table>\n";
$total=0;

echo $output_str;
?>



<?php

require 'template/footer.html';
