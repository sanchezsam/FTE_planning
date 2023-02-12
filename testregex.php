
<?php
$uri= 'posts/25';
$pattern = '{\w+/(\d+)}';

if (preg_match($pattern, $uri, $matches)) {
    print_r($matches);
}


$str = "SCI / ENG 170000 - 184999.99";
$str = str_replace(' ', '', $str);
#$pattern = "{([^0-9]+)([0-9]+)-([0-9]+.[0-9]+)}";
$pattern = "{([^0-9]+)([0-9]+)(\-)([0-9]+\.[0-9]+)}";

if (preg_match($pattern, $str, $matches)) {
    $title=$matches[1];
    $salary_min=$matches[2];
    $salary_max=$matches[4];
    echo $title,$salary_min,$salary_max;
}
#echo preg_match($pattern, $str); 
?>
