<?php 
include_once('config2.php'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_workpackage_info($name,$currentYear)
{
   #$currentYear=date("Y");
   $query="SELECT * 
           FROM tbl_wp_info
           WHERE concat(Project,' ', task)='$name'
                 and YEAR(enddate)=$currentYear limit 1";
   #echo "$query";
   return $query;
}


?>
<!doctype html>
<html lang="en-US" class="no-js">

<body>

  <?php
     #drop down
     $currentYear=date("Y");
     if(isset($_GET['currentYear']))
     {    
          $currentYear=$_GET['currentYear'];
     }
     $drop_down_str=drop_down_year_basic($conn);
     echo $drop_down_str;
?>
<script>
function refreshPage(passValue,search){
//do something in this function with the value
 window.location="update_workpackageinfo.php?currentYear="+passValue
}
</script>

  <?php




    $search_str=display_search_box("Enter workpackage in the search box");
    echo $search_str;
  ?>
  <script src="script_dir/jquery.min.js"></script>
<script src="script_dir/script_workpackage_update_info.js"></script>

    	<hr>
		<div class="clearfix"></div>
		<?php
   $name="";
   $managers="";
   if(isset($_POST['search'])){
       $name=$_POST['search'];
       if(isset($_GET['currentYear']))
       {
          $currentYear=$_GET['currentYear'];
       } 
   }
   if($name=="")
   {
     if(isset($_GET['search'])){
         $name=$_GET['search'];
     }
   }
   if($name!="")
   {
       $query=get_workpackage_info($name,$currentYear);
       $result=mysqli_query($conn,$query);
   }

	?>

<div id="msg"></div>
<form id="form" method="post" ACTION="update_workpackageinfo.php?search=<?php echo $name?>&currentYear=<?php echo $currentYear?>">
<input type="hidden" name="action" value="saveAddMore">
<input type="hidden" name="search" value="<?php echo $name;?>">
<input type="hidden" name="currentYear" value="<?php echo $currentYear;?>">
<?php
if($name!="")
{
   $termcount=0;
   $previousName="";
   $currentDate=date("Y/m/d");
   $currentYear=date("Y");
   $currentDate=strtotime($currentDate);
   $output_str="";
   $currentColor="";
   $header="Update Workpackage: $name";
   ###$display_str=display_table_header($header); 
   ###echo $display_str;
   ###$output_str.="<table width = '900' border='1' style='border:1px solid black;'>\n";
   ###$output_str.="<tr bgcolor ='#C1C1E8'>\n";
   ###$output_str.="<td valign='top'><b>Program</b></td>\n";
   ###$output_str.="<td valign='top'><b>Project</b></td>\n";
   ###$output_str.="<td valign='top'><b>Task</b></td>\n";
   ###$output_str.="<td valign='top'><b>Task Name</b></td>\n";
   ###$output_str.="<td valign='top'><b>Task Manager</b></td>\n";
   ###$output_str.="<td valign='top'><b>Task Description</b></td>\n";
   ###$output_str.="<td valign='top'><b>Burden Rate</b></td>\n";
   ###$output_str.="<td valign='top'><b>Target $$$</b></td>\n";
   ###$output_str.="<td valign='top'><b>Start Date</b></td>\n";
   ###$output_str.="<td valign='top'><b>End Date</b></td>\n";
   ###$output_str.="</tr>\n";
   ####$output_str.="<tbody id='tb'>\n";
   ###$output_str.="<tr bgcolor='$currentColor'>\n";
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
      $output_str.="<td valign='top'><a href='update_workpackageinfo.php?program_name=$program&currentYear=$currentYear&delete_id=$wp_id'>";
      $output_str.="<button type='button' data-toggle='tooltip' data-placement='right' class='btn btn-danger'><i class='fa fa-fw fa-trash-alt'></i></button></a></td></tr></table></td>";

      #$cost="$" . number_format(floatval($cost), 2, ".", ",");
      #$output_str.="<td valign='top'><input name='cost_txt[$wp_staff_id]' type='text' value='$cost'></td>\n";

      $output_str.="</tr></table>\n";
      echo $output_str;
      $termcount++;

   }

}


      $output_str="<table width = '900' style='border:1px solid black;'>\n";
      $output_str.="<tbody id='tb'>\n";
      #$output_str.="<tr bgcolor ='#C1C1E8'>";
      #$output_str.="<td>&nbsp;</td>";
      #$output_str.="<td width='125'>Name</td>";
      #$output_str.="<td>Z Number</td>";
      #$output_str.="<td>Team-Group</td>";
      #$output_str.="<td width='4'>FTE</td>";
      #$output_str.="<td>Start Date</td>";
      #$output_str.="<td>&nbsp;</td>";
      #$output_str.="</tr>";
      $output_str.="</tbody>";
      $output_str.="<tr>";
      $output_str.="<td colspan='6'>";
      $output_str.="<a href='javascript:;' class='btn btn-danger' id='addmore'><i class='fa fa-fw fa-plus-circle'></i> Add More</a>";
      $output_str.="<button type='submit' name='save' id='save' value='save' class='btn btn-primary'><i class='fa fa-fw fa-save'></i> Save</button>";
      $output_str.="</td>";
      $output_str.="</tr>";
      $output_str.="</tbody>";
      $output_str.="</table>\n";

      echo $output_str;








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
      echo "<script>window.open('update_workpackageinfo.php?program_name=$program&currentYear=$currentYear','_self') </script>";
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
                    #echo "<br>$insert_query<br>";
               $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
               $txt=$insert_query;
               fwrite($myfile, $txt);
               fclose($myfile);
                    $db->query($insert_query);
            
            }
         }
      }










        if(isset($_POST['task_name_txt']))
        {
            foreach($task_name_txt as $key=>$task_name)
            {
               $program=$program_txt[$key];
               $project=$project_txt[$key];
               $task_manager=$task_manager_txt[$key];
               $task_description=$task_description_txt[$key];
               $burden_rate=$burden_rate_txt[$key];
               $target=$target_txt[$key];
               #$startdate=$start_date_txt[$key];
               #$startdate=$start_date_txt[$key];
               #$enddate=$end_date_txt[$key];
               $update_query="UPDATE tbl_wp_info 
                              SET
                              program= '$program',
                              project = '$project',
                              task_name = '$task_name',
                              task_manager = '$task_manager',
                              task_description = '$task_description',
                              burden_rate = '$burden_rate',
                              target = '$target'
                              WHERE wp_id = $key; ";
               echo $update_query;
               $db->query($update_query);
               #$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
               #$txt=$update_query;
               #fwrite($myfile, $txt);
               #fclose($myfile);
                              
            }
        echo "<meta http-equiv='refresh' content='0'>";
        echo "<script>window.open('update_workpackageinfo.php?search=$name&currentYear=$currentYear','_self') </script>";
       }
}


?>
<?php
require 'template/footer.html';
?>




		
    </div> <!--/.container-->
	
	
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    
</body>
</html>
