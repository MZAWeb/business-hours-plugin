<?php
class BusinessHoursExceptions {

	const SETTINGS_EXCEPTIONS = 'exceptions';

	private static $_instance;
	private static $_today_id        = null;
	private static $_today_exception = null;
	private static $_today_date      = null;
	private static $_actual_dates    = array();

	/**
	 * @var BusinessHoursSet
	 */
	private $_exceptions = null;

	public function __construct() {
		require_once 'BusinessHoursTemporalExpressionsEngine.class.php';

		add_action( 'business-hours-settings-page', array( $this, 'show_exceptions_settings'       ), 2    );
		add_filter( 'business-hours-save-settings', array( $this, 'maybe_save_settings_exceptions' ), 2    );
		add_filter( 'business-hours-row-class',     array( $this, 'maybe_add_exception_class'      ), 2, 3 );
		add_action( 'business-hours-before-row',    array( $this, 'maybe_setup_exception'          ), 1, 5 );
		add_action( 'business-hours-after-row',     array( $this, 'maybe_show_exception'           ), 1, 5 );
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

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function get_exceptions_for_day_id( $id ) {
		$dates = $this->_pre_compute_this_week_actual_dates();
		$day   = date_i18n( get_option( 'date_format' ), $dates[$id] );
		return $this->get_exceptions_for_date( $day );
	}

	public function get_localized_date_for_day_id( $id ) {
		$dates = $this->_pre_compute_this_week_actual_dates();
		return date_i18n( get_option( 'date_format' ), $dates[$id] );
	}

	/**
	 * @param $id
	 * @param $day_name
	 * @param $open
	 * @param $close
	 * @param $is_open_today
	 */
	public function maybe_setup_exception( $id, $day_name, $open, $close, $is_open_today ) {
		$dates                  = $this->_pre_compute_this_week_actual_dates();
		self::$_today_date      = date_i18n( get_option( 'date_format' ), $dates[$id] );
		self::$_today_exception = $this->get_exceptions_for_date( self::$_today_date );
	}

	/**
	 * @param $id
	 * @param $day_name
	 * @param $open
	 * @param $close
	 * @param $is_open_today
	 */
	public function maybe_show_exception( $id, $day_name, $open, $close, $is_open_today ) {

		if ( !self::$_today_exception )
			return;

		$day_name      = self::$_today_date;
		$open          = self::$_today_exception['open'];
		$close         = self::$_today_exception['close'];
		$is_open_today = !empty( $open ) && !empty( $close );
		$closed_text = business_hours()->settings()->get_default_closed_text();

		$class = 'business_hours_table_day_exception';

		include business_hours()->locate_view( 'table-row.php' );

		self::$_today_exception = null;
		self::$_today_date      = null;
		self::$_today_id        = null;

	}

	public function maybe_add_exception_class( $class, $id, $day_name ) {

		if ( !self::$_today_exception )
			return $class;

		return $class . ' business_hours_has_exception';
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

		foreach ( (array) $exceptions as $exception ) {

			$day   = new BusinessHoursTEDay( $exception['day'] );
			$month = new BusinessHoursTEMonth( $exception['month'] );
			$year  = new BusinessHoursTEYear( $exception['year'] );

			$intersection = new BusinessHoursSetIntersection( 'iBusinessHoursTemporalExpression' );

			$intersection->addElement( $day );
			$intersection->addElement( $month );
			$intersection->addElement( $year );
			$intersection->setStorage( array( 'open' => $exception['open'], 'close' => $exception['close'] ) );

			$union->addElement( $intersection );

		}

		$this->_exceptions = $union;

	}

	/**
	 * Populates a cache array with the actual dates for this week days. It takes into
	 * account what days are displayed after and before today to generate the dates, so
	 * the dates are always consecutive.
	 *
	 * Oh boy, it'd be so much easier to assume Sunday=0, Saturday=6 and be done with it
	 * But I commited to honor custom "Week Starts On" settings, so, we need to do this.
	 *
	 * @return string
	 */
	private function _pre_compute_this_week_actual_dates() {

		if ( !empty( self::$_actual_dates ) )
			return self::$_actual_dates;

		$today    = key( business_hours()->get_day_using_timezone() );
		$days     = business_hours()->get_week_days();
		$modifier = "Last";

		foreach ( $days as $did => $name ) {
			if ( $did == $today ) {
				$modifier   = "Next";
				$today_date = business_hours()->get_timestamp_using_timezone();
			} else {
				$today_date = strtotime( $modifier . ' ' . $name );
			}
			self::$_actual_dates[$did] = $today_date;
		}

		return self::$_actual_dates;
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

		foreach ( (array) $exceptions as $exception_number => $exception ) {

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
