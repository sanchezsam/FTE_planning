<?php 
include_once('config2.php'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/lib.php';
require 'template/header.html';

function get_workpackage_activities($name,$currentYear)
{
   $query="SELECT tbl_wp_activities.*  
           FROM tbl_wp_info,tbl_wp_activities
           WHERE concat(tbl_wp_info.project,' ', tbl_wp_info.task)='$name'
                 and YEAR(tbl_wp_info.enddate)=$currentYear
                 and tbl_wp_activities.wp_id=tbl_wp_info.wp_id";
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
<script type="text/javascript">var searchYear = "<?php echo $currentYear; ?>";</script>
<input type="hidden" name="searchYear" value="<?php echo $currentYear;?>">
<script>
function refreshPage(passValue,search){
//do something in this function with the value
 window.location="update_workpackage_activities.php?currentYear="+passValue
}


function confirmationDelete(anchor)
{
   var conf = confirm('Are you sure want to delete this Activity?');
   if(conf)
      window.location=anchor.attr("href");
}
</script>



  <?php
    $search_str=display_search_box("Enter workpackage in the search box");
    echo $search_str;
  ?>
  <script src="script_dir/jquery.min.js"></script>
  <script src="script_dir/script_workpackage_activities.js"></script>

    	<hr>
		<div class="clearfix"></div>
		<?php
   $search_name="";
   $managers="";
   if(isset($_POST['search'])){
       $search_name=$_POST['search'];
       if(isset($_GET['currentYear']))
       {
          $currentYear=$_GET['currentYear'];
       }
   }
   if($search_name=="")
   {
     if(isset($_GET['search'])){
         $search_name=$_GET['search'];
     }
   }
   if($search_name!="")
   {
       $query=get_workpackage_activities($search_name,$currentYear);
       $result=mysqli_query($conn,$query);
   }

	?>

<div id="msg"></div>
<form id="form" method="post" ACTION="update_workpackage_activities.php?search=<?php echo $search_name?>&currentYear=<?php echo $currentYear?>">
<input type="hidden" name="action" value="saveAddMore">
<input type="hidden" name="search" value="<?php echo $search_name;?>">
<input type="hidden" name="currentYear" value="<?php echo $currentYear;?>">
<?php
if($search_name!="")
{

   if($verification=="True")
   {
       $wp_info = explode(" ", $search_name);
       $project=$wp_info[0];
       $task=$wp_info[1];
       if(isset($_SERVER['cn']))
       {
           $login_name=$_SERVER['cn'];
       }
       if(isset($_SERVER['REMOTE_USER']))
       {
           $login_name=$_SERVER['REMOTE_USER'];
       }
       $access_level=get_wp_access($conn,$login_name,$project,$task);
       $admin_level=get_wp_program_access($conn,$login_name);
       if($admin_level)
       {
          $access_level=$admin_level;
       }
       if($access_level==0)
       {
         exit("$login_name does not have access to $search_name");
       }

   }




   $termcount=0;
   $previousRow="";
   $znumbers=array();
   $currentDate=date("Y/m/d");
   #$currentYear=date("Y");
   $currentDate=strtotime($currentDate);
   $output_str="";
   $currentColor="";
   global $colour0, $colour1;
   global $column_color;

   $header="Update Activities in Workpackage: $search_name";
   $display_str=display_table_header($header);
   echo $display_str;
   $output_str.="<table width = '900' border='1' style='border:1px solid black;'>\n";
   $row_str="<tr bgcolor ='$column_color'>\n";
   $row_str.="<td valign='top'><b>Activity</b></td>\n";
   #$row_str.="<td valign='top'><b>Start Date</b></td>\n";
   #$row_str.="<td valign='top'><b>End Date</b></td>\n";
   $row_str.="<td valign='top'><b>Members</b></td>\n";
   $row_str.="<td valign='top' colspan='2'><b>Description</b></td>\n";
   $row_str.="</tr>\n";
   
   $output_str.="<tbody id='tb'>\n";
   $output_str.=$row_str;
   while($row=mysqli_fetch_array($result))
   {
      $activity_id=$row[0];
      $wp_id=$row[1];
      $activity=$row[2];
      $startdate=$row[3];
      $enddate=$row[4];
      $members=$row[5];
      $description=$row[6];
      $currentColor= ${'colour' .($termcount % 2)};
      $output_str.="<tr bgcolor='$currentColor'>\n";
      $output_str.="<input type='hidden' name='wp_id' value='$wp_id'>";

      $output_str.="<td valign='top'><input name='activity_txt[$activity_id]' type='text' value='$activity'></td>\n";
      #$output_str.="<td valign='top'><input name='startdate_txt[$activity_id]' type='text' value='$startdate'></td>\n";
      #$output_str.="<td valign='top'><input name='enddate_txt[$activity_id]' type='text' value='$enddate'></td>\n";
      $output_str.="<td valign='top' width='200'><input name='members_txt[$activity_id]' type='text' value='$members'></td>\n";
      $output_str.="<td valign='top'><textarea name='description_txt[$activity_id]' rows='2'>$description</textarea></td>\n";
      $output_str.="<td valign='top' align='center' class='text-danger'><a onclick='javascript:confirmationDelete($(this));return false;' href='update_workpackage_activities.php?search=$search_name&currentYear=$currentYear&delete_id=$activity_id'>";
     
      #$query="SELECT 'Yes' UNION ALL SELECT 'No';";
      #$drop_down_name="<select name='funded_txt[$wp_staff_id]' id='funded' width='4'>\n";
      #$output_str.=generate_select_list($db,$query,$funded,$drop_down_name);

      $output_str.="<button type='button' data-toggle='tooltip' data-placement='right' class='btn btn-danger'><i class='fa fa-fw fa-trash-alt'></i></button></a></td>";
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
      $activity_id=$_GET['delete_id'];
      $select_query="Select activity from tbl_wp_activities where activity_id='$activity_id'";
      $result=mysqli_query($conn,$select_query);
      while($row=mysqli_fetch_array($result))
      {
        $activity=$row[0];
      }

      $delete_query="DELETE from tbl_wp_activities where activity_id='$activity_id'";
      #echo $delete_query;
      $db->query($delete_query);
      if($db->query($delete_query))
      {
         $deleteMsg="Deleted Activity: $activity ";
         echo ' <script type="text/javascript">
              alert("'.$deleteMsg.'");
              </script>';
      }
      echo "<script>window.open('update_workpackage_activities.php?search=$search_name&currentYear=$currentYear','_self') </script>";
  }




if(isset($_POST['save'])){
        extract($_REQUEST);
        extract($_POST);
        #Refresh
        #var_dump($_POST);


       if (is_array($activity) || is_object($activity))
       {
            foreach($activity as $key=>$act){
                    #get wp_id
                    #$staff_name = $_POST['staff_name'];
                    #echo "SELECT wp_id FROM tbl_wp_info where concat(project,' ',task)='$search' and YEAR(enddate)='$currentYear'";
                    $result=$db->query("SELECT wp_id FROM tbl_wp_info where concat(project,' ',task)='$search' and YEAR(enddate)='$currentYear'");
                    while($val  =   $result->fetch_assoc())
                    {
                    #    $name=$val['name'];
                    #    $labor_pool=$val['labor_pool'];
                    #    $job_title=$val['job_title'];
                    #    $group_code=$val['group_code'];
                        $wp_id=$val['wp_id'];
                    }
                    #$currentYear=date("Y");
                    $startdate=date("Y-m-d");
                    #$enddate="$endFYIDate";
                    $enddate="$currentYear-$endMonth";
                    $member=trim($members[$key]);
                    $desc=$description[$key];
                    #$wp=$wp_id;
                    
                    #$wp=$key;
                    #echo "$act $member $desc $wp";
                    $insert_query="INSERT INTO tbl_wp_activities
                                   (wp_id,activity,startdate,enddate,members,description) 
                                   VALUES ('$wp_id','$act','$startdate','$enddate','$member','$desc');";
                    #echo "<br>$insert_query<br>";
                    $db->query($insert_query);

            }
            echo "<meta http-equiv='refresh' content='0'>";
            echo "<script>window.open('update_workpackage_activities.php?search=$search_name&currentYear=$currentYear','_self') </script>"; 
       }











        if(isset($_POST['activity_txt']))
        {
            foreach($activity_txt as $key=>$activity)
            {
               #$cost=$cost_txt[$key];
               #$total_cost=$total_cost_txt[$key];
               $description=$description_txt[$key];
               $members=$members_txt[$key];
               $description=$description_txt[$key];
               $update_query="UPDATE tbl_wp_activities
                              SET activity = '$activity',
                              members = '$members',
                              description = '$description'
                              WHERE activity_id = $key; ";
               #echo "$update_query<br>";
               $db->query($update_query);
               #$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
               #$txt=$update_query;
               #fwrite($myfile, $txt);
               #fclose($myfile);
                              
            }
       echo "<meta http-equiv='refresh' content='0'>";
       echo "<script>window.open('update_workpackage_activities.php?search=$search_name&currentYear=$currentYear','_self') </script>"; 
       }
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
