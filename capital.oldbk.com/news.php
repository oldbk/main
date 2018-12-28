<?php
session_start();
$google = 1;
require_once 'news_connect.php';
//mysql_query("SET NAMES utf8");
//ini_set('default_charset','utf8');
require_once 'lib/class.User.php';



$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));


$user = User::getUser($_SESSION['uid']);

$perpage = 20;

function newsEntryHTML($row, $disabledComments = false) {
	$additional = $disabledComments ? "style=\"display: none\"" : 0;
	$deleteButton = deleteButton($_SERVER['SCRIPT_NAME'] . '?act=admin&type=deleteEntry&id=' . $row['id']);
	$editButton = '<a href="?act=admin&type=editEntry&id='.$row['id'].'"> Р </a>';
	$notparent = "";
	if ($row['parent'] == 0) $notparent = ' (не включена)';
return <<<EOF
    <div class="item">
        <div class="title"><h1>{$row['topic']}{$notparent}</h1><div class="date">{$row['date']} {$deleteButton} {$editButton}</div></div>
        <div class="news_text">{$row['text']}</div>
		<div class="comm" {$additional}>Комментариев: [{$row['comments']}] <a href="?topic={$row['id']}">Оставить комментарий</a></div>
        <div class="clear"></div>
    </div>
EOF;
}


function showCommentsForm($id) {
return <<<EOF
    <div><form action="?topic={$id}" method="POST" style="padding: 0px; margin: 0px;">
        <textarea name="comment" style="width: 100%; height: 150px;"></textarea>
		<div align="right"><input style="width: 30%; border: 1px #585857 solid; background:#666866; color: #fff" type="submit" name="add_comment" value=" Добавить комментарий " /></div>
    </form></div>
EOF;

}

/*
function showCommentsForm($id) {
return <<<EOF
    <div>
    <iframe  src="http://capitalcity.oldbk.com/news_api_add_coment.php?topic={$id}" width="100%" height="175" scrolling="no" frameborder="0" style="padding: 0px; margin: 0px;" /></iframe>
</div>
EOF;

}
*/


function deleteButton($link) {
    global $user;
    return $user && $user->isAuth() && $user->isAdmin() ? "<a href=\"" . $link . "\"><img src=\"http://i.oldbk.com/i/clear.gif\" border=\"0\" /></a>" : "";
}

function commentsEntryHTML($row, $page) {
    global $user;
    $deleteButton = deleteButton($_SERVER['SCRIPT_NAME'] . '?act=admin&type=deleteComment&id=' . $row['id'] . '&topic=' . $row['parent'] . '&p=' . $page);
	$commentButton = $user && $user->isAuth() && $user->isAdmin() ? "<a href=\"javascript:doComment(" . $row['id'] . ", " . $page . ");\">Комментировать</a>" : "";

	$comments = "";
foreach($row['comments'] as $comment) {
    $deleteAnswerButton = deleteButton($_SERVER['SCRIPT_NAME'] . '?act=admin&type=deleteAnswer&id=' . $comment['id'] . '&topic=' . $row['parent'] . '&p=' . $page);
$comments .= <<<EOF
<div style="color: red;">{$deleteAnswerButton} {$comment['text']}</div>
EOF;
}

return <<<EOF
    <div class="item">
        <div class="titleauthor">{$row['author']} {$commentButton}<div class="date">{$row['date']}{$deleteButton}</div></div>
        <div class="news_text">{$row['text']} {$comments}</div>
        <div class="clear"></div>
    </div>
EOF;
}

function showPages($currentPage, $countPages, $link) {
    $html = "";
    for($i = 1; $i <= $countPages; $i++) {
	    if($i == $currentPage) {
		    $html .= sprintf("<a href=\"%s%d\" class=\"actived\">%d</a> ", $link, $i, $i);
		} else {
		    $html .= sprintf("<a href=\"%s%d\">%d</a> ", $link, $i, $i);
		}
	}

	return $html;
}

function showAdminAddForm($id = 0) {
    	global $user;
    	$error = array();

	if ($id > 0 && !isset($_REQUEST['edit_entry'])) $editn = mysql_fetch_assoc(mysql_query('SELECT * FROM news WHERE id = '.intval($id)));

	if(isset($_REQUEST['edit_entry'])) {
	    	if(!isset($_REQUEST['title']) || empty($_REQUEST['title'])) {
		    $error[] = "Вы не ввели заголовок новости";
		} 
		if(!isset($_REQUEST['body']) || empty($_REQUEST['body'])) {
		    $error[] = "Вы не ввели текст новости";
		}

		if (!$user->isAdmin())
			{
			 $_REQUEST['title'] = str_ireplace("<", "&lt;",$_REQUEST['title']);
			 $_REQUEST['title'] = str_ireplace(">", "&gt;", $_REQUEST['title']);
			 $_REQUEST['body'] = str_ireplace("<", "&lt;",$_REQUEST['title']);
			 $_REQUEST['body'] = str_ireplace(">", "&gt;", $_REQUEST['title']);
			}
		if(empty($error)) {
		    	mysql_query("UPDATE news SET parent = ".intval($_REQUEST['parent']).", `topic` = '".$_REQUEST['title']."', `text` = '" .$_REQUEST['body']."', `date` =  '".$_REQUEST['print_date']."' WHERE id = ".intval($id));
			
			header("location: " . $_SERVER['SCRIPT_NAME']);
			exit;
		}
	}

	if(isset($_REQUEST['add_entry'])) {
	    	if(!isset($_REQUEST['title']) || empty($_REQUEST['title'])) {
		    $error[] = "Вы не ввели заголовок новости";
		} 
		if(!isset($_REQUEST['body']) || empty($_REQUEST['body'])) {
		    $error[] = "Вы не ввели текст новости";
		}

		if (!$user->isAdmin())
			{
			 $_REQUEST['title'] = str_ireplace("<", "&lt;",$_REQUEST['title']);
			 $_REQUEST['title'] = str_ireplace(">", "&gt;", $_REQUEST['title']);
			 $_REQUEST['body'] = str_ireplace("<", "&lt;",$_REQUEST['title']);
			 $_REQUEST['body'] = str_ireplace(">", "&gt;", $_REQUEST['title']);
			}
		if(empty($error)) {
		
			if ((strlen($_REQUEST['print_date'])==19))
				{
				$print_time=$_REQUEST['print_date'];				
				}
				else
				{
				$print_time='NULL';
				}

			

		    mysql_query("INSERT INTO news (`parent`, `topic`, `text`, `date`, `author`, `print_time`) VALUES (".intval($_REQUEST['parent']).", '".$_REQUEST['title']."', '" .$_REQUEST['body'] . "', '" . date("d.m.y H:i:s") . "', '" . $user->drawLogin() . "' , '{$print_time}' )  ") or die();
			
			header("location: " . $_SERVER['SCRIPT_NAME']);
			exit;
		}
	}
	$errorHTML = implode("<br />", $error);

	if ($editn) {
		$_REQUEST['title'] = $editn['topic'];
		$_REQUEST['print_date'] = $editn['date'];
		$_REQUEST['parent'] = $editn['parent'];
		$_REQUEST['body'] = htmlspecialchars($editn['text'],ENT_QUOTES);
		$sbutton = 'name="edit_entry" value=" Редактировать новость " />';
	} else {
		$sbutton = 'name="add_entry" value=" Добавить новость " />';
	}                   

	if (isset($_REQUEST['edit_entry'])) {
		$sbutton = 'name="edit_entry" value=" Редактировать новость " />';
	}

	$addid = '';
	if ($id > 0) $addid = '&id='.$id;
	if (empty($_REQUEST['print_date'])) $_REQUEST['print_date'] = date("d.m.y 00:00");
	if ($_REQUEST['parent'] == "") $_REQUEST['parent'] = 0;

return <<<EOF
	<script type="text/javascript" src="/ajax/jquery/jquery.js"></script>
	<!--script type="text/javascript" src="/ajax/wymeditor/jquery.wymeditor.js"></script>
	<script type="text/javascript">
	jQuery(function() {
	jQuery('.wymeditor').wymeditor();
	});
	</script-->

    <div class="pagination" style="text-align: center"><a href="{$_SERVER['SCRIPT_NAME']}">Вернуться к новостям</a></div>
    {$errorHTML}
    <div><form action="?act=admin&type=add{$addid}" method="POST" style="padding: 0px; margin: 0px;">
	 Заголовок: 
	 <br /><input type="text" name="title" value="{$_REQUEST['title']}" />
	 <br />Дата публикации: 
	 <br /><input type="text" name="print_date" value="{$_REQUEST['print_date']}"  id="calendar-inputField1"  />

	 <br />Парент: 
	 <br /><input type="text" name="parent" value="{$_REQUEST['parent']}" />

	 
			<script>
			Calendar.setup({
		        trigger    : "calendar-trigger1",
		        inputField : "calendar-inputField1",
			dateFormat : "%Y.%m.%d 00:00:00",
			onSelect   : function() { this.hide() }
		    			});
			document.getElementById('calendar-trigger1').setAttribute("type","BUTTON");
			</script>
	 
	 <br />Текст новости:
         <br /><textarea class="wymeditor" name="body" style="width: 790px; height: 150px;">{$_REQUEST['body']}</textarea>
 	 <div align="right"><input style="width: 30%; border: 1px #585857 solid; background: #666866; color: #fff" type="submit" class="wymupdate" {$sbutton}</div>
    </form></div><br><br>
	<font color="red" size=5>ВСЕГДА ПРОВЕРЯТЬ НОВОСТЬ ПОСЛЕ ДОБАВЛЕНИЯ ИЛИ ИЗМЕНЕНИЯ И ПОЯВЛЕНИЯ ЕЁ ПОСЛЕ КЕША! <a href="http://pr-cy.ru/open/4/domain/oldbk.com/" target="_blank">ПРОВЕРКА!</a></font>
	<br><br>
	<pre>
		&lt;div align=center&gt; -> &lt;div style="text-align:center;"&gt;
		В ссылках в href амперсанд & -> &amp;amp;
		В IMG alt="" не ставить!
		&lt;td style="vertical-align:top;text-align:left;"&gt;
		&lt;div style="color:red;"&gt;
		&lt;table border=0  style="text-align:center;"&gt;
	</pre>
EOF;

}
//     <br /><textarea name="body" style="width: 790px; height: 150px;">{$_REQUEST['body']}</textarea>
//     <br /><textarea class="wymeditor" name="body" style="width: 790px; height: 150px;">{$_REQUEST['body']}</textarea>

function deleteEntry($id) {
    mysql_query("DELETE FROM news WHERE id = " . intval($id) . " OR parent = " . intval($id));

	header("location: " . $_SERVER['SCRIPT_NAME']);
	exit;
}

function deleteComment($id, $topic, $p) {
    global $user;
    $text = "<span style=\"color: red;\">Удалено ангелом " . $user->drawLogin() . "</span>";
    mysql_query("UPDATE news SET text = '".mysql_escape_string($text)."' WHERE id = " . intval($id));

	header("location: " . $_SERVER['SCRIPT_NAME'] . '?topic=' . $topic . '&p=' . $p);
	exit;
}

function deleteAnswer($id, $topic, $p) {
    global $user;

    mysql_query("DELETE FROM news WHERE id = " . intval($id));

	header("location: " . $_SERVER['SCRIPT_NAME'] . '?topic=' . $topic . '&p=' . $p);
	exit;
}

function answerComment($id, $comment, $p) {
    global $user;
    $query = mysql_query("SELECT * FROM news WHERE id = " . intval($id));
	if(($row = mysql_fetch_assoc($query)) !== false) {
        mysql_query("INSERT INTO news (`parent`, `text`, `date`, `author`) VALUES (" . $row['id'] . ", '" . $comment . "', '" . date("d.m.y H:i:s") . "', '" . $user->drawLogin() . "')") or die();

		header("location: " . $_SERVER['SCRIPT_NAME'] . "?topic=" . $row['parent'] . "&p=" . $p);
		exit;
	}
}

function showAdminContent() {
global $user;
    switch(@$_REQUEST['type']) {
	    case 'add':
		$rr=showAdminAddForm(@$_REQUEST['id']);
		return $rr;
		break;
		case 'deleteEntry':
		    deleteEntry(@$_REQUEST['id']);
		break;
		case 'deleteAnswer':
		    deleteAnswer(@$_REQUEST['id'], @$_REQUEST['topic'], @$_REQUEST['p']);
		break;
		case 'deleteComment':
		    deleteComment(@$_REQUEST['id'], @$_REQUEST['topic'], @$_REQUEST['p']);
		break;
		case 'answer':
		    answerComment(@$_REQUEST['id'], @$_REQUEST['comment'], @$_REQUEST['p']);
		break;
		case 'editEntry':
			$rr=showAdminAddForm(@$_REQUEST['id']);
			return $rr;
		break;
		default:
		    header("location: " . $_SERVER['SCRIPT_NAME']);
			exit;
		break;
	}
}

function showContent() {
    global $perpage, $user, $errmsg;
    $ret=array();
	if(isset($_REQUEST['act']) && $_REQUEST['act'] == "admin" && $user && $user->isAuth() && $user->isAdmin()) 
	{
	    return showAdminContent();
	}
    $html = '<div align=right><a href="http://oldbk.com/rss.php" target="_blank"><img src="http://i.oldbk.com/i/iconrss_s.png" alt="RSS-Лента новостей" title="RSS-Лента новостей"></a></div>';
    if(isset($_REQUEST['topic'])) {
	    //show 1 entry
		$query = mysql_query("SELECT * FROM news WHERE id = " . intval($_REQUEST['topic'])." and parent=1 and (ISNULL(print_time) OR print_time<=NOW() )");
		$data = array();
		if(($entry = mysql_fetch_assoc($query)) !== false) {

			$query      = mysql_query("SELECT count(*) AS count FROM news WhERE parent = ".intval($_REQUEST['topic'])."");
		    $count      = mysql_fetch_assoc($query);
		    $countPages = max(1, ceil($count['count'] / $perpage));
            		$page       = getCurrentPage($countPages);
		    $pages      = showPages($page, $countPages, $_SERVER['SCRIPT_NAME'] . '?topic=' . intval($_REQUEST['topic']) . '&p=');

		    $html .= "<div class=\"news\">";
		    $html .= "<div class=\"pagination\" style=\"text-align: center\"><a href=\"{$_SERVER['SCRIPT_NAME']}\">Вернуться к новостям</a></div>";
            $html .= newsEntryHTML($entry, true);
            $ret['title']=$entry['topic'];
//echo "|".$user['align']."|";


			if ($user['align']<2 or $user['align']>3)
				{
				 $_REQUEST['comment'] = str_ireplace("<", "&lt;",$_REQUEST['comment']);
				 $_REQUEST['comment'] = str_ireplace(">", "&gt;", $_REQUEST['comment']);
				}

			if(isset($_REQUEST['add_comment']) && $user && $user->isAuth()) {

				if ($user[level] >5)
				 {
				 mysql_query("INSERT INTO news (`parent`, `text`, `date`, `author`) VALUES (".$entry['id'].", '" . nl2br($_REQUEST['comment']) . "', '" . date("d.m.y H:i:s") . "', '" . $user->drawLogin() . "')");
				 header("location: " . $_SERVER['SCRIPT_NAME'] . '?topic=' . $entry['id'] . '&p=' . ceil(($count['count'] + 1) / $perpage));
				 exit;
				 }
				 else
				 {
				 $errmsg="<font color=red><b>Комментировать могут персонажи больше 5-го уровня!</b></font>";
				 }
			}

		    $query = mysql_query("SELECT *
		                          FROM news
				    			  WHERE parent = ".intval($_REQUEST['topic'])."
					    		  ORDER BY id
						    	  LIMIT " . ($perpage * $page - $perpage) . ", " . $perpage) or die();
			$comments = array();
			$ids = array();
		    while($row = mysql_fetch_assoc($query)) {
			    $row['comments'] = array();
			    $comments[$row['id']] = $row;
				$ids[] = $row['id'];
		    }

			$query = mysql_query("SELECT * FROM news WHERE parent > 0 AND parent IN ('" . implode("', '", $ids) .  "') ORDER BY id");
			while($row = mysql_fetch_assoc($query)) {
			    $comments[$row['parent']]['comments'][] = $row;
			}

			$commentsHTML = "";
			foreach($comments as $k => $v) {
			    $commentsHTML .= commentsEntryHTML($v, $page);
			}

		    if($commentsHTML != "") {
		        $html .= "<div class=\"pagination\">Страницы:  {$pages}</div>{$commentsHTML}<div class=\"pagination\">Страницы:  {$pages}</div>";
		    }

			if($user && $user->isAuth()) {
			
			   if ($user[level]>5)
			    {
			    $html .= showCommentsForm($entry['id']);
			    }
			    else
			    {
			    $html .= "<font color=red>Комментировать могут персонажи больше 5-го уровня!</font>";
			    }
			}
			$html .= "</div>";
		} else {
		    //not fount
		     $html .= "<div align=center><font color=red>Такая новость не найдена!</font></div>";
		}
	} else {
	    //show page news;
		$query      = mysql_query("SELECT count(*) AS count FROM news WHERE parent = 1 and (ISNULL(print_time) OR print_time<=NOW() )  ");
		$count      = mysql_fetch_assoc($query);
		$countPages = max(1, ceil($count['count'] / $perpage));
        $page       = getCurrentPage($countPages);
		$pages      = showPages($page, $countPages, $_SERVER['SCRIPT_NAME'] . '?p=');
        $ret['title']='';

		if($user && $user->isAuth() && $user->isAdmin()) {
			$query = mysql_query("SELECT n. * , (SELECT COUNT( * ) FROM news AS c WHERE c.parent = n.id) AS comments
                              FROM `news` AS n
                              WHERE (n.parent = 1 or n.parent = 0) AND (ISNULL(n.print_time) OR n.print_time<=NOW() )
							  ORDER BY id DESC
                              LIMIT ".($perpage * $page - $perpage)." , ".$perpage."");
		} else {
			$query = mysql_query("SELECT n. * , (SELECT COUNT( * ) FROM news AS c WHERE c.parent = n.id) AS comments
                              FROM `news` AS n
                              WHERE n.parent = 1 AND (ISNULL(n.print_time) OR n.print_time<=NOW() )
							  ORDER BY id DESC
                              LIMIT ".($perpage * $page - $perpage)." , ".$perpage."");
		}
        $html .= "<div class=\"pagination\">Страницы:  {$pages}</div>";

		if($user && $user->isAuth() && $user->isAdmin()) {
			$html .= "<div class=\"pagination\" style=\"text-align: center\"><a href=\"" . $_SERVER['SCRIPT_NAME'] . "?act=admin&type=add\">Добавить новость!</a></div>";
		}
		$html .= "<div class=\"news\">";
		while($row = mysql_fetch_assoc($query)) {
		    $html .= newsEntryHTML($row);
		}
		$html .= "</div>";
		$html .= "<div class=\"pagination\">Страницы:  {$pages}</div>";
	}

	$ret[0]=$html;
	return $ret;
}

function getCurrentPage($countPages) {
    return min($countPages, max(1, isset($_REQUEST['p']) ? intval($_REQUEST['p']) : 1));
}

$content = showContent();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>«<?=($content['title']!=''?$content['title'].' - ':'')?>Легендарный бойцовский клуб ОЛДБК - Oldbk.com».</title>
<meta name="keywords" content="бойцовский клуб, бк, онлайн игра, rpg, магия бой, игра фэнтези, fantasy, маг " />
<meta name="description" content="Бойцовский клуб - rpg онлайн игра, он же БК, созданный в 2003 году. Борьба Тьмы и Света. Бои, магия, персонажи - всё это Бойцовский клуб ">
<meta name="robots" content="index, follow"/>
<meta name="author" content="oldbk.com">
<link rel="apple-touch-icon-precomposed" sizes="512x512" href="http://i.oldbk.com/i/icon/oldbk_512x512.png" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="http://i.oldbk.com/i/icon/oldbk_144x144.png" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="http://i.oldbk.com/i/icon/oldbk_114x114.png" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="http://i.oldbk.com/i/icon/oldbk_72x72.png" />
<link rel="apple-touch-icon-precomposed" sizes="58x58" href="http://i.oldbk.com/i/icon/oldbk_58x58.png" />
<link rel="apple-touch-icon-precomposed" sizes="48x48" href="http://i.oldbk.com/i/icon/oldbk_48x48.png" />
<link rel="apple-touch-icon-precomposed" sizes="29x29" href="http://i.oldbk.com/i/icon/oldbk_29x29.png" />
<link rel="apple-touch-icon-precomposed" href="http://i.oldbk.com/i/icon/oldbk_57x57.png" />
<link rel="stylesheet" href="http://oldbk.com/inews/styles.css" type="text/css" media="screen"/>
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/steel/steel.css" />
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/jscal2.js"></script>
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/lang/ru2.js"></script>
<?php
if($user && $user->isAuth() && $user->isAdmin()) {
?>
<script type="text/javascript">
function doComment(id, p) {
    var comment = prompt("Введите комментарий", "");
	if(comment) {
	    location.href = '/news.php?act=admin&type=answer&id=' + id + '&comment=' + comment + '&p=' + p;
	} else {
	    alert('Ну и пожалуйста');
	}
}
</script>
<?php
}
?>

<!-- Asynchronous Tracking GA top piece counter -->
<script type="text/javascript">

var _gaq = _gaq || [];

var rsrc = /mgd_src=(\d+)/ig.exec(document.URL);
    if(rsrc != null) {
        _gaq.push(['_setCustomVar', 1, 'mgd_src', rsrc[1], 2]);
    }

_gaq.push(['_setAccount', 'UA-17715832-1']);
_gaq.push(['_addOrganic', 'm.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'images.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'blogs.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'video.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'go.mail.ru', 'q']);
_gaq.push(['_addOrganic', 'm.go.mail.ru', 'q', true]);
_gaq.push(['_addOrganic', 'mail.ru', 'q']);
_gaq.push(['_addOrganic', 'google.com.ua', 'q']);
_gaq.push(['_addOrganic', 'images.google.ru', 'q', true]);
_gaq.push(['_addOrganic', 'maps.google.ru', 'q', true]);
_gaq.push(['_addOrganic', 'nova.rambler.ru', 'query']);
_gaq.push(['_addOrganic', 'm.rambler.ru', 'query', true]);
_gaq.push(['_addOrganic', 'gogo.ru', 'q']);
_gaq.push(['_addOrganic', 'nigma.ru', 's']);
_gaq.push(['_addOrganic', 'search.qip.ru', 'query']);
_gaq.push(['_addOrganic', 'webalta.ru', 'q']);
_gaq.push(['_addOrganic', 'sm.aport.ru', 'r']);
_gaq.push(['_addOrganic', 'akavita.by', 'z']);
_gaq.push(['_addOrganic', 'meta.ua', 'q']);
_gaq.push(['_addOrganic', 'search.bigmir.net', 'z']);
_gaq.push(['_addOrganic', 'search.tut.by', 'query']);
_gaq.push(['_addOrganic', 'all.by', 'query']);
_gaq.push(['_addOrganic', 'search.i.ua', 'q']);
_gaq.push(['_addOrganic', 'index.online.ua', 'q']);
_gaq.push(['_addOrganic', 'web20.a.ua', 'query']);
_gaq.push(['_addOrganic', 'search.ukr.net', 'search_query']);
_gaq.push(['_addOrganic', 'search.com.ua', 'q']);
_gaq.push(['_addOrganic', 'search.ua', 'q']);
_gaq.push(['_addOrganic', 'poisk.ru', 'text']);
_gaq.push(['_addOrganic', 'go.km.ru', 'sq']);
_gaq.push(['_addOrganic', 'liveinternet.ru', 'ask']);
_gaq.push(['_addOrganic', 'gde.ru', 'keywords']);
_gaq.push(['_addOrganic', 'affiliates.quintura.com', 'request']);
_gaq.push(['_trackPageview']);
_gaq.push(['_trackPageLoadTime']);
</script>
<!-- Asynchronous Tracking GA top piece end -->
<!-- Asynchronous Tracking GA top piece end --> 
  <!--[if lt IE 9]>
   <script>document.createElement('figure');</script>
  <![endif]-->
</head>

<body leftmargin=0 rightmargin=0 bottommargin=0 topmargin=0 marginwidth=0 marginheight=0>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="1065" valign="top" class="leftY"><table width="100%" height="270" border="0" cellpadding="0" cellspacing="0" class="headLeft">
<tr>
<td height="270">&nbsp;</td>
</tr>
</table></td>
    <td width="1018" valign="top" background="http://oldbk.com/inews/main_bg.jpg"><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td height="263" class="header">&nbsp;</td>
</tr>
<tr>
<td height="700" valign="top" class="cont_cracks">

<br><?=$errmsg;?><center>
	<table  width="90%" border="0" cellspacing="0" cellpadding="0">
	<tr><td>
        <? if (is_array($content)) { echo $content[0] ; } else { echo $content ; }  ;?>        
        </td></tr>
        </table>
	</center>


</td>
</tr>
</table></td>
    <td valign="top" class="rightY"><table width="100%" height="215" border="0" cellpadding="0" cellspacing="0" class="headRight">
<tr>
<td height="270">&nbsp;</td>
</tr>
</table></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="footLeft">&nbsp;</td>
    <td width="1018" height="138" class="footer">
    <br>    <br>
     <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="down_menu">
	<tr>
	<td height="38" valign="bottom">
	<a href="http://oldbk.com/?about=yes" target=_blank class="down_menuL">ОБ ИГРЕ</a> | 	
	<a href="http://blog.oldbk.com" target=_blank class="down_menuL">БЛОГИ</a> | 
	<a href="http://oldbk.com/encicl/index.php" target=_blank class="down_menuL">БИБЛИОТЕКА</a> | 
	<a href="http://oldbk.com/forum.php" target=_blank class="down_menuL">ФОРУМ</a> | 
	<a href="http://top.oldbk.com/index.php" target=_blank class="down_menuL">РЕЙТИНГИ</a> | 
	<a href="http://oldbk.com/partners/index.php" target=_blank class="down_menuL">ПАРТНЕРАМ</a>
	</td>
	</tr>
   </table>
   <br>
   
<div align=center>
<!--Google Analytics counter-->
			<script type="text/javascript">
			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', 'UA-17715832-1']);

var rsrc = /mgd_src=(\d+)/ig.exec(document.URL);
    if(rsrc != null) {
        _gaq.push(['_setCustomVar', 1, 'mgd_src', rsrc[1], 2]);
    }			  
			  _gaq.push(['_trackPageview']);

			  (function() {
			    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			  })();

			</script>
			<!--Google Analytics counter-->


			<!--LiveInternet counter--><script type="text/javascript"><!--
			document.write("<a href='http://www.liveinternet.ru/click' "+
			"target=_blank><img style='float:center; ' src='http://counter.yadro.ru/hit?t54.2;r"+
			escape(document.referrer)+((typeof(screen)=="undefined")?"":
			";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
			screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
			";"+Math.random()+
			"' alt='' title='LiveInternet: показано число просмотров и"+
			" посетителей за 24 часа' "+
			"border='0' ><\/a>")
			//--></script><!--/LiveInternet-->


		   	<!--Rating@Mail.ru counter-->
			<script language="javascript" type="text/javascript"><!--
			d=document;var a='';a+=';r='+escape(d.referrer);js=10;//--></script>
			<script language="javascript1.1" type="text/javascript"><!--
			a+=';j='+navigator.javaEnabled();js=11;//--></script>
			<script language="javascript1.2" type="text/javascript"><!--
			s=screen;a+=';s='+s.width+'*'+s.height;
			a+=';d='+(s.colorDepth?s.colorDepth:s.pixelDepth);js=12;//--></script>

			<script language="javascript1.3" type="text/javascript"><!--
			js=13;//--></script><script language="javascript" type="text/javascript"><!--
			d.write('<a style="float:center; margin-left:10px;" href="http://top.mail.ru/jump?from=1765367" target="_blank">'+
			'<img src="http://df.ce.ba.a1.top.mail.ru/counter?id=1765367;t=49;js='+js+
			a+';rand='+Math.random()+'" alt="Рейтинг@Mail.ru" border="0" '+
			'height="31" width="88"><\/a>');if(11<js)d.write('<'+'!-- ');//--></script>
			<noscript><a target="_top" href="http://top.mail.ru/jump?from=1765367">
			<img src="http://df.ce.ba.a1.top.mail.ru/counter?js=na;id=1765367;t=49"
			height="31" width="88" border="0" alt="Рейтинг@Mail.ru"></a></noscript>
			<script language="javascript" type="text/javascript"><!--
			if(11<js)d.write('--'+'>');//--></script>
			<!--// Rating@Mail.ru counter-->
			
			<!-- Yandex.Metrika counter -->
			<script type="text/javascript">
			(function (d, w, c) {
				(w[c] = w[c] || []).push(function() {
					try {
						w.yaCounter1256934 = new Ya.Metrika({id:1256934,
								accurateTrackBounce:true, webvisor:true});
					} catch(e) {}
				});
    
				var n = d.getElementsByTagName("script")[0],
					s = d.createElement("script"),
					f = function () { n.parentNode.insertBefore(s, n); };
				s.type = "text/javascript";
				s.async = true;
				s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

				if (w.opera == "[object Opera]") {
					d.addEventListener("DOMContentLoaded", f);
				} else { f(); }
			})(document, window, "yandex_metrika_callbacks");
			</script>
			<noscript><div><img src="//mc.yandex.ru/watch/1256934" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
			<!-- /Yandex.Metrika counter -->


			<img src="http://i.oldbk.com/i/copy.gif" width="26" height="24" style="float:center; padding: 10px 5px 5px 5px;" border="0">
			<a href="http://oldbk.com">© 2010—<?=date("Y");?> «Бойцовский Клуб ОлдБК»</a>

			

		<br><a href="http://oldbk.com/" style="color:#808080;">Многопользовательская бесплатная онлайн фэнтези рпг - ОлдБК - Старый Бойцовский Клуб</a>
</div>

    
    </td>
    <td class="footRight">&nbsp;</td>
  </tr>
</table>



</body>
</html>
