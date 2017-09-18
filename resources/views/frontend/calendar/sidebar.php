<?php
/**
 * Calendar Month Sidebar View
 * @var $sidebar_month
 */

$calendar_id = $sidebar_month->get_post_id();
?>
<div class="gd_calendar_sidebar" data-calendar-id="<?php echo $calendar_id; ?>">
<?php
$sidebar = true;
$dateComponents = $sidebar_month->get_date_components();
$currentMonthName = $dateComponents['month'];
$currentYear = $sidebar_month->get_year();
$dayOfWeek = $dateComponents['wday'];
$currentMonth = str_pad($sidebar_month->get_month(), 2, "0", STR_PAD_LEFT);
$currentDate = date("m/d/Y", strtotime($sidebar_month->get_current_date()));
$hold_month = $sidebar_month->get_month() . '/01/' . $sidebar_month->get_year();

$first_week_number = absint($sidebar_month->get_current_weekday_number($sidebar_month->get_year()."-".$currentMonth."-01"));
$current_week_number = absint($sidebar_month->get_current_weekday_number($currentDate));
?>
    <div class="gd_calendar_small_date" data-date="<?php echo $hold_month; ?>">
        <span><?php echo $currentMonthName; ?></span>
        <span class="current_year_color"><?php echo $currentYear; ?></span>
    </div>
    <div class="gd_calendar_arrow_box">
        <a href="#" id="gd_calendar_left_arrow" data-type="left_arrow"><span>&#10094;</span></a>
        <a href="#" id="gd_calendar_right_arrow" data-type="right_arrow"><span>&#10095;</span></a>
    </div>
    <div class="gd_calendar_small">
        <table class='gd_calendar_small_table'>
            <tr><?php
                foreach($sidebar_month->get_days_of_week() as $key => $day) {
                    ?>
                    <th class='gd_calendar_header_small'><?php echo $day; ?></th>
                    <?php
                }
                ?></tr><tr class="<?php echo ($first_week_number === $current_week_number) ? 'gd_calendar_current_week_number' : '' ?>"><?php
                for($i=1; $i <= $dayOfWeek; $i++){
                    echo '<td></td>';
                }
                $currentDay = 1;
                while ($currentDay <= $sidebar_month->get_days_count()) {
                $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
                $date = $sidebar_month->get_year()."-$currentMonth-$currentDayRel";
                $week_number = absint($sidebar_month->get_current_weekday_number($date));
                if ($dayOfWeek == 7) {
                $dayOfWeek = 0;
                ?>
            </tr><tr class="<?php echo ($week_number === $current_week_number) ? 'gd_calendar_current_week_number' : '' ?>">
                <?php
                }
                $current_date = '';
                if($sidebar_month->get_current_date() === $date ) {
                    $current_date = 'gd_calendar_current_date_small';
                }
                ?>
                <td class='gd_calendar_day_small' rel='<?php echo $date; ?>'>
                    <div class="<?php echo $current_date; ?>">
                        <p><?php echo $currentDay; ?></p>
                        <?php
                        \GDCalendar\Helpers\View::render('frontend/calendar/events.php', array(
                            'searched_event' => $sidebar_month->get_searched_event(),
                            'date' => $date,
                            'sidebar' => $sidebar,
                            'calendar_id' => $calendar_id
                        ));
                        ?>
                    </div>
                </td>
                <?php
                $currentDay++;
                $dayOfWeek++;
                }
                for( $i=1; $i <= (7- $dayOfWeek); $i++ ){
                    echo '<td></td>';
                }
                ?>
            </tr>
        </table>
    </div>
</div>