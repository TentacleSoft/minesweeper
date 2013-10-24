'use strict';

var gameId = 1,
    game = {},
    pollingRate = 1000,
    section = 'lobby';

$(document).ready(function () {
    setInterval(function () {
        if (typeof globals != 'undefined') {
            if (section == 'lobby') {
                $.getJSON('/lobby/', function (data) {
                    updateLobbyInfo(data);
                });
                $.getJSON('/users/' + globals.user.id + '/games', function (data) {
                    updateGamesInfo(data);
                });
            } else if (section == 'game') {
                $.getJSON('/games/' + gameId, function (data) {
                    updateGameInfo(data);
                });
            }
        }
    }, pollingRate);

    $('.board-cell.enabled').click(function () {
        if (game.activePlayer != globals.user.id) {
            return;
        }

        var row = $(this).data('row'),
            col = $(this).data('col');
        $.post('/games/' + gameId, {row: row, col: col}, function (data) {
            updateGameInfo(data);
        });
        console.log('Click (' + row + ', ' + col + ')');
    });

    $('#form-chat').submit(function () {
        var text = $('#text').val();

        if (text != '') {
            $('#text').val('');
            $.post('/games/' + gameId + '/chat', {text: text}, function (data) {
                updateGameInfo(data);
            });
        }

        return false;
    });
});

function updateLobbyInfo(data) {
    updateChatInfo(data.chat);
    updateUsersInfo(data.users);
}

function updateGameInfo(data) {
    updateChatInfo(data.chat);

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

function updateChatInfo(chatData) {
    var chat = $('#chat').html(''),
        chatLines = '';

    for (var chatLineKey in chatData) {
        var chatLine = chatData[chatLineKey],
            line = $('<li>').text(chatLine.message);

        switch (chatLine.from) {
            case -1:
                line.addClass('info');
                break;
            case -2:
                line.addClass('error');
                break;
            default:
                line.prepend($('<span>').text(line.from));
        }

        chatLines += line.html();
    }

    chat.html(chatLines).scrollTop(chat[0].scrollHeight);
}

function updateUsersInfo(usersData) {
    var userList = $('#user-list').html('');

    for (var userKey in usersData) {
        var user = usersData[userKey],
            userElement = $('<li>').text(user.username + ' (' + user.games.won + '-' + user.games.lost + ')');
        userList.append(userElement);
    }
}

function updateGamesInfo(gamesData) {
    var gameList = $('#game-list').html('');

    for (var gameKey in gamesData) {
        var game = gamesData[gameKey],
            gameElement = $('<li>').text(game.id);
        gameList.append(gameElement);
    }
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
