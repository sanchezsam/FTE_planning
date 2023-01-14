<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_staff()
{
   $query="SELECT staff_name,team_name,group_name,startdate,enddate FROM vw_staff_mapping order by staff_name";
   return $query;
}

$query=get_staff();
$result=mysqli_query($conn,$query);

$termcount=0;
$previousName="";

$output_str="<table width = '900' style='border:1px solid black;'>\n";
$output_str.="<tr bgcolor='#C1C1E8'>\n";
$output_str.="<td valign='top'><b>Name</b></td>\n";
$output_str.="<td valign='top'><b>Team</b></td>\n";
$output_str.="<td valign='top'><b>Group</b></td>\n";
$output_str.="<td valign='top'><b>Start Date</b></td>\n";
$output_str.="<td valign='top'><b>End Date</b></td>\n";
$output_str.="</tr>\n";


while($row=mysqli_fetch_array($result))
{
   $name=$row[0];
   $team_name=$row[1];
   $group_name=$row[2];
   $startdate=$row[3];
   $enddate=$row[4];
   if(($previousName != $name))
   {
       #This is the first display for this term, calculate the tr background color ??
       $currentColor= ${'colour' .($termcount % 2)};
       $termcount++;
       $previousName = $name;
   }
   $output_str.="<tr bgcolor='$currentColor'>\n<td valign='top'>$name</td>\n";
   $output_str.="<td valign='top'>$team_name</td>\n";
   $output_str.="<td valign='top'>$group_name</td>\n";
   $output_str.="<td valign='top'>$startdate</td>\n";
   $output_str.="<td valign='top'>$enddate</td>\n";
   $output_str.="</tr>\n";
}
$output_str.="</table>\n";


//TITLE
echo "<br><strong>HPC</strong> Staff<br><br>";
//CONTENTS
echo $output_str;

require 'template/footer.html';
