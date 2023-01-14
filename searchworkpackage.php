<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require 'include/db.php';
require 'template/header.html';

function get_staff_fte($name,$currentYear)
{
   #$currentYear=date("Y");
   $query="SELECT staff_name,forcasted_amount,startdate,enddate FROM `vw_fte_mapping` where workpackage_name='$name' and YEAR(enddate)='$currentYear' order by enddate desc";
   #echo $query;
   return $query;
}




//TITLE
echo "<br><strong>Search</strong> Workpackages<br><br>";



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

   #$query=get_staff_fte($name,$currentYear);
   #$result=mysqli_query($conn,$query);



   $currentDate=strtotime($currentDate);
   $output_str.="<form id='yearform' method='post'>";
   $output_str="<table style='border:1px solid black;'>\n";
   $output_str.="<tr bgcolor ='#C1C1E8'>\n";
   $output_str.="<td valign='top'><b>View by Year</b></td>\n";
   $query="SELECT year(enddate) FROM vw_fte_mapping group by year(enddate)";
   $year_result=mysqli_query($conn,$query);
   $output_str.="<td>";
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
   echo $output_str;
?>
<script>
function refreshPage(passValue,search){
//do something in this function with the value
 window.location="searchworkpackage.php?currentYear="+passValue
}
</script>


<div class="container">
    <div class="row mt-4">
      <div class="col-md-8 mx-auto bg-light rounded p-4">
        <hr class="my-1">
        <h5 class="text-center text-secondary">Enter workpackage in the search box</h5>
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
  <script src="jquery.min.js"></script>
  <script src="script_workpackage.js"></script>


<?php

if(isset($_POST['search']))
{
   $name=$_POST['search'];
   if(isset($_GET['currentYear']))
   {
       $currentYear=$_GET['currentYear'];
   }
   $query=get_staff_fte($name,$currentYear);
   $result=mysqli_query($conn,$query);

   $termcount=0;
   $previousName="";
   $currentDate=date("Y/m/d");
   #$currentYear=date("Y");
   $currentDate=strtotime($currentDate);
   
   $output_str="<table><tr><td rowspan='4'>$name $currentYear Forcast</td></tr></table>";
   $output_str.="<table width = '900' style='border:1px solid black;'>\n";
   $output_str.="<tr bgcolor ='#C1C1E8'>\n";
   $output_str.="<td valign='top'><b>Staff</b></td>\n";
   $output_str.="<td valign='top'><b>Percent</b></td>\n";
   $output_str.="<td valign='top'><b>Start Date</b></td>\n";
   $output_str.="<td valign='top'><b>End Date</b></td>\n";
   $output_str.="</tr>\n";
   
   $total=0; 
   while($row=mysqli_fetch_array($result))
   {
      $name=$row[0];
      $percent=$row[1];
      $startdate=$row[2];
      $enddate=$row[3];
      $endFYI="$currentYear-$endFYIDate";
      #echo "<br>$enddate";
      if(($previousName != $name))
      {
          #This is the first display for this term, calculate the tr background color ??
          $currentColor= ${'colour' .($termcount % 2)};
          #if($currentDate>strtotime($enddate))
          #if($currentDate>strtotime($enddate) and date("Y")==$currentYear)
          #if($currentDate>strtotime($enddate) and strtotime($enddate)<strtotime($endFYI))
          if(strtotime($enddate)<strtotime($endFYI))
          {
              $currentColor=$old_color;
              $font_color=$change_font_color;
          }
          #else
          #{

            $total+=$percent;
          #}
          $output_str.="<tr bgcolor='$currentColor'>\n<td width=210 valign='top'><font color='$font_color'>$name</font></td>\n";
          $termcount++;
          $previousTeam = $name;
      }
      $output_str.="<td valign='top'><font color='$font_color'>$percent</font></td>\n";
      $output_str.="<td valign='top'><font color='$font_color'>$startdate</font></td>\n";
      $output_str.="<td valign='top'><font color='$font_color'>$enddate</font></td>\n";
      $output_str.="</tr>\n";
   }
   $output_str.="</table>\n";
    $output_str.="<table><tr><td rowspan='4'>Total FTEs $total</td></tr></table>";
   echo $output_str;
}
//echo "</section>";

require 'template/footer.html';
