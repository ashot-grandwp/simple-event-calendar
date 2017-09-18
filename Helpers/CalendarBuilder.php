<?php

namespace GDCalendar\Helpers;

use GDCalendar\Models\PostTypes\Event;
use GDCalendar\Models\PostTypes\Venue;
use GDCalendar\Models\PostTypes\Calendar;
use GDCalendar\Models\PostTypes\Organizer;


class CalendarBuilder
{

    /**
     * @var int
     */
    private $month;

    /**
     * @var int
     */
    private $year;

    /**
     * @var int
     */
    private $post_id;

    /**
     * @var int
     */
    private $current_date;

    /**
     * array containing abbreviations of days of week.
     * @var array
     */
    private $days_of_week = array('SUN','MON','TUE','WED','THU','FRI','SAT');

    /**
     * @var array
     */
    private $months_of_year = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

    /**
     * @var array
     */
    private static $hours = array('All-day','12 AM','1 AM','2 AM','3 AM','4 AM','5 AM','6 AM','7 AM','8 AM','9 AM','10 AM','11 AM','12 PM','1 PM','2 PM','3 PM','4 PM','5 PM','6 PM','7 PM','8 PM','9 PM','10 PM','11 PM');

    public function __construct( $month, $year , $post_id){
        $this->month = $month;
        $this->year = $year;
        $this->post_id = $post_id;
    }

    /**
     * @return string
     */
    public function get_current_date(){
        return $this->current_date = date('Y-m-d');
    }

    /**
     * @return int
     */
    public function get_weekday() {
        return date('w', strtotime($this->get_current_date()));
    }

    /**
     * @return int
     * @var string
     */
    public function get_current_weekday_number($date = false){
        if(false === $date){
            return date("W", strtotime($this->get_current_date() . '+ 1 day'));
        }
        return date("W", strtotime($date . '+ 1 day'));
    }

    /**
     * @return int
     */
    public function get_month(){
        return $this->month;
    }

    /**
     * @return int
     */
    public function get_year(){
        return $this->year;
    }

    /**
     * @return int
     */
    public function get_post_id(){
        return $this->post_id;
    }

    /**
     * @return array
     */
    public function get_days_of_week(){
        return $this->days_of_week;
    }

    /**
     * @return array
     */
    public function get_months_of_year(){
        return $this->months_of_year;
    }

    /**
     * @return array
     */
    public static function get_hours(){
        return self::$hours;
    }

    /**
     * What is the first day of the month in question?
     * @return false|int
     */
    public function get_first_day_of_month($month = false){
        if(false === $month){
            return mktime(0,0,0,$this->month,1,$this->year);
        }
        return mktime(0,0,0,$month,1,$this->year);
    }

    /**
     * What is the last day of the last month
     * @return false|int
     */
    public function get_last_day_of_previous_month(){
        return mktime(0, 0, 0, $this->month, 0, $this->year);
    }

    /**
     * What is the first day of the next month
     * @return false|int
     */
    public function get_first_day_of_next_month(){
        return mktime(0, 0, 0, $this->month + 1, 1, $this->year);
    }

    /**
     * How many days does this month contain?
     */
    public function get_days_count($month = false){
        return date('t',$this->get_first_day_of_month($month));
    }

    /**
     * Retrieve some information about the first day of the
     * month in question.
     */
    public function get_date_components($month = false){
        return getdate($this->get_first_day_of_month($month));
    }

    /**
     * Retrieve some information about last month
     * @return array
     */

    public function get_last_date_components(){
        return getdate($this->get_last_day_of_previous_month());
    }

    /**
     * Retrieve some information about next month
     * @return array
     */

    public function get_next_date_components(){
        return getdate($this->get_first_day_of_next_month());
    }

    /**
     * @param $day
     * @return array
     * Retrieve event by day
     */
    public static function get_event_by_day($day){
        $day_events = array();
        $post_id = absint(get_post()->ID);
        if(isset($_GET['id'])){
            $post_id = absint($_GET['id']);
        }
        elseif (isset($_POST['id'])){
            $post_id = absint($_POST['id']);
        }

        $calendar = new Calendar($post_id);
        $post_type = $calendar->get_select_events_by();
        $selected_categories = $calendar->get_cat();

        $tax_param = '';
        if(!empty($selected_categories) && taxonomy_exists($post_type)){
            $tax_param = array(
                'taxonomy' => $post_type,
                'terms' => $selected_categories,
                'include_children' => false,
            );
        }
        $events = Event::get(array(
                'post_status' => 'publish',
                'tax_query' => array(
                    $tax_param,
                ))
        );
        if($events && !empty($events)){
            foreach ($events as $event){
                $event_id = absint($event->get_id());

                if(!empty($selected_categories)){
                    if($post_type === 'gd_organizers'){
                        $organizers = $event->get_event_organizer();
                        $org_result = array_intersect($organizers, $selected_categories);
                        $result = (!empty($org_result)) ? true : false ;
                    }
                    elseif($post_type === 'gd_venues'){
                        $venue = $event->get_event_venue();
                        $result = in_array($venue, $selected_categories);
                    }
                    else{
                        $result = true;
                    }
                }
                else{
                    $result = true;
                }

                if(true === $result){
                    $event_dates = self::eventDateRange($event->get_start_date(), $event->get_end_date());
                    foreach ($event_dates as $date){
                        if($day === substr($date, 0, 10)){
                            if(isset($day_events[$event_id]) || empty($day_events[$event_id])){
                                $day_events[$event_id][] = $date;
                            }else{
                                $day_events[$event_id] = array($date);
                            }
                        }
                    }
                }
            }
        }
        return $day_events;
    }

    /**
     * @param $day
     * @return array
     * Retrieve events by hour
     */
    public static function get_event_by_hour($day){
        $hour_events = array();
        $day_events = self::get_event_by_day($day);
        foreach ($day_events as $id => $event){
            $date = sanitize_text_field(substr($event[0],11,8));
            if($date !== ''){
                $divide = explode(":", $date, 2);
                $time_digit = absint($divide[0]);
                $period = sanitize_text_field(substr( $divide[1], 3, 2 ));
                $time = $time_digit . ' ' . $period;
                $time = strtoupper($time); // for sorting with hours array
            }
            else{
                $time = 'All-day';
            }

            if(isset($hour_events[$time]) || empty($hour_events[$time])){
                $hour_events[$time][] = $id;
            }else{
                $hour_events[$time] = array($id);
            }
        }

        $hours = self::get_hours();
        $sorted_hour_events = array_merge(array_flip($hours), $hour_events);

        foreach($sorted_hour_events as $key => $value){
            if(!is_array($value))
                unset($sorted_hour_events[$key]);
        }
        $sorted_hour_events = array_change_key_case($sorted_hour_events,CASE_LOWER);

        return $sorted_hour_events;
    }

    /**
     * @param $day
     * @param CalendarBuilder $builder
     * @return array Retrieve events for week by hour
     * Retrieve events for week by hour
     */
    public static function get_event_by_week($day, CalendarBuilder $builder){
        $week_events = array();
        $week_number = absint($builder->get_current_weekday_number($day));

        for($i=0; $i<=6; $i++){
            $date = date('Y-m-d', strtotime(date('Y')."W". $week_number . $i));

            $day_events = self::get_event_by_hour($date);
            $week_events[$i] = $day_events;
        }
        return $week_events;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * Retrieve all dates between start and end dates
     */
    public static function eventDateRange($startDate, $endDate){
        $eventDate = array();
        $start_period = substr($startDate,17,2);
        $start_hour = 0;
        $start_min = 0;
        if($start_period){
            $start_hour = substr($startDate,11,2);
            $start_min = substr($startDate,14,2);
            if($start_period === 'pm' && $start_hour !== "12"){$start_hour += 12;}
            if($start_period === 'am' && $start_hour === "12"){$start_hour -= 12;}
        }

        $end_period = substr($endDate,17,2);
        $end_hour = 0;
        $end_min = 0;
        if($end_period){
            $end_hour = substr($endDate,11,2);
            $end_min = substr($endDate,14,2);
            if($end_period === 'pm' && $end_hour !== "12"){$end_hour += 12;}
            if($end_period === 'am' && $end_hour === "12"){$end_hour -= 12;}
        }

        $dateFrom = mktime($start_hour,$start_min,0,substr($startDate,0,2), substr($startDate,3,2),substr($startDate,6,4));
        $dateTo = mktime($end_hour,$end_min,0,substr($endDate,0,2), substr($endDate,3,2),substr($endDate,6,4));

        if ( date('Y-m-d', $dateTo) >= date('Y-m-d', $dateFrom) )
        {
            array_push($eventDate,($start_period) ? date('Y-m-d h:i a',$dateFrom) : date('Y-m-d',$dateFrom)); // first entry

            while (date('Y-m-d', $dateFrom) < date('Y-m-d', $dateTo))
            {
                $dateFrom+=86400; // add 24 hours
                array_push($eventDate,($start_period) ? date('Y-m-d h:i a',$dateFrom) : date('Y-m-d',$dateFrom));
            }
        }

        return $eventDate;
    }

    /**
     * @return array
     * Retrieve Get searched event with dates
     */
    public function get_searched_event(){
        global $wpdb;
        $searched_events_id = array();
        if(isset($_GET['search'])){
            $search = sanitize_text_field($_GET['search']);
            $search = '%' . $wpdb->esc_like( $search ) . '%';
            $post_id = absint(get_post()->ID);
            if(isset($_GET['id'])){
                $post_id = absint($_GET['id']);
            }
            elseif (isset($_POST['id'])){
                $post_id = absint($_POST['id']);
            }
            $calendar = new Calendar($post_id);
            $post_type = $calendar->get_select_events_by();
            $selected_categories = $calendar->get_cat();
            if(!empty($selected_categories && taxonomy_exists($post_type))){
                $taxonomies = implode(',', $selected_categories);
                $event_query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "posts AS p
                                            JOIN " . $wpdb->prefix . "term_relationships AS r ON (r.object_id = p.ID)
                                            JOIN " . $wpdb->prefix . "term_taxonomy AS t ON (t.term_taxonomy_id = r.term_taxonomy_id)
                                            JOIN " . $wpdb->prefix . "terms AS tr ON (tr.term_id = t.term_id) 
                                            WHERE
                                            p.post_type = '".Event::get_post_type()."' AND
                                            p.post_status = 'publish' AND 
                                            t.taxonomy = '".$post_type."' AND 
                                            tr.term_id IN ($taxonomies) AND 
                                            p.post_title LIKE %s", $search);
            }
            else{
                $event_query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "posts WHERE
                                            post_type = '".Event::get_post_type()."' AND
                                            post_status = 'publish' AND
                                            post_title LIKE %s", $search);
            }
            $events = $wpdb->get_results($event_query);
            if($wpdb->num_rows > 0){
                foreach($events as $event){
                    $event_id = $event->ID;
                    $event_filter = new Event($event_id);

                    if(!empty($selected_categories)){
                        if($post_type === Organizer::get_post_type()){
                            $organizers = $event_filter->get_event_organizer();
                            $org_result = array_intersect($organizers, $selected_categories);
                            $result = (!empty($org_result)) ? true : false ;
                        }
                        elseif($post_type === Venue::get_post_type()){
                            $venue = $event_filter->get_event_venue();
                            $result = in_array($venue, $selected_categories);
                        }
                        else{
                            $result = true;
                        }
                    }
                    else{
                        $result = true;
                    }

                    if(true === $result) {
                        array_push($searched_events_id, $event_id);
                    }
                }
            }
        }
        return $searched_events_id;
    }

    /**
     * Print calendar month table
     */
    public function get_calendar_month(){
        View::render('frontend/calendar/month.php', array( 'month' => $this ) );
    }

    /**
     * Print sidebar calendar table
     */
    public function get_calendar_sidebar(){
        View::render('frontend/calendar/sidebar.php', array( 'sidebar_month' => $this ) );
    }

    /**
     * Print calendar week table
     */
    public function get_calendar_week(){
        View::render('frontend/calendar/week.php', array( 'week' => $this ) );
    }

    /**
     * Print calendar day table
     */
    public function get_calendar_day(){
        View::render('frontend/calendar/day.php', array( 'day' => $this ) );
    }

    /**
     * Print Calendar
     */
    public function show(){
        View::render('frontend/calendar/show.php', array( 'show' => $this ) );
    }

}