'use strict';

var pollingRate = 1000;

$(document).ready(function () {
    setInterval(function () {
        $.get('/match/0/chat', function (data) {
            var chat = $('#chat');

            chat.html(data).scrollTop(chat[0].scrollHeight);
        });
    }, pollingRate);

    $('.board-cell').click(function () {
        console.log('Click (' + $(this).data('row') + ', ' + $(this).data('col') + ')');
    });
});
