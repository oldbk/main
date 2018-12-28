<?php
// magic идентификацыя
	//if (rand(1,2)==1) {

////M_conf=
$eff=5000;
      // echo 'QWEQWEQWEQWe';
        if($user[align]==2.12)
        {
        	$allow_align='1, 1.1, 1.2, 1.3, 1.5, 1.7, 1.75, 1.9, 1.91, 1.93, 1.99, 6';
        }
        if($user[align]==2.8)
        {
        	$allow_align='2';
        }
        if($user[align]==2.2)
        {
        	$allow_align='3';
        }
        if($user[align]==2.4 || $user[align]==2.7)
        {
        	$allow_align='1, 1.1, 1.2, 1.3, 1.5, 1.7, 1.75, 1.9, 1.91, 1.93, 1.99, 6, 2, 3';
        }

		if (!($_SESSION['uid'] >0)) {
		header("Location: index.php");
		die();
		}
        if($access[i_angel])
        {
         //   echo 'r';
			$target=$_POST['target'];
			$tar = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '{$_POST['target']}' AND align in (".$allow_align.") LIMIT 1;"));
            if($tar['id']>0)
            {
            	$exist_ef=mysql_fetch_array(mysql_query('select * from effects where owner ='.$tar[id].' AND type ='.$eff.' '));
            }

			if($tar['id']>0 && $exist_ef[id]>0)
	        {
	            mysql_query('delete from effects where owner ='.$tar['id'].' AND type = '.$eff.';');
	            $t=$tar[bpstor];
                 mysql_query('update place_zay set `z_curent'.$t.'`=(`z_curent'.$t.'`-1) WHERE t1min='.$tar[level].' order by start limit 1;');

	            echo "<font color=red><b>Удалено благословление с персонажа \"$target\".</b></font>";
	        }
			else
			{
				echo "<font color=red><b>Персонаж \"$target\" не существует или на нем нет благословления<b></font>";
			}
		}
		else
		{
				echo "<font color=red><b>Не надо так делать...<b></font>";
		}
?>
