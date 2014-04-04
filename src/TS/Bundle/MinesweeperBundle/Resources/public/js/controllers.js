var minesweeperControllers = angular.module('minesweeperControllers', []);

minesweeperControllers.controller('MainCtrl', ['$scope', function MainCtrl($scope) {}]);

minesweeperControllers.controller('LobbyCtrl', ['$scope', '$http', '$location',
    function LobbyCtrl($scope, $http, $location) {
        $http.get(Routing.generate('ts_minesweeper_lobby_info')).success(function (data) {
            $scope.users = data.users;
            $scope.chat = data.chat;
        });

        $http.get(Routing.generate('ts_minesweeper_user_games', {userId: loggedUser.id})).success(function (data) {
            $scope.games = data;
        });

        $scope.newGame = function (userId) {
            $http.post(Routing.generate('ts_minesweeper_new_game', {players: [loggedUser.id, userId]})).success(function (data) {
                $location.path('#/games/' + data.id);
            });
        };

        $scope.otherUsers = function (user) {
            return user.id != loggedUser.id;
        };
    }]
);

minesweeperControllers.controller('GameCtrl', ['$scope', '$http', '$routeParams',
    function GameCtrl($scope, $http, $routeParams) {
        $scope.isActivePlayer = function () {
            return $scope.loggedUser.id == $scope.activePlayer;
        };

        $scope.getPlayerPosition = function () {
            return $scope.loggedUser.id == $scope.game.players[0] ? 0 : 1;
        };

        $scope.openCell = function (row, col) {
            if ($scope.isActivePlayer()) {
                $http.post(Routing.generate('ts_minesweeper_open_cell', {gameId: $scope.game.id, row: row, col: col}))
                    .success(function (data) {
                        $scope.game.board = data.game.board;
                    });
            }
        };

        $scope.range = function (size) {
            var array = [];

            for (var pos = 0; pos < 16; pos++) {
                array.push(pos);
            }

            return array;
        };
    }
]);

minesweeperControllers.controller('ChatCtrl', ['$scope', '$http', '$routeParams',
    function ChatCtrl($scope, $http, $routeParams) {
        $scope.sendChat = function () {
            if (!this.message) {
                return;
            }

            var arguments = {};
            if (typeof($routeParams.gameId) !== 'undefined') {
                arguments.gameId = $routeParams.gameId;
            }

            $http
                .post(
                Routing.generate('ts_minesweeper_send_chat_' + $scope.section, arguments),
                {from: loggedUser.username, message: this.message}
            )
                .success(function (data) {
                    $scope.chat = data.chat;
                }
            );

            this.message = '';
        }
    }
]);
