<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/lib.php';
require 'template/header.html';

function get_teams()
{
   $query="SELECT team_name,group_name FROM vw_team_mapping order by group_name,team_name";
   return $query;
}

$query=get_teams();
$result=mysqli_query($conn,$query);

$colour0 = '#E3E3E3';
$colour1 = '#FFFFFF';
$termcount=0;
$previousTeam="";

$output_str="<table width = '900' style='border:1px solid black;'>\n";
$output_str.="<tr bgcolor ='#C1C1E8'>\n";
$output_str.="<td valign='top'><b>Team</b></td>\n";
$output_str.="<td valign='top'><b>Group</b></td>\n";
$output_str.="</tr>\n";


while($row=mysqli_fetch_array($result))
{
   $team_name=$row[0];
   $group_name=$row[1];
   #if(($previousTeam != $team_name))
   #{
       #This is the first display for this term, calculate the tr background color ??
       $currentColor= ${'colour' .($termcount % 2)};
       $termcount++;
       #$previousTeam = $team_name;
   #}
   $output_str.="<tr bgcolor='$currentColor'>\n<td width=210 valign='top'>$team_name</td>\n";
   $output_str.="<td valign='top'>$group_name</td>\n";
   $output_str.="</tr>\n";
}
$output_str.="</table>\n";


//echo "<section>";
//TITLE
echo "<br><strong>HPC</strong> Teams<br><br>";
//CONTENTS
echo $output_str;
//echo "</section>";

require 'template/footer.html';
