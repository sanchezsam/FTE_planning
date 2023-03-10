<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/lib.php';
require 'template/header.html';

function get_project_task($program,$currentYear)
{

 $query="SELECT
           vw_wp_totals.project,
           vw_wp_totals.task,
           pct_fte,
           Funded_Percent,
           CONCAT('$',IFNULL(Service_Cost,0)) AS 'Service Cost',
           CONCAT('$',IFNULL(Hardware_Inventory,0)) AS 'Hardware Inventory',
           CONCAT('$',IFNULL(Hardware_Costs,0)) AS 'Hardware Costs',
           CONCAT('$',IFNULL(Staff_Costs,0)) AS 'Staff Costs',
           CONCAT('$',IFNULL(Allocated,0)) as 'Allocated'
           FROM
           vw_wp_totals,tbl_wp_info
           where
           vw_wp_totals.wp_id=tbl_wp_info.wp_id
           and YEAR(tbl_wp_info.enddate)='$currentYear'
           and program='$program'
           order by vw_wp_totals.project,vw_wp_totals.task;";

   $query="SELECT
           vw_wp_totals.project,
           vw_wp_totals.task,
           pct_fte,
           Funded_Percent,
           CONCAT('$',FORMAT(REPLACE(Service_Cost,',', ''),0)) AS 'Service Cost',
           CONCAT('$',FORMAT(REPLACE(Hardware_Inventory,',', ''),0)) AS 'Hardware Inventory',
           CONCAT('$',FORMAT(REPLACE(Hardware_Costs,',', ''),0)) AS 'Hardware Costs',
           CONCAT('$',FORMAT(REPLACE(Staff_Costs,',', ''),0)) AS 'Staff Costs',
           CONCAT('$',FORMAT(REPLACE(Allocated,',', ''),0)) AS 'Allocated'
           FROM
           vw_wp_totals,tbl_wp_info
           where
           vw_wp_totals.wp_id=tbl_wp_info.wp_id
           and YEAR(tbl_wp_info.enddate)='$currentYear'
           and program='$program'
           order by vw_wp_totals.project,vw_wp_totals.task;";
   #echo $query;
   return $query;
}

function get_project_totals($program,$project,$currentYear)
{
    $query="SELECT
            tbl_wp_info.project,
            '' as '-',
            SUM(pct_fte) as 'Total PCT FTE',
            SUM(Funded_Percent) as 'Total Funded Percent',
            CONCAT('$',FORMAT(SUM(REPLACE(Service_Cost,',', '')),0)) as 'Total Service Cost',
            CONCAT('$',FORMAT(SUM(REPLACE(Hardware_Inventory,',', '')),0)) as 'Total Hardware Inventory',
            CONCAT('$',FORMAT(SUM(REPLACE(Hardware_Costs,',', '')),0)) as 'Total Hardware Cost',
            CONCAT('$',FORMAT(SUM(REPLACE(Staff_Costs,',', '')),0)) as 'Total Staff Costs',
            CONCAT('$',FORMAT(SUM(REPLACE(Allocated,',', '')),0)) as 'Allocated'
            FROM
            vw_wp_totals,tbl_wp_info
            where
            vw_wp_totals.wp_id=tbl_wp_info.wp_id
            and YEAR(tbl_wp_info.enddate)='$currentYear'
            and program='$program'
            and tbl_wp_info.project='$project'
            group by tbl_wp_info.project
            order by tbl_wp_info.project;";
    return $query;
}

function get_program_totals($program,$currentYear)
{
    $query="SELECT '' as '--------',
                   '' as '----------------',
            SUM(pct_fte) as 'Total PCT FTE',
            SUM(Funded_Percent) as 'Total Funded Percent',
            CONCAT('$',FORMAT(SUM(REPLACE(Service_Cost,',', '')),0)) as 'Total Service Cost',
            CONCAT('$',FORMAT(SUM(REPLACE(Hardware_Inventory,',', '')),0)) as 'Total Hardware Inventory',
            CONCAT('$',FORMAT(SUM(REPLACE(Hardware_Costs,',', '')),0)) as 'Total Hardware Cost',
            CONCAT('$',FORMAT(SUM(REPLACE(Staff_Costs,',', '')),0)) as 'Total Staff Costs',
            CONCAT('$',FORMAT(SUM(REPLACE(Allocated,',', '')),0)) as 'Allocated'
            FROM
            vw_wp_totals,tbl_wp_info
            where
            vw_wp_totals.wp_id=tbl_wp_info.wp_id
            and YEAR(tbl_wp_info.enddate)='$currentYear'
            and program='$program';";
     return $query;


}

//TITLE
echo "<br><strong>Workpackage Managers</strong><br><br>";

if($verification=="True")
{
    if(isset($_SERVER['cn']))
    {
        $login_name=$_SERVER['cn'];
    }
    if(isset($_SERVER['REMOTE_USER']))
    {
        $login_name=$_SERVER['REMOTE_USER'];
    }

    $access_level=get_wp_program_access($conn,$login_name);
    if($access_level<4)
    {
      exit("Program level or admin access Required");
    }
}


//Get drop down menu (Year selector)
$currentYear=date("Y");
$currentDate=date("Y/m/d");
$currentYear=date("Y");
if(isset($_GET['currentYear']))
{
     $currentYear=$_GET['currentYear'];
}
$drop_down_str=drop_down_year_with_program($conn,'view_project_totals');
echo $drop_down_str;

$program="";
if(isset($_POST["submit"]))
{

     if(isset($_POST['program_name']))
     {
        $program= $_POST['program_name'];
     }
     if(isset($_POST['year']))
     {
          $currentYear=$_POST['year'][0];
     }

}


?>



<script>
function refreshPage(passValue,search){
//do something in this function with the value
 window.location="view_project_totals.php?currentYear="+passValue
}
</script>

<?php

   if(isset($_GET['currentYear']))
   {
       $currentYear=$_GET['currentYear'];
   }


if(isset($_POST["submit"]))
{

   #display workpackage info
   $query=get_project_task($program,$currentYear);
   $result=mysqli_query($conn,$query);

   $output_str="<table id='dataTable' style='border:1px solid black;'>\n";
   #list($column_str,$columns)=get_mysql_columns($result);
   #$output_str.=$column_str;
   #mysqli_data_seek($result,0);
   #$output_str.=get_mysql_values($result);
   $previousProject="";
   $header="$program $currentYear Report";
   $output_str.=display_table_header($header,9);
   $output_str.="<tr>\n";
   $output_str.="<td style='background-color:$column_color' valign='top' width='200'><b>Project</b></td>\n";
   $output_str.="<td style='background-color:$column_color' valign='top' width='200'><b>Task</b></td>\n";
   $output_str.="<td style='background-color:$column_color' valign='top'><b>PCT FTE</b></td>\n";
   $output_str.="<td style='background-color:$column_color' valign='top'><b>Funded Percent</b></td>\n";
   $output_str.="<td style='background-color:$column_color' valign='top'><b>Service Cost</b></td>\n";
   $output_str.="<td style='background-color:$column_color' valign='top'><b>Hardware Inventory</b></td>\n";
   $output_str.="<td style='background-color:$column_color' valign='top'><b>Hardware Costs</b></td>\n";
   $output_str.="<td style='background-color:$column_color' valign='top'><b>Staff Cost</b></td>\n";
   $output_str.="<td style='background-color:$column_color' valign='top'><b>FY$currentYear Allocated</b></td>\n";
   $output_str.="</tr>\n";
   $termcount=0;
   $total=0;
   while($row=mysqli_fetch_array($result))
   {
       $project=$row[0];
       $task=$row[1];
       $pct_fte=$row[2];
       $funded_percent=$row[3];
       $service_cost=$row[4];
       $hardware_inventory=$row[5];
       $hardware_cost=$row[6];
       $staff_cost=$row[7];
       $allocated=$row[8];
       if(($previousProject != $project))
       {
           

         if($total>0)
         {
           #$output_str.="<tr bgcolor='#b1fefe'>\n<td colspan='5' align='right'>WP Total :  $total</td></tr>\n";
           $query=get_project_totals($program,$previousProject,$currentYear);
           $total_result=mysqli_query($conn,$query);
           list($column_totals_str,$columns_totals)=get_mysql_columns($total_result);
           #$output_str.=$column_totals_str;
           mysqli_data_seek($total_result,0);
           $output_str.=get_mysql_totals_values($total_result,$columns_totals,$columns_totals);
           $total=0;
          }

           #This is the first display for this term, calculate the tr background color ??
           $currentColor= ${'colour' .($termcount % 2)};
           $output_str.="<tr bgcolor='$currentColor'>\n<td style='background-color:$currentColor' width=200 valign='top'>$project</td>\n";
           $termcount++;

           $previousProject = $project;
       }
       else
       {
          $output_str.="<tr bgcolor=$currentColor>";
          $output_str.= "<td style='background-color:$currentColor'>&nbsp;</td>\n";
       }
       $prev_color=$currentColor;
       $output_str.="<td style='background-color:$currentColor' valign='top'>$task</td>\n";
       $output_str.="<td style='background-color:$currentColor' valign='top'>$pct_fte</td>\n";
       $output_str.="<td style='background-color:$currentColor' valign='top'>$funded_percent</td>\n";
       $output_str.="<td style='background-color:$currentColor' valign='top'>$service_cost</td>\n";
       $output_str.="<td style='background-color:$currentColor' valign='top'>$hardware_inventory</td>\n";
       $output_str.="<td style='background-color:$currentColor' valign='top'>$hardware_cost</td>\n";
       $output_str.="<td style='background-color:$currentColor' valign='top'>$staff_cost</td>\n";
       $output_str.="<td style='background-color:$currentColor' valign='top'>$allocated</td>\n";
       $currentColor=$prev_color;
       $output_str.="</tr>\n";
       $total=1;
    }
    #$output_str.="<tr bgcolor='#b1fefe'>\n";
    #$output_str.= "<td style='background-color:$totals_color' >&nbsp;</td>\n";
    #$output_str.= "<td style='background-color:$totals_color' >&nbsp;</td>\n";
    #$output_str.= "<td style='background-color:$totals_color' >&nbsp;</td>\n";
    #$output_str.= "<td style='background-color:$totals_color' >&nbsp;</td>\n";
    #$output_str.="<td  style='background-color:$totals_color' align='right'>WP Total :  $total</td></tr>\n";
    #
    #$output_str.="<tr bgcolor='#b1fefe'>\n";
    #$output_str.= "<td style='background-color:$totals_color'>&nbsp;</td>\n";
    #$output_str.= "<td style='background-color:$totals_color'>&nbsp;</td>\n";
    #$output_str.= "<td style='background-color:$totals_color'>&nbsp;</td>\n";
    #$output_str.= "<td style='background-color:$totals_color'>&nbsp;</td>\n";
    #$output_str.="<td  style='background-color:$totals_color' align='right'>Team Total :  $grand_total</td></tr>\n";
    #$total=0;
    #$output_str.="</table>\n";
    
    

   #$query=get_program_totals($program,$currentYear);
   #$result=mysqli_query($conn,$query);
   #list($column_totals_str,$columns_totals)=get_mysql_columns($result);
   #$output_str.=$column_totals_str;
   #mysqli_data_seek($result,0);
   #$output_str.=get_mysql_totals_values($result,$columns_totals,$columns_totals);

   $query=get_program_totals($program,$currentYear);
   $result=mysqli_query($conn,$query);
   list($column_totals_str,$columns_totals)=get_mysql_columns($result);
   $output_str.=$column_totals_str;
   mysqli_data_seek($result,0);
   $output_str.=get_mysql_totals_values($result,$columns_totals,$columns_totals);
   $output_str.="</table>\n";
   echo $output_str;
}
require 'template/footer.html';
