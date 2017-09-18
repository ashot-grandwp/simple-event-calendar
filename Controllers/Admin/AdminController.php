<?php
namespace GDCalendar\Controllers\Admin;

use GDCalendar\Helpers\View;
use GDCalendar\Models\PostTypes\Calendar;

class AdminController{

    /**
     * @var array
     */
    public $Pages = array();

    public function __construct()
    {
        $this->Pages = array('gd_events', 'gd_calendar', 'gd_organizers', 'gd_venues');
        add_action( 'admin_menu', array( $this, 'adminMenu' ) );
        add_filter('admin_head', array($this, 'topBanner'));
	add_action('admin_init', array( $this, 'delayNotices'), 1);

        add_filter('screen_options_show_screen', array($this, 'remove_screen_options'));
        add_filter('manage_edit-gd_calendar_columns', array(__CLASS__, 'calendarColumns'));
        add_action('manage_gd_calendar_posts_custom_column', array($this, 'calendarColumnsData'), 10, 2);
        add_action('save_post', array(__CLASS__, 'setDefaultObjectTerms'), 100, 2);
        new AdminAssetsController();
        new MetaBoxesController();
        new ShortcodeController();
    }

    public function remove_screen_options(){
        global $current_screen;
        $type = $current_screen->post_type;
        $page = $current_screen->id;

        if(!in_array($type, $this->Pages) && $page !== 'gd_events_page_gd_events_featured_plugins' && $page !== 'gd_events_page_gd_events_themes'){
            return true;
        }
        return false;
    }

    public function topBanner(){
        global $taxnow;
        global $current_screen;
        $type = $current_screen->post_type;
        $page = $current_screen->id;
        $base = $current_screen->base;

        if( $taxnow === '' && $base !== 'edit' ){
            echo '<style>.wrap h1.wp-heading-inline{display:inline-block;}</style>';
        }

        if ( in_array($type, $this->Pages) && $page !== 'gd_events_page_gd_events_settings' || $page === 'gd_events_page_gd_events_themes') {
        ?>
            <div class="gd_calendar_top_banner_container">
            <?php
            if (get_option('gd_calendar_review_notice_ignore') || get_option('gd_calendar_review_notice_delayed') &&
                (strtotime('now') - strtotime(get_option('gd_calendar_review_notice_delayed'))) < 604800 ||
                (strtotime('now') - strtotime(get_option('gd_calendar_plugin_installed'))) < 604800) {
            } else {
                View::render( 'admin/ask-for-review.php' );
            }
                View::render( 'admin/top-banner.php', array(
                    'taxonomy' => $taxnow,
                    'current_screen' => $current_screen,
                    'page' => $page
                ));
        ?>
            </div>
            <?php
        }
    }

    /* Ask user for review */
    public function delayNotices(){
        if ( isset( $_GET['gd_calendar_delay_notice'] ) ) {
            update_option('gd_calendar_review_notice_delayed', date('Y-m-d H:i:s'));

            $redirectLink = remove_query_arg( array( 'gd_calendar_delay_notice' ) );
            wp_redirect( $redirectLink );
            exit;
        } else if ( isset( $_GET['gd_calendar_ignore_notice'] ) ) {
            update_option('gd_calendar_review_notice_ignore', 1 );
            $redirectLink = remove_query_arg( array( 'gd_calendar_ignore_notice' ) );
            wp_redirect( $redirectLink );
            exit;
        }
    }

    public function adminMenu()
    {
        remove_submenu_page( 'edit.php?post_type=gd_events', 'post-new.php?post_type=gd_events' );
	    $this->Pages['themes'] = add_submenu_page( 'edit.php?post_type=gd_events', __('Themes', 'gd-calendar'), __('Themes', 'gd-calendar'), 'manage_options', 'gd_events_themes', array(__CLASS__, 'calendarThemes'));
	    $this->Pages['settings'] = add_submenu_page( 'edit.php?post_type=gd_events', __('Settings', 'gd-calendar'), __('Settings', 'gd-calendar'), 'manage_options', 'gd_events_settings', array(__CLASS__, 'calendarSettings'));
        $this->Pages['featured_plugins'] = add_submenu_page( 'edit.php?post_type=gd_events', __('Featured plugins', 'gd-calendar'), __('Featured plugins', 'gd-calendar'), 'manage_options', 'gd_events_featured_plugins', array(__CLASS__, 'calendarFeaturedPlugins'));
    }

    public static function calendarColumns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Title', 'gd-calendar'),
            'featured_image' => __('Featured Image', 'gd-calendar'),
            'shortcode' => __('Shortcode', 'gd-calendar'),
            'theme' => __('Theme', 'gd-calendar'),
            'date' => __('Date', 'gd-calendar'),
        );
        return $columns;
    }

    public static function calendarColumnsData($column, $post_id)
    {
        switch ( $column ) {
            case 'shortcode' :
            	echo '<textarea class="gd_calendar_textarea_box" readonly >[gd_calendar id="' . $post_id . '"]</textarea><br>';
            	echo '<textarea class="gd_calendar_textarea_box" readonly >&lt;?php echo do_shortcode("[gd_calendar id=\'' . $post_id .'\']"); ?&gt;</textarea>';
                break;
            case 'theme' :
                $all_themes = Calendar::$themes;
                $calendar = new Calendar($post_id);
                _e( $all_themes[$calendar->get_theme()], 'gd-calendar' );
                break;
            case 'featured_image' :
                $calendar = new Calendar($post_id);
                echo '<a href="'. $calendar->get_edit_link() . '">' . get_the_post_thumbnail( $post_id, array(50, 50)) . '</a>' ;
                break;
        }
    }

    public static function setDefaultObjectTerms($post_id, $post)
    {
        if ( 'publish' === $post->post_status && get_post_type($post_id) == 'gd_events') {
            $defaults = array( 'event_category' => 'Uncategorized' );
            $taxonomy = 'event_category';
            $terms = wp_get_post_terms( $post_id, $taxonomy );

            if ( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) {
                $affected_ids = wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
                if( is_array( $affected_ids ) && !empty( $affected_ids ) ){
                    update_option('default_event_category', $affected_ids[0]);
                }
            }
        }
    }

    public static function calendarThemes(){
        ?>
        <img src="<?php echo GDCALENDAR_IMAGES_URL . 'theme_pro.png'; ?>"/>
        <?php
    }
    public static function calendarSettings() {
        View::render('admin/calendar-settings.php');
    }

    public static function calendarFeaturedPlugins(){
        View::render('admin/calendar-featured-plugins.php');
    }
}