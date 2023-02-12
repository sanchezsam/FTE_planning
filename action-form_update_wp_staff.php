<?php include_once('config2.php');
if(isset($_REQUEST['action']) and $_REQUEST['action']=="addDataRow"){
$currentYear=date("Y");
?>
<tr>

   <td>Znumber</td>
   <td width='120' valign='top'><input type="znumber" name="znumbers[]" class="form-control" required="required"></td>
   
   <td valign='top' align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" onclick="if(confirm('Are you sure to remove?')){$(this).closest('tr').remove();}" class="btn btn-danger"><i class="fa fa-fw fa-trash-alt"></i></button></td>
</tr>
	<?php
	echo '|***|addmore';
}


