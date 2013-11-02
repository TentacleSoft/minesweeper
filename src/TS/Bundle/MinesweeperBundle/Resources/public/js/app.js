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
                controller: 'GameCtrl'
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
