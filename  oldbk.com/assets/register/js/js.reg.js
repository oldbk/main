var Register = function () {

    //return;
    var handleCufon = function () {
        Cufon.replace("#reg-title");
        Cufon.replace("#areg");
        Cufon.replace(".hh2");
        Cufon.replace(".reg-title");
    };

    var handleValidator = function () {

        $('#register-form').validator({
            disable: true,
            feedback: {
                success: 'text-success',
                error: 'text-danger'
            },
            rules: {
                password: {
                    required: true
                }
            },
            custom: {
                'check-login': function ($el) {

                    var login = $el.val().trim();

                    if (!login.length || login.length > 20 || login.length < 4) {
                        return 'Имя может содержать от 4 до 20 символов';
                    }

                    var reg = /^[a-zA-Zа-яА-Я0-9-][a-zA-Zа-яА-Я0-9_ -]+[a-zA-Zа-яА-Я0-9-]$/;
                    if (reg.test(login) == false) {
                        return "Имя должно состоять только из букв русского либо английского алфавита, цифр, символов '_', '-' и пробела. Имя не может начинаться или заканчиваться пробелом или символом '_'.";
                    }

                    reg = /__/;
                    if (reg.test(login) != false) {
                        return "Не должно присутствовать подряд более 1 символа '_'";
                    }

                    reg = /  /;
                    if (reg.test(login) != false) {
                        return "Не должно присутствовать подряд более 1 символа пробела";
                    }

                    reg = /--/;
                    if (reg.test(login) != false) {
                        return "Не должно присутствовать подряд более 1 символа '-'";
                    }

                    var lastch = 0;
                    var ccount = 0;
                    var bfound = false;

                    for (var i = 0; i < login.length; i++) {
                        if (lastch != login[i]) {
                            lastch = login[i];
                            ccount = 0;
                        } else {
                            ccount++;
                            if (ccount >= 2) {
                                bfound = true;
                                break;
                            }
                        }
                    }

                    if (bfound) {
                        return 'Не должно присутствовать более 3 других одинаковых символов или более 4-х цифр.';
                    }


                    //???
                    /*var reg1 = /[a-zA-Z]/, reg2 = /[а-яА-Я]/;
                     if(reg1.test(login) != false && reg2.test(login) != false) {
                     return 'ya zdes';
                     }*/

                    return false;
                },
                'check-email': function ($el) {
                    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
                    var address = $el.val();
                    if (reg.test(address) == false) {
                        return 'Некорректный e-mail';
                    }
                    return false;
                }
            }
        });

    };

    var handleBtnEvents = function () {

        $('.form-group').on('click', '#checklogin', function (el) {

            el.preventDefault();

            var login = $('#fnlogin').val();

            if (!login || login.length < 4 || login.length > 20) {
                $(el.delegateTarget).find('input').focus();
                return false;
            }

            $.ajax({
                type: "POST",
                dataType: 'json',
                url: '/f/reg/checklogin',
                data: {'login': login},
                success: function (data) {
                    if (data.status) {
                        if (data.exist) {
                            $(el.currentTarget)
                                .removeClass('badge-success')
                                .addClass('badge-danger')
                                .text('Занято. Выберите другой логин и нажмите эту кнопку');

                            $(el.delegateTarget)
                                .find('input')
                                .removeClass('is-valid')
                                .addClass('is-invalid');
                        } else {
                            $(el.currentTarget)
                                .removeClass('badge-danger')
                                .addClass('badge-success')
                                .text('Свободно. Можно продолжить регистрацию.');

                            $(el.delegateTarget)
                                .find('input')
                                .removeClass('is-invalid')
                                .addClass('is-valid');
                        }
                    }
                }
            });
        });
    };

    var handlePreloader = function () {
        var $preloader = $('#page-preloader'), $spinner = $preloader.find('.spinner');
        $spinner.fadeOut();
        $preloader.delay(750).fadeOut('slow');
    };

    var handleEventSubmit = function () {
        window.addEventListener('load', function () {
            var form = document.getElementById('register-form');
            form.addEventListener('submit', function (event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        }, false);
    };


    return {
        init: function () {
            handleCufon();
            // handleEventSubmit();
            handleValidator();
            handleBtnEvents();
            handlePreloader();
        }
    };
}();

Register.init();

