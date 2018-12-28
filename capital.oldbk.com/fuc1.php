<?
//// v.7.01 18/10/2012 (3-kom) - размены человекапротив ботов NEW _LOG
function do_razmen_to_bot ($bat,$us, $t_us , $en , $t_en, $us_dem, $en_dem , $us_type , $en_type , $btype)
{
if (($bat[win]==3) and ($bat[status]==0))
{
    #бой идет можем работать
    # проверим того кто ходит
    if ( ($us[hp] > 0) and ($en[hp] > 0) )
    {
           ## жертва и нападающий живые
           ## обновляем таймы
           		$ctA="to".$us[battle_t];
			$ctB="to".$en[battle_t];           		
           		
		mysql_query("update battle set {$ctA}='".time()."' , {$ctB}='".time()."' where id={$bat[id]};");
           ## тупо апдейтим обоих - быстрым запросом
           if ($en_dem > 0)  {
                  		$us[hp]=$us[hp]-$en_dem;
			          if ($us[hp] > 0)
			           {
			           //Жив
				   	mysql_query("update users set hp={$us[hp]} where id={$us[id]} and hp>0 ;");
				   	if (!(mysql_affected_rows()>0))
				   	{
				   	return "_ERR";
				   	}
				   	
			           }
			           else
			           {
			           $us[hp]=0;
			           //мертв
					mysql_query("update users set hp=0 where id={$us[id]} and hp>0;");
					if (!(mysql_affected_rows()>0))
				   	{
				   	return "_ERR";
				   	}
	                            # для боев в лабе меня убили на меня телепортануть в старт - бой еще идет
                                    if ($btype = 30)
                                       {
        	                            mysql_query("UPDATE labirint_users set x=0 , y=0, dead=dead+1 where owner={$us[id]};");
                                       }
			           }
            			}

         if ($us_dem >0 ) {
				$en[hp]=$en[hp]-$us_dem	;
				if ( $en[hp] > 0 )
				{
				//жив
					mysql_query("update users_clons set hp={$en[hp]} where id={$en[id]} and hp>0 ;");
					if (!(mysql_affected_rows()>0))
				   	{
				   	return "_ERR";
				   	}

					if ( ($en[id_user]==83) OR ($en[id_user]==136) )  // бой в бс c архом
					{
						mysql_query("update users set hp={$en[hp]},`fullhptime`='".time()."' where id={$en[id_user]} ;");
					}
					if ($en[id_user]==102) // бой c ботом
					{
						mysql_query("update users set hp={$en[hp]} where id=102 ;");
					}
				}
				else
				{
				$en[hp]=0;
				//мертв
				mysql_query("update users_clons set hp=0 where id={$en[id]} and hp>0;");
				if (!(mysql_affected_rows()>0))
				   	{
				   	return "_ERR";
				   	}
				   	
				if ( ($en[id_user]==83) OR ($en[id_user]==136))  // бой в бс c архом
					{
					mysql_query("update users set hp=0 where id={$en[id_user]} ;");
					}
				if ($en[id_user]==102) // бой с ботом
					{
					mysql_query("update users set hp={$en[hp]} where id=102 ;");
					}

				}

                             }
//////////////////////////////////////////////////////////////////
           ## терь проверим живы ли они - после размена
               if ($us[hp] > 0)
		{
                  #я живой
                          # проверяем живой ли противник
                           if ($en[hp] > 0)
                           {
                           # и противник жив остался
                           #select 'life' as us, 'life' as en;
                            RETURN "11";
                           }
                           else
                           	{

                                                      $Liusers=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat[id]} and battle_t!={$t_us} and hp>0"));
                                                      $Liclons=mysql_fetch_array(mysql_query("select count(*) from users_clons where battle={$bat[id]} and battle_t!={$t_us} and hp>0"));

                                                       if ( ($Liusers[0] > 0) OR ($Liclons[0] > 0))
                                                      {
                                                      # просто умер противник живые в команде еще есть
                                                      RETURN "10";
                                                      }
                                                      else
                                                      # умер последний противник
                                                      {
                                                      if ( (($bat[type] >240 )and ( $bat[type] <269 )) OR  ( $bat[type] ==172 ) )
                                                      	  {
                                                      	  //add to log-
                                                      	  addlog($bat[id],"!:X:".time()."::1\n");                                                      	  
	                                           	  RETURN "10";
                                                      	  }
                                                      	  else
                                                      	  {
								 mysql_query("update battle set status=1 where id={$bat[id]} ;");
        	                                                 RETURN "1010";
							  }

                                                      }
                             }
                  }
                  else
                 {
                  # я погиб

                         # проверяем живой ли противник
                         if ($en[hp] > 0)
                         {
                          # и противник жив остался

                                   # проверим остались ли живые в моей команде
                                    $Liusers=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat[id]} and battle_t={$t_us} and hp>0 ; "));
                                    $Liclons=mysql_fetch_array(mysql_query("select count(*) from users_clons where battle={$bat[id]} and battle_t={$t_us} and hp>0 ;"));
			 if ( ($Liusers[0] > 0) OR ($Liclons[0] > 0))
                                    {
                                    # да есть еще живые у меня в команде
                                    RETURN "01";
                                    }
                                    else
                                    {
                                    # не я был последни и умер
                                    		//проверяем 3ю команду
                                    		  $Liusers_other=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat[id]} and battle_t!={$t_us} and battle_t!={$t_en} and hp>0"));
	                                          $Liusers_other2=mysql_fetch_array(mysql_query("select count(*) from users_clons where battle={$bat[id]} and battle_t!={$t_us} and battle_t!={$t_en} and hp>0"));
                                                   if ( ($Liusers_other[0] > 0) OR ($Liusers_other2[0] > 0))
                                                   {
		                                    RETURN "01";                                                   
                                                   }
                                                   else
                                                   {
                                                   
                                                    /*if (  $bat[type] ==172 ) 
                                                      	  {
                                                      	  //add to log-
                                                      	  addlog($bat[id],'<span class=date>'.date("H:i").'</span> <b>Ожидаем следующую волну монстров....</b>!<BR>');
	                                           	  RETURN "01";
                                                      	  }
                                                      	  else
                                                      	 */ 
                                                      	  {
							    mysql_query("update battle set status=1 where id={$bat[id]} ;");
        			                            RETURN "0101";                                                   
        			                          }
                                                   }
                                    	

                                    }
                        }
                         else
                          {
                           # противник умер

                              # 00порверяем для начала команду свою
                              $Liusers1=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat[id]} and battle_t={$t_us} and hp>0"));
                              $Liclons1=mysql_fetch_array(mysql_query("select count(*) from users_clons where battle={$bat[id]} and battle_t={$t_us} and hp>0"));
				 if ( ($Liusers1[0] > 0) OR ($Liclons1[0] > 0))
                               {
                               # да у меня еще есть живые
                                #а у врага?

                              $Liusers2=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat[id]} and battle_t!={$t_us} and hp>0" ));
                              $Liclons2=mysql_fetch_array(mysql_query("select count(*) from users_clons where battle={$bat[id]} and battle_t!={$t_us} and hp>0 "));
  				 if ( ($Liusers2[0] > 0) OR ($Liclons2[0] > 0))
						{
                                            # у врага тоже есть живые бой идет дальше
                                                RETURN "00";
                                              }
                                            else
                                            {
                                            # у у врага нету живых мойей тимы победа - хоть я и враг погибли
                                            
        	                            	  	  mysql_query("update battle set status=1 where id={$bat[id]} ;");
	        	                                    RETURN "0010";
                                            }
				}
                               else
                               {
                               # не у меня живых нету - проерим терь врага вдруг ничья?
                              $Liusers2=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat[id]} and battle_t!={$t_us} and hp>0"));
                              $Liclons2=mysql_fetch_array(mysql_query("select count(*) from users_clons where battle={$bat[id]} and battle_t!={$t_us} and hp>0"));
				 if ( ($Liusers2[0] > 0) OR ($Liclons2[0] > 0))
                                           {
                                           # живые есть но оба чувака погибли 
                                                       	  //проверка остальных
		                                          $Liusers_other=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat[id]} and battle_t!={$t_us} and battle_t!={$t_en} and hp>0"));
		                                          $Liusers_other2=mysql_fetch_array(mysql_query("select count(*) from users_clons where battle={$bat[id]} and battle_t!={$t_us} and battle_t!={$t_en} and hp>0"));
	                                                   if ( ($Liusers_other[0] > 0) OR ($Liusers_other2[0] > 0))
	                                                   {
		                                                #v 3-i  komande est' jivie - boi idet dalshe
								RETURN "00";   
	                                                   }
	                                                   else
	                                                   {
		                                     	    mysql_query("update battle set status=1 where id={$bat[id]} ;");
                		                           RETURN "0001";	                                                   
	                                                   }

                                           }
                                           else
                                           {
                                           #не нету - таки ничья
                                       	   mysql_query("update battle set status=1 where id={$bat[id]} ; ");
                                           RETURN "0000";
                                           }

                               }


                         }

	  }
	}
	 else
	  {
	  RETURN "NO";
	  }
}
	else
	{
	RETURN "ENDB";
	}
}


function do_razmen_to_telo ($bat , $us , $t_us , $en, $t_en, $us_dem, $en_dem, $us_type, $en_type, $batl_type, $pkrit)
{
if (($bat[win]==3) and ($bat[status]==0))
{
    # проверим того кто ходит
    if ( ($us[hp] > 0) and ($en[hp] > 0) )
  	{
                  ## жертва и нападающий живые
	           ## обновляем таймы
       		$ctA="to".$us[battle_t];
		$ctB="to".$en[battle_t];           		
		mysql_query("update battle set {$ctA}='".time()."' , {$ctB}='".time()."' where id={$bat[id]};");

           
                      if ($en_dem > 0)  {
                  		$us[hp]=$us[hp]-$en_dem;
			          if ($us[hp] > 0)
			           {
			           //Жив
				   	mysql_query("update users set hp={$us[hp]} where id={$us[id]} and hp>0 ;");
				   	if (!(mysql_affected_rows()>0))
				   	{
				   	return "_ERR";
				   	}
				   	
			           }
			           else
			           {
			           $us[hp]=0;
			           //мертв
					mysql_query("update users set hp=0 where id={$us[id]} ;");
					if (!(mysql_affected_rows()>0))
				   	{
				   	return "_ERR";
				   	}
			           }
            			}
           
                    if ($us_dem >0 ) {
				$en[hp]=$en[hp]-$us_dem	;
				if ( $en[hp] > 0 )
				{
				//жив
					mysql_query("update users set hp={$en[hp]} where id={$en[id]} and hp>0 ");
					if (!(mysql_affected_rows()>0))
				   	{
				   	return "_ERR";
				   	}
				}
				else
				{
				$en[hp]=0;
				//мертв
				mysql_query("update users set hp=0 where id={$en[id]} ");
				if (!(mysql_affected_rows()>0))
				   	{
				   	return "_ERR";
				   	}
				}
                             }
           
   
//////////////////////////////////////////////////////////////////
           ## терь проверим живы ли они - после размена
               if ($us['hp'] > 0)
		{
                  #я живой
                          # проверяем живой ли противник
                           if ($en[hp] > 0)
                           {
                           # и противник жив остался
                            RETURN "11";
                           }
                           else
                           	{
                           	//противник труп
                           
		  if ($batl_type != 8) // нет травмы в цветоцных от удара
                           	{
                           	//ставим или нет травмы
	                           if ($us_type=='krita')                           
					{
		                           #30% travma - probiv blok
	                	          mysql_query("UPDATE users SET hp=0 , sila = IF((@RR:=100*RAND())<(10+{$pkrit}), settravma3(id,'sila',sila,level,{$bat['id']},{$batl_type},align,trv), sila), lovk = IF(@RR>=(10+{$pkrit}) AND @RR<(20+{$pkrit}), settravma3(id,'lovk',lovk,level,{$bat['id']},{$batl_type},align,trv), lovk), inta = IF(@RR>=(20+{$pkrit}) AND @RR<(30+{$pkrit}), settravma3(id,'inta',inta,level,{$bat['id']},{$batl_type},align,trv), inta) where  id={$en['id']} ");
	                	          log_krits_deb($bat['id']."::KRITA_1_to::".$en['id']);
                	           	}
                           elseif ($us_type=='krit')
					{                                             
	                               #12% - otkrita travma
        	                        mysql_query("UPDATE users SET hp=0 , sila = IF((@RR:=100*RAND())<(5+{$pkrit}), settravma3(id,'sila',sila,level,{$bat['id']},{$batl_type},align,trv), sila), lovk = IF(@RR>=(5+{$pkrit}) AND @RR<(10+{$pkrit}), settravma3(id,'lovk',lovk,level,{$bat['id']},{$batl_type},align,trv), lovk), inta = IF(@RR>=(10+{$pkrit}) AND @RR<(15+{$pkrit}), settravma3(id,'inta',inta,level,{$bat['id']},{$batl_type},align,trv), inta) where  id={$en['id']} ");
	                	          log_krits_deb($bat['id']."::KRIT_1_to::".$en['id']);        	                        
                			}
                		}	                           	

                                                      $Liusers=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat[id]} and battle_t!={$t_us} and hp>0"));
                                                      if ($Liusers[0] > 0) 
                                                      {
                                                      # просто умер противник живые люди в команде еще есть
                                                      RETURN "10";
                                                      }
                                                      else
                                                      {
                                                       $Liclons=mysql_fetch_array(mysql_query("select count(*) from users_clons where battle={$bat[id]} and battle_t!={$t_us} and hp>0"));
                                                       if ($Liclons[0] > 0) 
	                                                      {
	                                                      # просто умер противник живые клоны  в команде еще есть
	                                                      RETURN "10";
	                                                      }
								else
								{                                                      
	                                                      	# умер последний противник
								 mysql_query("update battle set status=1 where id={$bat[id]} ;");
        	                                                 RETURN "1010";
        	                                                }
                                                      }
                             }
                  }
                  else
                 {
                  # я погиб
	  if ($batl_type != 8) // нет травмы в цветоцных от удара
	  {
                  //делаем травму если надо
                       if ($en_type=='krita')                           
                           {                      
                           #30% travma - probiv blok
                           mysql_query("UPDATE users SET hp=0 , sila = IF((@RR:=100*RAND())<(10+{$pkrit}), settravma3(id,'sila',sila,level,{$bat['id']},{$batl_type},align,trv), sila), lovk = IF(@RR>=(10+{$pkrit}) AND @RR<(20+{$pkrit}), settravma3(id,'lovk',lovk,level,{$bat['id']},{$batl_type},align,trv), lovk), inta = IF(@RR>=(20+{$pkrit}) AND @RR<(30+{$pkrit}), settravma3(id,'inta',inta,level,{$bat['id']},{$batl_type},align,trv), inta) where  id={$us['id']} ");
               	          log_krits_deb($bat['id']."::KRITA_2_to::".$us['id']);                           
                           }
                           elseif ($en_type=='krit')
                            {
                               #12% - otkrita travma
                              mysql_query("UPDATE users SET hp=0 , sila = IF((@RR:=100*RAND())<(5+{$pkrit}), settravma3(id,'sila',sila,level,{$bat['id']},{$batl_type},align,trv), sila), lovk = IF(@RR>=(5+{$pkrit}) AND @RR<(10+{$pkrit}), settravma3(id,'lovk',lovk,level,{$bat['id']},{$batl_type},align,trv), lovk), inta = IF(@RR>=(10+{$pkrit}) AND @RR<(15+{$pkrit}), settravma3(id,'inta',inta,level,{$bat['id']},{$batl_type},align,trv), inta) where   id={$us['id']} ");
               	          log_krits_deb($bat['id']."::KRIT_2_to::".$us['id']);                              
                           }
          }

                         # проверяем живой ли противник
                         if ($en[hp] > 0)
                         {
                          #  противник жив остался
                          # проверим остались ли живые в моей команде
                                    $Liusers=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat[id]} and battle_t={$t_us} and hp>0 ; "));
				 if ( $Liusers[0] > 0) 
                                    {
                                    # да есть еще живые у меня в команде люди
                                    RETURN "01";
                                    }
                                    else
                                    {
                                       $Liclons=mysql_fetch_array(mysql_query("select count(*) from users_clons where battle={$bat[id]} and battle_t={$t_us} and hp>0 ;"));
                                       if ($Liclons[0] > 0)
                                       {
                                        # да есть еще живые у меня в команде клоны
                                       RETURN "01";
                                       }
                                    else 
                                    {
                                    # не я был последни и умер
                                    		
                                    	if ($bat['t3']!='')
                                    		{
                                    		//проверяем 3ю команду = если надо
                                    		 	 	$Liusers_other=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat[id]} and battle_t!={$t_us} and battle_t!={$t_en} and hp>0"));
        		                                           if ( $Liusers_other[0] > 0)
                		                                   {
                		                                   //есть в 3-е живые люди
		        		                            RETURN "01";                                                   
                                		                   }
                                		                   else
                                		                   {
 					                                   $Liusers_other2=mysql_fetch_array(mysql_query("select count(*) from users_clons where battle={$bat[id]} and battle_t!={$t_us} and battle_t!={$t_en} and hp>0"));
 					                                   if  ($Liusers_other2[0] > 0)
 					                                   {
										 //есть в 3-е живые боты
				        		                            RETURN "01";                                                   
 					                                   }
                                		                   }
                                        	}
                                                   
					    mysql_query("update battle set status=1 where id={$bat[id]} ;");
        			            RETURN "0101";                                                   

                                    	}

                                    }
                        }
                         else
                          {
                           # противник умер
                           
                           //ставим травму противнику
  if ($batl_type != 8) // нет травмы в цветоцных от удара                           
  			{
   	                        if ($us_type=='krita')                           
				{
        	                   #30% travma - probiv blok
        		          mysql_query("UPDATE users SET hp=0 , sila = IF((@RR:=100*RAND())<(10+{$pkrit}), settravma3(id,'sila',sila,level,{$bat['id']},{$batl_type},align,trv), sila), lovk = IF(@RR>=(10+{$pkrit}) AND @RR<(20+{$pkrit}), settravma3(id,'lovk',lovk,level,{$bat['id']},{$batl_type},align,trv), lovk), inta = IF(@RR>=(20+{$pkrit}) AND @RR<(30+{$pkrit}), settravma3(id,'inta',inta,level,{$bat['id']},{$batl_type},align,trv), inta) where  id={$en['id']} ");
                	          log_krits_deb($bat['id']."::KRITA_3_to::".$en['id']);        		          
	           		}
			        elseif ($us_type=='krit')
				{                                             
        	               #12% - otkrita travma
                	        mysql_query("UPDATE users SET hp=0 , sila = IF((@RR:=100*RAND())<(5+{$pkrit}), settravma3(id,'sila',sila,level,{$bat['id']},{$batl_type},align,trv), sila), lovk = IF(@RR>=(5+{$pkrit}) AND @RR<(10+{$pkrit}), settravma3(id,'lovk',lovk,level,{$bat['id']},{$batl_type},align,trv), lovk), inta = IF(@RR>=(10+{$pkrit}) AND @RR<(15+{$pkrit}), settravma3(id,'inta',inta,level,{$bat['id']},{$batl_type},align,trv), inta) where  id={$en['id']} ");
                	          log_krits_deb($bat['id']."::KRIT_3_to::".$en['id']);                	        
				}
			}  



                              # 00порверяем для начала команду свою
                              $Liusers1=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat[id]} and battle_t={$t_us} and hp>0"));
                              if ($Liusers1[0] == 0) {  $Liclons1=mysql_fetch_array(mysql_query("select count(*) from users_clons where battle={$bat[id]} and battle_t={$t_us} and hp>0")); }
				 if ( ($Liusers1[0] > 0) OR ($Liclons1[0] > 0))
                               {
                               # да у меня еще есть живые
                                #а у врага?

	                              $Liusers2=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat[id]} and battle_t!={$t_us} and hp>0" ));
  					if ( $Liusers2[0] > 0) 
						{
	                                        # у врага тоже есть живые бой идет дальше люди
                                                RETURN "00";
                                              }
                                            else
                                            {
                                             $Liclons2=mysql_fetch_array(mysql_query("select count(*) from users_clons where battle={$bat[id]} and battle_t!={$t_us} and hp>0 "));
						if ( $Liclons2[0] > 0) 
							{
		                                        # у врага тоже есть живые бой идет дальше  боты
        	                                        RETURN "00";							
							}
        	                                     else
	                                             {
	                                            # у у врага нету живых мойей тимы победа - хоть я и враг погибли
               	                            	  	  mysql_query("update battle set status=1 where id={$bat[id]} ;");
		       	                                    RETURN "0010";
		       	                            }
                                            }
				}
                               else
                               {
                               # не у меня живых нету - проерим терь врага вдруг ничья?
                              $Liusers2=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat[id]} and battle_t!={$t_us} and hp>0"));
                              
                              if ($Liusers2[0] == 0)
                              {
                              $Liclons2=mysql_fetch_array(mysql_query("select count(*) from users_clons where battle={$bat[id]} and battle_t!={$t_us} and hp>0"));
                              }

				 if ( ($Liusers2[0] > 0) OR ($Liclons2[0] > 0))
                                           {
                                           # живые есть у врага  но оба чувака погибли 
        	                            	if ($bat['t3']!='')
        	                            		{
	                                    		//проверяем 3ю команду = если надо                                           
                	                                       	  //проверка остальных
			                                         $Liusers_other=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat[id]} and battle_t!={$t_us} and battle_t!={$t_en} and hp>0"));
	                                                   	if  ($Liusers_other[0] > 0) 
	                                                   	{
		                                                	#v 3-i  komande est' jivie - boi idet dalshe люди
									RETURN "00";   
	                                                  	 }
	                                                  	 else
	                                                  	 {
	                                                  	  $Liusers_other2=mysql_fetch_array(mysql_query("select count(*) from users_clons where battle={$bat[id]} and battle_t!={$t_us} and battle_t!={$t_en} and hp>0"));
	                                                  	  if  ($Liusers_other2[0] > 0) 
		                                                   	{
		                                                	#v 3-i  komande est' jivie - Боты
									RETURN "00";   
		                                                  	 }
	                                                  	 }
	                                                  }
	                                                   

						    //выходит что уменя в тиме живых нет а упротивника есть = хоть я и прибил противника в размене =  и поэтому победа врага
	                                     	   mysql_query("update battle set status=1 where id={$bat[id]} ;");
               		                           RETURN "0001";	                                                   
                                           }
                                           else
                                           {
                                           #не нету - таки ничья
                                       	   mysql_query("update battle set status=1 where id={$bat[id]} ; ");
                                           RETURN "0000";
                                           }

                               }


                         }

	  }
	}
	 else
	  {
	  RETURN "NO";
	  }
	}
	else
	{
		return "ENDB";	
	}

}

function log_krits_deb($text)
{
/*
	$fp = fopen ("/www/other/krits.txt","a"); //открытие
	flock ($fp,LOCK_EX); //БЛОКИРОВКА ФАЙЛА
	fputs($fp , $text."\n"); //работа с файлом
	fflush ($fp); //ОЧИЩЕНИЕ ФАЙЛОВОГО БУФЕРА И ЗАПИСЬ В ФАЙЛ
	flock ($fp,LOCK_UN); //СНЯТИЕ БЛОКИРОВКИ
	fclose ($fp); //закрытие
*/
}

?>