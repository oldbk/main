<?php
session_start();
$google = 1;
require_once 'news_connect.php';
mysql_query("SET NAMES utf8");
ini_set('default_charset','utf8');
require_once 'lib/class.User.php';

$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
include "functions.php";


$user = User::getUser($_SESSION['uid']);

$perpage = 20;

function newsEntryHTML($row, $disabledComments = false) {
$additional = $disabledComments ? "style=\"display: none\"" : 0;
$deleteButton = deleteButton($_SERVER['SCRIPT_NAME'] . '?act=admin&type=deleteEntry&id=' . $row['id']);
return <<<EOF
    <div class="item">
        <div class="title"><h3>{$row['topic']}</h3><div class="date">{$row['date']} {$deleteButton}</div></div>
        <div class="news_text">{$row['text']}</div>
		<div class="comm" {$additional}>Комментариев: [{$row['comments']}] <a href="?topic={$row['id']}">Оставить комментарий</a></div>   
        <div class="clear"></div> 
    </div>
EOF;
}

function showCommentsForm($id) {
return <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ОлдБК - Новости</title>
<meta name="keywords" content="бойцовский клуб, бк, онлайн игра, rpg, магия бой, игра фэнтези, fantasy, маг " />
<meta name="description" content="Бойцовский клуб - rpg онлайн игра, он же БК, созданный в 2003 году. Борьба Тьмы и Света. Бои, магия, персонажи - всё это Бойцовский клуб ">
<meta name="robots" content="index, follow"/>
<meta name="author" content="oldbk.com">
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/stylesnews1.css" media="all" />
</head>
<body style="background:#666866">

    <div><form action="?topic={$id}" method="POST" style="padding: 0px; margin: 0px;">
        <textarea name="comment" style="width: 100%; height: 150px;"></textarea>
		<div align="right"><input style="width: 30%; border: 1px #585857 solid; background:#666866; color: #fff" type="submit" name="add_comment" value=" Добавить комментарий " /></div>
    </form></div>
</body>
</html>    
EOF;

}

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
        <div class="title">{$row['author']} ({$commentButton})<div class="date">{$row['date']}{$deleteButton}</div></div>
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

function showAdminAddForm() {
    global $user;
    $error = array();
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
//echo $_REQUEST['title']."<br>".$_REQUEST['body'];
		    mysql_query("INSERT INTO news (`parent`, `topic`, `text`, `date`, `author`) VALUES (1, '".$_REQUEST['title']."', '" .nl2br($_REQUEST['body']) . "', '" . date("d.m.y H:i:s") . "', '" . $user->drawLogin() . "')") or die();
			
			header("location: " . $_SERVER['SCRIPT_NAME']);
			exit;
		}
	}
	$errorHTML = implode("<br />", $error);
return <<<EOF
    <div class="pagination" style="text-align: center"><a href="{$_SERVER['SCRIPT_NAME']}">Вернуться к новостям</a></div>
    {$errorHTML}
    <div><form action="?act=admin&type=add" method="POST" style="padding: 0px; margin: 0px;">
	 Заголовок: 
	 <br /><input type="text" name="title" value="{$_REQUEST['title']}" />
	 <br />Текст новости:
     <br /><textarea name="body" style="width: 790px; height: 150px;">{$_REQUEST['body']}</textarea>
 	 <div align="right"><input style="width: 30%; border: 1px #585857 solid; background: #666866; color: #fff" type="submit" name="add_entry" value=" Добавить новость " /></div>
    </form></div>
EOF;

}

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
    switch(@$_REQUEST['type']) {
	    case 'add':
		return showAdminAddForm();
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
		default:
		    header("location: " . $_SERVER['SCRIPT_NAME']);
			exit;
		break;
	}
}

function showContent() {
    global $perpage, $user;
	if(isset($_REQUEST['act']) && $_REQUEST['act'] == "admin" && $user && $user->isAuth() && $user->isAdmin()) {
	    return showAdminContent();
	}
   // $html = '<div align=right><a href="http://oldbk.com/rss.php" target="_blank"><img src="http://i.oldbk.com/i/iconrss_s.png" alt="RSS-Лента новостей" title="RSS-Лента новостей"></a></div>';
    if(isset($_REQUEST['topic'])) {
	    //show 1 entry						
		$query = mysql_query("SELECT * FROM news WHERE id = " . intval($_REQUEST['topic']));
		$data = array();
		if(($entry = mysql_fetch_assoc($query)) !== false) {
			$query      = mysql_query("SELECT count(*) AS count FROM news WhERE parent = ".intval($_REQUEST['topic'])."");
		    $count      = mysql_fetch_assoc($query);
		    $countPages = max(1, ceil($count['count'] / $perpage));
            $page       = getCurrentPage($countPages);		
		    $pages      = showPages($page, $countPages, $_SERVER['SCRIPT_NAME'] . '?topic=' . intval($_REQUEST['topic']) . '&p=');
		
		//    $html .= "<div class=\"news\">";
		 //   $html .= "<div class=\"pagination\" style=\"text-align: center\"><a href=\"{$_SERVER['SCRIPT_NAME']}\">Вернуться к новостям</a></div>";
          //  $html .= newsEntryHTML($entry, true);		

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
				 //header("location: " . $_SERVER['SCRIPT_NAME'] . '?topic=' . $entry['id'] . '&p=' . ceil(($count['count'] + 1) / $perpage));
				 $url='http://oldbk.com/news.php';
				 $url=$url.'?topic=' . $entry['id'] . '&p=' . ceil(($count['count'] + 1) / $perpage);
				echo "<html>";
				echo '<body>';
				echo "<script type=\"text/javascript\">";
				echo 'window.parent.location.href="'.$url.'";</script>';
				echo " </body>";
				echo "</html>";				
				 exit;
				 }
				 else
				 {
				 echo "Комментировать могут персонажи больше 5-го уровня!";
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
		    //    $html .= "<div class=\"pagination\">Страницы:  {$pages}</div>{$commentsHTML}<div class=\"pagination\">Страницы:  {$pages}</div>";
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
			//$html .= "</div>";
		} else {
		    //not fount
		}									
	} else {
	/*
	    //show page news;
		$query      = mysql_query("SELECT count(*) AS count FROM news WHERE parent = 1");
		$count      = mysql_fetch_assoc($query);
		$countPages = max(1, ceil($count['count'] / $perpage));
        $page       = getCurrentPage($countPages);		
		$pages      = showPages($page, $countPages, $_SERVER['SCRIPT_NAME'] . '?p=');
		
		$query = mysql_query("SELECT n. * , (SELECT COUNT( * ) FROM news AS c WHERE c.parent = n.id) AS comments
                              FROM `news` AS n
                              WHERE n.parent = 1
							  ORDER BY id DESC
                              LIMIT ".($perpage * $page - $perpage)." , ".$perpage."");
        $html .= "<div class=\"pagination\">Страницы:  {$pages}</div>";
		
		if($user && $user->isAuth() && $user->isAdmin()) {
		$html .= "<div class=\"pagination\" style=\"text-align: center\"><a href=\"" . $_SERVER['SCRIPT_NAME'] . "?act=admin&type=add\">Добавить новость</a></div>";
		}
		$html .= "<div class=\"news\">";
		while($row = mysql_fetch_assoc($query)) {
		    $html .= newsEntryHTML($row);
		}
		$html .= "</div>";
		$html .= "<div class=\"pagination\">Страницы:  {$pages}</div>";
		*/
	}
	return $html;
}

function getCurrentPage($countPages) {
    return min($countPages, max(1, isset($_REQUEST['p']) ? intval($_REQUEST['p']) : 1));
}

$content = showContent();
echo $content;
?>          

