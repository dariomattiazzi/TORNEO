<?php

$tor = 23;
$cat = 131;
$zon = 95;



//c2
$arr[0] = 103;
$arr[1] = 104;
$arr[2] = 75;
$arr[3] = 105;
$arr[4] = 106;
$arr[5] = 107;
$arr[6] = 999;
$arr[7] = 95;
$arr[8] = 69;
$arr[9] = 71;


//c1
/*$arr[0] = 61;
$arr[1] = 97;
$arr[2] = 98;
$arr[3] = 99;
$arr[4] = 999;
$arr[5] = 100;
$arr[6] = 101;
$arr[7] = 102;
$arr[8] = 56;
$arr[9] = 64;
*/

$fix = "INSERT INTO fixture VALUES "."\n" ;

$fix_id = 2815;

//f1
$fech = 1;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[9], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[1], $arr[8], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[2], $arr[7], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[3], $arr[6], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[4], $arr[5], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;

//f2
$fech = 2;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[1], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[2], $arr[9], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[3], $arr[8], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[4], $arr[7], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[5], $arr[6], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;


//f3
$fech = 3;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[2], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[3], $arr[1], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[4], $arr[9], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[5], $arr[8], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[6], $arr[7], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;

//f4
$fech = 4;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[3], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[4], $arr[2], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[5], $arr[1], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[6], $arr[9], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[7], $arr[8], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;

//f5
$fech = 5;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[4], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[5], $arr[3], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[6], $arr[2], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[7], $arr[1], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[8], $arr[9], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;

//f6
$fech = 6;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[5], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[6], $arr[4], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[7], $arr[3], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[8], $arr[2], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[9], $arr[1], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;

//f7
$fech = 7;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[6], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[7], $arr[5], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[8], $arr[4], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[9], $arr[3], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[1], $arr[2], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;

//f8
$fech = 8;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[7], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[8], $arr[6], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[9], $arr[5], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[1], $arr[4], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[2], $arr[3], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;

//f9
$fech = 9;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[0], $arr[8], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[9], $arr[7], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[1], $arr[6], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[2], $arr[5], 1, 1, 999, null, null, 0, null, null),"."\n"; $fix_id++;
$fix .= "($fix_id, $tor, $cat, $zon, $fech, $arr[3], $arr[4], 1, 1, 999, null, null, 0, null, null);"."\n"; $fix_id++;

echo $fix; die;


?>
