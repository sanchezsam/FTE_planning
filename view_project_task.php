<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_project_task($program,$currentYear)
{
   $query="SELECT
           vw_wp_totals.project,
           vw_wp_totals.task,
           pct_fte,
           Funded_Percent,
           CONCAT('$',Service_Cost) AS 'Service Cost',
           CONCAT('$',Hardware_Inventory) AS 'Hardware Inventory',
           CONCAT('$',Hardware_Costs) AS 'Hardware Costs',
           CONCAT('$',Staff_Costs) AS 'Staff Costs',
           CONCAT('$',Allocated) as 'Allocated'
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

function get_program_totals($program,$currentYear)
{
    $query="SELECT '' as '--------',
                   '' as '----------------',
            SUM(pct_fte) as 'Total PCT FTE',
            SUM(Funded_Percent) as 'Total Funded Percent',
            CONCAT('$',FORMAT(SUM(REPLACE(Service_Cost,',', '')),2)) as 'Total Service Cost',
            CONCAT('$',FORMAT(SUM(REPLACE(Hardware_Inventory,',', '')),2)) as 'Total Hardware Inventory',
            CONCAT('$',FORMAT(SUM(REPLACE(Hardware_Costs,',', '')),2)) as 'Total Hardware Cost',
            CONCAT('$',FORMAT(SUM(REPLACE(Staff_Costs,',', '')),2)) as 'Total Staff Costs',
            CONCAT('$',FORMAT(SUM(REPLACE(Allocated,',', '')),2)) as 'Allocated'
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

//Get drop down menu (Year selector)
$currentYear=date("Y");
$currentDate=date("Y/m/d");
$currentYear=date("Y");
if(isset($_GET['currentYear']))
{
     $currentYear=$_GET['currentYear'];
}
$drop_down_str=drop_down_year_with_program($conn,'view_project_task');
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
 window.location="view_project_task.php?currentYear="+passValue
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
   list($column_str,$columns)=get_mysql_columns($result);
   $output_str.=$column_str;
   mysqli_data_seek($result,0);
   $output_str.=get_mysql_values($result);


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
