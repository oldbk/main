<HTML>
<?php
session_start();
include "connect.php";
include "functions.php";

include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';

$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
if (!(($user[klan]=='Adminion' || $user[klan]=='radminion') || ($user[id]==182783 || $user[id]== 8325)||($user[room]==44))) die('Страница не найдена :)');

  function count_kr($current_exp,$exptable) {
    $cl = 0; $money = 0; $stats = 3; $vinos = 3; $master = 1;
    $stats_count=$stats+12;//общая сумма статов на входе
    while($exptable) {
      if($current_exp >= $exptable[$cl][5]) {
        /* 0stat  1umen  2vinos 3kred, 4level, 5up*/
        $cl = $exptable[$cl][5];
        $money = $money+$exptable[$cl][3];
        $stats = $stats+$exptable[$cl][0];
        $master = $master+$exptable[$cl][1];
        $vinos = $vinos+$exptable[$cl][2];
        $stats_count=$stats_count+$exptable[$cl][0]+$exptable[$cl][2];
      }
      else
      {
        $arr = array('money'=>$money,'stats'=>$stats,'master'=>$master,'vinos'=>$vinos,'cl'=>$exptable[$cl][5],'count_stats'=>$stats_count);
      	return $arr;
      }
    }
  }
$stop=1;
//проверка тела
	if (($user[room]==44) and !(ADMIN) )
	{
	$_POST[nickname]=$user['login'];
	}


	if($_POST[nickname] && $_POST[check] == 1) 
	{
	   if(is_numeric($_POST[nickname]))
	   {
	   	$sql=' id="'.$_POST[nickname].'"';
	   }
	   else
	   {
	   	$sql= ' login="'.$_POST[nickname].'"';
	   }

	  $user_t = mysql_fetch_array(mysql_query('SELECT * FROM oldbk.users WHERE '.$sql.';'));
	  $user_t=check_users_city_data($user_t[id]);
	//  print_r($user_t);
	//  echo '<br>';
	//  print_r($user);

	  
	  if($user[id_city]!=$user_t[id_city])
	  {
	  	$txt="<font color=red><b>Обнулять персонажа можно только находясь с ним в одном городе...</b></font>";
	  	$stop=2;
	  }
	  
	  $shmots_stats= mysql_fetch_array(mysql_query('select sum(gsila) as gsila, sum(glovk) as glovk, sum(ginta) as ginta, sum(gintel) as gintel, sum(gmp) as gmp,
												sum(ghp) as ghp,
												sum(gnoj) as gnoj, sum(gtopor) as gtopor, sum(gdubina) as gdubina, sum(gmech) as gmech,
												sum(gfire) as gfire, sum(gwater) as gwater, sum(gair) as gair, sum(gearth) as gearth, sum(glight) as glight,
												sum(ggray) as ggray, sum(gdark) as gdark
												from oldbk.inventory where owner = '.$user_t[id].' and dressed = 1 and type!=12;'));

	  // print_r($user_t);
	$trsila=0;
	$tlovka=0;
	$tvinos=0;
	
	$bsila=0;
	$blovka=0;
	$binta=0;
	$bmudra=0;
	$bmaxhp=0;
	$trap=0;
	
	$bonus=mysql_query('SELECT * FROM '.$db_city[$user_t[id_city]].'users_bonus WHERE owner = '.$user_t[id].';');
	if(mysql_affected_rows()>0)
	{
		while($tr=mysql_fetch_assoc($bonus))
		{
			$bsila+=$tr['sila'];
			$blovka+=$tr['lovk'];
			$binta+=$tr['inta'];
			$bintel+=$tr['intel'];
		
			$bmudra+=$tr['mudra'];
			$bmaxhp+=$tr['maxhp'];
		}
	}
	$travma=mysql_query('SELECT * FROM '.$db_city[$user_t[id_city]].'effects WHERE type in (11,12,13,14) AND owner = '.$user_t[id].';');
	if(mysql_affected_rows()>0)
	{
		while($tr=mysql_fetch_assoc($travma))
		{
			$trsila+=$tr['sila'];
			$tlovka+=$tr['lovk'];
			$tinta+=$tr['inta'];
		}
	}
	if ($user_t['lab'] > 0) 
	{
		if ($user_t['id_city'] == 0) 
		{
			$i_have_st=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`labirint_var` WHERE  var='stat_trap'  and  `owner`='".$user_t[id]."' ;"));
			if ($i_have_st[owner]==$user_t[id]) 
			{
				$trap=3;
			}
		} 
		else 
		{
			$i_have_st=mysql_fetch_array(mysql_query("SELECT * FROM avalon.`labirint_var` WHERE  var='stat_trap'  and  `owner`='".$user_t[id]."' ;"));
			if ($i_have_st[owner]==$user_t[id]) 
			{
				$trap=3;
			}
		}
	}


	$e826 = 0;
	if ($user_t['id_city'] == 0) {
      		$li=mysql_query('SELECT * FROM oldbk.effects WHERE type in (826) AND owner = '.$user_t[id].';');
	} else {
      		$li=mysql_query('SELECT * FROM avalon.effects WHERE type in (826) AND owner = '.$user_t[id].';');
	}


      	while($lii=mysql_fetch_assoc($li)) {
		$e826 += $user_t['level'];
	}

       $victory_hp=0;
        //    ($trsila+$tlovka+$tinta)-($bsila+$blovka+$binta+$bmudra)
                                //  bpbonushp
	   $victory_hp=$user_t['bpbonushp'];
	   $current_stats['sila']=$user_t['sila']-$shmots_stats['gsila']-$bsila+$trsila;
	   $current_stats['lovk']=$user_t['lovk']-$shmots_stats['glovk']-$blovka+$tlovka;
	   $current_stats['inta']=$user_t['inta']-$shmots_stats['ginta']-$binta+$tinta;
	   $current_stats['intel']=$user_t['intel']-$shmots_stats['gintel']-$bintel-$e826;
	   $current_stats['mudra']=$user_t['mudra']-$shmots_stats['gmp']-$bmudra;
	   $current_stats['vinos']=$user_t['vinos'];
	   $current_stats['maxhp']=$user_t['maxhp']-$shmots_stats['ghp']-$bmaxhp-$victory_hp;
	   $current_stats['maxhp2']=$user_t['vinos']*6;

	   $current_stats['stats']= $user_t['stats'];

	   $current_stats['noj']=$user_t['noj']-$shmots_stats['gnoj'];
	   $current_stats['mec']=$user_t['mec']-$shmots_stats['gmech'];
	   $current_stats['topor']=$user_t['topor']-$shmots_stats['gtopor'];
	   $current_stats['dubina']=$user_t['dubina']-$shmots_stats['gdubina'];

	   $current_stats['mfire']=$user_t['mfire']-$shmots_stats['gfire'];
	   $current_stats['mwater']=$user_t['mwater']-$shmots_stats['gwater'];
	   $current_stats['mair']=$user_t['mair']-$shmots_stats['gair'];
	   $current_stats['mearth']=$user_t['mearth']-$shmots_stats['gearth'];
	   $current_stats['mlight']=$user_t['mlight']-$shmots_stats['glight'];
	   $current_stats['mgray']=$user_t['mgray']-$shmots_stats['ggray'];
	   $current_stats['mdark']=$user_t['mdark']-$shmots_stats['gdark'];
	   $current_stats['master']=$user_t['master'];

	   $current_stats['count_stats']=$current_stats['stats']+$current_stats['sila']+$current_stats['lovk']+
	   $current_stats['inta']+$current_stats['intel']+$current_stats['mudra']+$current_stats['vinos']+$trap;


	 //  $current_stats['stats']=$current_stats['count_stats']-$current_stats['vinos'];
	   $current_stats['master']=$current_stats['master']+$current_stats['mdark']+$current_stats['mgray']+$current_stats['mlight']+$current_stats['mearth']+$current_stats['mair']+$current_stats['mwater']+$current_stats['mfire']+$current_stats['noj']+$current_stats['mec']+$current_stats['topor']+$current_stats['dubina'];

	}


 //обнуляем по статам/умениям не полностью.
 $must_be=count_kr($user_t['exp'],$exptable);
 
	if (($user[room]==44) and !(ADMIN) )
	{
	$_POST[nickname]=$user['login'];
	}
 
    echo ' <FORM METHOD="POST" name=f2><input name="nickname" type="hidden" value="'.$_POST['nickname'].'">';
	 if($_POST['sbros'] && !$_POST[stop])
	 {

	 	 if(!$_POST[stop])
	 	 {
			undressall($user_t[id]);    //раздели
			mysql_query('DELETE from oldbk.users_bonus where owner='.$user_t['id'].';'); //убрали еду
			mysql_query('DELETE from avalon.users_bonus where owner='.$user_t['id'].';'); //убрали еду			
			
			mysql_query('DELETE from oldbk.effects where owner='.$user_t['id'].' AND type in (11,12,13,14,826);'); //убрали еду
			mysql_query('DELETE from avalon.effects where owner='.$user_t['id'].' AND type in (11,12,13,14,826);'); //убрали еду			
			
			mysql_query("DELETE FROM oldbk.`labirint_var` WHERE  var='stat_trap'  and  `owner`='".$user_t[id]."' ;");
			mysql_query("DELETE FROM avalon.`labirint_var` WHERE  var='stat_trap'  and  `owner`='".$user_t[id]."' ;");
	
		  //bpbonushp
		    	 $user_up = "UPDATE ".$db_city[$user_t[id_city]]."users SET intel='0', mudra='0', duh='0', bojes='0', mfire='0', mwater='0', mair='0', mearth='0', mlight='0', mgray='0', mdark='0',
		  		 sila='3', lovk='3', inta='3', vinos='".$must_be[vinos]."',maxmana = 0, stats='".$must_be[stats]."', maxhp=".($must_be[vinos]*6).",hp=".($must_be[vinos]*6).",
		   	     master='".$must_be[master]."', noj='0', mec='0', topor='0', dubina='0', bpbonushp='0', bpbonussila='0' WHERE id='".$user_t[id]."'";
	
		         if(mysql_query($user_up))
		         {
	
		         	echo '<font color=red><b>Персонаж обнулен.</b></font>';
		         	$telega = "INSERT INTO `telegraph` (`owner`,`date`,`text`) values ('".$user_t[id]."','','<font color=red>Внимание!</font> На Вашем персонаже были обнаружены лишние/недостающие параметры (статы или умения). Вашему персонажу произведен полный сброс характеристик в соответствии с опытом.');";
		             	mysql_query($telega);
		             	echo '<input name="stop" type="hidden" value="1">';
	
		         }
         	}
	 }

                                 //
$err='';
 $style='green';
if(($current_stats['count_stats'])!=$must_be['count_stats'] && $_POST)
{
	$err.='Не соответствует кол-во статов на '.(($current_stats['count_stats'])-$must_be['count_stats']).'<br>';
	$style_st='red';
}
if($current_stats['master']!=$must_be['master'] && $_POST)
{
	$err.='Не соответствует кол-во умений на '.($current_stats['master']-$must_be['master']).'<br>';
	$style_ms='red';
}

if(($current_stats['maxhp'])!=$current_stats['maxhp2'] && $_POST)
{
	$err.='Ошибка кол-ва жизней<br>';
	$style_hp='red';
}

if($err=='')
{
	$err.='Все в порядке<br>';
}

{
	echo '
		<table>
			<tr>
				<td>';
				if($user_t['in_tower']>0)
				{
					echo '<font color=red><b>Игрок в БС или Руинах</b></font>';
					$disabled='disabled="disabled"';

				}
				else
				if(($user_t[room]>=197 and $user_t[room]<=199) or ($user_t[room]>=211 and $user_t[room]<240) or ($user_t[room]>240 and $user_t[room]<270) or ($user_t[room]>270 and $user_t[room]<290))
				{
			                   echo '<font color=red><b>Игрок в ристалище</b></font>';
			                   $disabled='disabled="disabled"';
				}
				else
				if($user_t[battle]>0)
				{
					 echo '<font color=red><b>Игрок в бою</b></font>';
                   			$disabled='disabled="disabled"';
				}
				else
				if($stop==2)
				{
					echo $txt;
                   			$disabled='disabled="disabled"';
				}

					echo '<br><table border=1>';
					echo '<tr><td>Стат</td><td>				  Сейчас есть<br>(бонус и травма)						</td><td>от одежды									</td><td>Бонус(еда/виктори)			</td><td>Травма на		</td><td>Свои<br>(без бонусов и травм)</td></tr>';
					echo '<tr><td>Сила:</td><td>     		'.$user_t['sila'].										'</td><td>'.$shmots_stats['gsila'].'				</td><td>'.($bsila).'				</td><td>'.($trsila).'	</td><td>'.$current_stats['sila'] .				'</td></tr>';
					echo '<tr><td>Ловкость:</td><td>		'.$user_t['lovk'].										'</td><td>'.$shmots_stats['glovk'].'				</td><td>'.($blovka).'				</td><td>'.($tlovka).'	</td><td>'.$current_stats['lovk'] .				'</td></tr>';
					echo '<tr><td>Интуиция:</td><td>		'.$user_t['inta'].										'</td><td>'.$shmots_stats['ginta'].'				</td><td>'.($binta).'				</td><td>'.($tinta).'	</td><td>'.$current_stats['inta'] .				'</td></tr>';
					echo '<tr><td>Интелект:</td><td>		'.$user_t['intel'].										'</td><td>'.$shmots_stats['gintel'].'				</td><td>'.($bintel).'				</td><td>	&nbsp;		</td><td>'.$current_stats['intel'] .			'</td></tr>';
					echo '<tr><td>Мудрость:</td><td>		'.$user_t['mudra'].										'</td><td>'.$shmots_stats['gmp'].'					</td><td>'.($bmudra).'				</td><td>	&nbsp;		</td><td>'.$current_stats['mudra'] .			'</td></tr>';
					echo '<tr><td>Выносливость(hp):</td><td>'.$user_t['vinos'].'('.($user_t['maxhp']).')'.			'</td><td>'.$shmots_stats['ghp'].'					</td><td>'.$bmaxhp.'/'.$victory_hp.'</td><td>	&nbsp;		</td><td>'.$current_stats['vinos'].'('.($current_stats['vinos']*6).')'.		'</td></tr>';
					echo '<tr><td>Свободные:</td><td>		'.$user_t['stats'].										'</td><td>&nbsp;</td><td>	&nbsp;					</td><td>&nbsp;						</td><td>&nbsp;								 	</td></tr>';
					echo '<tr><td colspan=6><hr></td></tr>';
					echo '<tr><td>Ножи:</td><td>     		'.$user_t['noj'].										'</td><td>'.$shmots_stats['gnoj'].'				</td><td>&nbsp;							</td><td>&nbsp;			</td><td>'.$current_stats['noj'] .				'</td></tr>';
					echo '<tr><td>Мечи:</td><td>     		'.$user_t['mec'].										'</td><td>'.$shmots_stats['gmec'].'				</td><td>&nbsp;							</td><td>&nbsp;			</td><td>'.$current_stats['mec'] .				'</td></tr>';
					echo '<tr><td>Топоры:</td><td>     		'.$user_t['topor'].										'</td><td>'.$shmots_stats['gtopor'].'			</td><td>&nbsp;							</td><td>&nbsp;			</td><td>'.$current_stats['topor'] .			'</td></tr>';
					echo '<tr><td>Дубины:</td><td>     		'.$user_t['dubina'].									'</td><td>'.$shmots_stats['gdubina'].'			</td><td>&nbsp;							</td><td>&nbsp;			</td><td>'.$current_stats['dubina'] .			'</td></tr>';
					echo '<tr><td>Свет:</td><td>     		'.$user_t['mlight'].									'</td><td>'.$shmots_stats['glight'].'			</td><td>&nbsp;							</td><td>&nbsp;			</td><td>'.$current_stats['mlight'] .			'</td></tr>';
					echo '<tr><td>Серая:</td><td>     		'.$user_t['mgray'].										'</td><td>'.$shmots_stats['ggray'].'			</td><td>&nbsp;							</td><td>&nbsp;			</td><td>'.$current_stats['mgray'] .			'</td></tr>';
					echo '<tr><td>Темная:</td><td>     		'.$user_t['mdark'].										'</td><td>'.$shmots_stats['gdark'].'			</td><td>&nbsp;							</td><td>&nbsp;			</td><td>'.$current_stats['mdark'] .			'</td></tr>';
					echo '<tr><td>Умения:</td><td>     		'.$user_t['master'].									'</td><td>&nbsp;								</td><td>&nbsp;							</td><td>&nbsp;			</td><td>'.$current_stats['master'] .			'</td></tr>';
					echo '<tr><td>Огонь:</td><td>     		'.$user_t['mfire'].										'</td><td>'.$shmots_stats['gfire'].'			</td><td>&nbsp;							</td><td>&nbsp;			</td><td>'.$current_stats['mfire'] .			'</td></tr>';
					echo '<tr><td>Вода</td><td>     		'.$user_t['mwater'].									'</td><td>'.$shmots_stats['gwater'].'			</td><td>&nbsp;							</td><td>&nbsp;			</td><td>'.$current_stats['mwater'] .			'</td></tr>';
					echo '<tr><td>Воздух:</td><td>     		'.$user_t['mair'].										'</td><td>'.$shmots_stats['gair'].'				</td><td>&nbsp;							</td><td>&nbsp;			</td><td>'.$current_stats['mair'] .				'</td></tr>';
					echo '<tr><td>Земля:</td><td>     		'.$user_t['mearth'].									'</td><td>'.$shmots_stats['gearth'].'			</td><td>&nbsp;							</td><td>&nbsp;			</td><td>'.$current_stats['mearth'] .			'</td></tr>';
					
					echo '<tr><td  colspan=4></td></tr>';
					
					echo '</table> <br><br>';
					echo '>Итого сейчас :<br>
					<font color="'.($style_st!=''?$style_st:$style).'"><b>статов(без одежды, травм, ловушек и бонусов): '.$current_stats['count_stats'].' Должно быть: '.$must_be['count_stats'].'<b></font><br>
					<font color="'.($style_ms!=''?$style_ms:$style).'"><b>Умений(без одежды, травм, ловушек и бонусов): '.$current_stats['master'].' Должно быть: '.$must_be['master'].'<b></font><br>
					<font color="'.($style_hp!=''?$style_hp:$style).'"><b>Жизней(без одежды, травм, ловушек и бонусов): '.$current_stats['maxhp'].' Должно быть: '.$current_stats['maxhp2'].'<b></font><br>
					';

}
/*

<td><input size=4  name=sila type=text value=0></td>
<td><input size=4  name=lovk type=text value=0></td>
<td><input size=4  name=inta type=text value=0></td>
<td><input size=4  name=intel type=text value=0></td>
<td><input size=4  name=mudra type=text value=0></td>
<td><input size=4  name=vinos type=text value=0></td>
<td><input size=4  name=stats type=text value=0></td>
*/
echo '<font color="'.$style.'"><b>'.$err.'</b></font><br>';

?>

<h3> Обнуление статов и умений </h3>
<input type="hidden" name="check" value="1">
Введите ник / ID
<input type="text" name="nickname" value="<?=$_POST[nickname]?>"><input type="submit" value="NEXT">
	<?
		if($_POST)
		{
			echo '<input type="submit" name="sbros" value="Сбросить" '.$disabled.'>';
		}
	?>
</FORM>
</HTML>

