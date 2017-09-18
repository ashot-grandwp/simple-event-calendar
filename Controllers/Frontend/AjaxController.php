<?php

namespace GDCalendar\Controllers\Frontend;

use GDCalendar\Helpers\CalendarBuilder;

class AjaxController
{

    public static function init()
    {
        add_action('wp_ajax_event_filter', array(__CLASS__, 'calendarFrontendEventFilter'));
        add_action('wp_ajax_nopriv_event_filter', array(__CLASS__, 'calendarFrontendEventFilter'));

        add_action('wp_ajax_calendar_front', array(__CLASS__, 'calendarFrontendViewByType'));
        add_action('wp_ajax_nopriv_calendar_front', array(__CLASS__, 'calendarFrontendViewByType'));

        add_action('wp_ajax_search_front', array(__CLASS__, 'calendarFrontendSearch'));
        add_action('wp_ajax_nopriv_search_front', array(__CLASS__, 'calendarFrontendSearch'));

        add_action('wp_ajax_more_events', array(__CLASS__, 'calendarFrontendMoreEvents'));
        add_action('wp_ajax_nopriv_more_events', array(__CLASS__, 'calendarFrontendMoreEvents'));

        add_action('wp_ajax_week_more_events', array(__CLASS__, 'calendarFrontendWeekMoreEvents'));
        add_action('wp_ajax_nopriv_week_more_events', array(__CLASS__, 'calendarFrontendWeekMoreEvents'));

        add_action('wp_ajax_change_month', array(__CLASS__, 'calendarFrontendChangeMonth'));
        add_action('wp_ajax_nopriv_change_month', array(__CLASS__, 'calendarFrontendChangeMonth'));
    }

    public static function calendarFrontendEventFilter()
    {
        check_ajax_referer('event_filter', 'nonce');
        if (!isset($_POST['date']) || !isset($_POST['id'])) {
            return;
        }

        $date = sanitize_text_field($_POST['date']);
        $id = absint($_POST['id']);

        /* month view */
        if (strlen($date) < 8){
            if(strlen($date) !== 4){
                $month = absint(substr($date,0,2));
                $year = absint(substr($date,3,4));
                $filter_event_by_month = new CalendarBuilder($month,$year,$id);
                $filter_event_by_month->get_calendar_month();
            }
        }
        else{
            $month = absint(substr($date,0,2));
            $year = absint(substr($date,6,4));
            $filter_event = new CalendarBuilder($month,$year,$id);
            /* week view */
            if(isset($_POST['week'])){
                $filter_event->get_calendar_week();
            }
            /* day view */
            else{
                $filter_event->get_calendar_day();
            }
        }
        wp_die();
    }

    public static function calendarFrontendViewByType(){
        check_ajax_referer('calendar_front', 'nonce');

        if(!isset($_POST['type']) || !isset($_POST['id'])){
            return;
        }

        $type = $_POST['type'];
        $id = absint($_POST['id']);

        $month = absint(date("m"));
        $year = absint(date("Y"));
        $view_type = new CalendarBuilder($month,$year,$id);

        switch ($type){
            case 'day':
                $view_type->get_calendar_day();
                break;
            case 'month':
                $view_type->get_calendar_month();
                break;
            case 'week':
                $view_type->get_calendar_week();
                break;
        }
        wp_die();
    }

    public static function calendarFrontendSearch(){
        check_ajax_referer('search_front', 'nonce');
        if(!isset($_GET['type']) || !isset($_GET['id']) ){
            return;
        }

        $type = sanitize_text_field($_GET['type']);
        $id = absint($_GET['id']);


        if(isset($_GET['datepicker_month']) && !empty($_GET['datepicker_month'])){
            $datepicker_month = sanitize_text_field($_GET['datepicker_month']);
            $month = absint(substr($datepicker_month,0,2));
            $year = absint(substr($datepicker_month,3,4));
        }
        elseif (isset($_GET['datepicker_day']) && !empty($_GET['datepicker_day'])){
            $datepicker_day = sanitize_text_field($_GET['datepicker_day']);
            $month = absint(substr($datepicker_day,0,2));
            $year = absint(substr($datepicker_day,6,4));
        }
        elseif (isset($_GET['datepicker_week']) && !empty($_GET['datepicker_week'])){
            $datepicker_week = sanitize_text_field($_GET['datepicker_week']);
            $month = absint(substr($datepicker_week,0,2));
            $year = absint(substr($datepicker_week,6,4));
        }
        elseif (isset($_GET['datepicker_year']) && !empty($_GET['datepicker_year'])){
            $datepicker_year = sanitize_text_field($_GET['datepicker_year']);
            $month = absint(date('m'));
            $year = absint($datepicker_year);
        }
        else{
            $month = absint(date("m"));
            $year = absint(date("Y"));
        }

        $view_type = new CalendarBuilder($month,$year,$id);

        switch ($type){
            case 'day':
                $view_type->get_calendar_day();
                break;
            case 'month':
                $view_type->get_calendar_month();
                break;
            case 'week':
                $view_type->get_calendar_week();
                break;
        }
        wp_die();
    }

    public static function calendarFrontendMoreEvents(){
        check_ajax_referer('more_events', 'nonce');
        if(!isset($_POST['more_events_date']) || !isset($_POST['id'])){
            return;
        }

        $more_events_date = sanitize_text_field($_POST['more_events_date']);
        $id = absint($_POST['id']);

        $month = absint(substr($more_events_date,0,2));
        $year = absint(substr($more_events_date,6,4));
        $filter_day = new CalendarBuilder($month,$year,$id);
        $filter_day->get_calendar_day();

        wp_die();
    }

    public static function calendarFrontendWeekMoreEvents(){
        check_ajax_referer('more_events', 'nonce');
        if(!isset($_POST['more_week_events_date']) || !isset($_POST['id'])){
            return;
        }


        $more_events_date = sanitize_text_field($_POST['more_week_events_date']);
        $id = absint($_POST['id']);


        $month = absint(substr($more_events_date,0,2));
        $year = absint(substr($more_events_date,6,4));
        $filter_day = new CalendarBuilder($month,$year,$id);
        $filter_day->get_calendar_day();

        wp_die();
    }

    public static function calendarFrontendChangeMonth(){
        check_ajax_referer('change_month', 'nonce');
        if (!isset($_POST['id']) || !isset($_POST['current_month']) || !isset($_POST['arrow_type']) ) {
            return;
        }

        $id = absint($_POST['id']);
        $current_month = sanitize_text_field($_POST['current_month']);
        $arrow_type = sanitize_text_field($_POST['arrow_type']);

        $changed_month = '';
        if($arrow_type === 'left_arrow'){
            $changed_month = date("m/Y", strtotime($current_month . "-1 month" ));
        }
        elseif ($arrow_type === 'right_arrow'){
            $changed_month = date("m/Y", strtotime($current_month . "+1 month" ));
        }
        else{
            wp_die();
        }

        $month = absint(substr($changed_month,0,2));
        $year = absint(substr($changed_month,3,4));

        $filter_day = new CalendarBuilder($month,$year,$id);
        $filter_day->get_calendar_sidebar();

        wp_die();
    }

}