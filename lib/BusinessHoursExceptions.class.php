<?php
class BusinessHoursExceptions {

	const SETTINGS_EXCEPTIONS = 'exceptions';

	private static $_instance;

	public function __construct() {
		//$this->_exceptions = business_hours()->settings()->get_exceptions();

		require 'BusinessHoursTemporalExpressionsEngine.class.php';

		add_action( 'business-hours-settings-page', array( $this, 'show_exceptions_settings' ), 2 );
		add_filter( 'business-hours-save-settings', array( $this, 'maybe_save_settings_exceptions' ), 2 );

	}

	private function _get_exceptions() {
		$settings = business_hours()->settings()->get_full_settings();
		return $settings[self::SETTINGS_EXCEPTIONS];
	}

	/************ SETTINGS ***********/

	public function show_exceptions_settings() {
		include business_hours()->locate_view( 'settings-exceptions.php' );
	}

	public function maybe_save_settings_exceptions( $cache ) {

		$cache[self::SETTINGS_EXCEPTIONS] = array();

		if ( empty( $_POST['exception_day'] ) )
			return $cache;

		$days   = $_POST['exception_day'];
		$months = $_POST['exception_month'];
		$years  = $_POST['exception_year'];
		$open   = $_POST['exception_open'];
		$close  = $_POST['exception_close'];

		/* No exceptions */
		if ( empty( $days ) )
			return $cache;


		foreach ( $days as $index => $day ) {
			/* The first one is the model jQuery uses to clone.
			 * It's invisible to the user and we dont' care about it */
			if ( $index === 0 )
				continue;

			$cache[self::SETTINGS_EXCEPTIONS][] = array( 'day'   => $day,
			                                             'month' => $months[$index],
			                                             'year'  => $years[$index],
			                                             'open'  => $open[$index],
			                                             'close' => $close[$index] );

		}

		return $cache;
	}

	/************ SETTINGS HELPERS ***********/

	private function _show_exceptions() {
		$exceptions = $this->_get_exceptions();

		// Include hidden base rule, for jQuery to clone
		$exception_number = 0;

		$day = $month = $year = $open = $close = '';

		include business_hours()->locate_view( 'settings-exception-single.php', false );

		foreach ( $exceptions as $exception_number => $exception ) {

			// 0 is reserverd for the base rule
			$exception_number++;
			$day   = esc_attr( $exception['day'] );
			$month = esc_attr( $exception['month'] );
			$year  = esc_attr( $exception['year'] );
			$open  = esc_attr( $exception['open'] );
			$close = esc_attr( $exception['close'] );

			include business_hours()->locate_view( 'settings-exception-single.php', false );

		}
	}

	private function _show_exceptions_instructions() {
		$exception_number = 0;
		include business_hours()->locate_view( 'settings-exception-instructions.php' );
	}

	private function _show_exception_days( $selected ) {
		echo sprintf( '<option %s value="%s">%s</option>', selected( $selected, 'every', false ), 'every', __( 'Every day', 'business-hours' ) );
		echo sprintf( '<option %s value="%s">%s</option>', selected( $selected, 'monfri', false ), 'monfri', __( 'Mondays to Fridays', 'business-hours' ) );
		echo sprintf( '<option %s value="%s">%s</option>', selected( $selected, 'satsun', false ), 'satsun', __( 'Saturdays and Sundays', 'business-hours' ) );

		for ( $i = 1; $i < 32; $i++ ) {
			echo sprintf( '<option %s value="%2$d">%2$d</option>', selected( intval( $selected ), intval( $i ), false ), $i );
		}
	}

	private function _show_exception_months( $selected ) {
		echo sprintf( '<option %s value="%s">%s</option>', selected( $selected, 'every', false ), 'every', __( 'Every month', 'business-hours' ) );

		global $wp_locale;

		foreach ( $wp_locale->month as $id => $month ) {
			echo sprintf( '<option %s value="%d">%s</option>', selected( intval( $selected ), intval( $id ), false ), $id, $month );
		}

	}

	private function _show_exception_years( $selected ) {

		echo sprintf( '<option %s value="%s">%s</option>', selected( $selected, 'every', false ), 'every', __( 'Every year', 'business-hours' ) );

		$this_year = date( 'Y', time() );
		$limit     = apply_filters( 'business-hours-exceptions-how-many-years', 10 );

		for ( $i = 0; $i < $limit; $i++ ) {
			echo sprintf( '<option %s value="%d">%s</option>', selected( intval( $selected ), intval( $this_year ), false ), $this_year, $this_year );
			$this_year++;
		}
	}


	/**
	 * @static
	 * @return BusinessHoursExceptions
	 */
	public static function instance() {
		if ( !isset( self::$_instance ) ) {
			$className       = __CLASS__;
			self::$_instance = new $className;
		}
		return self::$_instance;
	}


}
