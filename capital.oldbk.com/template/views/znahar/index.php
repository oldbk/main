<?php
use components\Component\Config;
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 11.11.2015
 *
 * @var \components\Component\Slim\View $this
 * @var \components\models\User $user
 * @var int $free_stats_have
 * @var int $need_money_stat
 * @var int $need_money_all_stat
 * @var int $need_money_all_masters
 * @var boolean $free_abil_drop  //can drop ability free
 * @var array $allAbility
 * @var array $userAbility
 * @var \components\Component\Slim\Slim $app
 * @var boolean $isDressed
 * @var array $travma
 * @var boolean $isBonus
 * @var boolean $isHaveZnaharStats
 * @var array $bank_ids
 * @var array $canAbility
 * @var boolean $znahar_travma
 * @var string $quest_description
 * @var int $drop_klass_have
 * @var int $need_money_klass
 */

if($quest_description !== null) {
    echo $this->renderPartial('common/quest', array('quest_description' => $quest_description));
}

?>
<div id="znahar-wrapper" class="container-wrapper">
    <div class="title">
        <div class="h3">
            Хижина Знахаря
        </div>
        <div id="buttons">
            <a class="button-mid btn" href="<?= $app->urlFor('znahar', array('action' => 'index')) ?>" title="Обновить">Обновить</a>
            <a class="button-mid btn" href="/city.php?bps=1" title="Вернуться">Вернуться</a>
        </div>
    </div>
    <div>
        <strong>Запахи трав наполняют помещение, непонятные и путающие предметы скрываются в пляшущих тенях...</strong><br>
        <strong>Говорят, здесь можно изменить свою судьбу. Стать кем-то иным... кем раньше был лишь в мечтах...</strong><br>
        <div class="mhint" style="font-size: 13px;margin-top: 5px;">Все имеет цену. Но не все можно купить. Помните - некоторые шансы даются лишь раз в жизни...</div>
    </div>
    <div id="znahar">
        <table cellspacing="0" cellpadding="0">
            <colgroup>
                <col width="60%">
                <col width="40%">
            </colgroup>
            <tbody>
            <tr>
                <td>
                    <div class="flash">
						<?php foreach ($app->flashData() as $type => $message): ?>
                            <div class="alert alert-<?= $type ?>">
								<?= $message; ?>
                            </div>
						<?php endforeach; ?>
                    </div>
                    <table class="table" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr class="head-line">
                            <th>
                                <div class="head-left"></div>
                                <div class="head-title">Перераспределение параметров</div>
                                <div class="head-right"></div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="even">
                            <td>
                                Вы можете стать иным - более ловким, сильным или мудрым... но лишь за счет других параметров
                            </td>
                        </tr>
						<?php
						switch (true) {
							case isset($travma['id']):
								echo $this->renderPartial('common/main/travma', array(
									'travma' => $travma
								));
								break;
							case $isBonus:
								echo $this->renderPartial('common/main/bonus');
								break;
							case $isHaveZnaharStats:
								echo $this->renderPartial('common/main/stats_effects');
								break;
							case $isDressed:
								echo $this->renderPartial('common/main/undress');
								break;
							default:
								echo $this->renderPartial('common/main/main', array(
									'need_money_all_stat'       => $need_money_all_stat,
									'need_money_all_masters'    => $need_money_all_masters,
									'need_money_stat'           => $need_money_stat,
									'free_stats_have'           => $free_stats_have,
									'user'                      => $user,
								));
								break;
						}
						?>
                        </tbody>
                    </table>
					<?php if($user->level >= 8): ?>
						<?= $this->renderPartial('common/user_class', array(
							'user' => $user,
							'drop_klass_have' => $drop_klass_have,
							'need_money_klass' => $need_money_klass,
						)) ?>
					<?php endif; ?>
					<?php if($user->level >= $app->dbConfig->znahar_min_ability_view): ?>
						<?=
						$this->renderPartial('common/ability', array(
							'user'              => $user,
							'allAbility'        => $allAbility,
							'userAbility'       => $userAbility,
							'canAbility'        => $canAbility,
							'free_abil_drop'    => $free_abil_drop,
						)) ?>
					<?php endif; ?>
					<?php if($user->level >= $app->dbConfig->znahar_min_magic_view && $user->smagic == 0): ?>
						<?= $this->renderPartial('common/magic', array('user' => $user)) ?>
					<?php endif; ?>
					<?php if($user->level >= $app->dbConfig->znahar_min_element && $user->smagic > 0): ?>
						<?= $this->renderPartial('common/element', array('user' => $user)) ?>
					<?php endif; ?>
					<?= $this->renderPartial('common/bonus', [
						'isHaveZnaharStats' => $isHaveZnaharStats
					]) ?>
                </td>
                <td class="center">
                    <img src="http://i.oldbk.com/i/images/znahar/bg1_80.jpg">
					<?php if($user->bank): ?>
                        <div>
                            <strong>У Вас в наличии: <span class="money"><?= $user->bank['cr'] ?></span> кр / <span class="money"><?= $user->bank['ekr'] ?></span> екр.</strong>
                        </div>
					<?php endif ?>
                    <div class="auth-block<?= empty($bank_ids) ? ' no' : '' ?>">
                        <div class="inner-auth">
                            <form id="bank-auth" action="<?= $app->urlFor('bank', array('action' => 'login')) ?>" method="post">
                                <div class="auth-num">
                                    <strong>№</strong>
                                    <select name="number">
										<?php foreach ($bank_ids as $item): ?>
                                            <option value="<?= $item['id'] ?>"><?= $item['id'] ?></option>
										<?php endforeach ?>
                                    </select>
                                </div>
                                <div class="auth-pass">
                                    <strong>Пароль</strong> <input type="password" name="password">
                                </div>
                                <div class="center enter">
                                    <a href="javascript:void(0);" onclick="$('#bank-auth').submit();" class="button-mid btn" title="Войти">Войти</a>
                                </div>
                            </form>
                        </div>
                        <div class="hint-block center">
							<?php if(!$user->bank): ?>
								<?= empty($bank_ids)
									? 'Откройте счет в банке для совершения операций за <strong>екр</strong>'
									: 'Авторизуйтесь для совершения операций за <strong>екр</strong>' ?>

							<?php else: ?>
                                <strong>
                                    <a href="<?= $app->urlFor('bank', array('action' => 'logout')) ?>">Выйти</a>
                                </strong>
							<?php endif; ?>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(){
        $(document.body).on('click', '.spoiler-block', function(){
            var $self = $(this);
            var $spoiler = $self.find('.spoiler');
            var $table = $spoiler.closest('table');
            var $td = $table.find('tr td');
            $td.slideToggle('fast');

            if($spoiler.hasClass('spoiler-down')) {
                $spoiler.removeClass('spoiler-down').addClass('spoiler-up');
            } else {
                $spoiler.removeClass('spoiler-up').addClass('spoiler-down');
            }
        });
		
		<?php if($app->request->get('open') == 'ability'): ?>
            $('.ability-spoiler').trigger('click');
        <?php endif; ?>
    });
</script>
