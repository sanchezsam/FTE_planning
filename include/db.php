<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'root';
$dbname = 'FTE_Planning';
$colour0 = '#E3E3E3';
$colour1 = '#FFFFFF';
$old_color='#787878';
$header_color='#ADD8E6';
$change_font_color="white";
$column_color='#C1C1E8';
$totals_color='#33ffce';
#$totals_color='red';
$startFY="2022";
$endFY="2023";
$endMonth='10-01';
$startFYDate="2022-10-01";
$endFYIDate="9-30";
$nextStartFYDate="2023-10-01";
$nextEndFYDate="2024-09-30";
#$colour0 = 'blue';
#$colour1 = 'yellow';
#$old_color='green';
#$change_font_color="red";
$new_column_color='#90EE90';

$conn= mysqli_connect($dbhost,$dbuser,$dbpass,$dbname) or die("Problem connecting: ".mysqli_error());




function generate_select_list($db,$query,$selected_value,$drop_down_name)
{

      $output_str="<td valign='top'>\n";
      $output_str.=$drop_down_name;
      $output_str.="<option value=''>Select</option>\n";
      $result= $db->query($query);
      #echo "Selected Value (".trim($selected_value).")<br>";
      while($row=mysqli_fetch_array($result))
      {
          #echo "ROw ($row[0])<br>";
          if(trim($selected_value)==trim($row[0]))
          {
              $output_str.="<option value='$row[0]' selected='true'>$row[0]</option>\n";
              #$output_str.="<option value='$row[0]' selected='selected'>$row[0]</option>\n";
          }
          else
          {
              $output_str.="<option value='$row[0]'>$row[0]</option>\n";
          }
       }

       $output_str.="</select>\n";
       $output_str.="</td>\n";
    return $output_str;

}







function display_table_header($header_str,$colspan="")
{  
   global $header_color;
   if($colspan==""){
      $colspan='100%';
   }
   $output_str="\n<tr>\n<td style='background-color:$header_color' colspan='$colspan'><h4><b>$header_str</b></h4></td>\n</tr>\n";
   return $output_str;
}



function get_mysql_columns($result)
{
   global $column_color;
   $return_str="";
   $return_str.="<tr bgcolor='$column_color'>\n";
   $values = $result->fetch_all(MYSQLI_ASSOC);
   $columns = array();

   if(!empty($values)){
       $columns = array_keys($values[0]);
   }
   else
   {
      $return_str.="<td>&nbsp;</td>\n";
   }
   foreach($columns as $col){
       #echo "in foreach $col<br>";
       $return_str.="<td style='background-color:$column_color' valign='top'><b>$col</b></td>\n";
   }
   $return_str.="</tr>\n";
   return array($return_str,$columns);
}

function get_record_id($rec_id,$result)
{
    #check if results has something
    $row = $result -> fetch_array(MYSQLI_ASSOC);
    if($row)
    {
       return $row[$rec_id];
    }
    else
    {
       return;
    }
}

function get_mysql_values($result)
{
   global $colour0, $colour1;
   $return_str="";
   $termcount=0;
   $previousRow="";
   while($row=mysqli_fetch_row($result))
   {
      $currentColor= ${'colour' .($termcount % 2)};            
      $return_str.="<tr bgcolor='$currentColor'>\n";
      for($i=0;$i<count($row);$i++){
        if($row[$i]=="")
        {
              $return_str.="<td style='background-color:$currentColor'>&nbsp;</td>\n";
        }
        else{
            if (str_starts_with($row[$i], '-'))
            {  
                $value=$row[$i];
                $value=str_replace('-','(', $value);
                $value.=')';
                $return_str.="<td style='background-color:$currentColor'><font color='red'>$value</font></td>\n";
                #$return_str.="<td style='background-color:$currentColor'><font color='red'>$row[$i]</font></td>\n";
            }
            else
            {
                $return_str.="<td style='background-color:$currentColor'>$row[$i]</td>\n";
            }
        }
      }
      $termcount++;
      $return_str.="</tr>\n";
   }
   return $return_str;
}

function get_mysql_totals_values($result,$columns,$columns_totals)
{
   global $totals_color;
   $return_str="";
   $return_str.="<tr bgcolor='$totals_color'>";
   $found="F";
   $row= mysqli_fetch_array($result);
   $size=count($columns);
   if(count($columns)==0){
     return;
   } 
   for($i=0;$i<count($columns);$i++)
   {
        for($j=0;$j<count($columns_totals);$j++)
        {
            if($columns[$i]==$columns_totals[$j])
            {
              $found="T";
              $column_num=$j;
            }
        }
        if($found=="T")
        {


           if (str_starts_with($row[$column_num], '-'))
            {
                $value=$row[$column_num];
                $value=str_replace('-','(', $value);
                $value.=')';
                $return_str.="<td style='background-color:$totals_color'><font color='red'>$value</font></td>\n";
            }
            else
            {
                $return_str.="<td style='background-color:$totals_color'>$row[$column_num]</td>\n";
            }

            $found="F";
        }
        else
        {
            if($i==0)
            {
              $return_str.="<td><b>Totals:</b></td>\n";
            }
            else
            {
              $return_str.="<td>&nbsp;</td>\n";
            }
        }
   }
   $return_str.="</tr>\n";
   #echo htmlspecialchars($return_str);
   return $return_str;
}

function get_mysql_values_with_old($currentYear,$result,$columns)
{
   global $colour0, $colour1,$old_color,$font_color,$change_font_color,$endFYIDate;
   #$currentYear=$currentYear-1;
   $endFYI="$currentYear-$endFYIDate";
   $return_str="";
   $termcount=0;
   $previousRow="";
   $end_date_key = array_search('End Date', $columns);
   $row_count=0;
   while($row=mysqli_fetch_row($result))
   {
      $currentColor= ${'colour' .($termcount % 2)};            
      $enddate=$row[$end_date_key];
      #if(strtotime($enddate)<strtotime($endFYI) || $currentYear<date("Y"))
      #if(strtotime($enddate)<strtotime($endFYI))
      #{
      #   $currentColor=$old_color;
      #   $font_color=$change_font_color;
      #}
      $return_str.="<tr bgcolor='$currentColor'>";
      
      for($i=0;$i<count($row);$i++){
        if (str_starts_with($row[$i], '-'))
        {
           echo $row[$i];
           $font_color='red';
        }
        $return_str.="<td style='background-color:$currentColor'><font color=$font_color>$row[$i]</font></td>\n";
        $font_color='black';
      }
      $termcount++;
      $return_str.="</tr>\n";
   }
   return $return_str;
}


 

function drop_down_year_with_program($conn,$page_name)
{
$currentYear=date("Y");

#$currentDate=strtotime($currentDate);
$output_str="";
#$output_str.="<form id='yearform' method='post' action=workpackage_managers.php>";
$output_str.="<form id='yearform' method='post' action=$page_name.php>";
$output_str.="<table style='border:1px solid black;'>\n";
$output_str.="<tr bgcolor ='#C1C1E8'>\n";

$output_str.="<td align='left'>";
$output_str.="<ul class='actions'>";
$output_str.="<li><a href='#' onclick='doExport()' class='button'>Export</a></li>";
$output_str.="</ul>";

#$output_str.="<td align='left' class='btn-group pull-left'>";
#$output_str.="<button type='button' btn-lg dropdown-toggle' data-toggle='dropdown'>Export <span class='caret'></span></button>\n";
#$output_str.="<ul class='dropdown-menu' role='menu'>\n";
#$output_str.="<li><a class='dataExport' data-type='csv'>CSV</a></li>";
#$output_str.="<li><a class='dataExport' data-type='excel'>XLS</a></li>";
#$output_str.="<li><a class='dataExport' data-type='txt'>TXT</a></li>";
#$output_str.="</ul>";
#$output_str.="</td>";
$output_str.="<td>\n";
$query="SELECT distinct program  FROM tbl_wp_info";
$program_result=mysqli_query($conn,$query);
$check_box_str="";
#$currentYear="";



$program="";
if(isset($_POST["submit"]))
{
     if(isset($_POST['program_name']))
     {
        $program= $_POST['program_name'];
     }
     if(isset($_POST['year']))
     {
          $currentYear=$_POST['year'][0];
     }
}

if(isset($_GET['program_name']))
{
   $program= $_GET['program_name'];
}

if(isset($_GET['currentYear']))
{
     $currentYear=$_GET['currentYear'];
}

$checkbox_str="";
while($row=mysqli_fetch_array($program_result))
{
      $program_value=$row[0];
      if($program==$program_value)
      {
          $checkbox_str.="<input type='radio' id='$program_value' name=program_name value='$program_value' checked>";
      }
      else
      {
          $checkbox_str.="<input type='radio' id='$program_value' name=program_name value='$program_value' required='required'>";
      }
      #$checkbox_str.="<input type='radio' id='$program_value' name=program_name value='$program_value'>";
      $checkbox_str.="<label for=$program_value>$program_value</label>";
}

$output_str.=$checkbox_str;
$output_str.="</td>\n";

$output_str.="<td valign='top'><b>View by Year</b></td>\n";
$query="SELECT distinct year(enddate) FROM tbl_staff_info group by year(enddate)";
$year_result=mysqli_query($conn,$query);
$output_str.="<td width='200'>";
#$output_str.="<select  onchange='refreshPage(this.value);' name='year[]' id='year' data-size='4' required='required' onchange='change()'>";
$output_str.="<select  name='year[]' id='year' data-size='4' required='required' onchange='change()'>";
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
$output_str.="<td>";
#$output_str.="<a href='workpackage_managers.php?currentYear=$currentYear&program=$program'>";
$output_str.="<a href='$page_name.php?currentYear=$currentYear&program=$program'>";
$output_str.="<input type='submit' name='submit' value='Display'>";
$output_str.="</a>";

$output_str.="</td>";
$output_str.="</tr>\n";
$output_str.="</table>\n";
$output_str.="</form>";
return $output_str;
}












function drop_down_year_with_group($conn,$page,$where)
{
$currentYear=date("Y");
if(isset($_GET['currentYear']))
{
     $currentYear=$_GET['currentYear'];
}
#$currentDate=strtotime($currentDate);
$output_str="";
#$output_str.="<form id='yearform' method='post'>";
$output_str.="<form id='yearform' method='post' action=$page.php?$where>";
#$output_str="<table width = '432' style='border:1px solid black;'>\n";
$output_str.="<table style='border:1px solid black;'>\n";
$output_str.="<tr bgcolor ='#C1C1E8'>\n";


$output_str.="<td align='left'>";
$output_str.="<ul class='actions'>";
$output_str.="<li><a href='#' onclick='doExport()' class='button'>Export</a></li>";
$output_str.="</ul>";



#$output_str.="<td align='left' class='btn-group pull-left'>";
#$output_str.="<button type='button' btn-lg dropdown-toggle' data-toggle='dropdown'>Export <span class='caret'></span></button>\n";
#$output_str.="<ul class='dropdown-menu' role='menu'>\n";
#$output_str.="<li><a class='dataExport' data-type='csv'>CSV</a></li>";
#$output_str.="<li><a class='dataExport' data-type='excel'>XLS</a></li>";
#$output_str.="<li><a class='dataExport' data-type='txt'>TXT</a></li>";
#$output_str.="</ul>";
$output_str.="</td>";



$output_str.="<td>\n";
$query="SELECT distinct group_name FROM tbl_groups";
$group_result=mysqli_query($conn,$query);
$check_box_str="";



$group="HPC-SYS";
if(isset($_POST["submit"]))
{
     if(isset($_POST['group_name']))
     {
        $group= $_POST['group_name'];
     }
     if(isset($_POST['year']))
     {
          $currentYear=$_POST['year'][0];
     }
     #print_r($_POST);
     #echo '<pre>' . print_r(get_defined_vars(), true) . '</pre>';

}
$checkbox_str="";
while($row=mysqli_fetch_array($group_result))
{
      $group_value=$row[0];
      if($group==$group_value)
      {
          $checkbox_str.="<input type='radio' id='$group_value' name=group_name value='$group_value' checked>";
      }
      else
      {
          $checkbox_str.="<input type='radio' id='$group_value' name=group_name value='$group_value' required='required'>";
      }
      #$checkbox_str.="<input type='radio' id='$group_value' name=group_name value='$group_value'>";
      $checkbox_str.="<label for=$group_value>$group_value</label>";
}

$output_str.=$checkbox_str;
$output_str.="</td>\n";


$output_str.="<td valign='top'><b>View by Year</b></td>\n";
$query="SELECT distinct year(enddate) FROM tbl_staff_info group by year(enddate)";
$year_result=mysqli_query($conn,$query);
$output_str.="<td width='200'>";
$output_str.="<select  onchange='refreshPage(this.value);' name='year[]' id='year' data-size='4' required='required' onchange='change()'>";
#$output_str.="<select  name='year[]' id='year' data-size='4' required='required' onchange='change()'>";
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



$output_str.="<td>";
$output_str.="<a href='$page.php?currentYear=$currentYear&group=$group'>";
$output_str.="<input type='submit' name='submit' value='Display'>";
$output_str.="</a>";

$output_str.="</td>";
$output_str.="</tr>\n";
$output_str.="</table>\n";
$output_str.="</form>";
return $output_str;

}



function drop_down_year($conn)
{
$currentYear=date("Y");
if(isset($_GET['currentYear']))
{
     $currentYear=$_GET['currentYear'];
}

#$currentDate=strtotime($currentDate);
$output_str="";
$output_str.="<form id='yearform' method='post'>";
#$output_str="<table width = '432' style='border:1px solid black;'>\n";
$output_str="<table style='border:1px solid black;'>\n";
$output_str.="<tr bgcolor ='#C1C1E8'>\n";


$output_str.="<td align='left'>";
$output_str.="<ul class='actions'>";
$output_str.="<li><a href='#' onclick='doExport()' class='button'>Export</a></li>";
$output_str.="</ul>";


$output_str.="</td>";


$output_str.="<td valign='top'><b>View by Year</b></td>\n";
$query="SELECT distinct year(enddate) FROM tbl_staff_info group by year(enddate)";
$year_result=mysqli_query($conn,$query);
$output_str.="<td width='200'>";
$output_str.="<select  onchange='refreshPage(this.value);' name='year[]' id='year' data-size='4' required='required' onchange='change()'>";
#$output_str.="<select  name='year[]' id='year' data-size='4' required='required' onchange='change()'>";
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
$output_str.="<input type='hidden' name='currentYear' value='$currentYear'>";
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
   #echo $query;
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
   $previousStaff="";
   if($currentYear=="")
   {
     $currentYear=date("Y");

   }
   $query="SELECT staff_name,forcasted,round(difference,2) FROM `vw_staff_over_under` where YEAR(enddate)='$currentYear' and difference!=0 and group_name='$group'  ORDER BY vw_staff_over_under.difference ASC ";

   $query="SELECT  tbl_staff_info.name,
 round(sum(tbl_wp_staff.funded_percent),2) AS funded_percent,
 round((round(sum(tbl_wp_staff.funded_percent),2) - tbl_staff_info.fte_amount),2) AS `difference`
  FROM tbl_staff_info 
  LEFT JOIN tbl_wp_staff ON tbl_staff_info.znumber = tbl_wp_staff.znumber
   WHERE YEAR(tbl_staff_info.enddate)='$currentYear'
         and YEAR(tbl_wp_staff.enddate)='$currentYear'
         and tbl_staff_info.group_name='$group'
   group by tbl_staff_info.name  
ORDER BY `difference` DESC
          ";

   #echo $query;
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
      if($difference==0.00)
      {
        continue;
      }
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

function display_search_box($title)
{
    $search_str="<div class='container'>";
    $search_str.="<div class='row mt-4'>";
    $search_str.="<div class='col-md-8 mx-auto bg-light rounded p-4'>";
    $search_str.="<form action='' method='post' class='p-3'>";
    $search_str.="<table>";
    $search_str.="<tr>";
    $search_str.="<td>";
    $search_str.="<h5 class='text-center text-secondary'>$title</h5>";
    $search_str.="</td>";
    $search_str.="</tr>";
    $search_str.="<tr>";
    $search_str.="<td><input type='text' name='search' id='search' -control-lg rounded-0 border-info' placeholder='Search...' autocomplete='off' required></td>";
    $search_str.="</tr>";
    $search_str.="<tr>";
    $search_str.="<td align='right'><input type='submit' name='submit' value='Search' class='btn btn-info btn-lg rounded-0'></td>";
    $search_str.="</tr>";
    $search_str.="</table>";
    $search_str.="</form>";
    $search_str.="</div>";
    $search_str.="<div class='col-md-5' style='position: relative;margin-top: -153px;margin-left: 205px;'>";
    $search_str.="<div class='list-group' id='show-list'>";
    $search_str.="</div>";
    $search_str.="</div>";
    $search_str.="</div>";
    $search_str.="</div>";

    return $search_str;
}



function drop_down_year_basic($conn)
{
$currentYear=date("Y");
if(isset($_GET['currentYear']))
{
     $currentYear=$_GET['currentYear'];
}

#$currentDate=strtotime($currentDate);
$output_str="";
$output_str.="<form id='yearform' method='post'>";
#$output_str="<table width = '432' style='border:1px solid black;'>\n";
$output_str="<table style='border:1px solid black;'>\n";
$output_str.="<tr bgcolor ='#C1C1E8'>\n";


$output_str.="<td valign='top'><b>View by Year</b></td>\n";
$query="SELECT year(enddate) FROM tbl_staff_info group by year(enddate)";
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
$output_str.="<input type='hidden' name='currentYear' value='$currentYear'>";
$output_str.="</form>";
return $output_str;

}

?>
