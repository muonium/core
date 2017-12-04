<?php
/* Functions that can be used everywhere */

/* Convert file id to b64 encoded for download url */
function setURL($id) {
	return rtrim(strtr(base64_encode($id), '+/', '-_'), '=');
}

/* Convert b64 encoded from download url to file id */
function getFileId($b) {
	return base64_decode(str_pad(strtr($b, '-_', '+/'), strlen($b) % 4, '=', STR_PAD_RIGHT));
}

/* Return human readable size */
function showSize($size, $precision = 2) {
	// $size => size in bytes
	if(!is_numeric($size)) return 0;
	if($size <= 0) return 0;
	$base = log($size, 1000);
	$suffixes = array_values((array)\library\MVC\Languages::$txt->Units);
	return round(pow(1000, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}
