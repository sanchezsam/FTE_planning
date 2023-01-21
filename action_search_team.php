<?php
  require_once 'config.php';

  if (isset($_POST['query'])) {
    $inpText = $_POST['query'];
    #$sql = 'SELECT team_name FROM tbl_teams WHERE team_name LIKE :team order by team_name asc';
    $sql = 'SELECT team_name,group_name FROM vw_team_mapping WHERE team_name LIKE :team order by team_name asc';
    $stmt = $conn->prepare($sql);
    $stmt->execute(['team' => '%' . $inpText . '%']);
    $result = $stmt->fetchAll();

#$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
#$txt=$sql;
#fwrite($myfile, $sql);
#fclose($myfile);

    if ($result) {
      foreach ($result as $row) {
         $display_str=$row['group_name']."->".$row['team_name'];
         echo '<a href="#" class="list-group-item list-group-item-action border-1">' . $display_str. '</a>';
        #echo '<a href="#" class="list-group-item list-group-item-action border-1">' . $row['team_name'] . '</a>';
      }
    } else {
      echo '<p class="list-group-item border-1">No Record</p>';
    }
  }
?>
