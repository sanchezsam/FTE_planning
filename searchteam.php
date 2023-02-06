<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_team_fte($name,$currentYear)
{
   #echo $name;
   $query="select ''";
   if(str_contains($name,'->'))
   {
      $pieces = explode("->", $name);
      $group=$pieces[0];
      $team=$pieces[1];
      $query="SELECT * 
              FROM `vw_team_forcast`
              WHERE YEAR(enddate)=$currentYear
                    and  team_name='$team' 
                    and group_name='$group'
              ORDER BY workpackage_name, enddate desc";
   }
   #echo $query;
   return $query;
}


//TITLE
echo "<br><strong>Search</strong> By Team<br><br>";


?>



<?php
$name="";
if(isset($_POST['search']))
{
   $name=$_POST['search'];
}
//CONTENTS
//echo $output_str;


   $currentYear=date("Y");

   $termcount=0;
   $previousName="";
   $currentDate=date("Y/m/d");
   $currentYear=date("Y");
   if(isset($_GET['currentYear']))
   {
        $currentYear=$_GET['currentYear'];
   }
   if($name!=""){
       $query=get_team_fte($name,$currentYear);
       $result=mysqli_query($conn,$query);
   }



   #$currentDate=strtotime($currentDate);
   #$output_str.="<form id='yearform' method='post'>";
   #$output_str="<table style='border:1px solid black;'>\n";
   #$output_str.="<tr bgcolor ='#C1C1E8'>\n";
   #$output_str.="<td valign='top'><b>View by Year</b></td>\n";
   #$query="SELECT year(enddate) FROM vw_fte_mapping group by year(enddate)";
   #$year_result=mysqli_query($conn,$query);
   #$output_str.="<td>";
   #$output_str.="<select  onchange='refreshPage(this.value);' name='year[]' id='year' data-size='4' required='required' onchange='change()'>";
   #$output_str.="<option value=''>Select</option>";
   #while($row=mysqli_fetch_array($year_result))
   #{
   #   if($currentYear==$row[0])
   #   {
   #       $output_str.="<option value=$row[0] selected='true'>$row[0]</option>";
   #   }
   #   else
   #   {
   #       $output_str.="<option value=$row[0]>$row[0]</option>";
   #   }
   #}
   #$output_str.="</select>";
   #$output_str.="</td>";
   #$output_str.="</tr>\n";
   #$output_str.="</table>\n";
   #$output_str.="</form>";
   $output_str=drop_down_year($conn);
   echo $output_str;
?>
<script>
function refreshPage(passValue,search){
//do something in this function with the value
 window.location="searchteam.php?currentYear="+passValue
}
</script>



<div class="container">
    <div class="row mt-4">
      <div class="col-md-8 mx-auto bg-light rounded p-4">
        <hr class="my-1">
        <h5 class="text-center text-secondary">Enter team name in the search box</h5>
        <form action="" method="post" class="p-3">
          <div class="input-group">
            <input type="text" name="search" id="search" class="form-control form-control-lg rounded-0 border-info" placeholder="Search..." autocomplete="off" required>
            <div class="input-group-append">
              <input type="submit" name="submit" value="Search" class="btn btn-info btn-lg rounded-0">
            </div>
          </div>
        </form>
      </div>
      <div class="col-md-5" style="position: relative;margin-top: -72px;margin-left: 174px;">

        <div class="list-group" id="show-list">
          <!-- Here autocomplete list will be display -->
        </div>
      </div>
    </div>
  </div>
  <script src="script_dir/script_team.js"></script>




<?php









if(isset($_POST['search']))
{
   $name=$_POST['search'];
   $currentYear=date("Y");
   if(isset($_GET['currentYear']))
   {      
        $currentYear=$_GET['currentYear'];
   }
   $query=get_team_fte($name,$currentYear);
   $result=mysqli_query($conn,$query);

$termcount=0;
$previousWP="";
$currentDate=date("Y/m/d");
#$currentYear=date("Y");
$currentDate=strtotime($currentDate);

$output_str="<table id='dataTable' width = '900' style='border:1px solid black;'>\n";
#$output_str.="<tr><td>$name $currentYear Forcast</td></tr>\n";
$output_str.="<tr bgcolor ='#C1C1E8'>\n";
$output_str.="<td valign='top' width='300'><b>Workpackage Name</b></td>\n";
$output_str.="<td valign='top'><b>Staff Name</b></td>\n";
$output_str.="<td valign='top'><b>Forcasted Amount</b></td>\n";
$output_str.="<td valign='top'><b>Start Date</b></td>\n";
$output_str.="<td valign='top'><b>End Date</b></td>\n";
$output_str.="</tr>\n";

$total=0;
$grand_total=0;
while($row=mysqli_fetch_array($result))
{
   $staff_name=$row[1];
   $team_name=$row[2];
   $group_name=$row[3];
   $workpackage=$row[4];
   $forcasted=$row[5];
   $startdate=$row[6];
   $enddate=$row[7];
   $endFYI="$currentYear-$endFYIDate";
   if(($previousWP != $workpackage))
   {

       if($total>0)
       {   
           #$output_str.="<tr bgcolor='#b1fefe'>\n<td colspan='5' align='right'>WP Total :  $total</td></tr>\n";
           $output_str.="<tr bgcolor='#b1fefe'>\n";
           $output_str.= "<td>&nbsp;</td>\n";
           $output_str.= "<td>&nbsp;</td>\n";
           $output_str.= "<td>&nbsp;</td>\n";
           $output_str.= "<td>&nbsp;</td>\n";
           $output_str.="<td align='right'>WP Total :  $total</td></tr>\n";
           $total=0;
       }


       #This is the first display for this term, calculate the tr background color ??
       $currentColor= ${'colour' .($termcount % 2)};
       #$total+=$percent/100;
       $output_str.="<tr bgcolor='$currentColor'>\n<td width=210 valign='top'>$workpackage</td>\n";
       $termcount++;
       $previousWP = $workpackage;
   }
   else
   {
      $output_str.="<tr bgcolor=$currentColor>";
      $output_str.= "<td>&nbsp;</td>\n";
    }
   $total+=$forcasted;
   $grand_total+=$forcasted;
   $prev_color=$currentColor;
   if(strtotime($enddate)<strtotime($endFYI))
   {
         $currentColor=$old_color;
         $font_color=$change_font_color;
   }
   $output_str.="<td bgcolor='$currentColor' valign='top'>$staff_name</td>\n";
   $output_str.="<td bgcolor='$currentColor' valign='top' align='right'>$forcasted</td>\n";
   $output_str.="<td bgcolor='$currentColor' valign='top' align='right'>$startdate</td>\n";
   $output_str.="<td bgcolor='$currentColor' valign='top' align='right'>$enddate</td>\n";
   $currentColor=$prev_color;
   $output_str.="</tr>\n";
}
$output_str.="<tr bgcolor='#b1fefe'>\n";
$output_str.= "<td>&nbsp;</td>\n";
$output_str.= "<td>&nbsp;</td>\n";
$output_str.= "<td>&nbsp;</td>\n";
$output_str.= "<td>&nbsp;</td>\n";
$output_str.="<td align='right'>WP Total :  $total</td></tr>\n";

$output_str.="<tr bgcolor='#b1fefe'>\n";
$output_str.= "<td>&nbsp;</td>\n";
$output_str.= "<td>&nbsp;</td>\n";
$output_str.= "<td>&nbsp;</td>\n";
$output_str.= "<td>&nbsp;</td>\n";
$output_str.="<td align='right'>Team Total :  $grand_total</td></tr>\n";
$total=0;
$output_str.="</table>\n";


echo $output_str;
}
//echo "</section>";

require 'template/footer.html';
