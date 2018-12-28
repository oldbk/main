<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 29.11.2015
 *
 * @var string $quest_description
 * @var array $dialogs
 */ ?>

<div id="quest-dialog" style="z-index: 300; position: absolute; left: 50px; top: 30px;
					background: url('http://i.oldbk.com/i/quest/fp_1.png') no-repeat;
					background-position: top;
					border-radius: 13px;
					-webkit-box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75);
                    -moz-box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75);
                    box-shadow: 5px 5px 20px 0px rgba(0,0,0,0.75);">
    <br>
    <div id="dialog-content">
        <table  cellpadding="3" cellspasing="3" style="width: 688px;background: url('http://capitalcity.oldbk.com/i/quest/fp_2.jpg') repeat-y;">
            <colgroup>
                <col width="11px">
                <col width="auto">
                <col width="11px">
            </colgroup>
            <tbody>
            <tr>
                <td></td>
                <td>
                    Доступные диалоги
                    <ul>
						<?php foreach ($dialogs as $dialog): ?>
                            <li class="">
                                <a class="enter-dialog" href="javascript:void(0);" data-type="<?= $dialog['state'] ?>" data-b="<?= $dialog['bot_id'] ?>" data-d="<?= $dialog['dialog'] ?>">
									<?= $dialog['title'] ?> (State: <?= $dialog['state'] ?>)
                                </a>
                                -
                                <a class="hide-dialog" href="<?= $app->createUrl('dialog', array('action' => 'off', 'd' => $dialog['dialog'], 'q' => $dialog['quest_id'])) ?>">Скрыть</a>
                            </li>
						<?php endforeach ?>
                    </ul>
                    <span>
                        <em>
                            Скрытые диалоги можно будет включить в "состоянии"
                        </em>
                    </span>
                </td>
                <td></td>
            </tr>
            </tbody>
        </table>
    </div>
    <img src="http://i.oldbk.com/i/quest/fp_3.png">
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
    var j = jQuery.noConflict( true );

    j(function(){
        j(document.body).on('click', 'a .enter-dialog', function(){
            var $self = j(this);
            var b = $self.data('b');
            var d = $self.data('d');

            j.ajax({
                url         : '<?= $app->createUrl('dialog', array('action' => 'dialog')) ?>',
                data        : {d: d, b: b},
                type        : 'post',
                dataType    : 'json',
                success     : function(response){
                    if(response.status) {
                        $('#dialog-content').html(response.content);
                    }
                }
            });
        });

        j(document.body).on('click', 'a.hide-dialog', function(e) {
            e.preventDefault();
            var $self = j(this);

            j.ajax({
                url         : $self.attr('href'),
                dataType    : 'json',
                success     : function(response){
                }
            }).always(function() {
                $self.closest('li').remove();
                if(j('#dialog-content li').length == 0) {
                    j('#quest-dialog').hide();
                }
            });
        });

        j(document.body).on('click', '.dialog-action a', function(){
            var $self = j(this);
            var a = $self.data('a');
            var b = $self.data('b');
            var d = $self.data('d');

            if(!d || !b) {
                j('#quest-dialog').hide();
                return;
            }

            j.ajax({
                url         : '<?= $app->createUrl('dialog', array('action' => 'action')) ?>',
                data        : {d: d, b: b, a: a},
                type        : 'post',
                dataType    : 'json',
                success     : function(response){
                    if(response.status) {
                        j('#dialog-content').html(response.content);
                    } else {
                        j('#quest-dialog').hide();
                    }
                }
            });
        });
    });
</script>
