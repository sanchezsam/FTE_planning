<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_staff($group,$currentYear)
{
   $query="SELECT name as 'Staff',
                  team_name as 'Team',
                  group_name as 'Group',
                  startdate as 'Start Date',
                  enddate as 'End Date'
                  FROM tbl_staff_info";
   if($currentYear<date("Y"))
   {
        $query.=" WHERE YEAR(enddate)='$currentYear'";
   }
   else
   {
        $query.=" WHERE (YEAR(enddate)='$currentYear' or enddate IS NULL)";
   }

   $query.=" and group_name='$group'";
   $query.=" ORDER BY group_name,name";

   #echo $query;
   return $query;
}


//TITLE
echo "<br><strong>Workpackage Managers</strong><br><br>";

//Get drop down menu (Year selector)
#$currentYear=date("Y");
#$currentDate=date("Y/m/d");
$currentYear=date("Y");
$group='HPC-SYS';
if(isset($_GET['currentYear']))
{
     $currentYear=$_GET['currentYear'];
}
$where="currentYear=$currentYear&group_name=$group";
$page="staff";
$drop_down_str=drop_down_year_with_group($conn,$page,$where);
echo $drop_down_str;
?>
<script>
function refreshPage(passValue,search){
//do something in this function with the value
 window.location="staff.php?currentYear="+passValue
}
</script>

<?php

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


   if(isset($_GET['currentYear']))
   {
       $currentYear=$_GET['currentYear'];
   }

   #display staff info
   $query=get_staff($group,$currentYear);
   $result=mysqli_query($conn,$query);

   $output_str="<table id='dataTable' class='table table-striped' width = '900' style='border:1px solid black;'>\n";
   list($column_str,$columns)=get_mysql_columns($result);
   $output_str.=$column_str;
   $query=get_staff($group,$currentYear);
   $result=mysqli_query($conn,$query);
   #$output_str.=get_mysql_values_with_old($currentYear,$result,$columns);
   $output_str.=get_mysql_values($result);
   $output_str.="</table>\n";
   echo $output_str;
//echo "</section>";
}
require 'template/footer.html';
