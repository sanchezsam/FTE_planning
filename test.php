<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

$currentYear=date("Y");
$currentDate=date("Y/m/d");
$currentYear=date("Y");
if(isset($_GET['currentYear']))
{
     $currentYear=$_GET['currentYear'];
}
$drop_down_str=drop_down_year($conn);
echo $drop_down_str;

?>

<table id='dataTable' class='table table-striped'>

<tr bgcolor='#ADD8E6'><td colspan='100%'><b>COMPPROD 2023 Forcast </b></td></tr>

<tr bgcolor ='#C1C1E8'>
<td valign='top'><b>wp_id</b></td>
<td valign='top'><b>Task</b></td>
<td valign='top'><b>Task Name</b></td>
<td valign='top'><b>Task Manager</b></td>
<td valign='top'><b>Task Description</b></td>
<td valign='top'><b>Burden Rate</b></td>
<td valign='top'><b>Start Date</b></td>
<td valign='top'><b>End Date</b></td>
</tr>

<tr bgcolor='#E3E3E3'>
<td>1</td>
<td>COMPPROD</td>
<td>Production Administration</td>
<td>Sam Sanchez</td>
<td>Administer production computing systems to optimize performance availability security and reliability. </td>
<td>1</td>
<td>2022-10-01</td>
<td>2023-09-30</td>
</tr>


<tr><td colspan='100%'></rtd></tr>
<tr bgcolor ='#C1C1E8'>
<td valign='top'><b>wp_id</b></td>
<td valign='top'><b>Task</b></td>
<td valign='top'><b>Task Name</b></td>
<td valign='top'><b>Task Manager</b></td>
<td valign='top'><b>Task Description</b></td>
<td valign='top'><b>Burden Rate</b></td>
<td valign='top'><b>Start Date</b></td>
<td valign='top'><b>End Date</b></td>
</tr>

<tr bgcolor='#E3E3E3'>
<td>1</td>
<td>COMPPROD</td>
<td>Production Administration</td>
<td>Sam Sanchez</td>
<td>Administer production computing systems to optimize performance availability security and reliability. </td>
<td>1</td>
<td>2022-10-01</td>
<td>2023-09-30</td>
</tr>

</table>



<?php
require 'template/footer.html';
?>
