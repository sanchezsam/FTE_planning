<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_wp_info($name,$currentYear)
{
   $query="SELECT task as Task,task_name as 'Task Name',task_manager as 'Task Manager',task_description as 'Task Description',
           burden_rate as 'Burden Rate',startdate as 'Start Date',enddate as 'End Date'
           FROM tbl_wp_info where task='$name' and YEAR(enddate)='$currentYear' order by enddate desc";
   return $query;
}




//TITLE
echo "<br><strong>Search</strong> Workpackages<br><br>";


#$font_color="black";
#if(isset($_POST['search']))
#{
#   $name=$_POST['search'];
#}


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
 window.location="searchworkpackageinfo.php?currentYear="+passValue
}
</script>


<div class="container">
    <div class="row mt-4">
      <div class="col-md-8 mx-auto bg-light rounded p-4">
        <hr class="my-1">
        <h5 class="text-center text-secondary">Enter workpackage in the search box</h5>
        <form action="" method="post" class="p-3">
          <div class="input-group">
            <input type="text" name="search" id="search" class="form-control form-control-lg rounded-0 border-info" placeholder="Search..." autocomplete="off" required>
            <div class="input-group-append">
              <input type="submit" name="submit" value="Search" class="btn btn-info btn-lg rounded-0">
            </div>
          </div>
        </form>
      </div>
      <div class="col-md-5" style="position: relative;margin-top: -72px;margin-left: 174px;">

        <div class="list-group" id="show-list">
          <!-- Here autocomplete list will be display -->
        </div>
      </div>
    </div>
  </div>
  <script src="jquery.min.js"></script>
  <script src="script_workpackage_info.js"></script>


<?php

if(isset($_POST['search']))
{
   $name=$_POST['search'];
   if(isset($_GET['currentYear']))
   {
       $currentYear=$_GET['currentYear'];
   }

   #display workpackage info
   $query=get_wp_info($name,$currentYear);
   $result=mysqli_query($conn,$query);

   $output_str="<table><tr><td rowspan='4'>$name $currentYear Forcast</td></tr></table>";
   $output_str.="<table width = '900' style='border:1px solid black;'>\n";
   list($column_str,$columns)=get_mysql_columns($result);
   $output_str.=$column_str;
   $query=get_wp_info($name,$currentYear);
   $result=mysqli_query($conn,$query);
   $output_str.=get_mysql_values($result);
   $output_str.="</table>\n";
   echo $output_str;
}
//echo "</section>";

require 'template/footer.html';
