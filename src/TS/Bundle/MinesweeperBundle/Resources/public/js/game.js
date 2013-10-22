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
    if ($('<div>').html(data.chat).html() != chat.html()) {
        chat.html(data.chat).scrollTop(chat[0].scrollHeight);
    }

    drawBoard(data.board);

    var scoreboard = $('#scoreboard');
    scoreboard.find('div[data-player="0"] .score').text(data.scores[0]);
    scoreboard.find('div[data-player="1"] .score').text(data.scores[1]);

    scoreboard.find('div[data-player="0"] .username').text(data.players[0].username);
    scoreboard.find('div[data-player="1"] .username').text(data.players[1].username);

    var turn = $('#turn');
    if (data.activePlayer == globals.user.id) {
        turn.text('Your turn');
        if (data.players[0].id == globals.user.id) {
            turn.addClass('player0');
        } else {
            turn.addClass('player1');
        }
    } else {
        if (data.players[0].id == data.activePlayer) {
            turn.text(data.players[0].name + '\'s turn');
        } else {
            turn.text(data.players[1].name + '\'s turn');
        }

        turn.removeClass('player0').removeClass('player1');
    }

    game = data;
}

function drawBoard(board) {
    var cells = $('.board-cell');

    for (var pos = 0; pos < cells.length; pos++) {
        var cell = $(cells[pos]),
            cellValue = board[cell.data('row')][cell.data('col')];

        if (cell.hasClass('enabled')) {
            switch (cellValue) {
                case 'M0':
                    cell.removeClass('enabled').addClass('mine').attr('data-player', 0);
                    break;
                case 'M1':
                    cell.removeClass('enabled').addClass('mine').attr('data-player', 1);
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
}
