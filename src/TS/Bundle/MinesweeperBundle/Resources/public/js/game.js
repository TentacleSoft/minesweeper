'use strict';

var pollingRate = 1000;

$(document).ready(function () {
    setInterval(function () {
        $.getJSON('/game/0', function (data) {
            var chat = $('#chat');

            chat.html(data.chat).scrollTop(chat[0].scrollHeight);
        });
    }, pollingRate);

    $('.board-cell').click(function () {
        console.log('Click (' + $(this).data('row') + ', ' + $(this).data('col') + ')');
    });
});
