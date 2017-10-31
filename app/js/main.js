import $ from 'jquery';
import Vue from 'vue';

Vue.config.productionTip = false;

const monthNames = [
    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
];

Vue.filter( 'monthName', function( value ) {
    return monthNames[ value.getMonth() ];
} );


import App from './App.vue';
import { EventBus } from './event-bus';
import VCalendarConverter from './vcalendar_converter';

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
            let converter;
            try {
                converter = new VCalendarConverter( data );
            } catch ( e ) {
                EventBus.$emit( 'eventsLoaded', [] );
                return;
            }
            EventBus.$emit( 'eventsLoaded', converter.getEvents() );
        }).fail( function( jqXHR, textStatus, errorThrown ) {
            // TODO show failure indicator?
            console.log( 'Server request failed', textStatus, errorThrown );
        } );
    } );

    new Vue({
        el: '#vuetest',
        render: h => h(App),
    });

    setTimeout( () => EventBus.$emit( 'eventsLoaded', {foo:'bars'} ), 5000);

});