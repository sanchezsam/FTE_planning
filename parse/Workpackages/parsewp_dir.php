<?php

require '../../include/db.php';



function get_file_list_recursively(string $dir, bool $realpath = false): array
{
    $files = array();
    $files = [];
    foreach ((new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS))) as $file) {
        /** @var SplFileInfo $file */
        #if ($realpath) {
        #    $files[] = $file->getRealPath();
        #} else {
        #    $files[] = $file->getPathname();
        #}
        if (str_contains($file, 'csv')) {
            $files[] = $file;
        }
    }
    return $files;
}

function get_wp_id($conn,$workpackage_name,$startdate,$enddate)
{
   #$currentYear=date('Y');
   
   $query="SELECT wp_id FROM tbl_wp_info where task='$workpackage_name' and startdate='$startdate' and enddate='$enddate'";
   #echo "$query<br>";
   $result=mysqli_query($conn,$query);
   if (mysqli_num_rows($result)==0)
   {
      echo"Error No record in tbl_wp_info for $workpackage_name<br>";
      $wp_id=0;
   }
   while($row = mysqli_fetch_assoc($result)) {
       $wp_id=$row['wp_id'];
   }
   return $wp_id;
}

function validate_manager($conn,$manager)
{
   global $startFYDate;

   $query="SELECT manager_name FROM tbl_wp_manager where manager_name='$manager' and enddate is null";
   #echo "$query<br>";
   $result=mysqli_query($conn,$query);
   if (mysqli_num_rows($result)==0)
   {
      #echo"Error No record in tbl_wp_manager  $manager<br>";
      $insert_query="INSERT INTO tbl_wp_manager ( manager_name, startdate) VALUES ('$manager','$startFYDate');";
      echo "$insert_query<br>";
      #mysqli_query($conn,$insert_query);
   }
   return;
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



#$files = array_diff(scandir($wp_name), array('..', '.'));

$files = get_file_list_recursively($wp_name);

#foreach($files as $path =>$file)
#{
#   echo "$file<br>";
#}


echo "Start parsing contents in $wp_name<br>";
$wp_id=0;
#foreach($files as $file)
foreach($files as $path =>$file)
{

    #$file_name=$wp_name."/".$file;
    $file_name=$file;
    #if (str_starts_with($file, 'none')) {
    if (str_contains($file, 'none')) {
       #echo "#Skipping $file";
       continue;
    }
    echo "#$file_name<br><br>";
    $lines = file($file_name);
    $file_array=array();

    #if (str_starts_with($file, '1_')) {
    if (str_contains($file, '1_')) {
      echo "#------------------------<br>";
      $insert_values="";
      $table_name="tbl_wp_info";
      $insert_descr="program,project,task,task_name,task_manager,task_description,burden_rate,target,startdate,enddate";

 
    }
    #elseif(str_starts_with($file, '2_')) {
    elseif(str_contains($file, '2_')) {
      $table_name="tbl_wp_activities";
      $insert_values="'$wp_id',";
      $insert_descr="wp_id,activity,startdate,enddate,members,description";
    } 
    #elseif(str_starts_with($file, '3_')) {
    elseif(str_contains($file, '3_')) {
      $table_name="tbl_wp_materials";
      $insert_values="'$wp_id',";
      $insert_descr="wp_id,property_number,description,service_entry,owner,under_maintenance,maintenance_po,pct_fous,risk,replace_fund,replacement_cost,total_cost,notes";
    } 
    #elseif(str_starts_with($file, '4_')) {
    elseif(str_contains($file, '4_')) {
      $table_name="tbl_wp_services";
      $insert_values="'$wp_id',";
      $insert_descr="wp_id,description,startdate,enddate,owner,vendor,pct_fous,risk,funded,cost,total_cost,notes";
    } 
    #elseif(str_starts_with($file, '5_')) {
    elseif(str_contains($file, '5_')) {
      $table_name="tbl_wp_staff";
      $insert_values="";
      $insert_values="'$wp_id',";
      $insert_descr="wp_id,znumber,name,startdate,enddate,title,salary_min,salary_max,group_name,org_code,pct_fte,cost,funded,funded_percent,total_cost,notes";
    } 
    else{
      $insert_values="'$wp_id',";
      $insert_descr="wp_id,";
    }




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
           $insert_values.="'$item',";
         }
      }
      if($count>0)
      {
          $file=str_replace(".csv","",$file);
          #print "INSERT INTO tbl_$file ($insert_descr) values ($insert_values)<br>";
          $insert_values=substr($insert_values, 0, -1);
          #$table_name=substr($file, 2);
          $insert_query="INSERT INTO $table_name ($insert_descr) values ($insert_values);<br><br>";
          print $insert_query;
          #mysqli_query($conn,$insert_query);
          $insert_values="'$wp_id',";
          if (str_starts_with($file, '1_')) {
              #echo "Get wp id\n<br>";
              $program=$row[0];
              $project=$row[1];
              $task=$row[2];
              $task_manager=$row[4];
              $startdate=$row[8];
              $enddate=$row[9];
              $wp_id=get_wp_id($conn,$task,$startdate,$enddate);
              #check if task manager is in tbl_wp_manager
              $result=validate_manager($conn,$task_manager);   
          }
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
