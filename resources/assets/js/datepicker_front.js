'use strict';

/**
 * Event filter datepicker for day
 */
function gd_calendar_day_datepicker() {
    jQuery("#gd_calendar_day_event_filter").datepicker({
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
            jQuery("#date_holder").val(date);
            gdCalendarFilterByEvent(date);
        }
    });
}

/**
 * Event filter datepicker for week
 */
function gd_calendar_week_datepicker() {
    jQuery("#gd_calendar_week_event_filter").datepicker({
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
            jQuery("#date_holder").val(date);
            gdCalendarFilterByEvent(date,1);
        }
    });
}

/**
 * Event filter datepicker for month
 */
function gd_calendar_month_datepicker() {
    jQuery('#gd_calendar_month_event_filter').datepicker( {
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'mm/yy',
        onClose: function(dateText, inst) {
            jQuery(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            var date = jQuery(this).val();
            jQuery("#date_holder").val(date);
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
    var search = jQuery(".gd_calendar_search").val();
    var post_id = jQuery("#post_id").val();
    var data = {
        action: 'event_filter',
        nonce: gdCalendarEventFilterAjaxObj.filterNonce,
        date: selected_date,
        week: week,
        search: search,
        id: post_id
    }
    jQuery.ajax({
        url : gdCalendarEventFilterAjaxObj.ajaxUrl,
        type: 'post',
        data: data,
        dataType: 'text',
        beforeSend: function () {
            jQuery(".gd_calendar_wrapper").find(".gd_loading").css("visibility", "visible");
        }
    }).done(function(response) {
        jQuery("#gd_calendar").empty();
        jQuery("#gd_calendar").append(response);
        gdCalendarRemoveWeekBg();
        calendar_month_more_events();
        calendar_week_more_events();
        calendar_day_event_hover();
        calendar_month_event_hover();
        calendar_week_event_hover();
        mobile_day_event();
    }).always(function(){
        jQuery(".gd_calendar_wrapper").find(".gd_loading").css("visibility", "hidden");
    });
}

function gdCalendarRemoveWeekBg() {
    setTimeout(function() {
        jQuery(".gd_calendar_week_table").find(".gd_calendar_first_column").each(function () {
            if(jQuery(this).find('div.gd_calendar_week_box').length){
                jQuery(this).removeClass("gd_calendar_first_column");
            }
        });
    }, 10);
}

jQuery(document).ready(function () {

    gd_calendar_month_datepicker();

});