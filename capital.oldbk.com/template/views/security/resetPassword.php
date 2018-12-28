<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 12.02.2018
 * Time: 00:20
 */ ?>

<style type="text/css">
	body {
		margin: 0;
		padding: 0;
		background-color: #e2e0e0;
	}
	.password_wrapper {
		background: url("/i/enter/x-bg.jpg") repeat-x;
		height: 703px;
		width: 100%;
		margin-top: 50px;
	}
	.password_wrapper .p-middle {
		background: url("/i/enter/center.jpg") no-repeat;
		width: 1376px;
		height: 703px;
		margin: 0 auto;
		position: relative;
	}
	.password_wrapper #second_pass_div, .password_wrapper #content {
		position: absolute;
		width: 235px;
		height: 281px;
		left: 569px;
		top: 170px;
	}
	.password_wrapper table#calc {
		background-color:#e2e0e0;
		font-size: 14px;
		height: 100%;
		width: 100%;
		overflow: hidden;
	}
	.password_wrapper table#calc td {
		font-size: 14px;
		text-align: center;
	}
	.password_wrapper table#calc input {
		cursor:pointer;
		font-size:17px;
	}
</style>

</head>
<body>

<div class="password_wrapper">
	<div class="p-middle">
		<form id="content" name="second_form" method="post">
			<!-- Посылаемая форма -->
			<div style="width: 140px;text-align: left;margin: 0 auto;margin-top: 70px;">
				<div style="color: white;">Новый пароль</div>
				<input id='2fa_code' name='2fa_code' type="password" value="" style="padding: 5px;text-align: center" placeholder="" autocomplete="off" autofocus>
                <div style="color: white;margin-top: 10px;">Повторите пароль</div>
                <input id='2fa_code' name='2fa_code' type="password" value="" style="padding: 5px;text-align: center" placeholder="" autocomplete="off" autofocus>
				<div style="margin-top: 20px;text-align: center;">
                    <input type="submit" name="enter" value="Отправить">
                </div>
			</div>
		</form>
	</div>
</div>
