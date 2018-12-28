<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 08.06.2018
 * Time: 22:47
 *
 *
 * @var \components\Component\Slim\View $this
 * @var \components\models\UserEventRating[] $items
 * @var \components\models\EventRating[] $Ratings
 * @var boolean $isHorn
 */

?>
<link rel="stylesheet" href="http://capitalcity.oldbk.com/assets/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="http://capitalcity.oldbk.com/i/btn.css" type="text/css">
<style>
    body {
        background-color: #d7d7d7;
    }
    .popup-wrapper {
        width: 800px;
        position: absolute;
        top: 10%;
        left: 50%;
        margin-left: -400px;
        z-index: 100;
    }
    .f-lay {
        background-image: url("http://i.oldbk.com/i/newd/pop/bg-y_4_2.jpg");
        padding: 0 20px;
        padding-bottom: 10px;
        font-size: 11px !important;
    }
    .f-header {
        background-image: url("http://i.oldbk.com/i/newd/pop/up_bg_3_2.jpg");
        height: 17px;
    }
    .f-footer {
        background-image: url("http://i.oldbk.com/i/newd/pop/down_bg_2.jpg");
        height: 8px;
    }
    #f-close {
        height: 17px;
        width: 26px;
        position: absolute;
        right: 0px;
        cursor: pointer;
        background-image: url("http://i.oldbk.com/i/newd/pop/close_butt.jpg");
    }
    #f-close:hover {
        background-image: url("http://i.oldbk.com/i/newd/pop/close_butt_hover.jpg");
    }
    .f-lay .item {
        padding: 5px 0;
    }
    .f-lay .title {
        COLOR: #8f0000;
        font-weight: bold;
    }
    .separate {
        background: url("http://i.oldbk.com/i/sostojanie/hr_2.jpg");
        height: 2px;
    }
    a, a:visited, a:hover {
        FONT-WEIGHT: bold;
        COLOR: #003388;
        text-decoration: none;
    }
    .f-lay .hide {
        display: none;
    }
    .f-clear:after {
        display: block;
        content: "";
        clear: both;
    }
    .f-list {
        margin: 0;
        list-style: none;
        padding: 0;
    }
    .f-list .block-item {
        float: none;
        display: block;
    }
    .block-item .hr {
        height: 40px;
        position: relative;
        background-image: url(http://i.oldbk.com/i/newd/pop/razdelitel1.png);
        background-repeat: no-repeat;
        background-position-x: right;
        background-position-y: -10px;
        text-align: right;
    }
    .block-item {
        height: 95px;
        width: 370px;
        display: inline-block;
        padding-right: 10px;
    }
    .f-lay .block-title {
        color: #8F0000;
        font-weight: bold;
        text-align: center;
        font-size: 13px;
    }
    .block-item .block-content .block-logo {
        float: left;
        width: 65px;
        height: 65px;
        margin-right: 5px;
    }
    .block-item .block-content .block-text {
        padding-left: 65px;
    }
    div.details {
        padding-left: 15px;
        color:black;
        font-weight:normal;
    }
    .f-info {
        margin-bottom: 20px;
    }
    #tab-placeholder ul#reward-list {
        text-align: center;
    }
    #tab-placeholder ul#reward-list li.item-render {
        position: relative;
        display: inline-block;
    }
    #tab-placeholder ul#reward-list li.item-render .item-count {
        position: absolute;
        background-color: #7F7A79;
        min-width: 20px;
        color: white;
        text-align: center;
        bottom: 0px;
        right: 0px;
        font-size: 10px;
        padding: 0 5px;
        font-weight: bold;
    }
    #tab-placeholder #placeholder {
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .f-clear:after {
        display: block;
        content: "";
        clear: both;
    }
    .btn {
        line-height: 10px;
        height: 19px;
        background-repeat: no-repeat;
    }
</style>

<div class="popup-wrapper">
    <div class="f-header">
        <div id="f-close" class="f-close"></div>
    </div>
    <div class="f-lay">
        <div class="f-info text-center" style="font-size: 15px">
            <a href="javascript:void(0)" class="tabs-link" data-link="<?= $app->urlFor('rating', ['action' => 'counter']) ?>" data-tab="timer">Таймеры</a> |
            <a href="javascript:void(0)" class="tabs-link" data-link="<?= $app->urlFor('rating', ['action' => 'rating']) ?>" data-tab="rating">Рейтинги</a>
        </div>
        <div class="f-content tab-content" id="tab-placeholder">
            <?= $this->renderPartial('counter', ['items' => $items]) ?>
        </div>
    </div>
    <div class="f-footer"></div>
</div>
<script>
    jq111(function(){
        jq111(top.frames['main'].document.body).off('click', '.f-close');
        jq111(top.frames['main'].document.body).on('click', '.f-close', function() {
            jq111(this).closest('#rating-wrapper').remove();
        });

        jq111(top.frames['main'].document.body).off('click', '.tabs-link');
        jq111(top.frames['main'].document.body).on('click', '.tabs-link', function() {
            var $self = jq111(this);
            console.log($self.data('link'));

            jq111.ajax({
                url: 'http://capitalcity.oldbk.com' + $self.data('link'),
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                beforeSend: function() {

                },
                success: function(response) {
                    if(response.status == 1) {
                        $self.closest('.f-lay').find('#tab-placeholder').html(response.html);
                    } else if(response.status == 0 && response.message !== undefined) {
                        alert(response.message);
                    }
                }
            }).always(function() {

            });
        });

        jq111(top.frames['main'].document.body).off('click', '.f-lay .reward');
        jq111(top.frames['main'].document.body).on('click', '.f-lay .reward', function() {
            var $self = $(this);
            var ratingId = $self.data('rating-id');

            var $loader = $self.closest('.btn-wrapper').find('.f-loader');
            jq111.ajax({
                url: 'http://capitalcity.oldbk.com/action/rating/reward',
                dataType: 'json',
                type: 'post',
                data: {'urid': ratingId},
                xhrFields: {
                    withCredentials: true
                },
                beforeSend: function() {
                    $self.addClass('disabled');
                    $loader.show();
                },
                success: function(response) {
                    if(response.status == 1) {
                        $self.closest('.f-lay').find('#tab-placeholder').html(response.html);

                        try {
                            removeRating(response.ratingId);
                        } catch (e) {

                        }
                    } else if(response.status == 0 && response.message !== undefined) {
                        $self.removeClass('disabled');
                        alert(response.message);
                    }
                }
            }).always(function() {
                $loader.hide();
            });
        });
    });
</script>