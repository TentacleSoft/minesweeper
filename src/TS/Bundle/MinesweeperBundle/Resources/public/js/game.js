'use strict';

var game = {},
    pollingRate = 1000;

$(document).ready(function () {
    setInterval(function () {
        $.getJSON('/games/1', function (data) {
            if (data.board == 'undefined') {
                return;
            }

            var chat = $('#chat');

            chat.html(data.chat).scrollTop(chat[0].scrollHeight);
            drawBoard(data.board);

            game = data;
        });
    }, pollingRate);

    $('.board-cell').click(function () {
        console.log('Click (' + $(this).data('row') + ', ' + $(this).data('col') + ')');
    });
});

function drawBoard(board) {
    var cells = $('.board-cell');

    for (var pos = 0; pos < cells.length; pos++) {
        var cell = $(cells[pos]),
            cellValue = board[cell.data('row')][cell.data('col')];

        switch (cellValue) {
            case 'M':
                cell.removeClass('enabled').addClass('mine');
                break;
            case '':
                break;
            case 0:
                cell.removeClass('enabled').addClass('open');
                break;
            default: // if it's a number
                cell.removeClass('enabled').addClass('open');
                cell.html(cellValue);
        }
    }
}
