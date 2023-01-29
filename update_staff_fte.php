<?php 
include_once('config2.php'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_staff_fte($name)
{
   $currentYear=date("Y");
   $query="";
   if($name){
   $pieces = explode("->", $name);
   $name=$pieces[0];
   $team=$pieces[1];
   $group=$pieces[2];
   $query="SELECT vw_fte_mapping.fte_id,vw_fte_mapping.workpackage_name,vw_fte_mapping.forcasted_amount,vw_fte_mapping.startdate,vw_fte_mapping.enddate,vw_staff_mapping.staff_id FROM `vw_fte_mapping`,vw_staff_mapping where vw_fte_mapping.staff_name='$name' and vw_staff_mapping.staff_id= vw_fte_mapping.staff_id and vw_staff_mapping.team_name='$team' and vw_staff_mapping.group_name='$group' order by vw_fte_mapping.enddate desc";


   #$query="SELECT fte_id,workpackage_name,forcasted_amount,startdate,enddate FROM vw_fte_mapping where staff_name LIKE '%$name' and YEAR(enddate)>=$currentYear order by enddate desc";
   #echo $query;
   }
   return $query;
}



?>
<!doctype html>
<html lang="en-US" class="no-js">

<body>

<div class="container">
    <div class="row mt-4">
      <div class="col-md-8 mx-auto bg-light rounded p-4">
        <hr class="my-1">
        <h5 class="text-center text-secondary">Enter staff name in the search box</h5>
        <form action="" method="post" class="p-3">
          <div class="input-group">
            <input type="text" name="search" id="search" class="form-control form-control-lg rounded-0 border-info" placeholder="Search..." autocomplete="off" required>
            <div class="input-group-append">
              <input type="submit" name="submit" value="Search" class="btn btn-info btn-lg rounded-0">
            </div>
          </div>
        </form>
      </div>
      <div class="col-md-5" style="position: relative;margin-top: -72px;margin-left: 174px;">

        <div class="list-group" id="show-list">
          <!-- Here autocomplete list will be display -->
        </div>
      </div>
    </div>
  </div>
  <script src="jquery.min.js"></script>
  <script src="script_update_fte.js"></script>








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
   if($name!=""){
       $query=get_staff_fte($name);
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
					url:'action-form.ajax.php',
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
		<form id="form" method="post" ACTION="update_staff_fte.php?search=<?php echo $name?>">
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



   $output_str="<table><tr><td rowspan='4'>$name $currentYear Forcast</td></tr></table>";
   $output_str.="<table width = '900' style='border:1px solid black;'>\n";
   $output_str.="<tr bgcolor ='#C1C1E8'>\n";
   $output_str.="<td valign='top'><b>Charge Code</b></td>\n";
   $output_str.="<td valign='top'><b>Percent</b></td>\n";
   $output_str.="<td valign='top'><b>Start Date</b></td>\n";
   $output_str.="<td valign='top'><b>End Date</b></td>\n";
   $output_str.="</tr><tbody id='tb'>\n";

  $total=0;
   while($row=mysqli_fetch_array($result))
   {
      $fte_id=$row[0];
      $wp_name=$row[1];
      $percent=$row[2];
      $startdate=$row[3];
      $enddate=$row[4];
      $staff_id=$row[5];
      #echo "<br>SELECT enddate FROM `tbl_staff` where staff_id=$staff_id";
      $result_staff_id = $db->query("SELECT enddate FROM `tbl_staff` where staff_id=$staff_id");
      while($val  =   $result_staff_id->fetch_assoc()){
                     $staff_enddate = $val['enddate'];
      }
      $pass="T";
      if(($previousName != $wp_name))
      {
          #This is the first display for this term, calculate the tr background color ??
          $currentColor= ${'colour' .($termcount % 2)};
          $termcount++;
          $previousName = $wp_name;
      }
      #Change row color for records that are closed
      if($currentDate>=strtotime($enddate) or $staff_enddate!="")
      {
          $currentColor=$old_color;
          $font_color=$change_font_color;
          $pass="F";
      }
      else
      {

        $total+=$percent;
      }


      if($pass=="F")
      {
          $output_str.="<tr bgcolor='$currentColor'>\n<td width=350 valign='top'><font color='$font_color'>$wp_name</font></td>\n";
          $output_str.="<td width=210 valign='top'><font color='$font_color'>$percent</font></td>\n";
          $output_str.="<td valign='top'><font color='$font_color'>$startdate</font></td>\n";
          $output_str.="<td valign='top'><font color='$font_color'>$enddate</font></td>\n";
      }
      else
      {
          $output_str.="<tr bgcolor='$currentColor'>\n<td width=350 valign='top'><input name='wp_txt[$fte_id]' type='text' value='$wp_name'></td>\n";
          $output_str.="<td width=210 valign='top'><input name='forcast_txt[$fte_id]' type='text' value=$percent></td>\n";
          $output_str.="<td valign='top'>$startdate</td>\n";
          $output_str.="<td valign='top'>$enddate</td>\n";
      }

      $pass="T";

      $output_str.="</tr>\n";
   }
   
      $output_str.="</tbody><tr>";
      if($staff_enddate=="")
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
if(isset($_POST['save'])){
        extract($_REQUEST);
        extract($_POST);
        foreach($wp as $key=>$wp_id){
                $forcasted_value=$forcast[$key];
                #get wp_id
                $staff_name = $_POST['staff_name'];
                echo "<input type='hidden' name='search' value='$name'>";
                #$result=$db->query("SELECT staff_id FROM tbl_staff where staff_name LIKE '%$staff_name'");
                $pieces = explode("->", $staff_name);
                $name=$pieces[0];
                $team=$pieces[1];
                $group=$pieces[2];
                #$result=$db->query("SELECT staff_id FROM tbl_staff where staff_name LIKE '%$staff_name'");
                $result_id=$db->query("SELECT staff_id FROM vw_staff_mapping where staff_name LIKE '%$name' and team_name='$team' and group_name='$group'");
                #echo "<br>SELECT staff_id FROM vw_staff_mapping where staff_name LIKE '%$name' and team_name='$team' and group_name='$group'";
                while($val  =   $result_id->fetch_assoc())
                {
                    $staff_id=$val['staff_id'];
                }
                $currentDate=date("Y-m-d");
                $currentYear=date("Y");

                $insert_query="INSERT INTO tbl_fte_planning (fte_id,staff_id,wp_id,forcasted_amount,startdate,enddate) VALUES (NULL, '$staff_id','$wp_id', $forcasted_value, '$currentDate', '$currentYear-10-12');";
                #echo "<br>$insert_query";
                $db->query($insert_query);

        }
        #Refresh
        echo "<meta http-equiv='refresh' content='0'>";
        foreach($wp_txt as $key=>$wp_name)
        {
           foreach($forcast_txt as $key2=>$for_value)
           {
            if($key==$key2){ 
               $wp_result=$db->query("SELECT wp_id,fte_id,workpackage_name,forcasted_amount FROM vw_fte_mapping WHERE fte_id='$key'");
               while($val  =   $wp_result->fetch_assoc())
               {
                   
                       #$forcast_txt=$forcast_txt[$key];
                       $wp_id=$val['wp_id'];
                       $current_forcasted=$val['forcasted_amount'];
                       if($for_value!=$current_forcasted)
                       {
                          #echo "Updating";
                          #$update_query="UPDATE `tbl_fte_planning` SET `forcasted_amount` = '$for_value' WHERE `tbl_fte_planning`.`fte_id` = $key; ";
                          $currentDate=date("Y-m-d");
                          $currentYear=date("Y");
                          $update_query="UPDATE tbl_fte_planning SET enddate = '$currentDate'  WHERE tbl_fte_planning.fte_id = $key; ";
                          echo $update_query;
                          $db->query($update_query);
                          
                          if($for_value>0)
                          {
                              $staff_name = $_POST['staff_name'];
                              echo $staff_name;
                              $pieces = explode("->", $staff_name);
                              $name=$pieces[0];
                              $team=$pieces[1];
                              $group=$pieces[2];
                              #$result=$db->query("SELECT staff_id FROM tbl_staff where staff_name LIKE '%$staff_name'");
                              $result_id=$db->query("SELECT staff_id FROM vw_staff_mapping where staff_name LIKE '%$name' and team_name='$team' and group_name='$group'");
                              echo "<br>SELECT staff_id FROM vw_staff_mapping where staff_name LIKE '%$name' and team_name='$team' and group='$group'";
                              while($val  =   $result_id->fetch_assoc())
                              {
                                  $staff_id=$val['staff_id'];
                              }
                              $insert_query="INSERT INTO tbl_fte_planning (fte_id,staff_id,wp_id,forcasted_amount,startdate,enddate) VALUES (NULL, '$staff_id','$wp_id', $for_value, '$currentDate', '$currentYear-10-12');";
                              #echo $insert_query;

                              $db->query($insert_query);
                          }
                        }
                }
           }
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
