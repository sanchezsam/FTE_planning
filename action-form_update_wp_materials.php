<?php include_once('config2.php');
require 'include/db.php';
if(isset($_REQUEST['action']) and $_REQUEST['action']=="addDataRow"){
$currentYear=date("Y");
?>
<tr><td colspan=7>
<table>
<tr bgcolor ='<?php echo $new_column_color ?>'>
<td valign='top'><b>Property #</b></td>
<td valign='top' colspan='2'><b>Description</b></td>
<td valign='top'><b>Owner</b></td>
<td valign='top'><b>Service Entry</b></td>
<td valign='top'><b>Under Maint</b></td>
<td valign='top'><b>Maint PO</b></td>
</tr>





 <tr>

                <td valign='top'><input name="property_number[]" class="form-control" required="required"></td>
                <td width='250' colspan='2'><textarea name="description[]" class="form-control" required="required"></textarea></td>

                <td valign='top'  width='200'>
                <select name="owners[]" id="owners" data-size="10" required="required">
                        <option value="">Select</option>
                        <?php
                                $result = $db->query("SELECT distinct name FROM tbl_staff_info order by trim(name) asc ");
                                #$result = $db->query("SELECT distinct SUBSTRING(name, 1, 40) FROM tbl_staff_info order by name asc ");
                                while($val  =   $result->fetch_assoc()){
                                ?>
                                <option value="<?php echo $val['name']?>"><?php echo $val['name']?></option>
                        <?php }?>
                </select>
                </td>

                <td valign='top'><input type="service_entry" name="service_entry[]" class="form-control" required="required"></td>

                <td valign='top'>
                <select name="under_maint[]" id="under_maint" data-size="10">
                        <option value="">Select</option>
                        <?php
                           $result=$db->query("SELECT 'Yes' as value UNION ALL SELECT 'No';");
                           while($val  =   $result->fetch_assoc()){
                            ?>
                             <option value="<?php echo $val['value']?>"><?php echo $val['value']?></option>
                        <?php }?>
                </select>
                </td>

                <td valign='top'><input name="maint_po[]" class="form-control"></td>
 </tr>

<tr bgcolor ='<?php echo $new_column_color ?>'>
<td valign='top'><b>PCT Fous</b></td>
<td valign='top'><b>Risk</b></td>
<td valign='top'><b>Replace Fund</b></td>
<td valign='top'><b>Note</b></td>
<td valign='top' colspan='3'><b>Replacement Cost</b></td>
</tr>


 <tr>
                <td valign='top'><input name="pct_fous[]" class="form-control" required="required"></td>
              
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


                <td valign='top'>
                <select name="replace_fund[]" id="replace_fund" data-size="10" required="required">
                        <option value="">Select</option>
                        <?php
                           $result=$db->query("SELECT 'Yes' as value UNION ALL SELECT 'No';");
                           while($val  =   $result->fetch_assoc()){
                            ?>
                             <option value="<?php echo $val['value']?>"><?php echo $val['value']?></option>
                        <?php }?>
                </select>
                </td>


                <td><textarea name="note[]" class="form-control" required="required"></textarea></td>
                <td valign='top'><input name="replacement_cost[]" class="form-control" required="required"></td>

                <td align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" onclick="if(confirm('Are you sure to remove?')){$(this).closest('table').remove();}" class="btn btn-danger"><i class="fa fa-fw fa-trash-alt"></i></button></td>
        </tr>
</table>
</td></tr>
<?php
	echo '|***|addmore';
}


