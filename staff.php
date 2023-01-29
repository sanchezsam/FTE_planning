<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_staff($currentYear)
{
   $query="SELECT staff_name as 'Staff',
                  team_name as 'Team',
                  group_name as 'Group',
                  startdate as 'Start Date',
                  enddate as 'End Date'
                  FROM vw_staff_mapping";
   if($currentYear<date("Y"))
   {
        $query.=" WHERE YEAR(enddate)='$currentYear'";
   }

   $query.=" ORDER BY group_name,staff_name";

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
 window.location="staff.php?currentYear="+passValue
}
</script>

<?php

   if(isset($_GET['currentYear']))
   {
       $currentYear=$_GET['currentYear'];
   }

   #display staff info
   $query=get_staff($currentYear);
   $result=mysqli_query($conn,$query);

   $output_str="<table width = '900' style='border:1px solid black;'>\n";
   list($column_str,$columns)=get_mysql_columns($result);
   $output_str.=$column_str;
   $query=get_staff($currentYear);
   $result=mysqli_query($conn,$query);
   #$output_str.=get_mysql_values_with_old($currentYear,$result,$columns);
   $output_str.=get_mysql_values($result);
   $output_str.="</table>\n";
   echo $output_str;
//echo "</section>";

require 'template/footer.html';
