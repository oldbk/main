<?php
		if (!($_SESSION['uid'] >0))
		{
			header("Location: index.php");
			die();
		}
		$chck=mysql_fetch_array(mysql_query('select * from effects where owner ="'.$user[id].'" AND type=4997 LIMIT 1;'));

		if($chck[id]>0)
		{
			if (date("n") == 2) {
				echo 'Не перебарщивайте с алкоголем...  ;)';				
			} elseif (date("n") == 3) {
				echo 'Не перебарщивайте с калориями...  ;)';
			}
		}
		else
		{
			$sql='insert into effects
			(`type`,`name`,`time`,`owner`,`add_info`)
			VALUES
			("4997","Опыт","'.(time()+60*60*2).'","'.$user[id].'", "0.5"),
			("4998","Объедание","'.(time()+60*60*2).'","'.$user[id].'", "");';
			//Тип 4998  дает возможность смотреть голых баб в инфе. Если выпить флягу и получить данный эфект..
            		//4997 - просто на отслежку опыта..
			mysql_query($sql);
			if(mysql_affected_rows()>0)
			{
				mysql_query('update users set `expbonus`=`expbonus`+"0.5" where id='.$user[id].';');
				
				$bet=1;
				$sbet = 1;

				if (date("n") == 2) {
			        	echo 'Вы опьянели... ';
				} elseif (date("n") == 3) {
			        	echo 'Скушали пирожное... ';
				}
	        	}
        }
?>
