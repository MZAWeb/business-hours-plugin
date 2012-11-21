<?php
/*
Plugin Name: Business Hours
Plugin URI: http://mzaweb.com/en
Description: Business Hours lets you show to your visitors the time you open and close your business each day of the week.
Author: MZAWeb
Author URI: http://mzaweb.com
Version: 1.3.2
*/


/* CONSTANTS */

define('OPENHOURS_PATH', dirname(__FILE__));

/* IMPORTS */
require OPENHOURS_PATH . '/lib/MZASettings.php';
require OPENHOURS_PATH . '/working-hours.class.php';
require OPENHOURS_PATH . '/widget.php';


/* START */

function bussiness_hours_init() {
	load_plugin_textdomain( 'business-hours', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	global $workinghours;
	$workinghours = new WorkingHours();
}
add_action('init', 'bussiness_hours_init');



?>