<?php include_once('config2.php');
if(isset($_REQUEST['action']) and $_REQUEST['action']=="addDataRow"){
$currentYear=date("Y");
	?>
	<tr>

		<td><input type="workpackage" name="workpackage[]" class="form-control" required="required"></td>

		<td>
		<select name="managers[]" id="managers" data-size="10" required="required">
			<option value="">Select</option>
			<?php
				$result	=	$db->query("SELECT manager_id,manager_name FROM tbl_wp_manager order by manager_name asc ");
				while($val  =   $result->fetch_assoc()){
				?>
				<option value="<?php echo $val['manager_id']?>"><?php echo $val['manager_name']?></option>
			<?php }?>
		</select>
		</td>


		<td><input type="forcast" name="forcast[]" class="form-control" required="required"></td>

		<td align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" onclick="if(confirm('Are you sure to remove?')){$(this).closest('tr').remove();}" class="btn btn-danger"><i class="fa fa-fw fa-trash-alt"></i></button></td>
	</tr>
	<?php
	echo '|***|addmore';
}


#if(isset($_REQUEST['action']) and $_REQUEST['action']=="saveAddMore"){
#	extract($_REQUEST);
#	extract($_POST);
#	foreach($wp as $key=>$wp_id){
#                $forcasted_value=$forcast[$key];
#                #get wp_id
#                $staff_name = $_POST['staff_name'];
#                $result=$db->query("SELECT staff_id FROM tbl_staff where staff_name LIKE '%$staff_name'");
#                while($val  =   $result->fetch_assoc())
#                {
#                    $staff_id=$val['staff_id'];
#                }
#
#                $insert_query="INSERT INTO tbl_fte_planning (fte_id,staff_id,wp_id,forcasted_amount,startdate,enddate) VALUES (NULL, '$staff_id','$wp_id', $forcasted_value, '2023-01-12', '2023-10-12');";
#                $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
#                $text=extract($_REQUEST);
#                fwrite($myfile, $text);
#                fclose($myfile);
#		#$db->query($insert_query);
#                
#	}
#	echo '<div class="alert alert-success"><i class="fa fa-fw fa-thumbs-up"></i> Record added successfully!</div>|***|add';
#}