<?php 
include_once('config2.php'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_manager_details($name,$currentYear)
{
   #$currentYear=date("Y");
   $query="";
   if($name){
      $name=trim($name);
      $query="SELECT access_id,
                     manager_id,
                     manager_name as 'Name',
                     userlevel as 'User Level',
                     project as 'Project',
                     task as 'Project',
                     startdate as 'Start Date',
                     enddate as 'End Date'
              FROM vw_wp_access 
              where manager_name LIKE '%$name%'";
   #echo $query;
   }
   return $query;
}



?>
<!doctype html>
<html lang="en-US" class="no-js">

<body>



  <?php

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
 window.location="update_add_managers.php?currentYear="+passValue
}

function confirmationDelete(anchor)
{
   var conf = confirm('Are you sure want to delete this record?');
   if(conf)
      window.location=anchor.attr("href");
}

</script>

  <?php




    $search_str=display_search_box("Enter Staff Name in the search box");
    echo $search_str;
  ?>

  <script src="script_dir/jquery.min.js"></script>
  <script src="script_dir/script_add_managers.js"></script>

 <hr>
<div class="clearfix"></div>
<?php
   $name="";
   if(isset($_POST['search'])){
       $name=$_POST['search'];
   }
   if($name=="")
   {
     if(isset($_GET['search'])){
         $name=$_GET['search'];
     }
   }
   if($name){
       $query=get_manager_details($name,$currentYear);
       $result=mysqli_query($conn,$query);
   }



?>



<div id="msg"></div>
<form id="form" method="post" ACTION="update_add_managers.php?search=<?php echo $name?>&currentYear=<?php echo $currentYear?>">
	<input type="hidden" name="action" value="saveAddMore">
	<input type="hidden" name="staff_name" value="<?php echo $name;?>">
	<input type="hidden" name="search" value="<?php echo $name;?>">
<?php
if($name!="")
{
   $termcount=0;
   $previousName="";
   #$currentDate=date("Y/m/d");
   #$currentYear=date("Y");
   #$currentDate=strtotime($currentDate);
   $font_color="black";



   $output_str="<table><tr><td rowspan='4'>$name information</td></tr></table>";
   $output_str.="<table width = '900' style='border:1px solid black;'>\n";
   #list($column_str,$columns)=get_mysql_columns($result);
   #$output_str.=$column_str;
   #mysqli_data_seek($result,0);


   $row_str="<tr bgcolor ='$column_color'>\n";
   $row_str.="<td width='250' valign='top'><b>Name</b></td>\n";
   $row_str.="<td valign='top'><b>User Level</b></td>\n";
   $row_str.="<td valign='top'><b>Project</b></td>\n";
   $row_str.="<td valign='top'><b>Task</></td>\n";
   $row_str.="<td valign='top'><b>Start Date</b></td>\n";
   $row_str.="<td colspan='2' valign='top'><b>End Date</b></td>\n";
   $row_str.="</tr>\n";





   #$output_str.="<tbody id='tb'>\n";

  $output_str.=$row_str;
  $previousId=0;
   while($row=mysqli_fetch_array($result))
   {
      $access_id=$row[0];
      $manager_id=$row[1];
      $staff_name=$row[2];
      $userlevel=$row[3];
      $project=$row[4];
      $task=$row[5];
      $startdate=$row[6];
      $enddate=$row[7];
      $pass="T";
      if(($previousId != $access_id))
      {
          #This is the first display for this term, calculate the tr background color ??
          $currentColor= ${'colour' .($termcount % 2)};
          $termcount++;
          $previousId = $access_id;
      }

          $output_str.="<tr bgcolor='$currentColor'>\n<td width=10 valign='top'>$staff_name</td>\n";
          #$query="SELECT DISTINCT userlevel FROM vw_wp_access order by userlevel;";
          #$drop_down_name="<select name='userlevel_txt[$access_id]' id='userlevel' width='4'>\n";
          #$output_str.=generate_select_list($db,$query,$userlevel,$drop_down_name);

          #$query="SELECT DISTINCT project FROM tbl_wp_info order by project;";
          #$drop_down_name="<select name='project_txt[$access_id]' id='job_title' width='4'>\n";
          #$output_str.=generate_select_list($db,$query,$project,$drop_down_name);

          #$query="SELECT DISTINCT task FROM tbl_wp_info order by task;";
          #$drop_down_name="<select name='task_txt[$access_id]' id='job_title' width='4'>\n";
          #$output_str.=generate_select_list($db,$query,$task,$drop_down_name);

          $output_str.="<td valign='top'>$userlevel</td>\n";
          $output_str.="<td valign='top'>$project</td>\n";
          $output_str.="<td valign='top'>$task</td>\n";
          $output_str.="<td valign='top'>$startdate</td>\n";
          $output_str.="<td valign='top'>$enddate</td>\n";

          $output_str.="<td valign='top' align='center' class='text-danger'><a onclick='javascript:confirmationDelete($(this));return false;' href='update_add_managers.php?currentYear=$currentYear&delete_id=$access_id&name=$name'>";
          $output_str.="<button type='button' data-toggle='tooltip' data-placement='right' class='btn btn-danger'><i class='fa fa-fw fa-trash-alt'></i></button></a></td>";

          $output_str.="</tr>";

   }
   
      #$output_str.="</tbody><tr>";
      $output_str.="</table>\n";
      echo $output_str;

      $output_str="<table width = '900' style='border:1px solid black;'>\n";
      $output_str.="<tbody id='tb'>\n";
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
}

   ?>
                </form>
                <div class="clearfix"></div>

<?php

  if(isset($_GET['delete_id'])){
      $access_id=$_GET['delete_id'];
      $name=$_GET['name'];
      $delete_query="DELETE from tbl_wp_access where access_id='$access_id'";
      $db->query($delete_query);
      if($db->query($delete_query))
      {
         $deleteMsg="Deleted: $access_id";
         echo ' <script type="text/javascript">
              alert("'.$deleteMsg.'");
              </script>';
      }
      echo "<script>window.open('update_add_managers.php?currentYear=$currentYear&search=$name','_self') </script>";
  }




if(isset($_POST['save'])){
        extract($_REQUEST);
        extract($_POST);
        #print_r($_POST);
        $insertMsg="";
        $failedMsg="";
        if (is_array($projects) || is_object($projects))
        {
            $insertMsg="Added $staff_name to ";
            foreach($projects as $key=>$wp_id){
                    
                    #$userlevel=$userlevels[$key];
                    #$startdate=date("Y/m/d");


                        $insert_query="INSERT INTO tbl_wp_access
                                       (access_id,manager_id,wp_id)
                                        VALUES (NULL,'$manager_id','$wp_id')";

                        $myfile     = fopen("newfile.txt", "w") or die("Unable to open file!");
                        $txt=$insert_query;
                        fwrite(    $myfile, $txt);
                        fclose(    $myfile);
                        echo "<br>$insert_query";
                        $db->query($insert_query);
                        $insertMsg.="$wp_id,";

            }
            if($insertMsg)
            {
                 $insertMsg="Added: $insertMsg";
                 echo ' <script type="text/javascript">
                      alert("'.$insertMsg.'");
                      </script>';
            }
            if($failedMsg)
            {
                 $failedMsg="Failed Inserting. znumbers exists: $failedMsg";
                 echo ' <script type="text/javascript">
                      alert("'.$failedMsg.'");
                      </script>';
            }
          #Refresh
          echo "<meta http-equiv='refresh' content='0'>";
          echo "<script>window.open('update_add_managers.php?search=$name&currentYear=$currentYear','_self') </script>";
       }
}


?>
<?php
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
