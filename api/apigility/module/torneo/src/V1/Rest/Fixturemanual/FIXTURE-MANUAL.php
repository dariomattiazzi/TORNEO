<?php

$tor = 23;
$cat = 131;
$zon = 95;

//a1
/*
$arr[0] = 6;
$arr[1] = 7;
$arr[2] = 3;
$arr[3] = 45;
$arr[4] = 23;
$arr[5] = 18;
$arr[6] = 58;
$arr[7] = 15;
$arr[8] = 29;
$arr[9] = 24;
*/
//print_r($arr); die;



$fix = "INSERT INTO fixture VALUES "."\n" ;

//echo $fix; die;

$fix_id = 2915;



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




//a2
$arr[0] = 10;
$arr[1] = 74;
$arr[2] = 12;
$arr[3] = 21;
$arr[4] = 17;
$arr[5] = 20;
$arr[6] = 37;
$arr[7] = 2;
$arr[8] = 44;
$arr[9] = 14;

//a3
$arr[0] = 1;
$arr[1] = 19;
$arr[2] = 22;
$arr[3] = 80;
$arr[4] = 84;
$arr[5] = 11;
$arr[6] = 51;
$arr[7] = 27;
$arr[8] = 25;
$arr[9] = 5;


//b1
$arr[0] = 35;
$arr[1] = 16;
$arr[2] = 87;
$arr[3] = 73;
$arr[4] = 59;
$arr[5] = 40;
$arr[6] = 32;
$arr[7] = 76;
$arr[8] = 67;
$arr[9] = 4;

//b2
$arr[0] = 47;
$arr[1] = 88;
$arr[2] = 49;
$arr[3] = 54;
$arr[4] = 94;
$arr[5] = 26;
$arr[6] = 43;
$arr[7] = 63;
$arr[8] = 66;
$arr[9] = 93;

//b3
$arr[0] = 65;
$arr[1] = 42;
$arr[2] = 38;
$arr[3] = 53;
$arr[4] = 57;
$arr[5] = 96;
$arr[6] = 39;
$arr[7] = 36;
$arr[8] = 46;
$arr[9] = 55;



?>
