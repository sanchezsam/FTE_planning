<?php 
include_once('config2.php'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_workpackage_managers($name)
{
   $currentYear=date("Y");
   $query="SELECT tbl_workpackage.wp_id, tbl_wp_manager.manager_name,tbl_workpackage.workpackage_name,tbl_workpackage.forcasted_fte_total FROM tbl_workpackage,tbl_wp_manager where tbl_workpackage.manager_id=tbl_wp_manager.manager_id and tbl_workpackage.workpackage_name='$name' and YEAR(tbl_workpackage.enddate)=$currentYear order by tbl_workpackage.enddate desc ";
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
  <script src="script_dir/script_workpackage.js"></script>








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
       $query=get_workpackage_managers($name);
       $result=mysqli_query($conn,$query);
   }




		#$result	=	$db->query("SELECT workpackage_name,forcasted_amount FROM vw_fte_mapping WHERE staff_name='$name'");
		#while($row  =   $result->fetch_assoc()){
	#		$workpackage_name[$row['workpackage_name']]	=	$val['workpackage_name'];
	#	}
		?>
		<script>
		$(document).ready(function(e) {
			$('.selectpicker').selectpicker();
			
			$('body').on('mousemove',function(){
				$('[data-toggle="tooltip"]').tooltip();
			});
			
			$("#addmore").on("click",function(){
				$.ajax({
					type:'POST',
					url:'action-form_updateWP.php',
					data:{'action':'addDataRow'},
					success: function(data){
						$('#tb').append(data);
						$('.selectpicker').selectpicker('refresh');
						$('#save').removeAttr('hidden',true);
					}
				});
			});
			
			$("#form").on("submit",function(){
				$.ajax({
					type:'POST',
					url:'action-form.ajax.php',
					data:$(this).serialize(),
					success: function(data){
						var a	=	data.split('|***|');
						if(a[1]=="add"){
							$('#mag').html(a[0]);
							setTimeout(function(){location.reload();},1500);
						}
					}
				});
			});
			
		});
		</script>




		<div id="msg"></div>
		<form id="form" method="post" ACTION="update_wp.php?search=<?php echo $name?>">
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
   $output_str="";
   $currentColor="";



   $output_str.="<table width = '900' style='border:1px solid black;'>\n";
   $output_str.="<tr bgcolor ='#C1C1E8'>\n";
   $output_str.="<td valign='top'><b>Workpackage</b></td>\n";
   $output_str.="<td valign='top'><b>Manager</b></td>\n";
   $output_str.="<td valign='top'><b>Total Allowed FTEs</b></td>\n";
   #$output_str.="<td valign='top'><b>Start Date</b></td>\n";
   #$output_str.="<td valign='top'><b>End Date</b></td>\n";
   $output_str.="</tr><tbody id='tb'>\n";
   $total=0;
   while($row=mysqli_fetch_array($result))
   {
      $wp_id=$row[0];
      $manager_name=$row[1];
      $workpackage_name=$row[2];
      #$startdate=$row[2];
      #$enddate=$row[3];
      $forcasted_fte_total=$row[3];
      $pass="T";
      #if(($previousName != $wp_name))
      #{
      #    #This is the first display for this term, calculate the tr background color ??
      #    $currentColor= ${'colour' .($termcount % 2)};
      #    $termcount++;
      #    $previousName = $wp_name;
      #}
      ##Change row color for records that are closed
      #if($currentDate>=strtotime($enddate))
      #{
      #    $currentColor=$old_color;
      #    $font_color=$change_font_color;
      #    $pass="F";
      #}
      #else
      #{

      #  $total+=$percent;
      #}


      #if($pass=="F")
      #{
      #    $output_str.="<tr bgcolor='$currentColor'>\n<td width=350 valign='top'><font color='$font_color'>$workkpackage_name</font></td>\n";
      #    $output_str.="<td valign='top'><font color='$font_color'>$manager_name</font></td>\n";
      #    $output_str.="<td valign='top'><font color='$font_color'>$forcasted_fte_total</font></td>\n";
      #    $output_str.="<td valign='top'><font color='$font_color'>$startdate</font></td>\n";
      #    $output_str.="<td valign='top'><font color='$font_color'>$enddate</font></td>\n";
      #}
      #else
      #{
      $output_str.="<tr bgcolor='$currentColor'>\n<td>$workpackage_name</td>\n";
      $output_str.="<td valign='top'>$manager_name</td>\n";
      $output_str.="<td valign='top'><input name='wp_txt[$wp_id]' type='text' value='$forcasted_fte_total'></td>\n";
      #$output_str.="<td valign='top'>$startdate</td>\n";
      #$output_str.="<td valign='top'>$enddate</td>\n";
      #}

      #$pass="T";

      $output_str.="</tr>\n";
   }
   
      $output_str.="</tbody><tr>";
      $output_str.="<td colspan='6'>";

      #$output_str.="<table width = '900' style='border:1px solid black;'>\n";
      #$output_str.="<tr bgcolor ='#C1C1E8'>\n";  
      #$output_str.="<td>Workpacakge</td>";
      #$output_str.="<td>Manager</td>";
      #$output_str.="<td>FTEs</td>";
      #$output_str.="</tr>";
      $output_str.="<a href='javascript:;' class='btn btn-danger' id='addmore'><i class='fa fa-fw fa-plus-circle'></i> Add More</a>";
      $output_str.="<button type='submit' name='save' id='save' value='save' class='btn btn-primary'><i class='fa fa-fw fa-save'></i> Save</button>";
      $output_str.="</td>";
      $output_str.="</tr>";
   $output_str.="</table>\n";
   #$output_str.="<table><tr><td rowspan='4'>Total FTEs $total</td></tr></table>";
   echo $output_str;

}
      
      # $output_str.="<table width = '900' style='border:1px solid black;'>\n";
      # $output_str.="<tr bgcolor ='#C1C1E8'>\n";
      # $output_str.="<td valign='top'><b>Workpackage</b></td>\n";
      # $output_str.="<td valign='top'><b>Manager</b></td>\n";
      # $output_str.="<td valign='top'><b>Total Allowed FTEs</b></td>\n";
      #$output_str.="</tr>";
      #$output_str.="</tr>";
      #$output_str.="<td colspan='3'>";
      #$output_str.="<a href='javascript:;' class='btn btn-danger' id='addmore'><i class='fa fa-fw fa-plus-circle'></i> Add More</a>";
      #$output_str.="<button type='submit' name='save' id='save' value='save' class='btn btn-primary'><i class='fa fa-fw fa-save'></i> Save</button>";
      #$output_str.="</td>";
      #$output_str.="</tr>";
      #$output_str.="</table>";
      #echo $output_str;

   ?>
                </form>
                <div class="clearfix"></div>

<?php
if(isset($_POST['save'])){
        extract($_REQUEST);
        extract($_POST);
        if (is_array($managers) || is_object($managers))
        {
            foreach($managers as $key=>$manager_id){
                    $forcasted=$forcast[$key];
                    $workpackage_name=$workpackage[$key];
                    #get wp_id
                    #$staff_name = $_POST['staff_name'];
                    #echo "<input type='hidden' name='search' value='$name'>";
                    #$result=$db->query("SELECT staff_id FROM tbl_staff where staff_name LIKE '%$staff_name'");
                    #while($val  =   $result->fetch_assoc())
                    #{
                    #    $staff_id=$val['staff_id'];
                    #}
                    $currentDate=date("Y-m-d");
                    #$currentYear=date("Y");
                    $insert_query="INSERT INTO tbl_workpackage (wp_id,workpackage_name,startdate,enddate,manager_id,forcasted_fte_total) VALUES (NULL, '$workpackage_name','$currentDate','$currentYear-$endFYIDate',$manager_id,$forcasted);";
                    #echo "<br>$insert_query";
#$myfil    e = fopen("newfile.txt", "w") or die("Unable to open file!");
#$txt=$    insert_query;
#fwrite    ($myfile, $txt);
#fclose    ($myfile);
                    $db->query($insert_query);

            }
       }
        #Refresh
        echo "<meta http-equiv='refresh' content='0'>";
        foreach($wp_txt as $key=>$forcasted)
        {
           $update_query="UPDATE tbl_workpackage SET forcasted_fte_total = '$forcasted'  WHERE wp_id = $key; ";
           #echo $update_query;
           $db->query($update_query);
                          
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
