<?php include_once('config2.php');
require 'include/db.php';
if(isset($_REQUEST['action']) and $_REQUEST['action']=="addDataRow"){
$currentYear=date("Y");
?>
<tr><td colspan=10>
<table>
<tr bgcolor ='<?php echo $new_column_color ?>'>
<td valign='top' colspan='2'><b>Project/Task</b></td>
</tr>

 <tr>

   
   <td width=300 valign='top'>
   <select name="projects[]" id="projects" data-size="100" required="required">
   	<option value="">Select</option>
   	<?php
   	   $result=$db->query("SELECT DISTINCT concat(project,' ',task) as wp,wp_id FROM tbl_wp_info order by project");
   	    while($val  =   $result->fetch_assoc())
               {
   	?>
   	        <option value="<?php echo $val['wp_id']?>"><?php echo $val['wp']?></option>
   	<?php }?>
   </select>
   </td>
   
   

   <td valign='top' align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" onclick="if(confirm('Are you sure to remove?')){$(this).closest('table').remove();}" class="btn btn-danger"><i class="fa fa-fw fa-trash-alt"></i></button></td>
</tr>
</table>
</td>
</tr>
	<?php
}


