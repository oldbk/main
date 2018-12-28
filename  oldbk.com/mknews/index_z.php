<!DOCTYPE html>
<html lang="ru" xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <meta charset="windows-1251">
	<meta name='yandex-verification' content='60ef46abc2646a77' />
        <meta name="keywords" content="oldcombats, Бойцовский клуб, БК, комбатс, онлайн, игры, игра, online, on-line, games, combats, mmorpg игры бесплатно, бесплатная, ролевая, ролевые, бесплатные, браузерная рпг, RPG " />
        <meta name="description" content="Новая бесплатная многопользовательская MMORPG combats онлайн игра «Старый Бойцовский Клуб - ОлдБК». Стань участником Бойцовского Клуба Комбатс!"/>
	<meta name="robots" content="index, follow"/>
	<base href="http://oldbk.com/">
	<meta name="author" content="oldbk.com"/>
	<?php
		include "/../connect.php";
		$limit=(int)($_GET[lim]);
		if ($limit == 0) {
			$limit=3;
		}
	?>
	<title>OldBK</title>
<style>
table {
	border:0;padding: 0;border-collapse: collapse;border-spacing: 0;
}
td    {padding: 0px;}
img { border: 0; }
</style>
</head>
<body>
<table style="width:100%;text-align:center;">
 <tr>
  <td style="width:100%;text-align:center;">
	<table style="text-align:center;margin-left:auto;margin-right:auto;">
	 <tr>
	  <td style="width:50px;">
	  </td>
	  <td style="width:1100px;height:236px; background-image: url(http://i.oldbk.com/news/images/header_back.jpg);">
	 	<a href=http://oldbk.com><img width="1099" height="230" src="http://i.oldbk.com/i/.gif" alt=""></a>
	  </td>
	  <td style="width:50px;height:0px;">
	  </td>
	 </tr>
	 <tr>
	  <td style="height:0px;">
	  </td>
	  <td style="height:143px;background-image: url(http://i.oldbk.com/news/images/menu_reg_back.jpg);">
	  </td>
	  <td style="height:0px;">
	  </td>
	 </tr>
	 <tr>
	  <td>
	  </td>
	  <td style="width:1058px;height:800px;vertical-align:top;background: url(http://i.oldbk.com/news/images/container_back.jpg) no-repeat scroll 0 0 transparent;">
	  	<table style="width:100%;">
	  	 <tr>
	  	  <td style="width:10px;">
	  	  </td>
	  	  <td style="width:1038px;text-align:right;background: url(http://i.oldbk.com/news/images/bg_inf-y_png.png) repeat-y scroll left top transparent">
	  	  <!-- content -->

	  	  <?
		$query = mysql_query("SELECT * FROM news WHERE parent = 1 order by id desc LIMIT {$limit} ");
		while($row = mysql_fetch_assoc($query))
		{
			//render_news ($row[topic],$row[text],$row[date]);
			if (!($ico))
		 	{
		 		$ico='<img alt="" src=http://oldbk.com/images/news_icon.png>';
		 	}
			$dat=explode(' ',$row['date']);
			$dat=$dat[0];
	  	  
	  	  	?>
	  	  	
		  	  	   <table style="width:100%;">
		  	  	    <tr>
			  	  	     <td style="width:75px;height:22px;background: url(http://i.oldbk.com/news/images/cb-left-top.png) no-repeat scroll right top transparent">
			  	  	   	&nbsp;
			  	  	     </td>
			  	  	     <td style="height:22px;background: url(http://i.oldbk.com/news/images/cb-top.png) repeat-x scroll left top transparent">
			  	  	  	&nbsp;
			  	  	     </td>
			  	  	     <td style="width:95px;height:22px;background: url(http://i.oldbk.com/news/images/cb-top-right.png) no-repeat scroll left top transparent">
			  	  	  	&nbsp;
			  	  	     </td>
		  	  	    </tr>
		  	  	    <tr>
			  	  	     <td style="height:42px;text-align:right;background: url(http://i.oldbk.com/news/images/article_left.png) no-repeat scroll right top transparent">
			  	  	      <?=$ico?>&nbsp;&nbsp;&nbsp;&nbsp;
			  	  	     </td>
			  	  	     <td style="height:42px;background: url(http://i.oldbk.com/news/images/article_center.jpg) repeat-x scroll left top transparent">
						
				  	  	   <table style="width:100%;">
				  	  	   <tr>
				  	  	   	<td> 
							<div style="text-align:left;"><span style="color: #911717; font-size: 20px;"><b><?=$row[topic]?></b></span></div>
				  	  	   	</td>
				  	  	   	<td>
							<?
			  		  	     echo "<div style=\"text-align:right;\"><b>$dat</b></div>";
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
			  	  	     <td style="background-color: #E4DCBE;text-align:left;">
			  	  	     <? 
			  	  	        $text = $row['text'];
						$text = preg_replace('~<img[\ ]{1,}src=~iU', '<img alt="" src=', $text);
						$text = str_replace('border=0', '', $text);
						$text = str_replace('border="0"', '', $text);
						$text = preg_replace('~<font color="#850404">(.*)</font>~iU','<span style="color:#850404;margin:0px;padding:0px;">\\1</span>', $text);

			  	  	     	echo $text; 
			  	  	     ?>
			  	  	     </td>
			  	  	     <td style="background: url(http://i.oldbk.com/news/images/cb-right.png) repeat-y scroll left top transparent">
			  	  	     	&nbsp;
			  	  	     </td>
			  	  	    </tr>
		  	  	    <tr>
			  	  	     <td style="height:22px;background: url(http://i.oldbk.com/news/images/cb-bottom-left.png) repeat-y scroll right top transparent">
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
	  	  	
	  	  	<br />
	  	  <?	  	  	   
		}	  	  	   
	  	  ?> 
			<div style="height: 78px; background: url(http://i.oldbk.com/news/images/deamin_top_back_6.png) no-repeat scroll right top transparent"></div>
	  	  </td>
	  	  <td style="width:10px;">
	  	  </td>
	  	 </tr>
	  	 
	  	</table>
	  </td>
	  <td>
	  </td>
	 </tr>
	 <tr>
	  <td colspan=3 style="height:162px;text-align:left;vertical-align:middle;background: url(http://i.oldbk.com/news/images/statistic_bar_back.jpg);background-position: left 5px center;">
	     <a style="-moz-text-blink: none;
	     float:left; margin-left:300px;
	    -moz-text-decoration-color: -moz-use-text-color;
	    -moz-text-decoration-line: none;
	    -moz-text-decoration-style: solid;
	    color: #911717;font-size: 20px;
	    font-weight: bold;" target="_blank" href="http://oldbk.com">"Бойцовский Клуб - ОлдБК"</a>
	  </td>
	 </tr>
	</table>
  </td>
 </tr>
</table>



</body>
</html>
