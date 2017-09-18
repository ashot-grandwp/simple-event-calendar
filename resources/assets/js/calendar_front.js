"use strict";

jQuery(document).ready(function () {
    var button = jQuery(".gd_calendar_event_view_box button");

    var view_type = jQuery("#type_holder").val();
    if(view_type !== 'month'){
        jQuery(".gd_calendar_event_box_filter").find("input.hasDatepicker").remove();
    }
    // Make disabled Current Active Button
    jQuery(".gd_calendar_event_view_box").find(".gd_calendar_active_view").attr("disabled", "disabled");

    switch(view_type){
        case 'day':
            jQuery("input#date_holder").before('<input type="text" name="gd_calendar_day_event_filter" id="gd_calendar_day_event_filter" placeholder="Date">');
            gd_calendar_day_datepicker();
            break;
        // case 'month':
        //     jQuery("input#date_holder").before('<input type="text" name="gd_calendar_month_event_filter" id="gd_calendar_month_event_filter" placeholder="Date">');
        //     gd_calendar_month_datepicker();
        //     break;
        case 'week':
            jQuery("input#date_holder").before('<input type="text" name="gd_calendar_week_event_filter" id="gd_calendar_week_event_filter" placeholder="Date">');
            gd_calendar_week_datepicker();
            gdCalendarRemoveWeekBg();
            break;
        case 'year':
            jQuery("input#date_holder").before('<input type="text" name="gd_calendar_year_event_filter" id="gd_calendar_year_event_filter" placeholder="Date">');
            gd_calendar_year_datepicker();
            break;
    }

    calendar_month_more_events();
    calendar_week_more_events();
    calendar_day_event_hover();
    calendar_month_event_hover();
    calendar_week_event_hover();
    setTimeout(mobile_day_event,0);

    jQuery(window).on('resize', function () {
            mobile_day_event();
    });

    button.on("click", function () {
        var type = jQuery(this).attr('data-type');
        var search = jQuery(".gd_calendar_search").val();
        var post_id = jQuery("#post_id").val();
        var current_button = jQuery(this);
        var data = {
            action: 'calendar_front',
            nonce: gdCalendarFrontObj.frontNonce,
            type: type,
            search: search,
            id: post_id
        }
        jQuery.ajax({
            url: gdCalendarFrontObj.ajaxUrl,
            type: 'post',
            data: data,
            dataType: 'text',
            beforeSend: function () {
                jQuery(".gd_calendar_wrapper").find(".gd_loading").css("visibility", "visible");
            }
        }).done(function (response) {
            var active_button = jQuery(".gd_calendar_event_view_box").find(".gd_calendar_active_view");
            active_button.removeAttr("disabled");
            active_button.removeClass('gd_calendar_active_view');
            jQuery(".gd_calendar_event_box_filter").find("input.hasDatepicker").remove();
            jQuery("#type_holder").val(type);

            switch(type){
                case 'day':
                    jQuery("input#date_holder").before('<input type="text" name="gd_calendar_day_event_filter" id="gd_calendar_day_event_filter" placeholder="Date">');
                    gd_calendar_day_datepicker();
                    jQuery(".gd_calendar_wrapper .gd_calendar_sidebar").removeClass("sidebar_hide");
                    break;
                case 'month':
                    jQuery("input#date_holder").before('<input type="text" name="gd_calendar_month_event_filter" id="gd_calendar_month_event_filter" placeholder="Date">');
                    gd_calendar_month_datepicker();
                    jQuery(".gd_calendar_wrapper .gd_calendar_sidebar").removeClass("sidebar_hide");
                    break;
                case 'week':
                    jQuery("input#date_holder").before('<input type="text" name="gd_calendar_week_event_filter" id="gd_calendar_week_event_filter" placeholder="Date">');
                    gd_calendar_week_datepicker();
                    jQuery(".gd_calendar_wrapper .gd_calendar_sidebar").removeClass("sidebar_hide");
                    gdCalendarRemoveWeekBg();
                    break;
            }

            jQuery("#gd_calendar").empty();
            jQuery("#gd_calendar").append(response);

            calendar_month_more_events();
            calendar_week_more_events();
            calendar_day_event_hover();
            calendar_month_event_hover();
            calendar_week_event_hover();
            current_button.attr("disabled", "disabled");
            current_button.addClass('gd_calendar_active_view');
            mobile_day_event();

        }).always(function(){
            jQuery(".gd_calendar_wrapper").find(".gd_loading").css("visibility", "hidden");
        });
    });
});

function mobile_day_event() {
    var month_container = jQuery(".gd_calendar_main").width();
    if( month_container <= 415 ){
        jQuery(".gd_calendar_day").css({"cursor":"pointer"});
        jQuery(".gd_calendar_table").find(".gd_calendar_day").addClass("gd_calendar_month_more_events");
        calendar_month_more_events();
    }
    else{
        jQuery(".gd_calendar_day").css({"cursor":"default"});
        jQuery(".gd_calendar_table").find("td.gd_calendar_month_more_events").unbind('click');
        jQuery(".gd_calendar_table").find(".gd_calendar_day").removeClass("gd_calendar_month_more_events");
    }

}

function calendar_month_event_hover(){
    var day_event = jQuery(".gd_calendar_day_event");
    day_event.find('a.gd_calendar_month_hover_link').hover(function(){
        jQuery(this).parent().parent().find('.gd_calendar_hover_box').addClass('show');
    }, function(){
        jQuery(this).parent().parent().find('.gd_calendar_hover_box').removeClass('show');
    });
}

function calendar_week_event_hover(){
    jQuery('.gd_calendar_week_hover_link').hover(function(){
        jQuery(this).parent().find('.gd_calendar_hover_box').addClass('show');
    }, function(){
        jQuery(this).parent().find('.gd_calendar_hover_box').removeClass('show');
    });
}

function calendar_day_event_hover() {
    jQuery('.gd_calendar_one_day_hover_link').hover(function () {
        jQuery(this).parent().parent().find('.gd_calendar_day_hover_box').addClass('show');

        var _row = jQuery(this).parent().parent(),
            _rowOffset = _row.offset().top,
            _scrollTop = jQuery(document).scrollTop(),
            _popup = jQuery(".gd_calendar_day_hover_box").height();

        if(_rowOffset  < _scrollTop + _popup){
            jQuery('.gd_calendar_day_hover_box').css({ 'top': 0, 'bottom': '' });
            jQuery('.gd_calendar_day_hover_box').addClass('gd_calendar_change_hover');
        }else{
            jQuery('.gd_calendar_day_hover_box').css({ 'top': '', 'bottom': 0 });
            jQuery('.gd_calendar_day_hover_box').removeClass('gd_calendar_change_hover');
        }

    }, function () {
        jQuery(this).parent().parent().find('.gd_calendar_day_hover_box').removeClass('show');
    });

    jQuery('.gd_calendar_more_day_hover_link').each(function () {
        jQuery(this).hover(function () {
            var box = jQuery(this).parent().parent().find('.gd_calendar_day_hover_more_box');
            box.addClass('show');

            var title = jQuery(this).text();
            var start = jQuery(this).siblings(".start_event_hover").val();
            var end = jQuery(this).siblings(".end_event_hover").val();

            box.empty();
            box.append( "<h3>"+ title + "</h3>");
            box.append( "<p>" + start + "</p>" );
            box.append( "<p>" + end + "</p>");

            var _row = jQuery(this).parent().parent(),
                _rowOffset = _row.offset().top,
                _scrollTop = jQuery(document).scrollTop(),
                _popup_more = jQuery(".gd_calendar_day_hover_more_box").height();

            if(_rowOffset  < _scrollTop + _popup_more){
                jQuery('.gd_calendar_day_hover_more_box').css({ 'top': 0, 'bottom': '' });
                jQuery('.gd_calendar_day_hover_more_box').addClass('gd_calendar_change_hover');
            }else{
                jQuery('.gd_calendar_day_hover_more_box').css({ 'top': '', 'bottom': 0 });
                jQuery('.gd_calendar_day_hover_more_box').removeClass('gd_calendar_change_hover');
            }

        }, function () {
            jQuery(this).parent().parent().find('.gd_calendar_day_hover_more_box').removeClass('show');
        })
    });

}
