<?php

$filename='/www/oldbk.com/api/_cache/castles.xml';
$file_ok=file_exists($filename);
$cache_time=120; //120 секунд

if ($file_ok) {
    $file_stat=stat($filename);
    $file_mk_time=$file_stat[9];
}

if ( ($file_ok) and ($file_mk_time+$cache_time)>=time()) {
	// файл есть и актуальный
	// выдаем его и все
	header ("content-type: text/xml");
	print file_get_contents($filename);
} else {
	//файла нема или он просроченый делаем новый 
	//1 ищем данные
	include "/www/oldbk.com/connect.php";
	include "/www/oldbk.com/castles_config.php";
	include "/www/oldbk.com/castles_functions.php";
	$data=mysql_query("SELECT * FROM oldbk.castles WHERE id != 155");

	if (mysql_num_rows($data) > 0) {
   		$to_out = "<?xml version=\"1.0\" encoding=\"windows-1251\"?>\n";
		$to_out .= "<castles refresh=\"".time()."\" >\n";
    		while($row=mysql_fetch_array($data)) {
	   		$to_out.="<castle id=\"{$row[id]}\">\n";
	   		$to_out.="<name>".$castles_config[$row['num']]['name']." [".$row['nlevel']."] </name>\n";
	   		$to_out.="<status>".GetCastleStatus(array(),$row)."</status>\n";
	   		$to_out.=" </castle>\n";
	   	}
    		$to_out.="</castles>";	   
   	}   
   
   
	// выдаем и сохраняем
	header ("content-type: text/xml");
	print $to_out;

	$fp = fopen ($filename,"w"); //открытие
	flock ($fp,LOCK_EX);
	fputs($fp,$to_out);
	fflush ($fp);
	flock ($fp,LOCK_UN);
	fclose ($fp);

}

?>
