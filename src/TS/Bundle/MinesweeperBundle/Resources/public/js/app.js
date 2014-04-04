var minesweeperApp = angular.module('minesweeperApp', [
    'ngRoute',
    'ngAnimate',
    'minesweeperControllers'
]);

minesweeperApp.config(['$routeProvider',
    function ($routeProvider) {
        $routeProvider
            .when('/', {
                templateUrl: Routing.generate('ts_minesweeper_partials', {pageName: 'lobby'}),
                controller: 'LobbyCtrl'
            })
            .when('/games/:gameId', {
                templateUrl: Routing.generate('ts_minesweeper_partials', {pageName: 'game'}),
                controller: 'GameCtrl',
                resolve: {
                    minesweeperApp: function ($q, $scope, $http, $routeParams) {
                        var defer = $q.defer();

                        $http.get('/games/' + $routeParams.gameId).success(function (data) {
                            $scope.game = data;
                            $scope.chat = data.chat;
                            $scope.loggedUser = loggedUser;

                            var activePlayer = data.activePlayer;
                            $scope.activePlayer = activePlayer;
                            $scope.turnText =
                                data.activePlayer == loggedUser.id
                                    ? 'Your turn'
                                    : data.players[activePlayer].name + '\'s turn';
                        });

                        return defer.promise;
                    }
                }
            })
            .otherwise({
                redirectTo: '/'
            })
    }
]);

minesweeperApp.config(['$httpProvider', function ($httpProvider) {
    // Intercept POST requests, convert to standard form encoding
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
    $httpProvider.defaults.transformRequest.unshift(function (data, headersGetter) {
        var key, result = [];
        for (key in data) {
            if (data.hasOwnProperty(key)) {
                result.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
            }
        }
        return result.join('&');
    })
}]);
