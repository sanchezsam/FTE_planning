<?php include_once('config2.php');
require 'include/db.php';
if(isset($_REQUEST['action']) and $_REQUEST['action']=="addDataRow"){
$currentYear=date("Y");
?>
<tr><td colspan=10>
<table>
<tr bgcolor ='<?php echo $new_column_color ?>'>
<td valign='top'><b>Z#</b></td>
<td valign='top'><b>Name</b></td>
<td valign='top'><b>Labor Poot</b></td>
<td valign='top'><b>Title</b></td>
<td valign='top' colspan='2'><b>Total FTEs</b></td>
</tr>

 <tr>

   <td width='120' valign='top'><input type="znumbers" name="znumbers[]" class="form-control" required="required"></td>
   <td width='300' valign='top'><input type="staff_names" name="staff_names[]" class="form-control" required="required"></td>
   
   <td width=300 valign='top'>
   <select name="labor_pools[]" id="labor_pools" data-size="100" required="required">
   	<option value="">Select</option>
   	<?php
   	   $result=$db->query("SELECT DISTINCT labor_pool FROM tbl_job_family order by labor_pool");
   	    while($val  =   $result->fetch_assoc())
               {
   	?>
   	        <option value="<?php echo $val['labor_pool']?>"><?php echo $val['labor_pool']?></option>
   	<?php }?>
   </select>
   </td>
   
   
   <td width=300 valign='top'>
   <select name="job_titles[]" id="job_titles" data-size="100" required="required">
   	<option value="">Select</option>
   	<?php
   	   $result=$db->query("SELECT DISTINCT job_title FROM tbl_job_family order by job_title");
   	    while($val  =   $result->fetch_assoc())
               {
   	?>
   	        <option value="<?php echo $val['job_title']?>"><?php echo $val['job_title']?></option>
   	<?php }?>
   </select>
   </td>
   
   <td valign='top' width='75' colspan='2'><input type="forcasts" name="forcasts[]" class="form-control" required="required"></td>

</tr>
<tr bgcolor ='<?php echo $new_column_color ?>'>
<td valign='top'><b>Group Code</b></td>
<td valign='top'><b>Group Name</b></td>
<td valign='top'><b>Team Name </b></td>
<td valign='top'><b>Start Date</b></td>
<td valign='top' colspan='2'><b>End Date</b></td>
</tr>
<tr>




   <td width=300 valign='top'>
   <select name="group_codes[]" id="group_codes" data-size="100" required="required">
        <option value="">Select</option>
        <?php
           $result=$db->query("SELECT DISTINCT group_code FROM tbl_staff_info order by group_code");
            while($val  =   $result->fetch_assoc())
               {
        ?>
                <option value="<?php echo $val['group_code']?>"><?php echo $val['group_code']?></option>
        <?php }?>
   </select>
   </td>


   <td width=300 valign='top'>
   <select name="group_names[]" id="group_names" data-size="100" required="required">
        <option value="">Select</option>
        <?php
           $result=$db->query("SELECT DISTINCT group_name FROM tbl_staff_info order by group_name");
            while($val  =   $result->fetch_assoc())
               {
        ?>
                <option value="<?php echo $val['group_name']?>"><?php echo $val['group_name']?></option>
        <?php }?>
   </select>
   </td>

   <td width=300 valign='top'>
   <select name="team_names[]" id="team_names" data-size="100" required="required">
        <option value="">Select</option>
        <?php
           $result=$db->query("SELECT DISTINCT team_name FROM tbl_staff_info order by team_name");
            while($val  =   $result->fetch_assoc())
               {
        ?>
                <option value="<?php echo $val['team_name']?>"><?php echo $val['team_name']?></option>
        <?php }?>
   </select>
   </td>





   <td width=400 valign='top'><input type="startdates" name="startdates[]" class="form-control" required="required"></td>
   <td width=400 valign='top'><input type="enddates" name="enddates[]" class="form-control" required="required"></td>
   
   <td valign='top' align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" onclick="if(confirm('Are you sure to remove?')){$(this).closest('table').remove();}" class="btn btn-danger"><i class="fa fa-fw fa-trash-alt"></i></button></td>
</tr>
</table>
</td>
</tr>
	<?php
}


