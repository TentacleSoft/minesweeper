var minesweeperApp = angular.module('minesweeperApp', []);

minesweeperApp.controller('MainCtrl', function MainCtrl($scope, $http) {
    $http.get('/users/' + globals.user.id + '/games').success(function (data) {
        $scope.games = data;
    });
});
