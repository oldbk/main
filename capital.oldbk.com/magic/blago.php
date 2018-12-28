<?php
// magic идентификацыя
	//if (rand(1,2)==1) {

////M_conf=
$eff=5000;

		if (!($_SESSION['uid'] >0)) header("Location: index.php");
        if($access[i_angel])
        {
			$target=$_POST['target'];
			$tar = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '{$_POST['target']}' AND align in (".$allow_align.") LIMIT 1;"));
            if($tar['id']>0)
            {
            	$exist_ef=mysql_fetch_array(mysql_query('select * from effects where owner ='.$tar[id].' AND type ='.$eff.' '));
            }

			if($tar['id']>0 && $exist_ef[id]>0)
	        {
	            echo "<font color=red><b>На персонаже \"$target\" уже есть благословление!</b></font>";
	        }
	        elseif($tar[bpstor]>0 && $tar[bpstor]!=$t)
	        {
	        	echo "<font color=red><b>Соклановцы персонажа \"$target\" уже воюют за другую сторону!</b></font>";
	        }
	        elseif ($tar['id'])
	        {

				$ok=1;
				$tf= 't'.$t.'c';
                $tz='z_curent'.$t;
				$sql_t_2=$tar[level].' >= t'.$t.'min AND '.$tar[level].' <= t'.$t.'max';
                $sql='select min(id) as id, `start`, '.$tf.','.$tz.'
                    from place_zay where '.$sql_t_2.' and `type` =62 and start>'.time().';';
            //        echo $sql;
				$chk_z=mysql_fetch_array(mysql_query($sql));

			//	print_r($chk_z);
			//	echo $chk_z[$tf] . ' ' .$chk_z[$tz].'<br>';
				if($chk_z[$tf]>$chk_z[$tz] && (time()+60*60*12)>=$chk_z[start])
                {

					if (mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`,`add_info`) values
					('".$tar['id']."','Благословление на бой','".$chk_z[start]."','".$eff."','".$t."');")) {

                        if($tar[klan]!='')
                        {
                        	$sql= ' klan= "'.$tar[klan].'"';
                        }
                        else
                        {
                        	$sql= ' id= '.$tar['id'];
                        }
                        $sql='Update users set bpstor='.$t.' WHERE '.$sql.';';
                       // echo $sql;
                        mysql_query($sql);

                        $sql='Update place_zay set '.$tz.'=('.$tz.'+1) WHERE id='.$chk_z[id].';';
                    //    echo $sql;
                        mysql_query($sql);

						$messtel=" благословил Вас на бой на Арене Богов";
						tele_check($target,$messtel);
						echo "<font color=red><b>\"$target\" благословлен на ближайший бой</b></font>";
					}
					else {
						echo "<font color=red><b>Произошла ошибка!<b></font>";
					}
                }
                else
                {
                	echo "<font color=red><b>Вы исчерпали благославления на ближайший бой ".$tar[level]." уровней! Или этот бой начнется более чем через 12 часов</b></font>";
                }
			}
			else {
				echo "<font color=red><b>Персонаж \"$target\" не существует или у него не та склонность<b></font>";
			}
		}
		else
		{
				echo "<font color=red><b>Не надо так делать...<b></font>";
		}
?>
