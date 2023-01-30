<?php include_once('config2.php');
if(isset($_REQUEST['action']) and $_REQUEST['action']=="addDataRow"){
$currentYear=date("Y");
?>
<tr>

   <td>&nbsp;</td>
   <td width='300' valign='top'><input type="staff_name" name="staff_name[]" class="form-control" required="required"></td>
   <td valign='top'><input type="znumber" name="znumber[]" class="form-control" required="required"></td>
   
   <td width=300 valign='top'>
   <select name="team_names[]" id="team_names" data-size="100" required="required">
   	<option value="">Select</option>
   	<?php
   	   $result=$db->query("SELECT team_id,team_name,group_name FROM vw_team_mapping where enddate is NULL order by team_name ");
   	    while($val  =   $result->fetch_assoc())
               {
                   $group_team_str=$val['team_name']."   ".$val['group_name'];
   	?>
   	<option value="<?php echo $val['team_id']?>"><?php echo $group_team_str?></option>
   	<?php }?>
   </select>
   </td>
   
   
   <td valign='top'><input type="forcasted" name="forcasted[]" class="form-control" required="required"></td>
   <td width=150 valign='top'><input type="startdate" name="startdate[]" class="form-control" required="required"></td>
   
   <td valign='top' align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" onclick="if(confirm('Are you sure to remove?')){$(this).closest('tr').remove();}" class="btn btn-danger"><i class="fa fa-fw fa-trash-alt"></i></button></td>
</tr>
	<?php
	echo '|***|addmore';
}


