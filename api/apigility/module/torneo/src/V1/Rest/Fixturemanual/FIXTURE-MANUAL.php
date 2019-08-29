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



//print_r($arr); die;



$fix = "INSERT INTO fixture VALUES "."\n" ;

//echo $fix; die;





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

//a1
/*
$arr[0] = 18;
$arr[1] = 80;
$arr[2] = 43;
$arr[3] = 74;
$arr[4] = 1;
$arr[5] = 25;
$arr[6] = 29;
$arr[7] = 51;
$arr[8] = 12;
$arr[9] = 24;
*/


//a2
$arr[0] = 10;
$arr[1] = 17;
$arr[2] = 6;
$arr[3] = 22;
$arr[4] = 5;
$arr[5] = 84;
$arr[6] = 37;
$arr[7] = 16;
$arr[8] = 44;
$arr[9] = 19;


//a2
$arr[0] = 10;
$arr[1] = 17;
$arr[2] = 6;
$arr[3] = 22;
$arr[4] = 5;
$arr[5] = 84;
$arr[6] = 37;
$arr[7] = 16;
$arr[8] = 44;
$arr[9] = 19;

//a3
$arr[0] = 15;
$arr[1] = 58;
$arr[2] = 21;
$arr[3] = 20;
$arr[4] = 23;
$arr[5] = 87;
$arr[6] = 14;
$arr[7] = 7;
$arr[8] = 27;
$arr[9] = 45;


//b1
$arr[0] = 96;
$arr[1] = 65;
$arr[2] = 66;
$arr[3] = 63;
$arr[4] = 98;
$arr[5] = 39;
$arr[6] = 94;
$arr[7] = 73;
$arr[8] = 36;
$arr[9] = 88;

//b2
$arr[0] = 76;
$arr[1] = 97;
$arr[2] = 64;
$arr[3] = 38;
$arr[4] = 56;
$arr[5] = 57;
$arr[6] = 107;
$arr[7] = 93;
$arr[8] = 69;
$arr[9] = 47;

//b3
$arr[0] = 101;
$arr[1] = 42;
$arr[2] = 55;
$arr[3] = 26;
$arr[4] = 53;
$arr[5] = 4;
$arr[6] = 46;
$arr[7] = 59;
$arr[8] = 67;
$arr[9] = 71;



?>

