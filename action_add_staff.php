<?php
  require_once 'config.php';

  $currentYear=date("Y");
  if (isset($_POST['query'])) {
    $inpText = $_POST['query'];
    $currentYear = $_POST['currentYear'];
    #echo "Search $currentYear";
    #$sql = 'SELECT distinct name FROM tbl_staff_info WHERE name LIKE :staff order by name asc';
    $sql = "SELECT distinct name FROM tbl_staff_info WHERE YEAR(enddate)='$currentYear' and name LIKE :staff order by name asc";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['staff' => '%' . $inpText . '%']);
    $result = $stmt->fetchAll();

    if ($result) {
      foreach ($result as $row) {
        echo '<a href="#" class="list-group-item list-group-item-action border-1">' . $row['name'] . '</a>';
      }
    } else {
      echo '<p class="list-group-item border-1">No Record</p>';
    }
  }
?>
