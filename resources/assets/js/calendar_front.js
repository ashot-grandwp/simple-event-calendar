"use strict";

var calendars = [];
jQuery(document).ready(function () {
    jQuery(".gd_calendar_wrapper").each(function (i) {
        var id = jQuery(this).attr('id');
        calendars[i] = new Calendar_Front(id);
    });
});

function Calendar_Front(id) {

    var _this = this;
    _this.container = jQuery('#' + id);
    _this.post_id = _this.container.find("#post_id").val();
    _this.month_datepicker = _this.container.find(".gd_calendar_month_event_filter").attr('id');
    _this.button = _this.container.find(".gd_calendar_event_view_box button");
    _this.view_type = _this.container.find("#type_holder").val();

    if(_this.view_type !== 'month'){
        _this.container.find(".gd_calendar_event_box_filter .gd_calendar_datepicker").remove();
    }
    // Make disabled Current Active Button
    _this.container.find(".gd_calendar_event_view_box .gd_calendar_active_view").attr("disabled", "disabled");
    switch(_this.view_type){
        case 'day':
            _this.container.find("input#date_holder").before('<input type="text" name="gd_calendar_day_event_filter" id="gd_calendar_day_event_filter_'+ _this.post_id +'" placeholder="Date">');
            gd_calendar_day_datepicker();
            break;
        case 'month':
            gd_calendar_month_datepicker();
            break;
        case 'week':
            _this.container.find("input#date_holder").before('<input type="text" name="gd_calendar_week_event_filter" id="gd_calendar_week_event_filter_'+ _this.post_id +'" placeholder="Date">');
            gd_calendar_week_datepicker();
            gdCalendarRemoveWeekBg();
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

    _this.button.on("click", function () {
        var type = jQuery(this).attr('data-type');
        var search = _this.container.find(".gd_calendar_search").val();
        // var post_id = _this.container.find("#post_id").val();
        var current_button = jQuery(this);

        var data = {
            action: 'calendar_front',
            nonce: gdCalendarFrontObj.frontNonce,
            type: type,
            search: search,
            id: _this.post_id
        }

        jQuery.ajax({
            url: gdCalendarFrontObj.ajaxUrl,
            type: 'post',
            data: data,
            dataType: 'text',
            beforeSend: function () {
                _this.container.find(".gd_loading").css("visibility", "visible");
            }
        }).done(function (response) {
            var active_button = _this.container.find(".gd_calendar_event_view_box .gd_calendar_active_view");
            active_button.removeAttr("disabled");
            active_button.removeClass('gd_calendar_active_view');
            _this.container.find(".gd_calendar_event_box_filter input.hasDatepicker").remove();
            _this.container.find(".gd_calendar_event_view_box #type_holder").val(type);

            switch(type){
                case 'day':
                    _this.container.find("input#date_holder").before('<input type="text" name="gd_calendar_day_event_filter" id="gd_calendar_day_event_filter_'+ _this.post_id +'" placeholder="Date">');
                    gd_calendar_day_datepicker();
                    _this.container.find(".gd_calendar_sidebar").removeClass("sidebar_hide");
                    break;
                case 'month':
                    _this.container.find("input#date_holder").before('<input type="text" name="gd_calendar_month_event_filter" class="gd_calendar_month_event_filter" id="' + _this.month_datepicker + '" placeholder="Date">');
                    gd_calendar_month_datepicker();
                    _this.container.find(".gd_calendar_sidebar").removeClass("sidebar_hide");
                    break;
                case 'week':
                    _this.container.find("input#date_holder").before('<input type="text" name="gd_calendar_week_event_filter" id="gd_calendar_week_event_filter_'+ _this.post_id +'" placeholder="Date">');
                    gd_calendar_week_datepicker();
                    _this.container.find(".gd_calendar_sidebar").removeClass("sidebar_hide");
                    gdCalendarRemoveWeekBg();
                    break;
            }

            _this.container.find("#gd_calendar").empty();
            _this.container.find("#gd_calendar").append(response);

            calendar_month_more_events();
            calendar_week_more_events();
            calendar_day_event_hover();
            calendar_month_event_hover();
            calendar_week_event_hover();
            current_button.attr("disabled", "disabled");
            current_button.addClass('gd_calendar_active_view');
            mobile_day_event();

        }).always(function(){
            _this.container.find(".gd_loading").css("visibility", "hidden");
        });
    });

    /**
     * Event filter datepicker for day
     */
    function gd_calendar_day_datepicker() {
        jQuery('#gd_calendar_day_event_filter_' + _this.post_id).datepicker({
            firstDay: 1,
            changeMonth: true,
            changeYear: true,
            beforeShow: function() {
                setTimeout(function(){
                    jQuery('.ui-datepicker').css('z-index', 999);
                }, 0);
                jQuery(this).datepicker('widget').removeClass('hide-calendar-month');
                jQuery(this).datepicker('widget').removeClass('hide-calendar-year');
            },
            onClose: function(dateText, inst) {
                jQuery(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay));
                var date = jQuery(this).val();
                _this.container.find("#date_holder").val(date);
                gdCalendarFilterByEvent(date);
            }
        });
    }

    /**
     * Event filter datepicker for week
     */
    function gd_calendar_week_datepicker() {
        jQuery('#gd_calendar_week_event_filter_' + _this.post_id).datepicker({
            firstDay: 1,
            changeMonth: true,
            changeYear: true,
            beforeShow: function() {
                setTimeout(function(){
                    jQuery('.ui-datepicker').css('z-index', 999);
                }, 0);
                jQuery(this).datepicker('widget').removeClass('hide-calendar-month');
                jQuery(this).datepicker('widget').removeClass('hide-calendar-year');
            },
            onClose: function(dateText, inst) {
                jQuery(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay));
                var date = jQuery(this).val();
                _this.container.find("#date_holder").val(date);
                gdCalendarFilterByEvent(date,1);
            }
        });
    }

    /**
     * Event filter datepicker for month
     */

    function gd_calendar_month_datepicker() {
        jQuery('#' + _this.month_datepicker).datepicker( {
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'mm/yy',
            onClose: function(dateText, inst) {
                jQuery(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                var date = jQuery(this).val();
                _this.container.find("#date_holder").val(date);
                gdCalendarFilterByEvent(date);
            },

            beforeShow: function() {
                setTimeout(function(){
                    jQuery('.ui-datepicker').css('z-index', 999);
                }, 0);
                jQuery(this).datepicker('widget').removeClass('hide-calendar-year');
                jQuery(this).datepicker('widget').addClass('hide-calendar-month');

                var tmp = jQuery(this).val().split('/');

                jQuery(this).datepicker('option','defaultDate',new Date(tmp[1],tmp[0]-1,1));
                jQuery(this).datepicker('setDate', new Date(tmp[1], tmp[0]-1, 1));
            }
        });
    }

    /**
     * Event filter ajax handler
     * @param selected_date
     */
    function gdCalendarFilterByEvent(selected_date, week){

        var search = _this.container.find(".gd_calendar_search").val();
        var type = _this.container.find("#type_holder").val();

        var data = {
            action: 'event_filter',
            nonce: gdCalendarFrontObj.filterNonce,
            date: selected_date,
            week: week,
            search: search,
            id: _this.post_id,
            type: type
        }
        jQuery.ajax({
            url : gdCalendarFrontObj.ajaxUrl,
            type: 'post',
            data: data,
            dataType: 'text',
            beforeSend: function () {
                _this.container.find(".gd_loading").css("visibility", "visible");
            }
        }).done(function(response) {
            _this.container.find("#gd_calendar").empty();
            _this.container.find("#gd_calendar").append(response);
            gdCalendarRemoveWeekBg();
            calendar_month_more_events();
            calendar_week_more_events();
            calendar_day_event_hover();
            calendar_month_event_hover();
            calendar_week_event_hover();
            mobile_day_event();
        }).always(function(){
            _this.container.find(".gd_loading").css("visibility", "hidden");
        });
    }

    function gdCalendarRemoveWeekBg() {
        setTimeout(function() {
            _this.container.find(".gd_calendar_week_table .gd_calendar_first_column").each(function () {
                if(jQuery(this).find('div.gd_calendar_week_box').length){
                    jQuery(this).removeClass("gd_calendar_first_column");
                }
            });
        }, 10);
    }

    function mobile_day_event() {
        var month_container = _this.container.find(".gd_calendar_main").width();
        if( month_container <= 415 ){
            _this.container.find(".gd_calendar_day").css({"cursor":"pointer"});
            _this.container.find(".gd_calendar_table").find(".gd_calendar_day").addClass("gd_calendar_month_more_events");
            calendar_month_more_events();
        }
        else{
            _this.container.find(".gd_calendar_day").css({"cursor":"default"});
            _this.container.find(".gd_calendar_table td.gd_calendar_month_more_events").unbind('click');
            _this.container.find(".gd_calendar_table .gd_calendar_day").removeClass("gd_calendar_month_more_events");
        }
    }

    function calendar_month_event_hover(){
        var day_event = _this.container.find(".gd_calendar_day_event");
        day_event.find('a.gd_calendar_month_hover_link').hover(function(){
            jQuery(this).parent().parent().find('.gd_calendar_hover_box').addClass('show');
        }, function(){
            jQuery(this).parent().parent().find('.gd_calendar_hover_box').removeClass('show');
        });
    }

    function calendar_week_event_hover(){
        _this.container.find('.gd_calendar_week_hover_link').hover(function(){
            jQuery(this).parent().find('.gd_calendar_hover_box').addClass('show');
        }, function(){
            jQuery(this).parent().find('.gd_calendar_hover_box').removeClass('show');
        });
    }

    function calendar_day_event_hover() {
        _this.container.find('.gd_calendar_one_day_hover_link').hover(function () {
            jQuery(this).parent().parent().find('.gd_calendar_day_hover_box').addClass('show');

            var _row = jQuery(this).parent().parent(),
                _rowOffset = _row.offset().top,
                _scrollTop = jQuery(document).scrollTop(),
                _popup = _this.container.find(".gd_calendar_day_hover_box").height();

            if(_rowOffset  < _scrollTop + _popup){
                _this.container.find('.gd_calendar_day_hover_box').css({ 'top': 0, 'bottom': '' });
                _this.container.find('.gd_calendar_day_hover_box').addClass('gd_calendar_change_hover');
            }else{
                _this.container.find('.gd_calendar_day_hover_box').css({ 'top': '', 'bottom': 0 });
                _this.container.find('.gd_calendar_day_hover_box').removeClass('gd_calendar_change_hover');
            }

        }, function () {
            jQuery(this).parent().parent().find('.gd_calendar_day_hover_box').removeClass('show');
        });

        _this.container.find('.gd_calendar_more_day_hover_link').each(function () {
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
                    _popup_more = _this.container.find(".gd_calendar_day_hover_more_box").height();

                if(_rowOffset  < _scrollTop + _popup_more){
                    _this.container.find('.gd_calendar_day_hover_more_box').css({ 'top': 0, 'bottom': '' });
                    _this.container.find('.gd_calendar_day_hover_more_box').addClass('gd_calendar_change_hover');
                }else{
                    _this.container.find('.gd_calendar_day_hover_more_box').css({ 'top': '', 'bottom': 0 });
                    _this.container.find('.gd_calendar_day_hover_more_box').removeClass('gd_calendar_change_hover');
                }

            }, function () {
                jQuery(this).parent().parent().find('.gd_calendar_day_hover_more_box').removeClass('show');
            })
        });
    }

    function calendar_month_more_events(){
        _this.container.find(".gd_calendar_month_more_events").on("click", function (e) {

            /* If day view not selected expand div events */
            if (_this.container.find('.gd_calendar_day_event .gd_calendar_more_events').length) {
                jQuery(this).parent().parent().find(".gd_calendar_more_events").toggle();
                return false;
            }

            e.preventDefault();
            var date    = new Date(jQuery(this).closest(".gd_calendar_day ").attr('rel')),
                yr      = date.getFullYear(),
                mnt   = date.getMonth() + 1,
                month   = mnt < 10 ? '0' + mnt : mnt,
                day     = date.getDate()  < 10 ? '0' + date.getDate()  : date.getDate(),
                more_events_date = month + '/' + day + '/' + yr;
            var data = {
                action: 'more_events',
                nonce: gdCalendarFrontObj.moreEventsNonce,
                more_events_date: more_events_date,
                id: _this.post_id
            }
            jQuery.ajax({
                url: gdCalendarFrontObj.ajaxUrl,
                type: 'post',
                data: data,
                dataType: 'text'
            }).done(function (response) {
                var active_button = _this.container.find(".gd_calendar_event_view_box .gd_calendar_active_view");
                active_button.removeAttr("disabled");
                active_button.removeClass('gd_calendar_active_view');
                _this.container.find(".gd_calendar_event_box_filter input.hasDatepicker").remove();
                _this.container.find("input#date_holder").before('<input type="text" name="gd_calendar_day_event_filter" id="gd_calendar_day_event_filter_'+ _this.post_id +'" placeholder="Date">');
                gd_calendar_day_datepicker();
                _this.container.find("#gd_calendar_day_event_filter_" + _this.post_id).val(more_events_date);
                _this.container.find("#date_holder").val(more_events_date);
                _this.container.find("#type_holder").val('day');
                _this.container.find("#gd_calendar").empty();
                _this.container.find("#gd_calendar").append(response);
                _this.container.find("#gd_calendar_day_view").addClass('gd_calendar_active_view');
                // _this.container.find(".gd_calendar_event_view_box .gd_calendar_active_view").attr("disabled", "disabled");

                calendar_day_event_hover();
            });
        });
    }

    function calendar_week_more_events(){
        _this.container.find(".gd_calendar_week_more_events").on("click", function (e) {

            /* If day view not selected expand div events */
            if (_this.container.find('.gd_calendar_week_cell .gd_calendar_week_more_boxes').length) {
                jQuery(this).parent().parent().find(".gd_calendar_week_more_boxes").toggle();
                return false;
            }

            e.preventDefault();
            var date    = new Date(jQuery(this).closest(".gd_calendar_week_cell ").attr('rel')),
                yr      = date.getFullYear(),
                mnt   = date.getMonth() + 1,
                month   = mnt < 10 ? '0' + mnt : mnt,
                day     = date.getDate()  < 10 ? '0' + date.getDate()  : date.getDate(),
                more_week_events_date = month + '/' + day + '/' + yr;
            var data = {
                action: 'week_more_events',
                nonce: gdCalendarFrontObj.moreEventsNonce,
                more_week_events_date: more_week_events_date,
                id: _this.post_id
            }
            jQuery.ajax({
                url: gdCalendarFrontObj.ajaxUrl,
                type: 'post',
                data: data,
                dataType: 'text'
            }).done(function (response) {
                var active_button = _this.container.find(".gd_calendar_event_view_box .gd_calendar_active_view");
                active_button.removeAttr("disabled");
                active_button.removeClass('gd_calendar_active_view');
                _this.container.find(".gd_calendar_event_box_filter input.hasDatepicker").remove();
                _this.container.find("input#date_holder").before('<input type="text" name="gd_calendar_day_event_filter" id="gd_calendar_day_event_filter_'+ _this.post_id +'" placeholder="Date">');
                gd_calendar_day_datepicker();
                _this.container.find("#gd_calendar_day_event_filter_" + _this.post_id).val(more_week_events_date);
                _this.container.find("#date_holder").val(more_week_events_date);
                _this.container.find("#type_holder").val('day');
                _this.container.find("#gd_calendar").empty();
                _this.container.find("#gd_calendar").append(response);
                _this.container.find("#gd_calendar_day_view").addClass('gd_calendar_active_view');
                calendar_day_event_hover();
            });
        });
    }

    _this.container.on('click', ".gd_calendar_arrow_box a",  function (e) {
        e.preventDefault();
        var arrow_type = jQuery(this).attr('data-type');
        var post_id = _this.container.find(".gd_calendar_sidebar").data("calendar-id");

        var data = {
            action: 'change_month',
            nonce: gdCalendarFrontObj.changeMonthNonce,
            current_month: _this.container.find(".gd_calendar_small_date").data('date'),
            arrow_type: arrow_type,
            id: post_id
        }
        jQuery.ajax({
            url: gdCalendarFrontObj.ajaxUrl,
            type: 'post',
            data: data,
            dataType: 'text',
            beforeSend: function () {
                _this.container.find(".gd_loading").css("visibility", "visible");
            }
        }).done(function (responce) {
            _this.container.find(".gd_calendar_sidebar").empty();
            _this.container.find(".gd_calendar_sidebar").append(responce);
        }).always(function(){
            _this.container.find(".gd_loading").css("visibility", "hidden");
        });
    });

    _this.container.find("#search").on("submit", function (e) {
        e.preventDefault();
        // var type = jQuery(".gd_calendar_event_view_box").find(".gd_calendar_active_view").attr('data-type');
        var type = _this.container.find(".gd_calendar_event_view_box #type_holder").val();
        var search = _this.container.find(".gd_calendar_search").val();
        var datepicker_month = _this.container.find(".gd_calendar_month_event_filter").val();
        var datepicker_day = _this.container.find("#gd_calendar_day_event_filter_" + _this.post_id).val();
        var datepicker_week = _this.container.find("#gd_calendar_week_event_filter_" + _this.post_id).val();
        var datepicker_year = _this.container.find("#gd_calendar_year_event_filter_" + _this.post_id).val();

        var data = {
            action: 'search_front',
            nonce: gdCalendarFrontObj.searchNonce,
            type: type,
            search: search,
            id: _this.post_id,
            datepicker_month: datepicker_month,
            datepicker_day: datepicker_day,
            datepicker_week: datepicker_week,
            datepicker_year: datepicker_year,
        }
        jQuery.ajax({
            url: gdCalendarFrontObj.ajaxUrl,
            type: 'get',
            data: data,
            dataType: 'text',
            beforeSend: function () {
                _this.container.find(".gd_loading").css("visibility", "visible");
            }
        }).done(function (responce) {

            _this.container.find("#gd_calendar").empty();
            _this.container.find("#gd_calendar").append(responce);

            calendar_month_more_events();
            calendar_week_more_events();
            calendar_day_event_hover();
            calendar_month_event_hover();
            calendar_week_event_hover();
        }).always(function(){
            _this.container.find(".gd_loading").css("visibility", "hidden");
        });
    });
}
