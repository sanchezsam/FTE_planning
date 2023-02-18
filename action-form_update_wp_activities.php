<?php include_once('config2.php');
require 'include/db.php';

if(isset($_REQUEST['action']) and $_REQUEST['action']=="addDataRow"){
$currentYear=date("Y");
?>

<tr><td colspan=7>
<table>
<tr bgcolor ='<?php echo $new_column_color ?>'>
<td valign='top'><b>Activity</b></td>
<td valign='top'><b>Members</b></td>
<td valign='top' colspan='2'><b>Description</b></td>
</tr>





 <tr>

                <td valign='top'><input type="activity" name="activity[]" class="form-control" required="required"></td>

                <td valign='top'>
                <select name="members[]" id="members" data-size="10" required="required">
                        <option value="">Select</option>
                        <?php
                                $result =       $db->query("SELECT distinct name FROM tbl_staff_info order by name asc ");
                                while($val  =   $result->fetch_assoc()){
                                ?>
                                <option value="<?php echo $val['name']?>"><?php echo $val['name']?></option>
                        <?php }?>
                </select>
                </td>


                <td width='700' valign='top'><textarea name="description[]" class="form-control" required="required"></textarea></td>

                <td align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" onclick="if(confirm('Are you sure to remove?')){$(this).closest('table').remove();}" class="btn btn-danger"><i class="fa fa-fw fa-trash-alt"></i></button></td>
        </tr>
</table>
</td></tr>

<?php
	echo '|***|addmore';
}


