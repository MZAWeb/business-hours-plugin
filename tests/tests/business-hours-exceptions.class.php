<?php

class WP_Test_BusinessHoursExceptions extends WP_UnitTestCase {


	public $plugin = 'business-hours-plugin/working-hours.php';

	//http://files.mzaweb.com/image/2r2s2K0K2K20
	public $data = 'a:2:{s:5:"hours";a:7:{i:1;a:2:{s:4:"open";s:4:"9:00";s:5:"close";s:5:"14:00";}i:2;a:2:{s:4:"open";s:5:"10:00";s:5:"close";s:5:"15:00";}i:3;a:2:{s:4:"open";s:5:"11:00";s:5:"close";s:5:"16:00";}i:4;a:2:{s:4:"open";s:5:"12:00";s:5:"close";s:5:"17:00";}i:5;a:2:{s:4:"open";s:5:"13:00";s:5:"close";s:5:"18:00";}i:6;a:2:{s:4:"open";s:0:"";s:5:"close";s:0:"";}i:0;a:2:{s:4:"open";s:0:"";s:5:"close";s:0:"";}}s:10:"exceptions";a:2:{i:0;a:5:{s:3:"day";s:1:"1";s:5:"month";s:1:"1";s:4:"year";s:5:"every";s:4:"open";s:0:"";s:5:"close";s:0:"";}i:1;a:5:{s:3:"day";s:6:"satsun";s:5:"month";s:1:"3";s:4:"year";s:5:"every";s:4:"open";s:5:"20:00";s:5:"close";s:5:"21:00";}}}';

	/**
	 * @var BusinessHoursExceptions
	 */
	public $plugin_instance;

	public function setUp() {
		parent::setUp();

		if ( ! function_exists( "activate_plugin" ) )
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

		activate_plugin( $this->plugin );

		BusinessHours::instance();

		$this->plugin_instance = BusinessHoursExceptions::instance();

	}


	public function test_get_empty_exceptions_for_date_without_exceptions() {

		//update_option( 'business_hours_settings', maybe_unserialize( $this->data ) );

		$v = $this->plugin_instance->get_exceptions_for_date( strtotime( '2013-08-17' ) );
		
		$this->assertEmpty( $v );

		//update_option( 'business_hours_settings', array() );

	}

}