<?


function get_directory_list($path)
{
$max_dir=10; //максимально папок за проход


if ($handle = opendir($path))
{
$c=0;
    while (false !== ($file = readdir($handle)))
    { 
        if ($file != "." && $file != "..")
        {
        //есть папка - провер€ем не сегодн€шнее ли у папки число создани€
       	   $stat=stat($path.$file);
	   $data_mod=$stat[9];
	   $data_now=time();
           if ($data_now-$data_mod > (3*24*60*60) ) 
           	{
           	//типа все гуд
           	//теперь ищим в папке txt файлы
           	$fc=0;
           	foreach (glob($path.$file."/*.txt") as $filename) 
           		{
           		$fc++;
           			//файл есть надо его сжать

           			$zip_name=$filename.".gz"; 
           			$data = implode("", file($filename));
				$gzdata = gzencode($data, 9);
				$fp = fopen($zip_name, "w");
				fwrite($fp, $gzdata);
				fclose($fp);
				
				//удал€ем оригинал
				unlink($filename);
				//echo "$zip_name <br>";
	 		 	//  echo "$filename size " . filesize($filename) . "\n";
	 		 
			}
		 if ($fc>0)
		 	{
		 	echo "GZIP:".$fc."files <br>\n";
		 	//если больше 0 значит с папкой работали делаем папке счет +1
	 	       $c++; 
		 	}
			
    		}
    	//echo $path.$file;
    	//echo "<br>";	
        } 
     if ($c>$max_dir) return;
    }
    closedir($handle); 
} 

}

$input_dir='/www_logs3/combats_log/';
get_directory_list($input_dir);


?>