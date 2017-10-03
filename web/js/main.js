$(function () {
    $('#recurrence').change(function () {
        var endDate = $('#endDate');
        if ($(this).val() === '2' ) {
            endDate.show();
        } else {
            endDate.hide();
        }
    });

    var clientTimezone;
    try {
        clientTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    } catch (e) {
        clientTimezone = 'Europe/Berlin';
    }
    if ( $('#task_timezone option[value="'+clientTimezone+'"]' ).length > 0 ) {
        $('#task_timezone').val(clientTimezone);
    }
});