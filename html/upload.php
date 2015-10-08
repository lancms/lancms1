<?php
require_once __DIR__ . '/../include.php';

// FIXME Should probably have some kind of checking to see refererer etc.

if($sessioninfo->userID > 1) $can_upload = TRUE;

if($can_upload == TRUE) {

	$hide_smarty = TRUE;

	$file_base = 'files/';
	$file_type = $_POST['file_type'];
	$file_dir = $file_base.$file_type."/";
	if(!is_dir($file_dir)) die(_("No such file directory"));
	if(!is_writable($file_dir)) die(_("File directory not writable"));

	$filename = $_FILES['uploadfile']['name'];
	$filename_new = str_replace(str_split(preg_replace("/([[:alnum:]_\.-]*)/","",$filename)),"",$filename);
	$target_path = $file_dir.$filename_new;
	echo $target_path;	
	if(!file_exists($file_dir.$filename_new)) {
		if(move_uploaded_file($_FILES['uploadfile']['tmp_name'], $target_path))
		$upload_OK = TRUE;
		else die(_("Upload failed, could not move file").$target_path);
	} // end !file_exists
	else die(_("Upload failed, file exists"));

	if($upload_OK == TRUE) {
		switch($file_type) {
			case "profilepic":
				$extra = $sessioninfo->userID;
				break;
		} // End switch 
		db_query("INSERT INTO ".$sql_prefix."_files SET
			uploaded_by = '$sessioninfo->userID',
			file_path = '$target_path',
			file_type = '".db_escape($file_type)."',
			extra = '$extra'
		");

} // End can_upload == TRUE;
}
