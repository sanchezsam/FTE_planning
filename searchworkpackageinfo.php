<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';



function get_wp_totals($wp_id)
{
    $query="SELECT tbl_info.burden_rate as 'Burden Rate',
                   round(sum(tbl_wp_staff.pct_fte),2) as 'Request FTE',
                   round(sum(tbl_wp_staff.funded_percent),2) as 'Funded FTE',
                   CONCAT('$',FORMAT(tbl_services.service_cost*tbl_info.burden_rate,0)) as 'Serivce Costs',
                   CONCAT('$',FORMAT(tbl_materials.materials_cost,0)) as 'Hardware Inventory',
                   CONCAT('$',FORMAT(tbl_materials.materials_cost*tbl_info.burden_rate,0)) as 'Hardware Costs',
                   CONCAT('$', FORMAT(sum(tbl_wp_staff.total_cost) + 
                                        (tbl_services.service_cost*tbl_info.burden_rate)+
                                        (tbl_materials.materials_cost*tbl_info.burden_rate),0)) as 'FY ALLOCTIONS'
            FROM tbl_wp_staff, 
                 (SELECT sum(tbl_wp_services.total_cost) as 'service_cost'
                 FROM `tbl_wp_services`
                 WHERE tbl_wp_services.wp_id=$wp_id) as tbl_services,
                 (SELECT sum(tbl_wp_materials.total_cost)*1000 as 'materials_cost'
                 FROM `tbl_wp_materials`
                 WHERE tbl_wp_materials.wp_id=$wp_id) as tbl_materials,
                 (SELECT burden_rate 
                 FROM tbl_wp_info
                 WHERE wp_id=$wp_id) as tbl_info
           WHERE tbl_wp_staff.wp_id=$wp_id;";
   return $query;
}

function get_totals_for_staff($wp_id)
{
    $query="SELECT round(sum(pct_fte),2) as 'pct_fe',
                   CONCAT('$', FORMAT(sum(cost), 0)) as 'Cost',
                   round(sum(funded_percent),2) as 'Funded %',
                   CONCAT('$', FORMAT(sum(total_cost), 2)) as 'Totals Cost'
            FROM tbl_wp_staff
            WHERE wp_id=$wp_id";
    return $query;

}

function get_totals_for_services($wp_id)
{
    $query="SELECT CONCAT('$', FORMAT(sum(cost), 0)) as 'Cost',
                   CONCAT('$', FORMAT(sum(total_cost)*tbl_info.burden_rate,2)) as 'Total Cost'
            FROM tbl_wp_services,
            (SELECT burden_rate 
                 FROM tbl_wp_info
                 WHERE wp_id=$wp_id) as tbl_info
            WHERE wp_id=$wp_id";
    return $query;
}

function get_totals_for_materials($wp_id)
{
    $query="SELECT CONCAT('$', FORMAT(sum(replacement_cost), 0)) as 'Replacement Cost',
                   CONCAT('$', FORMAT(sum(total_cost)*tbl_info.burden_rate, 0)) as 'Total Cost'
            FROM tbl_wp_materials,
            (SELECT burden_rate 
                 FROM tbl_wp_info
                 WHERE wp_id=$wp_id) as tbl_info
            WHERE wp_id=$wp_id";
    return $query;

}
function get_wp_info($name,$currentYear)
{
   $query="SELECT wp_id,
                  task as Task,
                  task_name as 'Task Name',
                  task_manager as 'Task Manager',
                  task_description as 'Task Description',
                  burden_rate as 'Burden Rate',
                  startdate as 'Start Date',
                  enddate as 'End Date'
           FROM tbl_wp_info 
           WHERE task='$name' and
                 YEAR(enddate)='$currentYear'
           ORDER BY enddate desc";
   return $query;
}

function get_wp_staff($wp_id)
{
    $query="Select znumber as 'Z#',
                   name as 'Staff',
                   startdate as 'Start Date',
                   enddate as 'End Date',
                   title as 'Title',
                   CONCAT('$', FORMAT(salary_min, 2)) as 'Salary Min',
                   CONCAT('$', FORMAT(salary_max, 2)) as 'Salary Max',
                   group_name as 'Group Name',
                   org_code as 'ORG CODE',
                   CONCAT(FORMAT(pct_fte*100,0),'%') as 'pct_fe',
                   CONCAT('$', FORMAT(cost, 2)) as 'Cost',
                   funded as 'Funded',
                   CONCAT(FORMAT(funded_percent*100,0),'%') as 'Funded %',
                   CONCAT('$', FORMAT(total_cost, 2)) as 'Totals Cost',
                   notes 'Notes'
            FROM tbl_wp_staff
            WHERE wp_id='$wp_id'
           ";
   return $query;
}

function get_wp_materials($wp_id)
{
    $query="Select description as 'Service',
                   service_entry as 'Service Entry',
                   owner as 'Owner',
                   under_maintenance as 'Under Maint',
                   maintenance_po as 'Maint PO',
                   CONCAT(FORMAT(pct_fous*100,0),'%') as 'Percent of Fous',
        	   risk as 'Risk',
      	           replace_fund as 'Replace Fund',
  	           CONCAT('$', FORMAT(replacement_cost, 2)) as 'Replacement Cost',
                   CONCAT('$', FORMAT(total_cost, 2)) as 'Total Cost',
                   notes as 'Notes'
            FROM tbl_wp_materials
            WHERE wp_id='$wp_id'
           ";
   return $query;
}
function get_wp_services($wp_id)
{
    $query="Select description as 'Service',
                   startdate as 'Start Date',
                   enddate as 'End Date',
                   owner as 'Owner',
                   vendor as 'Vendor',
                   CONCAT(FORMAT(pct_fous*100,0),'%') as 'Percent of Fous',
                   risk as 'Risk',
                   funded as 'Funded',
                   CONCAT('$', FORMAT(cost, 2)) as 'Cost',
                   CONCAT('$', FORMAT(total_cost, 2)) as 'Total Cost',
                   notes as 'Notes'
            FROM tbl_wp_services
            WHERE wp_id='$wp_id'
           ";
   return $query;
}
function get_wp_activities($wp_id)
{
    $query="Select activity as 'Activity',
                   startdate as 'Start Date',
                   enddate as 'End Date', 
                   members as 'Members',
                   description as 'Description'
            FROM tbl_wp_activities
            WHERE wp_id='$wp_id'
           ";
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
  <script src="script_dir/jquery.min.js"></script>
  <script src="script_dir/script_workpackage_info.js"></script>


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
   
   $header_str="$name $currentYear Forcast ";
   $output_str=display_table_header($header_str); 

   $output_str.="<table width = '900' style='border:1px solid black;'>\n";
   list($column_str,$columns)=get_mysql_columns($result);
   $output_str.=$column_str;
   mysqli_data_seek($result,0);
   $record_id=get_record_id('wp_id',$result);
   mysqli_data_seek($result,0);
   $output_str.=get_mysql_values($result);
   $output_str.="</table>\n";
   echo $output_str;
   $output_str="";


   #display workpackage staff
   $query=get_wp_staff($record_id);
   $result=mysqli_query($conn,$query);
   $output_str.="<table width = '900' style='border:1px solid black;'>\n";
   $output_str.=display_table_header('Retained Team'); 
   list($column_str,$columns)=get_mysql_columns($result);
   $output_str.=$column_str;
   mysqli_data_seek($result,0);
   $output_str.=get_mysql_values($result);
   #totals for staff
   $colspan_size=sizeof($columns); 
   $query=get_totals_for_staff($record_id);
   $result=mysqli_query($conn,$query);
   list($column_totals_str,$columns_totals)=get_mysql_columns($result);
   mysqli_data_seek($result,0);
   $output_str.=get_mysql_totals_values($result,$columns,$columns_totals);
   $output_str.="</table>\n";
   echo $output_str;
   $output_str="";

   #display Services 
   $query=get_wp_services($record_id);
   $result=mysqli_query($conn,$query);
   $output_str.="<table width = '900' style='border:1px solid black;'>\n";
   $output_str.=display_table_header('Service & Support Contracts'); 
   list($column_str,$columns)=get_mysql_columns($result);
   $output_str.=$column_str;
   mysqli_data_seek($result,0);
   $output_str.=get_mysql_values($result);
   #totals for Services
   $colspan_size=sizeof($columns);  
   $query=get_totals_for_services($record_id);
   $result=mysqli_query($conn,$query);
   list($column_totals_str,$columns_totals)=get_mysql_columns($result);
   mysqli_data_seek($result,0);
   $output_str.=get_mysql_totals_values($result,$columns,$columns_totals);
   $output_str.="</table>\n";
   echo $output_str;
   $output_str="";

   #display Materials 
   $query=get_wp_materials($record_id);
   $result=mysqli_query($conn,$query);
   $output_str.="<table width = '900' style='border:1px solid black;'>\n";
   $output_str.=display_table_header('Systems & Materials'); 
   list($column_str,$columns)=get_mysql_columns($result);
   $output_str.=$column_str;
   mysqli_data_seek($result,0);
   $output_str.=get_mysql_values($result);
   #totals for materials
   $colspan_size=sizeof($columns); 
   $query=get_totals_for_materials($record_id);
   $result=mysqli_query($conn,$query);
   list($column_totals_str,$columns_totals)=get_mysql_columns($result);
   mysqli_data_seek($result,0);
   $output_str.=get_mysql_totals_values($result,$columns,$columns_totals);
   $output_str.="</table>\n";
   echo $output_str;
   $output_str="";


   #display Activities
   $query=get_wp_activities($record_id);
   $result=mysqli_query($conn,$query);
   $output_str.="<table width = '900' style='border:1px solid black;'>\n";
   $output_str.=display_table_header('Activity Descriptions'); 
   list($column_str,$columns)=get_mysql_columns($result);
   $output_str.=$column_str;
   mysqli_data_seek($result,0);
   $output_str.=get_mysql_values($result);
   $output_str.="</table>\n";
   echo $output_str;
   $output_str="";

   #display totals
   $query=get_wp_totals($record_id);
   $result=mysqli_query($conn,$query);
   $output_str.="<table width = '900' style='border:1px solid black;'>\n";
   $output_str.=display_table_header('Activity Descriptions');
   list($column_str,$columns)=get_mysql_columns($result);
   $output_str.=$column_str;
   mysqli_data_seek($result,0);
   $output_str.=get_mysql_values($result);
   $output_str.="</table>\n";
   echo $output_str;
   $output_str="";


}

//echo "</section>";

require 'template/footer.html';
