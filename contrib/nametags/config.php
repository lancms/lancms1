<?php

mysql_connect("localhost", "lancmsuser", "lancmspass") or die("Could not connect to database: ".mysql_error());
mysql_select_db("lancms_db") or die (mysql_error());



$base_image = 'baseimage.png';
/*
$image[0]['file'] = 'GL-logo.png';
$image[0]['startX'] = 0;
$image[0]['startY'] = 15;
$image[0]['width'] = 100;
$image[0]['height'] = 70;

$image[1]['file'] = 'GO-logo.png';
$image[1]['startX'] = 170;
$image[1]['startY'] = 30;
$image[1]['width'] = 70;
$image[1]['height'] = 70;
*/
$image[0]['file'] = 'globeorg-70x91.png';
$image[0]['startX'] = 190;
$image[0]['startY'] = 10;
$image[0]['width'] = 70;
$image[0]['height'] = 91;





//$text[0]['text'] = "Lars Åge Kamfjord";
$text[0]['startX'] = 10;
$text[0]['startY'] = 130-10;
$text[0]['fontsize'] = 10;
$text[0]['fontcolor'] = "purple";
$text[0]['text'] = $_GET['text0'];

//$text[1]['text'] = "Admin";
$text[1]['startX'] = 10;
$text[1]['startY'] = 155 - 10;
$text[1]['fontsize'] = 15;
$text[1]['fontcolor'] = "purple";
$text[1]['text'] = $_GET['text1'];

//$text[2]['text'] = "Laaknor";
$text[2]['startX'] = 10;
$text[2]['startY'] = 110 - 10;
$text[2]['fontsize'] = 15;
$text[2]['fontcolor'] = "purple";
$text[2]['text'] = $_GET['text2'];

$text[3]['startX'] = 10;
$text[3]['startY'] = 30;
$text[3]['fontsize'] = 14;
$text[3]['fontcolor'] = "purple";
$text[3]['text'] = "Globe";

$text[4]['startX'] = 60;
$text[4]['startY'] = 30;
$text[4]['fontsize'] = 14;
$text[4]['fontcolor'] = "orange";
$text[4]['text'] = "LAN";

$text[5]['startX'] = 100;
$text[5]['startY'] = 30;
$text[5]['fontsize'] = 14;
$text[5]['fontcolor'] = "purple";
$text[5]['text'] = "23";


#$barcode['text'] = "UID2";
$barcode['text'] = $_GET['bar'];
$barcode['height'] = 30;
$barcode['width'] = 160 + 20; // Default = 160, gives room for 6 digits
$barcode['url'] = "http://spock.globelan.net/~lak/nametags/barcode.php?text=0&format=png&barcode=".$barcode['text']."&height=".$barcode['height']."&width=".$barcode['width'];
$barcode['startX'] = 289-160-20;
#$barcode['startY'] = 0;// 156-30;
$barcode['startY'] = 187-30;













/* ----------------------------------------------- */
/* ----------------------------------------------- */
/* ----------------------------------------------- */
/* -     No edit should be done below this line  - */
/* ----------------------------------------------- */
/* ------------------(functions)------------------ */
/* ----------------------------------------------- */



function splitcmd($array) {
	$ex = explode(":", $array);
	return $ex;
}

function convert_ascii($string) {
	$result = '';
		for ($i=0; $i<strlen($string); $i++)
		{
			$char = $string{$i};
			$asciivalue = ord($char);
			$result .= "&#".$asciivalue.";";

		}

	return $result;
}


function query($q) {
$qu = mysql_query($q) or die("Kunne ikke kjøre '$q' grunnet ".mysql_error());
return $qu;
}

function num($q) {
$n = mysql_num_rows($q);
return $n;
}

function fetch($q) {
$r = mysql_fetch_object($q);
return $r;
}

