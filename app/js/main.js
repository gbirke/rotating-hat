import $ from 'jquery';
import Vue from 'vue';

Vue.config.productionTip = false;

import App from './App.vue';

$(function () {
    $('.js-recurrence').change(function () {
        var endDate = $('.js-end_date_row');
        if ($(this).val() === '2') {
            endDate.show();
        } else {
            endDate.hide();
        }
    });

    let clientTimezone = '';
    try {
        clientTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    } catch (e) {
        clientTimezone = '';
    }
    if (typeof clientTimezone !== 'undefined' && clientTimezone.length > 0) {
        $('.js-timezone').val(clientTimezone);
    } else {
        $('.js-timezone_select_row').show();
    }

    $( 'form :input' ).change( function () {
        const values = {};

        const fd = new FormData( document.querySelector( '.js-entry' ) );
        for ( let e of fd ) {
            values[ e[0] ] = e[1];
        }
        $.ajax( '/create-calendar', {
            data: values,
            dataType: 'json',
            method: 'POST',
        } ).done( function( data ) {
            // TODO:
            // - check for error object
            // - generate "events" (date, label) from jCalendar object
            // - render events

            console.log( 'received data', data);
        }).fail( function( jqXHR, textStatus, errorThrown ) {
            // TODO show failure indicator?
            console.log( 'Server request failed', textStatus, errorThrown );
        } );
    } );

    new Vue({
        el: '#vuetest',
        render: h => h(App),
    });

});