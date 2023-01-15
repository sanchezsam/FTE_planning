<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'root';
$dbname = 'FTE_Planning';
$colour0 = '#E3E3E3';
$colour1 = '#FFFFFF';
$old_color='#787878';
$change_font_color="white";
$endFYIDate="10-01";
#$colour0 = 'blue';
#$colour1 = 'yellow';
#$old_color='green';
#$change_font_color="red";

$conn= mysqli_connect($dbhost,$dbuser,$dbpass,$dbname) or die("Problem connecting: ".mysqli_error());



function drop_down_year($conn)
{
$currentYear=date("Y");
if(isset($_GET['currentYear']))
{
     $currentYear=$_GET['currentYear'];
}

$currentDate=strtotime($currentDate);
$output_str="";
$output_str.="<form id='yearform' method='post'>";
#$output_str="<table width = '432' style='border:1px solid black;'>\n";
$output_str="<table style='border:1px solid black;'>\n";
$output_str.="<tr bgcolor ='#C1C1E8'>\n";
$output_str.="<td valign='top'><b>View by Year</b></td>\n";
$query="SELECT year(enddate) FROM vw_fte_mapping group by year(enddate)";
$year_result=mysqli_query($conn,$query);
$output_str.="<td width='200'>";
$output_str.="<select  onchange='refreshPage(this.value);' name='year[]' id='year' data-size='4' required='required' onchange='change()'>";
$output_str.="<option value=''>Select</option>";
while($row=mysqli_fetch_array($year_result))
{
   if($currentYear==$row[0])
   {
       $output_str.="<option value=$row[0] selected='true'>$row[0]</option>";
   }
   else
   {
       $output_str.="<option value=$row[0]>$row[0]</option>";
   }
}
$output_str.="</select>";
$output_str.="</td>";
$output_str.="</tr>\n";
$output_str.="</table>\n";
$output_str.="</form>";
return $output_str;

}





function over_or_under($conn,$currentYear)
{
   $colour0 = '#E3E3E3';
   $colour1 = '#FFFFFF';
   $termcount=0;
   $previousWP="";
   if($currentYear=="")
   {
     $currentYear=date("Y");

   }
   $query="SELECT * FROM `vw_over_or_under` where YEAR(enddate)='$currentYear' and difference!=0  ORDER BY `vw_over_or_under`.`difference` ASC ";
   $result=mysqli_query($conn,$query);
   $output_str="<font size='1'>\n";
   $output_str.="<table class='style1' width='300' style='border:1px solid black;'>\n";
   $output_str.="<tr bgcolor ='#C1C1E8'>\n";
   $output_str.="<td valign='top'><b>Workpackage</b></td>\n";
   $output_str.="<td valign='top'><b>Forcasted</b></td>\n";
   $output_str.="<td valign='top'><b>Allocated</b></td>\n";
   $output_str.="<td valign='top'><b>Difference</b></td>\n";
   $output_str.="</tr>\n";
   while($row=mysqli_fetch_array($result))
   {
      $forcasted=$row[2];
      $allocated=$row[3];
      $difference=$row[4];
      $workpackage=$row[5];
      $font_color="black";
      if(($previousWP != $workpackage))
      {
   
          #This is the first display for this term, calculate the tr background color ??
          $currentColor= ${'colour' .($termcount % 2)};
          $termcount++;
          $previousWP = $workpackage;
      }
      $output_str.="<tr bgcolor='$currentColor'>\n<td valign='top'><font color='$font_color'>$workpackage</font></td>\n";
      $output_str.="<td valign='top' align='right'><font color='$font_color'>$forcasted</font></td>\n";
      $output_str.="<td valign='top'align='right'><font color='$font_color'>$allocated</font></td>\n";
       
      if($difference<0)
      {
          $font_color='red';
      }
      else
      {
          $difference="+$difference";    
      }
      $output_str.="<td valign='top'align='right'><font color='$font_color'>$difference</font></td>\n";
      $font_color="black";
      $output_str.="</tr>\n";
      #$currentColor=$prev_color;
   }
   $output_str.="</table>\n";
   $output_str.="</font>\n";
   return $output_str;
}


function over_or_under_staff($conn,$currentYear,$group)
{
   $colour0 = '#E3E3E3';
   $colour1 = '#FFFFFF';
   $termcount=0;
   $previousWP="";
   if($currentYear=="")
   {
     $currentYear=date("Y");

   }
   $query="SELECT staff_name,forcasted,round(difference,2) FROM `vw_staff_over_under` where YEAR(enddate)='$currentYear' and difference!=0 and group_name='$group'  ORDER BY vw_staff_over_under.difference ASC ";
   $result=mysqli_query($conn,$query);
   $output_str="<font size='1'>\n";
   $output_str.="<table class='style1' style='border:1px solid black;'>\n";
   $output_str.="<tr bgcolor ='#C1C1E8'>\n";
   $output_str.="<td valign='top' width='150'><b>Staff</b></td>\n";
   $output_str.="<td valign='top' align='right'><b>Forcasted</b></td>\n";
   $output_str.="<td valign='top' align='right'><b>Difference</b></td>\n";
   $output_str.="</tr>\n";
   while($row=mysqli_fetch_array($result))
   {
      $staff=$row[0];
      $forcasted=$row[1];
      $difference=$row[2];
      $font_color="black";
      if(($previousStaff != $staff))
      {
   
          #This is the first display for this term, calculate the tr background color ??
          $currentColor= ${'colour' .($termcount % 2)};
          $termcount++;
          $previousStaff = $staff;
      }
      $output_str.="<tr bgcolor='$currentColor'>\n<td valign='top'><font color='$font_color'>$staff</font></td>\n";
      $output_str.="<td valign='top' align='right'><font color='$font_color'>$forcasted</font></td>\n";
       
      if($difference<0)
      {
          $font_color='red';
      }
      else
      {
          $difference="+$difference";    
      }
      $output_str.="<td valign='top'align='right'><font color='$font_color'>$difference</font></td>\n";
      $font_color="black";
      $output_str.="</tr>\n";
      #$currentColor=$prev_color;
   }
   $output_str.="</table>\n";
   $output_str.="</font>\n";
   return $output_str;
}



?>
