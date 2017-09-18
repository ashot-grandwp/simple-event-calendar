<?php
/**
 * @var $show Calendar Builder
 */
    $day = false;
    $week = false;
    $month = false;
    $year = false;

    $id = $show->get_post_id();
    $calendar = new \GDCalendar\Models\PostTypes\Calendar($id);
    $views = $calendar->get_view_type();

    if(!empty($views)){
	    foreach ($views as $view){
		    switch ($view){
			    case 0:
				    $day = true;
				    break;
			    case 1:
				    $week = true;
				    break;
			    case 2:
				    $month = true;
				    break;
			    case 3:
				    $year = true;
				    break;
		    }
	    }
    }
    else{
        array_push($views, 2);
        $month = true;
    }

	    $type = '';
        $show_view = '';
        $day_active = '';
        $week_active = '';
        $month_active = '';

	    if(true === $month){
	        $type = 'month';
		    $show_view = \GDCalendar\Helpers\View::buffer('frontend/calendar/month.php', array('month' => $show));
		    $month_active = 'gd_calendar_active_view';
        }
        else{
	        if( true === $day ){
	            $type = 'day';
		        $show_view = \GDCalendar\Helpers\View::buffer('frontend/calendar/day.php', array('day' => $show));
		        $day_active = 'gd_calendar_active_view';
            }
            else{
	            if( true === $week){
	                $type = 'week';
		            $show_view = \GDCalendar\Helpers\View::buffer('frontend/calendar/week.php', array('week' => $show));
		            $week_active = 'gd_calendar_active_view';
                }
                else{
	                if( true === $year){
		                $type = 'year';
		                $show_view = \GDCalendar\Helpers\View::buffer('frontend/calendar/year.php', array('year' => $show));
                    }
                }
            }
        }
?>
    <div class="gd_calendar_wrapper gd_calendar_body">
        <?php
        if(has_post_thumbnail()){ ?>
            <div class="event_thumbnail">
                <?php echo the_post_thumbnail(); ?>
            </div>
        <?php
        } ?>
        <div class="gd_calendar_main">
            <form action="" method="get" name="search" id="search">
            <div class="gd_calendar_bar">
                <div class="gd_calendar_event_box_filter">
                    <input type="text" name="gd_calendar_month_event_filter" id="gd_calendar_month_event_filter" value="<?php if(isset($_GET['gd_calendar_month_event_filter'])){echo sanitize_text_field($_GET['gd_calendar_month_event_filter']);} ?>" placeholder="<?php _e("Date", "gd-calendar"); ?>" />
                    <input type="hidden" id="date_holder" />
                    <input type="hidden" id="post_id" value="<?php echo $id; ?>" />
                </div>
                <div class="gd_calendar_event_view_box">
                    <?php
                    if(count($views) !== 1 && true === $day){ ?>
                    <button type="button" data-type="day" id="gd_calendar_day_view" class="gd_calendar_day_view <?php echo $day_active; ?>"><?php _e('Day', 'gd-calendar'); ?></button>
                    <?php } ?>
                    <?php if(count($views) !== 1 && true === $week){ ?>
                    <button type="button" data-type="week" id="gd_calendar_week_view" class="gd_calendar_week_view <?php echo $week_active; ?>"><?php _e('Week', 'gd-calendar'); ?></button>
                    <?php } ?>
	                <?php if(count($views) !== 1 && true === $month){ ?>
                    <button type="button" data-type="month" id="gd_calendar_month_view" class="gd_calendar_month_view <?php echo $month_active; ?>"><?php _e('Month', 'gd-calendar'); ?></button>
	                <?php } ?>
                    <?php if(count($views) !== 1 && true === $year){ ?>
                        <button type="button" data-type="year" id="gd_calendar_year_view" class="gd_calendar_year_view"><?php _e('Year', 'gd-calendar'); ?></button>
                    <?php } ?>
                    <input type="hidden" id="type_holder" name="type_holder" value="<?php echo $type; ?>"/>
                </div>
                <div class="gd_calendar_search_box">
                    <input type="text" name="gd_calendar_search" id="gd_calendar_search" class="gd_calendar_search" placeholder="Search" value="">
                    <input type="submit" id="gd_calendar_search_icon" value="">
                </div>
                <div class="gd_loading"></div>
            </div>
            </form>
            <div id="gd_calendar">
                <?php echo $show_view; ?>
            </div>
        </div>
    </div>
