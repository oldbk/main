<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 29.05.17
 * Time: 17:46
 */
?>

<div id="dressroom">
    <div class="row btn_nav">
        <span id="result" class="btn_grey">
            <img alt="" src="/eassets/dressroom/images/bg_btn_grey_two.gif">
            <a href="javascript:void(0);">Сводная таблица</a>
        </span>
        <span id="add_room" class="btn_grey">
            <img alt="" src="/eassets/dressroom/images/bg_btn_grey_one.gif">
            <a href="javascript:void(0);">Добавить кабинку</a>
        </span>
        <ul id="cabins">
            <li class="active" data-index="0">
                <span class="btn_orange">
                    <a href="javascript:void(0);" class="title">Кабинка 1</a>
                </span>
            </li>
        </ul>
    </div>
    <div class="row" id="content-dummy">
        <div class="col-12 dress-bg-white">
            <div class="row">
                <div class="col-6" id="player">
                    <div class="player-top-info">
                        <h3 data-bind="text: login"></h3>

                        <div class="life">
                            <span class="life-icon"></span>
                            <div class="life-box"></div>
                            <span class="wrap">: <span data-bind="text: total.give('hp')"></span>/<span data-bind="text: total.give('hp')"></span></span>
                        </div>
                        <div class="mana" data-bind="visible: total.give('mp') > 0">
                            <span class="mana-icon"></span>
                            <div class="mana-box"></div>
                            <span class="wrap">: <span data-bind="text: total.give('mp')"></span>/<span data-bind="text: total.give('mp')"></span></span>
                        </div>
                    </div>

                    <div class="player">
                        <div class="stats_1 item" data-bind="click: click('earrings', $event), event: { mouseover: function(data, event) { hint.show('earrings', event); } , mouseout: hint.hide }">
                            <img src="" alt="" data-bind="attr:{src: earrings().getImage()}">
                        </div>
                        <div class="stats_2 item" data-bind="click: click('tshort', $event), event: { mouseover: function(data, event) { hint.show('tshort', event); } , mouseout: hint.hide }">
                            <img src="" alt="" data-bind="attr:{src: tshort().getImage()}">
                        </div>
                        <div class="stats_3 item" data-bind="click: click('cloak', $event), event: { mouseover: function(data, event) { hint.show('cloak', event); } , mouseout: hint.hide }">
                            <img src="" alt="" data-bind="attr:{src: cloak().getImage()}">
                        </div>
                        <div class="stats_4 item" data-bind="click: click('necklace', $event), event: { mouseover: function(data, event) { hint.show('necklace', event); } , mouseout: hint.hide }">
                            <img src="" alt="" data-bind="attr:{src: necklace().getImage()}">
                        </div>
                        <div class="stats_5 item" data-bind="click: click('weapons', $event), event: { mouseover: function(data, event) { hint.show('weapons', event); } , mouseout: hint.hide }">
                            <img src="" alt="" data-bind="attr:{src: weapons().getImage()}">
                        </div>
                        <div class="stats_6 item" data-bind="click: click('armor', $event), event: { mouseover: function(data, event) { hint.show('armor', event); } , mouseout: hint.hide }">
                            <img src="" alt="" data-bind="attr:{src: armor().getImage()}">
                        </div>
                        <div class="stats_7 item" data-bind="click: click('ring1', $event), event: { mouseover: function(data, event) { hint.show('ring1', event); } , mouseout: hint.hide }">
                            <img src="" alt="" data-bind="attr:{src: ring1().getImage()}">
                        </div>
                        <div class="stats_8 item" data-bind="click: click('ring2', $event), event: { mouseover: function(data, event) { hint.show('ring2', event); } , mouseout: hint.hide }">
                            <img src="" alt="" data-bind="attr:{src: ring2().getImage()}">
                        </div>
                        <div class="stats_9 item" data-bind="click: click('ring3', $event), event: { mouseover: function(data, event) { hint.show('ring3', event); } , mouseout: hint.hide }">
                            <img src="" alt="" data-bind="attr:{src: ring3().getImage()}">
                        </div>

                        <div class="player_box">
                            <img src="/eassets/dressroom/images/img_player.jpg" alt="">
                            <div class="cast" data-type="eat"></div>
                            <div class="cast" data-type="duh"></div>
                        </div>

                        <div class="stats_10 item" data-bind="click: click('helmet', $event), event: { mouseover: function(data, event) { hint.show('helmet', event); } , mouseout: hint.hide }">
                            <img src="" alt="" data-bind="attr:{src: helmet().getImage()}">
                        </div>
                        <div class="stats_11 item" data-bind="click: click('glove', $event), event: { mouseover: function(data, event) { hint.show('glove', event); } , mouseout: hint.hide }">
                            <img src="" alt="" data-bind="attr:{src: glove().getImage()}">
                        </div>
                        <div class="stats_12 item" data-bind="click: click('shield', $event), event: { mouseover: function(data, event) { hint.show('shield', event); } , mouseout: hint.hide }">
                            <img src="" alt="" data-bind="attr:{src: shield().getImage()}">
                        </div>
                        <div class="stats_13 item" data-bind="click: click('shoes', $event), event: { mouseover: function(data, event) { hint.show('shoes', event); } , mouseout: hint.hide }">
                            <img src="" alt="" data-bind="attr:{src: shoes().getImage()}">
                        </div>
                        <div class="stats_14" data-bind="click: medal202(medal202() ? false : true)">
                            <img src="https://i.oldbk.com/i/202medal.png" alt="" data-bind="css: medal202() ? 'selected' : ''">
                        </div>
                        <div class="stats_15" data-bind="click: medal203(medal203() ? false : true)">
                            <img src="https://i.oldbk.com/i/203medal.gif" alt="" data-bind="css: medal203() ? 'selected' : ''">
                        </div>
                        <div class="runes">
                            <div class="rune1 item" data-bind="click: click('rune1', $event), event: { mouseover: function(data, event) { hint.show('rune1', event); } , mouseout: hint.hide }">
                                <img src="" alt="" data-bind="attr:{src: rune1().getImage()}, visible: rune1().is_dressed">
                            </div>
                            <div class="rune2 item" data-bind="click: click('rune2', $event), event: { mouseover: function(data, event) { hint.show('rune2', event); } , mouseout: hint.hide }">
                                <img src="" alt="" data-bind="attr:{src: rune2().getImage()}, visible: rune2().is_dressed">
                            </div>
                            <div class="rune3 item" data-bind="click: click('rune3', $event), event: { mouseover: function(data, event) { hint.show('rune3', event); } , mouseout: hint.hide }">
                                <img src="" alt="" data-bind="attr:{src: rune3().getImage()}, visible: rune3().is_dressed">
                            </div>
                        </div>
                    </div>

                    <div class="orange_box">
                        <ul>
                            <li>На <span data-bind="text: own().up"></span> апе [<span data-bind="text: own().level"></span>] уровня Вам доступно <strong data-bind="text: params.max('stat')"></strong> родных статов и владений <strong data-bind="text: params.max('possession')"></strong>.</li>
                            <li>В вашем комплекте используется <span data-bind="text: params.have('stat')"></span> родных статов и владений <span data-bind="text: params.have('possession')"></span>.</li>
                            <li data-bind="visible: params.max('stat') < params.have('stat')">Не хватает статов: <span class="red" data-bind="text: params.have('stat') - params.max('stat')"></span></li>
                            <li data-bind="visible: params.max('possession') < params.have('possession')">Не хватает владений: <span class="red" data-bind="text: params.have('possession') - params.max('possession')"></span></li>
                            <li data-bind="visible: art.check() == false"><span class="red">Надеть можно 4 личных артефакта + 1 храмовый арт или 3 личных артефакта + 2 храмомых артефакта. В вашем комплекте личных артефактов: <span data-bind="text: art.lichka()"></span> и храмовых артефактов: <span data-bind="text: art.hram()"></span></span></li>
                            <li data-bind="visible: prokat.check() == false"><span class="red">Нельзя одновременно надеть более трех предметов обмундирования Прокатной лавки, в том числе больше одного кольца. В вашем комплекте предметов обмундирования Прокатной лавки: <span data-bind="text: prokat.itemCount()"></span>. Колец Прокатной лавки: <span data-bind="text: prokat.ringCount()"></span></li>
                            <li data-bind="visible: fair.check() == false"><span class="red">Нельзя одновременно надеть более четерех предметов обмундирования Ярмарки, в том числе больше одного кольца. В вашем комплекте предметов обмундирования Ярмарки: <span data-bind="text: fair.itemCount()"></span>. Колец Ярмарки: <span data-bind="text: fair.ringCount()"></span></li>
                        </ul>
                    </div>

                    <div id="left_info">
                        <ul class="list_one">
                            <li>
                                <input data-bind="numeric: own().level, value: own().level" type="text" id="level" />
                                <label for="level">Уровень</label>
                                <span class="values">
                                    <span data-bind="text: own().level"></span>, <span data-bind="css: own().level() >= total.need('level') ? 'green' : 'red'">[<span data-bind="text: total.need('level')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().level(own().level() + 1), visible: maximum('level')">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().level(own().level() - 1), visible: own().level() > 0">-</a>
                                </span>
                            </li>
                            <li>
                                <input data-bind="numeric: own().up, value: own().up" id="up" type="text" />
                                <label for="up">Ап</label>
                                <span class="values">
                                    <span data-bind="text: own().up()"></span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().up(own().up() + 1), visible: maximum('up')">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().up(own().up() - 1), visible: own().up() > 0">-</a>
                                </span>
                            </li>
                        </ul>

                        <ul class="list_one">
                            <li>
                                <input data-bind="numeric: own().strange, value: own().strange" id="strange" type="text" />
                                <label for="strange">Сила</label>
                                <span class="values">
                                    <span data-bind="text: total.give('strange')"></span>, <span data-bind="css: total.give('strange') >= total.need('strange') ? 'green' : 'red'">[<span data-bind="text: total.need('strange')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().strange(own().strange() + 1)">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().strange(own().strange() - 1), visible: minimal('strange')">-</a>
                                </span>
                            </li>
                            <li>
                                <input data-bind="numeric: own().agility, value: own().agility" id="agility" type="text" />
                                <label for="agility">Ловкость</label>
                                <span class="values">
                                    <span data-bind="text: total.give('agility')"></span>, <span data-bind="css: total.give('agility') >= total.need('agility') ? 'green' : 'red'">[<span data-bind="text: total.need('agility')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().agility(own().agility() + 1)">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().agility(own().agility() - 1), visible: minimal('agility')">-</a>
                                </span>
                            </li>
                            <li>
                                <input data-bind="numeric: own().intuition, value: own().intuition" id="intuition" type="text" />
                                <label for="intuition">Интуиция</label>
                                <span class="values">
                                    <span data-bind="text: total.give('intuition')"></span>, <span data-bind="css: total.give('intuition') >= total.need('intuition') ? 'green' : 'red'">[<span data-bind="text: total.need('intuition')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().intuition(own().intuition() + 1)">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().intuition(own().intuition() - 1), visible: minimal('intuition')">-</a>
                                </span>
                            </li>
                            <li>
                                <input data-bind="numeric: own().endurance, value: own().endurance" id="endurance" type="text" />
                                <label for="endurance">Выносливость</label>
                                <span class="values">
                                    <span data-bind="text: total.give('endurance')"></span>, <span data-bind="css: total.give('endurance') >= total.need('endurance') ? 'green' : 'red'">[<span data-bind="text: total.need('endurance')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().endurance(own().endurance() + 1)">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().endurance(own().endurance() - 1), visible: minimal('endurance')">-</a>
                                </span>
                            </li>
                            <li>
                                <input data-bind="numeric: own().intellect, value: own().intellect" id="intellect" type="text" />
                                <label for="intellect">Интеллект</label>
                                <span class="values">
                                    <span data-bind="text: total.give('intellect')"></span>, <span data-bind="css: total.give('intellect') >= total.need('intellect') ? 'green' : 'red'">[<span data-bind="text: total.need('intellect')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().intellect(own().intellect() + 1)">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().intellect(own().intellect() - 1), visible: own().intellect() > 0">-</a>
                                </span>
                            </li>
                            <li>
                                <input data-bind="numeric: own().wisdom, value: own().wisdom" id="wisdom" type="text" />
                                <label for="wisdom">Мудрость</label>
                                <span class="values">
                                    <span data-bind="text: total.give('wisdom')"></span>, <span data-bind="css: total.give('wisdom') >= total.need('wisdom') ? 'green' : 'red'">[<span data-bind="text: total.need('wisdom')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().wisdom(own().wisdom() + 1)">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().wisdom(own().wisdom() - 1), visible: own().wisdom() > 0">-</a>
                                </span>
                            </li>

                        </ul>

                        <ul class="list_one">
                            <li>
                                <input data-bind="numeric: own().knife, value: own().knife" id="knife" type="text" />
                                <label for="knife">Ножами и кастетами</label>
                                <span class="values">
                                    <span data-bind="text: total.give('knife')"></span>, <span data-bind="css: total.give('knife') >= total.need('knife') ? 'green' : 'red'">[<span data-bind="text: total.need('knife')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().knife(own().knife() + 1), visible: own().knife() < 5">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().knife(own().knife() - 1), visible: own().knife() > 0">-</a>
                                </span>
                            </li>
                            <li>
                                <input data-bind="numeric: own().ax, value: own().ax" id="ax" type="text" />
                                <label for="ax">Топорами и секирами</label>
                                <span class="values">
                                    <span data-bind="text: total.give('ax')"></span>, <span data-bind="css: total.give('ax') >= total.need('ax') ? 'green' : 'red'">[<span data-bind="text: total.need('ax')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().ax(own().ax() + 1), visible: own().ax() < 5">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().ax(own().ax() - 1), visible: own().ax() > 0">-</a>
                                </span>
                            </li>
                            <li>
                                <input data-bind="numeric: own().baton, value: own().baton" id="baton" type="text" />
                                <label for="baton">Дубины и булавы</label>
                                <span class="values">
                                    <span data-bind="text: total.give('baton')"></span>, <span data-bind="css: total.give('baton') >= total.need('baton') ? 'green' : 'red'">[<span data-bind="text: total.need('baton')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().baton(own().baton() + 1), visible: own().baton() < 5">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().baton(own().baton() - 1), visible: own().baton() > 0">-</a>
                                </span>
                            </li>
                            <li>
                                <input data-bind="numeric: own().sword, value: own().sword" id="sword" type="text" />
                                <label for="sword">Мечами</label>
                                <span class="values">
                                    <span data-bind="text: total.give('sword')"></span>, <span data-bind="css: total.give('sword') >= total.need('sword') ? 'green' : 'red'">[<span data-bind="text: total.need('sword')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().sword(own().sword() + 1), visible: own().sword() < 5">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().sword(own().sword() - 1), visible: own().sword() > 0">-</a>
                                </span>
                            </li>
                        </ul>
                        <ul class="list_one">
                            <li>
                                <input data-bind="numeric: own().fire, value: own().fire" id="fire" type="text" />
                                <label for="fire">Огонь</label>
                                <span class="values">
                                    <span data-bind="text: total.give('fire')"></span>, <span data-bind="css: total.give('fire') >= total.need('fire') ? 'green' : 'red'">[<span data-bind="text: total.need('fire')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().fire(own().fire() + 1)">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().fire(own().fire() - 1), visible: own().fire() > 0">-</a>
                                </span>
                            </li>
                            <li>
                                <input data-bind="numeric: own().water, value: own().water" id="water" type="text" />
                                <label for="water">Вода</label>
                                <span class="values">
                                    <span data-bind="text: total.give('water')"></span>, <span data-bind="css: total.give('water') >= total.need('water') ? 'green' : 'red'">[<span data-bind="text: total.need('water')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().water(own().water() + 1)">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().water(own().water() - 1), visible: own().water() > 0">-</a>
                                </span>
                            </li>
                            <li>
                                <input data-bind="numeric: own().earth, value: own().earth" id="earth" type="text" />
                                <label for="earth">Земля</label>
                                <span class="values">
                                    <span data-bind="text: total.give('earth')"></span>, <span data-bind="css: total.give('earth') >= total.need('earth') ? 'green' : 'red'">[<span data-bind="text: total.need('earth')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().earth(own().earth() + 1)">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().earth(own().earth() - 1), visible: own().earth() > 0">-</a>
                                </span>
                            </li>
                            <li>
                                <input data-bind="numeric: own().air, value: own().air" id="air" type="text" />
                                <label for="air">Воздух</label>
                                <span class="values">
                                    <span data-bind="text: total.give('air')"></span>, <span data-bind="css: total.give('air') >= total.need('air') ? 'green' : 'red'">[<span data-bind="text: total.need('air')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().air(own().air() + 1)">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().air(own().air() - 1), visible: own().air() > 0">-</a>
                                </span>
                            </li>
                            <li>
                                <input data-bind="numeric: own().grey, value: own().grey" id="grey" type="text" />
                                <label for="grey">Серая</label>
                                <span class="values">
                                    <span data-bind="text: total.give('grey')"></span>, <span data-bind="css: total.give('grey') >= total.need('grey') ? 'green' : 'red'">[<span data-bind="text: total.need('grey')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().grey(own().grey() + 1)">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().grey(own().grey() - 1), visible: own().grey() > 0">-</a>
                                </span>
                            </li>
                            <li>
                                <input data-bind="numeric: own().light, value: own().light" id="light" type="text" />
                                <label for="light">Свет</label>
                                <span class="values">
                                    <span data-bind="text: total.give('light')"></span>, <span data-bind="css: total.give('light') >= total.need('light') ? 'green' : 'red'">[<span data-bind="text: total.need('light')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().light(own().light() + 1)">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().light(own().light() - 1), visible: own().light() > 0">-</a>
                                </span>
                            </li>
                            <li>
                                <input data-bind="numeric: own().dark, value: own().dark" id="dark" type="text" />
                                <label for="dark">Тьма</label>
                                <span class="values">
                                    <span data-bind="text: total.give('dark')"></span>, <span data-bind="css: total.give('dark') >= total.need('dark') ? 'green' : 'red'">[<span data-bind="text: total.need('dark')"></span>]</span>
                                </span>
                                <span class="plus-block">
                                    <a href="javascript:void(0)" class="plus" data-bind="click: own().dark(own().dark() + 1)">+</a>
                                </span>
                                <span class="minus-block">
                                    <a href="javascript:void(0)" class="minus" data-bind="click: own().dark(own().dark() - 1), visible: own().dark() > 0">-</a>
                                </span>

                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-6" id="player-sum">
                    <ul class="list_one">
                        <li>
                            <span class="name">Урон: </span>
                            <strong data-bind="text: total.uron('min')"></strong>
                            -
                            <strong data-bind="text: total.uron('max')"></strong>

                            <span class="green" data-bind="visible: total.bonus.damage()">(+<span data-bind="text: total.bonus.damage()"></span>%)</span>
                        </li>
                    </ul>

                    <ul class="list_one">
                        <li>
                            <span class="name" data-bind="text: uniqueText()"></span>
                            <strong data-bind="text: uniqueValue()"></strong>
                        </li>
                    </ul>

                    <ul class="list_one">
                        <li>
                            <span class="name">Мф. критических ударов: </span>
                            <strong data-bind="text: total.mf('critical')"></strong>%
                            <span class="green" data-bind="visible: total.bonus.mf('critical')">(+<span data-bind="text: total.bonus.mf('critical')"></span>%)</span>
                        </li>
                        <li>
                            <span class="name">Мф. против крит. ударов:</span>
                            <strong data-bind="text: total.mf('p_critical')"></strong>%
                            <span class="green" data-bind="visible: total.bonus.mf('p_critical')">(+<span data-bind="text: total.bonus.mf('p_critical')"></span>%)</span>
                        </li>
                        <li>
                            <span class="name">Мф. увертливости: </span>
                            <strong data-bind="text: total.mf('flee')"></strong>%
                            <span class="green" data-bind="visible: total.bonus.mf('flee')">(+<span data-bind="text: total.bonus.mf('flee')"></span>%)</span>
                        </li>
                        <li>
                            <span class="name">Мф. против увертлив.: </span>
                            <strong data-bind="text: total.mf('p_flee')"></strong>%
                            <span class="green" data-bind="visible: total.bonus.mf('p_flee')">(+<span data-bind="text: total.bonus.mf('p_flee')"></span>%)</span>
                        </li>
                    </ul>

                    <ul class="list_one">
                        <li>
                            <span class="name">Усиление урона: </span>
                            <strong data-bind="text: total.give('increased', 'damage')"></strong>%
                        </li>
                        <li>
                            <span class="name">Усиление максимального мф.: </span>
                            <strong data-bind="text: total.give('increased', 'mf')"></strong>%
                        </li>
                        <li>
                            <span class="name">Усиление брони: 	</span>
                            <strong data-bind="text: total.give('increased', 'armor')"></strong>%
                        </li>
                    </ul>

                    <ul class="list_one">
                        <li>
                            <span class="name">Броня головы: </span>
                            <strong data-bind="text: total.armor('head')"></strong>
                            <span class="green" data-bind="visible: total.bonus.armor()">(+<span data-bind="text: total.bonus.armor()"></span>%)</span>
                        </li>
                        <li>
                            <span class="name">Броня корпуса: 	</span>
                            <strong data-bind="text: total.armor('body')"></strong>
                            <span class="green" data-bind="visible: total.bonus.armor()">(+<span data-bind="text: total.bonus.armor()"></span>%)</span>
                        </li>
                        <li>
                            <span class="name">Броня пояса: </span>
                            <strong data-bind="text: total.armor('belt')"></strong>
                            <span class="green" data-bind="visible: total.bonus.armor()">(+<span data-bind="text: total.bonus.armor()"></span>%)</span>
                        </li>
                        <li>
                            <span class="name">Броня ног: 	</span>
                            <strong data-bind="text: total.armor('feet')"></strong>
                            <span class="green" data-bind="visible: total.bonus.armor()">(+<span data-bind="text: total.bonus.armor()"></span>%)</span>
                        </li>
                        <li>
                            <span class="name">Эффективность брони: </span>
                            <strong data-bind="text: total.armorEff(true)"></strong>
                        </li>
                    </ul>
                    <!--<ul class="list_one">
                        <li>
                            <span class="name">Бонус опыта: </span>
                            <strong data-bind="text: total.give('bonus', 'exp')"></strong>%
                        </li>
                    </ul>-->

                    <ul class="list_one navs">
                        <li><a href="javascript:void(0);" class="ladda-button" data-style="expand-left" id="btn-clear-all">Очистить все</a></li>
                        <li><a href="javascript:void(0);" class="ladda-button" data-style="expand-left" id="btn-make-all-ok">Подогнать статы и умения</a></li>
                        <li><a href="javascript:void(0);" id="btn-save">Сохранить комплект</a></li>
                        <li><a href="javascript:void(0);" id="btn-load">Загрузить комплект</a></li>
                        <!--<li><a href="javascript:void(0);" id="loadByNick">Загрузить по персонажу ОлдБК</a></li>-->
                        <!--<li><a href="#" id="copyTab">Дублировать кабинку</a></li>-->
                    </ul>
                    <strong>Сохраненные комлекты</strong>
                    <ul class="list_one<?= isset($_SESSION['uid']) ? ' sets' : '' ?>">
						<?php if($sets): ?>
							<?php foreach ($sets as $set): ?>
                                <li>
                                    <a class="close delete" data-code="<?= $set['code']; ?>" data-title="<?= $set['title'] ?>" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </a>
                                    <a class="load-set" href="javascript:void(0);" data-code="<?= $set['code']; ?>"><?= $set['title'] ?></a>
                                </li>
							<?php endforeach; ?>
						<?php else: ?>
							<?php if(!isset($_SESSION['uid'])): ?>
                                <li>
                                    <em style="color: red;">Для создания и просмотра списка комплектов Примерочной, сохраненных для Вашего персонажа, необходима авторизация в игре.</em>
                                </li>
							<?php else: ?>
                                <li class="empty">Пусто</li>
							<?php endif; ?>
						<?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="content-result" style="display: none">

    </div>
</div>

<script>
    var login = '<?= $user ? $user['login'] : 'Боец' ?>';
    var active_room = 0;
    var dummy_list = [];
    var $filters, $items;
    $(function() {
        $filters = new Filters();
        $items = new Items();
        dummy_list[active_room] = new DummyModel(data);

        $.ajax({
            url : '/encicl/dressroom/items2.html',
            dataType: 'json',
            success : function(response) {
                if(response.status === 1) {
                    $.each(response.items, function(shop, shop_items) {
                        $.each(shop_items, function(category, items) {
                            $.each(items, function (i, item) {
                                var Item = new ItemModel(item);
                                $items.addItem(shop, category, Item);
                            });
                        });
                    });
                }
            }
        });

        ko.applyBindings(dummy_list[active_room], $('#dressroom')[0]);
        subscribe(dummy_list[active_room]);
    });
    var data = <?= $data ? $data : '{}' ?>;
</script>