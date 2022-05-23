$('.tile').contextmenu(function (e) {
    e.preventDefault();
    if ($(this).hasClass('flag')) {
        location.href = '/?flag=' + $(this).data('coordinates');
    } else if ($(this).is('a')) {
        location.href = '/?flag=' + $(this).prop('href').split('reveal=')[1];
    }
});

$('td').contextmenu(function (e) {
    e.preventDefault();
});

$('[name="new_game"]').on('change', function () {
    if ($('#custom').is(':checked')) {
        $('#custom-game-container').show()
    } else {
        $('#custom-game-container').hide()
    }
});

