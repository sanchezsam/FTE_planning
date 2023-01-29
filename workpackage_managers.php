<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_workpackage_managers($currentYear)
{
   $query="SELECT manager_name as 'Manager',
                  startdate as 'Start Date',
                  enddate as 'End Date',
                  workpackage_name as 'Workpackage',
                  forcasted_fte_total as 'Forcasted Amount' 
           FROM vw_workpackage_managers
           WHERE YEAR(enddate)=$currentYear
           ORDER BY enddate desc ";
   #echo $query;
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
$drop_down_str=drop_down_year($conn);
echo $drop_down_str;
?>
<script>
function refreshPage(passValue,search){
//do something in this function with the value
 window.location="workpackage_managers.php?currentYear="+passValue
}
</script>

<?php

   if(isset($_GET['currentYear']))
   {
       $currentYear=$_GET['currentYear'];
   }

   #display workpackage info
   $query=get_workpackage_managers($currentYear);
   $result=mysqli_query($conn,$query);

   $output_str="<table width = '900' style='border:1px solid black;'>\n";
   list($column_str,$columns)=get_mysql_columns($result);
   $output_str.=$column_str;
   $query=get_workpackage_managers($currentYear);
   $result=mysqli_query($conn,$query);
   #$output_str.=get_mysql_values_with_old($currentYear,$result,$columns);
   $output_str.=get_mysql_values($result);
   $output_str.="</table>\n";
   echo $output_str;
//echo "</section>";

require 'template/footer.html';
