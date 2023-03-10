<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/lib.php';
require 'template/header.html';

function get_project_task($program,$currentYear)
{
   $query="SELECT
          vw_wp_totals.project,
          vw_wp_totals.task,
          CONCAT('$',Service_Cost) AS 'Service Cost',
          CONCAT('$',Hardware_Inventory) AS 'Hardware Inventory',
          CONCAT('$',Hardware_Costs) AS 'Hardware Cost',
          CONCAT('$',Staff_Costs) AS 'Staff Costs',
          CONCAT('$',Allocated) as 'Allocated',
          CONCAT('$',FORMAT(REPLACE(tbl_wp_info.target,',',''),0)) as 'Targeted',
          IF(
          (REPLACE(tbl_wp_info.target,',', '')-REPLACE(allocated,',', ''))>0,
          CONCAT('$',FORMAT(REPLACE(tbl_wp_info.target,',', '')-REPLACE(allocated,',', ''),0)),
          REPLACE( FORMAT((REPLACE(tbl_wp_info.target,',', '')-REPLACE(allocated,',', '')),0),'-', '-$')
          ) as 'OverUnder'
          FROM
          vw_wp_totals,tbl_wp_info
          where
          vw_wp_totals.wp_id=tbl_wp_info.wp_id
          and YEAR(tbl_wp_info.enddate)='$currentYear'
          and program='$program'
          order by vw_wp_totals.project,vw_wp_totals.task;";
   return $query;
}

function get_program_totals($program,$currentYear)
{
    $query="SELECT '' as '--------',
                   '' as '----------------',
            CONCAT('$',FORMAT(SUM(REPLACE(Service_Cost,',', '')),0)) as 'Total Service Cost',
            CONCAT('$',FORMAT(SUM(REPLACE(Hardware_Inventory,',', '')),0)) as 'Total Hardware Inventory',
            CONCAT('$',FORMAT(SUM(REPLACE(Hardware_Costs,',', '')),0)) as 'Total Hardware Cost',
            CONCAT('$',FORMAT(SUM(REPLACE(Staff_Costs,',', '')),0)) as 'Total Staff Costs',
            CONCAT('$',FORMAT(SUM(REPLACE(Allocated,',', '')),0)) as 'Allocated',
            CONCAT('$',FORMAT(SUM(REPLACE(tbl_wp_info.target,',','')),0)) as 'Targeted',
            IF(
            SUM((REPLACE(tbl_wp_info.target,',', '')-REPLACE(allocated,',', '')))>0,
            CONCAT('$',FORMAT(SUM(REPLACE(tbl_wp_info.target,',', '')-REPLACE(allocated,',', '')),0)),
            REPLACE( FORMAT(SUM((REPLACE(tbl_wp_info.target,',', '')-REPLACE(allocated,',', ''))),0),'-', '-$')
            ) as 'OverUnder'
            FROM
            vw_wp_totals,tbl_wp_info
            where
            vw_wp_totals.wp_id=tbl_wp_info.wp_id
            and YEAR(tbl_wp_info.enddate)='$currentYear'
            and program='$program';";
     return $query;


}

//TITLE
echo "<br><strong>Project/Tasks Balances</strong><br><br>";

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
$drop_down_str=drop_down_year_with_program($conn,'view_project_balances');
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
 window.location="view_project_balances.php?currentYear="+passValue
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
