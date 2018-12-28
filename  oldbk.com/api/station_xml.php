<?php

$filename='/www/oldbk.com/api/_cache/station.xml';
$file_ok=file_exists($filename);
$cache_time=120; //120 секунд

if ($file_ok)
    {
    $file_stat=stat($filename);
    $file_mk_time=$file_stat[9];
    }

if ( ($file_ok) and ($file_mk_time+$cache_time)>=time())
{
//файл есть и актуальный
// выдаем его и все
header ("content-type: text/xml");
print file_get_contents($filename);
} else 
{
//файла нема или он просроченый делаем новый 
//1 ищем данные
   include "/www/oldbk.com/connect.php";
   $city_name[0]='Capital City';
   $city_name[1]='Avalon City';
   $data=mysql_query("SELECT *, 'Capital City' as station FROM oldbk.station UNION SELECT *, 'Avalon City' as station FROM avalon.station  ORDER BY starttime;");

   if (mysql_num_rows($data)>0)
   {
   $to_out="<?xml version=\"1.0\" encoding=\"windows-1251\"?>\n";
   $to_out.="<stations refresh=\"".time()."\" >\n";
	$h = date("H");
	$m = date("i");		
    	while($row=mysql_fetch_array($data))
	   {
	$t = explode(":",$row['starttime']);
	$ht = $t[0];
	$mt = $t[1];
	if ($h > $ht) {
			continue;
			} elseif ($h == $ht) 
					{
					if ($m > ($mt-2)) 
						{
						continue;
						}
					}
	   
	   $to_out.="<ticket start_city=\"{$row[station]}\" finish_city=\"".$city_name[$row[tocity]]."\" >\n";
	   $to_out.="<length>".$row[time]."</length>\n";
	   $to_out.="<start>".$row[starttime]."</start>\n";
	   $to_out.="<cost>".$row[price]."</cost>\n";
	   $to_out.="<count>".$row[count]."</count>\n";	   
	   $to_out.=" </ticket>\n";
	   }
    $to_out.="</stations>";	   
   }
   else
   {

   $cap_err='Билетов не найдено!';
   }
   /*
   */
   
   
   
   if (!($cap_err))
   {
   //выдаем и сохраняем
   header ("content-type: text/xml");
   print $to_out;

	$fp = fopen ($filename,"w"); //открытие
	flock ($fp,LOCK_EX);
	fputs($fp,$to_out);
	fflush ($fp);
	flock ($fp,LOCK_UN);
	fclose ($fp);
   }
   else echo "false";

}

?>
