<?
	session_start();
	include "connect.php";	
	include "functions.php";	
	include "bank_functions.php";		

	
	if ($user[klan]=='') { 	die('Файл не доступен');}
	
	
	$menu=(int)($_GET[id]);
	$param=(int)($_GET[param]);
	$bonekr=get_ekr_addbonus();	

?>



<table border=0 width=750 height=330 >
<tr><td  valign=top align="center"  colspan="2"><center><font style="COLOR:#8f0000;FONT-SIZE:12pt">
<?
 if (( (($menu>=40) AND  ($menu<=54) ) OR ($menu==2) OR ($menu==4) OR ($menu==5) OR ($menu==6) OR ($menu==7) OR ($menu==8) OR ($menu==10) OR ($menu==11) OR ($menu==23) OR ($menu==20) )   AND ($param==0) )
 	{
 	echo "<B>Пополнение клановой казны:</B>";
 	}
else  if ( (($menu>=131) AND  ($menu<=132) )  AND ($param==0) )
	{
 	echo "<B>Кредовая казна:</B>";
	} 	
else  if ( (($menu>=141) AND  ($menu<=142) )  AND ($param==0) )
	{
 	echo "<B>Безопасность:</B>";
	}	
else  if ( (($menu>=151) AND  ($menu<=152) )  AND ($param==0) )
	{
 	echo "<B>Екровая казна:</B>";
	}	
/*else  if ( (($menu>=61) AND  ($menu<=63) )  AND ($param==0) )
	{
 	echo "<B>Установить с екр казны:</B>";
	}*/	
else  if ( ($menu=171)  AND ($param==0) )
	{
 	echo "<B>Заявки на выкуп ваучеров:</B>";
	}	
 	else
 	{
 	die("Ошибка!");
 	}	
?>
</font></center></td>
<td  valign=top align="right"><a href=# onClick="closeinfo();" title="Закрыть"><img src='http://i.oldbk.com/i/bank/bclose.png' style="position:relative;top:-20px;right:-20px;" border=0 title='Закрыть'></a></td>
 </tr>
<tr>
<td width=25>&nbsp;</td>
<td width=900 height=250 valign="top" >

<?


if ($menu==2)
{
//наш вмз
			echo '<center>
			<table border=0 bgcolor=#eeeeee width="100%" height="100%" align="center">
			<tr valign="top"  align="center" >
			<td align="center">';
			$usd_kurs=get_ekr_usd();
			$STRNAME="WMZ";
			
			echo '<p><b><font color="red">Оплатить с помощью '.$STRNAME.': </font></b></p>' ;
		
			echo '
				<table>
					<tr>
						<td align="center">';				
				echo '<form method="post" action="https://merchant.webmoney.ru/lmi/payment.asp" target= _blank>';				
				echo '<input type="hidden" name="traderid" value="0:'.$_SESSION['bankid'].':666:'.$user['id'].'">';		
				echo '&nbsp;&nbsp;Сумма: <input name="LMI_PAYMENT_AMOUNT" value="0" size="8" id=awmz onChange=\'javascript: callcwmz(this.value,\''.$usd_kurs.'\')\';  onkeyup="this.value=this.value.replace(/[^\d\.]/,\'\'); callcwmz(this.value,'.$usd_kurs.');"> WMZ 
				<input type="hidden" value="Z755383101103" name="LMI_PAYEE_PURSE">';
				echo '<input type="hidden" name="LMI_PAYMENT_DESC" value="Покупка в проекте oldbk.com">';				
				echo '<br><small><b> 1  екр. = '.$usd_kurs.'WMZ </b></small><br><br><br>';
				echo '<input type="hidden" name="LMI_PAYMENT_NO" value="'.time().'"><br><br>';
				echo 'В казну: <input type=text id=ekwmz size="8" onChange=\'javascript: callcekrwmz(this.value,\''.$usd_kurs.'\')\';  onkeyup="this.value=this.value.replace(/[^\d\.]/,\'\'); callcekrwmz(this.value,'.$usd_kurs.');"> екр. ';						
				echo '<br><br><br><input type="submit" value="Оплатить" onClick="closeinfo();" ><br></form>';


				echo '</td>
							</tr>
						</table>
				</td>
				</tr></table>
				</center>';

}
elseif ($menu==4 OR ($menu==5) OR ($menu==6) OR ($menu==7) OR ($menu==8) OR ($menu==10) OR ($menu==11) )
{
//наш вмр
		$LMI_SDP_TYPE[5]=3; // Интернет-банк "Альфа-Клик"
		$LMI_SDP_TYPE[6]=5; // Интернет-банк "Русский стандарт"
		$LMI_SDP_TYPE[10]=6; // Интернет-банк "ВТБ24"
		$LMI_SDP_TYPE[7]=9; //Интернет-банк "Промсвязьбанк"
	   	$LMI_SDP_TYPE[8]=14; // Интернет-банк "Сбербанк Онлайн"		
	   	$LMI_SDP_TYPE[11]=11; // Почта РФ

	   	$LMI_SDP_TYPE[12]=20; //  битки
	   	//authtype_16
	   	
	   	$online_bank='';

	   	if ($LMI_SDP_TYPE[$menu]>0) { $online_bank="?at=authtype_18"; }

	   	if ($LMI_SDP_TYPE[$menu]==14) { $online_bank="?at=authtype_21"; }
	   	if ($LMI_SDP_TYPE[$menu]==11) { $online_bank="?at=authtype_11"; }	   	
	   	if ($LMI_SDP_TYPE[$menu]==16) { $online_bank="?at=authtype_16"; }	   		   	
	   	if ($LMI_SDP_TYPE[$menu]==20) { $online_bank="?at=authtype_20"; }
	   	
			echo '<center>
			<table border=0 bgcolor=#eeeeee width="100%" height="100%" align="center">
			<tr valign="top"  align="center" >
			<td align="center">';
			
			$RUR=get_rur_curs();
			$STRNAME="WMR";
			
			echo '<p><b><font color="red">Оплатить с помощью '.$STRNAME.': </font></b></p>' ;
		
			echo '
				<table>
					<tr>
						<td align="center">';				
				echo '<form method="post" action="https://merchant.webmoney.ru/lmi/payment.asp'.$online_bank.'" target= _blank>';				
				echo '<input type="hidden" name="traderid" value="0:'.$_SESSION['bankid'].':666:'.$user['id'].'">';		
				echo '&nbsp;&nbsp;Сумма: <input name="LMI_PAYMENT_AMOUNT" value="0" size="8" id=wmrrub onChange=\'javascript: callcekrwmr(this.value,'.$RUR.')\';  onkeyup="this.value=this.value.replace(/[^\d]/,\'\'); callcekrwmr(this.value,'.$RUR.');"> WMR ';
				echo '<input type="hidden" value="R418522840749" name="LMI_PAYEE_PURSE">';
				echo '<input type="hidden" name="LMI_PAYMENT_DESC" value="Покупка в проекте oldbk.com">';
				echo '<input type="hidden" name="LMI_PAYMENT_NO" value="'.time().'"><br><br>
				<small><b> 1  екр. = '.$RUR.' WMR </b></small><br><br>
				В казну: <input type=text id=wmrekr size="8" onChange=\'javascript: callcrubwmr(this.value,'.$RUR.')\';  onkeyup="this.value=this.value.replace(/[^\d\.]/,\'\'); callcrubwmr(this.value,'.$RUR.');"> екр. ';
				echo '<br><br><br>';
				
				if ($LMI_SDP_TYPE[$menu]==3 || $LMI_SDP_TYPE[$menu]==5 || $LMI_SDP_TYPE[$menu]==6 || $LMI_SDP_TYPE[$menu]==9) {
			   	 echo '<input type="hidden" name="LMI_SDP_TYPE" value="'.$LMI_SDP_TYPE[$menu].'">'; 
		 	   	 echo '<input type="hidden" name="LMI_ALLOW_SDP" value="'.$LMI_SDP_TYPE[$menu].'">'; 
			   	 }
				
				echo '<input type="submit" value="Оплатить" onClick="closeinfo();" ><br></form>';


				echo '</td>
							</tr>
						</table>
				</td>
				</tr></table>
				</center>';

}
elseif ($menu==23)
{
//наш пейпал

			echo '<center>
			<table border=0 bgcolor=#eeeeee width="100%" height="100%" align="center">
			<tr valign="top"  align="center" >
			<td align="center">';
			$usd_kurs=get_ekr_usd();
			$usd_kurs+=0.05;		
			$STRNAME="PayPal";
			
			echo '<p><b><font color="red">Оплатить с помощью '.$STRNAME.': </font></b></p>' ;
		
			echo '
				<table>
					<tr>
						<td align="center">';				
				echo '<form method="post" action="bank.php"  target=_blank>';
		 		echo '<input type="hidden" name="paypal_type" value="666">'; // тип 1 пополнение счета
		 		echo '<input type="hidden" name="paypal_param" value="666">'; // доппараметры
				
				echo '&nbsp;&nbsp;Сумма: <input name="paypal_amount" value="0" size="8" id=awmz onChange=\'javascript: callcwmz(this.value,\''.$usd_kurs.'\')\';  onkeyup="this.value=this.value.replace(/[^\d\.]/,\'\'); callcwmz(this.value,'.$usd_kurs.');"> USD ';
				echo '<br><small><b> 1  екр. = '.$usd_kurs.'USD </b></small><br><br><br>';
				echo 'В казну: <input type=text id=ekwmz size="8" onChange=\'javascript: callcekrwmz(this.value,\''.$usd_kurs.'\')\';  onkeyup="this.value=this.value.replace(/[^\d\.]/,\'\'); callcekrwmz(this.value,'.$usd_kurs.');"> екр. ';		

				echo '<br><br><br><input type="submit" value="Оплатить" onClick="closeinfo();" ><br></form>';


				echo '</td>
							</tr>
						</table>
				</td>
				</tr></table>
				</center>';

}
elseif ($menu==20)
{
//наш ликпай

			echo '<center>
			<table border=0 bgcolor=#eeeeee width="100%" height="100%" align="center">
			<tr valign="top"  align="center" >
			<td align="center">';
			$usd_kurs=get_ekr_usd();
			
			$STRNAME="Liqpay";
			
			echo '<p><b><font color="red">Оплатить с помощью '.$STRNAME.': </font></b></p>' ;
		
			echo '
				<table>
					<tr>
						<td align="center">';				
				echo '<form method="post" action="bank.php"  target=_blank>';
		 		echo '<input type="hidden" name="liqpay_type" value="666">'; // тип 1 пополнение счета
		 		echo '<input type="hidden" name="liqpay_param" value="666">'; // доппараметры
				
				echo '&nbsp;&nbsp;Сумма: <input name="liqpay_amount" value="0" size="8" id=awmz onChange=\'javascript: callcwmz(this.value,\''.$usd_kurs.'\')\';  onkeyup="this.value=this.value.replace(/[^\d\.]/,\'\'); callcwmz(this.value,'.$usd_kurs.');"> USD ';
				echo '<br><small><b> 1  екр. = '.$usd_kurs.'USD </b></small><br><br><br>';
				echo 'В казну: <input type=text id=ekwmz size="8" onChange=\'javascript: callcekrwmz(this.value,\''.$usd_kurs.'\')\';  onkeyup="this.value=this.value.replace(/[^\d\.]/,\'\'); callcekrwmz(this.value,'.$usd_kurs.');"> екр. ';		

				echo '<br><br><br><input type="submit" value="Оплатить" onClick="closeinfo();" ><br></form>';


				echo '</td>
							</tr>
						</table>
				</td>
				</tr></table>
				</center>';

}
else
if (($menu>=40) and ($menu<=48))
	{
			$usd_kurs=get_ekr_usd();
			echo '<center>
			<table border=0 bgcolor=#eeeeee width="100%" height="100%" align="center">
			<tr valign="top"  align="center" >
			<td align="center">';
			
			
				$STRNAME='OKPAY ';
				$CTYPE='USD';
				$RUR=get_rur_curs();				

				if ($menu==41) {
									$STRNAME='МТС';	// 	Mts 	MTS 	 руб
		 							$CTYPE='RUB'; 
 							}
				elseif ($menu==42)	{
									$STRNAME='Tele2'; //  	Tele2 	TL2  руб
									$CTYPE='RUB';
								  }

				elseif ($menu==43)	{
									$STRNAME='Beeline'; // 	Beeline 	BLN руб
									$CTYPE='RUB';
								  }
				elseif ($menu==44)	{
									$STRNAME='Мегафон'; // 	MegaFon 	MGF 	руб			
									$CTYPE='RUB';
								  }
				elseif ($menu==45)	{
									$STRNAME='Yandex Деньги'; //Yandex Money 	YMO    руб
									$CTYPE='RUB';
								  }
				elseif ($menu==46)	{
									$STRNAME='QIWI - кошелек'; // 	QIW  руб
									$CTYPE='RUB';
								  }
									
				elseif ($menu==47)	{
									$STRNAME='Альфа-Клик'; //Alfa Click 	ALF 
									$CTYPE='RUB';									
								  }
				elseif ($menu==48)	{
									$STRNAME='"Сбербанк"'; // Sberbank 	SBR  руб
									$CTYPE='RUB';
								  }
				elseif ($menu==49)	{
									$STRNAME='Visa & MasterCard'; //  	Visa & MasterCard 	VMF 
									}
				elseif ($menu==50)	{
									$STRNAME='WebMoney'; //  WebMoney 	WMT 
									}
				elseif ($menu==51)	{
									$STRNAME='Bitcoin'; //  bitcoin 	BTC 
									}																		
				elseif ($menu==52)	{
									$STRNAME='Money Polo'; //  Money Polo 	MFS 
									}																				
				elseif ($menu==53)	{
									$STRNAME='W1'; //  W1 	WON 
									}				
				elseif ($menu==54)	{
									$STRNAME='Промсвязьбанк'; //  PromSvyazBank 	PSB 
									$CTYPE='RUB';									
									}	

			
			echo '<p><b><font color="red">Оплатить с помощью '.$STRNAME.': </font></b></p>' ;
		
			

			echo '
				<table>
					<tr>
						<td align="center">';


		echo '<form method="post" action="bank.php" target="_blank">';
		
 		echo '<input type="hidden" name="okpay_subtype" value="'.$menu.'">';
 		echo '<input type="hidden" name="okpay_type" value="666">'; 
 		echo '<input type="hidden" name="okpay_param" value="666">'; 

					
				if ($CTYPE=='USD')
					{
					echo '&nbsp;&nbsp;Сумма: <input name="okpay_amount" value="1" size="8" id=awmz onChange=\'javascript: callcwmz(this.value,\''.$usd_kurs.'\')\';  onkeyup="this.value=this.value.replace(/[^\d\.]/,\'\'); callcwmz(this.value,'.$usd_kurs.');"> USD ';
					echo '<br><small><b> 1  екр. = '.$usd_kurs.'USD </b></small><br><br><br>';
					echo 'В казну: <input type=text value="1" id=ekwmz size="8" onChange=\'javascript: callcekrwmz(this.value,\''.$usd_kurs.'\')\';  onkeyup="this.value=this.value.replace(/[^\d\.]/,\'\'); callcekrwmz(this.value,'.$usd_kurs.');"> екр. ';		
					}
					else
					{
					echo '&nbsp;&nbsp;Сумма: <input name="okpay_amount" value="'.$RUR.'" size="8" id=wmrrub onChange=\'javascript: callcekrwmr(this.value,'.$RUR.')\';  onkeyup="this.value=this.value.replace(/[^\d]/,\'\'); callcekrwmr(this.value,'.$RUR.');"> руб. ';					
					echo '<br><small><b> 1  екр. = '.$RUR.'руб. </b></small><br><br><br>';
					echo 'В казну: <input type=text value="1" id=wmrekr size="8" onChange=\'javascript: callcrubwmr(this.value,'.$RUR.')\';  onkeyup="this.value=this.value.replace(/[^\d\.]/,\'\'); callcrubwmr(this.value,'.$RUR.');"> екр. ';					
					}


		echo '<br><br><br>
		<input type="submit" value="Оплатить" onClick="closeinfo();"><br>
		</form>';

		echo '</td>
					</tr>
				</table>
		</td>
		</tr></table>
		</center>';

		}
else		
if ($menu==131)
	{

		echo '<center>
			<table border=0 bgcolor=#eeeeee width="100%" height="100%" align="center">
			<tr valign="top"  align="center" >
			<td align="center">';
			
		echo '<p><b><font color="red">Пополнить клановую казну: </font></b></p>' ; 

			 echo "<form method=post>";			
			 echo "У вас в наличии: <b>".$user[money]." кр.</b><br>";
				echo "<table>";
				echo "<tr>";
				echo "<td align=right>Сумма:</td>";
				echo "<td><input type=text name=add_kr size=5 maxlength=5></td>";					 
				echo "</tr>";
				echo "<tr>";
				echo "<td align=right>Примечание:</td>";
				echo "<td><input type=text name=add_kr_txt value='' size=50 maxlength=150></td>";					 
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan=2 align=center><br><input type=submit name=add value='Пополнить'></td>";
				echo "</tr></table>";					 
			echo "</form>";
						
		echo '	
			</td>
			</tr>
			</table>
		</td>
		</tr></table>
		</center>';

	}		
else		
if ($menu==132)
	{

		echo '<center>
			<table border=0 bgcolor=#eeeeee width="100%" height="100%" align="center">
			<tr valign="top"  align="center" >
			<td align="center">';
			
		echo '<p><b><font color="red">Выдать из казны кредиты: </font></b></p>' ; 

			 echo "<form method=post>";			
				echo "<table>";
				echo "<tr>";
				echo "<td align=right>Сумма:</td>";
				echo "<td><input type=text name=give_kr value='' size=5 maxlength=5></td>";					 
				echo "</tr>";
				echo "<tr>";
				echo "<td align=right>Логин соклановца:</td>";
				echo "<td><input type=text name=give_kr_login value='' size=15></td>";					 
				echo "</tr>";
				echo "<tr>";
				echo "<td align=right>Пароль кредовой казны:</td>";
				echo "<td><input type=text name=give_kr_pass size=12 maxlength=12></td>";					 
				echo "</tr>";
				echo "<tr>";
				echo "<td align=right>Примечание:</td>";
				echo "<td><input type=text name=give_kr_txt value='' size=50 maxlength=150></td>";					 
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan=2 align=center><br><input type=submit name=give value='Выдать'></td>";
				echo "</tr></table>";					 
			echo "</form>";
						
		echo '	
			</td>
			</tr>
			</table>
		</td>
		</tr></table>
		</center>';

	}
else		
if ($menu==141)
	{

		echo '<center>
			<table border=0 bgcolor=#eeeeee width="100%" height="100%" align="center">
			<tr valign="top"  align="center" >
			<td align="center">';
			
		echo '<p><b><font color="red">Использование клановой казны: </font></b></p>' ; 

			 echo "<form method=post>";			
				echo "<table>";
				echo "<tr>";
				echo "<td colspan=2 align=center>Отчетная Дата:</td></tr><tr>";
				echo "<td align=right>С:</td>";
				echo "<td>";
				
				 echo "<input type=text name='looklog_date' value='". date("d.m.Y")."' id=\"calendar-inputField1\" readonly=\"true\" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;' >";
				 echo "<input type=button id=\"calendar-trigger1\" value='...'>";
				 echo "
				<script>
				Calendar.setup({
			        trigger    : \"calendar-trigger1\",
			        inputField : \"calendar-inputField1\",
				dateFormat : \"%d.%m.%Y\",
				onSelect   : function() { this.hide() }
		    			});
				document.getElementById('calendar-trigger1').setAttribute(\"type\",\"BUTTON\");
				</script>";				
				
				echo "</td>";					 
				echo "</tr>";
				echo "<tr>";
				echo "<td align=right>По:</td>";
				echo "<td>";
				
				 echo "<input type=text name='looklog_date_f' value='". date("d.m.Y")."' id=\"calendar-inputField2\" readonly=\"true\" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;' >";
				 echo "<input type=button id=\"calendar-trigger2\" value='...'>";
				 echo "
				<script>
				Calendar.setup({
			        trigger    : \"calendar-trigger2\",
			        inputField : \"calendar-inputField2\",
				dateFormat : \"%d.%m.%Y\",
				onSelect   : function() { this.hide() }
		    			});
				document.getElementById('calendar-trigger2').setAttribute(\"type\",\"BUTTON\");
				</script>";					
				
				
				echo "</td>";					 
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan=2 align=center><br><input type=submit name=look_log value='Просмотр'></td>";
				echo "</tr></table>";	
			echo "</form>";					


						
		echo '	
			</td>
			</tr>
			</table>
		</td>
		</tr></table>
		</center>';

	}	
else		
if ($menu==142)
	{

		echo '<center>
			<table border=0 bgcolor=#eeeeee width="100%" height="100%" align="center">
			<tr valign="top"  align="center" >
			<td align="center">';
			
		echo '<p><b><font color="red">Сменить пароль к казне: </font></b></p>' ; 

			 echo "<form method=post>";			
				echo "<table>";
				
				echo "<tr><td colspan=2 align=center><b>Для кредитного счета:</b></td></tr>";

				echo "<tr>";
				echo "<td align=right>Старый пароль:</td>";
				echo "<td><input type=text name='old_kr_pass' size=12 maxlength=12></td>";					 
				echo "</tr>";
				
				echo "<tr>";
				echo "<td align=right>Новый пароль:</td>";
				echo "<td><input type=text name='new_kr_pass' size=12 maxlength=12></td>";					 
				echo "</tr>";
				
				echo "<tr><td colspan=2 align=center><br><input type=submit name=chpkrass value='Сменить пароль'></td></tr>";				
				
				echo "<tr><td colspan=2 align=center><br><b>Для валютного счета:</b></td></tr>";

				echo "<tr>";
				echo "<td align=right>Старый пароль:</td>";
				echo "<td><input type=text name='old_ekr_pass' size=12 maxlength=12></td>";					 
				echo "</tr>";
				
				echo "<tr>";
				echo "<td align=right>Новый пароль:</td>";
				echo "<td><input type=text name='new_ekr_pass' size=12 maxlength=12></td>";					 
				echo "</tr>";
				
				echo "<tr><td colspan=2 align=center><br><input type=submit name=chpekrass value='Сменить пароль'></td></tr>";								

				
				
				echo "</tr></table>";					 
			echo "</form>";
			

			
						
		echo '	
			</td>
			</tr>
			</table>
		</td>
		</tr></table>
		</center>';

	}
else		
if ($menu==151)
	{
/*
		echo '<center>
			<table border=0 bgcolor=#eeeeee width="100%" height="100%" align="center">
			<tr valign="top"  align="center" >
			<td align="center">';
			
		echo '<p><b><font color="red">Перевести еврокредиты на счет: </font></b></p>' ; 

			 echo "<form method=post>";			
				echo "<table>";
				echo "<tr>";
				echo "<td align=right>Сумма:</td>";
				echo "<td><input type=text name=give_ekr value='' size=5 maxlength=5></td>";					 
				echo "</tr>";
				
				echo "<tr>";
				echo "<td align=right>Логин соклановца:</td>";
				echo "<td><input type=text name=give_ekr_login value='' size=15></td>";					 
				echo "</tr>";
				
				echo "<tr>";
				echo "<td align=right>№ счета:</td>";
				echo "<td><input type=text name=give_bank size=9 value='' ></td>";					 
				echo "</tr>";				
				
				echo "<tr>";
				echo "<td align=right>Пароль екровой казны:</td>";
				echo "<td><input type=text name=give_ekr_pass size=12 maxlength=12></td>";					 
				echo "</tr>";
				echo "<tr>";
				echo "<td align=right>Примечание:</td>";
				echo "<td><input type=text name=give_ekr_txt value='' size=50 maxlength=150></td>";					 
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan=2 align=center><br><input type=submit name=giveb value='Перевести'></td>";
				echo "</tr></table>";					 
			echo "</form>";
						
		echo '	
			</td>
			</tr>
			</table>
		</td>
		</tr></table>
		</center>';
*/
	}	
else		
if ($menu==152)
	{

		echo '<center>
			<table border=0 bgcolor=#eeeeee width="100%" height="100%" align="center">
			<tr valign="top"  align="center" >
			<td align="center">';
			
		echo '<p><b><font color="red">Обменять екр. на кредиты: </font></b></p>' ; 

			 echo "<form method=post>";			
				echo "<table>";
				echo "<tr>";
				echo "<td align=right>Сумма:</td>";
				echo "<td><input type=text name=сhange_ekr value='' size=5 maxlength=5></td>";					 
				echo "</tr>";
				
				echo "<tr>";
				echo "<td align=right>Пароль екровой казны:</td>";
				echo "<td><input type=text name=сhange_ekr_pass size=12 maxlength=12></td>";					 
				echo "</tr>";

				echo "<tr>";
				echo "<td colspan=2 align=center><br><input type=submit name=сhange value='Обменять'></td>";
				echo "</tr></table>";					 
			echo "</form>";

		echo '	
			</td>
			</tr>
			</table>
		</td>
		</tr></table>
		</center>';

	}		 		
/*	
else		
if (($menu==61) OR ($menu==62) OR ($menu==63) )
	{

$arr_txt[61]='Silver'; $arr_m[61]=1; $arr_p[61]="15 екр";
$arr_txt[62]='Gold'; $arr_m[62]=2; $arr_p[62]="40 екр";
$arr_txt[63]='Platinum'; $arr_m[63]=3; $arr_p[63]="100 екр";

		echo '<center>
			<table border=0 bgcolor=#eeeeee width="100%" height="100%" align="center">
			<tr valign="top"  align="center" >
			<td align="center">';
			
		echo '<p><b><font color="red">Установить/продлить '.$arr_txt[$menu].' account стоимость '.$arr_p[$menu].' :</font></b></p>' ; 

			 echo "<form method=post>";	
			 echo "<input type=hidden name=acctype value={$arr_m[$menu]}>";		
				echo "<table>";
				echo "<tr>";
				echo "<td align=right>Логин соклановца:</td>";
				echo "<td><input type=text name=silver_login value='' size=15 ></td>";					 
				echo "</tr>";
				
				echo "<tr>";
				echo "<td align=right>№ счета :</td>";
				echo "<td><input type=text name=silver_bank value='' size=9 ></td>";					 
				echo "</tr>";
			
				
				echo "<tr>";
				echo "<td align=right>Пароль екровой казны:</td>";
				echo "<td><input type=text name=silver_pass size=12 maxlength=12></td>";					 
				echo "</tr>";

				echo "<tr>";
				echo "<td colspan=2 align=center><br><input type=submit name=silver value='Выполнить'></td>";
				echo "</tr></table>";					 
			echo "</form>";


		echo '	
			</td>
			</tr>
			</table>
		</td>
		</tr></table>
		</center>';

	}*/
else		
if ($menu==171)
	{
/*	 $KURS=40;
	$klan = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `short` = '{$user['klan']}' LIMIT 1;"));
   	$polno = array();
	$polno = unserialize($klan['vozm']);
	if (($klan['glava']==$user['id'] OR $polno[$user['id']][4] == 1) )
			{
				$get_all_vau=mysql_query("SELECT * FROM `oldbk`.`clans_preset` where klanid='{$klan[id]}' ORDER BY `pdate` ;");
				if(mysql_num_rows($get_all_vau)>0)
								{
								while($row=mysql_fetch_array($get_all_vau))
									{
									$vau_mes_list.=  "<form method=post><font class=date>".date("d.m.Y H:i",$row[pdate])."</font>: <b>".$row[owner_login]."</b> <a title=\"Инф. о ".$row[owner_login]."\" target=\"_blank\" href=\"inf.php?".$row[owner]."\"><img width=\"12\" border=\"0\" height=\"11\" alt=\"Инф. о ".$row[owner_login]."\"  src=\"http://i.oldbk.com/i/inf.gif\"></img></a> продает ваучер на <b>".$row[ecost]." екр</b> за <b>".($row[ecost]*$KURS)." кр.</b>";
									$vau_mes_list.= " <input type=hidden name=vauid value='{$row[itemid]}'>";
									$vau_mes_list.= "<small> Пароль</small>:<input type=text  name=vaupass size=8 maxlength=12>";									
									$vau_mes_list.= "<input type=submit name=yesvau value='Подтвердить'>"; 
									$vau_mes_list.= "<input type=submit name=novau value='Отказать'>"; 								
									$vau_mes_list.= "</form><br>";
									}
								}
								else
								{
								 $vau_mes_list.= "<b>Пока заявок не поступало!</b>";							
								
								}
				echo "<table>";
				echo "<tr>";
				echo "<td align=left>{$vau_mes_list}</td>";
				echo "</tr></table>";	
								
			}
			else
			{
			echo "ОШИБКА ДОСТУПА!";
			}
	*/
	}
		
	
?>


</td>
<td width=25>&nbsp;</td>
</tr>
<tr><td align="center"  colspan="3">

</td></tr>

</table>




