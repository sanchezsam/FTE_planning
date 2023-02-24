<?php
  require_once 'config.php';

  $currentYear=date("Y");
  if (isset($_POST['query'])) {
    $inpText = $_POST['query'];
    $sql = "SELECT Distinct concat(Project,' ', task) as workpackage FROM tbl_wp_info WHERE concat(Project,' ', task) LIKE :wp";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['wp' => '%' . $inpText . '%']);
    $result = $stmt->fetchAll();

    if ($result) {
      foreach ($result as $row) {
        echo '<a href="#" class="list-group-item list-group-item-action border-1">' . $row['workpackage'] . '</a>';
      }
    } else {
      echo '<p class="list-group-item border-1">No Record</p>';
    }
  }
?>
