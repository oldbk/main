<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="windows-1251">

<Title>«Онлайн (online) MMORPG (мморпг) игра 2010-2012: играть бесплатно без регистрации в браузерную онлайн mmorpg игру онлайн».</Title>
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/news/css/style.css"/>
	<meta name='yandex-verification' content='60ef46abc2646a77' />
        <meta name="keywords" content="oldcombats, Бойцовский клуб, БК, комбатс, онлайн, игры, игра, online, on-line, games, combats, mmorpg игры бесплатно, бесплатная, ролевая, ролевые, бесплатные, браузерная рпг, RPG " />
        <meta name="description" content="Новая бесплатная многопользовательская MMORPG combats онлайн игра «Старый Бойцовский Клуб - ОлдБК». Стань участником Бойцовского Клуба Комбатс!"/>
	<meta name="robots" content="index, follow"/>
	<base href="http://oldbk.com/">
	<meta name="author" content="oldbk.com"/>
</head>
<body>

<div id="wrapper">
    <div id="left_outer">
        <div id="centre">
            <div id="div">
                <div id="auth">

                </div>

                <div id="knight_girl">
                    
                </div>
            </div>
            <div id="menu_reg">

               
            </div>

            <div id="container">
                <div id="content">

                    <div class="left">
                        <div class="content">
                            <div class="fullyContent">

<?
	include "/../connect.php";
	$limit=(int)($_GET[lim]);
	if ($limit == 0)
	{
	$limit=5;
	}
function render_news ($title,$text,$dat=null,$ico=null)
{
 if (!($ico))
 	{
 	$ico='<img src=/images/news_icon.png>';
 	}
echo '
 <article class="news_container">';
 				if ($title)
 				{
                                 echo '  <h1 class="news"><span class="type">'.$ico.'</span>'.$title.'</h1>';
                                  if ($dat)
                                  	{
                                  	$dat=explode(' ',$dat);
                                  	$dat=$dat[0];
                                  	echo "<div align=right ><b>$dat</b></div> ";
                                  	}
                                // echo '</h1>'; 	
                                 }   
				echo $text.'
                                    <div class="cb_bottom"></div>
                                    <div class="cb_top"></div>
                                </article>
                                <br>
                                 ';
}

					 $query = mysql_query("SELECT * FROM news WHERE parent = 1 order by id desc LIMIT {$limit} ");
							while($row = mysql_fetch_assoc($query))
							{
    								render_news ($row[topic],$row[text],$row[date]);
							}
?>



                                
                            </div>
                        </div>
                    </div>
                    <div class="right">
                        
                    </div>
                </div>
            </div>
            <div id="footer">
                <div class="statictic_bar">
                <br>                <br>                <br> <br>
		<img src="/images/18rec_b.jpg" style="float:left; margin-left:200px;">		
		</div>
                <div class="game_info">
                    <div class="content">
                        
                </div>
            </div>
        </div>

    </div>
    <div id="right_outer">
        <div id="right"></div>
    </div>
</div>


</body>
</html>
