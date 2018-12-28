<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 29.08.16
 * Time: 23:53
 */ ?>
<style>
    #captcha-block {
        height: 120px;
        position: absolute;
        z-index: 1000;
        left: 50%;
        top: 10%;
        margin-left: -275px;
        border: 1px solid black;
        padding: 5px;
    }
    #captcha-block ul {
        list-style: none;
        cursor: pointer;
    }
    #captcha-block ul li {
        float: left;
        padding: 0;
    }
    #captcha-block ul li.active {
        background-color: red;
    }
</style>
<div id="captcha-block">
    <ul>
        <li data-image="1">
            <img src="<?= $image1; ?>">
        </li>
        <li data-image="2">
            <img src="<?= $image2; ?>">
        </li>
        <li data-image="3">
            <img src="<?= $image3; ?>">
        </li>
        <li data-image="4">
            <img src="<?= $image4; ?>">
        </li>
        <li data-image="5">
            <img src="<?= $image5; ?>">
        </li>
    </ul>
    <div style="text-align: center">
        <a href="">Готово</a>
    </div>
</div>
<script>
    var $images = {0: false, 1: false};
    $(function(){
        $('#captcha-block li').on('click', function() {
            var $self = $(this);
            var image = $self.data('image');

            if($self.hasClass('active')) {
                $self.removeClass('active');
                var temp = $self.data('number');
                $images[temp] = false;

                return;
            }

            var flag = false;
            $.each($images, function(i, value) {
                if(value === false) {
                    addImage($self, i, image);
                    console.log('1');
                    flag = true;
                    return false;
                }
            });
            if(flag === true) {
                return;
            }

            if($images[0] !== false) {
                $('li[data-image="'+$images[0]+'"]').removeClass('active');
                addImage($self, 0, image);
                console.log('2');
            }

        });
    });

    function addImage($obj, num, image)
    {
        $images[num] = image;
        $obj.addClass('active')
            .data('number', num);
    }
</script>
