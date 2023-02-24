<?php


function get_file_list_recursively(string $dir, bool $realpath = false): array
{
    $files = array();
    $files = [];
    foreach ((new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS))) as $file) {
        /** @var SplFileInfo $file */
        if ($realpath) {
            $files[] = $file->getRealPath();
        } else {
            $files[] = $file->getPathname();
        }
    }
    return $files;
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


$files = get_file_list_recursively($wp_name);
foreach ($files as $file) {
  print "<br>$file\n";
}
?>
