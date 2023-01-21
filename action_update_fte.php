<?php
  require_once 'config.php';

  if (isset($_POST['query'])) {
    $inpText = $_POST['query'];
    $sql = 'SELECT staff_name,team_name,group_name FROM vw_staff_mapping WHERE staff_name LIKE :staff order by staff_name asc';
    #$sql="
    #        SELECT staff_id,
    #         IF(
    #             (SELECT COUNT(*) FROM vw_staff_mapping WHERE staff_name like '%$inpText%' group by staff_name)>1,
    #             (CONCAT(staff_name,team_name)),(CONCAT(staff_name))
    #           ) 
    #         AS 'staff_name'
    #         FROM vw_staff_mapping  WHERE staff_name like '%$inpText%'
    #        ";

#$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
#$txt=$sql;
#fwrite($myfile, $sql);
#fclose($myfile);

    $stmt = $conn->prepare($sql);
    $stmt->execute(['staff' => '%' . $inpText . '%']);
    $result = $stmt->fetchAll();

    if ($result) {
      foreach ($result as $row) {
            #WITH team and group
            $display_str=$row['staff_name']."->". $row['team_name']."->".$row['group_name'];
            echo '<a href="#" class="list-group-item list-group-item-action border-1">' . $display_str. '</a>';

            #echo '<a href="#" class="list-group-item list-group-item-action border-1">' . $row['staff_name'] . '</a>';
        }
    } else {
      echo '<p class="list-group-item border-1">No Record</p>';
    }
  }
?>
