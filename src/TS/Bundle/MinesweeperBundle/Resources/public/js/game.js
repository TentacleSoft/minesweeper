'use strict';

$(document).ready(function () {
    $('.board-cell').click(function () {
        console.log('Click (' + $(this).data('row') + ', ' + $(this).data('col') + ')');
    });
});
