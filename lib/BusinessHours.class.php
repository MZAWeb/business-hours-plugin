<?php
class BusinessHours {

	const VERSION = '2.0';
	const SLUG    = 'business-hours';

	/**
	 * Singleton instance of this class
	 * @var BusinessHours
	 */
	private static $instance;

	/**
	 * Instance to the settings class
	 * @var BusinessHoursSettings
	 */
	private $settings = null;

	/**
	 * Path of this plugin's folder
	 * @var string
	 */
	private $path;

	/**
	 * URL of this plugin's folder
	 * @var string
	 */
	private $url;

	public function  __construct() {
		$this->path = trailingslashit( dirname( dirname( __FILE__ )         ) );
		$this->url  = trailingslashit( dirname( plugins_url( '', __FILE__ ) ) );

		$this->settings();
		$this->_register_exceptions();
		$this->_register_shortcodes();

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		// see https://github.com/MZAWeb/wp-log-in-browser
		add_filter( 'wplinb-match-wp-debug', '__return_true' );
	}

	/**
	 *  Load the required styles and javascript files
	 */
	public function enqueue_resources() {
		wp_enqueue_style ( 'business_hours_style',  $this->url . 'resources/business-hours.css'                   );
		wp_enqueue_script( 'business_hours_script', $this->url . 'resources/business-hours.js', array( 'jquery' ) );
	}

	/**
	 *  Registers the widget
	 */
	public function register_widgets() {
		if ( ! class_exists( 'BusinessHoursWidget' ) )
			include 'BusinessHoursWidget.class.php';
		register_widget( 'BusinessHoursWidget' );
	}

	/**
	 * Getter for the settings class with lazy load
	 * @return BusinessHoursSettings
	 */
	public function settings() {

		if ( empty( $this->settings ) ) {

			if ( ! class_exists( 'BusinessHoursSettings' ) )
				include 'BusinessHoursSettings.class.php';

			$this->settings = new BusinessHoursSettings();
		}

		return $this->settings;
	}

	/**
	 * Today's hours shortcode handler.
	 *
	 * @param array $atts
	 * @param null  $content
	 *
	 * @url https://github.com/MZAWeb/business-hours-plugin/wiki/Shortcodes
	 *
	 * @return string
	 */
	public function shortcode( $atts, $content = null ) {

		$closed_text = business_hours()->settings()->get_default_closed_text();

		$atts = shortcode_atts( array( 'closed' => $closed_text ), $atts );

		if ( empty( $content ) )
			return $content;

		$day = $this->get_day_using_timezone();
		$id  = key( $day );

		$open          = esc_html( business_hours()->settings()->get_open_hour( $id ) );
		$close         = esc_html( business_hours()->settings()->get_close_hour( $id ) );
		$is_open_today = business_hours()->settings()->is_open( $id );

		if ( $is_open_today ) {
			$content = str_replace( "{{TodayOpen}}",  $open,  $content );
			$content = str_replace( "{{TodayClose}}", $close, $content );
		} else {
			$content = $atts['closed'];
		}

		return $content;
	}

	/**
	 *
	 * Everyday hours shortcode handler.
	 *
	 * @param      $atts
	 *
	 * @url https://github.com/MZAWeb/business-hours-plugin/wiki/Shortcodes
	 *
	 * @return string
	 */
	public function shortcode_table( $atts ) {

		$atts = shortcode_atts( array( 'collapsible' => 'false', ), $atts );

		$collapsible = ( strtolower( $atts['collapsible'] ) === "true" ) ? true : false;

		if ( $collapsible )
			$this->enqueue_resources();

		return $this->get_table( $collapsible );
	}


	/**
	 * Get the today's day name depending on the WP setting.
	 * To adjust your timezone go to Settings->General
	 *
	 * @return array [day_number int]day_name string
	 */
	public function get_day_using_timezone() {

		$timestamp = $this->get_timestamp_using_timezone();

		$arr = array( strtolower( gmdate( 'w', $timestamp ) ) => ucwords( date_i18n( 'l', $timestamp ) ) );

		return $arr;
	}

	/**
	 * Get the current timestamp in the timezone set up in Settings->General
	 * @return int
	 */
	public function get_timestamp_using_timezone() {

		$timezone_string = get_option( 'timezone_string' );

		if ( ! empty( $timezone_string ) ) {
			$zone      = new DateTimeZone( $timezone_string );
			$datetime  = new DateTime( 'now', $zone );
			$timestamp = time() + $datetime->getOffset();
		} else {
			$offset    = get_option( 'gmt_offset' );
			$offset    = $offset * HOUR_IN_SECONDS;
			$timestamp = time() + $offset;
		}

		return $timestamp;
	}

	/**
	 * Get the internationalized days names
	 * in the correct order for the start_of_week
	 * the user configured in Settings->General
	 *
	 * @return array
	 */
	public function get_week_days() {
		global $wp_locale;

		$days          = $wp_locale->weekday;
		$start_of_week = get_option( 'start_of_week' );

		if ( !$start_of_week )
			return $days;

		$first  = array_slice( $days, 0, $start_of_week, true );
		$second = array_slice( $days, $start_of_week, count( $days ), true );

		$days = $second + $first;

		return $days;
	}


	/**
	 * Echo the table with the open/close hours for each day of the week
	 *
	 * @param bool $collapsible_link
	 *
	 * @filter business-hours-collapsible-link-anchor
	 *
	 */
	public function show_table( $collapsible_link = true ) {

		$days     = $this->get_week_days();
		$template = business_hours()->locate_view( 'table.php' );

		$collapsible_link_anchor = apply_filters( 'business-hours-collapsible-link-anchor', __( '[Show working hours]', 'business-hours' ) );

		if ( ! empty( $template ) )
			include $template;
	}

	/**
	 * Returns the table with the open/close hours for each day of the week
	 *
	 * @param bool $collapsible_link
	 *
	 * @return string
	 */
	public function get_table( $collapsible_link = true ) {
		ob_start();
		$this->show_table( $collapsible_link );
		return ob_get_clean();
	}

	/**
	 *
	 * Echo the row for the given day for the hours table.
	 *
	 * @param $id
	 * @param $day_name
	 *
	 * @filter business-hours-closed-text
	 * @filter business-hours-open-hour
	 * @filter business-hours-close-hour
	 * @filter business-hours-is-open-today
	 *
	 */
	private function _table_row( $id, $day_name ) {
		$ret = "";

		$open          = esc_html( business_hours()->settings()->get_open_hour( $id  ) );
		$close         = esc_html( business_hours()->settings()->get_close_hour( $id ) );
		$is_open_today = business_hours()->settings()->is_open( $id );
		$closed_text   = business_hours()->settings()->get_default_closed_text();

		do_action( 'business-hours-before-row', $id, $day_name, $open, $close, $is_open_today );

		$class = apply_filters( 'business-hours-row-class', '', $id, $day_name );

		$template = business_hours()->locate_view( 'table-row.php' );

		if ( ! empty( $template ) )
			include $template;

		do_action( 'business-hours-after-row', $id, $day_name, $open, $close, $is_open_today );

	}

	/**
	 * Registers the shortcodes
	 */
	private function _register_shortcodes() {
		add_shortcode( 'businesshours',     array( $this, 'shortcode'       ) );
		add_shortcode( 'businesshoursweek', array( $this, 'shortcode_table' ) );
	}

	/**
	 *  Starts the exceptions engine
	 */
	private function _register_exceptions() {
		if ( ! class_exists( 'BusinessHoursExceptions' ) )
			include 'BusinessHoursExceptions.class.php';
		BusinessHoursExceptions::instance();
	}

	/**
	 * Allows users to overide views templates.
	 *
	 * It'll first check if the given $template is present in a business-hours folder in the user's theme.
	 * If the user didn't create an overide, it'll load the default file from this plugin's views template.
	 *
	 * @param $template
	 * @param $overridable
	 *
	 * @return string
	 */
	public function locate_view( $template, $overridable = true ) {
		if ( $overridable && $theme_file = locate_template( array( 'business-hours/' . $template ) ) ) {
			$file = $theme_file;
		} else {
			$file = $this->path . 'views/' . $template;
		}
		return apply_filters( 'business-hours-view-template', $file, $template );
	}

	/**
	 * @param $var
	 */
	public function log( $var ) {
		// see https://github.com/MZAWeb/wp-log-in-browser
		if ( function_exists( 'browser' ) && constant( "Browser::AUTHOR" ) && Browser::AUTHOR === 'MZAWeb' )
			browser()->log( $var );
	}


	/**
	 * Returns the singleton instance for this class.
	 *
	 * @static
	 * @return BusinessHours
	 */
	public static function instance() {
		if ( !isset( self::$instance ) ) {
			$className      = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

}

if ( !function_exists( 'business_hours' ) ) {
	/**
	 * Shorthand for BusinessHours::instance()
	 *
	 * @return BusinessHours
	 */
	function business_hours() {
		return BusinessHours::instance();
	}
}