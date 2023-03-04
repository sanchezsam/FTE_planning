<?php 
include_once('config2.php'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_workpackage_info($program,$currentYear)
{
   $query="select wp_id,
                  program,
                  project,
                  task,
                  task_name,
                  task_manager,
                  task_description,
                  burden_rate,
                  target,
                  startdate,
                  enddate
          from tbl_wp_info
          where YEAR(enddate)='$currentYear'
                and program='$program'";
   return $query;
}
function cal_cost($salary_min,$salary_max,$percent)
{
  $average_salary=(floatval($salary_min)+floatval($salary_max))/2;
  $percent_salary=$average_salary*floatval($percent);
  $cost=$average_salary-$percent_salary;
  return round($cost,2);
}


function get_name_from_znumber($conn,$znumber,$currentYear)
{
   $name=""; 
   $query="SELECT name FROM `tbl_staff_info` WHERE znumber='$znumber' and YEAR(enddate)='$currentYear'";
   $result=mysqli_query($conn,$query);
   while($row=mysqli_fetch_array($result))
   {
     $name=$row[0];
   }
   return $name;
}

function get_title_and_salary($conn,$znumber,$wp_staff_id,$currentYear)
{
   $query="SELECT labor_pool,job_title FROM `tbl_staff_info` WHERE znumber='$znumber' and YEAR(enddate)='$currentYear'";
   $result=mysqli_query($conn,$query);
   $output_str="";
   while($row=mysqli_fetch_array($result))
   {
      $title=$row[0];
      $str = str_replace(' ', '', $title);
      $pattern = "{([^0-9]+)([0-9]+)(\-)([0-9]+\.[0-9]+)}";

      if (preg_match($pattern, $str, $matches)) {
          $title="$matches[1] ($row[1])";
          $salary_min=$matches[2];
          $salary_max=$matches[4];
          #$salary_min="$" . number_format($matches[2], 2, ".", ",");
          #$salary_max="$" . number_format($matches[4], 2, ".", ",");
          #echo $title,$salary_min,$salary_max;
          $update_query="update tbl_wp_staff set title='$title',salary_min='$salary_min',salary_max='$salary_max' 
                         where znumber='$znumber'";
          mysqli_query($conn,$update_query);
      }
      #print_r($titleArray);
      $output_str="<td valign='top'>$title</td>\n";
      $output_str.="<td valign='top'><input name='salary_min_txt[$wp_staff_id]' type='text' value='$salary_min'></td>\n";
      $output_str.="<td valign='top'><input name='salary_max_txt[$wp_staff_id]' type='text' value='$salary_max'></td>\n";
   }
   return $output_str; 
}



?>
<!doctype html>
<html lang="en-US" class="no-js">

<body>
<?php
//TITLE
echo "<br><strong>Workpackage Managers</strong><br><br>";

//Get drop down menu (Year selector)
#$currentYear=date("Y");
#$currentDate=date("Y/m/d");
#$currentYear=date("Y");
if(isset($_GET['currentYear']))
{
     $currentYear=$_GET['currentYear'];
}
if(isset($_POST['year']))
{    
          $currentYear=$_POST['year'][0];
}
$drop_down_str=drop_down_year_with_program($conn,'update_workpackage_info');
echo $drop_down_str;



?>



<script>
function refreshPage(passValue,search){
//do something in this function with the value
 window.location="update_workpackage_info.php?currentYear="+passValue
}
</script>

<script src="script_dir/jquery.min.js"></script>
<script src="script_dir/script_workpackage_update_info.js"></script>

<hr><div class="clearfix"></div>
<?php
   $program="";
       if(isset($_POST['program_name'])){
            $program=$_POST['program_name'];
       }
       if(isset($_GET['program_name'])){
            $program=$_GET['program_name'];
       }
       if(isset($_GET['currentYear']))
       {
          $currentYear=$_GET['currentYear'];
       }

?>

<div id="msg"></div>
<form id="form" method="post" ACTION="update_workpackage_info.php?program_name=<?php echo $program?>&currentYear=<?php echo $currentYear?>">
<input type="hidden" name="action" value="saveAddMore">
<input type="hidden" name="search" value="<?php echo $program;?>">
<input type="hidden" name="currentYear" value="<?php echo $currentYear;?>">
<?php
extract($_POST);

if(isset($_POST['submit']) || isset($_POST['save']) || isset($_GET['program_name']))
#if(isset($_POST['submit']))
{
   if($program!="")
   {
       $query=get_workpackage_info($program,$currentYear);
       $result=mysqli_query($conn,$query);
   }
   $termcount=0;
   $previousName="";
   $znumbers=array();
   $currentDate=date("Y/m/d");
   #$currentYear=date("Y");
   $currentDate=strtotime($currentDate);
   $output_str="";
   $currentColor="";

   $header="Update $currentYear $program Workpackages";
   $display_str=display_table_header($header);
   echo $display_str;
   $output_str.="<table width = '900' border='1' style='border:1px solid black;'>\n";
   $row_str1="<tr bgcolor ='$column_color'>\n";
   $row_str1.="<td valign='top'><b>Program</b></td>\n";
   $row_str1.="<td valign='top'><b>Project</b></td>\n";
   $row_str1.="<td valign='top'><b>Task</b></td>\n";
   $row_str1.="<td valign='top'><b>Burden Rate</b></td>\n";
   $row_str1.="<td valign='top'><b>Target $$$</b></td>\n";
   $row_str1.="<td valign='top'><b>Task Manager</b></td>\n";
   $row_str1.="</tr>\n";
   

   $row_str2="<tr bgcolor ='$column_color'>\n";
   $row_str2.="<td valign='top' colspan='3'><b>Task Name</b></td>\n";
   #$row_str2.="<td valign='top'><b>Start Date</b></td>\n";
   #$row_str2.="<td valign='top'><b>End Date</b></td>\n";
   $row_str2.="<td valign='top' colspan='3'><b>Task Description</b></td>\n";
   $row_str2.="</tr>\n";

   $output_str.="<tbody id='tb'>\n";
   while($row=mysqli_fetch_array($result))
   {

      $wp_id=$row[0];
      $program=$row[1];
      $project=$row[2];
      $task=$row[3];
      $task_name=$row[4];
      $task_manager=$row[5];
      $task_description=$row[6];
      $burden_rate=$row[7];
      $target=$row[8];
      $start_date=$row[9];
      $end_date=$row[10];


      $currentColor= ${'colour' .($termcount % 2)};
      $output_str.=$row_str1;
      $output_str.="<tr bgcolor='$currentColor'>\n";

      $query="SELECT DISTINCT program_name FROM tbl_program_info;";
      $drop_down_name="<select name='program_txt[$wp_id]' id='program' width='4'>\n";
      $output_str.=generate_select_list($db,$query,$program,$drop_down_name);
      $output_str.="<td valign='top'><input name='project_txt[$wp_id]' type='text' value='$project'></td>\n";
      $output_str.="<td valign='top'><input name='task_txt[$wp_id]' type='text' value='$task'></td>\n";
      $output_str.="<td valign='top'><input name='burden_rate_txt[$wp_id]' type='text' value='$burden_rate'></td>\n";
      $output_str.="<td valign='top'><input name='target_txt[$wp_id]' type='text' value='$target'></td>\n";

      $query="SELECT DISTINCT manager_name FROM tbl_wp_manager order by manager_name asc;";
      $drop_down_name="<select name='task_manager_txt[$wp_id]' id='task_managers' data-size='10' required='required'>\n";
      $output_str.=generate_select_list($db,$query,$task_manager,$drop_down_name);

      $output_str.="</tr>";
      $output_str.=$row_str2;

      $output_str.="<tr bgcolor='$currentColor'>\n";

      $output_str.="<td valign='top' colspan='3'><input name='task_name_txt[$wp_id]' type='text' value='$task_name'></td>\n";
      $output_str.="<td valign='top' colspan='3'><table><tr><td valign='top' width='95%'><textarea name='task_description_txt[$wp_id]' rows='4'>$task_description</textarea></td>";
      $output_str.="<td valign='top'><a href='update_workpackage_info.php?program_name=$program&currentYear=$currentYear&delete_id=$wp_id'>";
      $output_str.="<button type='button' data-toggle='tooltip' data-placement='right' class='btn btn-danger'><i class='fa fa-fw fa-trash-alt'></i></button></a></td></tr></table></td>";

      #$cost="$" . number_format(floatval($cost), 2, ".", ",");
      #$output_str.="<td valign='top'><input name='cost_txt[$wp_staff_id]' type='text' value='$cost'></td>\n";

      $output_str.="</tr>\n";
      $termcount++;

   }

      $output_str.="</tbody><tr>";
      $output_str.="<td colspan='6'>";
      $output_str.="<a href='javascript:;' class='btn btn-danger' id='addmore'><i class='fa fa-fw fa-plus-circle'></i> Add More</a>";
      $output_str.="<button type='submit' name='save' id='save' value='save' class='btn btn-primary'><i class='fa fa-fw fa-save'></i> Save</button>";
      $output_str.="</td>";
      $output_str.="</tr>";
   $output_str.="</table>\n";
   echo $output_str;

}

   ?>
                </form>
                <div class="clearfix"></div>

<?php

  if(isset($_GET['delete_id'])){
      $wp_id=$_GET['delete_id'];
      $delete_query="DELETE from tbl_wp_info where wp_id='$wp_id'";
      $db->query($delete_query);
      $delete_query="DELETE from tbl_wp_staff where wp_id='$wp_id'";
      $db->query($delete_query);
      $delete_query="DELETE from tbl_wp_activities where wp_id='$wp_id'";
      $db->query($delete_query);
      $delete_query="DELETE from tbl_wp_services where wp_id='$wp_id'";
      $db->query($delete_query);
      $delete_query="DELETE from tbl_wp_materials where wp_id='$wp_id'";
      $db->query($delete_query);
      echo "<script>window.open('update_workpackage_info.php?program_name=$program&currentYear=$currentYear','_self') </script>";
  }




if(isset($_POST['save'])){
        extract($_REQUEST);
        extract($_POST);
        #Refresh
        #var_dump($_POST);

    if(isset($_POST['programs']))
    {
       if (is_array($programs) || is_object($programs))
       {
            foreach($programs as $key=>$program){
                   $startdate=date("Y-m-d");
                   $enddate="$currentYear-$endMonth";
                   $project=$project[$key];
                   $task=$task[$key];
                   $task_name=$task_name[$key];
                   $task_manager=$task_manager[$key];
                   $task_description=$task_description[$key];
                   $burden_rate=$burden_rate[$key];
                   $target=$target[$key]; 

                    $insert_query="INSERT INTO tbl_wp_info
                                   (program,project,task,task_name,task_manager,task_description,burden_rate,target,startdate,enddate) 
                                   VALUES ('$program','$project','$task','$task_name','$task_manager','$task_description',
                                            '$burden_rate','$target','$startdate','$enddate');";
                    echo "<br>$insert_query<br>";
               $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
               $txt=$insert_query;
               fwrite($myfile, $txt);
               fclose($myfile);
                    $db->query($insert_query);

            }
         }
      }











        if(isset($_POST['program_txt']))
        {
            foreach($program_txt as $key=>$program)
            {
               $project=$project_txt[$key];
               $task=$task_txt[$key];
               $task_name=$task_name_txt[$key];
               $task_description=$task_description_txt[$key];
               $task_manager=$task_manager_txt[$key];
               $burden_rate=$burden_rate_txt[$key];
               $target=$target_txt[$key];
               $update_query="UPDATE tbl_wp_info 
                              SET program = '$program',
                              project = '$project',
                              task = '$task',
                              task_name = '$task_name',
                              task_manager = '$task_manager',
                              task_description= '$task_description',
                              burden_rate = '$burden_rate',
                              target = '$target'
                              WHERE wp_id = $key; ";
               #echo "$update_query<br>";
               $db->query($update_query);
               #$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
               #$txt=$update_query;
               #fwrite($myfile, $txt);
               #fclose($myfile);
                              
            }
       }
       echo "<meta http-equiv='refresh' content='0'>";
       echo "<script>window.open('update_workpackage_info.php?program_name=$program&currentYear=$currentYear','_self') </script>"; 
}

require 'template/footer.html';
?>




		
    </div> <!--/.container-->
	
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
<!--    
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    -->	
    
</body>
</html>
