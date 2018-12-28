function stateChange() {

    var target = event.target || event.srcElement || event.originalTarget || event.parentNode;

    if (target.value.indexOf('hard') !== -1) {
        hardDelete($("option:selected", target).text(), $(target).val());
        $(target).prop('selectedIndex', 0);
        return;
    }

    if ($(target).val()) {
        if (confirm($("option:selected", target).text() + '?')) {
            location.href = $(target).val();
        } else {
            $(target).prop('selectedIndex', 0);
        }
    }
}

function moderateTopic(target) {

    target = target || event.target || event.srcElement || event.originalTarget;

    if (target.href.indexOf('hard') !== -1) {
        hardDelete(target.textContent, target.href);
        return false;
    }

    if (target.href.length) {
        if (confirm(target.textContent + '?')) {
            location.href = target.href;
        }
    }
}

function hardDelete(title, val, modal) {

    modal = modal || true;

    var n = new Noty({
        theme: notyTheme,
        type: 'error',
        layout: "center",
        closeWith: ['button'],
        text: title + '?',
        killer: true,
        modal: modal,
        animation: {
            open: 'animated fadeIn', // Animate.css class names
            close: 'animated fadeOut' // Animate.css class names
        },
        buttons: [
            Noty.button(title, 'btn btn-danger btn-sm mx-1', function () {

                location.href = val;

            }, {id: 'button1', 'data-status': 'ok'}),

            Noty.button('Отмена', 'btn btn-success btn-sm mx-1', function () {
                n.close();
            })
        ]
    }).show();

}

function writeCommentForPost(title, url, block, invisible, ispaladin) {

    var n = new Noty({
        theme: notyTheme,
        type: 'warning',
        layout: "center",
        closeWith: ['button'],
        text: title + '<form action="' + url + '" method="post">' +
        '<textarea style="width: 100%" name="comment" rows="3" ></textarea>' +
        (ispaladin ? '<div style="text-align: left;"><label><input type="checkbox" name="comment_author[]" value="-2"> Паладин</label></div>' : '') +
        (invisible ? '<div style="text-align: left;"><label><input type="checkbox" name="comment_author[]" value="-1"> Невидимка</label></div>' : '') +
        '</form>',
        container: '#p' + block,
        killer: true,
        animation: {
            open: 'animated fadeIn',
            close: 'animated fadeOut'
        },
        buttons: [
            Noty.button('Добавить', 'btn btn-success btn-sm mx-1', function () {

                if (!$(n.barDom).find('textarea').val().trim()) {
                    alert('Введите текст');
                    return false;
                }

                $(n.barDom).find('form').submit();

                n.close();
            }, {id: 'button' + block, 'data-status': 'ok'}),

            Noty.button('Отмена', 'btn btn-danger btn-sm mx-1', function () {
                n.close();
            })
        ],
        callbacks: {
            onShow: function () {
                $(this.barDom).find('textarea').focus();
            }
        }
    }).show();

}

function editPost(title, url, block) {

    var n = new Noty({
        theme: notyTheme,
        type: 'warning',
        layout: "center",
        closeWith: ['button'],
        text: title + '<form action="' + url + '" method="post"><textarea style="width: 100%;overflow: visible" name="edit_post" rows="5" ></textarea></form>',
        container: '#p' + block,
        killer: true,
        animation: {
            open: 'animated fadeIn', // Animate.css class names
            close: 'animated fadeOut' // Animate.css class names
        },
        buttons: [
            Noty.button('Редактировать', 'btn btn-success btn-sm mx-1', function () {

                if (!$(n.barDom).find('textarea').val().trim()) {
                    alert('Введите текст');
                    return false;
                }

                $(n.barDom).find('form').submit();

            }, {id: 'button' + block, 'data-status': 'ok'}),

            Noty.button('Отмена', 'btn btn-danger btn-sm mx-1', function () {
                n.close();
            })
        ],
        callbacks: {
            onShow: function () {

                var self = this;
                $.ajax({
                    url: url,
                    type: 'get',
                    dataType: 'json',
                    success: function (data) {
                        $(self.barDom).find('textarea').html(data.text);
                        $(self.barDom).find('textarea').focus();
                    }
                });

            }
        }
    }).show();
}

function deletePost(title, url, block, invisible) {

    var n = new Noty({
        theme: notyTheme,
        type: 'warning',
        layout: "center",
        closeWith: ['button'],
        text: title + '<form action="' + url + '" method="post"><textarea style="width: 100%" name="delete_post_reason" rows="3" ></textarea>' +
        '<div style="text-align: left">' +
        // '<input type="checkbox" name="reason_author[]" value="1" checked> Паладин' +
        '<br>' +
        (invisible ? '<input type="checkbox" name="reason_author[]" value="-1"> Невидимка' : '') +
        '</div>' +
        '</form>',
        container: '#p' + block,
        killer: true,
        animation: {
            open: 'animated fadeIn', // Animate.css class names
            close: 'animated fadeOut' // Animate.css class names
        },
        buttons: [
            Noty.button('Удалить', 'btn btn-success btn-sm mx-1', function () {

                $(n.barDom).find('form').submit();

            }, {id: 'button' + block, 'data-status': 'ok'}),

            Noty.button('Отмена', 'btn btn-danger btn-sm mx-1', function () {
                n.close();
            })
        ],
        callbacks: {
            onShow: function () {
                $(this.barDom).find('textarea').focus();
            }
        }
    }).show();

}

$(document).ready(function () {

    $('.dropdownMenuModerate a.dropdown-item').on('click', function (e) {
        e.preventDefault();

        moderateTopic(e.target);
    });

});