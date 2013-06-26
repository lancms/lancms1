<?php
//config("seatmap_type", $sessioninfo->eventID, "0");


if ($_REQUEST['alfa'] == '1')
{
	include_once ('seatmap_alfa.php');
}
elseif(config("seatmap_type", $sessioninfo->eventID) == "1") {
    include_once 'seatmap_gd.php';
}
elseif(config("seatmap_type", $sessioninfo->eventID) == "0") {
    include_once 'seatmap_table.php';
}

else {
    $content .= "WTF?";
}
