<?php 
include_once('config2.php'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_workpackage_info($name)
{
   $currentYear=date("Y");
   $query="SELECT * 
           FROM tbl_wp_info
           WHERE task='$name'
                 and YEAR(enddate)=$currentYear";
   return $query;
}


?>
<!doctype html>
<html lang="en-US" class="no-js">

<body>

  <?php
    $search_str=display_search_box("Enter workpackage in the search box");
    echo $search_str;
  ?>
  <script src="script_dir/jquery.min.js"></script>
  <script src="script_dir/script_workpackage_info.js"></script>

    	<hr>
		<div class="clearfix"></div>
		<?php
   $name="";
   $managers="";
   if(isset($_POST['search'])){
       $name=$_POST['search'];
   }
   if($name=="")
   {
     if(isset($_GET['search'])){
         $name=$_GET['search'];
     }
   }
   if($name!="")
   {
       $query=get_workpackage_info($name);
       $result=mysqli_query($conn,$query);
   }

	?>

<div id="msg"></div>
<form id="form" method="post" ACTION="update_workpackageinfo.php?search=<?php echo $name?>">
<input type="hidden" name="action" value="saveAddMore">
<input type="hidden" name="search" value="<?php echo $name;?>">
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

   $output_str.="<table width = '900' border='1' style='border:1px solid black;'>\n";
   $output_str.="<tr bgcolor ='#C1C1E8'>\n";
   $output_str.="<td valign='top'><b>Task</b></td>\n";
   $output_str.="<td valign='top'><b>Task Name</b></td>\n";
   $output_str.="<td valign='top'><b>Task Manager</b></td>\n";
   $output_str.="<td valign='top'><b>Task Description</b></td>\n";
   $output_str.="<td valign='top'><b>Burden Rate</b></td>\n";
   $output_str.="<td valign='top'><b>Start Date</b></td>\n";
   $output_str.="<td valign='top'><b>End Date</b></td>\n";
   $output_str.="</tr>\n";
   $output_str.="<tbody id='tb'>\n";
   $output_str.="<tr bgcolor='$currentColor'>\n";
   while($row=mysqli_fetch_array($result))
   {
      $wp_id=$row[0];
      $task=$row[1];
      $task_name=$row[2];
      $task_manager=$row[3];
      $task_description=$row[4];
      $burden_rate=$row[5];
      $start_date=$row[6];
      $end_date=$row[7];
      $output_str.="<td valign='top'>$task</td>\n";
      $output_str.="<td valign='top'><textarea name='task_name_txt[$wp_id]' rows='3'>$task_name</textarea></td>\n";
      $output_str.="<td valign='top'>\n";
       $output_str.="<select name='task_manager_txt[$wp_id]' id='task_managers' data-size='10' required='required'>\n";
       $output_str.="<option value=''>Select</option>\n";
       $result_managers= $db->query("SELECT manager_name FROM tbl_wp_manager order by manager_name asc ");
       while($row=mysqli_fetch_array($result_managers))
       {
          if($task_manager==$row[0])
          {
              $output_str.="<option value='$row[0]' selected='true'>$row[0]</option>\n";
          }
          else
          {
              $output_str.="<option value='$row[0]'>$row[0]</option>\n";
          }
       }

       $output_str.="</select>\n";
       $output_str.="</td>\n";

      $output_str.="<td valign='top'><textarea name='task_description_txt[$wp_id]' rows='6'>$task_description</textarea></td>\n";
      $output_str.="<td valign='top'><input name='burden_rate_txt[$wp_id]' type='text' value='$burden_rate'></td>\n";
      $output_str.="<td valign='top'><input name='start_date_txt[$wp_id]' type='text' value='$start_date'></td>\n";
      $output_str.="<td valign='top'><input name='end_date_txt[$wp_id]' type='text' value='$end_date'></td>\n";
   }
      $output_str.="</tr>\n";
      $output_str.="</tbody><tr>";
      $output_str.="<td colspan='6'>";
      #$output_str.="<a href='javascript:;' class='btn btn-danger' id='addmore'><i class='fa fa-fw fa-plus-circle'></i> Add More</a>";
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
if(isset($_POST['save'])){
        extract($_REQUEST);
        extract($_POST);
        #Refresh
        #var_dump($_POST);
        echo "<meta http-equiv='refresh' content='0'>";
        if(isset($_POST['task_name_txt']))
        {
            foreach($task_name_txt as $key=>$task_name)
            {
               $task_manager=$task_manager_txt[$key];
               $task_description=$task_description_txt[$key];
               $burden_rate=$burden_rate_txt[$key];
               $startdate=$start_date_txt[$key];
               $startdate=$start_date_txt[$key];
               $enddate=$end_date_txt[$key];
               $update_query="UPDATE tbl_wp_info 
                              SET task_name = '$task_name',
                              task_manager = '$task_manager',
                              task_description = '$task_description',
                              burden_rate = '$burden_rate',
                              startdate = '$startdate',
                              enddate = '$enddate'
                              WHERE wp_id = $key; ";
               echo $update_query;
               $db->query($update_query);
               #$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
               #$txt=$update_query;
               #fwrite($myfile, $txt);
               #fclose($myfile);
                              
            }
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
