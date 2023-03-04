<?php include_once('config2.php');
require 'include/db.php';
if(isset($_REQUEST['action']) and $_REQUEST['action']=="addDataRow"){
$currentYear=date("Y");
?>
<tr><td colspan=10>
<table>
<tr bgcolor ='<?php echo $new_column_color ?>'>
<td valign='top'><b>Description</b></td>
<td valign='top'><b>Owner</b></td>
<td valign='top'><b>Risk</b></td>
<td valign='top' colspan='2'><b>PCT Fous</b></td>
</tr>





 <tr>

                <td><textarea name="description[]" class="form-control" required="required"></textarea></td>

                <td valign='top'>
                <select name="owners[]" id="owners" data-size="10" required="required">
                        <option value="">Select</option>
                        <?php
                                $result =       $db->query("SELECT distinct name FROM tbl_staff_info order by trim(name) asc ");
                                while($val  =   $result->fetch_assoc()){
                                ?>
                                <option value="<?php echo $val['name']?>"><?php echo $val['name']?></option>
                        <?php }?>
                </select>
                </td>

                <td valign='top'>
                <select name="risk[]" id="risk" data-size="10" required="required">
                        <option value="">Select</option>
                        <?php
                           $result=$db->query("SELECT '5' as value UNION ALL SELECT '4' UNION ALL SELECT '3' UNION ALL SELECT '2' UNION ALL SELECT '1';");
                           while($val  =   $result->fetch_assoc()){
                            ?>
                             <option value="<?php echo $val['value']?>"><?php echo $val['value']?></option>
                        <?php }?>
                </select>
                </td>

                <td valign='top' width='100'><input type="pct_fous" name="pct_fous[]" class="form-control" required="required"></td>
</tr>              
<tr bgcolor ='<?php echo $new_column_color ?>'>
<td valign='top'><b>Vendor</b></td>
<td valign='top'><b>Notes</b></td>
<td valign='top'><b>Funded</b></td>
<td valign='top' colspan='2'><b>Cost</b></td>
</tr>
<tr>


                <td><textarea name="vendor[]" class="form-control" required="required"></textarea></td>
                <td><textarea name="new_note[]" class="form-control" required="required"></textarea></td>


                <td valign='top'>
                <select name="funded[]" id="funded" data-size="10" required="required">
                        <option value="">Select</option>
                        <?php
                           $result=$db->query("SELECT 'Yes' as value UNION ALL SELECT 'No';");
                           while($val  =   $result->fetch_assoc()){
                            ?>
                             <option value="<?php echo $val['value']?>"><?php echo $val['value']?></option>
                        <?php }?>
                </select>
                </td>



                <td valign='top' width='100'><input name="new_cost[]" class="form-control" required="required"></td>

                <td align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" onclick="if(confirm('Are you sure to remove?')){$(this).closest('table').remove();}" class="btn btn-danger"><i class="fa fa-fw fa-trash-alt"></i></button></td>
        </tr>
</table>
</td></tr>
<?php
	#echo '|***|addmore';
}


