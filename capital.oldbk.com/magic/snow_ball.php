<?php
// магия "Илюзая на смену образов"
if (!($_SESSION['uid'] >0)) header("Location: index.php");

	$sn[0] = " крепко слепленным ";
	$sn[1] = " замерзшим";
	$sn[2] = " рыхлым";
	$sn[3] = " талым";
	$sn[4] = " рыхлым";
	$sn[5] = " талым";
	$sn[6] = " наскоро слепленным";
	$sn[7] = " ";
	$sn[8] = " огромным";
	$sn[9] = " белым";
	$sn[10] = " пушистым";
	$sn[11] = " мягким";
	$sn[12] = " круглым";
	$sn[13] = " огромным";
	$sn[14] = " желтым ";
	
	$uron=rand(1,3);

	$txt='black';
	$ch=rand(0,count($sn)-1);
	$ball=$sn[$ch];
	if($ch==14)
	{
		$comm=1;
		$commen='Говорил же... Не ешь желтый снег!';
		$uron=10;
		$txt='red';
	}

	/*
	if($user[klan]=='Adminion'||$user[klan]=='radminion')
	{

		$comm=1;
		$commen='Говорил же... Не ешь желтый снег!';
		$uron=16;
		$txt='red';
		$obm=1;

	}*/
	
	$sn1[] = " снежком";
	$sn1[] = " комком снега";
	$sn1[] = " снежным шаром";
	$sn1[] = " шариком снега";
	$sn1[] = " шариком снега";
	$ball1=$sn1[rand(0,count($sn1)-1)];

	$sn2[] = ($user[sex]==1?' кинул':' кинула');
	$sn2[] = ($user[sex]==1?' бросил':' бросила');
	$sn2[] = ($user[sex]==1?' запульнул':' запульнула');
	$sn2[] = ($user[sex]==1?' залепил':' залепила');
	$sn2[] = ($user[sex]==1?' швырнул':' швырнула');
	$att=$sn2[rand(0,count($sn2)-1)];

	$sn3[] = " в";
	$sn3[] = " рядом с";
	$sn3[] = " прямо в";
	$sn3[] = " в лоб";
	$sn3[] = " в спину";
	$sn3[] = " прямым попаданием в";
	$sn3[] = " за воротник";
	$sn3[] = " в лицо";
	$sn3[] = " за пазуху";
	$sn3[] = " по шапке";
	$sn3[] = " в грудь";
	$sn3[] = " по ноге";
	$where=$sn3[rand(0,count($sn3)-1)];

		$coma = $att.$ball.$ball1.$where. ' <b>&quot;'.$_POST['target'].'&quot;</b>';

		$target=mysql_fetch_array(mysql_query('select * from users where login = "'.$_POST['target'].'"'));
		
	if (($target[odate]<time()-60) and  ($target[battle]==0))
	{
		echo '<font color=red><b>Персонажа нет в клубе</b></font>';
	}
	else
	if($user[room]==31)
	{
		echo '<font color=red><b>Тут нельзя разбрасывать снег... Архивариус простынет...</b></font>';
	}
    else
    {
		//bonus by Fred ^)))
		$get_eff=mysql_fetch_array(mysql_query('select * from effects where type=33 and owner = "'.$target[id].'"'));
		if ($get_eff[id] >0 ) {$uron=$uron*15+(mt_rand(1,9)); $obm=0; }

		if ($obm==1)
		   {
		  	 mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`) values ('".$target[id]."','Обморожение',".(time()+900).",33);");
		   }
		/////////////////////
		if($target[login]==$user[login])
		{
			echo '<font color=red><b>Кидать в себя?..<b></font>';
		}
		elseif (($target['battle']>0) and ($user['battle']==$target['battle'])) 
		{
			//if (($user['id']==14897) or ($user['id']==188))
			{
			//оба в одном бою!
			if ($target['hp']>30)
			{
			$baf_dem=rand(10,30);
			
				mysql_query("UPDATE users set hp=hp-'{$baf_dem}' where id='{$target['id']}' and hp>30 LIMIT 1; ");

				if (mysql_affected_rows()>0)
					{

			       	       $uron_str=$baf_dem;
       					$prhp=$target['hp']-$baf_dem;
			       	       
					if (($target['hidden'] > 0) and ($target['hiddenlog'] ==''))
		 		        {   $txtdm='[??/??]';  $uron_str=$baf_dem."|??";   } else  {  $txtdm='['.$prhp.'/'.$target['maxhp'].']';    }
		 		             			
					$add_log="\n!:1:".time().":".nick_new_in_battle($target).":".(100*$user['sex']+mt_rand(1,5))."::".nick_new_in_battle($user).":".mt_rand(0,14).":".mt_rand(1,5).":".mt_rand(1,12)."::".$uron_str.":".$txtdm;
					addlog($user['battle'],$add_log."\n");
					$bet=1;
					$sbet = 1;
					$MAGIC_OK=1;
					
					if (mt_rand(1,100)==1) //кидаем снежки в бою 1%  на карту
						{
						drop_card($user);
						}

                        try {
                            global $app;
                            $User = new \components\models\User($user);
                            $Quest = $app->quest
                                ->setUser($User)
                                ->get();
                            $Checker = new \components\Component\Quests\check\CheckerMagic();
                            $Checker->magic_id = 5276;
                            if(($Item = $Quest->isNeed($Checker)) !== false) {
                                $Quest->taskUp($Item);
                            }
                        } catch (Exception $ex) {
                            \components\Helper\FileHelper::writeArray(array(
                                'magic' => '5276',
                                'error' => $ex->getMessage(),
                                'trace' => $ex->getTraceAsString()
                            ), 'error_log');
                        }
					}
			}
		       	 else
		       	 {
				echo '<font color=red><b>Персонаж сильно слаб, Вы можете его убить снежком...<b></font>';		       	 
		       	 }      
		       }	 
		}
		elseif (($target[battle]>0) and ($user['battle']==0))
		{
			echo 'Этот персонаж в бою';
		}		
		elseif(($target[room]!=$user[room]) and ($user['klan']!='radminion') )
		{
			echo '<font color=red><b>Персонаж в другой комнате...<b></font>';
		}
		else
		{
			if ($user[hidden]>0) { $usnik="<i>Невидимка</i>"; } else {$usnik=$user[login]; }
			$messch ="<b>&quot;{$usnik}&quot;</b> {$coma} <b>(<font color={$txt}>-{$uron}</font>)</b>";
		 	if ($uron>$target[hp]) { $uron=$target[hp]; }
			mysql_query("UPDATE `users` SET `hp` = (hp-".$uron.") WHERE `id` = ".$target['id'].";");
			addch("<img src=i/magic/snezhok.gif> $messch",$user['room'],$user['id_city']);
				if($comm==1)
				{
					addchp($commen,"Комментатор",$user['room'],$user['id_city']);
				}
			echo "<font color=red><b>Вы кинули снежок в ".$target[login]."<b></font>";
			$bet=1;
			$sbet = 1;
		}
	}

?>