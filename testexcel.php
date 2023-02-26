<!DOCTYPE html>
<html lang="en">
<head>
<!---
    <meta charset="UTF-8">
    <title>HTML table Export</title>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="script_dir/FileSaver.min.js"></script>
    <script type="text/javascript" src="script_dir/tableExport.js"></script>
    <script type="text/javaScript">
      function doExport() {
        $('#dataTable').tableExport({
            type:'excel',
            mso: {
              styles: ['background-color',
                       'color',
                       'bgcolor',
                       'font-family',
                       'font-size',
                       'font-weight',
                       'text-align']
            }
          }
        );
      }
    </script>
-->
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
#require 'include/db.php';
require 'template/header2.html';
?>

</head>
<body>
<a href="#" onclick="doExport()">Export to Excel</a>
<table id="dataTable">
    <thead>
    <tr>
        <th style="font-family: arial; font-size: 18px; font-weight: bold">C1</th>
        <th style="font-family: arial; font-size: 18px; font-weight: bold">C2</th>
        <th style="font-family: arial; font-size: 18px; font-weight: bold">C3</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="background-color:red">A</td>
        <td style="background-color:green">B</td>
        <td style="background-color:blue">C</td>
    </tr>
    <tr>
        <td style="text-align:left">D</td>
        <td style="text-align:center">E</td>
        <td style="text-align:right">F</td>
    </tr>
    <tr>
        <td style="color:green">G</td>
        <td style="color:blue">H</td>
        <td style="color:red">I</td>
    </tr>
    </tbody>
</table>
</body>
</html>
