<?php
class BusinessHoursExceptions {

	private static $_instance;
	private $_exceptions;

	public function __construct() {
		$this->_exceptions = business_hours()->settings()->get_exceptions();
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
