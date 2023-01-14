<?php
  require_once 'config.php';
  $currentYear=date("Y");

  if (isset($_POST['query'])) {
    $inpText = $_POST['query'];
    #$sql = "SELECT workpackage_name FROM tbl_workpackage WHERE YEAR(enddate)>='$currentYear' and workpackage_name LIKE :wp";
    $sql = 'SELECT Distinct workpackage_name FROM tbl_workpackage WHERE workpackage_name LIKE :wp';
    $stmt = $conn->prepare($sql);
    $stmt->execute(['wp' => '%' . $inpText . '%']);
    $result = $stmt->fetchAll();

    if ($result) {
      foreach ($result as $row) {
        echo '<a href="#" class="list-group-item list-group-item-action border-1">' . $row['workpackage_name'] . '</a>';
      }
    } else {
      echo '<p class="list-group-item border-1">No Record</p>';
    }
  }
?>
