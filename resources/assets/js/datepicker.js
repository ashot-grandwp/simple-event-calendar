"use strict";
jQuery(document).ready(function () {

    /**
     *  Jquery UI Datepicker
     */

    var start_date = jQuery("#start_date"),
        end_date = jQuery("#end_date"),
        title = jQuery("#title");

    function calendarDatePicker(start_date, end_date){
        start_date.datepicker(
        {
            dateFormat: 'mm/dd/yy',
            changeMonth: true,
            changeYear: true,
            onSelect: function (){
                end_date.datepicker('option', 'minDate', start_date.datepicker('getDate') );
            }
        });
        end_date.datepicker({
            dateFormat: 'mm/dd/yy',
            changeMonth: true,
            changeYear: true,
            onSelect: function (){
                start_date.datepicker('option', 'maxDate', end_date.datepicker('getDate') );
            }
        });
    }

    function calendarDateTimePicker(start_date, end_date){
        start_date.datetimepicker({
            timeFormat: 'hh:mm tt',
            dateFormat: 'mm/dd/yy',
            oneLine: true,
            changeMonth: true,
            changeYear: true,
            controlType: 'select',
            onSelect: function (selectedDateTime){
                end_date.datetimepicker('option', 'minDate', start_date.datetimepicker('getDate') );
            }
        });
        end_date.datetimepicker({
            timeFormat: 'hh:mm tt',
            dateFormat: 'mm/dd/yy',
            oneLine: true,
            changeMonth: true,
            changeYear: true,
            controlType: 'select',
            onSelect: function (selectedDateTime){
                start_date.datetimepicker('option', 'maxDate', end_date.datetimepicker('getDate') );
            }
        });
    }

    calendarDateTimePicker(start_date, end_date);

    jQuery("#all_day").change(function () {
        if(jQuery(this).prop('checked')){
            start_date.datetimepicker('option',{
                timepicker:false,
            });
            end_date.datetimepicker('option',{
                timepicker:false,
            });
            start_date.datetimepicker('destroy');
            end_date.datetimepicker('destroy');
            calendarDatePicker(start_date, end_date);
        }
        else{
            start_date.datepicker('destroy');
            end_date.datepicker('destroy');
            calendarDateTimePicker(start_date, end_date);
        }
    });

    if(jQuery("#all_day").is(':checked')) {

        start_date.datetimepicker('option',{
            timepicker:false,
        });
        end_date.datetimepicker('option',{
            timepicker:false,
        });

        start_date.datetimepicker('destroy');
        end_date.datetimepicker('destroy');
        calendarDatePicker(start_date, end_date);
    }

    /**
     * Error for empty date
     */

    var publish = jQuery("#publish"),
        errorStart = jQuery(".error-start"),
        errorEnd = jQuery(".error-end");

    title.after( '<span class="error-msg error-title hide">' + orderL10n.titleError + '</span>' );
    title.on('blur', function () {
        if (jQuery(this).val() != "") {
            jQuery("body").find(".error-title").addClass('hide');
        }
    });
    start_date.on('blur', function () {
        if (jQuery(this).val() != "") {
            errorStart.addClass('hide');
        }
    });
    end_date.on('blur', function () {
        if (jQuery(this).val() != "") {
            errorEnd.addClass('hide');
        }
    });

    publish.click(function () {
        if(title.val() === ""){
            jQuery("body").find(".error-title").removeClass('hide');
            return false;
        }
        if (start_date.val() == "") {
            errorStart.removeClass('hide');
            return false;
        }
        if (end_date.val() == "") {
            errorEnd.removeClass('hide');
            return false;
        }
    });


});