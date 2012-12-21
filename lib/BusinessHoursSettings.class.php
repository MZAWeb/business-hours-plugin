<?php

if ( class_exists( 'BusinessHoursSettings' ) )
	return;

class BusinessHoursSettings {

	const SETTINGS           = 'business_hours_settings';
	const PRE_20_SETTINGS    = 'working-hours_settings';
	const SETTING_HOURS      = 'hours';
	const SETTING_EXCEPTIONS = 'exceptions';

	private static $saved = false;
	private $cache = false;
	private $page;
	private $path;
	private $url;

	public function __construct() {
		$this->path = trailingslashit( dirname( dirname( __FILE__ ) ) );
		$this->url  = trailingslashit( dirname( plugins_url( '', __FILE__ ) ) );

		$this->_load_settings();

		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'init',       array( $this, 'maybe_upgrade'     ) );

		add_action( 'business-hours-settings-page', array( $this, 'show_days_settings' ), 1 );
		add_filter( 'business-hours-save-settings', array( $this, 'maybe_save_settings_hours' ), 1 );
	}



	/**
	 * @param $day
	 *
	 * @return mixed|void
	 */
	public function get_open_hour( $day ) {
		$open = apply_filters( "business-hours-open-hour", $this->_get_business_hours( $day, "open" ), $day );
		return $open;
	}

	/**
	 * @param $day
	 *
	 * @return mixed|void
	 */
	public function get_close_hour( $day ) {
		$close = apply_filters( "business-hours-close-hour", $this->_get_business_hours( $day, "close" ), $day );
		return $close;
	}

	/**
	 * @return bool
	 */
	public function get_full_settings() {
		if ( empty( $this->cache ) )
			$this->_load_settings();

		return $this->cache;
	}

	/**
	 * @param $day
	 *
	 * @return mixed|void
	 */
	public function is_open( $day ) {
		$open    = $this->get_open_hour( $day );
		$close   = $this->get_close_hour( $day );
		$is_open = !empty( $open ) && !empty( $close );
		return apply_filters( 'business-hours-is-open-today', $is_open, $day );
	}

	/**
	 * @return mixed|void
	 */
	public function get_default_closed_text() {
		return apply_filters( "business-hours-closed-text", __( "Closed", "business-hours" ) );
	}

	/**** ADMIN PAGE ****/

	/**
	 *
	 */
	public function enqueue_resources() {
		wp_enqueue_style(  'business_hours_admin_style',  $this->url . 'resources/business-hours-admin.css'                   );
		wp_enqueue_script( 'business_hours_admin_script', $this->url . 'resources/business-hours-admin.js', array( 'jquery' ) );
	}

	/**
	 *
	 */
	public function add_settings_page() {
		$this->page = add_options_page( __( 'Business Hours', 'business-hours' ), __( 'Business Hours', 'business-hours' ), 'manage_options', BusinessHours::SLUG, array( $this,
		                                                                                                                                                                  'do_settings_page' ) );
		add_action( 'admin_print_scripts-' . $this->page, array( $this, 'enqueue_resources' ) );
	}


	/**
	 *
	 */
	public function do_settings_page() {
		$this->_maybe_save_settings();
		business_hours()->log( $this->cache );
		include business_hours()->locate_view( 'settings.php', false );
	}

	/**
	 *
	 */
	private function _maybe_save_settings() {

		if ( empty( $_POST['action'] ) || $_POST['action'] != 'update' )
			return;

		if ( empty( $_POST['bh_nonce'] ) || !wp_verify_nonce( $_POST['bh_nonce'], BusinessHours::SLUG ) )
			return;

		$this->cache = array();

		$this->cache = apply_filters( 'business-hours-save-settings', $this->cache );

		$this->_save_settings();

		self::$saved = true;

	}

	/**
	 * @param $cache
	 *
	 * @return mixed
	 */
	public function maybe_save_settings_hours( $cache ) {

		$days = business_hours()->get_week_days();

		foreach ( $days as $id => $day ) {
			$open = $close = '';

			if ( !empty( $_POST['open_' . $id] ) && !empty( $_POST['close_' . $id] ) ) {
				$open  = sanitize_text_field( ( $_POST['open_' . $id] ) );
				$close = sanitize_text_field( ( $_POST['close_' . $id] ) );
			}

			$cache[self::SETTING_HOURS][$id]['open']  = $open;
			$cache[self::SETTING_HOURS][$id]['close'] = $close;
		}

		return $cache;

	}

	/***** HELPERS *****/

	/**
	 *
	 */
	private function _load_settings() {
		$this->cache = get_option( BusinessHoursSettings::SETTINGS );
	}

	/**
	 *
	 */
	private function _save_settings() {

		if ( !isset( $this->cache[self::SETTING_HOURS] ) )
			$this->cache[self::SETTING_HOURS] = array();
		if ( !isset( $this->cache[self::SETTING_EXCEPTIONS] ) )
			$this->cache[self::SETTING_EXCEPTIONS] = array();

		update_option( BusinessHoursSettings::SETTINGS, $this->cache );
	}

	/**
	 * @param null $day
	 * @param null $key
	 *
	 * @return bool|null
	 */
	private function _get_business_hours( $day = null, $key = null ) {
		if ( empty( $this->cache ) )
			$this->_load_settings();

		if ( $day === null )
			return $this->cache;

		if ( empty( $this->cache[self::SETTING_HOURS][$day] ) )
			return null;

		if ( $key === null )
			return $this->cache[$day];

		return $this->cache[self::SETTING_HOURS][$day][$key];
	}

	/*********** HELPERS: ADMIN SCREEN ***************/

	/**
	 *
	 */
	private function _maybe_show_updated_notice() {
		if ( !self::$saved )
			return;

		echo '<div id="setting-error-settings_updated" class="updated settings-error">';
		echo '<p><strong>' . __( 'Settings saved.' ) . '</strong></p></div>';
	}

	/**
	 *
	 */
	public function show_days_settings() {
		include business_hours()->locate_view( 'settings-days.php' );
	}

	/**
	 *
	 */
	private function _show_days_controls() {
		$days = business_hours()->get_week_days();
		foreach ( $days as $id => $day ) {
			$this->_show_day_controls( $id, esc_html( $day ) );
		}
	}

	/**
	 * @param $id
	 * @param $name
	 */
	private function _show_day_controls( $id, $name ) {
		$open  = $this->get_open_hour( $id );
		$close = $this->get_close_hour( $id );

		include business_hours()->locate_view( 'settings-day-single.php', false );

	}

	/**
	 *
	 */
	private function _show_support_form() {
		include business_hours()->locate_view( 'settings-support.php' );
	}

	/***************** Upgrades *****************/

	public function maybe_upgrade() {

		$upgraded = false;

		if ( get_option( self::PRE_20_SETTINGS ) )
			$upgraded = $this->upgrade_1x_20();

		if ( $upgraded )
			add_action( 'admin_notices', array( $this, 'upgrade_notice' ) );

	}

	public function upgrade_notice() {
		$message = sprintf( __( 'Thanks for upgrading <strong>Business Hours</strong> to the %s version. Go to the <a href="%s">settings page</a> to check the new features. If you have any suggestion or issue, <a href="">create a support ticket</a>.', 'business-hours' ), BusinessHours::VERSION, get_admin_url( null, 'options-general.php?page=' . BusinessHours::SLUG ), 'https://github.com/MZAWeb/business-hours-plugin/issues' );
		echo sprintf( '<div id="message" class="updated"><p>%s</p></div>', $message );
	}

	public function upgrade_1x_20() {

		$old_settings = get_option( self::PRE_20_SETTINGS );
		$cache        = array();

		foreach ( business_hours()->get_week_days() as $id => $name ) {
			$name = strtolower( $name );
			if ( isset( $old_settings[$name] ) ) {
				$cache[self::SETTING_HOURS][$id]['open']  = isset( $old_settings[$name]['open'] ) ? $old_settings[$name]['open'] : "";
				$cache[self::SETTING_HOURS][$id]['close'] = isset( $old_settings[$name]['close'] ) ? $old_settings[$name]['close'] : "";
			}
		}

		$this->cache = $cache;
		$this->_save_settings();

		delete_option(self::PRE_20_SETTINGS);

		return true;
	}

}
