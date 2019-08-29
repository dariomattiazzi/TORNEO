<?php


$tor = 25;
$cat = 135;
$zon = 135;

$fix_id = 3860;

//b3
$arr[0] = 117;
$arr[1] = 118;
$arr[2] = 119;
$arr[3] = 120;
$arr[4] = 121;
$arr[5] = 122;
$arr[6] = 123;
$arr[7] = 124;
$arr[8] = 125;
$arr[9] = 126;

//c1
/*
$arr[0] = 95;
$arr[1] = 13;
$arr[2] = 105;
$arr[3] = 103;
$arr[4] = 85;
$arr[5] = 110;
$arr[6] = 111;
$arr[7] = 112;
$arr[8] = 113;
$arr[9] = 9999;
*/

//c2
/*$arr[0] = 106;
$arr[1] = 61;
$arr[2] = 78;
$arr[3] = 114;
$arr[4] = 115;
$arr[5] = 99;
$arr[6] = 100;
$arr[7] = 116;
$arr[8] = 75;
$arr[9] = 9999;
*/

$fix = "INSERT INTO fixture VALUES "."\n" ;



//f1
$fech = 1;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[7], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[1], $arr[6], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[2], $arr[5], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[3], $arr[4], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[9], $arr[8], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;

//f2
$fech = 2;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[1], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[2], $arr[7], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[3], $arr[6], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[9], $arr[5], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[8], $arr[4], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;


//f3
$fech = 3;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[2], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[3], $arr[1], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[9], $arr[7], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[8], $arr[6], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[4], $arr[5], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;

//f4
$fech = 4;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[3], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[9], $arr[2], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[8], $arr[1], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[4], $arr[7], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[5], $arr[6], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;

//f5
$fech = 5;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[9], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[8], $arr[3], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[4], $arr[2], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[5], $arr[1], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[6], $arr[7], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;

//f6
$fech = 6;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[8], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[4], $arr[9], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[5], $arr[3], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[6], $arr[2], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[7], $arr[1], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;

//f7
$fech = 7;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[4], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[5], $arr[8], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[6], $arr[9], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[7], $arr[3], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[1], $arr[2], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;

//f8
$fech = 8;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[5], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[6], $arr[4], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[7], $arr[8], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[1], $arr[9], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[2], $arr[3], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;

//f9
$fech = 9;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[6], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[7], $arr[5], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[1], $arr[4], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[2], $arr[8], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[3], $arr[9], 1, 1, 999, null, null, 0, null, null);"."\n"; $fix_id++;

echo $fix; die;


?>
