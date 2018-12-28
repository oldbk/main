/**
 * Created by me on 24.06.17.
 */
var _ladda = {};
$(function() {
    _ladda = {
        '#btn-clear-all':  Ladda.create( document.querySelector( '#btn-clear-all' ) ),
        '#btn-make-all-ok':  Ladda.create( document.querySelector( '#btn-make-all-ok' ) ),
    };

    $(document.body).on('click', '#btn-save', function () {
        var json = ko.toJSON(dummy_list[active_room]);

        swal({
            title: 'Сохранение комплекта',
            input: 'text',
            inputPlaceholder: 'Введите название комплекта',
            text: 'Вы уверены, что хотите сохранить комплект?',
            type: 'warning',

            confirmButtonText: 'Сохранить',
            confirmButtonColor: '#3085d6',

            showCancelButton: true,
            cancelButtonText: 'Отмена',
            cancelButtonColor: '#d33',


            showLoaderOnConfirm: true,
            preConfirm: function (text) {
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        url : '/encicl/dressroom/save.html',
                        type: 'post',
                        dataType: 'json',
                        data: {'json': json, 'title': text},
                        success : function(response) {
                            if(response.status === 1) {
                                resolve(response.key);

                                $('.sets li.empty').remove();
                                var $li = $('<li>').appendTo($('ul.sets'));
                                $li.html('<a class="close delete" data-code="'+response.key+'" data-title="'+text+'" aria-label="Close"><span aria-hidden="true">&times;</span></a>' +
                                    '<a class="load-set" href="javascript:void(0);" data-code="'+response.key+'">'+text+'</a>');
                            } else {
                                reject('Не удалось сохранить комплект');
                            }
                        }
                    });
                })
            },
            allowOutsideClick: false
        }).then(function(key) {
            swal({
                type: 'success',
                title: 'Комплект сохранен!',
                html: $(nunjucks.render('save.nunj', {'key': key})).html()
            });
        });
    });

    $(document.body).on('click', '#btn-load', function () {
        swal({
            title: 'Загрузить комплект',
            input: 'text',

            confirmButtonText: 'Загрузить',
            confirmButtonColor: '#3085d6',

            showCancelButton: true,
            cancelButtonText: 'Отмена',
            cancelButtonColor: '#d33',

            showLoaderOnConfirm: true,
            preConfirm: function (text) {
                return new Promise(function (resolve, reject) {

                    $.ajax({
                        url : '/encicl/dressroom/load.html',
                        type: 'post',
                        dataType: 'json',
                        data: {'key': text},
                        success : function(response) {
                            if(response.status === 1) {
                                dress(response.data);
                                resolve();
                            } else {
                                reject('Комплект не найден');
                            }
                        }
                    });
                })
            },
            allowOutsideClick: false
        }).then(function () {
            swal({
                type: 'success',
                title: 'Комплект загружен!'
            })
        })
    });

    $(document.body).on('click', '#btn-clear-all', function() {
        var $self = $(this);
        $self.addClass('not-active').blur();

        _ladda['#btn-clear-all'].start();

        dress({});
        setTimeout(function(){
            $self.removeClass('not-active');

            _ladda['#btn-clear-all'].stop();
        }, 1000);
    });

    $(document.body).on('click', '#btn-make-all-ok', function() {
        var $self = $(this);
        $self.addClass('not-active').blur();

        _ladda['#btn-make-all-ok'].start();

        dummy_list[active_room].makeAllOk();
        setTimeout(function(){
            $self.removeClass('not-active');

            _ladda['#btn-make-all-ok'].stop();
        }, 1000);
    });
});

function dress(data)
{
    ko.cleanNode($("#dressroom")[0]);
    dummy_list[active_room] = null;

    dummy_list[active_room] = new DummyModel(data);
    ko.applyBindings(dummy_list[active_room], $("#dressroom")[0]);

    subscribe(dummy_list[active_room]);
}

$(function() {
    $(document.body).on('click', '#cabins li a.title', function(){
        var $self = $(this);
        if($self.hasClass('active')) {
            return;
        }
        active_room = parseInt($self.closest('li').attr('data-index'));

        showCurrent();
    });
    $(document.body).on('click', '#cabins li .delete', function(){
        var $self = $(this);

        var index = parseInt($self.closest('li').attr('data-index'));

        active_room = index - 1;

        dummy_list[index] = null;
        dummy_list.splice( index, 1 );

        $self.closest('li').remove();
        $.each($('#cabins li'), function(i, el) {
            $(el).attr('data-index', i).find('a.title').text('Кабинка ' + (i+1));
        });

        showCurrent();
    });
    $(document.body).on('click', '.btn_nav #add_room', function() {
        if(dummy_list.length == 5) {
            swal(
                'Ошибка...',
                'Нельзя добавить больше 5 кабинок!',
                'error'
            );
            return;
        }


        active_room = dummy_list.length;
        dummy_list[active_room] = new DummyModel();

        var $li = $('<li>', {'data-index': active_room}).appendTo($('ul#cabins'));
        $li.html('<span class="btn_orange"><a href="javascript:void(0);" class="title">Кабинка '+(active_room + 1)+'</a><a href="javascript:void(0);" class="delete"></a></span>');

        showCurrent();
        subscribe(dummy_list[active_room]);
    });
    $(document.body).on('click', '.btn_nav #result', function() {
        $('#content-dummy').hide();
        $('#cabins li').removeClass('active');

        $('#content-result').html($(nunjucks.render('result.nunj', {'dummy_list':dummy_list}))).show();
    });

    $(document.body).on('click', '.load-set', function() {
        var $self = $(this);

        $('.load-set').addClass('not-active');
        $.ajax({
            url : '/encicl/dressroom/load.html',
            type: 'post',
            dataType: 'json',
            data: {'key': $self.attr('data-code')},
            success : function(response) {
                if(response.status === 1) {
                    dress(response.data);
                    swal({
                        type: 'success',
                        title: 'Комплект загружен!'
                    })
                } else {
                    swal(
                        'Ошибка...',
                        'Комплект не найден!',
                        'error'
                    );
                }
            }
        }).always(function() {
            $('.load-set').removeClass('not-active');
        });
    });
    $(document.body).on('click', '.sets .delete', function () {
        var $self = $(this);
        var code = $self.attr('data-code');
        var title = $self.attr('data-title');

        console.log(code, title);

        swal({
            title: 'Удаление комплекта',
            text: 'Вы уверены, что хотите удалить комплект "'+title+'"?',
            type: 'warning',

            confirmButtonText: 'Удалить',
            confirmButtonColor: '#3085d6',

            showCancelButton: true,
            cancelButtonText: 'Отмена',
            cancelButtonColor: '#d33',


            showLoaderOnConfirm: true,
            preConfirm: function () {
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        url : '/encicl/dressroom/drop.html',
                        type: 'post',
                        dataType: 'json',
                        data: {'code': code},
                        success : function(response) {
                            if(response.status === 1) {
                                resolve();

                                $self.closest('li').remove();
                            } else {
                                reject('Не удалось удалить комплект');
                            }
                        }
                    });
                })
            },
            allowOutsideClick: false
        }).then(function() {
            swal({
                type: 'success',
                title: 'Комплект удален!'
            });
        });
    });
});

function showCurrent()
{
    ko.cleanNode($("#dressroom")[0]);
    ko.applyBindings(dummy_list[active_room], $("#dressroom")[0]);

    $('#cabins li').removeClass('active');
    $('li[data-index="'+active_room+'"]').addClass('active');
    $('#content-dummy').show();
    $('#content-result').hide();
}