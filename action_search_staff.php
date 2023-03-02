<?php
  require_once 'config.php';

  if (isset($_POST['query'])) {
    
   $currentYear=date("Y");


   if(isset($_POST['year']))
   {
       $currentYear=$_POST['year'];
   }
   if(isset($_GET['currentYear']))
   {
        $currentYear=$_GET['currentYear'];
   }

    $inpText = $_POST['query'];
    #$sql = 'SELECT staff_name,team_name,group_name FROM vw_staff_mapping WHERE staff_name LIKE :staff order by staff_name asc';
    $sql = 'SELECT distinct name,team_name,group_name FROM tbl_staff_info WHERE name LIKE :staff order by name asc';

$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
fwrite($myfile, $currentYear);
fwrite($myfile, "year");
fclose($myfile);

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
  }
?>
