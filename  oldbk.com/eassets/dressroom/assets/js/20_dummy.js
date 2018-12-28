var Items = function () {
    var self = this;
    var list = {};

    self.addItem = function(shop, category, item) {
        if(list[shop] === undefined) {
            list[shop] = {};
        }
        if(list[shop][category] === undefined) {
            list[shop][category] = [];
        }
        list[shop][category].push(item);
    };

    self.getItems = function (shop, category) {
        try {
            return list[shop][category];
        } catch (e) {
            return [];
        }
    };
    self.getItem = function (shop, category, id) {
        var response = null;
        $.each(list[shop][category], function(i, item) {
            if(item.id == id) {
                response = item;

                return false;
            }
        });

        return response;
    };
};
var ItemModel = function(item) {
    var self = this;
    item = item !== undefined ? item : {};

    self.empty_image = item.empty_image !== undefined ? item.empty_image : null;
    self.is_dressed = item.is_dressed !== undefined ? item.is_dressed : false;
    self.start_mod = item.start_mod !== undefined ? item.start_mod : {};
    self.step = item.step !== undefined ? item.step : STEP_BASE_ITEM;
    self.shop = item.shop !== undefined ? item.shop : 0;

    self.id = item.id !== undefined ? item.id : null;
    self.image = item.image !== undefined ? item.image : null;
    self.name = item.name !== undefined ? item.name : null;
    self.durability = item.durability !== undefined ? item.durability : null;
    self.type = item.type !== undefined ? item.type : null;
    self.weight = item.weight !== undefined ? item.weight : 0;
    self.isArt = item.isArt !== undefined ? item.isArt : false;
    self.isUnlim = item.isUnlim !== undefined ? item.isUnlim : false;
    self.isProkat = item.isProkat !== undefined ? item.isProkat : false;

    self.type = item.type !== undefined ? item.type : false;
    self.rareitem = item.rareitem !== undefined ? item.rareitem : false;
    self.letter = item.letter !== undefined ? item.letter : false;
    self.category = item.category !== undefined ? item.category : null;
    self.art = item.art !== undefined ? item.art : {};
    self.section = {
        'give': function() {
            if(self.give().total.stats() > 0 || self.give().total.mf() > 0 || self.give().total.possession() > 0 || self.give().total.m_possession() > 0) {
                return true;
            }

            return false;
        },
        'need': function() {
            if(self.need().total.stats() > 0 || self.need().total.mf() > 0 || self.need().total.possession() > 0 || self.need().total.m_possession() > 0 || self.need().level() > 0) {
                return true;
            }

            return false;
        },
        'limit': function() {
            if(self.info().goden > 0 || self.info().isrep == 0 || self.info().notsell == 1) {
                return true;
            }

            return false;
        },
        'usil': function() {
            var flag = false;
            $.each(_default['name']['increased'], function(i, name) {
                if(self.total.give('increased', name) > 0 || self.give().bonus().ab()[name] > 0) {
                    flag = true;
                    return false;
                }
            });

            return flag;
        },
        'property': function() {
            if(self.u() || self.uu() || self.rareitem || self.type == 27 || self.type == 28 || self.charka().num() > 0 || self.isProkat) {
                return true;
            }

            return false;
        }
    };

    self.rlevel = ko.observable(item.rlevel !== undefined ? item.rlevel : 0);
    self.rlevel.subscribeChanged(function (newValue, oldValue) {
        newValue = parseInt(newValue);
        oldValue = parseInt(oldValue);

        var armor = 0;
        $.each(_default['rune'], function(level, info) {
            if(oldValue < level) {
                return false;
            }
            $.each(info['property'], function(name, value) {
                self.give()[name](self.give()[name]() - value);
            });

            armor += info['armor'];
            if((level == 5 || level == 30) && self.give().bonus().ab()) {
                $.each(self.give().bonus().ab(), function(name, value) {
                    var current = parseInt(self.additional().increased()[name]());
                    self.additional().increased()[name](current - value);
                });
            }
            if(info['m_possession'] > 0){
                $.each(_default['name']['rune_possession'], function(i, name) {
                    var current = parseInt(self.give()[name]());
                    self.give()[name](current - info['m_possession']);
                });
            }
        });
        self.additional().armor().all(self.additional().armor().all() - armor);

        armor = 0;
        $.each(_default['rune'], function(level, info) {
            if(newValue < level) {
                return false;
            }
            $.each(info['property'], function(name, value) {
                self.give()[name](self.give()[name]() + value);
            });

            armor += info['armor'];
            if((level == 5 || level == 30) && self.give().bonus().ab()) {
                $.each(self.give().bonus().ab(), function(name, value) {
                    var current = parseInt(self.additional().increased()[name]());
                    self.additional().increased()[name](current + value);
                });
            }
            if(info['m_possession'] > 0){
                $.each(_default['name']['rune_possession'], function(i, name) {
                    var current = parseInt(self.give()[name]());
                    self.give()[name](current + info['m_possession']);
                });
            }
        });
        self.additional().armor().all(self.additional().armor().all() + armor);

        newValue = parseInt(newValue);
        oldValue = parseInt(oldValue);
        if (newValue < oldValue) {
            var diff = self.total.free.mf() - self.additional().total.mf();
            if (diff < 0) {
                self.additional().remove('mf', (-1) * diff);
            }
            diff = self.total.free.stats() - self.additional().total.stats();
            if (diff < 0) {
                self.additional().remove('stats', (-1) * diff);
            }
        }
    });

    self.u = ko.observable(item.u !== undefined ? item.u : false).watch(function (root, trigger) {
        if (trigger() == false) {
            if (self.total.free.stats() - self.additional().total.stats() <= 0) {
                Logger.debug('Сбросили У. Снимаем 1 стат');
                self.additional().remove('stats', 1);
            }
            if (self.uu()) {
                self.uu(false);
            }
        }
    });
    self.uu = ko.observable(item.uu !== undefined ? item.uu : false).watch(function (root, trigger) {
        if (trigger() == false && (self.total.free.stats() - self.additional().total.stats()) <= 0) {
            Logger.debug('Сбросили УУ. Снимаем 1 стат');
            self.additional().remove('stats', 1);
        }
    });

    self.need = ko.observable(new ParamModel(item.need));
    self.give = ko.observable(new ParamModel(item.give));
    self.additional = ko.observable(new ParamModel(item.additional));
    $.each(['stats', 'mf', 'm_possession'], function(i, type) {
        $.each(_default['name'][type], function (i, name) {
            self.additional()[name].subscribe(function (newValue) {
                if (self.total.free[type]() >= self.additional().total[type]()) {
                    return;
                }
                var diff = parseInt(self.additional().total[type]()) - self.total.free[type]();
                newValue = parseInt(newValue) - diff;
                if(isNaN(newValue)) {
                    newValue = 0;
                }
                self.additional()[name](newValue);
            });
        });
    });


    self.price = ko.observable(new ItemPriceModel(item.price !== undefined ? item.price : {}));
    self.info = ko.observable(new ItemInfoModel(item.info !== undefined ? item.info : {}));

    var modf_info = item.modf_info !== undefined ? item.modf_info : {};
    modf_info['category'] = self.category;
    self.modf_info = ko.observable(new ItemMfModel(modf_info)).watch(function () {
        if (!self.modf_info().isMF()) {
            if (self.u() != false) {
                self.u(false);
            }
            if (self.podgon().num() > 0) {
                self.podgon().num(0);
            }
            if (self.charka().num() > 0) {
                self.charka().num(0);
            }
        }
    });

    var podgon = item.podgon !== undefined ? item.podgon : {};
    podgon['category'] = self.category;
    self.podgon = ko.observable(new ItemPodgonModel(podgon));
    self.podgon().num.subscribeChanged(function (newValue, oldValue) {
        newValue = parseInt(newValue);
        oldValue = parseInt(oldValue);
        if (newValue < oldValue) {
            var diff = self.total.free.mf() - self.additional().total.mf();
            if (diff < 0) {
                self.additional().remove('mf', (-1) * diff);
            }
        }
    });

    var charka = item.charka !== undefined ? item.charka : {};
    charka['category'] = self.category;
    charka['level'] = self.need().level();
    self.charka = ko.observable(new ItemCharkaModel(charka));

    var art_usil = item.art_usil !== undefined ? item.art_usil : {};
    self.art_usil = ko.observable(new ItemArtUsilModel(art_usil));

    var art_level = item.art_level !== undefined ? item.art_level : {};
    art_level['level'] = self.need().level();
    self.art_level = ko.observable(new ItemArtLevelModel(art_level, self));
    self.art_level().numSubscribe.subscribeChanged(function (newValue, oldValue) {
        newValue = parseInt(newValue);
        oldValue = parseInt(oldValue);
        if (newValue < oldValue) {
            var diff = self.total.free.mf() - self.additional().total.mf();
            if (diff < 0) {
                self.additional().remove('mf', (-1) * diff);
            }

            diff = self.total.free.stats() - self.additional().total.stats();
            if (diff < 0) {
                self.additional().remove('stats', (-1) * diff);
            }
        }
    });

    var sharpen = item.sharpen !== undefined ? item.sharpen : {};
    sharpen['category'] = self.category;
    self.sharpen = ko.observable(new ItemSharpenModel(sharpen));

    var access = item.access !== undefined ? item.access : {};
    access['itemclass'] = self.need().class();
    self.access = ko.observable(new ItemAccessModel(access));

    self.getImage = function () {
        if (self.image) {
            return self.image;
        }

        return self.empty_image;
    };
    self.base = {
        'give': function(param) {
            var value = parseInt(self.give()[param]());
            value += parseInt(self.modf_info().give()[param]());
            value += parseInt(self.charka().give()[param]());

            if(self.isArt) {
                value += parseInt(self.art_usil().give()[param]());
            }

            return value;
        }
    };
    self.total = {
        'durability': function() {
            var value = parseInt(self.durability());
            if(self.isArt) {
                value += self.art_level().give().durability();
            }

            return value;
        },
        'need': function (param) {
            var value = parseInt(self.need()[param]());
            value += parseInt(self.sharpen().need()[param]());

            if(self.isArt == true) {
                switch (param) {
                    case 'level':
                        if(value < parseInt(self.art_level().need()[param]())) {
                            value = parseInt(self.art_level().need()[param]());
                        }
                        break;
                    default:
                        value += parseInt(self.art_level().need()[param]());
                        break;
                }
            }

            return value;
        },
        'give': polymorph(
            function (param) {
                var value = parseInt(self.base.give(param));
                value += parseInt(self.additional()[param]());
                value += parseInt(self.podgon().give()[param]());
                value += parseInt(self.sharpen().give()[param]());

                if(self.isArt == true) {
                    value += parseInt(self.art_level().give()[param]());
                }

                return value;
            },
            function (subcat, param) {
                var value = parseInt(self.give()[subcat]()[param]());
                value += parseInt(self.additional()[subcat]()[param]());
                value += parseInt(self.podgon().give()[subcat]()[param]());
                value += parseInt(self.sharpen().give()[subcat]()[param]());
                value += parseInt(self.modf_info().give()[subcat]()[param]());
                value += parseInt(self.charka().give()[subcat]()[param]());

                if(self.isArt == true) {
                    value += parseInt(self.art_usil().give()[subcat]()[param]());
                    value += parseInt(self.art_level().give()[subcat]()[param]());
                }

                return value;
            }
        ),
        'free': {
            'mf': function () {
                var value = parseInt(self.give().free().mf());

                if(self.isArt == false || self.step == STEP_CREATE_ART) {
                    value += parseInt(self.podgon().give().free().mf());
                }
                if(self.art.hram == true) {
                    value += parseInt(self.art_level().give().free().mf());
                }

                if(self.rlevel() > 0) {
                    $.each(_default['rune'], function(level, info) {
                        if(self.rlevel() < level) {
                            return false;
                        }
                        value += info['mf'];
                    });
                }

                return value;
            },
            'm_possession': function() {
                return self.give().free().m_possession();
            },
            'stats': function () {
                var value = self.give().free().stats();
                if (self.u() && (self.isArt == false || (self.step == STEP_CREATE_ART && _default['name']['possession'].indexOf(self.category) < 0)) && self.category != 'flowers' && (self.shop != 6 || _default['shop']['fair']['exclude'].indexOf(self.category) > -1)) {
                    value += 1;
                }
                if (self.uu() && self.category != 'flowers' && (self.shop != 6 || _default['shop']['fair']['exclude'].indexOf(self.category) > -1)) {
                    value += 1;
                }
                if(self.isArt == true) {
                    value += parseInt(self.art_level().give().free().stats());
                }
                if(self.rlevel() > 0) {
                    $.each(_default['rune'], function(level, info) {
                        if(self.rlevel() < level) {
                            return false;
                        }

                        value += info['stat'];
                    });
                }

                return value;
            }
        }
    };
    self.have = {
        'stats': function () {
            var isStats = false;
            $.each(_default['name']['stats'], function (i, name) {
                if (self.give()[name]() > 0) {
                    isStats = true;
                    return false;
                }
            });

            return isStats;
        },
        'mf': function () {
            var isMF = false;
            $.each(_default['name']['mf'], function (i, name) {
                if (self.give()[name]() > 0) {
                    isMF = true;
                    return false;
                }
            });

            return isMF;
        }
    };
    self.getPrice = function (formatted) {
        var price = self.price().getPrice();
        if(self.isArt == false) {
            if (self.modf_info().isMF()) {
                price += Math.round(price / 2);
            }

            price = self.podgon().getPrice(price);
            price += self.sharpen().getPrice();
            if (self.u()) {
                price += 2500;
            }
            if (self.uu()) {
                price += 2500;
            }
        }

        if (formatted !== undefined) {
            var msg = 'Цена: ';
            switch (true) {
                case (self.price().gold() > 0):
                    msg += price + '  <img src=https://i.oldbk.com/i/icon/coin_icon.png>';
                    break;
                case (self.price().rep() > 0):
                    msg += price + ' реп.';
                    break;
                case (self.price().ekr() > 0):
                    msg += price + ' екр.';
                    break;
                default:
                    msg += price + ' кр.';
                    break;
            }

            return msg;
        }

        return price;
    };
    self.mf = function (status) {
        var hp = 20;
        var stats = 2;
        var armor = 3;
        if (status == false) {
            hp = 0;
            stats = 0;
            armor = 0;
        }

        self.modf_info().isMF(status);
        if (self.total.give('hp')) {
            self.modf_info().hp(hp);
            self.modf_info().give().hp(hp);
        }
        if (self.have.stats()) {
            self.modf_info().stats(stats);
            self.modf_info().give().free().stats(stats);
        }
        var setArmor = false;
        $.each(_default['name']['armor'], function (i, name) {
            if (self.give().armor()[name]() > 0) {
                self.modf_info().give().armor()[name](armor);
                setArmor = true;
            }
        });
        self.modf_info().armor(setArmor ? armor : 0);
    };

    self.can = {
        'save': function() {
            //есть доступные статы
            console.log('save 1');
            if (self.total.free.stats() - self.additional().total.stats() > 0) {
                return false;
            }
            //есть доступны МФ
            console.log('save 2');
            if (self.total.free.mf() - self.additional().total.mf() > 0) {
                return false;
            }
            //есть доступные статы после МФ
            console.log('save 3');
            if(self.modf_info().stats() - self.modf_info().give().total.stats() > 0) {
                return false;
            }
            //есть доступные статы после чарки
            console.log('save 4');
            if(self.charka().free.stats() > 0) {
                return false;
            }
            //есть доступные мф после чарки
            console.log('save 5');
            if(self.charka().free.mf() > 0) {
                return false;
            }
            //есть доступные владения после чарки
            console.log('save 6');
            if(self.charka().free.possession() > 0) {
                return false;
            }
            //есть доступные владения магией после чарки
            console.log('save 7');
            if(self.charka().free.m_possession() > 0) {
                return false;
            }

            if(self.art.lichka) {
                console.log('save 8');
                if(self.access().can.usil() != 1) {
                    return false;
                }
                console.log('save 9');
                if(self.access().can.mf() != 2) {
                    return false;
                }
                console.log('save 10');
                if(self.access().can.stats() != 2) {
                    return false;
                }
                console.log('save 11');
                if(self.art_usil().alldone() == false) {
                    return false;
                }
            }

            console.log('save 12');
            return true;
        },
        'modf': function() {
            return self.modf_info().can();
        },
        'podgon': function() {
            return self.podgon().can();
        },
        'charka': function() {
            return self.charka().can() && !self.isProkat && self.shop != 6;
        },
        'sharpen': function() {
            return self.sharpen().can();
        },
        'up_stat': function(name) {
            return self.total.free.stats() > 0 && (self.isArt == false || self.access().stats[name]() == true);
        },
        'up_mf': function(name) {
            return self.total.free.mf() > 0 && (self.isArt == false || self.access().modf[name]() == true);
        },
        'prokat': function() {
            return _default['prokat']['can'].indexOf(self.category) > -1;
        }
    };
    self.canChange = {
        'charka': function() {
            if(self.isProkat) {
                return false;
            }

            return true;
        },
        'modf': function() {
            if(self.isProkat) {
                return false;
            }

            return true;
        },
        'podgon': function() {
            if(self.isProkat) {
                return false;
            }

            return true;
        },
        'u': function() {
            if(self.isProkat || self.access().u == false) {
                return false;
            }
            if(self.shop == 6 && _default['shop']['fair']['exclude'].indexOf(self.category) < 0) {
                return false;
            }

            return true;
        },
        'uu': function() {
            if(self.isProkat || self.access().uu == false) {
                return false;
            }
            if(self.shop == 6 && _default['shop']['fair']['exclude'].indexOf(self.category) < 0) {
                return false;
            }

            return true;
        }
    };

    self.setArt = function() {
        self.access().mf       = false;
        self.access().u        = false;
        self.access().podgon   = false;

        if(self.modf_info().can()) {
            self.mf(true);

            if(self.have.mf()) {
                self.podgon().num(5);
            }
        }
        if(self.have.stats()) {
            self.u(true);
        }
        self.art.lichka = true;

        self.additional().armor().all(13);
        self.step = STEP_CREATE_ART;
        self.price().rep(300000);

        self.isArt = true;
        self.access().usil.damage.subscribe(function(newValue) {
            if(newValue == true) {
                self.give().increased().damage(self.give().increased().damage() + 1);
                self.give().increased().mf(self.give().increased().mf() + 3);
            } else {
                self.give().increased().damage(self.give().increased().damage() - 1);
                self.give().increased().mf(self.give().increased().mf() - 3);
            }
        });
        self.access().usil.armor.subscribe(function(newValue) {
            if(newValue == true) {
                self.give().increased().armor(self.give().increased().armor() + 10);
            } else {
                self.give().increased().armor(self.give().increased().armor() - 10);
            }
        });
        self.access().usil.mf.subscribe(function(newValue) {
            if(newValue == true) {
                self.give().increased().mf(self.give().increased().mf() + 5);
            } else {
                self.give().increased().mf(self.give().increased().mf() - 5);
            }
        });
    };
    self.setProkat = function() {
        self.isProkat = true;

        if(self.modf_info().can()) {
            self.mf(true);

            if(self.have.mf()) {
                self.podgon().num(5);
            }
        }
        if(self.have.stats()) {
            self.u(true);
            self.uu(true);
        }
        var hp = 35;
        var mf = 60;
        var stats = 5;
        if(self.need().level() == 7) {
            mf = 50;
            stats = 4;
        }

        self.give().hp(self.give().hp() + hp);
        self.give().free().mf(self.give().free().mf() + mf);
        self.give().free().stats(self.give().free().stats() + stats);
        $.each(['possession', 'rune_possession'], function(i, name) {
            $.each(_default['name'][name], function(j, param_name) {
                self.give()[param_name](self.give()[param_name]() + 2);
            });
        });

    };
    if(self.start_mod.isMF == true) {
        self.mf(true);
    };
    if(self.start_mod.u == true) {
        self.u(true);
    };
    if(self.start_mod.uu == true) {
        self.uu(true);
    };

    self.changeArtHP = function(type) {
        if(type == 'plus') {
            self.additional().armor().all(self.additional().armor().all() - 1);
            self.additional().hp(self.additional().hp() + 5);
        } else {
            self.additional().armor().all(self.additional().armor().all() + 1);
            self.additional().hp(self.additional().hp() - 5);
        }
    };
};
var ParamModel = function(params) {
    var self        = this;
    params = params !== undefined ? params : {};

    self.durability = ko.observable(params.durability !== undefined ? params.durability : 0);

    self.level      = ko.observable(params.level !== undefined ? params.level : 0);
    self.up         = ko.observable(params.up !== undefined ? params.up : 0);
    self.align      = ko.observable(params.align !== undefined ? params.align : 0);
    self.gender     = ko.observable(params.gender !== undefined ? params.gender : 0);
    self.needident  = ko.observable(params.needident !== undefined ? params.needident : 0);
    self.class      = ko.observable(params.class !== undefined ? params.class : 0);

    self.strange    = ko.observable(params.strange !== undefined ? params.strange : 0);
    self.agility    = ko.observable(params.agility !== undefined ? params.agility : 0);
    self.intuition  = ko.observable(params.intuition !== undefined ? params.intuition : 0);
    self.endurance  = ko.observable(params.endurance !== undefined ? params.endurance : 0);
    self.intellect  = ko.observable(params.intellect !== undefined ? params.intellect : 0);
    self.wisdom     = ko.observable(params.wisdom !== undefined ? params.wisdom : 0);

    self.knife      = ko.observable(params.knife !== undefined ? params.knife : 0);
    self.ax         = ko.observable(params.ax !== undefined ? params.ax : 0);
    self.sword      = ko.observable(params.sword !== undefined ? params.sword : 0);
    self.baton      = ko.observable(params.baton !== undefined ? params.baton : 0);

    self.fire       = ko.observable(params.fire !== undefined ? params.fire : 0);
    self.water      = ko.observable(params.water !== undefined ? params.water : 0);
    self.earth      = ko.observable(params.earth !== undefined ? params.earth : 0);
    self.air        = ko.observable(params.air !== undefined ? params.air : 0);
    self.grey       = ko.observable(params.grey !== undefined ? params.grey : 0);
    self.light      = ko.observable(params.light !== undefined ? params.light : 0);
    self.dark       = ko.observable(params.dark !== undefined ? params.dark : 0);

    self.hp         = ko.observable(params.hp !== undefined ? params.hp : 0);
    self.mp         = ko.observable(params.mp !== undefined ? params.mp : 0);

    self.min_damage = ko.observable(params.min_damage !== undefined ? params.min_damage : 0);
    self.max_damage = ko.observable(params.max_damage !== undefined ? params.max_damage : 0);

    self.critical   = ko.observable(params.critical !== undefined ? params.critical : 0);
    self.p_critical = ko.observable(params.p_critical !== undefined ? params.p_critical : 0);
    self.flee       = ko.observable(params.flee !== undefined ? params.flee : 0);
    self.p_flee     = ko.observable(params.p_flee !== undefined ? params.p_flee : 0);

    self.bonus      = ko.observable(new ItemBonusModel(params.bonus !== undefined ? params.bonus : {}));
    self.armor      = ko.observable(new ItemArmorModel(params.armor !== undefined ? params.armor : {}));
    self.increased  = ko.observable(new ItemIncreasedModel(params.increased !== undefined ? params.increased : {}));
    self.free       = ko.observable(new ItemFreeModel(params.free !== undefined ? params.free : {}));

    self.total      = {
        'stats' : function() {
            var value = 0;
            $.each(_default['name']['stats'], function(i, name) {
                value += parseInt(self[name]());
            });

            return value;
        },
        'mf' : function() {
            var value = 0;
            $.each(_default['name']['mf'], function(i, name) {
                value += parseInt(self[name]());
            });

            return value;
        },
        'm_possession' : function() {
            var value = 0;
            $.each(_default['name']['m_possession'], function(i, name) {
                value += parseInt(self[name]());
            });

            return value;
        },
        'possession' : function() {
            var value = 0;
            $.each(_default['name']['possession'], function(i, name) {
                value += parseInt(self[name]());
            });

            return value;
        },
        'other' : function() {
            var value = 0;
            $.each(['mp','hp'], function(i, name) {
                value += parseInt(self[name]());
            });

            return value;
        }
    };
    self.remove = function(type, count) {
        $.each(_default['name'][type], function(i, name) {
            var value = parseInt(self[name]());
            if(value > 0) {
                if(value - count >= 0) {
                    self[name](value - count);
                    return false;
                } else {
                    count -= value;
                    self[name](0);
                }
            }
        });
    };

    self.className = function () {
        return _default['name']['class'][self.class()];
    };

    return self;
};

var ItemAccessModel = function(params) {
    var self = this;
    params = params !== undefined ? params : {};

    self.mf         = params.mf !== undefined ? params.mf : true;
    self.podgon     = params.podgon !== undefined ? params.podgon : true;
    self.sharpen    = params.sharpen !== undefined ? params.sharpen : true;
    self.u          = params.u !== undefined ? params.u : true;
    self.uu         = params.uu !== undefined ? params.uu : true;
    self.charka     = params.charka !== undefined ? params.charka : true;
    self.itemclass  = params.itemclass !== undefined ? params.itemclass : true;
    self.art_usil   = params.art_usil !== undefined ? params.art_usil : true;

    self.stats      = {
        'strange'   : ko.observable(params.stats !== undefined && params.stats.strange !== undefined ? params.stats.strange : false),
        'agility'   : ko.observable(params.stats !== undefined && params.stats.agility !== undefined ? params.stats.agility : false),
        'intuition' : ko.observable(params.stats !== undefined && params.stats.intuition !== undefined ? params.stats.intuition : false),
        'intellect' : ko.observable(params.stats !== undefined && params.stats.intellect !== undefined ? params.stats.intellect : false),
        'wisdom'    : ko.observable(params.stats !== undefined && params.stats.wisdom !== undefined ? params.stats.wisdom : false)
    };
    self.modf       = {
        'critical'   : ko.observable(params.modf !== undefined && params.modf.critical !== undefined ? params.modf.critical : false),
        'p_critical' : ko.observable(params.modf !== undefined && params.modf.p_critical !== undefined ? params.modf.p_critical : false),
        'flee'       : ko.observable(params.modf !== undefined && params.modf.flee !== undefined ? params.modf.flee : false),
        'p_flee'     : ko.observable(params.modf !== undefined && params.modf.p_flee !== undefined ? params.modf.p_flee : false)
    };
    self.usil       = {
        'damage'    : ko.observable(params.usil !== undefined && params.usil.damage !== undefined ? params.usil.damage : false),
        'mf'        : ko.observable(params.usil !== undefined && params.usil.mf !== undefined ? params.usil.mf : false),
        'armor'     : ko.observable(params.usil !== undefined && params.usil.armor !== undefined ? params.usil.armor : false)
    };

    self.can = {
        'usil': function() {
            var value = 0;
            $.each(_default['name']['increased'], function(i, name) {
                if(self.usil[name] !== undefined && self.usil[name]() == true) {
                    value++;
                }
            });

            return value;
        },
        'stats': function() {
            var value = 0;
            $.each(_default['name']['stats'], function(i, name) {
                if(self.stats[name] !== undefined && self.stats[name]() == true) {
                    value++;
                }
            });

            return value;
        },
        'mf': function() {
            var value = 0;
            $.each(_default['name']['mf'], function(i, name) {
                if(self.modf[name] !== undefined && self.modf[name]() == true) {
                    value++;
                }
            });

            return value;
        }
    };
    self.checkClass = {
        'stats': function(name) {
            return (_default['art_limit']['stats'][self.itemclass] === undefined || _default['art_limit']['stats'][self.itemclass].indexOf(name) < 0);
        },
        'modf': function(name) {
            return (_default['art_limit']['mf'][self.itemclass] === undefined || _default['art_limit']['mf'][self.itemclass].indexOf(name) < 0);
        }
    };
};
var ItemMfModel = function(params) {
    var self = this;
    self.hp         = ko.observable(params.hp !== undefined ? params.hp : 0);
    self.stats      = ko.observable(params.stats !== undefined ? params.stats : 0);
    self.armor      = ko.observable(params.armor !== undefined ? params.armor : 0);

    self.category   = params.category !== undefined ? params.category : 0;
    self.give       = ko.observable(new ParamModel(params.give !== undefined ? params.give : {}));
    self.isMF       = ko.observable(params.isMF !== undefined ? params.isMF : false).watch(function(newValue) {
        if(newValue == false) {
            $.each(_default['name']['stats'], function(i, name) {
                if(self.give()[name]() > 0) {
                    self.give()[name](0);
                }
            });
        }
    });

    self.can = function() {
        return _default['mf']['can'].indexOf(self.category) > -1;
    };
    $.each(_default['name']['stats'], function (i, name) {
        self.give()[name].subscribe(function (newValue) {
            if (self.stats() >= self.give().total['stats']()) {
                return;
            }
            var diff = self.give().total['stats']() - self.stats();
            newValue = parseInt(newValue) - diff;
            if(isNaN(newValue)) {
                newValue = 0;
            }
            self.give()[name](newValue);
        });
    });
};
var ItemBonusModel = function(params) {
    var self = this;
    params = params !== undefined ? params : {};

    self.craft      = ko.observable(params.craft !== undefined ? params.craft : 0);
    self.craftsu    = ko.observable(params.craftsu !== undefined ? params.craftsu : 0);
    self.elka       = ko.observable(params.elka !== undefined ? params.elka : 0);
    self.exp        = ko.observable(params.exp !== undefined ? params.exp : 0);
    self.ab         = ko.observable(params.ab !== undefined ? params.ab : false);
};
var ItemArmorModel = function(params) {
    var self = this;
    params = params !== undefined ? params : {};

    self.head = ko.observable(params.head !== undefined ? params.head : 0);
    self.body = ko.observable(params.body !== undefined ? params.body : 0);
    self.belt = ko.observable(params.belt !== undefined ? params.belt : 0);
    self.feet = ko.observable(params.feet !== undefined ? params.feet : 0);

    self.all = ko.observable(params.all !== undefined ? params.all : 0);
    self.all.subscribeChanged(function(newValue, oldValue) {
        $.each(_default['name']['armor'], function(i, name) {
            self[name](parseInt(self[name]()) - parseInt(oldValue));
            self[name](parseInt(self[name]()) + parseInt(newValue));
        });
    });
};
var ItemIncreasedModel = function(params) {
    var self = this;
    params = params !== undefined ? params : {};

    self.damage = ko.observable(params.damage !== undefined ? params.damage : 0);
    self.armor  = ko.observable(params.armor !== undefined ? params.armor : 0);
    self.mf     = ko.observable(params.mf !== undefined ? params.mf : 0);
};
var ItemFreeModel = function(params) {
    var self = this;
    params = params !== undefined ? params : {};

    self.stats          = ko.observable(params.stats !== undefined ? params.stats : 0);
    self.mf             = ko.observable(params.mf !== undefined ? params.mf : 0);
    self.possession     = ko.observable(params.possession !== undefined ? params.possession : 0);
    self.m_possession   = ko.observable(params.m_possession !== undefined ? params.m_possession : 0);
};
var ItemPriceModel = function(params) {
    var self = this;
    params = params !== undefined ? params : {};

    self.gold   = ko.observable(params.gold !== undefined ? params.gold : 0).extend({ numeric: 2 });
    self.kr     = ko.observable(params.kr !== undefined ? params.kr : 0).extend({ numeric: 2 });
    self.ekr    = ko.observable(params.ekr !== undefined ? params.ekr : 0).extend({ numeric: 2 });
    self.rep    = ko.observable(params.rep !== undefined ? params.rep : 0).extend({ numeric: 2 });

    self.getPrice = function() {
        switch (true) {
            case (self.gold() > 0):
                return self.gold();
                break;
            case (self.rep() > 0):
                return self.rep();
                break;
            case (self.ekr() > 0):
                return self.ekr();
                break;
            default:
                return self.kr();
                break;
        }
    };
    self.isGold = function() {
        return self.gold() > 0;
    };
    self.isRep = function() {
        return self.gold() == 0 && self.rep() > 0;
    };
    self.isEkr = function() {
        return self.gold() == 0 && self.rep() == 0 && self.ekr() > 0;
    };
    self.isKr = function() {
        return self.gold() == 0 && self.rep() == 0 && self.ekr() == 0;
    };
};
var ItemInfoModel = function(params) {
    var self = this;
    params = params !== undefined ? params : {};

    self.goden      = params.goden !== undefined ? params.goden : 0;
    self.isrep      = params.isrep !== undefined ? params.isrep : 0;
    self.notsell    = params.notsell !== undefined ? params.notsell : 0;
};
var ItemPodgonModel = function(params) {
    var self = this;

    self.num        = ko.observable(params.num !== undefined ? params.num : 0);
    self.category   = params.category !== undefined ? params.category : 0;

    self.give       = ko.observable(new ParamModel(params.give !== undefined ? params.give : 0));
    self.image = function(num) {
        if(!self.can()) {
            return null;
        }

        return basePath + 'dress/podgon/' + self.category + '_' + num + '.gif';
    };
    self.can = function() {
        return _default['podgon']['can'].indexOf(self.category) > -1;
    };
    self.getPrice = function(price) {
        $.each(_default['podgon']['info'], function (num, info) {
            if(num > self.num()) {
                return false;
            }

            price += Math.round(price * info['cost']);
        });

        return price;
    };
    self.num.watch(function(root, trigger) {
        var newValue = trigger();

        self.give().free().mf(0);
        $.each(_default['podgon']['info'], function (num, info) {
            if(num > newValue) {
                return false;
            }

            self.give().free().mf(self.give().free().mf() + info['mf']);
        });
    });
};
var ItemSharpenModel = function(params) {
    var self = this;

    self.num        = ko.observable(params.num !== undefined ? params.num : 0);
    self.cost       = ko.observable(params.cost !== undefined ? params.cost : 0).extend({ numeric: 2 });
    self.category   = params.category !== undefined ? params.category : 0;

    self.give       = ko.observable(new ParamModel(params.give !== undefined ? params.give : 0));
    self.need       = ko.observable(new ParamModel(params.need !== undefined ? params.need : 0));

    self.num.subscribe(function(newValue) {
        $.each(_default['sharpen']['info'][self.category]['give'], function (param, value) {
            self.give()[param](value * newValue);
        });
        $.each(_default['sharpen']['info'][self.category]['need'], function (param, value) {
            self.need()[param](value * newValue);
        });
    });
    self.can = function() {
        return _default['sharpen']['can'].indexOf(self.category) > -1;
    };
    self.image = function(num) {
        if(!self.can()) {
            return null;
        }

        return basePath + 'dress/sharpen/' + self.category + '_' + num + '.gif';
    };
    self.getPrice = function() {
        if(!self.can()) {
            return 0;
        }

        return _default['sharpen']['info'][self.category]['cost'] * self.num();
    };
};
var ItemCharkaModel = function(params) {
    var self = this;

    self.num        = ko.observable(params.num !== undefined ? params.num : 0);
    self.category   = params.category !== undefined ? params.category : 0;
    self.level      = params.level !== undefined ? params.level : 0;

    self.free = {
        'stats': function() {
            return _default['charka']['info'][self.num()]['dynamic']['stats'] - self.give().total.stats();
        },
        'mf': function() {
            return _default['charka']['info'][self.num()]['dynamic']['mf'] - self.give().total.mf();
        },
        'possession': function() {
            return _default['charka']['info'][self.num()]['dynamic']['possession'] - self.give().total.possession();
        },
        'm_possession': function() {
            return _default['charka']['info'][self.num()]['dynamic']['m_possession'] - self.give().total.m_possession();
        }
    };
    self.add = {
        'stats': function(name) {
            var value = _default['charka']['info'][self.num()]['dynamic']['stats'];
            self.give()[name](value);
        },
        'mf': function(name) {
            var value = _default['charka']['info'][self.num()]['dynamic']['mf'];
            self.give()[name](value);
        },
        'possession': function(name) {
            var value = _default['charka']['info'][self.num()]['dynamic']['possession'];
            self.give()[name](value);
        },
        'm_possession': function(name) {
            var value = _default['charka']['info'][self.num()]['dynamic']['m_possession'];
            self.give()[name](value);
        }
    };
    self.take = {
        'stats': function() {
            $.each(_default['name']['stats'], function(i, name) {
                if(self.give()[name]() > 0) {
                    self.give()[name](0)
                }
            });
        },
        'mf': function() {
            $.each(_default['name']['mf'], function(i, name) {
                if(self.give()[name]() > 0) {
                    self.give()[name](0)
                }
            });
        },
        'possession': function() {
            $.each(_default['name']['possession'], function(i, name) {
                if(self.give()[name]() > 0) {
                    self.give()[name](0)
                }
            });
        },
        'm_possession': function() {
            $.each(_default['name']['m_possession'], function(i, name) {
                if(self.give()[name]() > 0) {
                    self.give()[name](0)
                }
            });
        }
    };
    self.can = function() {
        return _default['charka']['can'].indexOf(self.category) > -1 && _default['charka']['info'][1]['min_level'] <= self.level;
    };
    self.checkLevel = function(num) {
        return _default['charka']['info'][num]['min_level'] <= self.level;
    };

    self.give       = ko.observable(new ParamModel(params.give !== undefined ? params.give : 0));
    self.image = function(num) {
        if(!self.can()) {
            return null;
        }

        return basePath + 'dress/charka/' + num + '.gif';
    };
    self.num.watch(function(root, trigger) {
        var value = parseInt(trigger());
        self.give(new ParamModel());

        self.give().hp(_default['charka']['info'][value]['static']['hp']);
    });
};
var ItemArtUsilModel = function(params) {
    var self = this;
    self.levels = [
        {
            'active': ko.observable(true),
            'setup': ko.observable(1)
        },   //0
        {
            'active': ko.observable(params.levels !== undefined ? params.levels[1].active : false).watch(function(root, trigger){if(trigger() == false) {_disable(1);}}),
            'setup': ko.observable(params.levels !== undefined ? params.levels[1].setup : 0)
        },  //1
        {
            'active': ko.observable(params.levels !== undefined ? params.levels[2].active : false).watch(function(root, trigger){if(trigger() == false) {_disable(2);}}),
            'setup': ko.observable(params.levels !== undefined ? params.levels[2].setup : 0)
        },  //2
        {
            'active': ko.observable(params.levels !== undefined ? params.levels[3].active : false).watch(function(root, trigger){if(trigger() == false) {_disable(3);}}),
            'setup': ko.observable(params.levels !== undefined ? params.levels[3].setup : 0)
        },  //3
        {
            'active': ko.observable(params.levels !== undefined ? params.levels[4].active : false).watch(function(root, trigger){if(trigger() == false) {_disable(4);}}),
            'setup': ko.observable(params.levels !== undefined ? params.levels[4].setup : 0)
        },  //4
        {
            'active': ko.observable(params.levels !== undefined ? params.levels[5].active : false).watch(function(root, trigger){if(trigger() == false) {_disable(5);}}),
            'setup': ko.observable(params.levels !== undefined ? params.levels[5].setup : 0)
        },  //5
        {
            'active': ko.observable(params.levels !== undefined ? params.levels[6].active : false).watch(function(root, trigger){ if(trigger() == false) {_disable(6);} }),
            'setup': ko.observable(params.levels !== undefined ? params.levels[6].setup : 0)
        }   //6
    ];
    self.levels[1].setup.subscribeChanged(function(newValue, oldValue) { _change(1, newValue, oldValue) });
    self.levels[2].setup.subscribeChanged(function(newValue, oldValue) { _change(2, newValue, oldValue) });
    self.levels[3].setup.subscribeChanged(function(newValue, oldValue) { _change(3, newValue, oldValue) });
    self.levels[4].setup.subscribeChanged(function(newValue, oldValue) { _change(4, newValue, oldValue) });
    self.levels[5].setup.subscribeChanged(function(newValue, oldValue) { _change(5, newValue, oldValue) });
    self.levels[6].setup.subscribeChanged(function(newValue, oldValue) { _change(6, newValue, oldValue) });
    var _disable = function(num) {
        self.levels[num].setup(false);
        if(num < 6 && self.levels[num+1].active() == true) {self.levels[num+1].active(false);}
    };
    var _change = function(level, item, old_item) {
        var v = 1;
        if(item == false) {
            v = -1;
            item = old_item;
        }

        switch (true) {
            case (level == 1 && (item == 1 || item == 2)):
                var value = _default['art_usil'][level][item]['armor'];
                self.give().armor().all(self.give().armor().all() + (value * v ));

                if(_default['art_usil'][level][item]['hp'] !== undefined) {
                    self.give().hp(self.give().hp() + (_default['art_usil'][level][item]['hp'] * v));
                }
                break;
            case (level == 6):
                $.each(_default['art_usil'][level][item]['increased'], function(name, value) {
                    self.give().increased()[name](self.give().increased()[name]() + (value * v));
                });
                break;
            default:
                $.each(_default['art_usil'][level][item], function(name, value) {
                    self.give()[name](self.give()[name]() + (value * v));
                });
                break;
        }
    };


    self.give       = ko.observable(new ParamModel(params.give !== undefined ? params.give : 0));
    self.image = function(num) {
        return basePath + 'dress/art_usil/' + num + '.gif';
    };
    self.click = function(num) {
        var value = !self.levels[num].active();
        self.levels[num].active(value);
    };
    self.alldone = function() {
        var flag = true;
        $.each(self.levels, function(i, info) {
            if(info.active() == true && info.setup() == false) {
                flag = false;
                return false;
            }
        });

        return flag;
    };
};
var ItemArtLevelModel = function(params, $parent) {
    var self = this;

    self.level = params.level !== undefined ? params.level : 0;

    self.num = ko.observable(params.num !== undefined ? params.num : 0);
    self.numSubscribe = ko.observable(params.numSubscribe !== undefined ? params.numSubscribe : 0);
    self.num.subscribeChanged(function(newValue, oldValue) {
        if(oldValue > 0) {
            self.give(new ParamModel());
            self.need(new ParamModel());
        }

        if(newValue == 0) {
            return false;
        }

        var newValues = _getSetupValue(newValue);
        $.each(newValues['need'], function(name, value) {
            if($parent.need()[name]() > 0) {
                self.need()[name](value);
            }
        });
        $.each(newValues['give'], function(name, value) {
            if($parent.give()[name]() > 0) {
                self.give()[name](value);
            }
        });

        $.each(_default['name']['armor'], function(i, name) {
            if($parent.give().armor()[name]() > 0) {
                self.give().armor()[name](newValues['armor']);
            }
        });

        if(_default['name']['possession'].indexOf($parent.category) < 0) {
            self.give().free().mf(newValues['free']['mf']);
            self.give().free().stats(newValues['free']['stats']);
        }

        self.numSubscribe(parseInt(newValue));
    });

    var _getSetupValue = function(end) {
        var give = {
            'durability' : 0
        };
        var need = {};
        var free = {'mf':0,'stats':0};
        var count = 0;
        var armor = 0;
        $.each(_default['art_level'], function(i, info) {
            if(i > end) {return false;}
            if(i == 0) {return true;}
            if(!self.checkLevel(i)) {
                return true;
            }

            $.each(info['give']['property'], function(name, value) {
                if(give[name] === undefined) {
                    give[name] = 0;
                }
                give[name] += value;
            });
            give['durability'] += info['give']['durability'];

            free.mf += info['give']['free']['mf'];
            free.stats += info['give']['free']['stats'];

            armor += info['give']['armor'];
            need['level'] = info['level'] + 1;
            count++;
        });
        $.each(_default['art_level'][end]['need'], function (name, value) {
            need[name] = value * count;
        });

        return {
            'need': need,
            'give': give,
            'free': free,
            'armor': armor
        };
    };


    self.give       = ko.observable(new ParamModel(params.give !== undefined ? params.give : {}));
    self.need       = ko.observable(new ParamModel(params.need !== undefined ? params.need : {}));
    self.image = function(num) {
        return basePath + 'dress/art_level/' + num + '.gif';
    };
    self.checkLevel = function(num) {
        return _default['art_level'][num]['level'] >= self.level;
    };
    self.click = function(num) {
        if(num == self.num()) {
            if(self.checkLevel(num - 1)) {
                self.num(num - 1);
            } else {
                self.num(0);
            }
        } else {
            self.num(num);
        }
    };
};
var DummyModel = function(params) {
    var self    = this;
    params = params !== undefined ? params : {};

    self.login      = ko.observable(params.login !== undefined ? params.login : login);
    self.own        = ko.observable(new ParamModel(params.own !== undefined ? params.own : _default['own']));
    self.medal202   = ko.observable(params.medal202 !== undefined ? params.medal202 : false);
    self.medal203   = ko.observable(params.medal203 !== undefined ? params.medal203 : false);

    var _mfBase = function(type) {
        var mf;
        var agility = self.total.give('agility');
        var intuition = self.total.give('intuition');

        switch (type) {
            case 'critical':
                mf = self.total.give('critical');

                mf += intuition * 5;
                break;
            case 'p_critical':
                mf = self.total.give('p_critical');

                mf += intuition * 5;
                mf += agility * 2;
                break;
            case 'flee':
                mf = self.total.give('flee');

                mf += agility * 5;
                break;
            case 'p_flee':
                mf = self.total.give('p_flee');

                mf += intuition * 2;
                mf += agility * 5;
                break;
        }

        return mf;
    };
    var _getModfMax = function() {
        var critical = _mfBase('critical');
        var p_critical = _mfBase('p_critical');
        var flee = _mfBase('flee');
        var p_flee = _mfBase('p_flee');
        if(critical >= p_critical && critical >= flee && critical >= p_flee) {
            return 'critical';
        } else if(p_critical >= critical && p_critical >= flee && p_critical >= p_flee) {
            return 'p_critical';
        } else if(flee >= p_critical && flee >= critical && flee >= p_flee) {
            return 'flee';
        } else {
            return 'p_flee';
        }
    };
    self.total = {
        'mf': function(type) {
            var mf = _mfBase(type);
            var _percent = self.total.bonus.mf(type);

            return Math.round(mf + (mf / 100 * _percent));
        },
        'armor': function(type) {
            var armor = self.total.give('armor', type);
            var _percent = self.total.bonus.armor(type);

            return Math.round(armor + (armor / 100 * _percent));
        },
        'armorEff': function(formatted) {
            var level = self.own().level();
            if(level < 7) {
                level = 7;
            } else if(level > 14) {
                level = 14;
            }

            var data = _default['armor_eff'][level];
            var keys = Object.keys(data).sort(function (a, b) {
                return b - a;
            });

            var own_endurance = self.own().endurance();
            var eff = 0;
            $.each(keys, function(i, endurance) {
                if(own_endurance >= endurance) {
                    eff = data[endurance];
                    return false;
                }
            });

            if(formatted == true) {
                return eff + ' - ' + (eff + 20) + '%';
            }
            return null;
        },
        'bonus': {
            'mf': function(type) {
                var _unique = _getUnique();

                var _percent = _unique['value'];
                if(_getModfMax() == type) {
                    _percent += self.total.give('increased', 'mf');
                }

                return _percent;
            },
            'damage': function() {
                var _percent = self.total.give('increased', 'damage');

                return _percent;
            },
            'armor': function() {
                var _percent = self.total.give('increased', 'armor');

                return _percent;
            }
        },
        'give': polymorph(
            function (param) {
                var value = 0;
                switch (param) {
                    case 'hp':
                        value += parseInt(self.own().endurance()) * 6;
                        break;
                }

                if(self.own()[param] === undefined) {
                    return value;
                }
                value += parseInt(self.own()[param]());

                $.each(_default['name']['items'], function(i, item_name) {
                    value += parseInt(self[item_name]().total.give(param));
                });

                return value;
            },
            function (subcat, param) {
                if(self.own()[subcat] === undefined || self.own()[subcat]()[param] === undefined) {
                    return 0;
                }
                var value = parseInt(self.own()[subcat]()[param]());

                $.each(_default['name']['items'], function(i, item_name) {
                    value += parseInt(self[item_name]().total.give(subcat, param));
                });

                return value;
            }
        ),
        'need': function(param) {
            var value = 0;

            $.each(_default['name']['items'], function(i, item_name) {
                if(self[item_name]().total.need(param) > value) {
                    value = self[item_name]().total.need(param);
                }
            });

            return value;
        },
        'price': function(type) {
            var cost = 0;
            $.each(_default['name']['items'], function(i, name) {
                switch (true) {
                    case (type == 'gold' && self[name]().price().isGold()):
                        cost += self[name]().getPrice();
                    case (type == 'rep' && self[name]().price().isRep()):
                        cost += self[name]().getPrice();
                        break;
                    case (type == 'ekr' && self[name]().price().isEkr()):
                        cost += self[name]().getPrice();
                        break;
                    case (type == 'kr' && self[name]().price().isKr()):
                        cost += self[name]().getPrice();
                }
            });

            return cost;
        },
        'uron': function(type) {
            var damage = Math.floor(self.total.give('strange') * 1/3);

            switch (type) {
                case 'min':
                    damage += Math.round(1 + parseInt(self.own().level()) + parseInt(self.total.give('min_damage')) * (1 + 0.07 * parseInt(possession())));
                    break;
                case 'max':
                    damage += Math.round(4 + parseInt(self.own().level()) + parseInt(self.total.give('max_damage')) * (1 + 0.07 * parseInt(possession())));
                    break;
            }

            var _percent = self.total.bonus.damage(type);

            return Math.round(damage + (damage / 100 * _percent));
        }
    };

    self.click = function(category, e) {
        self.hint.hide();
        $filters.category = category;

        if(self[category]().is_dressed || category == 'weapons') {
            var _menu = new DisplayMenu(e);
            _menu.show(category);
        } else {
            var _popup = new DisplayPopup(e);
            _popup.show();
        }
    };
    self.dress = function (category, item) {
        switch (category) {
            case 'ax':
            case 'knife':
            case 'baton':
            case 'sword':
            case 'flowers':
                category = 'weapons';
                break;
        }

        var newItem = ko.toJS(item);
        newItem.is_dressed = true;
        newItem = new ItemModel(newItem);

        if(newItem.isArt && newItem.art.lichka == false) {
            $.each(_default['name']['stats'], function(i, name) {
                if(newItem.give()[name]() > 0) {
                    newItem.access().stats[name](true);
                }
            });
            $.each(_default['name']['mf'], function(i, name) {
                if(newItem.give()[name]() > 0) {
                    newItem.access().modf[name](true);
                }
            });
        }

        self[category](newItem);
    };
    self.dressArt = function(category, item) {
        item.give().free().stats(6);
        item.give().free().mf(70);

        var add_damage = 2;
        if(_default['name']['possession'].indexOf(item.category) >= 0) {
            add_damage = 7;
        }
        item.give().min_damage(item.give().min_damage() + add_damage);
        item.give().max_damage(item.give().max_damage() + add_damage);
        item.step = STEP_BASE_ITEM;

        $.each(['stats', 'mf', 'armor'], function(i, type) {
            $.each(_default['name'][type], function(i, name) {
                if(type == 'armor') {
                    var value = parseInt(item.additional().armor()[name]());
                    if(value > 0) {
                        item.additional().armor()[name](0);
                        item.give().armor()[name](parseInt(item.give().armor()[name]()) + value);
                    }
                } else {
                    var value = parseInt(item.additional()[name]());
                    if(value > 0) {
                        item.additional()[name](0);
                        item.give()[name](parseInt(item.give()[name]()) + value);
                    }
                }
            });
        });

        var value = parseInt(item.additional()['hp']());
        if(value > 0) {
            item.additional()['hp'](0);
            item.give()['hp'](parseInt(item.give()['hp']()) + value);
        }

        self.dress(category, item);
    };
    self.undress = function (category) {
        self[category](_default['items'][convertCategory(category)]);
    };

    var possession = function() {
        switch (self.weapons().category) {
            case 'ax':
            case 'knife':
            case 'baton':
            case 'sword':
                return self.total.give(self.weapons().category);
                break;
            case 'flowers':
                var value = 0;
                $.each(_default['name']['possession'], function(i, name) {
                    if(value < self.total.give(name)) {
                        value = self.total.give(name);
                    }
                });

                return value;
                break;
            default:
                return 0;
        }
    };

    self.minimal = function(param) {
        return _default['level'][self.own().level()]['min'][param] < self.own()[param]();
    };
    self.maximum = function(param) {
        switch (param) {
            case 'level':

                return _default['level'][self.own().level() + 1] !== undefined;
                break;
            case 'up':
                if(_default['level'][self.own().level()] === undefined) {
                    return false;
                }

                return _default['level'][self.own().level()]['up'][self.own().up() + 1] !== undefined;
                break;
        }

        return false;
    };
    self.params = {
        'max': function(type) {
            var stat = 0;
            var possession = 0;

            $.each(_default['level'], function(level, l_info) {
                if(level > self.own().level()) {
                    return false;
                }

                stat += l_info['default']['stat'];
                possession += l_info['default']['possession'];

                $.each(l_info['up'], function(up, u_info) {
                    if(level == self.own().level() && up > self.own().up()) {
                        return false;
                    }

                    stat += u_info['stat'];
                    possession += u_info['possession'];
                })
            });

            return type == 'stat' ? stat : possession;
        },
        'have': function(type) {
            var stat = 0;
            $.each(_default['name']['stats'], function(i, name) {
                stat += parseInt(self.own()[name]());
            });

            var possession = 0;
            $.each(_default['name']['possession'], function(i, name) {
                possession += parseInt(self.own()[name]());
            });
            $.each(_default['name']['m_possession'], function(i, name) {
                possession += parseInt(self.own()[name]());
            });

            return type == 'stat' ? stat : possession;
        }
    };
    self.hint = {
        'object' : null,
        'isActive': function() {
            return !!self.hint.object
        },
        'show': function(category, e) {
            if(self[category]().is_dressed) {
                self.hint.object = new DisplayHint(ko.toJS(self[category]), e);
                self.hint.object.show();
            }
        },
        'hide': function() {
            if(self.hint.isActive()) {
                self.hint.object.destroy();
                self.hint.object = null;
            }
        }
    };
    self.weight = {
        'have': function() {
            var weight = 0;
            $.each(_default['name']['items'], function(i, item) {
                if(self[item]().is_dressed == true) {
                    weight += self[item]().weight;
                }
            });

            return weight;
        },
        'max': function() {
            return self.total.give('strange') * 4;
        }
    };

    self.clearAll = function() {

        clearAll();
        /*self.own().up(0);
        self.own().level(0);

        $.each(_default['name']['items'], function(i, name) {
            self.undress(name);
        });

        $.each(['stats', 'possession', 'm_possession'], function(i, type) {
            $.each(_default['name'][type], function(i, name) {
                self.own()[name](0);
            });
        });*/
    };
    self.makeAllOk = function() {
        $.each(['stats', 'possession', 'm_possession'], function(i, type) {
            $.each(_default['name'][type], function(i, name) {
                var need = self.total.need(name);
                var give = self.total.give(name);
                if(need > give) {
                    var diff = need - give;
                    self.own()[name](self.own()[name]() + diff);
                }
            });
        });
    };
    var _getUnique = function() {
        var u = 0;
        var uu = 0;
        if(self.medal202()) {
            u++;
        }
        if(self.medal203()) {
            uu++;
        }

        var u_bonus = {};
        var uu_bonus = {};
        $.each(_default['name']['items'], function(i, name) {
            if(self[name]().u()) {
                u++;
            }
            if(self[name]().uu()) {
                uu++;
            }
        });
        $.each(_default['unik']['u'], function(i, info) {
            if(info['min'] <= u && u <= info['max']) {
                u_bonus = info;
                return false;
            }

        });
        $.each(_default['unik']['uu'], function(i, info) {
            if(info['min'] <= uu && uu <= info['max']) {
                uu_bonus = info;
                return false;
            }
        });

        if(u_bonus['value'] >= uu_bonus['value'] && u > uu) {
            u_bonus['current'] = u;
            return u_bonus;
        } else {
            uu_bonus['current'] = uu;
            return uu_bonus;
        }
    };
    self.uniqueText = function() {
        var unique = _getUnique();

        return unique['text']+':';
    };
    self.uniqueValue = function() {
        var unique = _getUnique();

        return unique['current'] + '/' + unique['next'];
    };

    self.earrings   = ko.observable(params.earrings !== undefined ? new ItemModel(params.earrings) : _default['items']['earrings']);
    self.tshort     = ko.observable(params.tshort !== undefined ? new ItemModel(params.tshort) : _default['items']['tshort']);
    self.cloak      = ko.observable(params.cloak !== undefined ? new ItemModel(params.cloak) : _default['items']['cloak']);
    self.necklace   = ko.observable(params.necklace !== undefined ? new ItemModel(params.necklace) : _default['items']['necklace']);
    self.weapons    = ko.observable(params.weapons !== undefined ? new ItemModel(params.weapons) : _default['items']['weapons']);
    self.armor      = ko.observable(params.armor !== undefined ? new ItemModel(params.armor) : _default['items']['armor']);
    self.ring1      = ko.observable(params.ring1 !== undefined ? new ItemModel(params.ring1) : _default['items']['ring']);
    self.ring2      = ko.observable(params.ring2 !== undefined ? new ItemModel(params.ring2) : _default['items']['ring']);
    self.ring3      = ko.observable(params.ring3 !== undefined ? new ItemModel(params.ring3) : _default['items']['ring']);
    self.helmet     = ko.observable(params.helmet !== undefined ? new ItemModel(params.helmet) : _default['items']['helmet']);
    self.glove      = ko.observable(params.glove !== undefined ? new ItemModel(params.glove) : _default['items']['glove']);
    self.shield     = ko.observable(params.shield !== undefined ? new ItemModel(params.shield) : _default['items']['shield']);
    self.shoes      = ko.observable(params.shoes !== undefined ? new ItemModel(params.shoes) : _default['items']['shoes']);
    self.rune1      = ko.observable(params.rune1 !== undefined ? new ItemModel(params.rune1) : _default['items']['rune']);
    self.rune2      = ko.observable(params.rune2 !== undefined ? new ItemModel(params.rune2) : _default['items']['rune']);
    self.rune3      = ko.observable(params.rune3 !== undefined ? new ItemModel(params.rune3) : _default['items']['rune']);

    self.art = {
        'check': function() {
            var hram = 0;
            var lichka = 0;
            $.each(_default['name']['items'], function(i, name) {
                if(self[name]().isArt == false) {
                    return true;
                }
                if(self[name]().art.hram == true) {
                    hram++;
                } else if(self[name]().art.lichka == true || self[name]().art.flowers == true) {
                    lichka++;
                }
            });
            if(lichka > 4 || (hram == 2 && lichka > 3)) {
                return false;
            }

            return true;
        },
        'lichka': function() {
            var lichka = 0;
            $.each(_default['name']['items'], function(i, name) {
                if(self[name]().isArt == false) {
                    return true;
                }

                if(self[name]().art.lichka == true || self[name]().art.flowers == true) {
                    lichka++;
                }
            });

            return lichka;
        },
        'hram': function() {
            var hram = 0;
            $.each(_default['name']['items'], function(i, name) {
                if(self[name]().isArt == false) {
                    return true;
                }

                if(self[name]().art.hram == true) {
                    hram++;
                }
            });

            return hram;
        }
    };
    self.prokat = {
        'check': function() {
            var item = self.prokat.itemCount();
            var ring = self.prokat.ringCount();

            if(ring > 1 || item > 3 || (ring + item) > 3) {
                return false;
            }

            return true;
        },
        'itemCount': function() {
            var count = 0;
            $.each(_default['name']['items'], function(i, name) {
                if(self[name]().isProkat == false) {
                    return true;
                }
                if(convertCategory(self[name]().category) != 'ring') {
                    count++;
                }
            });

            return count;
        },
        'ringCount': function() {
            var count = 0;
            $.each(_default['name']['items'], function(i, name) {
                if(self[name]().isProkat == false) {
                    return true;
                }
                if(convertCategory(self[name]().category) == 'ring') {
                    count++;
                }
            });

            return count;
        }
    };
    self.fair = {
        'check': function() {
            var item = self.fair.itemCount();
            var ring = self.fair.ringCount();

            if(ring > 1 || item > 4 || (ring + item) > 4) {
                return false;
            }

            return true;
        },
        'itemCount': function() {
            var count = 0;
            $.each(_default['name']['items'], function(i, name) {
                if(self[name]().shop != 6 || _default['shop']['fair']['exclude'].indexOf(self[name]().category) > -1) {
                    return true;
                }
                if(convertCategory(self[name]().category) != 'ring') {
                    count++;
                }
            });

            return count;
        },
        'ringCount': function() {
            var count = 0;
            $.each(_default['name']['items'], function(i, name) {
                if(self[name]().shop != 6) {
                    return true;
                }
                if(convertCategory(self[name]().category) == 'ring') {
                    count++;
                }
            });

            return count;
        }
    }
};
var Filters = function() {
    var self = this;

    self.init = function () {
        for (var i = 0; i < 15; i++) {
            self.levels.push({'value':i, 'name': 'Уровень: ' + i});
        }
    };

    self.shops = [
        {'value':'shop', 'name': 'Гос. Магазин'},
        {'value':'bereza', 'name': 'Березка'},
        {'value':'hram', 'name': 'Храмовые вещи'},
        {'value':'fair', 'name': 'Ярмарка'}
    ];

    self.classes = [
        {'value':'All', 'name': 'Все'},
        {'value':4, 'name': 'Универсальные'},
        {'value':2, 'name': 'Крит'},
        {'value':1, 'name': 'Уворот'},
        {'value':3, 'name': 'Танк'}
    ];
    self.levels = [{'value':'All', 'name': 'Все'}];

    self.init();

    self.shop       = 'shop';
    self.category   = null;
    self.level      = null;
    self.class      = null;
};
var DisplayMenu = function (e) {
    var self = this;

    self.e = e;

    self.show = function (category) {
        self.destroy();
        var art_count = 0;
        $.each(_default['name']['items'], function(i, name) {
            if(dummy_list[active_room][category]().isArt) {
                art_count++;
            }
        });

        var params = {
            'category': category,
            'isDressed': dummy_list[active_room][category]().is_dressed,
            'canArt': !dummy_list[active_room][category]().isArt && art_count < 4 && dummy_list[active_room][category]().step != STEP_MOD_ITEM && _default['art']['can'].indexOf(category) > -1 && dummy_list[active_room][category]().category != 'flowers' && dummy_list[active_room][category]().shop != 6,
            'canProkat': dummy_list[active_room][category]().can.prokat() && dummy_list[active_room][category]().step != STEP_MOD_ITEM && dummy_list[active_room][category]().need().level() > 6 && dummy_list[active_room][category]().shop != 6,
        };

        $(nunjucks.render('menu.nunj', params)).appendTo('body')
            .css({left : self.e.pageX + 20, top : self.e.pageY})
            .show()
            .on('click', '#undress', function() {
                dummy_list[active_room].undress(category);
                self.destroy();
            })
            .on('click', '#change', function() {
                self.destroy();

                var item = dummy_list[active_room][category]();
                var _popup = new DisplayPopupDetails(item, e);
                _popup.show(category);
            })
            .on('click', '#create-art', function() {
                self.destroy();

                var item = dummy_list[active_room][category]();
                var _popup = new DisplayPopupArt(item, e);
                _popup.show(category);
            })
            .on('click', '#create-prokat', function() {
                self.destroy();

                var item = dummy_list[active_room][category]();
                var _popup = new DisplayPopupDetails(item, e);
                _popup.showProkat(category);
            })
            .on('click', 'li.weapons', function() {
                $filters.category = $(this).data('category');
                self.destroy();

                var _popup = new DisplayPopup(e);
                _popup.show();
            });
    };
    self.destroy = function () {
        if($('body #menu_dressed').length) {
            $('body #menu_dressed').remove();
        }
    };
};
var DisplayPopup = function (e) {
    var self = this;
    var _popup = null;
    var _hint = null;

    self.e = e;

    self.show = function () {
        self.destroy();

        _popup = $(nunjucks.render('popup.nunj', { 'filters': $filters })).appendTo('body')
            .on('change', '[name="filter-city"]', function() {
                $filters.shop = $(this).val();

                _popup.trigger( "filter-change" );
            })
            .on('change', '[name="filter-level"]', function() {
                $filters.level = $(this).val() == 'All' ? null : $(this).val();

                _popup.trigger( "filter-change" );
            })
            .on('change', '[name="filter-class"]', function() {
                $filters.class = $(this).val() == 'All'? null : $(this).val();

                _popup.trigger( "filter-change" );
            })
            .modal('show')
            .on('hidden.bs.modal', function () {
                $(this).data('bs.modal', null).remove();
                _popup = null;
            }).on('filter-change', function() {
                self.drawItems();
            });

        _popup.trigger( "filter-change" );
    };

    self.drawItems = function () {
        var itemToView = [];
        var itemCategory = convertCategory($filters.category);

        var itemList = $items.getItems($filters.shop, itemCategory);
        $.each(itemList, function (i, item) {
            if($filters.level !== null && $filters.level != item.need().level()) {
                return;
            }
            if($filters.class !== null && $filters.class != item.need().class()) {
                return;
            }

            itemToView.push(item);
        });

        var items_view = $(nunjucks.render('list.nunj', { 'items': itemToView, 'filter': $filters }))
            .on('mouseover', 'img.img-details', function(e) {
                var item = $items.getItem($(this).data('shop'), convertCategory($(this).data('category')), $(this).data('id'));
                _hint = new DisplayHint(item, e);
                _hint.show();
            })
            .on('mouseout', 'img.img-details', function() {
                if(_hint) {
                    _hint.destroy();
                    _hint = null;
                }
            })
            .on('click', 'img.img-details', function() {
                var shop = $(this).data('shop');
                var category = $(this).data('category');
                var id = $(this).data('id');

                var item = $items.getItem(shop, convertCategory(category), id);
                dummy_list[active_room].dress(category, item);

                self.destroy();
            });

        $('#choose-item .modal-body').html('').append(items_view);
    };

    self.destroy = function () {
        if(_hint) {
            _hint.destroy();
            _hint = null;
        }
        if(_popup) {
            _popup.modal( 'hide' );
        }
    };
};
var DisplayPopupDetails = function (item, e) {
    var self = this;
    var _popup;

    self.e = e;
    self.item = new ItemModel(ko.toJS(item));

    self.showProkat = function (category) {
        self.item.setProkat();

        self.show(category);
    };
    self.show = function (category) {
        self.destroy();

        _popup = $(nunjucks.render('popup-details.nunj'))
            .appendTo('body')
            .on('click', '#save-change', function() {
                self.item.step = STEP_MOD_ITEM;
                dummy_list[active_room].dress(category, self.item);
                _popup.modal('hide');
            });


        ko.applyBindings(self.item, _popup[0]);

        _popup
            .modal('show')
            .on('hidden.bs.modal', function () {
                self.destroy();
            })
    };

    self.destroy = function () {
        if(_popup) {
            ko.cleanNode(_popup[0]);

            _popup.data( 'bs.modal', null ).remove();
            _popup = null;
        }
    };
};
var DisplayPopupArt = function (item, e) {
    var self = this;
    var _popup;

    self.e = e;
    self.item = new ItemModel(ko.toJS(item));

    self.show = function (category) {
        self.destroy();

        self.item.setArt();

        _popup = $(nunjucks.render('popup-art.nunj'))
            .appendTo('body')
            .on('click', '#save-change', function() {
                dummy_list[active_room].dressArt(category, self.item);
                _popup.modal('hide');

                var _popup2 = new DisplayPopupDetails(self.item, self.e);
                _popup2.show(category);
            });

        ko.applyBindings(self.item, _popup[0]);

        _popup
            .modal('show')
            .on('hidden.bs.modal', function () {
                self.destroy();
            })
    };

    self.destroy = function () {
        if(_popup) {
            ko.cleanNode(_popup[0]);

            _popup.data( 'bs.modal', null ).remove();
            _popup = null;
        }
    };
};
var DisplayHint = function (item, e) {
    var self = this;

    self.item = item;
    self.e = e;

    self.show = function () {
        self.destroy();

        var item_view = $(nunjucks.render('details.nunj', { 'item': ko.toJS(self.item), 'classes': _default['name']['class']})).appendTo($('body'));

        var windowWidth = $(window).width();
        var windowHeight = $(window).height();

        var modalWidth = item_view.outerWidth();
        var modalHeight = item_view.outerHeight();

        var left = (self.e.pageX + modalWidth) < windowWidth ? self.e.pageX + 20 : (self.e.pageX  - modalWidth  - 20);
        var top = (self.e.pageY + modalHeight) < windowHeight ? self.e.pageY : (self.e.pageY - modalHeight);
        if(top < 0) {
            top += modalHeight / 2;
        }

        item_view
            .css({
                'left' : left,
                'top' : top
            }).show();
    };
    self.destroy = function () {
        if($('body .popup-item-details').length) {
            $('body .popup-item-details').remove();
        }
    };
};

_default['items'] = {
    'earrings'  : new ItemModel({'empty_image' : basePath + 'img_stats_1.jpg'}),
    'tshort'    : new ItemModel({'empty_image' : basePath + 'img_stats_2.jpg'}),
    'cloak'     : new ItemModel({'empty_image' : basePath + 'img_stats_3.jpg'}),
    'necklace'  : new ItemModel({'empty_image' : basePath + 'img_stats_4.jpg'}),
    'weapons'   : new ItemModel({'empty_image' : basePath + 'img_stats_5.jpg'}),
    'armor'     : new ItemModel({'empty_image' : basePath + 'img_stats_6.jpg'}),
    'ring'      : new ItemModel({'empty_image' : basePath + 'img_stats_7.jpg'}),
    'helmet'    : new ItemModel({'empty_image' : basePath + 'img_stats_8.jpg'}),
    'glove'     : new ItemModel({'empty_image' : basePath + 'img_stats_9.jpg'}),
    'shield'    : new ItemModel({'empty_image' : basePath + 'img_stats_10.jpg'}),
    'shoes'     : new ItemModel({'empty_image' : basePath + 'img_stats_11.jpg'}),
    'rune'      : new ItemModel()
};

function convertCategory(category) {
    switch (category) {
        case 'ring1':
        case 'ring2':
        case 'ring3':
            return 'ring';
            break;
        case 'rune1':
        case 'rune2':
        case 'rune3':
            return 'rune';
            break;
    }

    return category;
}