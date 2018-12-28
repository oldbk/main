<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 10/23/18
 * Time: 6:19 PM
 */

namespace components\widgets\progress;


use components\Component\Slim\Slim;

class ProgressBar
{
	public static function widget($duration)
	{
		$app = Slim::getInstance();
		$interval = 0;
		if($duration > 0) {
			$interval = $duration / 100 * 1000;
		}

		$html = <<<EOF
		<div id="progress-bar" class="mx-auto">
                <div id="progress-wrapper">
                    <span class="progress red"></span>
                    <span id="progress" class="progress" style="background-color: green;"></span>
                </div>
        </div>
EOF;


		$js = <<<EOF
    var progressEnd = 100;
	var progressInterval = $interval;
	var progressAt = 0;
	var progressTimer;
    function progress_set(st) {
        document.getElementById('progress').style.width = st ? st + '%' : '1';
    }

    function progress_update() {
        progressAt++;
        if (progressAt > progressEnd) {
            clearTimeout(progressTimer);
            return;
        } else {
        	progress_set(progressAt);
        }
        progressTimer = setTimeout('progress_update()', progressInterval);
    }


    progress_set(0);
    progress_update();
EOF;
		$app->clientScript->registerJsCode($js);

		return $html;
	}
}