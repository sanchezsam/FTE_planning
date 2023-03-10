<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/lib.php';
require 'template/header.html';

function get_staff()
{
   $query="SELECT manager_name as 'User',userlevel as 'User Level',
            	  project as 'Project',	task as 'Task'
                  FROM vw_wp_access";
   $query.=" ORDER BY manager_name";

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
#$drop_down_str=drop_down_year_basic();
#echo $drop_down_str;
?>
<script>
function refreshPage(passValue,search){
//do something in this function with the value
 window.location="managers.php?currentYear="+passValue
}
</script>

<?php


   #display staff info
   $query=get_staff();
   $result=mysqli_query($conn,$query);

   $output_str="<table id='dataTable' class='table table-striped' width = '900' style='border:1px solid black;'>\n";
   list($column_str,$columns)=get_mysql_columns($result);
   $output_str.=$column_str;
   $query=get_staff();
   $result=mysqli_query($conn,$query);
   #$output_str.=get_mysql_values_with_old($currentYear,$result,$columns);
   $output_str.=get_mysql_values($result);
   $output_str.="</table>\n";
   echo $output_str;
//echo "</section>";
require 'template/footer.html';
