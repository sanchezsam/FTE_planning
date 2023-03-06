<?php 
include_once('config2.php'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_workpackage_services($name,$currentYear)
{
   #$currentYear=date("Y");
   $query="SELECT tbl_wp_services.*  
           FROM tbl_wp_info,tbl_wp_services
           WHERE concat(tbl_wp_info.project,' ', tbl_wp_info.task)='$name'
                 and YEAR(tbl_wp_info.enddate)=$currentYear
                 and tbl_wp_services.wp_id=tbl_wp_info.wp_id";
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
 window.location="update_workpackage_services.php?currentYear="+passValue
}


function confirmationDelete(anchor)
{
   var conf = confirm('Are you sure want to delete this Service?');
   if(conf)
      window.location=anchor.attr("href");
}
</script>


  <?php
    $search_str=display_search_box("Enter workpackage in the search box");
    echo $search_str;
  ?>
  <script src="script_dir/jquery.min.js"></script>
  <script src="script_dir/script_workpackage_services.js"></script>

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
       $query=get_workpackage_services($search_name,$currentYear);
       $result=mysqli_query($conn,$query);
   }

	?>

<div id="msg"></div>
<form id="form" method="post" ACTION="update_workpackage_services.php?search=<?php echo $search_name?>&currentYear=<?php echo $currentYear?>">
<input type="hidden" name="action" value="saveAddMore">
<input type="hidden" name="search" value="<?php echo $search_name;?>">
<input type="hidden" name="currentYear" value="<?php echo $currentYear;?>">
<?php
if($search_name!="")
{
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

   $header="Update Services in Workpackage: $search_name";
   $display_str=display_table_header($header);
   echo $display_str;
   $output_str.="<table width = '900' border='1' style='border:1px solid black;'>\n";
   $row_str1="<tr bgcolor ='$column_color'>\n";
   $row_str1.="<td valign='top' colspan='2'><b>Description</b></td>\n";
   $row_str1.="<td valign='top'><b>Owner</b></td>\n";
   $row_str1.="<td valign='top'><b>Risk</b></td>\n";
   $row_str1.="<td valign='top'><b>PCT Fous</b></td>\n";
   $row_str1.="<td valign='top' colspan='2'><b>Cost</b></td>\n";
   $row_str1.="</tr>\n";
   $row_str2="<tr bgcolor ='$column_color'>\n";
   $row_str2.="<td valign='top' colspan='2'><b>Vendor</b></td>\n";
   $row_str2.="<td valign='top' colspan='2'><b>Note</b></td>\n";
   $row_str2.="<td valign='top'><b>Funded</b></td>\n";
   $row_str2.="<td valign='top' colspan='2'><b>Total Cost</b></td>\n";
   $row_str2.="</tr>\n";
   
   $output_str.="<tbody id='tb'>\n";
   while($row=mysqli_fetch_array($result))
   {
      $service_id=$row[0];
      $wp_id=$row[1];
      $description=$row[2];
      $startdate=$row[3];
      $enddate=$row[4];
      $owner=$row[5];
      $vendor=$row[6];
      $pct_fous=$row[7];
      $risk=$row[8];
      $funded=$row[9];
      $cost=$row[10];
      $total_cost=$row[11];
      $notes=$row[12];
      $currentColor= ${'colour' .($termcount % 2)};
      $output_str.=$row_str1;
      $output_str.="<tr bgcolor='$currentColor'>\n";
      $output_str.="<input type='hidden' name='wp_id' value='$wp_id'>";

      $output_str.="<td valign='top' width='250' colspan='2'><textarea name='description_txt[$service_id]' rows='1'>$description</textarea></td>\n";
      #$output_str.="<td valign='top' width='250'><textarea name='owner_txt[$service_id]' rows='2'>$owner</textarea></td>\n";
      $query="SELECT distinct trim(name) FROM tbl_staff_info where YEAR(enddate)='$currentYear' order by trim(name) asc";
      $drop_down_name="<select name='owner_txt[$service_id]' id='owner' width='4'>\n";
      $output_str.=generate_select_list($db,$query,$owner,$drop_down_name);


      $query="SELECT '5' UNION ALL SELECT '4' UNION ALL SELECT '3' UNION ALL SELECT '2' UNION ALL SELECT '1';";
      $drop_down_name="<select name='risk_txt[$service_id]' id='risk' width='4'>\n";
      $output_str.=generate_select_list($db,$query,$risk,$drop_down_name);

      $output_str.="<td valign='top' width='100'><input name='pct_fous_txt[$service_id]' type='text' value='$pct_fous'></td>\n";
      $output_str.="<td valign='top' width='150' colspan='2'><input name='cost_txt[$service_id]' type='text' value='$cost'></td>\n";

      $output_str.="</tr>";
      $output_str.=$row_str2;
      $output_str.="<tr bgcolor='$currentColor'>\n";

      $output_str.="<td valign='top' width='250' colspan='2'><textarea name='vendor_txt[$service_id]' rows='1'>$vendor</textarea></td>\n";
      $output_str.="<td valign='top' width='250' colspan='2'><textarea name='notes_txt[$service_id]' rows='2'>$notes</textarea></td>\n";

      $query="SELECT 'Yes' UNION ALL SELECT 'No';";
      $drop_down_name="<select name='funded_txt[$service_id]' id='funded' width='4'>\n";
      $output_str.=generate_select_list($db,$query,$funded,$drop_down_name);

      $output_str.="<td valign='top' width='150'><input name='total_cost_txt[$service_id]' type='text' value='$total_cost'></td>\n";
      $output_str.="<td valign='top' align='center' class='text-danger'><a  onclick='javascript:confirmationDelete($(this));return false;' href='update_workpackage_services.php?search=$search_name&currentYear=$currentYear&delete_id=$service_id'>";
     
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
      $service_id=$_GET['delete_id'];
      $select_query="Select description from tbl_wp_services where service_id='$service_id'";
      $result=mysqli_query($conn,$select_query);
      while($row=mysqli_fetch_array($result))
      {
        $description=$row[0];
      }
      $delete_query="DELETE from tbl_wp_services where service_id='$service_id'";
      #echo $delete_query;
      $db->query($delete_query);
      if($db->query($delete_query))
      {
         $deleteMsg="Deleted Service: $description ";
         echo ' <script type="text/javascript">
              alert("'.$deleteMsg.'");
              </script>';
      }
      echo "<script>window.open('update_workpackage_services.php?search=$search_name&currentYear=$currentYear','_self') </script>";
  }




if(isset($_POST['save'])){
        extract($_REQUEST);
        extract($_POST);
        #Refresh
        #var_dump($_POST);


       if (is_array($description) || is_object($description))
       {
            foreach($description as $key=>$desc){

                     $result=$db->query("SELECT wp_id FROM tbl_wp_info where concat(project,' ',task)='$search' and YEAR(enddate)='$currentYear'");
                    while($val  =   $result->fetch_assoc())
                    {
                        $wp_id=$val['wp_id'];
                    }
                    $startdate=date("Y-m-d");
                    $enddate="$currentYear-$endMonth";
                    $owners=$owners[$key];
                    $vendor=$vendor[$key];
                    $pct_fous=$pct_fous[$key];
                    $risk=$risk[$key];
                    $funded=$funded[$key];
                    $cost=$new_cost[$key];
                    $note=$new_note[$key];
                    if($funded=='Yes')
                    {
                        $total_cost=$cost*$pct_fous;
                    }
                    else
                    {
                       $total_cost="0";
                    }
                    $insert_query="INSERT INTO tbl_wp_services
                                   (wp_id,description,startdate,enddate,owner,vendor,pct_fous,risk,funded,cost,total_cost,notes) 
                                   VALUES ('$wp_id','$desc','$startdate','$enddate','$owners','$vendor','$pct_fous',
                                           '$risk','$funded','$cost','$total_cost','$note');";
                    echo "<br>$insert_query<br>";
                    $db->query($insert_query);

            }
            echo "<meta http-equiv='refresh' content='0'>";
            echo "<script>window.open('update_workpackage_services.php?search=$search_name&currentYear=$currentYear','_self') </script>"; 
       }

        if(isset($_POST['description_txt']))
        {
            foreach($description_txt as $key=>$des)
            {
               #$cost=$cost_txt[$key];
               #$total_cost=$total_cost_txt[$key];
               $owner=$owner_txt[$key];
               $vendor=$vendor_txt[$key];
               $pct_fous=$pct_fous_txt[$key];
               $risk=$risk_txt[$key];
               $funded=$funded_txt[$key];
               $cost=$cost_txt[$key];
               $note=$notes_txt[$key];
               if($funded=='Yes')
               {
                   if (str_contains($pct_fous, '%'))
                   {
                      
                      $pct_fous = str_replace('%', '', $pct_fous);
                      $pct_fous=$pct_fous/100;
                   }
                   if (!str_contains($pct_fous, '.')&& $pct_fous!=1)
                   {
                      
                      $pct_fous=$pct_fous/100;
                   }
                   $total_cost=$cost*$pct_fous;
               }
               else
               {
                  $total_cost="0";
               }
               $des=$db->real_escape_string($des);;
               $note=$db->real_escape_string($note);;
               $update_query="UPDATE tbl_wp_services
                              SET description = '$des',
                              owner = '$owner',
                              vendor = '$vendor',
                              pct_fous= '$pct_fous',
                              risk = '$risk',
                              funded = '$funded',
                              cost = '$cost',
                              total_cost = '$total_cost',
                              notes = '$note'
                              WHERE service_id = $key; ";
               #echo "$update_query<br>";
               $db->query($update_query);
               #$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
               #$txt=$update_query;
               #fwrite($myfile, $txt);
               #fclose($myfile);
                              
            }
       echo "<meta http-equiv='refresh' content='0'>";
       echo "<script>window.open('update_workpackage_services.php?search=$search_name&currentYear=$currentYear','_self') </script>"; 
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
