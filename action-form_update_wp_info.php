<?php include_once('config2.php');
require 'include/lib.php';
if(isset($_REQUEST['action']) and $_REQUEST['action']=="addDataRow"){
$currentYear=date("Y");
?>
<tr><td colspan=10>
<table>
<tr bgcolor ='<?php echo $new_column_color ?>'>
<td valign='top'><b>Program</b></td>
<td valign='top'><b>Project</b></td>
<td valign='top'><b>Task</b></td>
<td valign='top' colspan='2'><b>Burden Rate</b></td>
<td valign='top' colspan='3'><b>Target $$$</b></td>
<td valign='top'><b>Task Manager</b></td>
</tr>





 <tr>


                <td valign='top'>
                <select name="programs[]" id="programs" data-size="10" required="required">
                        <option value="">Select</option>
                        <?php
                                $result =       $db->query("SELECT DISTINCT program_name FROM tbl_program_info");
                                while($val  =   $result->fetch_assoc()){
                                ?>
                                <option value="<?php echo $val['program_name']?>"><?php echo $val['program_name']?></option>
                        <?php }?>
                </select>
                </td>

                <td valign='top' width='300'><input type="project" name="project[]" class="form-control" required="required"></td>
                <td valign='top' width='300'><input type="task" name="task[]" class="form-control" required="required"></td>
                <td colspan='2' valign='top' width='100'><input name="burden_rate[]" class="form-control" required="required"></td>
                <td colspan='3' valign='top' width='100'><input name="target[]" class="form-control" required="required"></td>

                <td valign='top'>
                <select name="task_manager[]" id="task_manager" data-size="10" required="required">
                        <option value="">Select</option>
                        <?php
                                $result =       $db->query("SELECT Distinct manager_name FROM tbl_wp_manager order by manager_name asc;");
                                while($val  =   $result->fetch_assoc()){
                                ?>
                                <option value="<?php echo $val['manager_name']?>"><?php echo $val['manager_name']?></option>
                        <?php }?>
                </select>
                </td>
</tr>              
<tr bgcolor ='<?php echo $new_column_color ?>'>
<td valign='top' colspan='3'><b>Task Name</b></td>
<td valign='top' colspan='6'><b>Task Description</b></td>
</tr>
<tr>

                <td colspan='3' valign='top' width='500'><input type="task_name" name="task_name[]" class="form-control" required="required"></td>

                <td colspan='5' valign='top' width='600'><textarea name="task_description[]" rows='4' class="form-control" required="required"></textarea></td>
                   <td align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" onclick="if(confirm('Are you sure to remove?')){$(this).closest('table').remove();}" class="btn btn-danger"><i class="fa fa-fw fa-trash-alt"></i></button></td>

</tr>
</table>
</td></tr>
<?php
	#echo '|***|addmore';
}


