$(function () {
    $('#recurrence').change(function () {
        var endDate = $('#endDate');
        if ($(this).val() === '2' ) {
            endDate.show();
        } else {
            endDate.hide();
        }
    });
});