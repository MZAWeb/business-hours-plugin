<?php


/****************************** Temporal Expressions ******************************/


class BusinessHoursTEDay implements iBusinessHoursTemporalExpression {

	private $_day;

	public function __construct( $day ) {
		$this->_day = $day;
	}

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

	private function is_weekend( $date ) {
		$weekDay = date( 'w', strtotime( $date ) );
		return ( $weekDay == 0 || $weekDay == 6 );
	}

}


class BusinessHoursTEMonth implements iBusinessHoursTemporalExpression {

	private $_month;

	public function __construct( $month ) {
		$this->_month = $month;
	}

	public function includes( $date ) {

		if ( $this->_month === 'every' )
			return true;

		if ( intval( $this->_month ) === intval( date( 'n', strtotime( $date ) ) ) )
			return true;

		return false;
	}

}


class BusinessHoursTEYear implements iBusinessHoursTemporalExpression {

	private $_year;

	public function __construct( $year ) {
		$this->_year = $year;
	}

	public function includes( $date ) {

		if ( $this->_year === 'every' )
			return true;

		if ( intval( $this->_year ) === intval( date( 'Y', strtotime( $date ) ) ) )
			return true;

		return false;
	}

}


/****************************** SETS ******************************/


class BusinessHoursSetIntersection extends BusinessHoursSet {

	public function __call( $method, $arguments ) {

		if ( !method_exists( $this->_setInterface, $method ) ) {
			throw new Exception( "$method is not defined in $this->_setInterface" );
		}

		if ( empty( $this->_elements ) )
			return false;

		foreach ( $this->_elements as $element )
			if ( !call_user_func_array( array( $element, $method ), $arguments ) )
				return false;

		return true;
	}

}


class BusinessHoursSetUnion extends BusinessHoursSet {

	public function __call( $method, $arguments ) {

		if ( !method_exists( $this->_setInterface, $method ) ) {
			throw new Exception( "$method is not defined in $this->_setInterface" );
		}

		if ( empty( $this->_elements ) )
			return false;

		foreach ( $this->_elements as $element )
			if ( call_user_func_array( array( $element, $method ), $arguments ) )
				return true;

		return false;
	}

}


/****************************** HELPERS ******************************/


abstract class BusinessHoursSet {

	protected $_setInterface = null;
	protected $_elements = array();

	public function __construct( $setInterface ) {
		if ( !is_string( $setInterface ) ) {
			throw new Exception( "Interface must be a string." );
		}
		if ( !interface_exists( $setInterface, true ) ) {
			throw new Exception( "$setInterface is not a valid interface." );
		}
		$this->_setInterface = $setInterface;
	}

	public function addElement( $element ) {
		if ( $element instanceof $this->_setInterface || $element instanceof BusinessHoursSet ) {
			$this->_elements[] = $element;
		} else {
			throw new Exception( "Element must implement $this->_setInterface or Set" );
		}
	}

	public function addElements( array $elements ) {
		foreach ( $elements as $element ) {
			$this->addElement( $element );
		}
	}


}


interface iBusinessHoursTemporalExpression {

	public function includes( $date );
}
