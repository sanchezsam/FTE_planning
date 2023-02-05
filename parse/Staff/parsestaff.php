<?php

require '../../include/db.php';


function get_wp_id($conn,$workpackage_name,$startdate,$enddate)
{
   #$currentYear=date('Y');
   
   $query="SELECT wp_id FROM tbl_wp_info where task='$workpackage_name' and startdate='$startdate' and enddate='$enddate'";
   #echo "$query<br>";
   $result=mysqli_query($conn,$query);
   if (mysqli_num_rows($result)==0)
   {
      echo"Error No record in tbl_wp_info for task $task<br>";
      $wp_id=0;
   }
   while($row = mysqli_fetch_assoc($result)) {
       $wp_id=$row['wp_id'];
   }
   return $wp_id;
}
function get_staff_id($znumber)
{
   $query="SELECT staff_id FROM tbl_staff where znumber='$znumber' and enddate IS NULL";
   #echo "$query<br>";
   return $query;
}
if(isset($_GET['wp']))
{
    $wp_name=$_GET['wp'];
}
else
{
  echo "Must put file name: ie /parse.php?wp=PROD0000<br>";
  exit();
}



$files = array_diff(scandir($wp_name), array('..', '.'));
echo "Start parsing contents in $wp_name<br>";
$wp_id=0;
foreach($files as $file)
{

    #print_r($file_name);
    $file_name=$wp_name."/".$file;
    echo "#$file_name<br><br>";
    $lines = file($file_name);
    $file_array=array();

    if (str_starts_with($file, '1_')) {
      $insert_values="";
      $table_name="tbl_job_class";
      $insert_descr="title,pay_range,min_salary,max_salary,startdate,enddate";
    }
    elseif(str_starts_with($file, '2_')) {
      $table_name="tbl_staff_info";
      $insert_values="";
      $insert_descr="znumber,name,labor_pool,job_title,group_code,group_name,startdate,enddate";
    } 
    elseif(str_starts_with($file, '3_')) {
      $table_name="tbl_job_family";
      $insert_values="";
      $insert_descr="labor_pool,job_title,job_class_desc,job_family_desc,job_function_desc,job_category_desc,startdate,	enddate ";
    } 
    #else{
    #  $insert_values="'$wp_id',";
    #  $insert_descr="wp_id,";
    #}




    foreach($lines as $line) 
    {
       //echo "<br>$line<br>";
       $line_array=explode(",",$line);
       array_push($file_array,$line_array); 
    }
    //Iterate through array of lines
    $count=0;
    foreach($file_array as $row)
    {
      #print "Header";
      foreach($row as $item)
      {
         if($count==0)
         {
            #$insert_descr.="$item,";
         }
         else
         {
           $item = str_replace("'", "\'", $item);
           
          if(strpos($item, "*") !== false )
          {
              $myArray = explode('*', $item); 
              $item="$myArray[1] $myArray[0]";
          }
           $insert_values.="'$item',";
         }
      }
      if($count>0)
      {
          $file=str_replace(".csv","",$file);
          $insert_values=substr($insert_values, 0, -1);
          print "INSERT INTO $table_name ($insert_descr) values ($insert_values);<br><br>";
          $insert_values="";
          #if (str_starts_with($file, '1_')) {
          #    echo "Get wp id\n<br>";
          #    $task=$row[0];
          #    $startdate=$row[5];
          #    $enddate=$row[6];
          #    $wp_id=get_wp_id($conn,$task,$startdate,$enddate);
          #}
      }
      $count++;
    
    }
}
#
#$count = 0;
#$startdate='2022-10-01';
#$enddate='2023-10-01';
#$timestamp = strtotime($startdate);
#$currentYear=date("Y", $timestamp);
#$error_str="";
#foreach($lines as $line) {
#    $skip="F";
#    #echo "$line\n<br>";
#    $line_array=explode(",",$line);
#    #print_r($line_array);
#    $znumber=$line_array[0];
#    $name=$line_array[1];
#    //echo $znumber,$name;
#    //Get staff_id from mysql
#    $query=get_staff_id($znumber);
#    $result=mysqli_query($conn,$query);
#    if (mysqli_num_rows($result)==0)
#    {
#       $error_str.="Error No record in tbl_staff for $znumber:$name [$line]<br>";
#       $skip="T";
#    }
#    while($row = mysqli_fetch_assoc($result)) {
#        $staff_id=$row['staff_id'];
#    }
#    //echo "<br>";
#    $start=2;
#    while($start<=count($line_array)-2)
#    {
#      $wp=$line_array[$start];
#      $percent=$line_array[$start+1];
#      #echo "$wp,$percent<br>";
#      $start+=2;
#
#      //Get wp_id from mysql
#      $query=get_wp_id($wp,$currentYear);
#      $result=mysqli_query($conn,$query);
#      if (mysqli_num_rows($result)==0)
#      {
#          $error_str.="Error No record in tbl_workpackages for $wp [$line]<br>";
#          $skip="T";
#      }
#      while($row = mysqli_fetch_assoc($result)) {
#          $wp_id=$row['wp_id'];
#          //echo "wp_id $wp_id<br>";
#      }
#      if($percent<=0)
#      {
#        $percent=0;
#      }
#      if($skip=="F")
#      {
#          $insert_fte="INSERT INTO `tbl_fte_planning` (`fte_id`, `staff_id`, `wp_id`, `forcasted_amount`, `startdate`, `enddate`) 
#                   VALUES (NULL, '$staff_id', '$wp_id', $percent, '$startdate', '$enddate');";
#          echo $insert_fte;
#          echo "<br>";
#      }
#
#    }
#}
#echo "<br>";
#echo $error_str;
#
