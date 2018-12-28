#!/usr/bin/php
<?php
include "/www/capitalcity.oldbk.com/cron/init.php"; //CAPLITAL CITY ONLY

/*if( !lockCreate("cron_curs_job") ) {
    exit("Script already running.");
}
*/


function get_content()
	{
							//$date = date("d/m/Y");
							//$link = "http://www.cbr.ru/scripts/XML_daily.asp?date_req=$date";
							$link = "http://www.cbr.ru/scripts/XML_daily.asp?";
							
							$fd = fopen($link, "r");
							$text="";
							if (!$fd) echo "ъЅ–“Ѕџ…„Ѕ≈ЌЅ— ”‘“Ѕќ…√Ѕ cbr.ru  ќ≈ ќЅ ƒ≈ќЅ";
							else
							{
								while (!feof ($fd)) $text .= fgets($fd, 4096);
							}
							fclose ($fd);
							return $text;
	}


function get_WME_xml()
	{
							$dateY = date("Y");
							$dateM = date("m");		
							$dateD = date("d");
							$link = "https://wm.exchanger.ru/asp/XMLQuerysStats.asp?exchtype=4&grouptype=3&daystats=".$dateD."&monthstats=".$dateM."&yearstats=".$dateY;
							$fd = fopen($link, "r");
							$text="";
							if (!$fd) echo "ъЅ–“Ѕџ…„Ѕ≈ЌЅ— ”‘“Ѕќ…√Ѕ wm.exchanger.ru  ќ≈ ќЅ ƒ≈ќЅ";
							else
							{
								while (!feof ($fd)) $text .= fgets($fd, 4096);
							}
							fclose ($fd);
							return $text;	
	}

function get_WMU_xml()
	{
							$dateY = date("Y");
							$dateM = date("m");		
							$dateD = date("d");
							$link = "https://wm.exchanger.ru/asp/XMLQuerysStats.asp?exchtype=8&grouptype=3&daystats=".$dateD."&monthstats=".$dateM."&yearstats=".$dateY;
							$fd = fopen($link, "r");
							$text="";
							if (!$fd) echo "ъЅ–“Ѕџ…„Ѕ≈ЌЅ— ”‘“Ѕќ…√Ѕ wm.exchanger.ru  ќ≈ ќЅ ƒ≈ќЅ";
							else
							{
								while (!feof ($fd)) $text .= fgets($fd, 4096);
							}
							fclose ($fd);
							return $text;	
	}


function get_WMR_xml()
	{
							$dateY = date("Y");
							$dateM = date("m");		
							$dateD = date("d");
							$link = "https://wm.exchanger.ru/asp/XMLQuerysStats.asp?exchtype=2&grouptype=3&daystats=".$dateD."&monthstats=".$dateM."&yearstats=".$dateY;
							//по часам выбирать пример https://wm.exchanger.ru/asp/XMLQuerysStats.asp?exchtype=2&grouptype=4&hourstats=20&daystats=19&monthstats=07&yearstats=2016
							echo $link;							
							$fd = fopen($link, "r");
							$text="";
							if (!$fd) echo "ъЅ–“Ѕџ…„Ѕ≈ЌЅ— ”‘“Ѕќ…√Ѕ wm.exchanger.ru  ќ≈ ќЅ ƒ≈ќЅ";
							else
							{
								while (!feof ($fd)) $text .= fgets($fd, 4096);
							}
							fclose ($fd);
							return $text;	
	}

$check_old=mysql_fetch_assoc(mysql_query("select * from variables where var='ekrbonus' "));
//if ($check['value']!=date("Y-m-d") )
{
$err=0;
echo "Update Curs:".date("Y-m-d H:i:s")."\n";

	$contentwme = get_WME_xml();
	if ($contentwme)
	{
	$p = xml_parser_create();
	xml_parse_into_struct($p, $contentwme, $vals, $index);
	xml_parser_free($p);
	$Euro_WME=($vals[1][attributes][AVGRATE]);
	$Euro_WME=(ceil($Euro_WME/0.01) * 0.01);
	echo "Euro WME - $Euro_WME \n";	
	$euro=round(1/$Euro_WME,2);
	}
	else
	{
	echo "Error UPDATE CURS WME !!!\n";
	}

	$contentwmu = get_WMU_xml();
	if ($contentwmu)
	{
	$p = xml_parser_create();
	xml_parse_into_struct($p, $contentwmu, $vals, $index);
	xml_parser_free($p);
	$GRIVNA_WMU=($vals[1][attributes][AVGRATE]*0.995);
	$GRIVNA_WMU=(ceil($GRIVNA_WMU/0.01) * 0.01);
	$GRIVNA_WMU=round($GRIVNA_WMU*1.03);
	echo "Grivna WMU - $GRIVNA_WMU \n";	
	}
	else
	{
	echo "Error UPDATE CURS WMU !!!\n";
	}	
	
	$dollar=0;
	
	/*$contentwmr = get_WMR_xml();
	if ($contentwmr)
	{
	$p = xml_parser_create();
	xml_parse_into_struct($p, $contentwmr, $vals, $index);
	xml_parser_free($p);
	$dollar=($vals[1][attributes][MAXRATE]); // было AVGRATE [22:43:02 7-19-2016] Deni: оставл€й как есть, замени ток на maxrate
	echo "WMR to WMZ => $dollar \n";	
			 $ekr_bonus=($dollar/53)-1;
			 $ekr_bonus=((int)($ekr_bonus*100))/100;
			 if ($ekr_bonus<0)
			 	{
			 	$ekr_bonus=0;
			 	}
			 elseif ($ekr_bonus>0.5)
			 	{
			 	$ekr_bonus=0.5;
			 	}
	
	$dollar=round(($dollar*1),4);
	
	}
	*/

	if (true)
	{
		//echo "Error UPDATE CURS WMR !!!\n";
	//	была проблема, не обновл€лс€ курс, апи вебмани возвращало пустое значение
	//вобщем надо если такое происходит, чтоб был бэкап и шел запрос на cbr.ru и выставл€лс€ их курс*1.042
	//Deni https://www.cbr.ru/development/ надо чтоб забирало курс из двух мест и ставило больший из них сможешь быстро запилить?

					$content = get_content();
					if ($content)
					{
					$pattern = "#<Valute ID=\"([^\"]+)[^>]+>[^>]+>([^<]+)[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)[^>]+>[^>]+>([^<]+)#i";
					preg_match_all($pattern, $content, $out, PREG_SET_ORDER);
						foreach($out as $cur)
						{
						if($cur[2] == 840)
							{
							 $dollar_CB = str_replace(",",".",$cur[4]);
							 $dollar_CB=round(($dollar_CB*1.03),4);
							 echo "Dollar CB - $dollar_CB \n";
							 }
							 /*
						elseif($cur[2] == 978) 
							{
							 $euro_CB   = str_replace(",",".",$cur[4]);
							 echo "Euro - $euro \n";
							 }
						elseif($cur[2] == 980) 
							{
							 $grivna_CB   = str_replace(",",".",$cur[4]);
							 echo "Grivna SBR - $grivna \n";
							}
							*/
							echo " dollar update by CBR \n";
							$dollar=$dollar_CB;
							
							if ($dollar>0)	
								{
								mysql_query("INSERT INTO `oldbk`.`variables` SET `var`='dollar',`value`='{$dollar}' ON DUPLICATE KEY UPDATE value='{$dollar}' ");	
								}
							
							
						}
					}
					else
					{
					echo "Error UPDATE CURS CBR !!!\n";
					}

		
		
	}	
	
	//updates
	if ( ($euro>0) and ($GRIVNA_WMU>0) )
		{
		
		mysql_query("INSERT INTO `oldbk`.`variables` SET `var`='ekrkof',`value`='1' ON DUPLICATE KEY UPDATE value='1' ");
		mysql_query("INSERT INTO `oldbk`.`variables` SET `var`='ekrbonus',`value`='{$ekr_bonus}' ON DUPLICATE KEY UPDATE value='{$ekr_bonus}' ");	
		
		mysql_query("INSERT INTO `oldbk`.`variables` SET `var`='euro',`value`='{$euro}' ON DUPLICATE KEY UPDATE value='{$euro}' ");			 
		mysql_query("INSERT INTO `oldbk`.`variables` SET `var`='grivna',`value`='{$GRIVNA_WMU}' ON DUPLICATE KEY UPDATE value='{$GRIVNA_WMU}' ");			 			 
		mysql_query("INSERT INTO `oldbk`.`variables` SET `var`='grivna_wmu',`value`='{$GRIVNA_WMU}' ON DUPLICATE KEY UPDATE value='{$GRIVNA_WMU}' ");			 			 			
		
		$dtup=date("Y-m-d");
		mysql_query("INSERT INTO `oldbk`.`variables` SET `var`='curs_update',`value`='{$dtup}' ON DUPLICATE KEY UPDATE value='{$dtup}' ");	
		
		/*
		if ($check_old['value']!=$ekr_bonus)
				{
				//ѕ‘–“Ѕ„ћ—≈Ќ ”…”‘≈ЌЋ’ ƒћ— “≈∆“≈џЅ
				addchsys("top.frames['newplr'].location='http://chat.oldbk.com/plrfr.php';");
				}
		*/
				 			 			
		echo "ALL Update ok.\n";		
		}
		else
		{
		echo "Error UPDATES ALL! \n";
		}

}
/*
else
	{
	echo "No Update ok.\n";		
	}
*/
//lockDestroy("cron_curs_job");

?>