<?php
require '../../include/db.php';
#DELETE FROM `tbl_wp_info` WHERE `wp_id` >= 32;
#DELETE FROM `tbl_wp_materials` WHERE `wp_id` >= 32;
#DELETE FROM `tbl_wp_staff` WHERE `wp_id` >= 32;
#DELETE FROM `tbl_wp_services` WHERE `wp_id` >= 32;
#DELETE FROM `tbl_wp_activities` WHERE `wp_id` >= 32;



function duplicate_records($conn,$wp_id,$new_wp_id,$tmp_table,$org_table,$ID,$nextStartFYDate,$nextEndFYDate,$service_entry="F")
{
        $create_table="CREATE table $tmp_table as SELECT * FROM $org_table where wp_id=$wp_id";
        mysqli_query($conn,$create_table);

        #Alter
        $alter_table="ALTER TABLE $tmp_table MODIFY $ID INT";
        $alter_result=mysqli_query($conn,$alter_table);

        #UPDATE temporary_table
        if($service_entry=='F')
        {
            $update_query="UPDATE $tmp_table
                          SET
                              $ID=NULL,
                              wp_id='$new_wp_id',
                              startdate='$nextStartFYDate',
                              enddate='$nextEndFYDate'";
        }
        else
        {
            $update_query="UPDATE $tmp_table
                          SET
                              $ID=NULL,
                              wp_id='$new_wp_id',
                              service_entry='$nextStartFYDate'";

        }
        mysqli_query($conn,$update_query);

        #INSERT INTO original_table SELECT * FROM temporary_table;
        $insert_table="INSERT INTO $org_table SELECT * FROM $tmp_table";
        echo "$insert_table<br>";
        $insert_result=mysqli_query($conn,$insert_table);

        #DROP TABLE temporary_table;
        $drop_table="DROP TABLE $tmp_table";
        $drop_result=mysqli_query($conn,$drop_table);
  return;

}

$startFY="2022";
$nextStartFYDate="2023-10-01";
$nextEndFYDate="2024-09-30";


#-------Duplicate wp info
$org_table='tbl_wp_info';
$ID='wp_id';

#$select_wp_info="select * from tbl_wp_info where YEAR(startdate)='$startFY' and wp_id='1'";
$select_wp_info="select * from tbl_wp_info where YEAR(startdate)='$startFY'";
echo $select_wp_info;
$result=mysqli_query($conn,$select_wp_info);
while($row=mysqli_fetch_assoc($result))
{
    
    #INSERT INTO original_table SELECT * FROM temporary_table;
    $wp_id=$row['wp_id'];
    $program=$row['program'];
    $project=$row['project'];
    $task=$row['task'];
    $task_name=$row['task_name'];
    $task_manager=$row['task_manager'];
    $task_description=$row['task_description'];
    $burden_rate=$row['burden_rate'];
    $target=$row['target'];
    $insert_table="INSERT INTO tbl_wp_info values (NULL,
                                                 '$program',
                                                 '$project',
                                                 '$task',
                                                 '$task_name',
                                                 '$task_manager',
                                                 '$task_description',
                                                 '$burden_rate',
                                                 '$target',
                                                 '$nextStartFYDate',
                                                 '$nextEndFYDate'
                                                 );";
    
    echo "$insert_table<br>";
    $insert_result=mysqli_query($conn,$insert_table);
    $last_inserted="SELECT LAST_INSERT_ID();";
    $result_last=mysqli_query($conn,$last_inserted);
    while($last_row=mysqli_fetch_array($result_last))
    {

        $new_wp_id=$last_row[0]; 
        #query all wp_id from temporary_table
        #duplicate tbl_wp_activities 
        $tmp_table='tbl_wp_activities_tmp';
        $org_table='tbl_wp_activities';
        $ID='activity_id';
        duplicate_records($conn,$wp_id,$new_wp_id,$tmp_table,$org_table,$ID,$nextStartFYDate,$nextEndFYDate);

        #duplicate tbl_wp_services 
        $tmp_table='tbl_wp_services_tmp';
        $org_table='tbl_wp_services';
        $ID='service_id';
        duplicate_records($conn,$wp_id,$new_wp_id,$tmp_table,$org_table,$ID,$nextStartFYDate,$nextEndFYDate);

        #duplicate tbl_wp_staff 
        $tmp_table='tbl_wp_staff_tmp';
        $org_table='tbl_wp_staff';
        $ID='wp_staff_id';
        duplicate_records($conn,$wp_id,$new_wp_id,$tmp_table,$org_table,$ID,$nextStartFYDate,$nextEndFYDate);

        #duplicate tbl_wp_materials 
        $tmp_table='tbl_wp_materials_tmp';
        $org_table='tbl_wp_materials';
        $ID='material_id';
        duplicate_records($conn,$wp_id,$new_wp_id,$tmp_table,$org_table,$ID,$nextStartFYDate,$nextEndFYDate,'T');
        

    }
    
    
}
