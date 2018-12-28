<?php
use components\Component\Config;
use \components\models\User;
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 11.11.2015
 *
 * @var \components\models\User[] $Winners
 * @var array $winnersMoney
 * @var array $comments
 * @var int $comment_count
 */


$smiles = ["/:flowers:/","/:inv:/","/:hug:/","/:horse:/","/:str:/","/:susel:/","/:smile:/","/:laugh:/","/:fingal:/","/:eek:/","/:smoke:/","/:hi:/","/:bye:/","/:king:/","/:king2:/","/:boks2:/","/:boks:/","/:gent:/","/:lady:/","/:tongue:/","/:smil:/","/:rotate:/","/:ponder:/","/:bow:/","/:angel:/","/:angel2:/","/:hello:/","/:dont:/","/:idea:/", "/:mol:/", "/:super:/","/:beer:/","/:drink:/","/:baby:/","/:tongue2:/", "/:sword:/", "/:agree:/","/:loveya:/","/:kiss:/","/:kiss2:/", "/:kiss3:/", "/:kiss4:/","/:rose:/","/:love:/","/:love2:/", "/:confused:/", "/:yes:/","/:no:/","/:shuffle:/","/:nono:/","/:maniac:/","/:privet:/","/:ok:/","/:ninja:/","/:pif:/", "/:smash:/","/:alien:/","/:pirate:/","/:gun:/","/:trup:/","/:mdr:/", "/:sneeze:/","/:mad:/","/:friday:/","/:cry:/","/:grust:/","/:rupor:/","/:fie:/", "/:nnn:/","/:row:/","/:red:/","/:lick:/","/:help:/","/:wink:/","/:jeer:/","/:tease:/","/:kruger:/","/:girl:/","/:Knight1:/","/:rev:/","/:smile100:/","/:smile118:/","/:smile149:/","/:smile166:/","/:smile237:/","/:smile245:/","/:smile28:/","/:smile289:/","/:smile314:/","/:smile36:/","/:smile39:/","/:smile44:/","/:smile70:/","/:smile87:/","/:smile434:/","/:vamp:/","/:ball_girl:/","/:warning2:/","/:futbol:/","/:s180:/","/:s210:/","/:ball:/","/:radio001:/","/:radio002:/","/:radio003:/","/:wall:/","/:smile26:/","/:showng:/","/:snegur:/","/:dedmoroz:/","/:superng:/","/:snowfight:/","/:doctor:/","/:nye:/"];
$smiles2 = ["<img style=\"cursor:pointer;\" onclick=S(\"flowers\") src=http://i.oldbk.com/i/smiles/flowers.gif>","<img style=\"cursor:pointer;\" onclick=S(\"inv\") src=http://i.oldbk.com/i/smiles/inv.gif>","<img style=\"cursor:pointer;\" onclick=S(\"hug\") src=http://i.oldbk.com/i/smiles/hug.gif>","<img style=\"cursor:pointer;\" onclick=S(\"horse\") src=http://i.oldbk.com/i/smiles/horse.gif>","<img style=\"cursor:pointer;\" onclick=S(\"str\") src=http://i.oldbk.com/i/smiles/str.gif>","<img style=\"cursor:pointer;\" onclick=S(\"susel\") src=http://i.oldbk.com/i/smiles/susel.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile\") src=http://i.oldbk.com/i/smiles/smile.gif>","<img style=\"cursor:pointer;\" onclick=S(\"laugh\") src=http://i.oldbk.com/i/smiles/laugh.gif>","<img style=\"cursor:pointer;\" onclick=S(\"fingal\") src=http://i.oldbk.com/i/smiles/fingal.gif>","<img style=\"cursor:pointer;\" onclick=S(\"eek\") src=http://i.oldbk.com/i/smiles/eek.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smoke\") src=http://i.oldbk.com/i/smiles/smoke.gif>","<img style=\"cursor:pointer;\" onclick=S(\"hi\") src=http://i.oldbk.com/i/smiles/hi.gif>","<img style=\"cursor:pointer;\" onclick=S(\"bye\") src=http://i.oldbk.com/i/smiles/bye.gif>","<img style=\"cursor:pointer;\" onclick=S(\"king\") src=http://i.oldbk.com/i/smiles/king.gif>","<img style=\"cursor:pointer;\" onclick=S(\"king2\") src=http://i.oldbk.com/i/smiles/king2.gif>","<img style=\"cursor:pointer;\" onclick=S(\"boks2\") src=http://i.oldbk.com/i/smiles/boks2.gif>","<img style=\"cursor:pointer;\" onclick=S(\"boks\") src=http://i.oldbk.com/i/smiles/boks.gif>","<img style=\"cursor:pointer;\" onclick=S(\"gent\") src=http://i.oldbk.com/i/smiles/gent.gif>","<img style=\"cursor:pointer;\" onclick=S(\"lady\") src=http://i.oldbk.com/i/smiles/lady.gif>","<img style=\"cursor:pointer;\" onclick=S(\"tongue\") src=http://i.oldbk.com/i/smiles/tongue.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smil\") src=http://i.oldbk.com/i/smiles/smil.gif>","<img style=\"cursor:pointer;\" onclick=S(\"rotate\") src=http://i.oldbk.com/i/smiles/rotate.gif>","<img style=\"cursor:pointer;\" onclick=S(\"ponder\") src=http://i.oldbk.com/i/smiles/ponder.gif>","<img style=\"cursor:pointer;\" onclick=S(\"bow\") src=http://i.oldbk.com/i/smiles/bow.gif>","<img style=\"cursor:pointer;\" onclick=S(\"angel\") src=http://i.oldbk.com/i/smiles/angel.gif>","<img style=\"cursor:pointer;\" onclick=S(\"angel2\") src=http://i.oldbk.com/i/smiles/angel2.gif>","<img style=\"cursor:pointer;\" onclick=S(\"hello\") src=http://i.oldbk.com/i/smiles/hello.gif>","<img style=\"cursor:pointer;\" onclick=S(\"dont\") src=http://i.oldbk.com/i/smiles/dont.gif>","<img style=\"cursor:pointer;\" onclick=S(\"idea\") src=http://i.oldbk.com/i/smiles/idea.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"mol\") src=http://i.oldbk.com/i/smiles/mol.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"super\") src=http://i.oldbk.com/i/smiles/super.gif>","<img style=\"cursor:pointer;\" onclick=S(\"beer\") src=http://i.oldbk.com/i/smiles/beer.gif>","<img style=\"cursor:pointer;\" onclick=S(\"drink\") src=http://i.oldbk.com/i/smiles/drink.gif>","<img style=\"cursor:pointer;\" onclick=S(\"baby\") src=http://i.oldbk.com/i/smiles/baby.gif>","<img style=\"cursor:pointer;\" onclick=S(\"tongue2\") src=http://i.oldbk.com/i/smiles/tongue2.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"sword\") src=http://i.oldbk.com/i/smiles/sword.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"agree\") src=http://i.oldbk.com/i/smiles/agree.gif>","<img style=\"cursor:pointer;\" onclick=S(\"loveya\") src=http://i.oldbk.com/i/smiles/loveya.gif>","<img style=\"cursor:pointer;\" onclick=S(\"kiss\") src=http://i.oldbk.com/i/smiles/kiss.gif>","<img style=\"cursor:pointer;\" onclick=S(\"kiss2\") src=http://i.oldbk.com/i/smiles/kiss2.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"kiss3\") src=http://i.oldbk.com/i/smiles/kiss3.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"kiss4\") src=http://i.oldbk.com/i/smiles/kiss4.gif>","<img style=\"cursor:pointer;\" onclick=S(\"rose\") src=http://i.oldbk.com/i/smiles/rose.gif>","<img style=\"cursor:pointer;\" onclick=S(\"love\") src=http://i.oldbk.com/i/smiles/love.gif>","<img style=\"cursor:pointer;\" onclick=S(\"love2\") src=http://i.oldbk.com/i/smiles/love2.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"confused\") src=http://i.oldbk.com/i/smiles/confused.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"yes\") src=http://i.oldbk.com/i/smiles/yes.gif>","<img style=\"cursor:pointer;\" onclick=S(\"no\") src=http://i.oldbk.com/i/smiles/no.gif>","<img style=\"cursor:pointer;\" onclick=S(\"shuffle\") src=http://i.oldbk.com/i/smiles/shuffle.gif>","<img style=\"cursor:pointer;\" onclick=S(\"nono\") src=http://i.oldbk.com/i/smiles/nono.gif>","<img style=\"cursor:pointer;\" onclick=S(\"maniac\") src=http://i.oldbk.com/i/smiles/maniac.gif>","<img style=\"cursor:pointer;\" onclick=S(\"privet\") src=http://i.oldbk.com/i/smiles/privet.gif>","<img style=\"cursor:pointer;\" onclick=S(\"ok\") src=http://i.oldbk.com/i/smiles/ok.gif>","<img style=\"cursor:pointer;\" onclick=S(\"ninja\") src=http://i.oldbk.com/i/smiles/ninja.gif>","<img style=\"cursor:pointer;\" onclick=S(\"pif\") src=http://i.oldbk.com/i/smiles/pif.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"smash\") src=http://i.oldbk.com/i/smiles/smash.gif>","<img style=\"cursor:pointer;\" onclick=S(\"alien\") src=http://i.oldbk.com/i/smiles/alien.gif>","<img style=\"cursor:pointer;\" onclick=S(\"pirate\") src=http://i.oldbk.com/i/smiles/pirate.gif>","<img style=\"cursor:pointer;\" onclick=S(\"gun\") src=http://i.oldbk.com/i/smiles/gun.gif>","<img style=\"cursor:pointer;\" onclick=S(\"trup\") src=http://i.oldbk.com/i/smiles/trup.gif>","<img style=\"cursor:pointer;\" onclick=S(\"mdr\") src=http://i.oldbk.com/i/smiles/mdr.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"sneeze\") src=http://i.oldbk.com/i/smiles/sneeze.gif>","<img style=\"cursor:pointer;\" onclick=S(\"mad\") src=http://i.oldbk.com/i/smiles/mad.gif>","<img style=\"cursor:pointer;\" onclick=S(\"friday\") src=http://i.oldbk.com/i/smiles/friday.gif>","<img style=\"cursor:pointer;\" onclick=S(\"cry\") src=http://i.oldbk.com/i/smiles/cry.gif>","<img style=\"cursor:pointer;\" onclick=S(\"grust\") src=http://i.oldbk.com/i/smiles/grust.gif>","<img style=\"cursor:pointer;\" onclick=S(\"rupor\") src=http://i.oldbk.com/i/smiles/rupor.gif>","<img style=\"cursor:pointer;\" onclick=S(\"fie\") src=http://i.oldbk.com/i/smiles/fie.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"nnn\") src=http://i.oldbk.com/i/smiles/nnn.gif>","<img style=\"cursor:pointer;\" onclick=S(\"row\") src=http://i.oldbk.com/i/smiles/row.gif>","<img style=\"cursor:pointer;\" onclick=S(\"red\") src=http://i.oldbk.com/i/smiles/red.gif>","<img style=\"cursor:pointer;\" onclick=S(\"lick\") src=http://i.oldbk.com/i/smiles/lick.gif>","<img style=\"cursor:pointer;\" onclick=S(\"help\") src=http://i.oldbk.com/i/smiles/help.gif>","<img style=\"cursor:pointer;\" onclick=S(\"wink\") src=http://i.oldbk.com/i/smiles/wink.gif>","<img style=\"cursor:pointer;\" onclick=S(\"jeer\") src=http://i.oldbk.com/i/smiles/jeer.gif>","<img style=\"cursor:pointer;\" onclick=S(\"tease\") src=http://i.oldbk.com/i/smiles/tease.gif>","<img style=\"cursor:pointer;\" onclick=S(\"kruger\") src=http://i.oldbk.com/i/smiles/kruger.gif>","<img style=\"cursor:pointer;\" onclick=S(\"girl\") src=http://i.oldbk.com/i/smiles/girl.gif>","<img style=\"cursor:pointer;\" onclick=S(\"Knight1\") src=http://i.oldbk.com/i/smiles/Knight1.gif>","<img style=\"cursor:pointer;\" onclick=S(\"rev\") src=http://i.oldbk.com/i/smiles/rev.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile100\") src=http://i.oldbk.com/i/smiles/smile100.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile118\") src=http://i.oldbk.com/i/smiles/smile118.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile149\") src=http://i.oldbk.com/i/smiles/smile149.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile166\") src=http://i.oldbk.com/i/smiles/smile166.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile237\") src=http://i.oldbk.com/i/smiles/smile237.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile245\") src=http://i.oldbk.com/i/smiles/smile245.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile28\") src=http://i.oldbk.com/i/smiles/smile28.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile289\") src=http://i.oldbk.com/i/smiles/smile289.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile314\") src=http://i.oldbk.com/i/smiles/smile314.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile36\") src=http://i.oldbk.com/i/smiles/smile36.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile39\") src=http://i.oldbk.com/i/smiles/smile39.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile44\") src=http://i.oldbk.com/i/smiles/smile44.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile70\") src=http://i.oldbk.com/i/smiles/smile70.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile87\") src=http://i.oldbk.com/i/smiles/smile87.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile434\") src=http://i.oldbk.com/i/smiles/smile434.gif>","<img style=\"cursor:pointer;\" onclick=S(\"vamp\") src=http://i.oldbk.com/i/smiles/vamp.gif>","<img style=\"cursor:pointer;\" onclick=S(\"ball_girl\") src=http://i.oldbk.com/i/smiles/ball_girl.gif>","<img style=\"cursor:pointer;\" onclick=S(\"warning2\") src=http://i.oldbk.com/i/smiles/warning2.gif>","<img style=\"cursor:pointer;\" onclick=S(\"futbol\") src=http://i.oldbk.com/i/smiles/futbol.gif>","<img style=\"cursor:pointer;\" onclick=S(\"s180\") src=http://i.oldbk.com/i/smiles/s180.gif>","<img style=\"cursor:pointer;\" onclick=S(\"s210\") src=http://i.oldbk.com/i/smiles/s210.gif>","<img style=\"cursor:pointer;\" onclick=S(\"ball\") src=http://i.oldbk.com/i/smiles/ball.gif>","<img style=\"cursor:pointer;\" onclick=S(\"radio001\") src=http://i.oldbk.com/i/smiles/radio001.gif>","<img style=\"cursor:pointer;\" onclick=S(\"radio002\") src=http://i.oldbk.com/i/smiles/radio002.gif>","<img style=\"cursor:pointer;\" onclick=S(\"radio003\") src=http://i.oldbk.com/i/smiles/radio003.gif>","<img style=\"cursor:pointer;\" onclick=S(\"wall\") src=http://i.oldbk.com/i/smiles/wall.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile26\") src=http://i.oldbk.com/i/smiles/smile26.gif>","<img style=\"cursor:pointer;\" onclick=S(\"showng\") src=http://i.oldbk.com/i/smiles/showng.gif>","<img style=\"cursor:pointer;\" onclick=S(\"snegur\") src=http://i.oldbk.com/i/smiles/snegur.gif>","<img style=\"cursor:pointer;\" onclick=S(\"dedmoroz\") src=http://i.oldbk.com/i/smiles/dedmoroz.gif>","<img style=\"cursor:pointer;\" onclick=S(\"superng\") src=http://i.oldbk.com/i/smiles/superng.gif>","<img style=\"cursor:pointer;\" onclick=S(\"snowfight\") src=http://i.oldbk.com/i/smiles/snowfight.gif>","<img style=\"cursor:pointer;\" onclick=S(\"doctor\") src=http://i.oldbk.com/i/smiles/doctor.gif>","<img style=\"cursor:pointer;\" onclick=S(\"nye\") src=http://i.oldbk.com/i/smiles/nye.gif>"];

?>
<style>
    #page-wrapper .button-mid {
        line-height: 12px;
        height: 20px;
    }
    #buttons {
        margin-right: 20px;
        margin-top: 20px;
    }
    .bg-fontan {
        background: url("http://i.oldbk.com/i/fontan/fontanbg.jpg") no-repeat;
        width: 900px;
        min-height: 700px;
        margin: 0 auto;
        background-position-y: 20px;
        position: relative;
        padding-top: 200px;
    }
    .bg-fontan .btn-coin {
        background: url("http://i.oldbk.com/i/city/sub/fallmoney.png") no-repeat;
        height: 32px;
        width: 170px;
        position: absolute;
        left: 155px;
        top: 50px;
    }
    .bg-fontan .btn-coin:hover {
        background: url("http://i.oldbk.com/i/city/sub/fallmoney2.png") no-repeat;
    }
    .bg-fontan .btn-water {
        background: url("http://i.oldbk.com/i/city/sub/drnkwater.png") no-repeat;
        height: 32px;
        width: 170px;
        position: absolute;
        right: 150px;
        top: 50px;
    }
    .bg-fontan .btn-water:hover {
        background: url("http://i.oldbk.com/i/city/sub/drnkwater2.png") no-repeat;
    }
    .bg-fontan .box {
        width: 715px;
        margin-left: 110px;
    }
    #page-wrapper .winner-list li {
        padding: 5px;
    }
    #page-wrapper #fontan-wrapper {
        padding-bottom: 100px;
    }
    hr {
       margin: 5px 0;
    }
</style>
<div id="buttons">
    <a class="button-mid btn" href="<?= $app->urlFor('fontan', array('action' => 'index')) ?>" title="Обновить">Обновить</a>
    <a class="button-mid btn" href="/city.php?bps=1" title="Вернуться">Вернуться</a>
</div>
<div id="fontan-wrapper" class="container-wrapper">
    <div class="container h-100">
        <div class="title">
            <div class="h3">
                Фонтан Удачи
            </div>
        </div>
		<?php
		$start_fontan = (new DateTime())->getTimestamp();
		$message_fontan = sprintf('У вас есть %s/%s попыток!', $DailyFree->uses, $DailyFree->limit_uses);
		$nextAdded = $DailyFree->getNextAddedTimestamp();
		if($DailyFree->uses == 0) {
			$message_fontan .= sprintf(' Следующая попытка восстановится через %s. %d',
				\components\Helper\TimeHelper::prettyTime($start_fontan, $nextAdded), $DailyFree->uses);
		}
		?>
        <div style="color: red" class="center">
			<?= $message_fontan ?>
        </div>
        <div class="flash">
			<?php foreach ($app->flashData() as $type => $message): ?>
                <div class="alert alert-<?= $type ?>">
					<?= $message; ?>
                </div>
			<?php endforeach; ?>
        </div>
        <div class="bg-fontan">
            <a class="btn-coin" href="<?= $app->urlFor('fontan', array('action' => 'coin')) ?>"></a>
            <a class="btn-water" href="<?= $app->urlFor('fontan', array('action' => 'fontan')) ?>"></a>
            <div class="box">
                <div class="row">
                    <div class="col-6">
                        <div class="row">
                            <div class="col-12">
                                В сутки можно бросить в фонтан не больше 50 монеток.
                            </div>
                        </div>
                        <div class="row" style="padding: 10px 0;">
                            <div class="col-12">
                                Всего выиграно: <b><?= $win_sum ?></b>кр.
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <b>20</b> последних выигрышей:
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <ul class="winner-list">
									<?php foreach ($winnersMoney as $_w):
										if(!isset($Winners[$_w['winner']])) {
											continue;
										}
										?>
                                        <li>
											<?= sprintf('%s - %s кр.', $Winners[$_w['winner']]->htmlLogin(), $_w['winner_count']) ?>
                                        </li>
									<?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
						<?php foreach ($comments as $_c):
							$_c['text'] = preg_replace($smiles, $smiles2, $_c['text'],3);
							?>
                            <div class="row">
                                <div class="col-12">
                                    <div>
										<?= User::renderNick($_c['owner'], $_c['align'], $_c['klan'], $_c['login'], $_c['level']) ?>
                                    </div>
                                    <div>
										<?php if(!$_c['del_id']): ?>
											<?= $_c['text']; ?>
                                            <a onclick="if (!confirm('Удалить пост?')) { return false; }" href="<?= $app->urlFor('fontan', ['action' => 'deleteComment', 'id' => $_c['id']]); ?>" class="del-comment">
                                                <img src="/i/clear.gif">
                                            </a>
										<?php else: ?>
											<?php if($app->webUser->checkAccess('can_forum_restore')): ?>
                                                <i><font color=grey><?= $_c['text']; ?></font></i>
                                                <a onclick="if (!confirm('Восстановить пост?')) { return false; } " href="<?= $app->urlFor('fontan', ['action' => 'restoreComment', 'id' => $_c['id']]) ?>" class="res-comment">
                                                    <img src="/i/icon2.gif">
                                                </a>
											<?php endif; ?>
                                            <font color=red>Удалено <?= User::renderNick($_c['del_id'], $_c['del_align'], $_c['del_klan'], $_c['del_login'], $_c['del_level']) ?></font>
										<?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <hr>
						<?php endforeach; ?>
                        <div class="row">
                            <div class="col-12 center">
								<?php
								$pgs = $comment_count/10;

								$pages_str='';
								$page = (int)$_GET['page']>0 ? (((int)$_GET['page']+1)>$pgs ? ($pgs-1):(int)$_GET['page']):0;
								$page=ceil($page);
								if ($pgs>1)
								{
									//$pages_str.=($page>4 ? "...":"");
									for ($i=0;$i<ceil($pgs);$i++)
										if (($i>($page-4))&&($i<=($page+3)))
											$pages_str.=($i==$page ? " <b>".($i+1)."</b>":" <a href='?page=".($i)."'>".($i+1)."</a>");
									$pages_str.=($page<$pgs-4 ? "...":"");
									$pages_str=($page>3 ? "<a href='?&page=".($page-1)."'> < </a>...":"").$pages_str.(($page<($pgs-1) ? "<a href='?&page=".($page+1)."' > ></a>":""));
								}
								$FirstPage=(ceil($pgs)>3 ? $_GET['page']>0 ? "<a href='?&page=0'> Перв. </a>":"":"");
								$LastPage=(ceil($pgs)>3 ? (ceil($pgs)-1)!=$_GET['page'] ? "<a href='?&page=".(ceil($pgs)-1)."'> Посл. </a>":"":"");
								$pages_str=$FirstPage.$pages_str.$LastPage;
								echo $pages_str;
								?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="btn-control">
                                    <form action="<?= $app->urlFor('fontan', ['action' => 'addComment']); ?>" method="post">
                                        <div class="form-group">
                                            <label for="exampleFormControlTextarea1">Оставить сообщение:</label>
                                            <textarea name="message" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                                        </div>
                                        <input type="submit" class="button-mid btn" name="add" value="Добавить">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
