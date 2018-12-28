<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="windows-1251">
	<meta name='yandex-verification' content='60ef46abc2646a77' />
        <meta name="keywords" content="oldcombats, Бойцовский клуб, БК, комбатс, онлайн, игры, игра, online, on-line, games, combats, mmorpg игры бесплатно, бесплатная, ролевая, ролевые, бесплатные, браузерная рпг, RPG " />
        <meta name="description" content="Новая бесплатная многопользовательская MMORPG combats онлайн игра «Старый Бойцовский Клуб - ОлдБК». Стань участником Бойцовского Клуба Комбатс!"/>
	<meta name="robots" content="index, follow"/>
	<base href="http://oldbk.com/">
	<meta name="author" content="oldbk.com"/>
	<?
include "/../connect.php";
	$limit=(int)($_GET[lim]);
	if ($limit == 0)
	{
		$limit=4;
	}
		
?>


</head>
<body>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td width="100%" align="center">
	<table border="0" cellpadding="0" cellspacing="0" rules="none">
	 <tr>
	  <td width="45">
	   &nbsp;
	  </td>
	  <td width="1100" style="background-repeat: no-repeat" height="236" background="http://i.oldbk.com/news/images/header_back.jpg">
	 <a href="http://oldbk.com"><img width="1099" height="230"  border=0  src="http://oldbk.com/i/clear.gif"></a>
	  </td>
	  <td width="50">
	   &nbsp;
	  </td>
	 </tr>
	 <tr>
	  <td>
	   &nbsp;
	  </td>
	  <td height="147" background="http://i.oldbk.com/news/images/menu_reg_back.jpg">
		&nbsp;
	  </td>
	  <td>
	   &nbsp;
	  </td>
	 </tr>
	 <tr>
	  <td>
	   &nbsp;
	  </td>
	  <td valign="top" width="1058" height="800" style="background: url(http://i.oldbk.com/news/images/container_back.jpg) no-repeat scroll 0 0 transparent;">
	  	
	  	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  	 <tr>
	  	  <td width="10">
	  	  &nbsp;
	  	  </td>
	  	  <td align="right" width="1038" style="background: url(http://i.oldbk.com/news/images/bg_inf-y_png.png) repeat-y scroll left top transparent">
	  	  <!-- content -->

	  	  <?
		$query = mysql_query("SELECT * FROM news WHERE parent = 1 order by id desc LIMIT {$limit} ");
		while($row = mysql_fetch_assoc($query))
		{
			//render_news ($row[topic],$row[text],$row[date]);
			if (!($ico))
		 	{
		 		$ico='<img src=http://oldbk.com/images/news_icon.png>';
		 	}
			$dat=explode(' ',$row['date']);
			$dat=$dat[0];
	  	  
	  	  	?>
	  	  	
		  	  	   <table cellpadding="0" cellspacing="0" width="100%">
		  	  	    <tr>
			  	  	     <td width="75" height="22" style="background: url(http://i.oldbk.com/news/images/cb-left-top.png) no-repeat scroll right top transparent">
			  	  	   	&nbsp;
			  	  	     </td>
			  	  	     <td height="22" style="background: url(http://i.oldbk.com/news/images/cb-top.png) repeat-x scroll left top transparent">
			  	  	  	&nbsp;
			  	  	     </td>
			  	  	     <td width="95" height="22" style="background: url(http://i.oldbk.com/news/images/cb-top-right.png) no-repeat scroll left top transparent">
			  	  	  	&nbsp;
			  	  	     </td>
		  	  	    </tr>
		  	  	    <tr>
			  	  	     <td align="right" height="42" style="background: url(http://i.oldbk.com/news/images/article_left.png) no-repeat scroll right top transparent">
			  	  	      <?=$ico?>&nbsp;&nbsp;&nbsp;&nbsp;
			  	  	     </td>
			  	  	     <td height="42" style="background: url(http://i.oldbk.com/news/images/article_center.jpg) repeat-x scroll left top transparent">
						
				  	  	   <table cellpadding="0" cellspacing="0" width="100%">
				  	  	   <tr>
				  	  	   	<td> 
							<div align=left><font style="color: #911717; font-size: 20px;"><b><?=$row[topic]?></b></font></div>				  	  	   	
				  	  	   	</td>
				  	  	   	<td>
							<?
			  		  	     echo "<div align=right><b>$dat</b></div>";
							?>				  	  	   	
				  	  	   	</td>
				  	  	   </tr>
				  	  	   </table>
					
			  	  	     </td>
			  	  	     <td style="background: url(http://i.oldbk.com/news/images/article_right.png) no-repeat scroll left top transparent">
			  	  	      &nbsp;
			  	  	     </td>
		  	  	    </tr>
		  	  	    <tr>
			  	  	     <td style="background: url(http://i.oldbk.com/news/images/cb-left.png) repeat-y scroll right top transparent">
			  	  	      &nbsp;	
			  	  	     </td>
			  	  	     <td style="background-color: #E4DCBE;" align="left">
			  	  	     <? 
			  	  	  //   echo "<div align=right><b>$dat</b></div>";
			  	  	     echo $row[text]; 
			  	  	     ?>
			  	  	     </td>
			  	  	     <td style="background: url(http://i.oldbk.com/news/images/cb-right.png) repeat-y scroll left top transparent">
			  	  	     	&nbsp;
			  	  	     </td>
			  	  	    </tr>
		  	  	    <tr>
			  	  	     <td height="22" style="background: url(http://i.oldbk.com/news/images/cb-bottom-left.png) repeat-y scroll right top transparent">
			  	  	      &nbsp;
			  	  	     </td>
			  	  	     <td style="background: url(http://i.oldbk.com/news/images/cb-bottom.png) repeat-x scroll left top transparent">
			  	  	      &nbsp;
			  	  	     </td>
			  	  	     <td style="background: url(http://i.oldbk.com/news/images/cb-bottom-right.png) no-repeat scroll left top transparent">
			  	  	      &nbsp;
			  	  	     </td>
		  	  	    </tr>
		  	  	    
		  	  	    
	  	  	   	   </table>
	  	  	
	  	  	</br>
	  	  <?	  	  	   
		}	  	  	   
	  	  ?> 
			<div style="height: 80px; background: url(http://i.oldbk.com/news/images/deamin_top_back_6.png) no-repeat scroll right top transparent"></div>
	  	  </td>
	  	  <td width="10">
	  	  &nbsp;
	  	  </td>
	  	 </tr>
	  	 
	  	</table>
	  </td>
	  <td>
	   &nbsp;
	  </td>
	 </tr>
	 <tr>
<!--	  <td align="left" valign="middle" colspan="3" height=162 background="http://i.oldbk.com/news/images/statistic_bar_back.jpg">-->
	<td width="10"></td><td align="left" valign="middle" style="background: url('http://oldbk.com/i/statistic_bar_back.jpg') no-repeat 11px 0px; background-repeat: no-repeat;" height="162">
	     <a style="-moz-text-blink: none;
	     float:left; margin-left:300px;
	    -moz-text-decoration-color: -moz-use-text-color;
	    -moz-text-decoration-line: none;
	    -moz-text-decoration-style: solid;
	    color: #911717;font-size: 20px;
	    font-weight: bold;" target="_blank" href="http://oldbk.com">"Бойцовский Клуб - ОлдБК"</a>
	  </td><td></td>
	 </tr>
	</table>
<center>*Вы получили это письмо в соответствии с правилами о предоставлении игрового сервиса.</center>
  </td>
 </tr>
</table>



</body>
</html>
