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

		if ( !$day )
			return $this->cache;

		if ( empty( $this->cache[$day] ) )
			return null;

		if ( !$key )
			return $this->cache[$day];

		return $this->cache[$day][$key];

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


		foreach ( $days as $day ) {
			$id   = key( $day );
			$open = $close = $working = '';

			if ( !empty( $_POST['open_' . $id] ) && !empty( $_POST['close_' . $id] ) ) {
				$open    = sanitize_text_field( ( $_POST['open_' . $id] ) );
				$close   = sanitize_text_field( ( $_POST['close_' . $id] ) );
				$working = 'true';
			}

			$this->cache[$id]['open']    = $open;
			$this->cache[$id]['close']   = $close;
			$this->cache[$id]['working'] = $working;
		}

		$this->save_settings();

	}


	/***** HELPERS *****/

	private function _show_days_controls() {
		$days = business_hours()->get_week_days();
		foreach ( $days as $day ) {
			$this->_show_day_controls( esc_attr( key( $day ) ), esc_html( $day[key( $day )] ) );
		}
	}

	private function _show_day_controls( $id, $name ) {
		$open  = ( !empty( $this->cache[$id]['open'] ) ) ? $this->cache[$id]['open'] : '';
		$close = ( !empty( $this->cache[$id]['close'] ) ) ? $this->cache[$id]['close'] : '';

		include business_hours()->locate_view( 'settings-single-day.php' );

	}

}
