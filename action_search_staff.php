<?php
  require_once 'config.php';


    $inpText = $_POST['query'];
    $currentYear = $_POST['currentYear'];
    #$sql = 'SELECT distinct name,team_name,group_name FROM tbl_staff_info WHERE name LIKE :staff order by name asc';
    $sql = "SELECT distinct name,team_name,group_name FROM tbl_staff_info WHERE YEAR(enddate)='$currentYear' and name LIKE :staff order by name asc";

#$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
#fwrite($myfile, $currentYear);
#fwrite($myfile, "year");
#fclose($myfile);

    $stmt = $conn->prepare($sql);
    $stmt->execute(['staff' => '%' . $inpText . '%']);
    $result = $stmt->fetchAll();

    if ($result) {
      foreach ($result as $row) {
            #WITH team and group
            $display_str=$row['name']."->". $row['team_name']."->".$row['group_name'];
            echo '<a href="#" class="list-group-item list-group-item-action border-1">' . $display_str. '</a>';

            #echo '<a href="#" class="list-group-item list-group-item-action border-1">' . $row['staff_name'] . '</a>';
        }
    } else {
      echo '<p class="list-group-item border-1">No Record</p>';
    }
?>
