"use strict";

jQuery(document).ready(function () {
    jQuery("#search").on("submit", function (e) {
        e.preventDefault();
        var type = jQuery(".gd_calendar_event_view_box").find(".gd_calendar_active_view").attr('data-type');
        var search = jQuery(".gd_calendar_search").val();
        var post_id = jQuery("#post_id").val();
        var datepicker_month = jQuery("#gd_calendar_month_event_filter").val();
        var datepicker_day = jQuery("#gd_calendar_day_event_filter").val();
        var datepicker_week = jQuery("#gd_calendar_week_event_filter").val();
        var datepicker_year = jQuery("#gd_calendar_year_event_filter").val();

        var data = {
            action: 'search_front',
            nonce: gdCalendarSearchFrontObj.searchNonce,
            type: type,
            search: search,
            id: post_id,
            datepicker_month: datepicker_month,
            datepicker_day: datepicker_day,
            datepicker_week: datepicker_week,
            datepicker_year: datepicker_year,
        }
        jQuery.ajax({
            url: gdCalendarSearchFrontObj.ajaxUrl,
            type: 'get',
            data: data,
            dataType: 'text',
            beforeSend: function () {
                jQuery(".gd_calendar_wrapper").find(".gd_loading").css("visibility", "visible");
            }
        }).done(function (responce) {

            jQuery("#gd_calendar").empty();
            jQuery("#gd_calendar").append(responce);

            calendar_day_event_hover();
            calendar_month_event_hover();
            calendar_week_event_hover();
        }).always(function(){
            jQuery(".gd_calendar_wrapper").find(".gd_loading").css("visibility", "hidden");
        });
    })
});
