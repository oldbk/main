<?
function add_to_new_delo($rec)
{
		$q = mysql_query("INSERT INTO `oldbk`.`new_delo` SET `owner`='{$rec['owner']}',
						`owner_login`='{$rec['owner_login']}',
						`owner_balans_do`='{$rec['owner_balans_do']}',
						`owner_balans_posle`='{$rec['owner_balans_posle']}',
						`owner_rep_do`='{$rec['owner_rep_do']}',
						`owner_rep_posle`='{$rec['owner_rep_posle']}',
						`target`='{$rec['target']}',
						`target_login`='{$rec['target_login']}',
						`type`='{$rec['type']}',
						`sdate`='".time()."',
						`sum_kr`='{$rec['sum_kr']}',
						`sum_ekr`='{$rec['sum_ekr']}',
						`sum_kom`='{$rec['sum_kom']}',
						`sum_rep`='{$rec['sum_rep']}',
						`item_id`='{$rec['item_id']}',
						`aitem_id`='{$rec['aitem_id']}',
						`add_info`='{$rec['add_info']}',
						`item_name`='{$rec['item_name']}',
						`item_count`='{$rec['item_count']}',
						`item_type`='{$rec['item_type']}',
						`item_cost`='{$rec['item_cost']}',
						`item_dur`='{$rec['item_dur']}',
						`item_maxdur`='{$rec['item_maxdur']}',
						`item_ups`='{$rec['item_ups']}',
						`item_unic`='{$rec['item_unic']}',
						`item_incmagic_id`='{$rec['item_incmagic_id']}',
			                    	`item_ecost`='{$rec['item_ecost']}',
			                    	`item_sowner`='{$rec['item_sowner']}',
						`item_proto`='{$rec['item_proto']}',
						`item_incmagic`='{$rec['item_incmagic']}',
						`item_incmagic_count`='{$rec['item_incmagic_count']}',
						`item_arsenal`='{$rec['item_arsenal']}',
						`battle`='{$rec['battle']}',
						`bank_id`='{$rec['bank_id']}' ;");
		if ($q === FALSE) return false;

	        $d_id=mysql_insert_id();
	        $sert=array(200001,200002,200005,200010,200025,200050,200100,200250,200500);
	        
	        
		if(($rec['item_type']>0 && $rec['item_type']<12 || $rec['item_type']==28 || $rec['item_type']==555 || $rec['item_type']==27 || (in_array($rec['item_proto'],$sert))) && $rec['type']!=32 && $rec['type']!=33)
		{
			$it=explode(',',$rec['item_id']);
			$sql="INSERT INTO `oldbk`.`new_delo_it_index` (`item_id`,`delo_id`) VALUES ";
			for($j=0;$j<count($it);$j++)
			{
				$sql.="('".trim($it[$j])."','".$d_id."'),";
			}
			$sql=substr($sql,0,-1).";";
			mysql_query($sql);
		}
	return true;
}


function  update_new_delo($rec)
{
	mysql_query("UPDATE `oldbk`.`new_delo` SET
	`owner_balans_do`='".$rec['money']."',
	`owner_balans_posle`='".$rec['money']."',
	`sum_kr`='".$rec['sum_kr']."'
	WHERE owner='".$rec['owner']."' AND type='".$rec['type']."' AND id='".$rec['id']."' LIMIT 1");
}

function login_fix_for_delo($rec)
{
	if($rec[type]==35)     {$row[target_login]="Срок годности предмета";}
	elseif($rec[type]==80) {$rec[target_login]="Свиток переноса магии";}
	elseif($rec[type]==19) {$rec[target_login]="Выбросил предметы";}
	elseif($rec[type]==401){$rec[target_login]="Цветочный магазин";}
	elseif($rec[type]==172){$rec[target_login]="Храмовая Лавка";}
	
	elseif($rec[type]==270 || $rec[type]==271) {$rec[target_login]="Квесты загорода";}
	elseif($rec[type]==1 || $rec[type]==34) {$rec[target_login]="Государственный магазин";}
	elseif($rec[type]==251 && $rec[target_login]=='лаба1') {$rec[target_login]="Лабиринт (обычный)";}
	elseif($rec[type]==251 && $rec[target_login]=='лаба2') {$rec[target_login]="Лабиринт (героический)";}
	elseif($rec[type]==251 && $rec[target_login]=='лаба3') {$rec[target_login]="Лабиринт (новичковый)";}
	elseif($rec[type]==120 || $rec[type]==124) {$rec[target_login]="Комиссионный магазин";}
	elseif($rec[type]==191 || $rec[type]==198 || $rec[type]==197 || $rec[type]==177 || $rec[type]==193 || $rec[type]==194 || $rec[type]== 179) {$rec[target_login]="Ремонтная мастерская";}
		
	return $rec;
}

function get_delo_rec($rec,$al,$sql_str="",$add_info_off)
{
	//  $rec['owner']
	//  $rec['owner_login']
	//  $rec['owner_balans_do']
	//  $rec['owner_balans_posle']

	//  $rec['owner_rep_do']
	//  $rec['owner_rep_posle']

	//  $rec['target'] кому
	//  $rec['target_login']+
	//  $rec['type']+
	//  $rec['sdate']+
	//  $rec['sum_kr']+
	//  $rec['sum_ekr']+
	//  $rec['sum_rep']+
	//  $rec['sum_kom']+
	//  $rec['item_id']+'предмет ид c городом через запятую'
	//  $rec['item_name'] +
	//  $rec['item_count']+
	//  $rec['item_type']
	//  $rec['item_cost']+
	//  $rec['item_dur']+
	//  $rec['item_maxdur']+
	//  $rec['item_ups']+
	//  $rec['item_unic']+
	//  $rec['item_incmagic']
	//  $rec['item_incmagic_count']
	//  $rec['item_arsenal']
	//  $rec['battle']
	//  $rec['bank_id']
	//  $rec['add_info'] -- если мало полей

	//текстовые масивы

	//Админ чисто админ инфо

	$delo_type[10001]="Изменение вещи \"{$rec['item_name']}\".";
	$delo_type[10002]="\"{$rec['owner_login']}\" получил {$rec['add_info']} склонность от Ангела \"{$rec['target_login']}\".";
	$delo_type[10003]="\"{$rec['owner_login']}\" лишился склонности, снял Ангел \"{$rec['target_login']}\".";
	$delo_type[10007]="У Персонажа \"{$rec['owner_login']}\", списано Администрацией с банковского счета №{$rec['bank_id']}, сумма \"{$rec['sum_ekr']}\" екр.";
     //все кредиты (получил, отдал, передал и тд -за услуги, сбросы, образы и тд...) без предметов!!
     //креды/екры/репа
	{
	    //креды
	 	{
	 		//получил
		 	{

				$delo_type[7]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. возврат ставки из заявки на бой.";
				$delo_type[10]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. при взятии апа ";
				$delo_type[286]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. за выйгрыш в лоттерею, угадано {$rec['add_info']} ";
				$delo_type[12]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. от \"{$rec['target_login']}\" (в лабиринте).";
				$delo_type[13]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. от \"{$rec['target_login']}\" (за чек в лабиринте).";
				$delo_type[15]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. за победу в поединке №{$rec['battle']}";
				$delo_type[16]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. за ничью в поединке №{$rec['battle']}";
				$delo_type[18]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. возврат ставки (напали в заявке на деньги).";
				$delo_type[25]="\"{$rec['owner_login']}\" пополнил на {$rec['sum_kr']} кр. свой счет №.{$rec['bank_id']} в банке.";
				$delo_type[26]="\"{$rec['owner_login']}\" снял {$rec['sum_kr']} кр. со своего счета №.{$rec['bank_id']} в банке.";
				$delo_type[27]="\"{$rec['owner_login']}\" обменял {$rec['sum_ekr']} екр. на {$rec['sum_kr']} кр. на своем счету №.{$rec['bank_id']} в банке.";
				$delo_type[37]="Получены кредиты {$rec['sum_kr']} кр. от \"{$rec['target_login']}\" к \"{$rec['owner_login']}\"".($rec['add_info']!=''?' Детали платежа:<b>'.$rec['add_info'].'</b>':'');
				$delo_type[43]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр за услуги модификации предмета \"{$rec['item_name']}\"  от \"{$rec['target_login']}\"";
				$delo_type[50]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. на свой счет №.{$rec['bank_id']} в банке со счета №{$rec['add_info']} персонажа \"{$rec['target_login']}\" комисия {$rec['sum_kom']} кр.";
                		$delo_type[57]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. , от дилера \"{$rec['target_login']}\" на открытие счета.";
                		$delo_type[69]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр., возврат ставки (напали в заявке на деньги).";
			    	$delo_type[101]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. за выйгрыш в турнире Башни смерти";
			    	$delo_type[102]="\"{$rec['owner_login']}\" обналичен чек на {$rec['sum_kr']} кр. у Архивариуса";
			        $delo_type[104]="\"{$rec['owner_login']}\" получен выигрыш на Фонтане Удачи {$rec['sum_kr']} кр.";
			        $delo_type[105]="\"{$rec['owner_login']}\" возвращено {$rec['sum_kr']} кр. за обнуление заявки(ок) (x{$rec['item_count']} на войну).";
			 	$delo_type[108]="\"{$rec['owner_login']}\" возвращено {$rec['sum_kr']} кр. за отзыв заявки на войну с кланом {$rec['target_login']}.";
			 	$delo_type[110]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. за сброс военной помощи (x{$rec['item_count']} заявок) в связи с переполением мест в альянсе.";
				$delo_type[111]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. за отказ в помощи от клана {$rec['target_login']}.";
				$delo_type[112]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. за отзыв заявки на помощь в войне у клана {$rec['target_login']}.";
			 	$delo_type[167]="Почтой получены кредиты {$rec['sum_kr']} кр. от \"{$rec['target_login']}\" к \"{$rec['owner_login']}\"";
			 	$delo_type[181]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. за выполнение квеста \"{$rec['add_info']}\".";
			 	$delo_type[184]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. за победу в викторине от бота \"{$rec['target_login']}\".";
			 	$delo_type[201]="\"{$rec['owner_login']}\" вернул {$rec['sum_kr']} кр. за участие в турнире на руинах";
			 	$delo_type[258]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. за продажу лошади Конюху";
			 	$delo_type[259]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. от квестового бота";
			 	$delo_type[262]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. на грабеже";
			 	$delo_type[408]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. из казны клана {$rec['add_info']} ";
			 	$delo_type[342]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. из казны клана {$rec['add_info']}, за продажу ваучера.";			
				$delo_type[367]="\"{$rec['owner_login']}\" получил {$rec['sum_kr']} кр. возврат при отмене турнира отрядов.";							 	
			 	
		        }
	        //заплатил
		        {
			        $delo_type[287]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за билет в лоттерее Сталкеров.";
			        $delo_type[24]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за открытие счета №.{$rec['bank_id']} в банке.";
				$delo_type[6]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за участие в поединке.";
				$delo_type[366]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за участие в турнире отрядов.";				
				$delo_type[17]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. смену текста автоответчика.";
				$delo_type[36]="Переведены кредиты {$rec['sum_kr']} кр. от \"{$rec['owner_login']}\" к \"{$rec['target_login']}\"".($rec['add_info']!=''?' Детали платежа:<b>'.$rec['add_info'].'</b>':'');
				$delo_type[42]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр за модификацию вещи:\"{$rec['item_name']}\" через мага \"{$rec['target_login']}\" (комиссия:{$rec['sum_kom']} кр.)";
				$delo_type[44]="\"{$rec['owner_login']}\" заплатил {$rec['sum_ekr']} екр. со своего счета №.{$rec['bank_id']} в банке за сброс образа.";
				$delo_type[47]="\"{$rec['owner_login']}\" заплатил {$rec['sum_ekr']} екр. со своего счета №.{$rec['bank_id']} в банке за покупку темной склонности.";
				$delo_type[48]="\"{$rec['owner_login']}\" заплатил {$rec['sum_ekr']} екр. со своего счета №.{$rec['bank_id']} в банке за покупку нейтральной склонности.";
				$delo_type[96]="\"{$rec['owner_login']}\" заплатил {$rec['sum_ekr']} екр. со своего счета №.{$rec['bank_id']} в банке за покупку светлой склонности.";
				$delo_type[97]="\"{$rec['owner_login']}\" заплатил {$rec['sum_ekr']} екр. со своего счета №.{$rec['bank_id']} в банке за смену ника на {$rec['add_info']} .";
		                $delo_type[49]="\"{$rec['owner_login']}\" перевел {$rec['sum_kr']} кр. со своего счета №.{$rec['bank_id']} в банке на счет №{$rec['add_info']} персонажу \"{$rec['target_login']}\" комисия {$rec['sum_kom']} кр.";
		                $delo_type[100]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за участие в турнире Башни смерти";
		                $delo_type[103]="\"{$rec['owner_login']}\" бросил {$rec['sum_kr']} кр в фонтан удачи.";
		                $delo_type[106]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за вызов клана {$rec['target_login']} на войну.";
				$delo_type[107]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за вызов клана {$rec['target_login']} на помощь в войне.";
				$delo_type[109]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за ответ на войну от клана {$rec['target_login']}.";
				$delo_type[124]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за смену цены на предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}].";
				$delo_type[130]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. 5 клановых сообщений.";
				$delo_type[131]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за использование телеграфа.";
				$delo_type[132]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за покупку канала.";
				$delo_type[133]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за изгнание из клана персонажа.";
				$delo_type[134]="\"{$rec['owner_login']}\" перевел {$rec['sum_kr']} кр. в казну клана {$rec['target_login']}.";
				$delo_type[135]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за принятие персонажа {$rec['target_login']} в клан.";
				$delo_type[166]="Почтой переведены кредиты {$rec['sum_kr']} кр. от \"{$rec['owner_login']}\" к \"{$rec['target_login']}\", налог составил {$rec['sum_kom']}. Детали платежа:{$rec['add_info']}";
				$delo_type[170]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за перераспределение статов у Знахаря.";
				$delo_type[171]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за сброс статов у Знахаря.";
				$delo_type[5010]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за сброс умений у Знахаря.";
				
				$delo_type[175]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']}кр. за лечение травмы в БС.";
				$delo_type[190]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за нанесение гравировки на предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт.";
				$delo_type[191]="\"{$rec['owner_login']}\" отремонтировал предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. за ".($rec['sum_kr']>0?$rec['sum_kr']." кр.":$rec['sum_ekr']." екр.");
				$delo_type[192]="\"{$rec['owner_login']}\" сбросил АП предмета \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. за {$rec['sum_kr']} кр.";
				$delo_type[193]="\"{$rec['owner_login']}\" сбросил СТАТЫ предмета \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. за {$rec['sum_kr']} кр.";
				$delo_type[194]="\"{$rec['owner_login']}\" сбросил МФ предмета \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. за {$rec['sum_kr']} кр.";
	            		$delo_type[197]="\"{$rec['owner_login']}\" перезарядил предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. за {$rec['sum_ekr']} екр.";
	                	$delo_type[200]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за участие в турнире на руинах";
		                $delo_type[252]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. квестовому боту";
		                $delo_type[257]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. Конюху за покупку лошади";
			 	$delo_type[263]="\"{$rec['owner_login']}\" был ограблен на {$rec['sum_kr']} кр.";
			 	$delo_type[320]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kom']} кр. за продление хранения предмета :\"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] в комиссионном магазине.";
	                	$delo_type[321]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за принятие вызова наемника!";
	                	$delo_type[322]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за сброс реликтов у знахаря!";	                	
				$delo_type[402]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kr']} кр. за упаковку подарка\" {$rec['item_name']} [{$rec['item_dur']}/{$rec['item_maxdur']}] ";
				$delo_type[406]="\"{$rec['owner_login']}\" создал клан казну для клана  подарка{$rec['add_info']} ";
				$delo_type[407]="\"{$rec['owner_login']}\" пополнил казну клана {$rec['add_info']} на {$rec['sum_kr']} кр.";
	            	}
		}
		//екры
		{
		    //получил
		    	{
			    	$delo_type[409]="\"{$rec['owner_login']}\" получил {$rec['sum_ekr']} екр. на счет {$rec['bank_id']} из казны клана {$rec['bank_id']} ";
			    	$delo_type[11]="\"{$rec['owner_login']}\" получил {$rec['sum_ekr']} екр. на счет {$rec['bank_id']} от Администрации за достижение персонажем \"{$rec['target_login']}\" уровня ";
			    	$delo_type[1111]="\"{$rec['owner_login']}\" получил {$rec['sum_ekr']} екр. на счет {$rec['bank_id']} от Администрации за окончание квеста \"Первые шаги\" ";
			    	$delo_type[29]="\"{$rec['owner_login']}\" обменял {$rec['sum_kr']} кр. на {$rec['sum_ekr']} екр. на своем счету №.{$rec['bank_id']} в банке.";
			    	$delo_type[51]="\"{$rec['owner_login']}\" получил {$rec['sum_ekr']} екр. на свой счет №.{$rec['bank_id']} в банке, от дилера \"{$rec['target_login']}\" ";
			    	$delo_type[52]="\"{$rec['owner_login']}\" купил ваучер на {$rec['sum_ekr']} екр, через дилера \"{$rec['target_login']}\".";
			    	$delo_type[55]="\"{$rec['owner_login']}\" получил бонус за покупку артефактов {$rec['sum_ekr']} екр. на свой счет №{$rec['bank_id']} в банке, от дилера \"{$rec['target_login']}\" ";
			    	$delo_type[355]="\"{$rec['owner_login']}\" получил бонус за покупку ваучара {$rec['sum_ekr']} екр. на свой счет №{$rec['bank_id']} в банке, от дилера \"{$rec['target_login']}\" ";			    	
			    	$delo_type[356]="\"{$rec['owner_login']}\" получил бонус за установку изображения {$rec['sum_ekr']} екр. на свой счет №{$rec['bank_id']} в банке.";			    	
 				$delo_type[68]="\"{$rec['owner_login']}\" получил {$rec['sum_ekr']} екр. на счет {$rec['bank_id']} через Банк.";
		                $delo_type[45]="\"{$rec['owner_login']}\" получил {$rec['sum_ekr']} екр. на свой счет №.{$rec['bank_id']} в банке за сдачу Лотерейных билетов.";
		                $delo_type[46]="\"{$rec['owner_login']}\" получил {$rec['sum_ekr']} екр. на свой счет №.{$rec['bank_id']} в банке за обмен \"{$rec['item_name']}\" (x{$rec['item_count']}).";
			    	$delo_type[261]="\"{$rec['owner_login']}\" пополнил свой счет №.{$rec['bank_id']} в банке, на {$rec['sum_ekr']} екр.";		                
				$delo_type[311]="\"{$rec['owner_login']}\" получил {$rec['sum_ekr']} екр. на счет №.{$rec['bank_id']} в банке, при отказе услуг Коммерческого отдела, номер счета №{$rec['add_info']}";
				$delo_type[312]="\"{$rec['owner_login']}\" поднял монету в замке и пополнил казну клана на {$rec['sum_ekr']} екр.";

		    	}
			//заплатил
			{
				$delo_type[8]="\"{$rec['owner_login']}\" заплатил {$rec['sum_ekr']} екр. со своего счета {$rec['bank_id']} за аренду предмета \"{$rec['item_name']}\" до {$rec['add_info']}.";
				$delo_type[9]="\"{$rec['owner_login']}\" заплатил {$rec['sum_ekr']} екр. со своего счета {$rec['bank_id']} за продление аренды предмета \"{$rec['item_name']}\" до {$rec['add_info']}.";
				$delo_type[30]="\"{$rec['owner_login']}\" заплатил {$rec['sum_ekr']} екр. со своего счета №.{$rec['bank_id']} в банке за перераспределение умений.";
				$delo_type[31]="\"{$rec['owner_login']}\" заплатил {$rec['sum_ekr']} екр. со своего счета №.{$rec['bank_id']} в банке за перераспределение статов.";
                		$delo_type[53]="\"{$rec['owner_login']}\" оплатил личный образ, через дилера \"{$rec['target_login']}\".";
				$delo_type[58]="\"{$rec['owner_login']}\" купил {$rec['add_info']} склонность за {$rec['sum_ekr']} екр, через дилера \"{$rec['target_login']}\".";

 				$delo_type[59]="\"{$rec['owner_login']}\" приобрел/продлил Silver account за {$rec['sum_ekr']} екр, 5 екр на счет №{$rec['bank_id']}, сроком до {$rec['add_info']}, через  \"{$rec['target_login']}\".";
 				$delo_type[359]="\"{$rec['owner_login']}\" приобрел/продлил Gold account за {$rec['sum_ekr']} екр, 5 екр на счет №{$rec['bank_id']}, сроком до {$rec['add_info']}, через  \"{$rec['target_login']}\".";
 				$delo_type[358]="\"{$rec['owner_login']}\" приобрел/продлил Platinum account за {$rec['sum_ekr']} екр, 5 екр на счет №{$rec['bank_id']}, сроком до {$rec['add_info']}, через  \"{$rec['target_login']}\".";

				$delo_type[310]="\"{$rec['owner_login']}\" заплатил {$rec['sum_ekr']} екр. со своего счета №.{$rec['bank_id']} в банке за услуги Коммерческого отдела, номер счета №{$rec['add_info']}"; 				
				//абилки истенного хаоса
				$delo_type[70]="\"{$rec['owner_login']}\" потратил {$rec['sum_ekr']} екр. со своего счета №{$rec['bank_id']} в банке, за использование абилки: \"Вампиризм постоянный\". (Остаток на счету:{$rec['add_info']} екр.) ";
				$delo_type[71]="\"{$rec['owner_login']}\" потратил {$rec['sum_ekr']} екр. со своего счета №{$rec['bank_id']} в банке, за использование абилки: \"Нападание\". (Остаток на счету:{$rec['add_info']} екр.) ";
				$delo_type[72]="\"{$rec['owner_login']}\" потратил {$rec['sum_ekr']} екр. со своего счета №{$rec['bank_id']} в банке, за использование абилки: \"Кровавое Нападание\". (Остаток на счету:{$rec['add_info']} екр.) ";
				$delo_type[73]="\"{$rec['owner_login']}\" потратил {$rec['sum_ekr']} екр. со своего счета №{$rec['bank_id']} в банке, за использование абилки: \"Лечение травм\". (Остаток на счету:{$rec['add_info']} екр.) ";
				$delo_type[74]="\"{$rec['owner_login']}\" потратил {$rec['sum_ekr']} екр. со своего счета №{$rec['bank_id']} в банке, за использование абилки: \"Молчанка 30 мин.\". (Остаток на счету:{$rec['add_info']} екр.) ";
				$delo_type[75]="\"{$rec['owner_login']}\" потратил {$rec['sum_ekr']} екр. со своего счета №{$rec['bank_id']} в банке, за использование абилки: \"Переманить клона\". (Остаток на счету:{$rec['add_info']} екр.) ";
				$delo_type[76]="\"{$rec['owner_login']}\" потратил {$rec['sum_ekr']} екр. со своего счета №{$rec['bank_id']} в банке, за использование абилки: \"Выход из боя\". (Остаток на счету:{$rec['add_info']} екр.) ";
				$delo_type[77]="\"{$rec['owner_login']}\" потратил {$rec['sum_ekr']} екр. со своего счета №{$rec['bank_id']} в банке, за использование абилки: \"Восстановление энергии +180\". (Остаток на счету:{$rec['add_info']} екр.) ";
	                	$delo_type[196]="\"{$rec['owner_login']}\" отрихтовал предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. за {$rec['sum_ekr']} eкр.";
	                	$delo_type[198]="\"{$rec['owner_login']}\" перезарядил предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. за {$rec['sum_kr']} кр.";

			}
		}
		//репа
	   	{
	    	//получил
		    	{
	                	$delo_type[28]="\"{$rec['owner_login']}\" обменял {$rec['sum_ekr']} екр. на {$rec['sum_rep']} репутации на своем счету №.{$rec['bank_id']} в банке.";
	                	$delo_type[2828]="\"{$rec['owner_login']}\" купил за {$rec['sum_ekr']} екр. {$rec['sum_rep']} репутации через дилера.";	                	
				$delo_type[182]="\"{$rec['owner_login']}\" получил {$rec['sum_rep']} реп. за выполнение квеста \"{$rec['add_info']}\".";
				$delo_type[183]="\"{$rec['owner_login']}\" получил {$rec['sum_rep']} реп. победу в турнире.";
				$delo_type[1184]="\"{$rec['owner_login']}\" получил предмет {$rec['item_name']} за победу в турнире.";				
	                	$delo_type[203]="\"{$rec['owner_login']}\" получил {$rec['sum_rep']} репутации за победу в турнире на руинах";
	                	$delo_type[250]="\"{$rec['owner_login']}\" получил {$rec['sum_rep']} репутации за количество побед на руинах";
				$delo_type[204]="\"{$rec['owner_login']}\" получил {$rec['sum_rep']} репутации за проигрыш в турнире на руинах";
	                	$delo_type[251]="\"{$rec['owner_login']}\" получил {$rec['sum_rep']} репутации за проход лабиринта";
	                	$delo_type[254]="\"{$rec['owner_login']}\" получил {$rec['sum_rep']} репутации за выполнение квеста";
	                	$delo_type[260]="\"{$rec['owner_login']}\" получил {$rec['sum_rep']} репутации за сдачу ресурсов в храм";	                	
				$delo_type[360]="\"{$rec['owner_login']}\" получил {$rec['sum_rep']} репутации за отказ в создании артефакта";	                		                	
				$delo_type[361]="\"{$rec['owner_login']}\" получил {$rec['sum_rep']} репутации за сдачу артефакта в ком отдел";
				$delo_type[381]="\"{$rec['owner_login']}\" - {$rec['add_info']}.";
				$delo_type[382]="\"{$rec['owner_login']}\" получил {$rec['sum_rep']} репутации ".(($rec['sum_kr']>0)?" и {$rec['sum_kr']} кр. ":"")." - {$rec['add_info']}.";				
				$delo_type[383]="\"{$rec['owner_login']}\" - {$rec['add_info']}.";	
	
					//тут еще и шмотки....  победы разные
				{
					$delo_type[3]="\"{$rec['owner_login']}\" получил: \"{$rec['item_name']}\" и \"{$rec['sum_rep']}\" репутации за победу на ристалище в \"Одиночных сражениях\".";
					$delo_type[4]="\"{$rec['owner_login']}\" получил: \"{$rec['item_name']}\" и \"{$rec['sum_rep']}\" репутации за победу на ристалище в \"Сражениях отрядов\".";
					$delo_type[5]="\"{$rec['owner_login']}\" получил: \"{$rec['item_name']}\" и \"{$rec['sum_rep']}\" репутации за победу на ристалище в \"Групповых сражениях\".";
				}
		    	}
	    		//заплатил
		    	{
		    		
		    		$delo_type[5001]="Изъято {$rec['sum_kr']} кр. с личного счета, в счет долга.";
		    		$delo_type[5002]="Изъято {$rec['sum_kr']} кр. с банковского счета № {$rec['bank_id']}, в счет долга.";
		    		$delo_type[5003]="Изъято {$rec['sum_ekr']} екр. с банковского счета № {$rec['bank_id']}, в счет долга.";
		    		$delo_type[5004]="Получено {$rec['sum_kr']} кр. Сдача за возврат долга предметом.";
				$delo_type[5005]="Изъят сертификат {$rec['item_name']}({$rec['item_id']}), в счет долга.";
				$delo_type[5006]="Получено {$rec['sum_ekr']} екр. Сдача (екр на счет {$rec['bank_id']}) за возврат долга сертификатом.";
				$delo_type[5007]="Получено {$rec['sum_kr']} кр. Сдача (кр) за возврат долга сертификатом.";
				$delo_type[5008]="Получен предмет {$rec['item_name']}, на сумму {$rec['sum_ekr']} екр. Сдача за возврат долга сертификатом.";
		    		$delo_type[5020]="Заплатил {$rec['sum_ekr']} екр с помощью {$rec['add_info']}, в счет долга.";
				
		    	}
	    }


	    //аренда
		{
			if ($rec['type'] == 215) { $t = explode("/",$rec['add_info']); $delo_type[215]="\"{$rec['owner_login']}\" получил за аренду \"{$rec['item_name']}\" {$rec['sum_kr']} кр. Аренда сроком на ".$t[0]." дней"; }
			if ($rec['type'] == 216) { $t = explode("/",$rec['add_info']); $delo_type[216]="\"{$rec['owner_login']}\" заплатил за продление аренды: \"{$rec['item_name']}\" сроком на ".$t[0]." дней за ".$rec['sum_kr']." кр."; }
			if ($rec['type'] == 217) { $t = explode("/",$rec['add_info']); $delo_type[217]="\"{$rec['owner_login']}\" получил за продление аренды: \"{$rec['item_name']}\" {$rec['sum_kr']} кр. Сроком на ".$t[0]." дней"; }
		}
		//репутация
		{

		}
	}


//Предметы
	{
		//купил
		{
			$delo_type[1]="\"{$rec['owner_login']}\" купил товар: \"{$rec['item_name']}\" (x{$rec['item_count']}) [{$rec['item_dur']}/{$rec['item_maxdur']}] за {$rec['sum_kr']} кр.".if_have(" (в арсенал клана:",$rec['item_arsenal'],")");
			$delo_type[401]="\"{$rec['owner_login']}\" купил товар: \"{$rec['item_name']}\" (x{$rec['item_count']}) [{$rec['item_dur']}/{$rec['item_maxdur']}] за {$rec['sum_kr']} кр.".if_have(" (в арсенал клана:",$rec['item_arsenal'],")");
			$delo_type[2]="\"{$rec['owner_login']}\" купил товар: \"{$rec['item_name']}\" (x{$rec['item_count']}) [{$rec['item_dur']}/{$rec['item_maxdur']}] за {$rec['sum_rep']} кр.".if_have(" (в арсенал клана:",$rec['item_arsenal'],")");
			$delo_type[40]="\"{$rec['owner_login']}\" купил  предмет:\"{$rec['item_name']}\" за {$rec['sum_kr']} кр у \"{$rec['target_login']}\"";
			$delo_type[54]="\"{$rec['owner_login']}\" купил \"{$rec['item_name']}\" за {$rec['sum_ekr']} екр, через дилера \"{$rec['target_login']}\".";
			$delo_type[354]="\"{$rec['owner_login']}\" купил \"{$rec['item_name']}\" за ".if_have(" ",$rec['sum_ekr']," екр.")." ".if_have(" ",$rec['sum_rep']," реп.").".";			
			$delo_type[82]="\"{$rec['owner_login']}\" купил \"{$rec['item_name']}\" за {$rec['sum_ekr']} екр, через дилера \"{$rec['target_login']}\" и получил бонус \"{$rec['add_info']}\" екр. на счет №{$rec['bank_id']}.";
		    	$delo_type[122]="\"{$rec['owner_login']}\" купил предмет:\"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. из комиссионки за {$rec['sum_kr']} кр.";
			$delo_type[172]="\"{$rec['owner_login']}\" купил товар: \"{$rec['item_name']}\" (x{$rec['item_count']}) [{$rec['item_dur']}/{$rec['item_maxdur']}] за {$rec['sum_rep']} реп.";
			$delo_type[272]="\"{$rec['owner_login']}\" купил товар: \"{$rec['item_name']}\" (x{$rec['item_count']}) [{$rec['item_dur']}/{$rec['item_maxdur']}] за {$rec['sum_kr']} кр.".if_have(" (в арсенал клана:",$rec['item_arsenal'],")");
			$delo_type[372]="\"{$rec['owner_login']}\" оплатил личный артефакт: \"{$rec['item_name']}\" (x{$rec['item_count']}) [{$rec['item_dur']}/{$rec['item_maxdur']}] за {$rec['sum_rep']} реп. {$rec['add_info']} ";			
			$delo_type[373]="\"{$rec['owner_login']}\" купил уник товар в КО: \"{$rec['item_name']}\" (x{$rec['item_count']}) [{$rec['item_dur']}/{$rec['item_maxdur']}] за {$rec['sum_ekr']} екр.";			
			$delo_type[374]="\"{$rec['owner_login']}\"  заплатил за переход руны : \"{$rec['item_name']}\" (x{$rec['item_count']}) [{$rec['item_dur']}/{$rec['item_maxdur']}] за {$rec['sum_rep']} реп. {$rec['add_info']} ";	
			$delo_type[375]="\"{$rec['owner_login']}\"  заплатил за покупку уровня руны : \"{$rec['item_name']}\" (x{$rec['item_count']}) [{$rec['item_dur']}/{$rec['item_maxdur']}] за {$rec['sum_rep']} реп. {$rec['add_info']} ";				
			$delo_type[376]="\"{$rec['owner_login']}\" оплатил личный артефакт векселем: \"{$rec['item_name']}\" (x{$rec['item_count']}) [{$rec['item_dur']}/{$rec['item_maxdur']}] на сумму {$rec['sum_rep']} реп. {$rec['add_info']} ";						
			
		}
		//получил/подарили
		{
			$delo_type[403]="\"{$rec['owner_login']}\" распаковал свой предмет:\"{$rec['item_name']}\".";
			$delo_type[404]="\"{$rec['owner_login']}\" получил в подарок предмет:\"{$rec['item_name']}\" от \"{$rec['target_login']}\" из упаковки.";
			$delo_type[410]="\"{$rec['owner_login']}\" получил в подарок упакованный предмет от \"{$rec['target_login']}\".";
			$delo_type[14]="\"{$rec['owner_login']}\" получил в подарок предмет:\"{$rec['item_name']}\" от \"{$rec['target_login']}\" (в лабиринте обмен свитков подгона).";
			$delo_type[21]="\"{$rec['owner_login']}\" забрал из арсенала при выходе из клана \"{$rec['item_arsenal']}\"  предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}]";
			$delo_type[22]="\"{$rec['owner_login']}\" забрал через арсенал при выходе из клана \"{$rec['item_arsenal']}\"  предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}]";
			$delo_type[23]="У \"{$rec['owner_login']}\" был изъят предмет:\"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}], пренадлежавший \"{$rec['target_login']}\" ";
			$delo_type[56]="\"{$rec['owner_login']}\" получил в бонус \"{$rec['item_name']}\" за {$rec['sum_ekr']} екр, от Коммерческого отдела.";
			$delo_type[60]="\"{$rec['owner_login']}\" получил в бою \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт.";
			$delo_type[61]="\"{$rec['owner_login']}\" забрал из арсенал \"{$rec['item_arsenal']}\" свой предмет: \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт.";
			$delo_type[62]="\"{$rec['owner_login']}\" взял из арсенала \"{$rec['item_arsenal']}\" предмет: \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. сроком до {$rec['add_info']};";
			$delo_type[63]="\"{$rec['owner_login']}\" изъял через арсенал \"{$rec['item_arsenal']}\" предмет: \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. с персонажа {$rec['add_info']};";
			$delo_type[64]="\" У {$rec['owner_login']}\" был изъят через арсенал \"{$rec['item_arsenal']}\" предмет: \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. персонажем {$rec['add_info']};";
			$delo_type[88]="\"{$rec['owner_login']}\" получил из арсенала предмет: \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт.";
			$delo_type[288]="\"{$rec['owner_login']}\" получил в бонус предмет: \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. при достижении 4го уровня.";
			$delo_type[99]="\"{$rec['owner_login']}\" получил предмет:\"{$rec['item_name']}\" (x{$rec['item_count']}) шт. ".($rec['item_count']==1?"(".$rec['item_id'].")":"")." от персонажа \"{$rec['target_login']}\"";
			$delo_type[169]="\"{$rec['owner_login']}\" почтой получил предмет:\"{$rec['item_name']}\" (x{$rec['item_count']}) шт. от \"{$rec['target_login']}\"";

			$delo_type[419]="\"{$rec['owner_login']}\" получил из ларца {$rec['add_info']}, предмет: \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. .";
			$delo_type[420]="\"{$rec['owner_login']}\" получил из ларца {$rec['add_info']}, личную абилити: \"{$rec['item_name']}\"  {$rec['item_count']}шт. .";
			
			$delo_type[98]="\"{$rec['owner_login']}\" получил в подарок предмет:\"{$rec['item_name']}\" (x{$rec['item_count']}) шт. от персонажа \"{$rec['target_login']}\"";
		    	$delo_type[140]="\"{$rec['owner_login']}\" купил товар: \"{$rec['item_name']}\" (x{$rec['item_count']}) [{$rec['item_dur']}/{$rec['item_maxdur']}] за {$rec['sum_ekr']} eкр.";
			
			
		    	
		    	
		    	$delo_type[180]="\"{$rec['owner_login']}\" получил предмет:\"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. за выполнение квеста \"{$rec['add_info']}\".";
		 	//   $delo_type[183]="\"{$rec['owner_login']}\" получил предмет:\"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. за выполнение квеста \"{$rec['add_info']}\".";
			$delo_type[185]="\"{$rec['owner_login']}\" выйграл предмет:\"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. в Лоттерею.";
			$delo_type[205]="\"{$rec['owner_login']}\" выбил {$rec['item_name']} из монстра во время турнира на руинах";
			$delo_type[206]="\"{$rec['owner_login']}\" собрал букет {$rec['item_name']} в цветочном магазине.";
			$delo_type[202]="\"{$rec['owner_login']}\" получил {$rec['item_name']} за победу в турнире на руинах";
			$delo_type[209]="\"{$rec['owner_login']}\" получил подарок из цветочного магазина: \"{$rec['item_name']}\" от персонажа \"{$rec['target_login']}\"";
			$delo_type[253]="\"{$rec['owner_login']}\" получил квестовый предмет: \"{$rec['item_name']}\" от \"{$rec['target_login']}\"";
			$delo_type[255]="\"{$rec['owner_login']}\" получил предмет в награду за квест: \"{$rec['item_name']}\" от \"{$rec['target_login']}\"";
			$delo_type[264]="\"{$rec['owner_login']}\" получил предмет \"{$rec['item_name']}\" при грабеже.";

			$delo_type[266]="\"{$rec['owner_login']}\" украл лошадь за городом.";
			$delo_type[267]="У \"{$rec['owner_login']}\" украли лошадь за городом.";
		    	$delo_type[280]="\"{$rec['owner_login']}\" получил ваучер (сдачу) на сумму {$rec['sum_ekr']} екр. от Коммерческого отдела";
    		    	$delo_type[283]="\"{$rec['owner_login']}\" получил ваучер (возврат) на сумму {$rec['sum_ekr']} екр. от Коммерческого отдела";
		    	$delo_type[281]="\"{$rec['owner_login']}\" заплатил ваучеры за покупку личных реликтов";
		    	$delo_type[282]="\"{$rec['owner_login']}\" заплатил ваучеры за оплату услуг Коммерческого отдела";		    	
		    	$delo_type[284]="\"{$rec['owner_login']}\" заплатил ваучеры за покупку клановых реликтов";		    	
		}
		//продал
		{
			$delo_type[34]="\"{$rec['owner_login']}\" продал в магазин товар: \"{$rec['item_name']}\" (x{$rec['item_count']}) [{$rec['item_dur']}/{$rec['item_maxdur']}] за {$rec['sum_kr']} кр.";
			$delo_type[41]="\"{$rec['owner_login']}\" продал предмет:\"{$rec['item_name']}\" за {$rec['sum_kr']} кр, персонажу \"{$rec['target_login']}\" (комиссия:{$rec['sum_kom']} кр.)";
			$delo_type[123]="\"{$rec['owner_login']}\" продал через комиссионку предмет:\"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. за {$rec['sum_kr']} кр. Комиссия составила {$rec['sum_kom']}кр.";
		}
		//вернул, подарил, передал, сдал (избавился)
		{
			$delo_type[66]="\" У {$rec['owner_login']}\" был изъят предмет клана \"{$rec['item_arsenal']}\": \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. персонажем {$rec['add_info']};";
			
			$delo_type[39]="\"{$rec['owner_login']}\" передал предмет:\"{$rec['item_name']}\" (x{$rec['item_count']}) шт. ".($rec['item_count']==1?"(".$rec['item_id'].")":"")." персонажу \"{$rec['target_login']}\" ";
			$delo_type[168]="\"{$rec['owner_login']}\" почтой передал предмет:\"{$rec['item_name']}\" (x{$rec['item_count']}) шт. ".($rec['item_count']==1?"(".$rec['item_id'].")":"")." персонажу \"{$rec['target_login']}\"";
			
			
			$delo_type[38]="\"{$rec['owner_login']}\" подарил предмет:\"{$rec['item_name']}\" (x{$rec['item_count']}) шт.  персонажу \"{$rec['target_login']}\" ";
			$delo_type[65]="\"{$rec['owner_login']}\" сдал в арсенал \"{$rec['item_arsenal']}\" свой предмет: \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт.";
			$delo_type[67]="\"{$rec['owner_login']}\" вернул в арсенал \"{$rec['item_arsenal']}\" предмет: \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт.";
			$delo_type[86]="\"{$rec['owner_login']}\" подарил в арсенал предмет: \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт.";
			$delo_type[89]="\"{$rec['owner_login']}\" вернул арсенальный предмет: \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. хозяину.";
		    	$delo_type[120]="\"{$rec['owner_login']}\" сдал предмет:\"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. в комиссионный магазин за {$rec['sum_kr']} кр.";
			$delo_type[20]="\"{$rec['owner_login']}\" вернул в арсенал при выходе из клана \"{$rec['item_arsenal']}\"  предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}]";
			$delo_type[186]="Заокнчился срок аренды предмета:\"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}].";
			$delo_type[187]="Предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. возвращен в \"{$rec['target_login']}\".";
			$delo_type[188]="\"{$rec['owner_login']}\" вернул хозяину через арсенал при выходе из клана \"{$rec['item_arsenal']}\" предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}]";
			$delo_type[189]="Возвращен предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] через арсенал при выходе из клана \"{$rec['item_arsenal']}\".";
			$delo_type[207]="\"{$rec['owner_login']}\" подарил предмет из цветочного магазина: \"{$rec['item_name']}\" персонажу \"{$rec['add_info']}\" с отсрочкой доставки, уплачено {$rec['sum_kr']} кр.";
			$delo_type[208]="\"{$rec['owner_login']}\" подарил предмет из цветочного магазина: \"{$rec['item_name']}\" персонажу \"{$rec['target_login']}\"";
			$delo_type[405]="\"{$rec['owner_login']}\" подарил упакованный предмет из цветочного магазина: \"{$rec['item_name']}\" персонажу \"{$rec['target_login']}\"";
			$delo_type[302]="\"{$rec['owner_login']}\" сдал в казну клана \"{$rec['add_info']}\"   \"{$rec['item_name']}\" ";						
			$delo_type[256]="\"{$rec['owner_login']}\" передал квестовый предмет: \"{$rec['item_name']}\" к \"{$rec['target_login']}\"";
			$delo_type[265]="\"{$rec['owner_login']}\" лишился предмета \"{$rec['item_name']}\" на грабеже";
			$delo_type[301]="У \"{$rec['owner_login']}\" предмет \"{$rec['item_name']}\" был изъят к персонажу Повелитель багов";	
			
			$delo_type[340]="\"{$rec['owner_login']}\" сдал на продажу  в казну клана \"{$rec['add_info']}\"  \"{$rec['item_name']}\" ";						
					
			$delo_type[411]="У \"{$rec['owner_login']}\" сдал предмет \"{$rec['item_name']}\" (".$rec['item_id'].") в ремонтную мастерскую";
			$delo_type[412]="У \"{$rec['owner_login']}\" получил предмет \"{$rec['item_name']}\" (".$rec['item_id'].") из ремонтной мастеркой";
        		$delo_type[413]="У \"{$rec['owner_login']}\" Получены сертификаты: \"Подарочный сертификат - 5 екр\" и \"Подарочный сертификат - 10 екр\" из ремонтной мастеркой при обмене кольца Вдохновения";
        	}
		//забрал, вернул (себе или хозяину)
		{
			
			$delo_type[341]="\"{$rec['owner_login']}\" получил обратно ваучер \"{$rec['item_name']}\", при отказе выкупа в казну клана  \"{$rec['add_info']}\"  ";						
			$delo_type[121]="\"{$rec['owner_login']}\" забрал предмет:\"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. из комиссионного магазина, комиссия за хранения {$rec['sum_kom']} кр.";
			$delo_type[173]="\"{$rec['owner_login']}\" забрал угощение {$rec['add_info']}: \"{$rec['item_name']}\" (x{$rec['item_count']}) [{$rec['item_dur']}/{$rec['item_maxdur']}].";
			$delo_type[174]="\"{$rec['owner_login']}\" забрал подарок {$rec['add_info']}: \"{$rec['item_name']}\" (x{$rec['item_count']}) [{$rec['item_dur']}/{$rec['item_maxdur']}].";
			$delo_type[300]="\"{$rec['owner_login']}\" вскрыл подарок и получил {$rec['add_info']}: \"{$rec['item_name']}\" (x{$rec['item_count']}) [{$rec['item_dur']}/{$rec['item_maxdur']}].";
		}

		{//разные действия с предметами
			$delo_type[195]="\"{$rec['owner_login']}\" обменял предмет предмета \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт.";
			$delo_type[19]="\"{$rec['owner_login']}\" выбросил предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}]";
			$delo_type[35]=" У \"{$rec['owner_login']}\" предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] пришел в полную негодность и разрушился.";
			
			$delo_type[32]="\"{$rec['owner_login']}\" удачно использовал предмет:\"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}]";
			$delo_type[33]="\"{$rec['owner_login']}\" неудачно использовал предмет:\"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}]";
			$delo_type[78]="\"{$rec['owner_login']}\" подогнал бесплатно свитком предмет:\"{$rec['item_name']}\".";
			$delo_type[80]="\"{$rec['owner_login']}\" перенес свиток \"{$rec['item_incmagic']}\" из \"{$rec['add_info']}\" в \"{$rec['item_name']}\".".if_have(" (предмет клана:",$rec['item_arsenal'],")");
			$delo_type[81]="\"{$rec['owner_login']}\" перевстроил свиток \"{$rec['add_info']}\" в \"{$rec['item_name']}\".".if_have(" (предмет клана:",$rec['item_arsenal'],")");
			$delo_type[176]="\"{$rec['owner_login']}\" бесплатно подогнал предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. ".($rec['add_info']!=''?$rec['add_info']:'').".";
			$delo_type[177]="\"{$rec['owner_login']}\" подогнал предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. за {$rec['sum_kr']} кр.";
			$delo_type[178]="\"{$rec['owner_login']}\" бесплатно модифицировал предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт.";
			$delo_type[179]="\"{$rec['owner_login']}\" модифицировал предмет \"{$rec['item_name']}\" [{$rec['item_dur']}/{$rec['item_maxdur']}] {$rec['item_count']}шт. за {$rec['sum_kr']} кр.";
			$delo_type[313]="\"{$rec['owner_login']}\" поднял страницу магической книги в замке";
        	}
	    //аренда
		{ 	$delo_type[210]="\"{$rec['owner_login']}\" забрал из ломбарда: \"{$rec['item_name']}\" за \"{$rec['sum_kr']}\" кр. ".$rec['add_info'];
			$delo_type[211]="\"{$rec['owner_login']}\" сдал в ломбард: \"{$rec['item_name']}\" за \"{$rec['sum_kr']}\" кр. на ".$rec['add_info']." дней";
			if ($rec['type'] == 212) {  $t = explode("/",$rec['add_info']); $delo_type[212]="\"{$rec['owner_login']}\" сдал в арендную лавку: \"{$rec['item_name']}\" сроком на ".$t[0]." дней за ".$t[1]." кр. в сутки (снято 1 кр. налога)"; }
			$delo_type[213]="\"{$rec['owner_login']}\" забрал из арендной лавки: \"{$rec['item_name']}\""; 	if ($rec['type'] == 214) {  $t = explode("/",$rec['add_info']); $delo_type[214]="\"{$rec['owner_login']}\" заплатил за аренду: \"{$rec['item_name']}\" сроком на ".$t[0]." дней за ".$rec['sum_kr']." кр."; }
	        	$delo_type[218]="\"{$rec['owner_login']}\" вернул в арендную лавку \"{$rec['item_name']}\" по окончанию срока аренды";
			$delo_type[219]="\"{$rec['owner_login']}\" возвращена вещь из арендной лавки: \"{$rec['item_name']}\" окончен макс. срок.";

		}
	}

 //Аукцион
	{
		$delo_type[220]="\"{$rec['owner_login']}\" забрал вещь \"{$rec['item_name']}\" из аукциона в арсенал клана {$rec['item_arsenal']}";
		$delo_type[221]="\"{$rec['owner_login']}\" забрал вещь \"{$rec['item_name']}\" из аукциона";
		if ($rec['type'] == 222) { $t = explode("/",$rec['add_info']); $delo_type[222]="\"{$rec['owner_login']}\" выставил вещь из арсенала на аукцион: \"{$rec['item_name']}\". Сроком на ".$t[0]." дней и начальной стоимостью ".$t[1]." кр. из клана {$rec['item_arsenal']} (снято 1 кр налога из казны)"; }
		if ($rec['type'] == 223) { $t = explode("/",$rec['add_info']); $delo_type[223]="\"{$rec['owner_login']}\" выставил вещь на аукцион: \"{$rec['item_name']}\". Сроком на ".$t[0]." дней и начальной стоимостью ".$t[1]." кр. (снято 1 кр налога)"; }
		$delo_type[224]="\"{$rec['owner_login']}\" продал на аукционе вещь \"{$rec['item_name']}\", выручил {$rec['sum_kr']} кр., комиссия: {$rec['sum_kom']}. Деньги пошли в казну клана {$rec['item_arsenal']}";
		$delo_type[225]="\"{$rec['owner_login']}\" продал на аукционе вещь \"{$rec['item_name']}\", выручил {$rec['sum_kr']} кр., комиссия: {$rec['sum_kom']}.";
		$delo_type[226]="\"{$rec['owner_login']}\" выиграл на аукционе в арсенал клана \"{$rec['item_arsenal']}\" вещь \"{$rec['item_name']}\" за {$rec['sum_kr']} кр.";
		$delo_type[227]="\"{$rec['owner_login']}\" выиграл вещь на аукционе \"{$rec['item_name']}\" за {$rec['sum_kr']} кр.";
		$delo_type[228]="\"{$rec['owner_login']}\" вернулась вещь из аукциона \"{$rec['item_name']}\" в арсенал клана {$rec['item_arsenal']}";
		$delo_type[229]="\"{$rec['owner_login']}\" вернулась вещь из аукциона \"{$rec['item_name']}\"";
		$delo_type[230]="\"{$rec['owner_login']}\" получил возврат ставки с аукциона за \"{$rec['item_name']}\"";
		$delo_type[231]="\"{$rec['owner_login']}\" сделал ставку на аукционе за \"{$rec['item_name']}\"";
		$delo_type[232]="\"{$rec['owner_login']}\" поднял свою ставку на аукционе за \"{$rec['item_name']}\"";
		$delo_type[233]="\"{$rec['owner_login']}\" заплатил за регистрацию клана";
		$delo_type[234]="\"{$rec['owner_login']}\" заплатил за обновление регистрации о клане";
		$delo_type[235]="\"{$rec['owner_login']}\" заплатил за заявку на рекрутство";
		$delo_type[236]="\"{$rec['owner_login']}\" заплатил за выписку по счёту {$rec['sum_kr']} кр";
	}

	$delo_type[237]="\"{$rec['owner_login']}\" заплатил {$rec['sum_kom']} за {$rec['add_info']}";
	$delo_type[0]="\"{$rec['owner_login']}\", операция не определена!";
	$delo_type[79]="\"{$rec['owner_login']}\" получил {$rec['add_info']} склонность от укуса «Абсолютного хаоса» \"{$rec['target_login']}\".";

	$delo_type[270]="\"{$rec['owner_login']}\" получил загородный квест № \"{$rec['add_info']}\".";
	$delo_type[271]="\"{$rec['owner_login']}\" завершил загородный квест № \"{$rec['add_info']}\".";


	$out=$delo_type[$rec['type']];
// вывод
	if ($al[perevodi]>=5 && $add_info_off==2)
	{
		if($al[item_hist]==1) //ссылка на историю предмета, палы от 1.9 и выше + админы ($access[perevodi_deep]>5)
		{
			if($rec['item_id'] !='')
			{
				$it=explode(',',$rec['item_id']);
				for($j=0;$j<count($it);$j++)
				{
					$it[$j]="<a href='perevod.php?sh=3&item_hist=".$it[$j]."' target=_blank>".$it[$j]."</a> ";
				}
				$rec['item_id']=implode(',',$it);
			}
		}

	  //админское время
		$rec_date=date("d-m-Y H:i:s",$rec['sdate']);
		$out= $rec['type']. ' '. $rec_date.": ".$out;
		
		$rec=login_fix_for_delo($rec);
		  //админам доп инфа
		$out.=" (".
		if_have("операция с:",$rec['target_login']).
		if_have(", Сумма:",$rec['sum_kr']," кр.").
		if_have(", Сумма:",$rec['sum_ekr']," екр.").
		if_have(", коммисия:",$rec['sum_kom']).
		if_have(", номер счета:",$rec['bank_id']).
		if_have(", использован предмет id:",$rec['aitem_id']).
		if_have(", стоимость предмета:",$rec['item_cost']).
		if_have(", id:",$rec['item_id']).
		if_have(", ups:",$rec['item_ups']).
		if_have(", Уник:",$rec['item_unic']).
		if_have(", Встройка:",$rec['item_incmagic']).
		if_have(", Юзов:",$rec['item_incmagic_count']).
		if_have(", Арсенал клана:",$rec['item_arsenal']).
		if_have(", лог боя:",$rec['battle']).
		if_have(", Баланс до:",$rec['owner_balans_do']," /").
		if_have(" Баланс после:",$rec['owner_balans_posle']).
		if_have(", реп. до:",$rec['owner_rep_do']," /").
		if_have(" реп. после:",$rec['owner_rep_posle']).
		if_have(" доп инфо:",$rec['add_info'])."   )";
		$out=str_replace("(,","(",$out);
		$out=str_replace(",,",",",$out);
	  }
	  else
	  {
	  	//обычное
		  $rec_date=date("d-m-Y H:i:s",$rec['sdate']);
		  $out=$rec_date.": ".$out;
	  }
	return $out;
}



function if_have($str,$val,$str2="")
{
  if (($val!='') AND ($val!='0'))
   {

     if ($str!='')
        {
           return $str.$val.$str2;
        }
        else
        {
        	return $val.$str2;
        }
   }
   else if ($val==0)
   {
   	return "";
   }
   else return "".$str2;
}
?>