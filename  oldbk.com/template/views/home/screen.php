<?php
$screens='<div style="text-align:center;"><table cellSpacing=0 cellPadding=0 width="100%" border=0 style="text-align:center;">';

$ko=1;

for ($imgk=1;$imgk<=25;$imgk+=1)
{
    $close_img=array(2,6,9,12,13,18,19,22,23);
    if (in_array(($imgk),$close_img)) continue;

    if ($ko==1)
    {
        $screens.='<tr style="text-align:center;">';
        $screens.='<td><img src="http://i.oldbk.com/i/slide/sm'.$imgk.'m.jpg" onclick="showBox(\'http://i.oldbk.com/i/slide/sm'.$imgk.'b.jpg\',\'\');"  style="cursor: pointer;"></td>';
        $ko=2;
    }
    else
    {
        $screens.='<td><img src="http://i.oldbk.com/i/slide/sm'.$imgk.'m.jpg" onclick="showBox(\'http://i.oldbk.com/i/slide/sm'.$imgk.'b.jpg\',\'\');"  style="cursor: pointer;"></td>';
        $screens.='</tr>';
        $ko=1;
    }
}
$screens.='</table></div>';

//render_news('Скриншоты', $screens);
?>

<div class="kp-news-item box po-re sh-10">
    <div class="kp-backgr-news po-ab">
        <div class="kp-bk-full im-10 po-ab"></div>
        <div class="kp-bk-top im-11 m-im-100 po-ab"></div>
        <div class="kp-bk-bot im-12 m-im-100 po-ab"></div>
    </div>
    <div class="po-re">
        <div class="kp-news-title po-re">
            <div class="kp-backgr-news-title po-ab">
                <div class="kp-bk-full-tt im-15 po-ab"></div>
                <div class="kp-bk-left-tt im-16 po-ab"></div>
                <div class="kp-bk-right-tt im-17 po-ab"></div>
            </div>
            <div class="oh po-re kp-main-tittle">
                <i class="fa fa-newspaper-o fl po-re" aria-hidden="true"></i>
                <div class="kp-title-news-name fl"><h3>Скриншоты</h3></div>
            </div>
        </div>
        <div class="kp-news-content">
            <?= $screens;?>
        </div>
    </div>
</div>

<script>
    function showBox(imagelink, titeletext) {
        Shadowbox.open({content: imagelink, player: 'img', title: titeletext, width: '920', height: '480'});
    }

    $(document).ready(function () {
        Shadowbox.init();
    });
</script>
