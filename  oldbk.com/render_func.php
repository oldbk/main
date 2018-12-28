<?php

if (!function_exists('nick_render')) {
    function nick_render ($telo) {
        $mm = '';
        if ($telo['align']>0) { $mm .= "<img alt='' src=\"http://i.oldbk.com/i/align_".($telo['align']>0 ? $telo['align']:"0").".gif\">"; }
        if ($telo['klan'] <> '') {	$mm .= '<img alt="'.$telo['klan'].'" title="'.$telo['klan'].'" src="http://i.oldbk.com/i/klan/'.$telo['klan'].'.gif">'; }
        $mm .= "<B>{$telo['login']}</B> [{$telo['level']}]<!--noindex--><a rel='nofollow' href='http://capitalcity.oldbk.com/inf.php?{$telo['id']}' target=_blank><IMG SRC='http://i.oldbk.com/i/inf.gif' WIDTH=12 HEIGHT=11 ALT=\"Инф. о {$telo['login']}\" title=\"Инф. о {$telo['login']}\"></a><!--/noindex-->";

        return $mm;
    }
}

if (!function_exists('clan_render')) {
    function clan_render($clan) {
        $mm = '';
        $mm .= "<img alt='' src=\"http://i.oldbk.com/i/align_".($clan['align']>0 ? $clan['align']:"0").".gif\">";
        $mm .= '<img alt="'.$clan['name'].'" title="'.$clan['name'].'" src="http://i.oldbk.com/i/klan/'.$clan['short'].'.gif">';

        $mm .= "<B>{$clan['short']}</B> <a rel='nofollow' href='http://oldbk.com/encicl/klani/clans.php?clan={$clan['short']}' target=_blank><IMG SRC='http://i.oldbk.com/i/inf.gif' WIDTH=12 HEIGHT=11 ALT=\"Инф. о клане {$clan['short']}\" title=\"Инф. о клане {$clan['short']}\"></a>";

        return $mm;
    }
}

if (!function_exists('order_actions')) {
    function order_actions($arr)
    {
        $arr_out=array();
        $arr_ok=array();
        $nr=0;
        foreach($arr as $k => $vline)
        {
            foreach($vline as $nazv=> $val)
            {
                if ($nazv=='start')
                {
                    $nr++; // +1 секунда для избежания пропаданий акций с одинаковым стартом
                    if ($val=='') $val=time();
                    $arr_out[$val+$nr]=$vline;
                }
            }
        }


        krsort($arr_out); //сортируем



//возвращаем ид на место
        foreach($arr_out as $k => $vline)
        {
            $arr_ok[]=$vline;
        }

        return $arr_ok;
    }
}

if (!function_exists('render_news')) {
    function render_news($title, $text, $dat = null, $ico = null)
    {
        $isTitle = strlen($title) > 0;
        if (!($ico))
            $ico = '<img alt="" src="img/news_ico.png">';

        $html = '<div class="cont_box">
		<div class="cont_box_top">
			<div class="cont_box_bot">';

        if ($isTitle) {
            $html .= '
				<div class="news">';
            $html .= '<div class="title">' . $ico;
            $html .= '<span>' . $title . '</span>';
            if ($dat) {
                $dat = explode(' ', $dat);
                $dat = $dat[0];

		$dat = explode("-",$dat);
                $html .= '<span class="date">' .$dat[2].'-'.$dat[1].'-'.$dat[0]. '</span>';
            }

            $html .= '</div>';
        } else
            $html .= '<div class="info">';

        //$text = preg_replace('~<img[\ ]{1,}src=~iU', '<img src=', $text);
        $text = str_replace('border=0', '', $text);
        $text = str_replace('border="0"', '', $text);
        $text = preg_replace('~<font color="#850404">(.*)</font>~iU', '<span style="color:#850404;margin:0px;padding:0px;">\\1</span>', $text);
        $html .= $text;

        $html .= '</div>
			</div>
		</div>
	</div>';

        echo $html;
    }
}

if (!function_exists('render_kom')) {
    function render_kom($title, $links)
    {

        if (empty($links)) {
            return false;
        }

        $isTitle = strlen($title) > 0;
        if (!isset($ico))
            $ico = '<img alt="" src="img/news_ico.png">';

        $html = '<div class="cont_box">
			<div class="cont_box_top">
				<div class="cont_box_bot_com">';
        if ($isTitle) {
            $html .= '
				<div class="news">';
            $html .= '<div class="title">' . $ico;
            $html .= '<span>' . $title . '</span>';
            if (isset($dat)) {
                $dat = explode(' ', $dat);
                $dat = $dat[0];
                $html .= '<span class="date">' . $dat . '</span>';
            }

            $html .= '</div>';
        } else {
            $html .= '<div class="info">';
        }
        $act_count = count($links);


        $html .= '<div id="slider-wrap">
		<div id="slider">';

        for ($i = 0; $i < $act_count; $i++) {
            $html .= '<div class="slide"><a href="' . (isset($links[$i]['url']) ? $links[$i]['url'] : '') . '" target=_blank><img alt="" src="' . $links[$i]['img'] . '" >';
            if (isset($links[$i]['color'])) {
                $html .= '<span class="cont_box_bot_com_txt1" style="color:' . $links[$i]['color'] . '">' . $links[$i]['title'] . '</span>';
            } else {
                $html .= '<span class="cont_box_bot_com_txt1">' . $links[$i]['title'] . '</span>';
            }
            if (isset($links[$i]['color2'])) {
                $html .= '<span class="cont_box_bot_com_txt2" style="color:' . $links[$i]['color2'] . '">' . $links[$i]['text'] . '</span></a></div>';
            } else {
                $html .= '<span class="cont_box_bot_com_txt2">' . $links[$i]['text'] . '</span></a></div>';
            }
        }

        $html .= '</div>
		</div>
	</div>
			</div>
		</div>
	</div>';

        echo $html;
    }
}

if (!function_exists('render_wars')) {
    function render_wars($wars)
    {

        if (empty($wars)) {
            return false;
        }

        ?>
        <div id=top_war class="war_wrapper">
            <div class="war_head"><div class="war_title">Текущие клановые войны</div></div>
            <div class="war_middle">
                <ul class="war_list">
                    <?

                    $i = 0;
                    foreach ($wars as $war) {

                        $i++;
                        if ($i>3)
                        {
                            $hiddes='class="hidden"><div class="hidden"';
                            $hiddeo='</div>';
                        }
                        else
                        {
                            $hiddes='';
                            $hiddeo='';
                        }

                        $war['agr_txt']=str_replace('и рекруты', ', ', $war['agr_txt']);
                        $war['def_txt']=str_replace('и рекруты', ', ', $war['def_txt']);

                        $war['agr_txt']=preg_replace('/<a .+?clans.+?\/a>/i', '', $war['agr_txt']);
                        $war['def_txt']=preg_replace('/<a .+?clans.+?\/a>/i', '', $war['def_txt']);

                        $war['agr_txt']=str_replace('<img title', '<img alt="" title', $war['agr_txt']);
                        $war['def_txt']=str_replace('<img title', '<img alt="" title', $war['def_txt']);

                        $war['agr_txt']=str_replace('<img src', '<img alt="" src', $war['agr_txt']);
                        $war['def_txt']=str_replace('<img src', '<img alt="" src', $war['def_txt']);

                        $war['agr_txt']=str_replace('<img border=0 src=http://i.oldbk.com/i/inf.gif>', '<img alt="" src=http://i.oldbk.com/i/inf.gif>', $war['agr_txt']);
                        $war['def_txt']=str_replace('<img border=0 src=http://i.oldbk.com/i/inf.gif>', '<img alt="" src=http://i.oldbk.com/i/inf.gif>', $war['def_txt']);


                        $war['agr_txt'] = str_replace('border=0', '', $war['agr_txt']);
                        $war['agr_txt'] = str_replace('border="0"', '', $war['agr_txt']);

                        $war['def_txt'] = str_replace('border=0', '', $war['def_txt']);
                        $war['def_txt'] = str_replace('border="0"', '', $war['def_txt']);

                        echo '  <li '.$hiddes.'><div class="war_left">'.$war['agr_txt'].'</div><div class="war_right">'.$war['def_txt'].'</div><div class="war_icon">'.$hiddeo.'</div></li>';

                    }

                    ?>
                </ul>
            </div>
            <div class="war_bottom">
                <a id="war_toggle" data-type="hidden" href="javascript:void(0)"></a>
            </div>
        </div>
        <script>
            jQuery(function($){
                $(document.body).on('click', '#war_toggle', function(event){
                    var $self = $(this);
                    if($self.attr('data-type') == 'hidden') {
                        $self.attr('data-type', 'visible');
                        $('#top_war .war_list li.hidden .hidden').fadeIn(1500);
                        $('#top_war li.hidden').slideToggle('slow');
                    } else {
                        $self.attr('data-type', 'hidden');
                        $('#top_war .war_list li.hidden .hidden').fadeOut('fast');
                        $('#top_war li.hidden').slideToggle('slow');
                    }
                });
            });

            Element.prototype.remove = function() {
                this.parentElement.removeChild(this);
            };
            NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
                for(var i = this.length - 1; i >= 0; i--) {
                    if(this[i] && this[i].parentElement) {
                        this[i].parentElement.removeChild(this[i]);
                    }
                }
            };
        </script>
        <?
    }
}

