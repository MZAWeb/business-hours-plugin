<?php

if ( class_exists( 'BusinessHoursSettings' ) )
	return;

class BusinessHoursSettings {

	const SETTINGS        = 'business_hours_settings';
	const PRE_20_SETTINGS = 'working-hours_settings';

	private $cache = false;
	private $page;
	private $path;
	private $url;

	public function __construct() {
		$this->path = trailingslashit( dirname( dirname( __FILE__ ) ) );
		$this->url  = trailingslashit( dirname( plugins_url( '', __FILE__ ) ) );

		$this->load_settings();

		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
	}

	public function get_business_hours( $day = null, $key = null ) {
		if ( empty( $this->cache ) )
			$this->cache = get_option( self::PRE_20_SETTINGS );

		if ( $day === null )
			return $this->cache;

		if ( empty( $this->cache[$day] ) )
			return null;

		if ( $key === null )
			return $this->cache[$day];

		return $this->cache[$day][$key];

	}

	public function get_open_hour( $day ) {
		$open = apply_filters( "business-hours-open-hour", $this->get_business_hours( $day, "open" ), $day );
		return $open;
	}

	public function get_close_hour( $day ) {
		$close = apply_filters( "business-hours-close-hour", $this->get_business_hours( $day, "close" ), $day );
		return $close;
	}

	public function is_open( $day ) {
		$open    = $this->get_open_hour( $day );
		$close   = $this->get_close_hour( $day );
		$is_open = !empty( $open ) && !empty( $close );
		return apply_filters( 'business-hours-is-open-today', $is_open, $day );
	}

	public function get_default_closed_text() {
		return apply_filters( "business-hours-closed-text", __( "Closed", "business-hours" ) );
	}

	private function load_settings() {
		$this->cache = get_option( BusinessHoursSettings::PRE_20_SETTINGS );
	}

	private function save_settings() {
		update_option( BusinessHoursSettings::PRE_20_SETTINGS, $this->cache );
	}

	/**** ADMIN PAGE ****/

	public function enqueue_resources() {
		wp_enqueue_style( 'idealforms', $this->url . 'resources/jquery.idealforms.min.css' );
		wp_enqueue_script( 'idealforms', $this->url . 'resources/jquery.idealforms.min.js', array( 'jquery' ) );

		wp_enqueue_style( 'business_hours_admin_style', $this->url . 'resources/business-hours-admin.css' );
		wp_enqueue_script( 'business_hours_admin_script', $this->url . 'resources/business-hours-admin.js', array( 'jquery',
		                                                                                                           'idealforms' ) );
	}

	public function add_settings_page() {
		$this->page = add_options_page( __( 'Business Hours', 'business-hours' ), __( 'Business Hours', 'business-hours' ), 'manage_options', BusinessHours::SLUG, array( $this,
		                                                                                                                                                                  'do_settings_page' ) );
		add_action( 'admin_print_scripts-' . $this->page, array( $this, 'enqueue_resources' ) );
	}

	public function do_settings_page() {

		$this->maybe_save_settings();

		include business_hours()->locate_view( 'settings.php' );
	}

	private function maybe_save_settings() {

		if ( empty( $_POST['action'] ) || $_POST['action'] != 'update' )
			return;

		if ( empty( $_POST['bh_nonce'] ) || !wp_verify_nonce( $_POST['bh_nonce'], BusinessHours::SLUG ) )
			return;

		$this->cache = array();

		$days = business_hours()->get_week_days();

		foreach ( $days as $id => $day ) {
			$open = $close = '';

			if ( !empty( $_POST['open_' . $id] ) && !empty( $_POST['close_' . $id] ) ) {
				$open    = sanitize_text_field( ( $_POST['open_' . $id] ) );
				$close   = sanitize_text_field( ( $_POST['close_' . $id] ) );
			}

			$this->cache[$id]['open']    = $open;
			$this->cache[$id]['close']   = $close;
		}

		$this->save_settings();

	}


	/***** HELPERS *****/

	private function _show_days_controls() {
		$days = business_hours()->get_week_days();
		foreach ( $days as $id => $day ) {
			$this->_show_day_controls($id, esc_html( $day ) );
		}
	}

	private function _show_day_controls( $id, $name ) {
		$open  = ( !empty( $this->cache[$id]['open'] ) ) ? $this->cache[$id]['open'] : '';
		$close = ( !empty( $this->cache[$id]['close'] ) ) ? $this->cache[$id]['close'] : '';

		include business_hours()->locate_view( 'settings-single-day.php' );

	}

	private function _show_support_form() {
		include business_hours()->locate_view( 'support.php' );
	}

}
