<?php 
include_once('config2.php'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_staff_details($name,$currentYear)
{
   #$currentYear=date("Y");
   $query="";
   if($name){
      $name=trim($name);
      $query="SELECT staff_id,
                     name as 'Name',
                     znumber as 'Z#',
                     labor_pool as 'Labor Pool',
                     job_title as 'Title',
                     fte_amount as 'Total FTEs',
                     group_code as 'Group Code',
                     group_name as 'Group Name',
                     team_name as 'Team Name',
                     startdate as 'Start Date',
                     enddate as 'End Date'
              FROM tbl_staff_info 
              where name LIKE '%$name%'
                    and YEAR(enddate)='$currentYear'";
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
<script>
function refreshPage(passValue,search){
//do something in this function with the value
 window.location="update_add_staff.php?currentYear="+passValue
}
</script>

  <?php




    $search_str=display_search_box("Enter Staff Name in the search box");
    echo $search_str;
  ?>

  <script src="script_dir/jquery.min.js"></script>
  <script src="script_dir/script_add_staff.js"></script>

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
       $query=get_staff_details($name,$currentYear);
       $result=mysqli_query($conn,$query);
   }



?>



<div id="msg"></div>
<form id="form" method="post" ACTION="update_add_staff.php?search=<?php echo $name?>&currentYear=<?php echo $currentYear?>">
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


   $row_str1="<tr bgcolor ='$column_color'>\n";
   $row_str1.="<td valign='top'><b>Z#</b></td>\n";
   $row_str1.="<td valign='top'><b>Name</b></td>\n";
   $row_str1.="<td valign='top'><b>Labor Pool</b></td>\n";
   $row_str1.="<td valign='top'><b>Title</b></td>\n";
   $row_str1.="<td colspan='2' valign='top'><b>Total FTEs</b></td>\n";
   $row_str1.="</tr>\n";


   $row_str2="<tr bgcolor ='$column_color'>\n";
   $row_str2.="<td valign='top'><b>Group Code</b></td>\n";
   $row_str2.="<td valign='top'><b>Group Name</b></td>\n";
   $row_str2.="<td valign='top'><b>Team Name</b></td>\n";
   $row_str2.="<td valign='top'><b>Start Date</b></td>\n";
   $row_str2.="<td colspan='2' valign='top'><b>End Date</b></td>\n";
   $row_str2.="</tr>\n";





   #$output_str.="<tbody id='tb'>\n";

  $previousId=0;
   while($row=mysqli_fetch_array($result))
   {
      $staff_id=$row[0];
      $staff_name=$row[1];
      $znumber=$row[2];
      $labor_pool=$row[3];
      $job_title=$row[4];
      $forcasted=$row[5];
      $group_code=$row[6];
      $group_name=$row[7];
      $team_name=$row[8];
      $startdate=$row[9];
      $enddate=$row[10];
      $pass="T";
      if(($previousId != $staff_id))
      {
          #This is the first display for this term, calculate the tr background color ??
          $currentColor= ${'colour' .($termcount % 2)};
          $termcount++;
          $previousId = $staff_id;
      }

          $output_str.=$row_str1;
          $output_str.="<tr bgcolor='$currentColor'>\n<td width=10 valign='top'>$znumber</td>\n";
          $output_str.="<td width=200 valign='top'>$staff_name</td>\n";
          #$output_str.="<td width=200 valign='top'><input name='staff_name_txt[$staff_id]' type='text' value=$staff_name></td>\n";
          $query="SELECT DISTINCT labor_pool FROM tbl_job_family order by labor_pool;";

          $drop_down_name="<select name='labor_pool_txt[$staff_id]' id='labor_pool' width='4'>\n";
          $output_str.=generate_select_list($db,$query,$labor_pool,$drop_down_name);

          #$output_str.="<td width=10 valign='top'>$title</td>\n";
          $query="SELECT DISTINCT job_title FROM tbl_job_family order by job_title;";
          $drop_down_name="<select name='job_title_txt[$staff_id]' id='job_title' width='4'>\n";
          $output_str.=generate_select_list($db,$query,$job_title,$drop_down_name);

          $output_str.="<td colspan='2' width=4 valign='top'><input name='forcasted_txt[$staff_id]' type='text' value=$forcasted></td>\n";
          $output_str.="</tr>";
          $output_str.=$row_str2;


          #$output_str.="<td width=100 valign='top'>$group_code</td>\n";

          $query="SELECT DISTINCT group_code FROM tbl_staff_info order by group_code;";
          $drop_down_name="<select name='group_code_txt[$staff_id]' id='group_code' width='4'>\n";
          $output_str.=generate_select_list($db,$query,$group_code,$drop_down_name);


          #$output_str.="<td width=100 valign='top'>$group_name</td>\n";
          $query="SELECT DISTINCT group_name FROM tbl_staff_info order by group_name;";
          $drop_down_name="<select name='group_name_txt[$staff_id]' id='group_name' width='4'>\n";
          $output_str.=generate_select_list($db,$query,$group_name,$drop_down_name);

      
          $query="SELECT DISTINCT team_name FROM tbl_teams where enddate is null;";
          $drop_down_name="<select name='team_name_txt[$staff_id]' id='team_name' width='4'>\n";
          $output_str.=generate_select_list($db,$query,$team_name,$drop_down_name);

          $output_str.="<td width=400 valign='top'><input name='startdate_txt[$staff_id]' type='text' value=$startdate></td>\n";
          $output_str.="<td width=400 valign='top'><input name='enddate_txt[$staff_id]' type='text' value=$enddate></td>\n";
          $output_str.="<td valign='top' align='center' class='text-danger'><a href='update_add_staff.php?currentYear=$currentYear&delete_id=$staff_id&name=$name'>";
          $output_str.="<button type='button' data-toggle='tooltip' data-placement='right' class='btn btn-danger'><i class='fa fa-fw fa-trash-alt'></i></button></a></td>";


      $output_str.="</tr>\n";
   }
   
      #$output_str.="</tbody><tr>";
      $output_str.="</table>\n";
      echo $output_str;
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
###}

   ?>
                </form>
                <div class="clearfix"></div>

<?php

  if(isset($_GET['delete_id'])){
      $staff_id=$_GET['delete_id'];
      $name=$_GET['name'];
      $delete_query="DELETE from tbl_staff_info where staff_id='$staff_id'";
      $db->query($delete_query);
      if($db->query($delete_query))
      {
         $deleteMsg="Deleted: $name";
         echo ' <script type="text/javascript">
              alert("'.$deleteMsg.'");
              </script>';
      }
      echo "<script>window.open('update_add_staff.php?currentYear=$currentYear','_self') </script>";
  }




if(isset($_POST['save'])){
        extract($_REQUEST);
        extract($_POST);
        #print_r($_POST);
        $insertMsg="";
        $failedMsg="";
        if (is_array($staff_names) || is_object($staff_names))
        {
            #print_r($staff_names);
            foreach($staff_names as $key=>$staff){
                    
                    $znumber=$znumbers[$key];
                    $labor_pool=$labor_pools[$key];
                    $job_title=$job_titles[$key];
                    $forcasted=$forcasts[$key];
                    $team_name=$team_names[$key];
                    $group_code=$group_codes[$key];
                    $group_name=$group_names[$key];
                    $startdate=$startdates[$key];
                    $enddate=$enddates[$key];



                    #check if znumber exists
                    $select_query="select znumber from tbl_staff_info where znumber='$znumber'";
                    $result=$db->query($select_query);
                    $count=mysqli_num_rows($result);
                    if($count>0)
                    {
                       $failedMsg.="$znumber,";
                    }
                    else
                    { 
                        $insert_query="INSERT INTO tbl_staff_info
                                       (staff_id,znumber,name,labor_pool,job_title,fte_amount,team_name,group_code,
                                        group_name,startdate,enddate)
                                        VALUES (
                                       NULL,'$znumber','$staff','$labor_pool','$job_title','$forcasted',
                                             '$team_name','$group_code','$group_name','$startdate','$enddate');";

                        $myfile     = fopen("newfile.txt", "w") or die("Unable to open file!");
                        $txt=$insert_query;
                        fwrite(    $myfile, $txt);
                        fclose(    $myfile);
                        #echo "<br>$insert_query";
                        $db->query($insert_query);
                        $insertMsg.="$staff,";
                    }

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
          echo "<script>window.open('update_add_staff.php?search=$name&currentYear=$currentYear','_self') </script>";
        }
        if(isset($_POST['forcasted_txt'])){
            foreach($forcasted_txt as $key=>$forcasted)
            {
               $labor_pool=$labor_pool_txt[$key];
               $job_title=$job_title_txt[$key];
               $group_code=$group_code_txt[$key];
               $group_name=$group_name_txt[$key];
               $team_name=$team_name_txt[$key];
               $startdate=$startdate_txt[$key];
               $enddate=$enddate_txt[$key];
               $update_query="UPDATE tbl_staff_info 
                              SET labor_pool = '$labor_pool',
                              job_title = '$job_title',
                              group_code = '$group_code',
                              job_title = '$job_title',
                              fte_amount = '$forcasted',
                              group_code= '$group_code',
                              group_name = '$group_name',
                              team_name = '$team_name',
                              startdate = '$startdate',
                              enddate = '$enddate'
                              WHERE staff_id = $key; ";
               #$enddate=$enddate_txt[$key];
               #$update_query="UPDATE tbl_staff SET fte_amount = '$forcasted'  WHERE staff_id = $key; ";
               echo "<br>$update_query";
               #$db->query($update_query);
               #if($enddate==""){
               #   $update_query="UPDATE tbl_staff SET enddate = NULL  WHERE staff_id = $key; ";
               #}
               #else{
               #   $update_query="UPDATE tbl_staff SET enddate = '$enddate'  WHERE staff_id = $key; ";
               #}
               ##echo "<br>$update_query";
               $db->query($update_query);
                              
            }
       #Refresh
       echo "<meta http-equiv='refresh' content='0'>";
       echo "<script>window.open('update_add_staff.php?search=$name&currentYear=$currentYear','_self') </script>";
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
