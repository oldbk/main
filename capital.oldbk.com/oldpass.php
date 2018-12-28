<?
session_start();
include "connect.php";
include "alg.php";

if (!empty($_SERVER['HTTP_CF_CONNECTING_IP']) )     	 	{     	$_SERVER['REMOTE_ADDR']=$_SERVER['HTTP_CF_CONNECTING_IP'];    	}
$ip = $_SERVER['REMOTE_ADDR'];

?>
<HTML><HEAD>
    <link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
    <meta content="text/html; charset=windows-1251" http-equiv=Content-type>
    <META Http-Equiv=Cache-Control Content=no-cache>
    <meta http-equiv=PRAGMA content=NO-CACHE>
    <META Http-Equiv=Expires Content=0>
</HEAD>
<body bgcolor=e2e0e0>
<?
if ($_SESSION['chpass']>0)
{
    $chk_owner=$_SESSION['chpass'];
}
elseif ( (!(isset($_SESSION['chpass']))) and (isset($_GET['confim_id'])) )
{
    $chk_owner=(int)($_GET['confim_id']);
}
else
{
    die();
}

///проверка активации
$chk_act=mysql_fetch_array(mysql_query("select * from oldbk.confirmpasswd_new where owner='{$chk_owner}' AND active=1"));
if ($chk_act['owner']>0)
{
    //обработка
    if ($chk_act['date']>time())
    {
        if (isset($_GET['confim_key']))
        {


            // проверка кода активации
            if ($chk_act['active_key']===$_GET['confim_key'])
            {
                try {
                    $User = \components\models\User::where('id', '=', $chk_owner)->first();
                    if(!$User) {
                        throw new Exception();
                    }

                    $User->pass = $chk_act['passwd'];
                    $User->salt = $chk_act['salt'];
                    if(!$User->save()) {
                        throw new Exception();
                    }

                    //установка времени обновления пароля
                    mysql_query("INSERT `users_pas_ch` (`owner`,`login` ,`last`) values('".$chk_act['owner']."','".$chk_act['login']."' , '".time()."') ON DUPLICATE KEY UPDATE `last`='".time()."' ; ");

                    $err="<font color=red><b>Пароль удачно сменен. </b></font> <br> <a href=http://oldbk.com/>Перейдите на главную страницу и войдите с новым паролем !</a> ";
                    $print_from=3;
                    $str_delo="INSERT INTO oldbk.`lichka` (`id` , `pers` , `text`, `date`) 	VALUES 	('','{$chk_act['owner']}','<font color=green>Сменен пароль</font>. Ip с которого произведена смена: ".$ip."','".time()."');";
                    if(!mysql_query($str_delo))
                    {
                        throw new Exception();
                    }

                    unset($_SESSION['chpass']);
                    mysql_query("delete from oldbk.confirmpasswd_new where owner='{$chk_owner}'");

                } catch (Exception $ex) {
                    $err="Ошибка установки нового пароля, обратитесь к Администрации....";
                    $err.="<br>Owner id:".$chk_act['owner'];
                    //$err.=mysql_error();

                    $print_from=3;
                }
            }
            else
            {
                //ошибка
                $err='Неверный код активации!';
                $print_from=2;
            }
        }
        else
        {
            //вывод формы для ввода кода активации
            $print_from=2;
        }
    }
    else
    {
        mysql_query("delete from oldbk.confirmpasswd_new where owner='{$chk_owner}'");
        $print_from=3;
        $err='Код активации устарел! <a href=http://oldbk.com/>Перейдите на главную страницу!</a> и установите новый пароль повторно.';
    }
}
else
//////////////////////
    if (($_POST['changepsw']) and ($_SESSION['chpass']>0) )
    {
/// проверка нужных условий

        if (($_POST['oldpass'] && $_POST['npass'] && $_POST['npass2']) )
        {
            if (strlen($_POST['npass'])<6 || strlen($_POST['npass'])>20 )
            {
                $err .= "<font color=red><b>Пароль должен быть от 6 до 20 символов!</b></font><br>";$stop=1;$print_from=1;
            }
            ///////////////
            if($_POST['npass'] != $_POST['npass2'])
            {
                $err.="<font color=red><b>Не совпадают новые пароли.</b></font><br>"; $stop=1;$print_from=1;
            }
            ////////////////////
            // ищим чара
            if ($stop!=1)
            {

                $ff=in_smdp($_POST['oldpass']);
                $ff_2=in_smdp_new($_POST['oldpass']);

                $ops = mysql_fetch_array(mysql_query("SELECT id, `pass` ,`login`, `email`, `realname` FROM `users` WHERE `id` = '{$_SESSION['chpass']}'   AND  (`pass` = '".$ff."'  OR  `pass` = '".$ff_2."' )  "));

                if ($ops['id']>0)
                {
                    if ($_POST['oldpass']===$_POST['npass']) { $err.="<font color=red><b>Новый пароль должен отличаться от старого</b></font> <br>"; $stop=1;$print_from=1;  }
                }
                else
                {
                    $err.="<font color=red><b>Неверный старый пароль.</b></font> <br>"; $stop=1;$print_from=1;
                }
///////////////////////////////////////////////////////////////////////////////////////
                // update
                if ($stop!=1)
                {

                    //1. заносим пароль в таблик активации
                    //2. отправляем линк активации
                    if ($ops['email']!='')
                    {
                        $akey=md5(md5(math.rand(-2000000,2000000).$ops['login']));
                        $akey=substr($akey,0,10);
                        $lasttime=time()+(24*3600);

                        $salt = \components\models\User::generateSalt();
                        $_new_password = \components\models\User::generatePassword($_POST['npass'], $salt);


                        if (mysql_query("insert into `oldbk`.confirmpasswd_new (login,owner,passwd,salt,active_key,date,ip,active) values('".$ops['login']."','".$ops['id']."','".$_new_password."','".$salt."','".$akey."','".$lasttime."','".$ip."',1)"))
                        {
                            $aa='<html>
									<head>
										<title>Смена старого пароля</title>
									</head>
									<body>
										Добрый день '.$ops['realname'].'.<br>
										Вами было запрошено изменение старого пароля для персонажа '.$ops['login'].' c IP адреса - '.$ip.', если это были не Вы, просто удалите это письмо.<br>
										<br>
										<br>
										<h3>Для подтверждения нового пароля пройдите по ссылке ниже.</h3><br>
										<a href="http://capitalcity.oldbk.com/oldpass.php?confim_key='.$akey.'&confim_id='.$ops['id'].'&flag=1&timev='.$lasttime.'">Подтвердить новый пароль</a>
										<br>
										Ваш код активации:'.$akey.' <br>
										<br>
										<font color="blue">Если вы не подтвердите пароль до <b>'.date("d-M-Y", $lasttime) .' 00:00</b>, ссылка будет неактивной.</font>
										<br>
										Отвечать на данное письмо не нужно.
									</body>
								</html>';

                            $mg = \Mailgun\Mailgun::create($app->config('mailgun.api_key'));
                            $message = $mg->messages();
                            $mg->messages()->send('oldbk.com', [
                                'from'    => 'support@oldbk.com',
                                'to'      => $ops['email'],
                                'subject' => mb_convert_encoding("Смена старого пароля на oldbk.com, для пользователя - ".$ops['login'], "utf-8", "windows-1251"),
                                'html'    => mb_convert_encoding($aa, "utf-8", "windows-1251")
                            ]);
                            //mailnew($ops['email'],"Смена старого пароля на oldbk.com, для пользователя - ".$ops['login'],$aa,true);

                            $parts=explode('@',$ops['email']);
                            $ppp='•';
                            if (strlen($parts[0])>4)
                            {
                                for ($i=4;$i<=strlen($parts[0]);$i++)
                                {
                                    $ppp.='•';
                                }
                            }
                            $hidden_mail=$parts[0][0].$parts[0][1].$parts[0][2].$ppp."@".$parts[1];

                            $err="<font color='red' ><b>Вам на почту ".$hidden_mail." была отправленна ссылка для активации нового пароля.</b></font>";
                            //$err="<font color='red' ><b>Активации нового пароля.</b></font>";
                            $print_from=2;
                            $chk_act['active_key']=$akey;
                        }
                        else
                        {
                            $err="<font color='red'><b>Сегодня пароль уже была попытка смены старого пароля. <br>Проверьте почту!</b></font>";
                            $print_from=2;
                        }
                    }
                    else
                    {
                        $err="<font color='red'><b>Ошибка отправки почты! У Вас не установлен почтовый адресс, обратитесь к Администрации!</font>";
                        $print_from=3;
                    }
                }
/////////////////////////////////////////////////////////////////////////////////////////
            }
            ////////////////////
        }
        else
        { $err.="<font color=red><b>Заполните все поля!</b></font> <br>"; $stop=1;$print_from=1;  }



    }
    else
    {
        $print_from=1;
    }





if (($print_from==1) and ($_SESSION['chpass']>0))
{
// форма
    ?>
    <FORM ACTION="oldpass.php" METHOD=POST>
        <table width=100%><tr><td><h3>Ваш пароль устарел, введите новый пароль</h3></td><td align=right>
                </td></tr>
        </table>
        <table width="100%">
            <tr>
                <td valign="top" width="50%">
                    <center> Уважаемый игрок!</center>
                    <br>
                    &nbsp&nbsp&nbsp<b>Смена пароля производится в обязательном порядке, из соображений безопасности не рекомендуем использовать любые старые пароли, которые хотя бы раз были установлены на вашем персонаже.</b>
                    <br>
                    &nbsp&nbsp&nbspВведите старый и новый пароли в поля под этим текстом и нажмите кнопку "Сменить пароль".
                    <br>
                    <br>
                    <div align=right><i>С уважением,Администрация ОлдБК.</i></div>
                    <fieldset>
                        <legend><b>Сменить пароль</b></legend>
                        <table>
                            <tr>
                                <td align=right>Старый пароль:</td><td><input type=password name="oldpass"></td>
                            </tr>
                            <tr>
                                <td align=right>Новый пароль:</td><td><input type=password name="npass"></td>
                            </tr>

                            <tr>
                                <td align=right>Новый пароль (еще раз):</td><td><input type=password name="npass2"></td>
                            </tr>
                            <tr>
                                <td align=right><input type=submit value="Сменить пароль" name="changepsw"></td><td></td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
        </table>
        <fieldset>
            <?=$err;?>
        </fieldset>
        </td>
        </tr>
        </table>
    </form>
    <?
}
elseif ($print_from==2)
{
// форма

    //$chk_act
    /*
	$test_ip=mysql_fetch_array(mysql_query("select * from (SELECT * FROM iplog WHERE owner = '{$chk_act['owner']}' ORDER BY id DESC LIMIT 10)as c where owner='{$chk_act['owner']}'  and ip like '%{$chk_act['ip']}%' limit 1"));
	if ($test_ip['id']>0)
		{
		$AKEY=$chk_act['active_key'];
		}
		else
		{
		$AKEY='';
		}
	*/

    //$AKEY=$chk_act['active_key'];
    $AKEY='';

    ?>
    <FORM ACTION="oldpass.php" METHOD=GET>
        <table width=100%><tr><td><h3>Активация нового пароля</h3></td><td align=right>
                </td></tr>
        </table>
        <table width="100%">
            <tr>
                <td valign="top" width="50%">
                    <br>
                    <br>
                    <br>
                    <fieldset>
                        <legend><b>Для активации нового пароля введите код-активации:</b></legend>
                        <table>
                            <tr>
                                <td align=right>Код:</td><td><input type=text name="confim_key" value="<?=$AKEY;?>"></td>
                            </tr>
                            <tr>
                                <td align=right><input type=submit value="Активировать" name="active_confim"></td><td></td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
        </table>
        <fieldset>
            <?=$err;?>
        </fieldset>
        </td>
        </tr>
        </table>
    </form>
    <?
}
elseif ($print_from==3)
{
    ?>
    <table width=100%><tr><td><h3>Активация нового пароля</h3></td><td align=right>
            </td></tr>
    </table>
    <table width="100%">
        <tr>
            <td valign="top" width="50%">
                <br>
                <br>
                <?=$err;?>
                </fieldset>
            </td>
        </tr>
    </table>
    </form>
    <?
}


?>
</body>
</html>
