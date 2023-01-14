<?php

require '../include/db.php';


function get_wp_id($workpackage_name,$currentYear)
{
   #$currentYear=date('Y');
   
   $query="SELECT wp_id FROM tbl_workpackage where workpackage_name='$workpackage_name' and YEAR(startdate)=$currentYear";
   #echo "$query<br>";
   return $query;
}
function get_staff_id($znumber)
{
   $query="SELECT staff_id FROM tbl_staff where znumber='$znumber' and enddate IS NULL";
   #echo "$query<br>";
   return $query;
}
if(isset($_GET['file']))
{
    $file_name=$_GET['file'];
    echo "$file_name<br>";
}
else
{
  echo "Must put file name: ie /insertFTE.php?file=staff_fteENV.txt_2023<br>";
  exit();
}

$lines = file($file_name);

$count = 0;
$startdate='2022-10-01';
$enddate='2023-11-01';
$timestamp = strtotime($startdate);
$currentYear=date("Y", $timestamp);
$error_str="";
foreach($lines as $line) {
    $skip="F";
    #echo "$line\n<br>";
    $line_array=explode(",",$line);
    #print_r($line_array);
    $znumber=$line_array[0];
    $name=$line_array[1];
    //echo $znumber,$name;
    //Get staff_id from mysql
    $query=get_staff_id($znumber);
    $result=mysqli_query($conn,$query);
    if (mysqli_num_rows($result)==0)
    {
       $error_str.="Error No record in tbl_staff for $znumber:$name [$line]<br>";
       $skip="T";
    }
    while($row = mysqli_fetch_assoc($result)) {
        $staff_id=$row['staff_id'];
    }
    //echo "<br>";
    $start=2;
    while($start<=count($line_array)-2)
    {
      $wp=$line_array[$start];
      $percent=$line_array[$start+1];
      #echo "$wp,$percent<br>";
      $start+=2;

      //Get wp_id from mysql
      $query=get_wp_id($wp,$currentYear);
      $result=mysqli_query($conn,$query);
      if (mysqli_num_rows($result)==0)
      {
          $error_str.="Error No record in tbl_workpackages for $wp [$line]<br>";
          $skip="T";
      }
      while($row = mysqli_fetch_assoc($result)) {
          $wp_id=$row['wp_id'];
          //echo "wp_id $wp_id<br>";
      }
      if($percent<=0)
      {
        $percent=0;
      }
      if($skip=="F")
      {
          $insert_fte="INSERT INTO `tbl_fte_planning` (`fte_id`, `staff_id`, `wp_id`, `forcasted_amount`, `startdate`, `enddate`) 
                   VALUES (NULL, '$staff_id', '$wp_id', $percent, '$startdate', '$enddate');";
          echo $insert_fte;
          echo "<br>";
      }

    }
}
echo "<br>";
echo $error_str;

