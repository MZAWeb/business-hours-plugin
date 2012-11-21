<?php
class WorkingHours {

	public $settings;

	public function  __construct() {
		$this->register_settings();
		add_shortcode( 'businesshours', array( $this, 'shortcode' ) );
		add_shortcode( 'businesshoursweek', array( $this, 'shortcode_table' ) );
	}

	public function shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array( 'closed'	  => 'Closed' ), $atts ) );
		if ( $content ) {

			$day = $this->get_day_using_timezone();

			$id   = key( $day );

			$open    = $this->settings->get_setting( $id, "open" );
			$close   = $this->settings->get_setting( $id, "close" );
			$working = $this->settings->get_setting( $id, "working" );

			if ( $working == "true" ) {
				$content = str_replace( "{{TodayOpen}}", $open, $content );
				$content = str_replace( "{{TodayClose}}", $close, $content );
			} else {
				$content = $closed;
			}
		}
		return $content;
	}

	public function shortcode_table( $atts ) {

		extract( shortcode_atts( array( 'collapsible' => 'false', ), $atts ) );

		if ( strtolower( $collapsible ) == "true" ) {
			$collapsible = true;
		}
		if ( strtolower( $collapsible ) == "false" ) {
			$collapsible = false;
		}

		if ( $collapsible ) {
			wp_register_style( 'BusinessHoursStyle', plugins_url( 'style.css', __FILE__ ) );
			wp_enqueue_style( 'BusinessHoursStyle' );

			wp_register_script( 'BusinessHoursScript', plugins_url( 'script.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_script( 'BusinessHoursScript' );
		}
		return $this->get_table( $collapsible );
	}


	public function get_day_using_timezone() {
		$offset    = get_option( 'gmt_offset' );
		$offset    = $offset * 60 * 60;
		$timestamp = time() + $offset;
		$arr = array( strtolower( gmdate( 'l', $timestamp ) )  => ucwords( date_i18n( 'l', $timestamp ) ) );
		return $arr;
	}

	private function _get_week_days() {

		$timestamp = strtotime( 'next Sunday' );
		$days      = array();
		for ( $i = 0; $i < 7; $i++ ) {

			$days[] = array( strtolower( gmdate( 'l', $timestamp ) )  => ucwords( date_i18n( 'l', $timestamp ) ) );
			$timestamp = strtotime( '+1 day', $timestamp );
		}

		return $days;
	}

	public function show_table( $collapsible_link = true ) {
		echo $this->get_table( $collapsible_link );
	}

	public function get_table( $collapsible_link = true ) {

		global $workinghours;

		$ret = "";

		if ( $collapsible_link ) {
			$ret .= '<a class="business_hours_collapsible_handler" href="#">' . __( "[Show all days]", "business-hours" ) . '</a>';
			$ret .= '<div class="business_hours_collapsible">';
		}


		$days = $this->_get_week_days();

		$ret .= "<table width='100%'>";
		$ret .= "<tr><th>" . __( "Day", "business-hours" ) . "</th><th  class='business_hours_table_heading'>" . __( "Open", "business-hours" ) . "</th><th  class='business_hours_table_heading'>" . __( "Close", "business-hours" ) . "</th></tr>";
		foreach ( $days as $day ) {

			$id = key($day);
			$name = $day[$id];

			$open    = $workinghours->settings->get_setting( $id, "open" );
			$close   = $workinghours->settings->get_setting( $id, "close" );
			$working = $workinghours->settings->get_setting( $id, "working" );

			$ret .= "<tr>";
			$ret .= "<td class='business_hours_table_day'>" . ucwords( $name ) . "</td>";
			if ( $working == "true" ) {
				$ret .= "<td class='business_hours_table_open'>" . ucwords( $open ) . "</td>";
				$ret .= "<td class='business_hours_table_close'>" . ucwords( $close ) . "</td>";
			} else {
				$ret .= "<td class='business_hours_table_closed' colspan='2' align='center'>" . __( "Closed", "business-hours" ) . "</td>";
			}


			$ret .= "</tr>";
		}
		$ret .= "</table>";
		if ( $collapsible_link ) {
			$ret .= '</div>';
		}

		return $ret;
	}


	private function register_settings() {

		$days     = $this->_get_week_days();
		$sections = array();

		foreach ( $days as $day ) {
			$id = key($day);
			$name = $day[$id];
			$sections[$id] = array( "title"  => $name, "business-hours",
												   "fields" => array( "working" => array( "title"   => sprintf( __( "Is it open on %s?", "business-hours" ), $name ),
																						  "type"	=> "checkbox",
																						  "options" => array( "true" => "" ) ),
																	  "open"	=> array( "title"	   => __( "Open", "business-hours" ) . ":",
																						  "type"		=> "time",
																						  "description" => "HH:MM"

																	  ),
																	  "close"   => array( "title"	   => __( "Close", "business-hours" ) . ":",
																						  "type"		=> "time",
																						  "description" => "HH:MM"

																	  ) ) );

		}

		$sections["support"] = array( "title"  => __( "Support", "business-hours" ),
									  "fields" => array( "mzaweb" => array( "title" => __( "Bugs? Questions? Suggestions?", "business-hours" ),
																			"type"  => "support",
																			"email" => "support@mzaweb.com" ) ) );

		$this->settings                    = new MZASettings( "working-hours", 'options-general.php', $sections );
		$this->settings->settingsPageTitle = __( "Business Hours Settings", "business-hours" );
		$this->settings->settingsLinkTitle = __( "Business Hours", "business-hours" );

		$this->settings->customJS .= "jQuery('#working-hours_settings_form input:checkbox').each(function() {
            index = jQuery(this).index('#working-hours_settings_form input:checkbox') * 2;

            if (this.checked){

                jQuery('.field-row-time').eq(index).show();
                jQuery('.field-row-time').eq(index+1).show();
            }else{
                jQuery('.field-row-time').eq(index).hide();
                jQuery('.field-row-time').eq(index+1).hide();
            }
        });";

		$this->settings->customJS .= "jQuery('#working-hours_settings_form input:checkbox').change(function() {
            index = jQuery(this).index('#working-hours_settings_form input:checkbox') * 2;

            if (this.checked){

                jQuery('.field-row-time').eq(index).show();
                jQuery('.field-row-time').eq(index+1).show();
            }else{
                jQuery('.field-row-time').eq(index).hide();
                jQuery('.field-row-time').eq(index+1).hide();
            }
        });";


	}

}

?>