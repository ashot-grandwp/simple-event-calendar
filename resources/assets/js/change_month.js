"use strict";

(function () {
    jQuery(document).on('click', ".gd_calendar_arrow_box a",  function (e) {
        e.preventDefault();
        var arrow_type = jQuery(this).attr('data-type');
        // var post_id = jQuery("#post_id").val();
        var post_id = jQuery(".gd_calendar_sidebar").data("calendar-id");
        var data = {
            action: 'change_month',
            nonce: gdCalendarChangeMonthObj.changeMonthNonce,
            current_month: jQuery(".gd_calendar_small_date").data('date'),
            arrow_type: arrow_type,
            id: post_id
        }
        jQuery.ajax({
            url: gdCalendarChangeMonthObj.ajaxUrl,
            type: 'post',
            data: data,
            dataType: 'text',
            beforeSend: function () {
                jQuery(".gd_calendar_wrapper").find(".gd_loading").css("visibility", "visible");
            }
        }).done(function (responce) {
            jQuery(".gd_calendar_sidebar").empty();
            jQuery(".gd_calendar_sidebar").append(responce);
        }).always(function(){
            jQuery(".gd_calendar_wrapper").find(".gd_loading").css("visibility", "hidden");
        });
    });
})();

