<?php
class BusinessHours {

	const VERSION = '2.0';
	const SLUG    = 'business-hours';

	/**
	 * @var BusinessHours
	 */
	private static $instance;
	/**
	 * @var BusinessHoursSettings
	 */
	private $settings;


	private $path;
	private $url;

	public function  __construct() {
		$this->path = trailingslashit( dirname( dirname( __FILE__ ) ) );
		$this->url  = trailingslashit( dirname( plugins_url( '', __FILE__ ) ) );

		$this->_register_settings();
		$this->_register_shortcodes();
	}

	/**
	 *  Load the required styles and javascript files
	 */
	public function enqueue_resources() {
		wp_enqueue_style( 'business_hours_style', $this->url . 'resources/business-hours.css' );
		wp_enqueue_script( 'business_hours_script', $this->url . 'resources/business-hours.js', array( 'jquery' ) );
	}

	/**
	 *
	 * Today's hours shortcode handler.
	 * See https://github.com/MZAWeb/business-hours-plugin/wiki/Shortcodes
	 *
	 * @param      $atts
	 * @param null $content
	 *
	 * @return mixed|null
	 */
	public function shortcode( $atts, $content = null ) {

		$closed_text = business_hours()->settings()->get_default_closed_text();

		extract( shortcode_atts( array( 'closed' => $closed_text ), $atts ) );

		if ( empty( $content ) )
			return $content;

		$day = $this->get_day_using_timezone();
		$id  = key( $day );

		$open          = esc_html( business_hours()->settings()->get_open_hour( $id ) );
		$close         = esc_html( business_hours()->settings()->get_close_hour( $id ) );
		$is_open_today = business_hours()->settings()->is_open( $id );

		if ( $is_open_today ) {
			$content = str_replace( "{{TodayOpen}}", $open, $content );
			$content = str_replace( "{{TodayClose}}", $close, $content );
		} else {
			$content = $closed;
		}

		return $content;
	}

	/**
	 *
	 * Everyday hours shortcode handler.
	 * See https://github.com/MZAWeb/business-hours-plugin/wiki/Shortcodes
	 *
	 * @param      $atts
	 *
	 * @return mixed|null
	 */
	public function shortcode_table( $atts ) {

		extract( shortcode_atts( array( 'collapsible' => 'false', ), $atts ) );
		$collapsible = ( strtolower( $collapsible ) === "true" ) ? true : false;

		if ( $collapsible )
			$this->enqueue_resources();

		return $this->get_table( $collapsible );
	}


	/**
	 * Get the today's day name depending on the WP setting.
	 * To adjust your timezone go to Settings->General
	 *
	 * @return array
	 */
	public function get_day_using_timezone() {

		if ( get_option( 'timezone_string' ) ) {
			$zone      = new DateTimeZone( get_option( 'timezone_string' ) );
			$timestamp = new DateTime( 'now', $zone );
			$timestamp = strtotime( $timestamp->date );
		} else {
			$offset    = get_option( 'gmt_offset' );
			$offset    = $offset * 60 * 60;
			$timestamp = time() + $offset;
		}

		$arr = array( strtolower( gmdate( 'l', $timestamp ) )  => ucwords( date_i18n( 'l', $timestamp ) ) );
		return $arr;
	}

	/**
	 *
	 * Get the internationalized days names
	 *
	 * @return array
	 */
	public function get_week_days() {
		$timestamp = strtotime( 'next Sunday' );
		$days      = array();
		for ( $i = 0; $i < 7; $i++ ) {
			$days[]    = array( strtolower( gmdate( 'l', $timestamp ) )  => ucwords( date_i18n( 'l', $timestamp ) ) );
			$timestamp = strtotime( '+1 day', $timestamp );
		}
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
		$days = $this->get_week_days();

		$collapsible_link_anchor = apply_filters( 'business-hours-collapsible-link-anchor', '[Show working hours]' );

		include business_hours()->locate_view( 'table.php' );
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
	 * @param $day
	 *
	 * @filter business-hours-closed-text
	 * @filter business-hours-open-hour
	 * @filter business-hours-close-hour
	 * @filter business-hours-is-open-today
	 *
	 */
	private function _table_row( $day ) {
		$ret      = "";
		$id       = key( $day );
		$day_name = $day[$id];

		$open          = esc_html( business_hours()->settings()->get_open_hour( $id ) );
		$close         = esc_html( business_hours()->settings()->get_close_hour( $id ) );
		$is_open_today = business_hours()->settings()->is_open( $id );
		$closed_text   = business_hours()->settings()->get_default_closed_text();

		include business_hours()->locate_view( 'table-row.php' );

	}

	/**
	 *
	 */
	private function _register_shortcodes() {
		add_shortcode( 'businesshours', array( $this, 'shortcode' ) );
		add_shortcode( 'businesshoursweek', array( $this, 'shortcode_table' ) );
	}

	/**
	 * @return BusinessHoursSettings
	 */
	public function settings() {
		return $this->settings;
	}

	/**
	 *  Register the settings to create the settings screen
	 *
	 */
	private function _register_settings() {
		$this->settings = new BusinessHoursSettings();
	}

	/**
	 * Allows users to overide views templates.
	 *
	 * It'll first check if the given $template is present in a business-hours folder in the user's theme.
	 * If the user didn't create an overide, it'll load the default file from this plugin's views template.
	 *
	 * @param $template
	 *
	 * @return string
	 */
	public function locate_view( $template ) {
		if ( $theme_file = locate_template( array( 'business-hours/' . $template ) ) ) {
			$file = $theme_file;
		} else {
			$file = $this->path . 'views/' . $template;
		}
		return apply_filters( 'business-hours-view-template', $file, $template );
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