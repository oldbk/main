<?php
// magic идентификацыя
if (!($_SESSION['uid'] >0)) header("Location: index.php");

if(!$data_battle[id])
{
	$data_battle=mysql_fetch_assoc(mysql_query("SELECT * FROM battle where id='".$user[battle]."'"));
}
   
if (($data_battle[type]==60) or ($data_battle[type]==61))
    {
    $lider=mysql_fetch_array(mysql_query("SELECT * FROM `place_battle` WHERE (var='leader1' and  val='".$user[id]."') OR (var='leader2' and val='".$user[id]."') LIMIT 1;"));
    }

if($data_battle['teams']!='') 
    {
     $h=explode(":||:",$data_battle['teams']);
     if ($h[0]==20000)
      	{
	$noexit="Бой изолирован...";
      	}
     }
 
$chck_battle=$data_battle;

if ($user['battle'] == 0) {	echo "Это боевая магия..."; }
elseif ($noexit!='') { echo $noexit ;}
elseif ($user['room']==200) { echo "Нельзя выйти из турнирного боя"; }
elseif($user['lab'] >0) { echo "Нельзя выйти из боя в лабиринте..."; }
elseif(!($user['hp'] > 0)) { echo "Вы уже убиты. Ждите окончания боя..."; }
elseif (($user['room'] >=210)AND($user['room'] <299)) { echo "Тут это не работает..."; }
elseif ($lider[0] > 0) { echo "Вы лидер в бою...Вы не можете выйти из боя... ";}
elseif($chck_battle['type']==100 && ($user['klan']!='radminion')){echo 'Из кланового боя выйти нельзя...';}
elseif( ($chck_battle['type']==140 || $chck_battle['type']==141 || $chck_battle['type']==150 || $chck_battle['type']==151 ) && ($user['klan']!='radminion')){echo 'Из кланового боя выйти нельзя...';}
elseif($chck_battle['type'] == 13 || $chck_battle['type'] == 14 || $chck_battle['type'] == 40 || $chck_battle['type']== 41 || $chck_battle['type']== 170 || $chck_battle['type']== 171){echo 'Из этого боя выйти нельзя...';}
elseif($chck_battle['teams'] =='Бой склонностей') {echo 'Из этого боя выйти нельзя...';}
elseif($chck_battle['teams'] =='Куча') {echo 'Из этого боя выйти нельзя...';}
elseif($chck_battle['nomagic'] >0 ){echo 'Нельзя выйти из этого боя ...';}
		else {
		// test battle
		$get_lock=mysql_fetch_array(mysql_query("select * from battle where id='{$user[battle]}';"));
		if ($get_lock[status]==0)
		{
		
		if ( ($CHAOS==true) or ($user[align]==5) )	
					{ 
					$bco=1; 
					$stor=$user[battle_t];
					} else 
					{
					$bco=2; 
					$stor=$user[battle_t];
					}
		
		$bco=3; 
		mysql_query("INSERT battle_vars (`battle`, `owner`, `bexit_count`, `bexit_team`) values ('{$user[battle]}', '{$user[id]}' , '{$bco}', '{$stor}' ) ON DUPLICATE KEY UPDATE `bexit_count`=`bexit_count`+{$bco}, `bexit_team`='{$stor}' ; ");

		mysql_query("delete from battle_fd where (razmen_to='{$user['id']}') or (razmen_from='{$user['id']}')  or (owner='{$user['id']}')  ");
		$time = time();
		mysql_query("UPDATE battle SET inf=inf+1, to1=".$time.", to2=".$time.",  t".$user[battle_t]."hist=REPLACE(t".$user[battle_t]."hist,'".BNewHist($user)."','') WHERE id = ".$user['battle']." ;");

			if($user['align'] >= '2.1' && $user['align'] <= '2.9') {
			mysql_query('UPDATE `users` SET `battle`=0 , `battle_t`=0 , `battle_fin`=0 , `hp`=`maxhp` WHERE `id` = '.$user['id'].';');
			} else {
			mysql_query('UPDATE `users` SET `battle`=0 , `battle_t`=0, `battle_fin`=0  ,`hp`=20, mana=0,   `fullmptime`='.time().' ,  `fullhptime` = '.time().'    WHERE `id` = '.$user['id'].';');
			}
			$bet=1;
			$sbet=1;
			$STEP=5;
			// Write logs
			
		$fuser = load_perevopl($user);
		if($fuser['sex'] == 1) { $lsex='вышел'; } else { $lsex='вышла'; }
		if (($user[hidden]>0) and ($user[hiddenlog]=='')) {$fuser[sex]=1;}		
		
//		addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($fuser,$user[battle_t]).' '.$lsex.' из боя!<BR>');
		addlog($user['battle'],"!:E:".time().":".nick_new_in_battle($user).":".(($fuser[sex]*100)+3)."\n");				
		
		$_SESSION['bexit'] = "Вы вышли из боя...";
		addchp ('<font color=red>Внимание!</font> Вы вышли из боя...','{[]}'.$fuser['login'].'{[]}',$user['room'],$user['id_city']);
		if ($user[battle_t]==1) {unset($boec_t1[$user[id]]);} 
		elseif ($user[battle_t]==2) {unset($boec_t2[$user[id]]);}
		elseif ($user[battle_t]==3) {unset($boec_t3[$user[id]]);}
		$user[battle]=0;

		$rabbit_log = [];

		try {
			global $app;
			$data = false;
			if (strlen($user['gruppovuha'])) $data = unserialize($user['gruppovuha']);

			if ($data !== false) {
				if (isset($data[10]) && $data[10] > 0) {
					$rabbit_log = [
						"user_id" 	=> $user['id'],
						'err_level'	=> \Monolog\Logger::INFO,
					];
				}
			}
			\components\Component\RabbitMQ\Builder::setApp($app);

			$queue = \components\Component\RabbitMQ\Builder::queue('mslots','mslots-logs');
			$queue->emit($rabbit_log);
		} catch (Exception $ex) {
			\components\Helper\FileHelper::writeException($ex, 'usebexit');
		}
			
	}
  } // end of else of late stage

?>
