<?php
class BusinessHoursExceptions {

	const SETTINGS_EXCEPTIONS = 'exceptions';

	private static $_instance;
	private static $_today_cache = null;
	/**
	 * @var BusinessHoursSet
	 */
	private $_exceptions = null;

	public function __construct() {
		require_once 'BusinessHoursTemporalExpressionsEngine.class.php';

		add_action( 'business-hours-settings-page', array( $this, 'show_exceptions_settings' ), 2 );
		add_filter( 'business-hours-save-settings', array( $this, 'maybe_save_settings_exceptions' ), 2 );

		add_action( 'business-hours-after-row', array( $this, 'maybe_show_exception' ), 1, 5 );
	}

	/**
	 * @param $date
	 *
	 * @return mixed
	 */
	public function get_exceptions_for_date( $date ) {

		if ( !$this->_exceptions )
			$this->_build_exceptions_rules();

		return $this->_exceptions->includes( $date );
	}

	public function maybe_show_exception( $id, $day_name, $open, $close, $is_open_today ) {
		if ( !self::$_today_cache )
			self::$_today_cache = key( business_hours()->get_day_using_timezone() );


		if ( self::$_today_cache === $id ) {

			$date      = date( 'Y-m-d', business_hours()->get_timestamp_using_timezone() );
			$exception = $this->get_exceptions_for_date( $date );

			if ( empty( $exception ) )
				return;

			$day_name      = 'Exception for ' . $date;
			$open          = $exception['open'];
			$close         = $exception['close'];
			$is_open_today = !empty( $open ) && !empty( $close );

			$closed_text = business_hours()->settings()->get_default_closed_text();

			$class = 'business_hours_table_day_exception';

			include business_hours()->locate_view( 'table-row.php' );


		}


	}

	/************ HELPERS ***********/

	/**
	 * @param null $exceptions
	 *
	 * @return BusinessHoursSet
	 */
	private function _build_exceptions_rules( $exceptions = null ) {

		if ( !$exceptions )
			$exceptions = $this->_get_exceptions();

		$union = new BusinessHoursSetUnion( 'iBusinessHoursTemporalExpression' );

		foreach ( $exceptions as $exception ) {

			$day   = new BusinessHoursTEDay( $exception['day'] );
			$month = new BusinessHoursTEMonth( $exception['month'] );
			$year  = new BusinessHoursTEYear( $exception['year'] );

			$intersection = new BusinessHoursSetIntersection( 'iBusinessHoursTemporalExpression' );

			$intersection->addElement( $day );
			$intersection->addElement( $month );
			$intersection->addElement( $year );

			$union->addElement( $intersection, array( 'open' => $exception['open'], 'close' => $exception['close'] ) );

		}

		$this->_exceptions = $union;

	}

	/**
	 * @return mixed
	 */
	private function _get_exceptions() {
		$settings = business_hours()->settings()->get_full_settings();
		return $settings[self::SETTINGS_EXCEPTIONS];
	}

	/************ SETTINGS ***********/

	/**
	 *
	 */
	public function show_exceptions_settings() {
		include business_hours()->locate_view( 'settings-exceptions.php' );
	}

	/**
	 * @param $cache
	 *
	 * @return array
	 */
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


		$this->_build_exceptions_rules( $cache[self::SETTINGS_EXCEPTIONS] );

		return $cache;
	}

	/************ SETTINGS HELPERS ***********/

	/**
	 *
	 */
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

	/**
	 *
	 */
	private function _show_exceptions_instructions() {
		$exception_number = 0;
		include business_hours()->locate_view( 'settings-exception-instructions.php' );
	}

	/**
	 * @param $selected
	 */
	private function _show_exception_days( $selected ) {
		echo sprintf( '<option %s value="%s">%s</option>', selected( $selected, 'every', false ), 'every', __( 'Every day', 'business-hours' ) );
		echo sprintf( '<option %s value="%s">%s</option>', selected( $selected, 'monfri', false ), 'monfri', __( 'Mondays to Fridays', 'business-hours' ) );
		echo sprintf( '<option %s value="%s">%s</option>', selected( $selected, 'satsun', false ), 'satsun', __( 'Saturdays and Sundays', 'business-hours' ) );

		for ( $i = 1; $i < 32; $i++ ) {
			echo sprintf( '<option %s value="%2$d">%2$d</option>', selected( intval( $selected ), intval( $i ), false ), $i );
		}
	}

	/**
	 * @param $selected
	 */
	private function _show_exception_months( $selected ) {
		echo sprintf( '<option %s value="%s">%s</option>', selected( $selected, 'every', false ), 'every', __( 'Every month', 'business-hours' ) );

		global $wp_locale;

		foreach ( $wp_locale->month as $id => $month ) {
			echo sprintf( '<option %s value="%d">%s</option>', selected( intval( $selected ), intval( $id ), false ), $id, $month );
		}

	}

	/**
	 * @param $selected
	 */
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
