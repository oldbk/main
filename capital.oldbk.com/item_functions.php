<?

function curr_price($item,$check_sowner=0,$shop_jail=0){
	global $user,$shop_skupka;

	$max_ups=5;   // Максимальное кол. апов шмотки - подобная запись есть В РЕМОНТКЕ! Там меня так же!!!

    $prot=mysql_fetch_array(mysql_query('select * from oldbk.shop where id = '.$item[prototype]));
    if($prot[id]>0)
    {
    	//все ок..
    }
    else
    {
    	$prot['cost']=$item[cost];
    	$prot[nlevel]=0;
    }
	    $mf_cost=0;
	    $is_mf = !(strpos($item['name'], '(мф)') === false);
	    if($is_mf>0){
	    	$mf_cost=$prot[cost]*0.5;
	    	if (($prot['gsila'] == 0) and ($prot['glovk'] == 0) and ($prot['ginta'] == 0) and ($prot['gintel'] == 0))
			{
				$mf_cost = round($mf_cost*0.5, 0);
			}

	    }
	    if(ADMIN)
	    {
	      // print_r($prot);
	       //echo $prot[name].': МФ:'.$mf_cost;
	    }

	    $real_price[sowner]=$item[sowner];
	    $real_price[prot_cost]=$prot[cost];
	    
	    if(($prot[ecost]!=0 || $item[ecost]!=0 ) && $shop_jail==1) //тлько для тюрьмы
	    {
	    	 $real_price[prot_cost]=$item[ecost]*10;
	    }
	    
	    	$real_price[mf_cost]=$mf_cost;
	    	$real_price[item_cost]=$real_price[prot_cost];
	    	$cost_add = round($prot['cost'], 0);
		$max_ups_left = $max_ups - $item['ups'];
	    	$mx_op=array(1=>'5',2=>'4',3=>'3',4=>'2',5=>'1');
		$u_cost=0;


		if($item['ups']>0 && $real_price[sowner]==0 && (($check_sowner==1) OR ($item['stavka']>0) ) )
		{
			for($cc=$item['ups'];$cc>0;$cc--)
			{
				$costs[$cc]=upgrade_item($cost_add,$mx_op[$cc]);
				$u_cost+=$costs[$cc][up_cost];
			}
		}
		if(ADMIN)
		{
		       //echo ' под:'.$u_cost;
		}
		$up_price=0;
		$real_price[u_cost]=$u_cost;
		
	    if($item[up_level]>6)
	    {
			$up_lvl_cost=array(7=>'25',8=>'35',9=>'85',10=>'120',11=>'180', 12 => '220');
	  		// $prot[nlevel]>$item[up_level] && $prot[nlevel]>6

	        for($up_lvl=$item[up_level];$up_lvl>6;$up_lvl--)
	        {
	        	if($up_lvl>$prot[nlevel])
	        	{
	        		$up_price+=$up_lvl_cost[$up_lvl];
	        	}
	        }
	    }
	    if(ADMIN)
	    {
	       //echo ' лвл:'.$up_price;
	    }
		
	    $sharp_pr=0;
		if($item['type']==3 && (($shop_skupka==1) OR ($item['stavka']>0) ) )
		{
			$sharp=explode("+",$item['name']);
			if((int)($sharp[1])>0)
			{
				$is_sharp=array(1=>20,2=>40,3=>80,4=>160,5=>320, 6 => 640, 7 => 1280, 8 => 2560);
				$sharp_pr=$is_sharp[$sharp[1]];
			}
		}

	    if(ADMIN)
	    {
	       //echo ' точка:'.$sharp_pr;
	    }
	    $real_price[sharp_pr]=$sharp_pr;
	    //высчитываем от госцены цена + мф + подгоны + апы (цена свитков из храма), берем 90% и за это скупаем в госе
	    $real_price[up_price]=$up_price;
	    $real_price[summ]=$real_price[item_cost]+$real_price[mf_cost]+$real_price[u_cost]+$real_price[up_price]+$real_price[sharp_pr];
	    
	    
	    if($shop_jail==1)
	    {
	    	if($item[includemagic]>0)
	    	{
	    		if(ADMIN)
	    		{
	    			//echo 'Встройка 250';	
	    		}
	    		if($item[includemagiccost]>0)
	    		{
	    			$real_price[summ]+=$item[includemagiccost]*2;
	    		}
	    		else
	    		if($item[includemagicekrcost]>0)
	    		{
	    			$real_price[summ]+=$item[includemagicekrcost]*2*10;
	    		}
	    		
	    		$real_price[summ]+=150;
	    			
	    	}
	    	//проверяем встройки еще
	    }
	    
	    if(ADMIN)
	    {
	     // echo ' итог:'.$real_price[summ].' цена продажи:'.($real_price[summ]).'<br>';
	    }
	return $real_price;
}


?>