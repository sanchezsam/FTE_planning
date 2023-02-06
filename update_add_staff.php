<?php 
include_once('config2.php'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_staff_details($name)
{
   $currentYear=date("Y");
   $query="";
   if($name){
      $name=trim($name);
      $query="SELECT staff_id,
                     staff_name as 'Name',
                     znumber as 'Z#',
                     team_name as 'Team',
                     group_name as 'Group',
                     fte_amount as 'Total FTEs',
                     startdate as 'Start Date',
                     enddate as 'End Date'
              FROM vw_staff_mapping where staff_name LIKE '%$name%'";
      #echo $query;
   }
   return $query;
}



?>
<!doctype html>
<html lang="en-US" class="no-js">

<body>

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
       $query=get_staff_details($name);
       $result=mysqli_query($conn,$query);
   }



?>



<div id="msg"></div>
<form id="form" method="post" ACTION="update_add_staff.php?search=<?php echo $name?>">
	<input type="hidden" name="action" value="saveAddMore">
	<input type="hidden" name="staff_name" value="<?php echo $name;?>">
	<input type="hidden" name="search" value="<?php echo $name;?>">
<?php
if($name!="")
{
   $termcount=0;
   $previousName="";
   $currentDate=date("Y/m/d");
   $currentYear=date("Y");
   $currentDate=strtotime($currentDate);
   $font_color="black";



   $output_str="<table><tr><td rowspan='4'>$name information</td></tr></table>";
   $output_str.="<table width = '900' style='border:1px solid black;'>\n";
   list($column_str,$columns)=get_mysql_columns($result);
   $output_str.=$column_str;
   mysqli_data_seek($result,0);
   #$output_str.="<tbody id='tb'>\n";

  $previousId=0;
   while($row=mysqli_fetch_array($result))
   {
      $staff_id=$row[0];
      $staff_name=$row[1];
      $znumber=$row[2];
      $team=$row[3];
      $group=$row[4];
      $forcasted=$row[5];
      $startdate=$row[6];
      $enddate=$row[7];
      $pass="T";
      if(($previousId != $staff_id))
      {
          #This is the first display for this term, calculate the tr background color ??
          $currentColor= ${'colour' .($termcount % 2)};
          $termcount++;
          $previousId = $staff_id;
      }


          $output_str.="<tr bgcolor='$currentColor'>\n<td width=10 valign='top'>$staff_id</td>\n";
          #$output_str.="<td width=350 valign='top'><input name='staff_txt[$staff_id]' type='text' value='$staff_name'></td>\n";
          #$output_str.="<td width=210 valign='top'><input name='team_txt[$staff_id]' type='text' value=$team></td>\n";
          #$output_str.="<td width=210 valign='top'><input name='group_txt[$staff_id]' type='text' value=$group></td>\n";
          #$output_str.="<td width=210 valign='top'><input name='startdate_txt[$staff_id]' type='text' value=$startdate></td>\n";
          $output_str.="<td width=200 valign='top'>$staff_name</td>\n";
          $output_str.="<td width=10 valign='top'>$znumber</td>\n";
          $output_str.="<td width=100 valign='top'>$team</td>\n";
          $output_str.="<td width=100 valign='top'>$group</td>\n";
          $output_str.="<td width=4 valign='top'><input name='forcasted_txt[$staff_id]' type='text' value=$forcasted></td>\n";
          $output_str.="<td width=100 valign='top'>$startdate</td>\n";
          $output_str.="<td width=100 valign='top'><input name='enddate_txt[$staff_id]' type='text' value=$enddate></td>\n";


      $output_str.="</tr>\n";
   }
   
      #$output_str.="</tbody><tr>";
      $output_str.="</table>\n";
      echo $output_str;
}

      $output_str="<table width = '900' style='border:1px solid black;'>\n";
      $output_str.="<tbody id='tb'>\n";
      $output_str.="<tr bgcolor ='#C1C1E8'>";
      $output_str.="<td>&nbsp;</td>";
      $output_str.="<td width='125'>Name</td>";
      $output_str.="<td>Z Number</td>";
      $output_str.="<td>Team-Group</td>";
      $output_str.="<td width='4'>FTE</td>";
      $output_str.="<td>Start Date</td>";
      $output_str.="<td>&nbsp;</td>";
      $output_str.="</tr>";
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
if(isset($_POST['save'])){
        extract($_REQUEST);
        extract($_POST);
        echo "in save";
        #print_r($_POST);
        if (is_array($staff_name) || is_object($staff_name))
        {
            print_r($staff_name);
            foreach($staff_name as $key=>$staff_id){
                    $staff=$staff_name[$key];
                    $znum=$znumber[$key];
                    $team_id=$team_names[$key];
                    $start=$startdate[$key];
                    $forcasted_amount=$forcasted[$key];
                    #echo "<input type='hidden' name='search' value='$name'>";
                    $insert_query="INSERT INTO tbl_staff (staff_id,znumber,staff_name,team_id,startdate,enddate,fte_amount) VALUES (NULL,'$znum','$staff',$team_id, '$start',NULL,$forcasted_amount);";
#$myfile     = fopen("newfile.txt", "w") or die("Unable to open file!");
#$txt=$insert_query;
#fwrite(    $myfile, $txt);
#fclose(    $myfile);
                    echo "<br>$insert_query";
                    $db->query($insert_query);

            }
        }
        #Refresh
        echo "<meta http-equiv='refresh' content='0'>";
        if($forcasted_txt){
            foreach($forcasted_txt as $key=>$forcasted)
            {
            
               $enddate=$enddate_txt[$key];
               $update_query="UPDATE tbl_staff SET fte_amount = '$forcasted'  WHERE staff_id = $key; ";
               echo "<br>$update_query";
               $db->query($update_query);
               if($enddate==""){
                  $update_query="UPDATE tbl_staff SET enddate = NULL  WHERE staff_id = $key; ";
               }
               else{
                  $update_query="UPDATE tbl_staff SET enddate = '$enddate'  WHERE staff_id = $key; ";
               }
               #echo "<br>$update_query";
               $db->query($update_query);
                              
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
