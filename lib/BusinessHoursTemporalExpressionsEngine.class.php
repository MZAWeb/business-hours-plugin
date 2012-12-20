<?php


/****************************** Temporal Expressions ******************************/


/**
 *
 */
class BusinessHoursTEDay implements iBusinessHoursTemporalExpression {

	/**
	 * @var
	 */
	private $_day;

	/**
	 * @param $day
	 */
	public function __construct( $day ) {
		$this->_day = $day;
	}

	/**
	 * @param $date
	 *
	 * @return bool
	 */
	public function includes( $date ) {

		if ( $this->_day === 'every' )
			return true;

		if ( $this->_day === 'monfri' && !$this->is_weekend( $date ) )
			return true;

		if ( $this->_day === 'satsun' && $this->is_weekend( $date ) )
			return true;

		if ( intval( $this->_day ) === intval( date( 'j', strtotime( $date ) ) ) )
			return true;

		return false;

	}

	/**
	 * @param $date
	 *
	 * @return bool
	 */
	private function is_weekend( $date ) {
		$weekDay = date( 'w', strtotime( $date ) );
		return ( $weekDay == 0 || $weekDay == 6 );
	}

}


/**
 *
 */
class BusinessHoursTEMonth implements iBusinessHoursTemporalExpression {

	/**
	 * @var
	 */
	private $_month;

	/**
	 * @param $month
	 */
	public function __construct( $month ) {
		$this->_month = $month;
	}

	/**
	 * @param $date
	 *
	 * @return bool
	 */
	public function includes( $date ) {

		if ( $this->_month === 'every' )
			return true;

		if ( intval( $this->_month ) === intval( date( 'n', strtotime( $date ) ) ) )
			return true;

		return false;
	}

}


/**
 *
 */
class BusinessHoursTEYear implements iBusinessHoursTemporalExpression {

	/**
	 * @var
	 */
	private $_year;

	/**
	 * @param $year
	 */
	public function __construct( $year ) {
		$this->_year = $year;
	}

	/**
	 * @param $date
	 *
	 * @return bool
	 */
	public function includes( $date ) {

		if ( $this->_year === 'every' )
			return true;

		if ( intval( $this->_year ) === intval( date( 'Y', strtotime( $date ) ) ) )
			return true;

		return false;
	}

}


/****************************** SETS ******************************/


/**
 *
 */
class BusinessHoursSetIntersection extends BusinessHoursSet {

	/**
	 * @param $method
	 * @param $arguments
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function __call( $method, $arguments ) {

		if ( !method_exists( $this->_setInterface, $method ) ) {
			throw new Exception( "$method is not defined in $this->_setInterface" );
		}

		if ( empty( $this->_elements ) )
			return false;

		foreach ( $this->_elements as $element )
			if ( !call_user_func_array( array( $element, $method ), $arguments ) )
				return false;

		return $this->_storage;
	}

}


/**
 *
 */
class BusinessHoursSetUnion extends BusinessHoursSet {

	/**
	 * @param $method
	 * @param $arguments
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function __call( $method, $arguments ) {

		if ( !method_exists( $this->_setInterface, $method ) ) {
			throw new Exception( "$method is not defined in $this->_setInterface" );
		}

		if ( empty( $this->_elements ) )
			return false;
		
		foreach ( $this->_elements as $element )
			if ( call_user_func_array( array( $element, $method ), $arguments ) )
				return $element->_storage;

		return false;
	}

}


/****************************** HELPERS ******************************/


/**
 *
 */
abstract class BusinessHoursSet {

	protected $_setInterface = null;
	protected $_elements = array();
	protected $_storage = true;

	public function __construct( $setInterface ) {
		if ( !is_string( $setInterface ) ) {
			throw new Exception( "Interface must be a string." );
		}
		if ( !interface_exists( $setInterface, true ) ) {
			throw new Exception( "$setInterface is not a valid interface." );
		}
		$this->_setInterface = $setInterface;
	}

	/**
	 * @param $element
	 *
	 * @param $storage
	 *
	 * @throws Exception
	 */
	public function addElement( $element, $storage = array() ) {
		if ( $element instanceof $this->_setInterface || $element instanceof BusinessHoursSet ) {
			$this->_elements[] = $element;

			if ( !empty( $storage ) )
				$this->_storage = $storage;

		} else {
			throw new Exception( "Element must implement $this->_setInterface or Set" );
		}
	}

	public function setStorage( $storage = array() ) {
		if ( !empty( $storage ) )
			$this->_storage = $storage;
	}

}


/**
 *
 */
interface iBusinessHoursTemporalExpression {

	/**
	 * @abstract
	 *
	 * @param $date
	 *
	 * @return mixed
	 */
	public function includes( $date );
}
