"use strict";

function calendar_month_more_events(){
    jQuery(".gd_calendar_month_more_events").on("click", function (e) {
        e.preventDefault();
        var post_id = jQuery("#post_id").val();
        var date    = new Date(jQuery(this).closest(".gd_calendar_day ").attr('rel')),
            yr      = date.getFullYear(),
            mnt   = date.getMonth() + 1,
            month   = mnt < 10 ? '0' + mnt : mnt,
            day     = date.getDate()  < 10 ? '0' + date.getDate()  : date.getDate(),
            more_events_date = month + '/' + day + '/' + yr;
        var data = {
            action: 'more_events',
            nonce: gdCalendarMoreEventsObj.moreEventsNonce,
            more_events_date: more_events_date,
            id: post_id
        }
        jQuery.ajax({
            url: gdCalendarMoreEventsObj.ajaxUrl,
            type: 'post',
            data: data,
            dataType: 'text'
        }).done(function (response) {
            var active_button = jQuery(".gd_calendar_event_view_box").find(".gd_calendar_active_view");
            active_button.removeClass('gd_calendar_active_view');
            jQuery(".gd_calendar_event_box_filter").find("input.hasDatepicker").remove();

            jQuery("input#date_holder").before('<input type="text" name="gd_calendar_day_event_filter" id="gd_calendar_day_event_filter" placeholder="Date">');
            gd_calendar_day_datepicker();
            jQuery("#gd_calendar_day_event_filter").val(more_events_date);
            jQuery("#date_holder").val(more_events_date);

            jQuery("#gd_calendar").empty();
            jQuery("#gd_calendar").append(response);
            jQuery("#gd_calendar_day_view").addClass('gd_calendar_active_view');
            calendar_day_event_hover();
        });
    });
}


function calendar_week_more_events(){
    jQuery(".gd_calendar_week_more_events").on("click", function (e) {
        e.preventDefault();
        var post_id = jQuery("#post_id").val();
        var date    = new Date(jQuery(this).closest(".gd_calendar_week_cell ").attr('rel')),
            yr      = date.getFullYear(),
            mnt   = date.getMonth() + 1,
            month   = mnt < 10 ? '0' + mnt : mnt,
            day     = date.getDate()  < 10 ? '0' + date.getDate()  : date.getDate(),
            more_week_events_date = month + '/' + day + '/' + yr;
        var data = {
            action: 'week_more_events',
            nonce: gdCalendarMoreEventsObj.moreEventsNonce,
            more_week_events_date: more_week_events_date,
            id: post_id
        }
        jQuery.ajax({
            url: gdCalendarMoreEventsObj.ajaxUrl,
            type: 'post',
            data: data,
            dataType: 'text'
        }).done(function (response) {
            var active_button = jQuery(".gd_calendar_event_view_box").find(".gd_calendar_active_view");
            active_button.removeClass('gd_calendar_active_view');
            jQuery(".gd_calendar_event_box_filter").find("input.hasDatepicker").remove();

            jQuery("input#date_holder").before('<input type="text" name="gd_calendar_day_event_filter" id="gd_calendar_day_event_filter" placeholder="Date">');
            gd_calendar_day_datepicker();
            jQuery("#gd_calendar_day_event_filter").val(more_week_events_date);
            jQuery("#date_holder").val(more_week_events_date);

            jQuery("#gd_calendar").empty();
            jQuery("#gd_calendar").append(response);
            jQuery("#gd_calendar_day_view").addClass('gd_calendar_active_view');
            calendar_day_event_hover();
        });
    });
}
