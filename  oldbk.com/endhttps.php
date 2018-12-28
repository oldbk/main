<?php
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "https") {
	$out = ob_get_contents();
	ob_end_clean();
	$out = str_replace('http://','https://',$out);
	$out = str_replace('https://capitalcity.oldbk.com','http://capitalcity.oldbk.com',$out);
	$out = str_replace('https://plug.oldbk.com','http://plug.oldbk.com',$out);
	$out = str_replace('https://paladins.oldbk.com','http://paladins.oldbk.com',$out);
	$counters = include $_SERVER['DOCUMENT_ROOT']."/counters/all.php";
	if (isset($isUTF8)) {
		$out = str_replace('%COUNTERS%',iconv("windows-1251","UTF-8",$counters),$out);
	} else {
		$out = str_replace('%COUNTERS%',$counters,$out);
	}
	echo $out;
} else {
	$out = ob_get_contents();
	ob_end_clean();
	$counters = include $_SERVER['DOCUMENT_ROOT']."/counters/all.php";
	if (isset($isUTF8)) {
		$out = str_replace('%COUNTERS%',iconv("windows-1251","UTF-8",$counters),$out);
	} else {
		$out = str_replace('%COUNTERS%',$counters,$out);
	}
	echo $out;
}
die();
?>