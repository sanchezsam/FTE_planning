<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
#require 'template/header.html';
require 'template/header.html';

#$file_name ="test.xls";
#header("Content-type: application/vnd.ms-excel");
#header("Content-Disposition: attachment; filename=$file_name");


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
                 (SELECT COALESCE(sum(tbl_wp_services.total_cost),0) as 'service_cost'
                 FROM `tbl_wp_services`
                 WHERE tbl_wp_services.wp_id=$wp_id) as tbl_services,
                 (SELECT COALESCE(sum(tbl_wp_materials.total_cost),0)*1000 as 'materials_cost'
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
                  program as Program,
                  project as Project,
                  task as Task,
                  task_name as 'Task Name',
                  task_manager as 'Task Manager',
                  burden_rate as 'Burden Rate',
                  startdate as 'Start Date',
                  enddate as 'End Date',
                  task_description as 'Task Description'
           FROM tbl_wp_info 
           WHERE concat(Project,' ', task)='$name' and
                 YEAR(enddate)='$currentYear'
           ORDER BY enddate desc limit 1";
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
    $query="Select property_number as 'Property Number',
                   description as 'Service',
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

  <?php
    $search_str=display_search_box("Enter workpackage in the search box");
    echo $search_str;
    echo "<br>";
  ?>


  <!--<script src="script_dir/jquery.min.js"></script>-->
  <script src="script_dir/script_workpackage_info.js"></script>


<?php

if(isset($_POST['search']))
{
   $name=$_POST['search'];
   if(isset($_GET['currentYear']))
   {
       $currentYear=$_GET['currentYear'];
   }
   $seperator_color='white'; 
   #open of main table
   $output_str="\n<table id='dataTable' width='100%'>\n";
   #display workpackage info
   $query=get_wp_info($name,$currentYear);
   $result=mysqli_query($conn,$query);
  
   $count=mysqli_num_rows($result);
   if($count==0)
   {

      echo "No Results";
   }
   else
   { 
       $header_str="$name $currentYear Forcast ";
       $output_str.=display_table_header($header_str,7); 

       $termcount=1;
       while($row=mysqli_fetch_array($result))
       {
          $wp_id=$row[0];
          $program=$row[1];
          $project=$row[2];
          $task=$row[3];
          $task_name=$row[4];
          $task_manager=$row[5];
          $burden_rate=$row[6];
          $startdate=$row[7];
          $enddate=$row[8];
          $task_description=$row[9];
          $currentColor= ${'colour' .($termcount % 2)};
          
          #$output_str.="<tr bgcolor='$column_color'>\n";
          $output_str.="<tr>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Program</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Project</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Task</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top' colspan='4'><b>Task Name</b></td>\n";
          $output_str.="</tr>";
          $output_str.="<tr>\n";
          $output_str.="<td  style='background-color:$currentColor' valign='top'>$program</td>\n";
          $output_str.="<td  style='background-color:$currentColor' valign='top'>$project</td>\n";
          $output_str.="<td  style='background-color:$currentColor' valign='top'>$task</td>\n";
          $output_str.="<td  style='background-color:$currentColor' valign='top' colspan='4'>$task_name</td>\n";
          $output_str.="</tr>";

          $output_str.="<tr>\n";
          $output_str.="<td  style='background-color:$column_color' valign='top' colspan='4'><b>Task Manager</b></td>\n";
          $output_str.="<td  style='background-color:$column_color' valign='top'><b>Burden Rate</b></td>\n";
          $output_str.="<td  style='background-color:$column_color' valign='top'><b>Start Date</b></td>\n";
          $output_str.="<td  style='background-color:$column_color' valign='top'><b>End Date</b></td>\n";
          $output_str.="</tr>";
          $output_str.="<tr bgcolor='$currentColor'>\n";
          $output_str.="<td  style='background-color:$currentColor' valign='top' colspan='4'>$task_manager</td>\n";
          $output_str.="<td  style='background-color:$currentColor' valign='top'>$burden_rate</td>\n";
          $output_str.="<td  style='background-color:$currentColor' valign='top'>$startdate</td>\n";
          $output_str.="<td  style='background-color:$currentColor' valign='top'>$enddate</td>\n";
          $output_str.="</tr>";

          $output_str.="<tr>\n";
          $output_str.="<td style='background-color:$column_color' colspan='7' valign='top'><b>Task Description</b></td>\n";
          $output_str.="</tr>";
          $output_str.="<tr bgcolor='$currentColor'>\n";
          $output_str.="<td style='background-color:$currentColor' colspan='7' valign='top'>$task_description</td>\n";
          $output_str.="</tr>";
          $termcount++;
        }

       $output_str.="<tr bgcolor='$seperator_color'><td colspan='9'></td></tr>";


       #display workpackage staff 
       $query=get_wp_staff($wp_id);
       $result=mysqli_query($conn,$query);
       
       $output_str.=display_table_header('Retained Team',9); 

       $termcount=1;
       while($row=mysqli_fetch_array($result))
       {
          $znumber=$row[0];
          $staff=$row[1];
          $startdate=$row[2];
          $enddate=$row[3];
          $title=$row[4];
          $salary_min=$row[5];
          $salary_max=$row[6];
          $group_name=$row[7];
          $org_code=$row[8];
          $pct_fte=$row[9];
          $cost=$row[10];
          $funded=$row[11];
          $funded_percent=$row[12];
          $total_cost=$row[13];
          $notes=$row[14];
          $currentColor= ${'colour' .($termcount % 2)};
          
          $output_str.="<tr bgcolor='$column_color'>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>znumber</b></td>\n";
          $output_str.="<td style='background-color:$column_color' colspan='2' valign='top'><b>Staff</b></td>\n";
          $output_str.="<td style='background-color:$column_color' colspan='4' valign='top'><b>Title</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Start Date</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>End Date</b></td>\n";
          $output_str.="</tr>";
          $output_str.="<tr bgcolor='$currentColor'>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$znumber</td>\n";
          $output_str.="<td style='background-color:$currentColor' colspan='2' valign='top'>$staff</td>\n";
          $output_str.="<td style='background-color:$currentColor' colspan='4' valign='top'>$title</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$startdate</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$enddate</td>\n";
          $output_str.="</tr>";

          $output_str.="<tr bgcolor='$column_color'>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Group Name</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Org Code</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Salary Min</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Salary Max</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Pct FTE</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Cost</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Funded</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Funded Percent</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Total Cost</b></td>\n";
          $output_str.="</tr>";
          $output_str.="<tr bgcolor='$currentColor'>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$group_name</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$org_code</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$salary_min</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$salary_max</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$pct_fte</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$cost</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$funded</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$funded_percent</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$total_cost</td>\n";
          $output_str.="</tr>";

          if(trim($notes)!="")
          {
              $output_str.="<tr bgcolor='$column_color'>\n";
              $output_str.="<td style='background-color:$currentColor' colspan='9' valign='top'><b>Notes:</b> $notes</td>\n";
              $output_str.="</tr>";
          }
          $output_str.="<tr bgcolor='gray'><td colspan='9'></td></tr>";
          $termcount++;
        }

       #totals for staff
       $query=get_totals_for_staff($wp_id);
       $result=mysqli_query($conn,$query);
       $output_str.="<tr bgcolor='$totals_color'>";
       $output_str.="<td style='background-color:$totals_color' colspan='4'></td>";
       $output_str.="<td style='background-color:$totals_color'><b>PCT FTE</b></td>";
       $output_str.="<td style='background-color:$totals_color'><b>Cost</b></td>";
       $output_str.="<td style='background-color:$totals_color'></td>";
       $output_str.="<td style='background-color:$totals_color'><b>Funded %</b></td>";
       $output_str.="<td style='background-color:$totals_color'><b>Total Cost</b></td>";
       $output_str.="</tr>";
       while($row=mysqli_fetch_array($result))
       {
           $pct_fte=$row[0];
           $cost=$row[1];
           $funded=$row[2];
           $total_cost=$row[3];
           $output_str.="<tr bgcolor='$totals_color'>";
           $output_str.="<td style='background-color:$totals_color' colspan='4'></td>";
           $output_str.="<td style='background-color:$totals_color'>$pct_fte</td>";
           $output_str.="<td style='background-color:$totals_color'>$cost</td>";
           $output_str.="<td style='background-color:$totals_color'></td>";
           $output_str.="<td style='background-color:$totals_color'>$funded</td>";
           $output_str.="<td style='background-color:$totals_color'>$total_cost</td>";
           $output_str.="</tr>";
       }

       $output_str.="<tr bgcolor='$seperator_color'><td colspan='9'></td></tr>";

       ##display Services 

       $query=get_wp_services($wp_id);
       $result=mysqli_query($conn,$query);
       
       $output_str.=display_table_header('Service & Support Contracts',10); 
       #$output_str.=display_table_header($header_str,4); 

       $termcount=1;
       $output_str.="<tr bgcolor='$column_color'>\n";
       $output_str.="<td style='background-color:$column_color' valign='top'><b>Service</b></td>\n";
       $output_str.="<td style='background-color:$column_color' valign='top'><b>Start Date</b></td>\n";
       $output_str.="<td style='background-color:$column_color' valign='top'><b>End Date</b></td>\n";
       $output_str.="<td style='background-color:$column_color' valign='top'><b>Owner</b></td>\n";
       $output_str.="<td style='background-color:$column_color' valign='top'><b>Vendor</b></td>\n";
       $output_str.="<td style='background-color:$column_color' valign='top'><b>Percent of FOUS</b></td>\n";
       $output_str.="<td style='background-color:$column_color' valign='top'><b>Risk</b></td>\n";
       $output_str.="<td style='background-color:$column_color' valign='top'><b>Funded</b></td>\n";
       $output_str.="<td style='background-color:$column_color' valign='top'><b>Cost</b></td>\n";
       $output_str.="<td style='background-color:$column_color' valign='top'><b>Total Cost</b></td>\n";
       $output_str.="</tr>";
       while($row=mysqli_fetch_array($result))
       {
          $service=$row[0];
          $startdate=$row[1];
          $enddate=$row[2];
          $owner=$row[3];
          $vendor=$row[4];
          $percent=$row[5];
          $risk=$row[6];
          $funded=$row[7];
          $cost=$row[8];
          $total_cost=$row[9];
          $notes=$row[10];
          $currentColor= ${'colour' .($termcount % 2)};
          $output_str.="<tr bgcolor='$currentColor'>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$service</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$startdate</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$enddate</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$owner</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$vendor</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$percent</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$risk</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$funded</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$cost</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$total_cost</td>\n";
          $output_str.="</tr>";
          if(trim($notes)!="")
          {
              $output_str.="<tr>\n";
              $output_str.="<td style='background-color:$currentColor' colspan='10' valign='top'><b>Notes:</b> $notes</td>\n";
              $output_str.="</tr>";
          }
          $termcount++;
        }
       #totals for services
       $query=get_totals_for_services($wp_id);
       $result=mysqli_query($conn,$query);
       $output_str.="<tr bgcolor='$totals_color'>";
       $output_str.="<td style='background-color:$totals_color' colspan='8'></td>";
       $output_str.="<td style='background-color:$totals_color' colspan='1'><b>Cost</b></td>";
       $output_str.="<td style='background-color:$totals_color' colspan='1'><b>Total Cost</b></td>";
       $output_str.="</tr>";
       while($row=mysqli_fetch_array($result))
       {
           $cost=$row[0];
           $total_cost=$row[1];
           $output_str.="<tr bgcolor='$totals_color'>";
           $output_str.="<td style='background-color:$totals_color' colspan='8'></td>";
           $output_str.="<td style='background-color:$totals_color' colspan='1'>$cost</td>";
           $output_str.="<td style='background-color:$totals_color' colspan='1'>$total_cost</td>";
           $output_str.="</tr>";
       }

       $output_str.="<tr bgcolor='$seperator_color'><td colspan='100%'></td></tr>";


       ##display Materials 

       $query=get_wp_materials($wp_id);
       $result=mysqli_query($conn,$query);
       
       $output_str.=display_table_header('Systems & Materials',10); 

       $termcount=1;
       while($row=mysqli_fetch_array($result))
       {
          $property_number=$row[0];
          $description=$row[1];
          $service_entry=$row[2];
          $owner=$row[3];
          $under_maint=$row[4];
          $maint_po=$row[5];
          $pct_fous=$row[6];
          $risk=$row[7];
          $replace_fund=$row[8];
          $replace_cost=$row[9];
          $total_cost=$row[10];
          $notes=$row[11];
          $currentColor= ${'colour' .($termcount % 2)};
          
          $output_str.="<tr bgcolor='$column_color'>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Property Number</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top' colspan='9'><b>Description</b></td>\n";
          $output_str.="</tr>";
          $output_str.="<tr bgcolor='$currentColor'>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$property_number</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'colspan='9'>$description</td>\n";
          $output_str.="</tr>";

          $output_str.="<tr bgcolor='$column_color'>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Service Entry</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top' colspan='3'><b>Owner</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Under Maint</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Percent of FOUS</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Risk</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Replace Fund</b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Replacement Cost<b></td>\n";
          $output_str.="<td style='background-color:$column_color' valign='top'><b>Total Cost</b></td>\n";
          $output_str.="</tr>";
          $output_str.="<tr bgcolor='$currentColor'>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$service_entry</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top' colspan='3'>$owner</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$under_maint</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$percent</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$risk</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$replace_fund</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$replace_cost</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$total_cost</td>\n";
          $output_str.="</tr>";

          if(trim($notes)!="")
          {
              $output_str.="<tr bgcolor='$currentColor'>\n";
              $output_str.="<td style='background-color:$currentColor' colspan='10' valign='top'><b>Notes:</b> $notes</td>\n";
              $output_str.="</tr>";
          }
          $output_str.="<tr bgcolor='gray'><td colspan='100%'></td></tr>";
          $termcount++;
        }

       #totals for materials
       $query=get_totals_for_materials($wp_id);
       $result=mysqli_query($conn,$query);
       $output_str.="<tr bgcolor='$totals_color'>";
       $output_str.="<td style='background-color:$totals_color' colspan='8'></td>";
       $output_str.="<td style='background-color:$totals_color' colspan='1'><b>Cost</b></td>";
       $output_str.="<td style='background-color:$totals_color' colspan='1'><b>Total Cost</b></td>";
       $output_str.="</tr>";
       while($row=mysqli_fetch_array($result))
       {
           $cost=$row[0];
           $total_cost=$row[1];
           $output_str.="<tr bgcolor='$totals_color'>";
           $output_str.="<td style='background-color:$totals_color' colspan='8'></td>";
           $output_str.="<td style='background-color:$totals_color' colspan='1'>$cost</td>";
           $output_str.="<td style='background-color:$totals_color' colspan='1'>$total_cost</td>";
           $output_str.="</tr>";
       }  
       $output_str.="<tr bgcolor='$seperator_color'><td colspan='100%'></td></tr>";

       ##display Activities

       $query=get_wp_activities($wp_id);
       $result=mysqli_query($conn,$query);
       
       $output_str.=display_table_header('Activity Description',10); 
       #$output_str.=display_table_header($header_str,4); 

       $termcount=1;
       $output_str.="<tr bgcolor='$column_color'>\n";
       $output_str.="<td style='background-color:$column_color' valign='top' colspan='2'><b>Activity</b></td>\n";
       $output_str.="<td style='background-color:$column_color' valign='top'><b>Start Date</b></td>\n";
       $output_str.="<td style='background-color:$column_color' valign='top'><b>End Date</b></td>\n";
       $output_str.="<td style='background-color:$column_color' valign='top' colspan='2'><b>Members</b></td>\n";
       $output_str.="<td style='background-color:$column_color' valign='top' colspan='4'><b>Description</b></td>\n";
       $output_str.="</tr>";
       while($row=mysqli_fetch_array($result))
       {
          $activity=$row[0];
          $startdate=$row[1];
          $enddate=$row[2];
          $members=$row[3];
          $description=$row[4];
          $currentColor= ${'colour' .($termcount % 2)};
          $output_str.="<tr bgcolor='$currentColor'>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top' colspan='2'>$activity</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$startdate</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top'>$enddate</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top' colspan='2'>$members</td>\n";
          $output_str.="<td style='background-color:$currentColor' valign='top' colspan='4'>$description</td>\n";
          $output_str.="</tr>";
          $termcount++;
        }
  

       $output_str.="<tr bgcolor='$seperator_color'><td colspan='100%'></td></tr>";
       #display totals
       $query=get_wp_totals($wp_id);
       $result=mysqli_query($conn,$query);
       $output_str.=display_table_header('Workpackage Info',10);

       $output_str.="<tr bgcolor='$column_color'>\n";
       $output_str.="<td style='background-color:$totals_color' valign='top'><b>Burden Rate</b></td>\n";
       $output_str.="<td style='background-color:$totals_color' valign='top' colspan='2'><b>Request FTE</b></td>\n";
       $output_str.="<td style='background-color:$totals_color' valign='top' colspan='2'><b>Funded FTE</b></td>\n";
       $output_str.="<td style='background-color:$totals_color' valign='top' colspan='2'><b>Service Costs</b></td>\n";
       $output_str.="<td style='background-color:$totals_color' valign='top' colspan='1'><b>Hardware Inventory</b></td>\n";
       $output_str.="<td style='background-color:$totals_color' valign='top' colspan='1'><b>Hardware Costs</b></td>\n";
       $output_str.="<td style='background-color:$totals_color' valign='top' colspan='1'><b>FY Allocations</b></td>\n";
       $output_str.="</tr>";
       while($row=mysqli_fetch_array($result))
       {
          $burden_rate=$row[0];
          $request_fte=$row[1];
          $funded_fte=$row[2];
          $service_costs=$row[3];
          $hardware_inventory=$row[4];
          $hardware_costs=$row[5];
          $fy_allocations=$row[6];
          $currentColor= ${'colour' .($termcount % 2)};
          $output_str.="<tr bgcolor='$currentColor'>\n";
          $output_str.="<td style='background-color:$totals_color' valign='top'>$burden_rate</td>\n";
          $output_str.="<td style='background-color:$totals_color' valign='top' colspan='2'>$request_fte</td>\n";
          $output_str.="<td style='background-color:$totals_color' valign='top' colspan='2'>$funded_fte</td>\n";
          $output_str.="<td style='background-color:$totals_color' valign='top' colspan='2'>$service_costs</td>\n";
          $output_str.="<td style='background-color:$totals_color' valign='top' colspan='1'>$hardware_inventory</td>\n";
          $output_str.="<td style='background-color:$totals_color' valign='top' colspan='1'>$hardware_costs</td>\n";
          $output_str.="<td style='background-color:$totals_color' valign='top' colspan='1'>$fy_allocations</td>\n";
          $output_str.="</tr>";
        }

       #Close of main table
       $output_str.="</table>\n";
       echo $output_str;
       $output_str="";

   }
}

//echo "</section>";

require 'template/footer.html';
