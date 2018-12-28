<?php

/**
 * @var \components\Component\Slim\View $this
 * @var \DebugBar\SlimDebugBar $debugbar;
 */
?>
<!--<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">-->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <link rel="stylesheet" href="/eassets/stylesssl.css" type="text/css" media="screen">
    <link rel="apple-touch-icon" sizes="512x512" href="//i.oldbk.com/i/icon/oldbk_512x512.png">
    <link rel="apple-touch-icon" sizes="144x144" href="//i.oldbk.com/i/icon/oldbk_144x144.png">
    <link rel="apple-touch-icon" sizes="114x114" href="//i.oldbk.com/i/icon/oldbk_114x114.png">
    <link rel="apple-touch-icon" sizes="72x72" href="//i.oldbk.com/i/icon/oldbk_72x72.png">
    <link rel="apple-touch-icon" sizes="58x58" href="//i.oldbk.com/i/icon/oldbk_58x58.png">
    <link rel="apple-touch-icon" sizes="48x48" href="//i.oldbk.com/i/icon/oldbk_48x48.png">
    <link rel="apple-touch-icon" sizes="29x29" href="//i.oldbk.com/i/icon/oldbk_29x29.png">
    <link rel="apple-touch-icon" href="//i.oldbk.com/i/icon/oldbk_57x57.png">
    <meta name='yandex-verification' content='60ef46abc2646a77'>
    <title><?= $page_title ?></title>
    <?php if($page_description): ?>
        <META name="description" content="<?= $page_description ?>">
    <?php endif; ?>
    <?php foreach ($app->clientScript->getCssFiles() as $cssFile): ?>
        <link rel="StyleSheet" href="<?= $cssFile ?>" type="text/css">
    <?php endforeach; ?>
    <script type="text/javascript">
        function look(type){
            param=document.getElementById(type);
            if(param.style.display == "none") param.style.display = "block";
            else param.style.display = "none"
        }
    </script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <?php foreach ($app->clientScript->getJsFiles(\components\Component\Slim\Middleware\ClientScript\ClientScript::JS_POSITION_BEGIN) as $jsFile): ?>
        <script src="<?= $jsFile; ?>"></script>
    <?php endforeach; ?>
    <?php
    if($debugbar) {
        echo $debugbar->getJavascriptRenderer()->renderHead();
    }
    ?>
    <style>
        table.table_library tr:nth-child(odd) {background: #E6E2CE}
        table.table_library tr:nth-child(even) {background: #F3F1E7}
    </style>
</head>

<body>
<table class="table tableWidth100">
    <tr>
        <td class="leftY">
            <table class="headLeft table tableWidth100">
                <tr>
                    <td class="empty270">&nbsp;</td>
                </tr>
            </table>
        </td>
        <td class="main_bg">
            <table class="tableWidth100 table">
                <tr>
                    <td class="header">&nbsp;</td>
                </tr>
                <tr>
                    <td class="cont_cracks">
                        <table class="table cont_cracks_table">
                            <tr>
                                <td class="cont_cracks_table_td">
                                    <table class="tableWidth100 table" style="height:738px;">
                                        <tbody>
                                        <tr>
                                            <td class="menu_head"></td>
                                        </tr>
                                        <tr>
                                            <td class="menu_bg">
                                                <table class="category_table table">
                                                    <tbody>
                                                    <?php foreach ($categories as $category): ?>
                                                        <?php
                                                        if ($category['parent'] == -1) {
                                                            ?>
                                                            <tr>
                                                                <td class="menu_cat"><?= $category['title'] ?></td>
                                                            </tr>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <tr>
                                                                <td class="menu align_left"><span style="color:#413321"><?= $category['title'] ?></span></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        <?php if (isset($pages_by_category[$category['id']])) foreach ($pages_by_category[$category['id']] as $page): ?>
                                                            <tr>
                                                                <td class="menu"><a href="/encicl/<?= $page['dir'] ?>.html">&#9658; <?= $page['page_title'] ?></a><br></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="menu_foot">&nbsp;</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                <td class="TD1">
                                    <hr style="border: none;color: #a09c81;background-color: #a09c81;height: 2px;">
                                    <div style="text-align: right"><input type="image" alt="Регистрация" src="https://oldbk.com/i/main/lib_reg2.gif" onclick="location.href='https://oldbk.com/reg.php?reg=1&amp;b=&amp;pid=203&amp;ref='"></div>
                                    <div class="container">
                                        <?= $content ?>
                                    </div>
                                    <div>&nbsp;</div>
                                    <div style="text-align: right"><input type="image" alt="Регистрация" src="https://oldbk.com/i/main/lib_reg2.gif" onclick="location.href='https://oldbk.com/reg.php?reg=1&amp;b=&amp;pid=203&amp;ref='"></div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td class="rightY">
            <table style="height:215px;" class="headRight table tableWidth100">
                <tbody>
                <tr>
                    <td class="empty270">&nbsp;</td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>
<table class="table tableWidth100">
    <tr>
        <td class="footLeft">&nbsp;</td>
        <td class="footer">
            <div class="down_menu">
                <a href="https://oldbk.com/?about=yes" target=_blank class="down_menuL">ОБ ИГРЕ</a> |
                <a href="https://oldbk.com/news.php" target=_blank class="down_menuL">НОВОСТИ</a> |
                <a href="https://oldbk.com/forum.php" target=_blank class="down_menuL">ФОРУМ</a> |
                <a href="https://top.oldbk.com/index.php" target=_blank class="down_menuL">РЕЙТИНГИ</a> |
                <a href="https://oldbk.com/partners/index.php" target=_blank class="down_menuL">ПАРТНЕРАМ</a>
            </div>
            <div style="margin-top:10px; text-align: center;">
                <?= include($_SERVER['DOCUMENT_ROOT'].'/counters/all.php'); ?>
                <br><a href="https://oldbk.com/" style="color:#808080;">Многопользовательская бесплатная онлайн фэнтези рпг - ОлдБК - Старый Бойцовский Клуб</a>
            </div>
        </td>
        <td class="footRight">&nbsp;</td>
    </tr>
</table>
</body>
</html>