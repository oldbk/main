<?php
// magic сброс МФ
//
//ini_set('display_errors','On');

$upsa=array("0" => "0","1" => "2","2" => "5","3" => "9","4" => "15","5" => "25");


//	echo "TESTTTTT<br>";

if ($user['battle'] > 0) {
	echo "Не в бою...";
} else

{
if($user[klan]=='radminion')
{
	print_r($_GET);
	echo '<br>';
    print_r($_POST);
}
/*
Array ( [edit] => 1 [use] => 335602 )
Array ( [sd4] => 6 [target] => Кольцо созидания (МФ) )
 */
 $ss='';
 $ss1='';
 if ( strpos($_POST[target],"Кольцо созидания") !== FALSE  || strpos($_POST[target],"Кольцо вдохновения") !== FALSE)
  {

  }
  else
  {
  	$ss="and ups!=0";
  	$ss1="(`name` LIKE '%(МФ)%' ) and";
  }
	if (!($_SESSION['uid'] >0)) header("Location: index.php");

		$sql="SELECT * FROM oldbk.`inventory` WHERE ".$ss1." mfbonus=0 and karman=0 ".$ss." and
		( mfkrit!=0 or  mfakrit!=0 or mfuvorot!=0 or mfauvorot !=0 ) and setsale=0 and  dressed=0
		AND `owner` = '{$user['id']}' AND `name` = '{$_POST['target']}'  LIMIT 1;";

if($user[klan]=='radminion')
{
		echo $sql;
}

		$dress = mysql_fetch_array(mysql_query($sql));

		$sql = "SELECT * FROM oldbk.`inventory` WHERE `name` = 'Сброс Модификаторов' AND `owner` = '{$user['id']}' LIMIT 1;";

if($user[klan]=='radminion')
{
		echo $sql;
}
		$svitok = mysql_fetch_array(mysql_query($sql));

		$proto=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`shop` WHERE `id` = '{$dress['prototype']}' LIMIT 1;"));

			if ($dress && $svitok)
			{
				$rr=n_fields(mfs);
				$rr[]='mfbonus';
				$dn_item=downgrade_item($dress,$rr);  //сброс МФ/статов

				if($dn_item[delta_stat]!=$dress[stbonus] || $dn_item[delta_mf]!=$dress[mfbonus])
				{
					$str='';
					for($i=0;$i<count($rr);$i++)
					{
					$str.=" " .$rr[$i]."= '".$dn_item[$rr[$i]]."',";   //собираем апдейт в инвентарь
					}
					//стандартное обновление всего по пользователю.
					$str=substr($str, 0, -1);
					$str= "UPDATE oldbk.`inventory` SET ".$str." WHERE id ='".$dress['id']."' AND `dressed` = 0 AND `setsale`=0 AND owner = '{$_SESSION['uid']}';";
					//echo  $str;
					if(mysql_query($str))
					{
						echo "<font color=red><b>Модификаторы предмета \"{$_POST['target']}\" удачно сброшены<b> </font> ";
						$bet=1;
						$sbet = 1;
					}
					else
					{
					echo "<font color=red><b>Произошла ошибка!<b></font>";
					}
				}
				else
				{
					echo "<font color=red><b>Произошла ошибка!<b></font>";
				}
			}
			else
			{
				echo "<font color=red><b>Неправильное имя предмета, или неправильный свиток, или ваша вещь лежит в кармане, или на ней есть свободные Модификаторы<b></font>";
			}
}
        /*
 function downgrade_item($row,$rr)
 {
        $prot = mysql_fetch_assoc(mysql_query("SELECT * FROM `shop` WHERE  id = '".$row[prototype]."' LIMIT 1;")); //смотрим прототип
        $st=0;

        for($i=0;$i<count($rr);$i++)
        {
          $dn_item[$rr[$i]]=$prot[$rr[$i]];   //собираем нужные поля от прототипа в поля которые будут сброшены
        }
        //тут всякие расчеты цен, сумм статов и МФ и тд

 		$dn_item[sh_stat]=$row[gsila]+$row[glovk]+$row[ginta]+$row[gintel]+$row[stbonus];
        $dn_item[prot_stat]=$prot[gsila]+$prot[glovk]+$prot[ginta]+$prot[gintel];
		$dn_item[delta_stat]=$dn_item[sh_stat]-$dn_item[prot_stat];

		$dn_item[pr_stat]=$dn_item[delta_stat]*5;

        $dn_item[sh_mf]=$row[mfkrit]+$row[mfakrit]+$row[mfuvorot]+$row[mfauvorot]+$row[mfbonus];
        $dn_item[prot_mf]=$prot[mfkrit]+$prot[mfakrit]+$prot[mfuvorot]+$prot[mfauvorot];
        $dn_item[delta_mf]=$dn_item[sh_mf]-$dn_item[prot_mf];
        $dn_item[pr_mf]=round($row[cost]/10);

        //в зависимости от того что сбрасываем - возвращаем статы/мф  свободные статы/мф  и суммы денег для добавления в инвентарь
        if(in_array('gsila',$rr)){
		$dn_item[stbonus]=$dn_item[delta_stat];
		$dn_item[money]=$dn_item[pr_stat];
		}
		if(in_array('mfkrit',$rr)){
		$dn_item[mfbonus]=$dn_item[delta_mf];
		$dn_item[money]=$dn_item[pr_mf];
		}

        return $dn_item;
 }    */
?>