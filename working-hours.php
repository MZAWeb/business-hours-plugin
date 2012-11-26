<?php
/*
Plugin Name: Business Hours
Plugin URI: http://mzaweb.com/en
Description: Business Hours lets you show to your visitors the time you open and close your business each day of the week.
Author: MZAWeb
Author URI: http://mzaweb.com
Version: 2.0-alpha

For documentation see: https://github.com/MZAWeb/business-hours-plugin/wiki
For bug reports, ideas or comments: https://github.com/MZAWeb/business-hours-plugin/issues?state=open

*/

require 'lib/BusinessHours.class.php';

function business_hours_init() {
	load_plugin_textdomain( 'business-hours', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	business_hours();
}

add_action( 'init', 'business_hours_init' );
