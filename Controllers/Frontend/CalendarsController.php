<?php

namespace GDCalendar\Controllers\Frontend;

use GDCalendar\Helpers\CalendarBuilder;
use GDCalendar\Models\PostTypes\Calendar;

class CalendarsController
{

    public function __construct(){
        add_filter( 'the_content', array( $this, 'maybeShow' ));
    }

    public function maybeShow($content){

        if(get_post_type() == Calendar::get_post_type()){
            ob_start();
            $this->show(get_the_ID());
            return ob_get_clean();
        }
        else{
            return $content;
        }
    }

    public function show($post_id){
        do_action( 'gd_calendar_frontend_css' );
        do_action( 'gd_calendar_frontend_datepicker_css' );

        $id = absint($post_id);
        do_action('gd_calendar_themes',$id);
        do_action('gd_calendar_show_script');
        if (isset($_GET['gd_calendar_month_event_filter'])){
            $selected_month = sanitize_text_field($_GET['gd_calendar_month_event_filter']);
            $month = absint(substr($selected_month,0,2));
            $year = absint(substr($selected_month,3,4));
        }
        else{
            $month = date('m');
            $year = date('Y');
        }

        $builder = new CalendarBuilder($month, $year, $id);
        $builder->show();
    }

    public static function sidebarShow($post_id){
        $id = absint($post_id);
        do_action('gd_calendar_themes',$id);
        do_action('gd_calendar_show_script');

        $month = date('m');
        $year = date('Y');

        $builder = new CalendarBuilder($month, $year, $id);
        $builder->get_calendar_sidebar();
    }

}