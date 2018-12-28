<?
	session_start();

	
	//ini_set('display_errors','On');
	if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
	include "connect.php";
?>

<style>
.close {
    position: absolute;right: 60px; top: 50px; width: 15px; height: 15px; background-image: url("http://i.oldbk.com/i/diz/close.png"); cursor: pointer;
    }
.close:hover {
    background-image: url("http://i.oldbk.com/i/diz/close_h.png");
}

.list {
    position: relative;
    max-height: 500px;
    width: 300px;
    margin: 80px 0 0 100px;
    text-align: left;
    overflow: hidden;
}
.list ul {
    list-style: none;
    margin: 0;
    padding: 0;
}
.list ul li {
    line-height: 19px;
    white-space: nowrap;
}
.list ul .num {
    width: 30px;
    line-height: 19px;    
    text-align: center;
    display: inline-block;
    color: brown;
}
.list ul .nickname {
    line-height: 19px;
    font-weight: bold;
}
.list ul .level {
	color:#F03C0E;
	line-height: 19px;	
	font-weight: bold;
}
</style>
<div class="close"  onclick="closeinfo();";></div>
 <div class="list">
        <ul>
            <?
            	$kk=1;
//            	$get_users_inf=mysql_query("select id,login,align,level, klan from users where level=13 and klan!='radminion' and klan!='Adminion' and bot=0 and block=0 order by `exp` desc limit 20");
            	$get_users_inf=mysql_query("select fe.exp as nexp, u.id, u.login, u.level, u.align, u.klan from users_14lvl fe LEFT join users u ON u.id=owner WHERE u.block = 0 and u.align != 4 and u.id is not null and u.klan != 'radminion' and u.klan != 'Adminion' order by fe.id limit 25");
            	
		if (mysql_num_rows($get_users_inf) > 0) 
		{            	
		while ($row = mysql_fetch_array($get_users_inf)) 
			{
			echo '	
			            <li>
			                <span class="num">'.$kk++.'</span>
			                <span class="nickname">';
			                if ($row['align']>0) { echo '<img src="http://i.oldbk.com/i/align_'.$row['align'].'.gif">';}
			                if ($row['klan']!='') { echo '<img src="http://i.oldbk.com/i/klan/'.$row['klan'].'.gif">'; }
			                echo $row['login'];
			                echo '</span>
			                <span class="level">['.$row['level'].']</span> <a href="/inf.php?'.$row['id'].'" target="_blank"><img src="http://i.oldbk.com/i/inf.gif"></a>
			            </li>';
			}
		}
		else
		{
			echo "<center><b>Пока нет таких героев...</b></center>";
		}
			
			for ($j=$kk;$j<=25;$j++)
			{
			echo '	
			            <li>
			                <span class="num">'.$kk++.'</span>
			                <span class="nickname"></span>
			                <span class="level"></span>
			            </li>';			
			}
			
            ?>
        </ul>
    </div>
    
