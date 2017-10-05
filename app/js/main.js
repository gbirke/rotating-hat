import $ from 'jquery';

$(function () {
	$('.js-recurrence').change(function () {
		var endDate = $('.js-end_date_row');
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
		$('.js-timezone').val(clientTimezone);
	} else {
		$('.js-timezone_select_row').show();
	}

});