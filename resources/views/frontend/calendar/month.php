<?php
/**
 * Calendar Month View (Main)
 * @var $month
 */
$calendar_id = $month->get_post_id();

$dateComponents = $month->get_date_components();
$currentMonthName = $dateComponents['month'];
// What is the index value (0-6) of the first day of the month
$dayOfWeek = $dateComponents['wday'];

$lastDateComponents = $month->get_last_date_components();
// What is last month number of days
$lastDayOfMonth = $lastDateComponents['mday'];

$nextDateComponents = $month->get_next_date_components();
$nextMonthName = $nextDateComponents['month'];

$lastMonth = str_pad($lastDateComponents['mon'], 2, "0", STR_PAD_LEFT);
$currentMonth = str_pad($month->get_month(), 2, "0", STR_PAD_LEFT);
$nextMonth = str_pad($nextDateComponents['mon'], 2, "0", STR_PAD_LEFT);

$weekend_bg = 'gd_calendar_weekend_bg';
$gd_calendar_day_light = 'gd_calendar_day_light';

    if(isset($_GET['search']) && !empty($_GET['search']) && empty($month->get_searched_event())){
        ?>
        <div class="gd_calendar_message">
        <?php
            esc_html_e('No results found', 'gd-calendar'); ?>
        </div>
    <?php
    }

?>
    <table class='gd_calendar_table'>
        <tr><?php
            // calendar day of week
            $currentWeek = $month->get_weekday();

            foreach($month->get_days_of_week() as $key => $day) {
                $weekday_color = "";
                if($key == 0 || $key == 6){
                    $weekday_color = "gd_calendar_weekday";
                }

                $selectedDate = $month->get_year() . '-' . $currentMonth;
                $restOfCurrentDate = substr($month->get_current_date(), 0, 7);
                $currentWeekFont = "";
                if ($selectedDate == $restOfCurrentDate){
                    if($key == $currentWeek) {
                        $currentWeekFont = "current_week_big_size";
                    }
                }
                ?>
                <th class='gd_calendar_header <?php echo $weekday_color; echo $currentWeekFont; ?>'><?php echo $day; ?></th>
                <?php
            }

            ?></tr><tr><?php

            // Previous months days view

            $lastMonthDay = 1;
            if ($dayOfWeek > 0) {
                $lastDays = $lastDayOfMonth - $dayOfWeek + 1;
                while($lastMonthDay <= $dayOfWeek)
                {
                    $lastYear = $month->get_year();
                    if($month->get_month() == 1){
                        $lastYear = $month->get_year() - 1;
                    }

                    $lastDayRel = str_pad($lastDays, 2, "0", STR_PAD_LEFT);
                    $date = $lastYear."-$lastMonth-$lastDayRel";
                    ?>
                    <td class="gd_calendar_day <?php if($lastMonthDay == 1){ echo $weekend_bg; } ?>" rel="<?php echo $date; ?>">
                        <p class="<?php echo $gd_calendar_day_light; ?>"><?php echo $lastDays; ?></p>
                        <?php
                        \GDCalendar\Helpers\View::render('frontend/calendar/events.php', array(
                            'searched_event' => $month->get_searched_event(),
                            'date' => $date,
                            'calendar_id' => $calendar_id
                        ));
                        ?>
                    </td>
                    <?php
                    $lastMonthDay++;
                    $lastDays++;
                }
            }

            // Current month view
            $currentDay = 1;
            while ($currentDay <= $month->get_days_count()) {
            if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            ?>
        </tr><tr>
            <?php
            }
            $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
            $date = $month->get_year()."-$currentMonth-$currentDayRel";
            $current_date = '';
            if($month->get_current_date() === $date ) {
                $current_date = 'gd_calendar_current_date';
            }
            ?>
            <td class='gd_calendar_day <?php if($dayOfWeek == 0 || $dayOfWeek == 6){ echo $weekend_bg; } ?>' rel='<?php echo $date; ?>'>
                <p class="<?php echo ($currentDay != 1) ? $current_date : 'gd_calendar_first_day'; ?>"><?php if($currentDay == 1){ echo substr($currentMonthName, 0, 3) . " ";} echo $currentDay; ?></p>
                <?php
                \GDCalendar\Helpers\View::render('frontend/calendar/events.php', array(
                    'searched_event' => $month->get_searched_event(),
                    'date' => $date,
                    'calendar_id' => $calendar_id
                ));
                ?>
            </td>
            <?php

            $currentDay++;
            $dayOfWeek++;
            }

            // Next months days view

            if ($dayOfWeek != 7) {
                $remainingDays = 7 - $dayOfWeek;
                $nextMonthDay = 1;
                while($nextMonthDay <= $remainingDays)
                {
                    $nextDayRel = str_pad($nextMonthDay, 2, "0", STR_PAD_LEFT);
                    $nextYear = $month->get_year();

                    if($month->get_month() == 12){
                        $nextYear = $month->get_year() + 1;
                    }

                    $date = $nextYear ."-$nextMonth-$nextDayRel";
                    ?>
                    <td class="gd_calendar_day <?php if($nextMonthDay == $remainingDays){echo $weekend_bg;}?>" rel='<?php echo $date; ?>'>
                        <p class="<?php echo $gd_calendar_day_light; ?>"><?php if($nextMonthDay == 1){ echo substr($nextMonthName, 0, 3) . " ";} echo $nextMonthDay; ?></p>
                        <?php
                        \GDCalendar\Helpers\View::render('frontend/calendar/events.php', array(
                            'searched_event' => $month->get_searched_event(),
                            'date' => $date,
                            'calendar_id' => $calendar_id
                        ));
                        ?>
                    </td>
                    <?php
                    $nextMonthDay++;
                }
            }
            ?>
        </tr>
    </table>
