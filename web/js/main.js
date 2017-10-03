$(function () {
    $('#recurrence').change(function () {
        var endDate = $('#endDate');
        if ($(this).val() === '2' ) {
            endDate.show();
        } else {
            endDate.hide();
        }
    });

    var clientTimezone = '';
    try {
        clientTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    } catch (e) {
        clientTimezone = '';
    }
    if ( typeof clientTimezone !== 'undefined' && clientTimezone.length > 0 ) {
        $('#timezone').val(clientTimezone);
    } else {
        $('#timezone_select_row').show();
    }

});