<?php

require_once 'config.php';

//$img = imagecreatefrompng($base_image) or die("Error opening base");
$img = imagecreatetruecolor(289, 187);
$bgcolor = imagecolorallocate($img, 255, 255, 255);
$purple = imagecolorallocate($img, 0, 42, 84);
$orange = imagecolorallocate($img, 245, 159, 25);

imagefill($img,0,0,$bgcolor);
// Put in the images
for($i=0;$i<count($image);$i++) {
	$im = imagecreatefrompng($image[$i]['file']);
//	echo $image[$i]['file'];
	list($width, $height) = getimagesize($image[$i]['file']);
	$new_width = $image[$i]['width'];
	$new_height = $image[$i]['height'];
	imagecopyresized($img, $im, $image[$i]['startX'], $image[$i]['startY'], 0, 0, $new_width, $new_height, $width, $height) or die("Error resizing");
//die();



}

for($i=0;$i<count($text);$i++) {
	if($text[$i]['fontcolor'] == "purple") $textcolor = $purple;
	elseif($text[$i]['fontcolor'] == "orange") $textcolor = $orange;
	$string = convert_ascii(utf8_decode($text[$i]['text']));
	//die($string);
	imagettftext($img, $text[$i]['fontsize'],0, $text[$i]['startX'], $text[$i]['startY'], $textcolor, "./arial.ttf", $string);


}

if($barcode['url']) {
	$bc = imagecreatefrompng($barcode['url']);
	imagecopyresized($img, $bc, $barcode['startX'], $barcode['startY'], 0, 0, $barcode['width'], $barcode['height'], $barcode['width'], $barcode['height'] );

}


// End that image
header('Content-type: image/png');
imagepng($img);
imagedestroy($img);
