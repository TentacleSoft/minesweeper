'use strict';

var gameId = 1,
    game = {},
    pollingRate = 1000;

$(document).ready(function () {
    setInterval(function () {
        $.getJSON('/games/' + gameId, function (data) {
            updateInfo(data);
        });
    }, pollingRate);

    $('.board-cell.enabled').click(function () {
        if (game.activePlayer != globals.user.id) {
            return;
        }

        var row = $(this).data('row'),
            col = $(this).data('col');
        $.post('/games/' + gameId, {row: row, col: col}, function (data) {
            updateInfo(data);
        });
        console.log('Click (' + row + ', ' + col + ')');
    });

    $('#form-chat').submit(function () {
        var text = $('#text').val();

        if (text != '') {
            $('#text').val('');
            $.post('/games/' + gameId + '/chat', {text: text}, function (data) {
                updateInfo(data);
            });
        }

        return false;
    });
});

function updateInfo(data) {
    if (data.board == 'undefined') {
        return;
    }

    var chat = $('#chat');

    chat.html(data.chat).scrollTop(chat[0].scrollHeight);
    drawBoard(data.board);

    game = data;
}

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
                cell.attr('data-number', cellValue);
                cell.html(cellValue);
        }
    }
}
