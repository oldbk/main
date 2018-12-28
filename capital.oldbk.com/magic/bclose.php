<?
$do_not_close=array(10000,190672,14897,9,10);

$btid=(int)$_POST['target'];

$btl=mysql_fetch_array(mysql_query("SELECT * FROM `battle` WHERE `id` = '{$btid}' and status=0 and win=3  LIMIT 1;"));

$arrt1=explode(";",$btl['t1']);
$arrt2=explode(";",$btl['t2']);


if ( ($btl['coment'] =='Бой с Исчадием Хаоса') OR ($btl['coment'] =='<b>Бой с Волнами Драконов</b>') OR ($btl['coment'] =='<b>Бой с Пятницо</b>') OR   (search_arr_in_arr($do_not_close,array_merge($arrt1,$arrt2))==true) ) 
{
	err('Нельзя использовать в этом поединке!');
}
else
if ($btl[id]>0)
{

// new hiddent
		if(($user[hidden] >0) and ($user[hiddenlog] ==''))
		 {
			$user_nick="<B><i>Невидимка</i></B>";
			$sexi[0]='закрыл';
 		      	$sexi[1]='закрыл';
 		      	$user[sex]=1;
		}
		else
		{
			$fuser=load_perevopl($user); 
			$user[sex]=$fuser[sex]; 
			$user_nick=nick_align_klan($fuser);
			$sexi[0]='закрыла';
 		      	$sexi[1]='закрыл';
		}
		
		addch("<img src=i/magic/bclose.gif> {$user_nick}, применив магию, {$sexi[$user[sex]]} <a href=logs.php?log=".$btid." target=_blank>бой</a> от вмешательства.",$user['room'],$user['id_city']);	
		
//		addlog($btid,'<span class=date>'.date("H:i").'</span> '.$user_nick.'  '.$sexi[$user[sex]].' бой от вмешательства!<BR>');
		addlog($btid,'!:X:'.time().':'.nick_new_in_battle($user).':'.($user[sex]+500)."\n");		
		
		mysql_query("UPDATE battle set teams='{$user_nick}' WHERE `id` = '{$btid}' and status=0 and win=3  LIMIT 1;");
		echo "Вы закрыли бой от вмешательства!";
}
else
{
echo "Бой не найден или окончен!";
}
?>