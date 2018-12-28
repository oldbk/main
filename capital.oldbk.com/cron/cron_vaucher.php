#!/usr/bin/php
<?php
ini_set('display_errors','On');
$CITY_NAME='capitalcity';
include "/www/".$CITY_NAME.".oldbk.com/cron/init.php";
if( !lockCreate("cron_vaucher_job") ) {
    exit("Script already running.");
}
echo date("d.m.y H:i:s").'\r\n';

$get_all_vau=mysql_query("select i.*, pre.owner as powner, pre.klan_name as klan_name   from oldbk.inventory i LEFT JOIN oldbk.clans_preset pre ON i.id=pre.itemid where pdate<=".(time()-(24*60*60))."  and i.owner=460  ;");
							
							if(mysql_num_rows($get_all_vau)>0)
							{
							while($get_item=mysql_fetch_array($get_all_vau))
								{

					 			mysql_query("UPDATE `oldbk`.`inventory` SET `owner`='{$get_item[powner]}'  WHERE `id`='{$get_item[id]}' LIMIT 1;");
					 					
					 				if(mysql_affected_rows()>0)										 					
												{
									 			mysql_query("DELETE FROM oldbk.`clans_preset` WHERE `itemid`='{$get_item[id]}' LIMIT 1; ");
												
												 //пишем в дело и отправляем телегу
						 						$gtelo=check_users_city_data($get_item[powner]);

												 			
								 					 	        $rec['owner']=$gtelo[id];
															$rec['owner_login']=$gtelo[login];
															$rec['owner_balans_do']=$gtelo['money'];
															$rec['owner_balans_posle']=$gtelo['money'];
															$rec['target']=0;
															$rec['target_login']='Казна '.$get_item['klan_name'];
															$rec['type']=341;
															$rec['sum_kr']=0;
															$rec['sum_ekr']=0;
															$rec['sum_rep']=0;					
															$rec['sum_kom']=0;
															$rec['item_id']=get_item_fid($get_item);
															$rec['item_name']=$get_item[name];
															$rec['item_count']=1;
															$rec['item_type']=$get_item[type];
															$rec['item_cost']=$get_item[cost];
															$rec['item_dur']=$get_item[duration];
															$rec['item_maxdur']=$get_item[maxdur];
															$rec['item_ups']=0;
															$rec['item_unic']=0;
															$rec['item_incmagic']='';
															$rec['item_incmagic_count']='';
															$rec['item_arsenal']='';
															$rec['add_info']="(Авто возврат)".$get_item['klan_name'];
														        add_to_new_delo($rec); 
												 			$message="<font color=red>Внимание!</font> Ваша заявка на продажу ваучера <b>{$get_item[ecost]} екр.</b> в клановую казну \"".$get_item['klan_name']."\" отклонена по истечению 24-х часов. ";
															telepost_new($gtelo,$message);
												 }
												 else
												 {
												 echo "error: ";
												 echo mysql_error();
												 }
												 
					 					
				 				}

							}
							



lockDestroy("cron_vaucher_job");
?>