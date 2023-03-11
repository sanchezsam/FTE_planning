<?php 
include_once('config2.php'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/lib.php';
require 'template/header.html';

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

function get_workpackage_staff($name,$currentYear)
{
   $query="SELECT tbl_wp_staff.*  
           FROM tbl_wp_info,tbl_wp_staff
           WHERE concat(tbl_wp_info.project,' ', tbl_wp_info.task)='$name'
                 and YEAR(tbl_wp_info.enddate)=$currentYear
                 and tbl_wp_staff.wp_id=tbl_wp_info.wp_id
           ORDER by tbl_wp_staff.startdate";
   #echo $query;
   return $query;
}
function cal_cost($conn,$znumber,$percent,$currentYear)
{
  
  $query="SELECT staff_cost FROM `tbl_staff_info` WHERE znumber='$znumber' and YEAR(enddate)='$currentYear'";
  $result=mysqli_query($conn,$query);

  while($row=mysqli_fetch_array($result))
   {
     $cost=$row[0];
   }
  
  $cost=floatval($cost)*floatval($percent);
  return round($cost,2);
}


function get_name_from_znumber($conn,$znumber,$currentYear)
{
   $name=""; 
   $query="SELECT name FROM `tbl_staff_info` WHERE znumber='$znumber' and YEAR(enddate)='$currentYear'";
   #echo "$query<br>";
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
      else
      {
         $salary_min=0;
         $salary_max=0;
      }
      #print_r($titleArray);
      $output_str="<td valign='top'>$title</td>\n";
      $output_str.="<td valign='top'><input name='salary_min_txt[$wp_staff_id]' type='text' value='$salary_min'></td>\n";
      $output_str.="<td valign='top'><input name='salary_max_txt[$wp_staff_id]' type='text' value='$salary_max'></td>\n";
   }
   return $output_str; 
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
#function generate_select_list($db,$query,$selected_value,$drop_down_name)
#{
#
#      $output_str="<td valign='top'>\n";
#      $output_str.=$drop_down_name;
#      $output_str.="<option value=''>Select</option>\n";
#      $result= $db->query($query);
#      while($row=mysqli_fetch_array($result))
#      {
#          if($selected_value==$row[0])
#          {
#              #echo $row[0],$selected_value;
#              $output_str.="<option value='$row[0]' selected='true'>$row[0]</option>\n";
#          }
#          else
#          {
#              $output_str.="<option value='$row[0]'>$row[0]</option>\n";
#          }
#       }
#
#       $output_str.="</select>\n";
#       $output_str.="</td>\n";
#    return $output_str;
#
#}

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
 window.location="update_workpackage_staff.php?currentYear="+passValue
}
</script>

  <?php
    $search_str=display_search_box("Enter workpackage in the search box");
    echo $search_str;
  ?>
  <script src="script_dir/jquery.min.js"></script>
  <script src="script_dir/script_workpackage_info.js"></script>

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
       $query=get_workpackage_staff($search_name,$currentYear);
       $result=mysqli_query($conn,$query);
   }

	?>

<div id="msg"></div>
<form id="form" method="post" ACTION="update_workpackage_staff.php?search=<?php echo $search_name?>&currentYear=<?php echo $currentYear?>">
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
           $login=$_SERVER['cn'];
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
         exit("$login does not have access to $search_name");
       }

   }






   $termcount=0;
   $previousName="";
   $znumbers=array();
   $currentDate=date("Y/m/d");
   #$currentYear=date("Y");
   $currentDate=strtotime($currentDate);
   $output_str="";
   $currentColor="";

   $header="Update Staff in Workpackage: $search_name";
   $display_str=display_table_header($header);
   echo $display_str;
   $output_str.="<table width = '900' border='1' style='border:1px solid black;'>\n";
   $row_str1="<tr bgcolor ='$column_color'>\n";
   $row_str1.="<td valign='top'><b>Staff #</b></td>\n";
   #----$row_str1.="<td valign='top'><b>znumber</b></td>\n";
   $row_str1.="<td valign='top'><b>Name</b></td>\n";
   #----$row_str1.="<td valign='top'><b>StartDate</b></td>\n";
   #----$row_str1.="<td valign='top'><b>EndDate</b></td>\n";
   $row_str1.="<td valign='top'><b>Title</b></td>\n";
   $row_str1.="<td valign='top'><b>SalaryMin</b></td>\n";
   $row_str1.="<td valign='top'><b>SalaryMax</b></td>\n";
   $row_str1.="<td valign='top'><b>PCT FTE</b></td>\n";
   $row_str1.="</tr>\n";
   

   #$row_str2="<tr bgcolor ='#C1C1E8'>\n";
   $row_str2="<tr bgcolor ='$column_color'>\n";
   #$row_str2.="<td valign='top'><b>Group</b></td>\n";
   #$row_str2.="<td valign='top'><b>ORG</b></td>\n";
   $row_str2.="<td valign='top'><b>Cost</b></td>\n";
   $row_str2.="<td valign='top'><b>Funded</b></td>\n";
   $row_str2.="<td valign='top'><b>Notes</b></td>\n";
   $row_str2.="<td valign='top'><b>Funded %</b></td>\n";
   $row_str2.="<td valign='top' colspan='2'><b>Total Cost</b></td>\n";
   $row_str2.="</tr>\n";

   #list($column_str,$columns)=get_mysql_columns($result);
   #$output_str.=$column_str;
   #mysqli_data_seek($result,0);
   $output_str.="<tbody id='tb'>\n";
   while($row=mysqli_fetch_array($result))
   {
      $wp_staff_id=$row[0];
      $wp_id=$row[1];
      $znumber=$row[2];
      $name=$row[3];
      $startdate=$row[4];
      $enddate=$row[5];
      $title=$row[6];
      $salary_min=$row[7];
      $salary_max=$row[8];
      $group_name=$row[9];
      $org_code=$row[10];
      $pct_fte=$row[11];
      $cost=$row[12];
      $funded=$row[13];
      $funded_percent=$row[14];
      $total_cost=$row[15];
      $notes=$row[16];
      $currentColor= ${'colour' .($termcount % 2)};

      $output_str.=$row_str1;

      $output_str.="<tr bgcolor='$currentColor'>\n";
      $output_str.="<td valign='top'>$wp_staff_id</td>\n";
      #$output_str.="<td valign='top'>$wp_id</td>\n";
      #----$output_str.="<td valign='top'><input name='znumber_txt[$wp_staff_id]' type='text' value='$znumber' readonly></td>\n";
      if($znumber)
      {
          $znum2name=get_name_from_znumber($conn,$znumber,$currentYear); 
          if($name=="")
          {
             $name=$znum2name;
          }
      }
      $output_str.="<td valign='top'><input name='name_txt[$wp_staff_id]' type='text' value='$name'></td>\n";
      #----$output_str.="<td valign='top'><input name='start_date_txt[$wp_staff_id]' type='text' value='$startdate'></td>\n";
      #----$output_str.="<td valign='top'><input name='end_date_txt[$wp_staff_id]' type='text' value='$enddate'></td>\n";
      #----$output_str.="<td valign='top'><input name='title_txt[$wp_staff_id]' type='text' value='$title'></td>\n";
      #----$output_str.="<td valign='top'><input name='salary_min_txt[$wp_staff_id]' type='text' value='$salary_min'></td>\n";
      #----$output_str.="<td valign='top'><input name='salary_max_txt[$wp_staff_id]' type='text' value='$salary_max'></td>\n";
      if($znumber)
      {
         $output_str.=get_title_and_salary($conn,$znumber,$wp_staff_id,$currentYear);
      }
      else
      {

      $query="SELECT DISTINCT labor_pool FROM tbl_job_family;";
      $drop_down_name="<select name='funded_txt[$wp_staff_id]' id='funded' width='4'>\n";
      #echo "<br>$funded";
      $output_str.=generate_select_list($db,$query,$funded,$drop_down_name);
      $output_str.="<td valign='top'></td>\n";
      $output_str.="<td valign='top'></td>\n";

      }
      $output_str.="<td valign='top'><input name='pct_fte_txt[$wp_staff_id]' type='text' value='$pct_fte'></td>\n";
      $output_str.="</tr>";
      $output_str.=$row_str2;
      $output_str.="<tr bgcolor='$currentColor'>\n";
      ####$output_str.="<td valign='top'><input name='group_name_txt[$wp_staff_id]' type='text' value='$group_name'></td>\n";
      #####$output_str.="<td valign='top'><input name='org_code_txt[$wp_staff_id]' type='text' value='$org_code'></td>\n";
     
      $cost="$" . number_format(floatval($cost), 2, ".", ",");
      $output_str.="<td valign='top'><input name='cost_txt[$wp_staff_id]' type='text' value='$cost'></td>\n";
      #$output_str.="<td valign='top'><input name='funded_txt[$wp_staff_id]' type='text' value='$funded'></td>\n";

      $query="SELECT 'Yes' UNION ALL SELECT 'No';";
      $drop_down_name="<select name='funded_txt[$wp_staff_id]' id='funded' width='4'>\n";
      #echo "<br>$funded";
      $output_str.=generate_select_list($db,$query,$funded,$drop_down_name);


      $output_str.="<td valign='top'><textarea name='notes_txt[$wp_staff_id]' rows='2'>$notes</textarea></td>\n";
      $output_str.="<td valign='top'><input name='funded_percent_txt[$wp_staff_id]' type='text' value='$funded_percent'></td>\n";
      $total_cost="$" . number_format(floatval($total_cost), 2, ".", ",");
      $output_str.="<td valign='top'><input name='total_cost_txt[$wp_staff_id]' type='text' value='$total_cost'></td>\n";
      $output_str.="<td valign='top' align='center' class='text-danger'><a href='update_workpackage_staff.php?search=$search_name&currentYear=$currentYear&delete_id=$wp_staff_id&name=$name'>";
      #$output_str.="<button type='button' data-toggle='tooltip' data-placement='right' onclick='if(confirm(\"Are you sure to remove?\")){$(this).closest('tr').remove();}'";
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
      $wp_staff_id=$_GET['delete_id'];
      $name=$_GET['name'];
      $delete_query="DELETE from tbl_wp_staff where wp_staff_id='$wp_staff_id'";
      #echo $delete_query;
      $db->query($delete_query);
      if($db->query($delete_query))
      {
         $deleteMsg="Deleted: $name";
         echo ' <script type="text/javascript">
              alert("'.$deleteMsg.'");
              </script>';
      }
      echo "<script>window.open('update_workpackage_staff.php?search=$search_name&currentYear=$currentYear','_self') </script>";
  }




if(isset($_POST['save'])){
        extract($_REQUEST);
        extract($_POST);
        #Refresh
        #var_dump($_POST);


       if (is_array($znumbers) || is_object($znumbers))
       {
            $errorMsg="";
            $insertMsg="";
            $errorAlready="";
            foreach($znumbers as $key=>$znumber){
                    #get wp_id
                    #$staff_name = $_POST['staff_name'];
                    #echo "<input type='hidden' name='search' value='$name'>";
                    $select_query="SELECT * FROM tbl_staff_info where znumber ='$znumber' and YEAR(enddate)='$currentYear'";
                    $result=$db->query("SELECT * FROM tbl_staff_info where znumber ='$znumber' and YEAR(enddate)='$currentYear'");
                    $count=mysqli_num_rows($result);

                    #$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
                    #fwrite($myfile, $select_query);
                    #fwrite($myfile, $count);
                    #fclose($myfile);
           
                    if ($count>0)
                    {  
                        #list($salary_min,$salary_max)=get_salary($conn,$znumber,$currentYear);
                        #$total_cost=cal_cost($salary_min,$salary_max,$funded_percent);

                        $wp_pieces=explode(" ", $search_name);
                        $project=$wp_pieces[0];
                        $task=$wp_pieces[1];
                        $work_pack="$project $task";
                        $wp_id=get_workpackage_id($conn,$project,$task,$currentYear);
                        $select_query="SELECT * FROM `tbl_wp_staff`,tbl_wp_info
                                       where tbl_wp_staff.wp_id=tbl_wp_info.wp_id
                                       AND tbl_wp_info.project='$project' 
                                       and tbl_wp_info.task='$task' 
                                       and tbl_wp_staff.znumber ='$znumber';
                                       ";
                        #echo $select_query; 
                        $result_in=$db->query($select_query);
                        $in_count=mysqli_num_rows($result_in);            
                        #echo $in_count;
                        if($in_count==0) 
                        {     
                                while($val  =   $result->fetch_assoc())
                                {
                                    $name=trim($val['name']);
                                    $labor_pool=$val['labor_pool'];
                                    $job_title=$val['job_title'];
                                    $group_name=$val['group_name'];
                                }
                                $currentDate=date("Y-m-d");
                                #$currentY=date("Y");
                                $enddate="$currentYear-$endMonth";
                                #echo "<br>$znumber";
                                #echo "<br>$name";
                                #echo "<br>$labor_pool";
                                #echo "<br>$job_title";
                                #echo "<br>$group_code";
                                #echo "<br>$group_name";
                                

                                $insert_query="INSERT INTO tbl_wp_staff
                                               (wp_id,znumber,name,group_name,startdate,enddate) 
                                               VALUES ('$wp_id','$znumber','$name','$group_name','$currentDate','$enddate');";
                                #echo "<br>$insert_query<br>";
                                $db->query($insert_query);
                                $insertMsg.=" $name,";
                         }
                         else
                         {
                            $errorAlready.=" $znumber already in $work_pack";
                         }
                     }
                     else
                     {
                        $insert_query="";
                        #$insert_query="Failed insert: No staff with that znumber";
                        $errorMsg.=" $znumber";
                        #echo "Error: " . $query . "<br>" . $conn->error;
                     }
                       #$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
                       #$txt=$insert_query;
                       #fwrite($myfile, $txt);
                       #fclose($myfile);

            }
            if($errorMsg)
            {
                 $errorMsg="Invaild zumber('s): $errorMsg";
                 echo ' <script type="text/javascript">
                      alert("'.$errorMsg.'");
                      </script>';
             }
           if($errorAlready)
            {
                 $errorMsg="$errorAlready";
                 echo ' <script type="text/javascript">
                      alert("'.$errorMsg.'");
                      </script>';
             }
            if($insertMsg)
            {
                 $insertMsg="Added: $insertMsg";
                 echo ' <script type="text/javascript">
                      alert("'.$insertMsg.'");
                      </script>';
             }
       }











        if(isset($_POST['name_txt']))
        {
            foreach($name_txt as $key=>$name)
            {
               if(isset($salary_min_txt[$key]))
               {
                   $salary_min=$salary_min_txt[$key];
               }
               if(isset($salary_min_txt[$key]))
               {
                   $salary_max=$salary_max_txt[$key];
               }
               $name=$name_txt[$key];
               $znumber=get_znumber($conn,$key);

               $pct_fte=$pct_fte_txt[$key];
               $funded=$funded_txt[$key];
               
               $funded_percent=$funded_percent_txt[$key];
               if($znumber!="")
               {
                   if($pct_fte==0)
                   {
                       $cost=0;
                   }
                   else
                   {
                       $cost=cal_cost($conn,$znumber,$pct_fte,$currentYear);
                   }
                   if($funded=='Yes')
                   {
                      $total_cost=cal_cost($conn,$znumber,$funded_percent,$currentYear);
                      
                   }
                   else
                   {
                       $total_cost="0";
                   }
               }
               else
               {
                   $cost=$cost_txt[$key];
                   $replace=array('$',',');
                   $cost=str_replace($replace,"", $cost);
                   $total_cost=$total_cost_txt[$key];
                   $total_cost=str_replace($replace,"", $total_cost);
               }
               $notes=addslashes($notes_txt[$key]);
                     
               $update_query="UPDATE tbl_wp_staff 
                              SET pct_fte = '$pct_fte',
                              cost = '$cost',
                              name = '$name',
                              funded = '$funded',
                              funded_percent = '$funded_percent',
                              salary_min = '$salary_min',
                              salary_max = '$salary_max',
                              total_cost = '$total_cost',
                              notes = '$notes'
                              WHERE wp_staff_id = $key; ";
               echo "$update_query<br>";
               $db->query($update_query);
               #$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
               #$txt=$update_query;
               #fwrite($myfile, $txt);
               #fclose($myfile);
                              
            }
       }
       echo "<meta http-equiv='refresh' content='0'>";
       echo "<script>window.open('update_workpackage_staff.php?search=$search_name&currentYear=$currentYear','_self') </script>"; 
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
