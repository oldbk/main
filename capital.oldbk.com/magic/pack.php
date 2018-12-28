<?php

	$pr=(int)$_GET['use'];
	$box=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.inventory WHERE id='".$pr."' AND owner='".$user[id]."'  LIMIT 1"));
	if($box[id]>0)
	{
		$present=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.paket WHERE pid='".$box[id]."' LIMIT 1"));
		if($present[id]>0)
		{
			$item=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.inventory WHERE owner='499' AND id='".$present[iid]."' limit 1"));
			//
			if($box[present]!='')
			{
				$prez=$box['present'];
				
				if($item[otdel]==72)
				{
					$prez=$box['present'].':|:'.$present['owner'];
				}
				if($item[prototype]==104 || $item[prototype]==100000009 || $item[prototype]==100000010)
				{
					$sql=", present='".$prez."', present_text='".$box['present_text']."', letter='".$item['letter']."'";
				}
				else
				{
					$sql=", present='".$prez."', present_text='".$box['present_text']."', letter='".$box['letter']."'";
				}
				$g=true;
			}
			else
			{
				$g=false;
			}
			
			if($present['dategoden']>0)
			{
				$sql1=" ,dategoden=".(time()+$present['dategoden']);
			}
			
			mysql_query("UPDATE oldbk.inventory SET owner='".$user[id]."', add_time = '".time()."' ".$sql.$sql1." WHERE id='".$present[iid]."' AND owner=499 ");
			$err='Вы вынули <b>'.$item[name].'</b>...';
			mysql_query("DELETE FROM oldbk.paket WHERE id='".$present[id]."'");
			mysql_query("DELETE FROM oldbk.inventory WHERE id='".$present[pid]."'");
			//пишем в дело.. Если открыл то что ранее упаковал - то самому себе (мол раскрыл и все)
			// если распаковал подаренное - то записываем прокачку от кого и кому
			
			$dressid = get_item_fid($item);
			$rec = array();
   			$rec['owner']=$user[id];
			$rec['owner_login']=$user[login];
			$rec['owner_balans_do']=$user['money'];
			$rec['owner_balans_posle']=$user['money'];
			
			
			
			if($g==true)
			{
				if($box['present']=='Аноним')
				{
					$daritel=check_users_city_data($present['owner']);
					$rec['add_info']=$daritel['login'];
					
				}
				$rec['type']=404; //распаковал чужой подарок
				$rec['target']=$present['owner'];
				$rec['target_login']=$box['present'];
			}
			else
			{
				
				$rec['type']=403; //распаковал свой подарок
				$rec['target']=0;
				$rec['target_login']="цветочный маг.";
			}
			
			$rec['sum_kr']=0;
			$rec['item_id']=$dressid;
			$rec['item_name']=$item['name'];
			$rec['item_count']=1;
			$rec['item_proto']=$item['prototype'];
			$rec['item_unic']=$item['unik'];
			
			$rec['item_type']=$item['type'];
			$rec['item_cost']=$item['cost'];
			$rec['item_ecost']=$item['ecost'];
			$rec['item_dur']=$item['duration'];
			$rec['item_maxdur']=$item['maxdur'];
			$rec['item_sowner']=($item['sowner']>0?1:0);
			$rec['item_ups']=$item['ups'];
			
			$rec['item_incmagic']=$item['includemagicname'];
			$rec['item_incmagic_count']=$item['includemagicuses'];
			$rec['item_incmagic_id']=$item['includemagic'];
			$rec['item_arsenal']='';
			
			$rec['item_arsenal']='';
			add_to_new_delo($rec);
			$MAGIC_OK=1;
			$bet=1;
			$sbet = 1;
		}
		else
		{
			$err='Нет такого подарка вам...';
		}		
	}
	else
	{
		$err='У Вас нет такого предмета...';
	}
	
	echo $err;
?>