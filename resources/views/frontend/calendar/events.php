<?php
/**
 * @var $date
 * @var $searched_event
 * @var $sidebar
 * @var calendar_id
 */
?>
<div class="<?php echo (isset($sidebar)) ? 'gd_calendar_day_event_small' : 'gd_calendar_day_event'; ?>">
    <?php
    /**
     * Month search logic
     */

    if(isset($_POST['search']) && !empty($_POST['search'])){
        $_GET['search'] = sanitize_text_field($_POST['search']);
    }
    $counter = 0;
    if(isset($_GET['search'])){
        foreach ($searched_event as $event){
            $event_id = absint($event);
            $get_searched_event = new \GDCalendar\Models\PostTypes\Event($event_id);
            $event_dates = \GDCalendar\Helpers\CalendarBuilder::eventDateRange($get_searched_event->get_start_date(), $get_searched_event->get_end_date());
            $start_time = sanitize_text_field(substr($get_searched_event->get_start_date(),11,8));
            $end_time = sanitize_text_field(substr($get_searched_event->get_end_date(),11,8));
            $all_day = "";
            if($start_time == "" || $end_time == ""){
                $all_day = __('all day','gd-calendar');
            }
            foreach ($event_dates as $event_date){
                if($date === substr($event_date, 0, 10)){
                    if ($counter < 3) {
                        $circle = '';
                        $event_background = '';
                        if($counter == 0){
                            $circle = 'circle_first';
                            $event_background = 'background_first';
                        }
                        elseif($counter == 1){
                            $circle = 'circle_second';
                            $event_background = 'background_second';
                        }
                        elseif($counter == 2){
                            $circle = 'circle_third';
                            $event_background = 'background_third';
                        }

                        if(isset($sidebar)){
                            ?>
                            <span class="<?php echo $circle; ?>"></span>
                            <?php
                        }
                        else{
                            \GDCalendar\Helpers\View::render('frontend/calendar/one-event.php', array(
                                'event_id' => $event_id,
                                'start_time' => $start_time,
                                'all_day' => $all_day,
                                'start_date' => $get_searched_event->get_start_date(),
                                'end_date' => $get_searched_event->get_end_date(),
                                'circle' => $circle,
                                'event_background' => $event_background,
                                'calendar_id' => $calendar_id
                            ));
                        }
                    }
                    $counter++;
                }
            }
        }
        if(!isset($sidebar)){
            if ($counter > 3) {
                \GDCalendar\Helpers\View::render('frontend/calendar/more-event.php', array(
                    'counter' => $counter,
                ));
            }
        }
    }
    else{
        $calendar = new \GDCalendar\Models\PostTypes\Calendar($calendar_id);
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

        $events = \GDCalendar\Models\PostTypes\Event::get(array(
                'post_status' => 'publish',
                'tax_query' => array(
                    $tax_param,
                ))
        );

        if($events && !empty($events)) {

            foreach ($events as $event) {
                $event_id = absint($event->get_id());
                if (!empty($selected_categories)) {
                    if ($post_type === 'gd_organizers') {
                        $organizers = $event->get_event_organizer();
                        $org_result = array_intersect($organizers, $selected_categories);
                        $result = (!empty($org_result)) ? true : false;
                    } elseif ($post_type === 'gd_venues') {
                        $venue = $event->get_event_venue();
                        $result = in_array($venue, $selected_categories);
                    } else {
                        $result = true;
                    }
                } else {
                    $result = true;
                }

                if (true === $result) {
                    $event_dates = \GDCalendar\Helpers\CalendarBuilder::eventDateRange($event->get_start_date(), $event->get_end_date());
                    $start_time = sanitize_text_field(substr($event->get_start_date(), 11, 8));
                    $end_time = sanitize_text_field(substr($event->get_end_date(), 11, 8));
                    $all_day = "";
                    if ($start_time == "" || $end_time == "") {
                        $all_day = __('All day', 'gd-calendar');
                    }
                    foreach ($event_dates as $event_date) {
                        if ($date === substr($event_date, 0, 10)) {
                            if ($counter < 3) {
                                $circle = '';
                                $event_background = '';
                                if ($counter == 0) {
                                    $circle = 'circle_first';
                                    $event_background = 'background_first';
                                } elseif ($counter == 1) {
                                    $circle = 'circle_second';
                                    $event_background = 'background_second';
                                } elseif ($counter == 2) {
                                    $circle = 'circle_third';
                                    $event_background = 'background_third';
                                }

                                if (isset($sidebar)) {
                                    ?>
                                    <span class="<?php echo $circle; ?>"></span>
                                    <?php
                                } else {
                                    \GDCalendar\Helpers\View::render('frontend/calendar/one-event.php', array(
                                        'event_id' => $event_id,
                                        'start_time' => $start_time,
                                        'all_day' => $all_day,
                                        'start_date' => $event->get_start_date(),
                                        'end_date' => $event->get_end_date(),
                                        'circle' => $circle,
                                        'event_background' => $event_background,
                                        'calendar_id' => $calendar_id
                                    ));
                                }
                            }
                            $counter++;
                        }
                    }
                }
            }
        }
        if(!isset($sidebar)) {
            if ($counter > 3) {
                \GDCalendar\Helpers\View::render('frontend/calendar/more-event.php', array(
                    'counter' => $counter,
                ));
            }
        }
    }
    ?>
</div>
