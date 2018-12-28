var notyTheme = 'bootstrap-v4';

function quoteAuthor(target) {

    var author = $('#p' + target + ' .post_author').text().trim().replace(/\s+/g, " ");
    var date = $('#p' + target + ' .date').text();
    var message = $('#p' + target + ' .post_body').html().replace(/(?:<br>)+/g, "\n").replace(/\s{2,}/g, "\n");
    var text = document.F1.text.value;
    var quote = "<blockquote>" + author + " (" + date + ")\n " + message + "</blockquote>";

    text = text.length
        ? text + "\n" + quote + "\n"
        : quote + "\n";

    document.F1.text.value = text;
    document.F1.text.focus();
    return false;

}

function makeAppeal(target, url) {

    var author = $('#p' + target + ' .post_author').html().trim()/*.replace(/\s+/g, " ")*/;
    var date = $('#p' + target + ' .post_date').html()/*.replace(/\s+/g, " ")*/;

    var n = new Noty({
        theme: notyTheme,
        type: 'warning',
        layout: "center",
        closeWith: ['button'],
        text: 'Сообщить о нарушении автора ' + author + ' ' + date + ' ?',
        container: '#p' + target,
        killer: true,
        animation: {
            open: 'animated fadeIn',
            close: 'animated fadeOut'
        },
        buttons: [
            Noty.button('Пожаловаться', 'btn btn-success btn-sm mx-1', function () {
                $.ajax({
                    url: url,
                    dataType: 'json',
                    success: function (data) {

                        new Noty({
                            text: data ? data.text : 'Что-то пошло не так =(',
                            layout: "bottomRight",
                            theme: notyTheme,
                            type: data ? data.type : 'error',
                            timeout: 5000
                        }).show();

                    }
                });

                n.close();
            }, {id: 'button' + target, 'data-status': 'ok'}),

            Noty.button('Отмена', 'btn btn-danger btn-sm mx-1', function () {
                n.close();
            })
        ]
    }).show();
}

function addText(elname, wrap1, wrap2) {

    var txtarea = document.forms['F1'].elements[elname];
    var s = txtarea.value;

    if (wrap1 === '<blockquote>') {
        var selection = String(getSelected()).trim();

        if (selection === '') {
            return new Noty({
                text: "Не выделен текст!\nДля вставки цитаты, сначала выделите на странице нужный текст, а затем нажмите кнопку цитаты.",
                layout: "bottomRight",
                theme: notyTheme,
                type: 'error',
                timeout: 5000
            }).show();
        }

        txtarea.value = s + wrap1 + selection + wrap2 + "\n";
    } else {
        var sel = getSurroundingSelection(txtarea);
        txtarea.value = sel[0] + wrap1 + sel[1] + wrap2 + sel[2];
    }

    txtarea.focus();

}

function getSelected() {
    if (window.getSelection) {
        return window.getSelection();
    }
    else if (document.getSelection) {
        return document.getSelection();
    }
    else if (document.selection) {
        var selection = document.selection.createRange();
        if (selection.text) {
            return selection.text;
        }
    }
    return '';
}

function getSurroundingSelection(textarea) {
    return [
        textarea.value.substring(0, textarea.selectionStart),
        textarea.value.substring(textarea.selectionStart, textarea.selectionEnd),
        textarea.value.substring(textarea.selectionEnd, textarea.value.length)
    ]
}

function getCaretPosition(textarea) {
    return textarea.selectionStart
}

$(function () {
    $(document.body).on('click', '.like-wrapper .like', function () {
        var $self = $(this);
        if ($self.hasClass('not'))
            return;

        if ($self.attr('data-process') === 'true') {
            new Noty({
                text: 'Дождитесь выполнения операции',
                layout: "bottomRight",
                theme: notyTheme,
                type: 'error',
                timeout: 5000
            }).show();
            return;
        }

        var action = $self.hasClass('done') ? 'remove' : 'add';

        $.ajax({
            'url': '/forum/topic/' + $self.data('topic') + '/like/' + action,
            'type': 'get',
            'dataType': 'json',
            'beforeSend': function () {
                $self.attr('data-process', true);
            },
            'success': function (response) {
                if (response.status === 'error') {
                    new Noty({
                        text: response.message,
                        layout: "bottomRight",
                        theme: notyTheme,
                        type: 'error',
                        timeout: 5000
                    }).show();
                }
                else {
                    $self.toggleClass('done');
                    $self.closest('.like-wrapper').find('.count').html(response.count);
                }
            }
        }).done(function () {
            $self.attr('data-process', false);
        });
    });
});