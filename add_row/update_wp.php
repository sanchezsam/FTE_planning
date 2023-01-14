<?php include_once('config.php'); ?>
<!doctype html>
<html lang="en-US" prefix="og: http://ogp.me/ns#" class="no-js">

<body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    	<hr>
		<div class="clearfix"></div>
		<?php
		$result	=	$db->query("SELECT workpackage_name,forcasted_amount FROM vw_fte_mapping WHERE staff_name='Sam Sanchez'");
		while($row  =   $result->fetch_assoc()){
			$workpackage_name[$row['workpackage_name']]	=	$val['workpackage_name'];
		}
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
		<form id="form" method="post" ACTION = "update_wp.php">
			<input type="hidden" name="action" value="saveAddMore">
			<input type="hidden" name="staff_name" value="Samuel Sanchez">
			<table border='1'>
					<tr>
						<td>WP</td>
						<td>Forcasted</td>
						<td></td>
					</tr>
				<tbody id="tb">
					<?php 
      
					$wp_result=$db->query("SELECT fte_id,workpackage_name,forcasted_amount FROM vw_fte_mapping WHERE staff_name LIKE '%Samuel Sanchez%'");
					if($wp_result->num_rows>0){
						$s	=	'';
						while($val  =   $wp_result->fetch_assoc()){
                                                        $fte_id=$val['fte_id'];
							 ?>
						<tr>
                                                        <td>
                                                          <input name="wp_txt[<?php echo $fte_id?>]" type="text" value="<?php echo( htmlspecialchars( $val['workpackage_name'] ) ); ?>" />
                                                        </td>
                                                        <td>
                                                        <input name="forcast_txt[<?php echo $fte_id?>]" type="text" value="<?php echo( htmlspecialchars( $val['forcasted_amount'] ) ); ?>" />
                                                        </td>

							<td>Delete Button</td>
						</tr>
						<?php
						}
					}else{ ?>
					<tr>
						<td colspan="6" class="bg-light text-center"><strong>No Record(s) Found!</strong></td>
					</tr>
					<?php }?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="6">
							<a href="javascript:;" class="btn btn-danger" id="addmore"><i class="fa fa-fw fa-plus-circle"></i> Add More</a>
							<button type="submit" name="save" id="save" value="save" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</button>
						</td>
					</tr>
				</tfoot>
			</table>
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
                $result=$db->query("SELECT staff_id FROM tbl_staff where staff_name LIKE '%$staff_name'");
                while($val  =   $result->fetch_assoc())
                {
                    $staff_id=$val['staff_id'];
                }

                $currentDate=date("Y-m-d");
                $currentY=date("Y");
                $insert_query="INSERT INTO tbl_fte_planning (fte_id,staff_id,wp_id,forcasted_amount,startdate,enddate) VALUES (NULL, '$staff_id','$wp_id', $forcasted_value, '$currentDate', '$currentYear-10-12');";
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
                          echo "Updating";
                          #$update_query="UPDATE `tbl_fte_planning` SET `forcasted_amount` = '$for_value' WHERE `tbl_fte_planning`.`fte_id` = $key; ";
                          $currentDate=date("Y-m-d");
                          $update_query="UPDATE tbl_fte_planning SET enddate = '$currentDate'  WHERE tbl_fte_planning.fte_id = $key; ";
                          echo $update_query;
                          $db->query($update_query);
   
                          $staff_name = $_POST['staff_name'];
                          $result=$db->query("SELECT staff_id FROM tbl_staff where staff_name LIKE '%$staff_name'");
                          while($val  =   $result->fetch_assoc())
                          {
                              $staff_id=$val['staff_id'];
                          }
                          $insert_query="INSERT INTO tbl_fte_planning (fte_id,staff_id,wp_id,forcasted_amount,startdate,enddate) VALUES (NULL, '$staff_id','$wp_id', $for_value, '2023-01-12', '2023-10-12');";
                          echo $insert_query;
                          $db->query($insert_query);
                        }
                }
           }
          }
        }
}



?>




		
    </div> <!--/.container-->
	
	
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    
</body>
</html>
