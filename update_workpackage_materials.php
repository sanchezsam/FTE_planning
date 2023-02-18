<?php 
include_once('config2.php'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_workpackage_materials($name)
{
   $currentYear=date("Y");
   $query="SELECT tbl_wp_materials.*  
           FROM tbl_wp_info,tbl_wp_materials
           WHERE tbl_wp_info.task='$name'
                 and YEAR(tbl_wp_info.enddate)=$currentYear
                 and tbl_wp_materials.wp_id=tbl_wp_info.wp_id";
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
  <script src="script_dir/script_workpackage_materials.js"></script>

    	<hr>
		<div class="clearfix"></div>
		<?php
   $search_name="";
   $managers="";
   if(isset($_POST['search'])){
       $search_name=$_POST['search'];
   }
   if($search_name=="")
   {
     if(isset($_GET['search'])){
         $search_name=$_GET['search'];
     }
   }
   if($search_name!="")
   {
       $query=get_workpackage_materials($search_name);
       $result=mysqli_query($conn,$query);
   }

	?>

<div id="msg"></div>
<form id="form" method="post" ACTION="update_workpackage_materials.php?search=<?php echo $search_name?>">
<input type="hidden" name="action" value="saveAddMore">
<input type="hidden" name="search" value="<?php echo $search_name;?>">
<?php
if($search_name!="")
{
   $termcount=0;
   $previousRow="";
   $znumbers=array();
   $currentDate=date("Y/m/d");
   $currentYear=date("Y");
   $currentDate=strtotime($currentDate);
   $output_str="";
   $currentColor="";
   global $colour0, $colour1;
   global $column_color;

   $header="Update Materials in Workpackage: $search_name";
   $display_str=display_table_header($header);
   echo $display_str;
   $output_str.="<table width = '900' border='1' style='border:1px solid black;'>\n";
   $row_str1="<tr bgcolor ='$column_color'>\n";
   $row_str1.="<td valign='top'><b>Property #</b></td>\n";
   #$row_str.="<td valign='top'><b>Start Date</b></td>\n";
   #$row_str.="<td valign='top'><b>End Date</b></td>\n";
   $row_str1.="<td valign='top'><b>Description</b></td>\n";
   $row_str1.="<td valign='top'><b>Owner</b></td>\n";
   $row_str1.="<td valign='top'><b>Service Entry</b></td>\n";
   $row_str1.="<td valign='top'><b>Under Maint</b></td>\n";
   $row_str1.="<td valign='top' colspan='2'><b>Maint PO</b></td>\n";
   $row_str1.="</tr>\n";

   $row_str2="<tr bgcolor ='$column_color'>\n";
   $row_str2.="<td valign='top'><b>Pct Fous</b></td>\n";
   $row_str2.="<td valign='top'><b>Risk</b></td>\n";
   $row_str2.="<td valign='top'><b>Note</b></td>\n";
   $row_str2.="<td valign='top'><b>Replace Fund</b></td>\n";
   $row_str2.="<td valign='top'><b>Replacement Cost</b></td>\n";
   $row_str2.="<td valign='top' colspan='2'><b>Total Cost</b></td>\n";
   $row_str2.="</tr>\n";
   
   $output_str.="<tbody id='tb'>\n";
   #$output_str.=$row_str1;
   while($row=mysqli_fetch_array($result))
   {
      $material_id=$row[0];
      $wp_id=$row[1];
      $property_num=$row[2];
      $description=$row[3];
      $service_entry=$row[4];
      $owner=$row[5];
      $under_maint=$row[6];
      $maint_po=$row[7];
      $pct_fous=$row[8];
      $risk=$row[9];
      $replace_fund=$row[10];
      $replacement_cost=$row[11];
      $total_cost=$row[12];
      $notes=$row[13];
      $currentColor= ${'colour' .($termcount % 2)};
     
      $output_str.=$row_str1;
      $output_str.="<tr bgcolor='$currentColor'>\n";
      $output_str.="<input type='hidden' name='wp_id' value='$wp_id'>";

      $output_str.="<td valign='top' width='200'><input name='property_num_txt[$material_id]' type='text' value='$property_num'></td>\n";
      $output_str.="<td valign='top' width='250'><textarea name='description_txt[$material_id]' rows='2'>$description</textarea></td>\n";
      $output_str.="<td valign='top' width='275'><textarea name='owner_txt[$material_id]' rows='2'>$owner</textarea></td>\n";
      $output_str.="<td valign='top' width='275'><input name='service_entry_txt[$material_id]' type='text' value='$service_entry'></td>\n";


      $query="SELECT 'Yes' UNION ALL SELECT 'No';";
      $drop_down_name="<select name='under_maint_txt[$material_id]' id='under_maint' width='4'>\n";
      $output_str.=generate_select_list($db,$query,$under_maint,$drop_down_name);


      $output_str.="<td valign='top' width='150' colspan='2'><input name='maint_po_txt[$material_id]' type='text' value='$maint_po'></td>\n";
      $output_str.="</tr>";
      $output_str.=$row_str2;

      $output_str.="<tr bgcolor='$currentColor'>\n";
      $output_str.="<td valign='top' width='100'><input name='pct_fous_txt[$material_id]' type='text' value='$pct_fous'></td>\n";


      $query="SELECT '5' UNION ALL SELECT '4' UNION ALL SELECT '3' UNION ALL SELECT '2' UNION ALL SELECT '1';";
      $drop_down_name="<select name='risk_txt[$material_id]' id='risk' width='4'>\n";
      $output_str.=generate_select_list($db,$query,$risk,$drop_down_name);

      $output_str.="<td valign='top' width='350'><textarea name='notes_txt[$material_id]' rows='4'>$notes</textarea></td>\n";

      $query="SELECT 'Yes' UNION ALL SELECT 'No';";
      $drop_down_name="<select name='replace_fund_txt[$material_id]' id='replace_fund' width='4'>\n";
      $output_str.=generate_select_list($db,$query,$replace_fund,$drop_down_name);

      $output_str.="<td valign='top' width='150'><input name='replacement_cost_txt[$material_id]' type='text' value='$replacement_cost'></td>\n";
      $output_str.="<td valign='top' width='150'><input name='total_cost_txt[$material_id]' type='text' value='$total_cost'></td>\n";
      $output_str.="<td valign='top' align='center' class='text-danger'><a href='update_workpackage_materials.php?search=$search_name&delete_id=$material_id'>";
     
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
      $material_id=$_GET['delete_id'];
      $delete_query="DELETE from tbl_wp_materials where material_id='$material_id'";
      echo $delete_query;
      $db->query($delete_query);
      echo "<script>window.open('update_workpackage_materials.php?search=$search_name','_self') </script>";
  }




if(isset($_POST['save'])){
        extract($_REQUEST);
        extract($_POST);
        #Refresh
        #var_dump($_POST);


       if (is_array($description) || is_object($description))
       {
            foreach($description as $key=>$desc){
                    $currentYear=date("Y");
                    $service_entry=date("Y-m-d");
		    $property_number=$property_number[$key];
                    $owners=$owners[$key];
                    $under_maint=$under_maint[$key];
                    $maint_po=$maint_po[$key];
                    $pct_fous=$pct_fous[$key];
                    $risk=$risk[$key];
                    $replace_fund=$replace_fund[$key];
                    $replacement_cost=$replacement_cost[$key];
                    $note=$note[$key];
                    $wp=$wp_id;
                    #echo "$act $member $desc $wp";
                    if($replace_fund=='Yes')
                    {
                        $total_cost=$replacement_cost*$pct_fous;
                    }
                    else
                    {
                       $total_cost="0";
                    }
                    $insert_query="INSERT INTO tbl_wp_materials
                                   (wp_id,property_number,description,service_entry,owner,under_maintenance,
                                    maintenance_po,pct_fous,risk,replace_fund,replacement_cost,total_cost,notes) 
                                   VALUES ('$wp_id','$property_number','$desc','$service_entry','$owners','$under_maint',
                                           '$maint_po','$pct_fous','$risk','$replace_fund','$replacement_cost','$total_cost','$note');";
                    echo "<br>$insert_query<br>";
                    $db->query($insert_query);

            }
            echo "<meta http-equiv='refresh' content='0'>";
            echo "<script>window.open('update_workpackage_materials.php?search=$search_name','_self') </script>"; 
       }

        if(isset($_POST['description_txt']))
        {
            foreach($description_txt as $key=>$des)
            {
               $property_num=$property_num_txt[$key];
               $description=$description_txt[$key];
               $service_entry=$service_entry_txt[$key];
               $owner=$owner_txt[$key];
               $under_maint=$under_maint_txt[$key];
               $maint_po=$maint_po_txt[$key];
               $pct_fous=$pct_fous_txt[$key];
               $risk=$risk_txt[$key];
               $replace_fund=$replace_fund_txt[$key];
               $replacement_cost=$replacement_cost_txt[$key];
               $note=$notes_txt[$key];

               if($replace_fund=='Yes')
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
                   $total_cost=$replacement_cost*$pct_fous;
               }
               else
               {
                  $total_cost="0";
               }
               $des=$db->real_escape_string($des);;
               $note=$db->real_escape_string($note);;
               $update_query="UPDATE tbl_wp_materials
                              SET property_number='$property_num', 
                              description = '$des',
                              service_entry = '$service_entry',
                              owner = '$owner',
                              under_maintenance = '$under_maint',
                              maintenance_po = '$maint_po',
                              pct_fous= '$pct_fous',
                              risk = '$risk',
                              replace_fund = '$replace_fund',
                              replacement_cost = '$replacement_cost',
                              total_cost = '$total_cost',
                              notes = '$note'
                              WHERE material_id = $key; ";
               #echo "$update_query<br>";
               $db->query($update_query);
               #$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
               #$txt=$update_query;
               #fwrite($myfile, $txt);
               #fclose($myfile);
                              
            }
       echo "<meta http-equiv='refresh' content='0'>";
       echo "<script>window.open('update_workpackage_materials.php?search=$search_name','_self') </script>"; 
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
