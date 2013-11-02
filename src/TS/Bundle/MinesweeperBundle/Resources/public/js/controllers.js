var minesweeperControllers = angular.module('minesweeperControllers', []);

minesweeperControllers.controller('MainCtrl', ['$scope', '$http',
    function MainCtrl($scope, $http) {
        $http.get('/users/' + loggedUser.id + '/games').success(function (data) {
            $scope.games = data;
        });
    }]
);

minesweeperControllers.controller('GameCtrl', ['$scope', '$http', '$routeParams',
    function GameCtrl($scope, $http, $routeParams) {
        $http.get('/games/' + $routeParams.gameId).success(function (data) {
            $scope.game = data;

            var activePlayer = data.activePlayer == data.players[0].id ? 0 : 1;
            $scope.activePlayer = activePlayer;
            $scope.turnText =
                data.activePlayer == loggedUser.id
                    ? 'Your turn'
                    : data.players[activePlayer].name + '\'s turn';

            var $turn = $('#turn');
            $turn.removeClass('player0').removeClass('player1');
            $turn.addClass('player' + activePlayer);
        });

        $scope.sendChat = function () {
            if (!this.message) {
                return;
            }

            $http
                .post(
                Routing.generate('ts_minesweeper_game_send_chat', {gameId: $routeParams.gameId}),
                {from: loggedUser.username, message: this.message}
            )
                .success(function (data) {
                    $scope.game.chat = data.chat;
                }
            );

            this.message = '';
        }
    }
]);

minesweeperControllers.controller('ChatCtrl', ['$scope', '$http',

]);
