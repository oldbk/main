<table class="table_library" cellspacing="0" cellpadding="0">
<?php
if (isset($Items) && count($Items)) {
    foreach ($Items as $k => $v) {
        echo $CI->renderPartial(
            'common/renderitem', array(
            'Item' => $v,
        ),true
        );
	}
}
?>
</table>