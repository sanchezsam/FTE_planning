<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_wp_info($name,$currentYear)
{

   #$query="SELECT staff_name as 'Staff Name',
   #               forcasted_amount as 'Forcasted Amount',
   #               startdate as 'Start Date',
   #               enddate as 'End Date'
   #         FROM `vw_fte_mapping`
   #         WHERE workpackage_name='$name'
   #               and YEAR(enddate)='$currentYear'
   #         ORDER BY enddate desc";
   
   $pieces = explode(" ", $name);
   $project=$pieces[0]; 
   $task= $pieces[1]; 

   #$query="SELECT CONCAT(project,' ',task) as workpackage, 
   $query="SELECT tbl_wp_staff.name, 
                  funded_percent, 
                  tbl_wp_info.startdate, 
                  tbl_wp_info.enddate 
                  FROM `tbl_wp_staff`,tbl_wp_info,tbl_staff_info
                  where tbl_wp_staff.wp_id=tbl_wp_info.wp_id 
                  and tbl_staff_info.znumber=tbl_wp_staff.znumber 
                  and project='$project'
                  and task='$task'
                  and YEAR(tbl_wp_staff.enddate)='$currentYear'
                  group by name
                  order by name";
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

<script type="text/javascript">var searchYear = "<?php echo $currentYear; ?>";</script>
<input type="hidden" name="searchYear" value="<?php echo $currentYear;?>">

<script>
function refreshPage(passValue,search){
//do something in this function with the value
 window.location="searchworkpackage.php?currentYear="+passValue
}
</script>
  <?php
    $search_str=display_search_box("Enter workpackage in the search box");
    echo $search_str;
  ?>

  <script src="script_dir/script_workpackage.js"></script>


<?php

if(isset($_POST['search']))
{
   $name=$_POST['search'];
   if(isset($_GET['currentYear']))
   {
       $currentYear=$_GET['currentYear'];
   }
?>

<?php
   #display workpackage info
   $query=get_wp_info($name,$currentYear);
   $result=mysqli_query($conn,$query);

   $output_str="<table id='dataTable' width = '900' style='border:1px solid black;'>\n";
   $header="$name $currentYear Forcast";
   $output_str.=display_table_header($header,4); 
   list($column_str,$columns)=get_mysql_columns($result);
   $output_str.=$column_str;
   mysqli_data_seek($result,0);
   $output_str.=get_mysql_values_with_old($currentYear,$result,$columns);
   $output_str.="</table>\n";
   echo $output_str;
}
//echo "</section>";

require 'template/footer.html';
