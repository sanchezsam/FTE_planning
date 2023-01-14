<?php include_once('config.php');
if(isset($_REQUEST['action']) and $_REQUEST['action']=="addDataRow"){
	?>
	<tr>
		<td align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="if(confirm('Are you sure to remove?')){$(this).closest('tr').remove();}" class="btn btn-danger"><i class="fa fa-fw fa-trash-alt"></i></button></td>
		<td align="center"><?php echo date('Y-m-d H:i:s');?></td>
		<td><input type="text" name="username[]" class="form-control" required="required"></td>
		<td>
		<select name="usercountry[]" id="usercountry" class="form-control selectpicker" data-live-search="true" data-size="10" required="required">
			<option value="">Select</option>
			<?php
				$result	=	$db->query("SELECT * FROM tbl_teams WHERE 1 ORDER BY team_name ASC ");
				while($val  =   $result->fetch_assoc()){
				?>
				<option value="<?php echo $val['id']?>" data-subtext="(<?php echo $val['team_name']?>)"><?php echo mb_strtoupper($val['team_name'],'UTF-8')?></option>
			<?php }?>
		</select>
		</td>
		<td><input type="email" name="useremail[]" class="form-control" required="required"></td>
		<td><input type="text" name="userphone[]" class="form-control" required="required"></td>
	</tr>
	<?php
	echo '|***|addmore';
}


if(isset($_REQUEST['action']) and $_REQUEST['action']=="saveAddMore"){
	extract($_REQUEST);
	
	#foreach($username as $key=>$un){
#		$db->query('INSERT INTO reorderusers (username, useremail, userphone, usercountry) VALUES ("'.$un.'", "'.$useremail[$key].'", "'.$userphone[$key].'", "'.$usercountry[$key].'") ');
#	}
	echo '<div class="alert alert-success"><i class="fa fa-fw fa-thumbs-up"></i> Record added successfully!</div>|***|add';
}
