<?
session_start();
include "connect.php";
include "functions.php";
if((int)$user['id'] != 8325)
{
	//if (($user['align']<2.1 && $user['align']!=1.93) || $user['align']>2.9) die('Страница не найдена :)');
	if ($user['align']!=2.3 and  $user['align']!=2.4 and $user['align']!=2.7) die('Страница не найдена :)');

}

include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
	
 if(isset($_POST[nickname])) 
 {
   $telo = mysql_fetch_array(mysql_query("SELECT id, battle,login,level,money,id_city FROM oldbk.users WHERE login='".$_POST[nickname]."'"));   
   if ($telo[id_city]==1) { $telo = mysql_fetch_array(mysql_query("SELECT id, battle,login,level,money,id_city FROM avalon.users WHERE login='".$_POST[nickname]."'"));    }
   
   echo "ID: $telo[id] / <a taget='_blank' href='/inf.php?$telo[id]'>$telo[login]</a> [$telo[level]]<br/>";
   
  if($telo[battle]>0) {
    die("Персонаж в бою!");
  }
  
  $item = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.inventory WHERE id='".$_POST[itemid]."' AND owner='".$telo['id']."'"));
  if((int)$item['id'] <= 0)
  {
	die("Данный предмет не существует или не принадлежит персонажу <a taget='_blank' href='/inf.php?$telo[id]'>$telo[login]</a> [$telo[level]]<br/>");
  }
  
   if((int)$item["dressed"] == 1)
  {
	undressall($telo['id']);
  }  

	if($item[add_pick]!='') {
		undress_img($item);
	}

  
   if(mysql_query("UPDATE oldbk.inventory SET owner='8325', dressed=0 where id='".$item['id']."'"))
  {
	$rec['owner']=$telo[id];
	$rec['owner_login']=$telo[login];
	$rec['owner_balans_do']=$telo[money];
	$rec['owner_balans_posle']=$telo[money];
	$rec['target']=8325;
	$rec['target_login']='Повелитель багов';
	$rec['type']=301;
	$rec['sum_kr']=0;
	$rec['sum_ekr']=0;
	$rec['sum_kom']=0;
	$rec['item_id']=get_item_fid($item);
	$rec['item_name']=$item['name'];
	$rec['item_count']=1;
	$rec['item_type']=$item['type'];
	$rec['item_cost']=$item['cost'];
	$rec['item_dur']=$item['duration'];
	$rec['item_maxdur']=$item['maxdur'];
	$rec['item_ups']=$item['ups'];
	$rec['item_unic']=$item['unik'];
	$rec['item_incmagic']=$item['includemagicname'];
	$rec['item_incmagic_count']=$item['includemagicuses'];
	$rec['item_arsenal']=$item['arsenal_klan'];
	add_to_new_delo($rec);
	
	
	$telega = "INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('".$telo['id']."', '".time()."','Предмет \"".$item['name']."-".$item['id']."\" передан от \"".$telo['login']."\" к персонажу \"Повелитель багов\"');";
	mysql_query($telega);

	addchp ("<font color=red>Внимание!</font> Предмет ".$item['name']."-".$item['id']." передан от ".$telo['login']." к персонажу Повелитель багов", "{[]}".($telo['login'])."{[]}");
	
	echo "<br/>Предмет \"".$item['name']."-".$item['id']."\" передан от \"".$telo['login']."\" к персонажу \"Повелитель багов\"";
  }
  else
  {
	die("Ошибка!");
  }  
 }
?>
<HTML>
<head>
<script type="text/javascript" src="/i/globaljs.js"></script>
</head>
<body>
<h3> Изьять предмет </h3>
<FORM METHOD="POST">
<input type="hidden" name="check" value="1">
ID вещи<input type="text" name="itemid" value="<?=$_POST[itemid]?>">
Ник персонажа<input type="text" name="nickname" value="<?=$_POST[nickname]?>"><input type="submit" value="NEXT">
</FORM>
</body>
</HTML>
