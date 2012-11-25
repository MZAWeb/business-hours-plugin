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


	/************ SETTINGS ***********/

	public function show_exceptions_settings() {
		include business_hours()->locate_view( 'settings-exceptions.php' );
	}

	private function _show_exception_days() {
		echo sprintf( '<option value="%s">%s</option>', 'every', __( 'Every day', 'business-hours' ) );
		echo sprintf( '<option value="%s">%s</option>', 'monfri', __( 'Mondays to Fridays', 'business-hours' ) );
		echo sprintf( '<option value="%s">%s</option>', 'satsun', __( 'Saturdays and Sundays', 'business-hours' ) );

		for ( $i = 1; $i < 32; $i++ ) {
			echo sprintf( '<option value="%1$d">%1$d</option>', $i );
		}
	}


	public function maybe_save_settings_exceptions( $cache ) {

		$cache[self::SETTINGS_EXCEPTIONS] = array();

		if ( empty( $_POST['exception_day'] ) )
			return $cache;

		$days   = $_POST['exception_day'];
		$months = $_POST['exception_month'];
		$years   = $_POST['exception_year'];
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

	private function _show_exception_months() {
		echo sprintf( '<option value="%s">%s</option>', 'every', __( 'Every month', 'business-hours' ) );

		global $wp_locale;

		foreach ( $wp_locale->month as $id => $month ) {
			echo sprintf( '<option value="%d">%s</option>', $id, $month );
		}

	}

	private function _show_exception_years() {

		echo sprintf( '<option value="%s">%s</option>', 'every', __( 'Every year', 'business-hours' ) );

		$this_year = date( 'Y', time() );
		$limit     = apply_filters( 'business-hours-exceptions-how-many-years', 10 );

		for ( $i = 0; $i < $limit; $i++ ) {
			echo sprintf( '<option value="%d">%s</option>', $this_year, $this_year );
			$this_year++;
		}
	}

	private function _show_exceptions() {
		$exception_number = 0;
		include business_hours()->locate_view( 'settings-exception-single.php', false );
	}

	private function _show_exceptions_instructions() {
		$exception_number = 0;
		include business_hours()->locate_view( 'settings-exception-instructions.php' );
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
