<?php
/*-------------------------------------------------------------------------------------*
 * Helps loading templates from the /views/ directory
 *
 * @author Modern Tribe Inc. (http://tri.be/)
 *-------------------------------------------------------------------------------------*/

class WP_Test_BusinessHours extends WP_UnitTestCase {

	public $plugin = 'business-hours-plugin/working-hours.php';
	/**
	 * @var BusinessHours
	 */
	public $plugin_instance;

	public function setUp() {
		parent::setUp();

		if ( ! function_exists( "activate_plugin" ) )
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

		activate_plugin( $this->plugin );

		$this->plugin_instance = BusinessHours::instance();
	}

	function test_get_timestamp_using_timezone_works_for_gmt_offset() {
		update_option( 'timezone_string', '' );

		update_option( 'gmt_offset', 0 );
		$this->assertEquals( time(), $this->plugin_instance->get_timestamp_using_timezone(), "GMT" );

		update_option( 'gmt_offset', - 3 );
		$time = time() - ( 3 * HOUR_IN_SECONDS );
		$this->assertEquals( $time, $this->plugin_instance->get_timestamp_using_timezone(), "GMT-3" );

		update_option( 'gmt_offset', 5 );
		$time = time() + ( 5 * HOUR_IN_SECONDS );
		$this->assertEquals( $time, $this->plugin_instance->get_timestamp_using_timezone(), "GMT+5" );
	}

	function test_get_timestamp_using_timezone_works_for_timezone_string() {
		update_option( 'timezone_string', 'America/Argentina/Buenos_Aires' );
		$date = new DateTime( null, new DateTimeZone( 'America/Argentina/Buenos_Aires' ) );
		$this->assertEquals( $date->getTimestamp() + $date->getOffset(), $this->plugin_instance->get_timestamp_using_timezone(), "America/Argentina/Buenos_Aires" );

		update_option( 'timezone_string', 'Asia/Macau' );
		$date = new DateTime( null, new DateTimeZone( 'Asia/Macau' ) );
		$this->assertEquals( $date->getTimestamp() + $date->getOffset(), $this->plugin_instance->get_timestamp_using_timezone(), "Asia/Macau" );

		update_option( 'timezone_string', 'UTC' );
		$date = new DateTime( null, new DateTimeZone( 'UTC' ) );
		$this->assertEquals( $date->getTimestamp() + $date->getOffset(), $this->plugin_instance->get_timestamp_using_timezone(), "UTC" );

		update_option( 'timezone_string', 'America/Phoenix' );
		$date = new DateTime( null, new DateTimeZone( 'America/Phoenix' ) );
		$this->assertEquals( $date->getTimestamp() + $date->getOffset(), $this->plugin_instance->get_timestamp_using_timezone(), "America/Phoenix" );
	}

	function test_get_day_using_timezone() {
		update_option( 'timezone_string', '' );
		update_option( 'gmt_offset', 0 );

		$day = $this->plugin_instance->get_day_using_timezone();
		$this->assertEquals( key( $day ), date( "w", time() ), "Day number" );
		$this->assertEquals( $day[key( $day )], ucwords( date_i18n( 'l', time() ) ), "Day name" );

		update_option( 'gmt_offset', - 30 );
		$time = time() - ( 30 * HOUR_IN_SECONDS );
		$day  = $this->plugin_instance->get_day_using_timezone();
		$this->assertEquals( key( $day ), date( "w", $time ), "Day number" );
		$this->assertEquals( $day[key( $day )], ucwords( date_i18n( 'l', $time ) ), "Day name" );

		update_option( 'gmt_offset', 0 );
		update_option( 'timezone_string', 'Asia/Macau' );
		$date = new DateTime( null, new DateTimeZone( 'Asia/Macau' ) );
		$day  = $this->plugin_instance->get_day_using_timezone();
		$this->assertEquals( key( $day ), date( "w", $date->getTimestamp() + $date->getOffset() ), "Day number" );
		$this->assertEquals( $day[key( $day )], ucwords( date_i18n( 'l', $date->getTimestamp() + $date->getOffset() ) ), "Day name" );

	}

	function test_get_week_days_given_start_week_day() {

		update_option( 'start_of_week', 1 );

		$days = array(
			1 => 'Monday',
			2 => 'Tuesday',
			3 => 'Wednesday',
			4 => 'Thursday',
			5 => 'Friday',
			6 => 'Saturday',
			0 => 'Sunday',
		);

		$this->assertEquals( $this->plugin_instance->get_week_days(), $days, "Starts on Monday" );

		update_option( 'start_of_week', 4 );

		$days = array(
			4 => 'Thursday',
			5 => 'Friday',
			6 => 'Saturday',
			0 => 'Sunday',
			1 => 'Monday',
			2 => 'Tuesday',
			3 => 'Wednesday',
		);

		$this->assertEquals( $this->plugin_instance->get_week_days(), $days, "Starts on Thursday" );

	}


}