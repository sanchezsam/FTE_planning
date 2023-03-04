<?php 
include_once('config2.php'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';



function get_znumber($conn,$wp_staff_id)
{  
   $name=""; 
   $query="SELECT znumber FROM tbl_wp_staff  WHERE wp_staff_id='$wp_staff_id'";
   $result=mysqli_query($conn,$query);
   while($row=mysqli_fetch_array($result))
   { 
     $znumber=$row[0];
   }
   return $znumber;
}


function get_workpackage_id($conn,$project,$task,$currentYear)
{
   $wp_id='';
   $query="SELECT wp_id FROM tbl_wp_info  WHERE project='$project' and task='$task' and YEAR(enddate)='$currentYear'";
   $result=mysqli_query($conn,$query);
   while($row=mysqli_fetch_array($result))
   {
     $wp_id=$row[0];
   }
   return $wp_id;
}



function get_znumber_from_name($conn,$name)
{  
   $query="SELECT distinct znumber FROM tbl_staff_info  WHERE name like '%$name%'";
   #echo $query;
   $result=mysqli_query($conn,$query);
   while($row=mysqli_fetch_array($result))
   { 
     $znumber=$row[0];
   }
   #echo $query;
   return $znumber;
}


function cal_cost($conn,$znumber,$percent,$currentYear)
{

  $query="SELECT staff_cost FROM `tbl_staff_info` WHERE znumber='$znumber' and YEAR(enddate)='$currentYear'";
  $result=mysqli_query($conn,$query);
  #$info= "$percent $query";
  #$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
  #fwrite($myfile, $info);
  #fclose($myfile);

  while($row=mysqli_fetch_array($result))
   {
     $cost=$row[0];
   }

  $cost=floatval($cost)*floatval($percent);
  return round($cost,2);
}

function get_salary($conn,$znumber,$currentYear)
{
   $query="SELECT labor_pool,job_title FROM tbl_staff_info WHERE znumber='$znumber' and YEAR(enddate)='$currentYear'";
   $result=mysqli_query($conn,$query);
   $salary_min="";
   $salary_max="";
   while($row=mysqli_fetch_array($result))
   {
      $title=$row[0];
      $str = str_replace(' ', '', $title);
      $pattern = "{([^0-9]+)([0-9]+)(\-)([0-9]+\.[0-9]+)}";

      if (preg_match($pattern, $str, $matches)) {
          $title="$matches[1] ($row[1])";
          $salary_min=$matches[2];
          $salary_max=$matches[4];
      }
   }
   return array($salary_min,$salary_max);
}


function get_staff_fte($name,$currentYear)
{
   #$currentYear=date("Y");
   $query="";
   if($name){
   $pieces = explode("->", $name);
   $name=$pieces[0];
   $team=$pieces[1];
   $group=$pieces[2];
   $query="SELECT wp_staff_id,
                     project,
                     task,
                     funded_percent,
                     tbl_wp_info.startdate,
                     tbl_wp_info.enddate,
                     tbl_staff_info.znumber
             FROM `tbl_wp_staff`,tbl_wp_info,tbl_staff_info
             where tbl_wp_staff.wp_id=tbl_wp_info.wp_id
             and tbl_staff_info.znumber=tbl_wp_staff.znumber
             and tbl_staff_info.name like '%$name%'
             and tbl_staff_info.team_name ='$team'
             and tbl_staff_info.group_name ='$group'
             and YEAR(tbl_wp_staff.enddate)=$currentYear
             group by project,task
             ";
   }
   #echo $query;
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
     $drop_down_str=drop_down_year_basic($conn);
     echo $drop_down_str;
?>
<script>
function refreshPage(passValue,search){
//do something in this function with the value
 window.location="update_staff_fte.php?currentYear="+passValue
}
</script>


  <?php

    $search_str=display_search_box("Enter Staff Name in the search box");
    echo $search_str;
  ?>
  <script src="script_dir/jquery.min.js"></script>
  <script src="script_dir/script_update_fte.js"></script>
    	<hr>
		<div class="clearfix"></div>
		<?php
   $name="";
   if(isset($_POST['search'])){
       $name=$_POST['search'];
       $search_name=$_POST['search'];
   }
   if($name=="")
   {
     if(isset($_GET['search'])){
         $name=$_GET['search'];
         $search_name=$_GET['search'];
     }
   }
   if($name!=""){
       $query=get_staff_fte($name,$currentYear);
       $result=mysqli_query($conn,$query);
   }
?>



		<div id="msg"></div>
		<form id="form" method="post" ACTION="update_staff_fte.php?search=<?php echo $search_name?>&currentYear=<?php echo $currentYear?>">
			<input type="hidden" name="action" value="saveAddMore">
			<input type="hidden" name="staff_name" value="<?php echo $name;?>">
			<input type="hidden" name="znumber" value="<?php echo $znumber;?>">
			<input type="hidden" name="search" value="<?php echo $name;?>">
<?php
if($name!="")
{
   $termcount=0;
   $previousName="";
   $currentDate=date("Y/m/d");
   #$currentYear=date("Y");
   #$currentDate=strtotime($currentDate);



   $output_str="<table><tr><td rowspan='4'>$name $currentYear Forcast</td></tr></table>";
   $output_str.="<table width = '900' style='border:1px solid black;'>\n";
   $output_str.="<tr bgcolor ='#C1C1E8'>\n";
   $output_str.="<td valign='top'><b>Project</b></td>\n";
   $output_str.="<td valign='top'><b>Task</b></td>\n";
   $output_str.="<td valign='top'><b>Percent</b></td>\n";
   $output_str.="<td valign='top'><b>Start Date</b></td>\n";
   $output_str.="<td valign='top' colspan='2'><b>End Date</b></td>\n";
   $output_str.="</tr><tbody id='tb'>\n";

  $total=0;
   $staff_enddate="";
   $enddate="";
   while($row=mysqli_fetch_array($result))
   {
      $wp_staff_id=$row[0];
      $project=$row[1];
      $task=$row[2];
      $percent=$row[3];
      $startdate=$row[4];
      $enddate=$row[5];
      $znumber=$row[6];
      $pass="T";
      $wp_name="$project $task";
      if(($previousName != $wp_name))
      {
          #This is the first display for this term, calculate the tr background color ??
          $currentColor= ${'colour' .($termcount % 2)};
          $termcount++;
          $previousName = $wp_name;
      }
      $total+=$percent;


          $output_str.="<tr bgcolor='$currentColor'>\n";
          $output_str.="<td width=210 valign='top'>$project</td>\n";
          $output_str.="<td width=210 valign='top'>$task</td>\n";
          $output_str.="<td width=210 valign='top'><input name='forcast_txt[$wp_staff_id]' type='text' value=$percent></td>\n";
          $output_str.="<td valign='top'>$startdate</td>\n";
          $output_str.="<td valign='top'>$enddate</td>\n";
          $output_str.="<td valign='top' align='center' class='text-danger'><a href='update_staff_fte.php?search=$name&delete_id=$wp_staff_id&search=$search_name&proj=$project&task=$task&currentYear=$currentYear'>";
      $output_str.="<button type='button' data-toggle='tooltip' data-placement='right' class='btn btn-danger'><i class='fa fa-fw fa-trash-alt'></i></button></a></td>";

      $output_str.="</tr>\n";
   }
   
      $output_str.="</tbody><tr>";
      if($currentDate<=strtotime($enddate) or $staff_enddate=="")
      {
      $output_str.="<td colspan='6'>";
      $output_str.="<a href='javascript:;' class='btn btn-danger' id='addmore'><i class='fa fa-fw fa-plus-circle'></i> Add More</a>";
      $output_str.="<button type='submit' name='save' id='save' value='save' class='btn btn-primary'><i class='fa fa-fw fa-save'></i> Save</button>";
      $output_str.="</td>";
      }
      $output_str.="</tr>";
   $output_str.="</table>\n";
   $output_str.="<table><tr><td rowspan='4'>Total FTEs $total</td></tr></table>";
   echo $output_str;

}

   ?>
                </form>
                <div class="clearfix"></div>

<?php

if(isset($_GET['delete_id'])){
      $wp_staff_id=$_GET['delete_id'];
      $project=$_GET['proj'];
      $task=$_GET['task'];
      $name=$_GET['search'];
      $delete_query="DELETE from tbl_wp_staff where wp_staff_id='$wp_staff_id'";
      #echo $delete_query;
      #$db->query($delete_query);
      if($db->query($delete_query))
      {
         $deleteMsg="Deleted: $name from $proj $task ";
         echo ' <script type="text/javascript">
              alert("'.$deleteMsg.'");
              </script>';
      }
      echo "<script>window.open('update_staff_fte.php?search=$name&currentYear=$currentYear','_self') </script>";
  }



if(isset($_POST['save'])){
        extract($_REQUEST);
        extract($_POST);
        #Refresh
        #var_dump($_POST);
        $projects="";
        if (is_array($wp) || is_object($wp))
           {
                $errorMsg="";
                $insertMsg="";
                foreach($wp as $key=>$work_pack){
                        #get wp_id
                        $staff_name = $_POST['search'];
                        $pieces = explode("->", $staff_name);
                        $staff_name=$pieces[0];
                        $funded_percent=$forcast[$key];
                        $znumber=get_znumber_from_name($conn,$staff_name);               
                        if($znumber!="")
                        {
                            if($funded_percent==0)
                            {
                                $total_cost="0";
                            }
                            else
                            {
                                $total_cost=cal_cost($conn,$znumber,$funded_percent,$currentYear);
                            }
         
                        }

                        $wp_pieces=explode(" ", $work_pack);
                        $project=$wp_pieces[0];
                        $task=$wp_pieces[1];
                        $wp_id=get_workpackage_id($conn,$project,$task,$currentYear);
                        $select_query="SELECT * FROM `tbl_wp_staff`,tbl_wp_info
                                       where tbl_wp_staff.wp_id=tbl_wp_info.wp_id
                                       AND tbl_wp_info.project='$project' 
                                       and tbl_wp_info.task='$task' 
                                       and tbl_wp_staff.znumber ='$znumber';
                                       ";
                        #echo $select_query;
                        $result=$db->query($select_query);
                        $count=mysqli_num_rows($result);

                        #$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
                        #fwrite($myfile, $select_query);
                        #fwrite($myfile, $count);
                        #fclose($myfile);

                        if ($count==0)
                        {
                                $currentDate=date("m-d");
                                $startYear=$currentYear-1;
                                $currentDate="$startYear-$currentDate";
                                $endDateValue="$currentYear-$endFYIDate";
                                $insert_query="INSERT INTO tbl_wp_staff
                                               (wp_id,znumber,name,startdate,enddate,salary_min,salary_max,total_cost,funded,funded_percent,cost,pct_fte) 
                                              VALUES ('$wp_id','$znumber','$staff_name','$currentDate','$endDateValue',
                                                       '$salary_min','$salary_max','$total_cost','Yes','$funded_percent','$total_cost','$funded_percent');";
                                echo "<br>$insert_query<br>";
                                $db->query($insert_query);
                                $insertMsg.=" $work_pack,";
                         }
                         else
                         {
                            $insert_query="";
                            $errorMsg.=" $work_pack,";
                            #echo "Error: " . $query . "<br>" . $conn->error;
                         }
                           $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
                           $txt=$insert_query;
                           fwrite($myfile, $txt);
                           fclose($myfile);

                }
                if($errorMsg)
                {
                     $errorMsg="$staff_name already added to $errorMsg";
                     echo ' <script type="text/javascript">
                          alert("'.$errorMsg.'");
                          </script>';
                 }

                if($insertMsg)
                {
                     $insertMsg="Added: $name to $insertMsg";
                     echo ' <script type="text/javascript">
                          alert("'.$insertMsg.'");
                          </script>';
                 }
       echo "<meta http-equiv='refresh' content='0'>";
       echo "<script>window.open('update_staff_fte.php?search=$name&currentYear=$currentYear','_self') </script>";
           }
       










       if(isset($_POST['forcast_txt']))
        {
            foreach($forcast_txt as $key=>$funded_percent)
            {
               $funded="Yes";
               $znumber=get_znumber($conn,$key);               
               $total_cost=cal_cost($conn,$znumber,$funded_percent,$currentYear);
               $update_query="UPDATE tbl_wp_staff 
                              SET 
                              funded = '$funded',
                              total_cost = '$total_cost',
                              pct_fte = '$funded',
                              cost = '$total_cost',
                              funded_percent = '$funded_percent'
                              WHERE wp_staff_id = $key; ";
               #echo "$update_query<br>";
               $db->query($update_query);
               $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
               $txt=$update_query;
               fwrite($myfile, $txt);
               fclose($myfile);
       echo "<meta http-equiv='refresh' content='0'>";
       echo "<script>window.open('update_staff_fte.php?search=$name&currentYear=$currentYear','_self') </script>";

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
