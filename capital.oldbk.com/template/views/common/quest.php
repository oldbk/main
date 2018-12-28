<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 29.11.2015
 *
 * @var string $message
 * @var array $actions
 */ ?>
<style>
    .dialog-actions {
        padding-top: 5px;
        border-top: 1px solid;
    }
    .dialog-actions ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
</style>
<table cellpadding="3" cellspasing="3" style="width: 688px;background: url('http://capitalcity.oldbk.com/i/quest/fp_2.jpg') repeat-y;">
    <colgroup>
        <col width="11px">
        <col width="146px">
        <col width="auto">
        <col width="11px">
    </colgroup>
    <tbody>
    <tr>
        <td></td>
        <td valign="top"><img src="http://i.oldbk.com/i/quest/quest_1_g.jpg"></td>
        <td>
            <div class="dialog-text">
				<?= $message; ?>
            </div>
            <div class="dialog-actions">
				<?php if($actions): ?>
					<?php foreach ($actions as $action): ?>
                        <ul>
                            <li class="dialog-action">
                                <a data-d="<?= $action['dialog'] ?>" data-a="<?= $action['action'] ?>" data-b="<?= $action['bot_id'] ?>" href="javascript:void(0)">
									<?= $action['message'] ?>
                                </a>
                            </li>
                        </ul>
					<?php endforeach;; ?>
				<?php endif; ?>
            </div>
        </td>
        <td></td>
    </tr>
    </tbody>
</table>
