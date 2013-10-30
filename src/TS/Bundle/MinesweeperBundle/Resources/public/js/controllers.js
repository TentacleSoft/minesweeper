var minesweeperApp = angular.module('minesweeperApp', []);

minesweeperApp.controller('MainCtrl', function MainCtrl($scope) {
    $scope.games = [
        'Test Game 1',
        'Game 2'
    ]
});
