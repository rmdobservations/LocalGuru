<?php

/***************************************************************************

Selects linux helpers based on search criteria and provides HTML-balloons.
Copyright (C) 2013	Ruud Beukema

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

***************************************************************************/

require_once('../settings.php');
require_once('db_lookup_users.php');

// This class forms the interface to the tables that contain
// the search-criteria
class LinuxHelpers
{
	protected $m_actions = array();
	protected $m_distros = array();
	protected $m_desktops = array();
	protected $m_groups = array();
	protected $m_targets = array();
	protected $m_rewards = array();

	// Creates a LinuxHelpers instance
	public function __construct()
	{
		// do nothing here
	}
	

	// Adds search criteria upon which helpers are selected later.
	public function process_search_criteria( $a_distros, $a_desktops, $a_actions, $a_groups, $a_targets, $a_rewards)
	{
		// See if user gave us (valid, non-zero) search criteria
		if( $a_distros != "" ) 	$this->m_distros	= explode(",", $a_distros);
		if( $a_desktops != "" ) $this->m_desktops	= explode(",", $a_desktops);
		if( $a_actions != "" ) 	$this->m_actions 	= explode(",", $a_actions);
		if( $a_groups != "" ) 	$this->m_groups 	= explode(",", $a_groups);
		if( $a_targets != "" ) 	$this->m_targets 	= explode(",", $a_targets);
		if( $a_rewards != "" )	$this->m_rewards 	= explode(",", $a_rewards);
	}
	

	// Returns a list of users that match the given search criteria.
	public function select_helpers()
	{
		$l_lookup = new UsersLookup();
		$l_users = array();
		$l_users = $l_lookup->get_users_bytags( $this->m_actions, $this->m_distros, $this->m_desktops, $this->m_groups, $this->m_targets, $this->m_rewards );
		return $l_users;
	}
	

	// Constructs and returns a helper's full name as a string
	public function get_full_name( $a_helper )
	{
		return $a_helper['firstname']." ".$a_helper['lastname'];
	}
	

	// Constructs and returns a helper's full address as a string
	public function get_address( $a_helper )
	{
		// For now pass on everything regardless of whether it's a company or not
		$l_result = $a_helper['street']." ".$a_helper['number']." ".$a_helper['zip']." ".$a_helper['city'];
		// remove heading spaces
		return trim( $l_result );
	}
	

	// Returns the country-code of the helper's country
	public function get_country_code( $a_helper )
	{
		return $a_helper['country'];
	}
	

	// Constructs and returns a HTML-formatted popup-balloon containing info on
	// a helper for use on the map.
	public function get_balloon_text( $a_helper )
	{
		// Set balloon parameters
		$l_name 	= $this->get_full_name( $a_helper );
		$l_company 	= $a_helper['companyname'];
		$l_street 	= $a_helper['street'];
		$l_number 	= $a_helper['number'];
		$l_zip 		= $a_helper['zip'];
		$l_city 	= $a_helper['city'];
		$l_phone1 	= $a_helper['phone_1st'];
		$l_phone2 	= $a_helper['phone_2nd'];
		$l_website 	= $a_helper['website'];
		$l_email 	= $a_helper['email'];		
		$l_info = addcslashes($a_helper['info'], "\0..\37'\\");
		if( isset($l_company) && $l_company != "")
		{
			$l_title = $l_company;
			$l_address = "<b>Contact: </b><br>".$l_name."<br>";
			if( isset($l_street) && $l_street != "") 	$l_address .= $l_street." ".$l_number."<br>";
			if( isset($l_zip) && $l_zip != "")			$l_address .= $l_zip." ".$l_city."<br>";
			if( isset($l_phone1) && $l_phone1 != "")	$l_address .= "Tel 1: ".$l_phone1."<br>";
			if( isset($l_phone2) && $l_phone2 != "")	$l_address .= "Tel 2: ".$l_phone2."<br>";
		}
		else
		{
			$l_title = "Particulier";
			$l_address = "<b>Naam: </b>".$l_name;
		}
		
		$l_html = "<div id='map_balloon'>";
		$l_html .= "<div id='map_close_button'><img onclick='hide_popup(); return false;' src='".MAP_URL."img/close_button.png'></img></div>";
		$l_html .= "<div id='map_balloon_title'>".$l_title."</div><div id='map_balloon_text'>&nbsp;</div>";
		if( $l_address != "" ) $l_html .= "<div id='map_balloon_text'>".$l_address."</div>";
		if( $l_info != "") $l_html .= "<div id='map_balloon_text_scroll_y'><br><b>Aanvullende informatie:</b><br>".$l_info."</div><div id='map_balloon_text'>&nbsp;</div>";
		$l_html .='<span id="map_url"><a href="javascript:open_new_window(\'Email '.$l_name.'\',\''.MAP_URL.'email.php?user='.$a_helper["id"].'\', 650, 550);" title="Neem contact op via email" alt="Neem contact op via email">Neem contact op</a></span>';
		$l_html .=' | <span id="map_url"><a href="javascript:open_new_window(\'Email '.$l_name.'\',\''.MAP_URL.'profiel/bekijken/'.$a_helper['id'].'\', 650, 650);" title="Bekijk dit Buurtlinux-profiel" alt="Bekijk dit Buurtlinux-profiel">Bekijk profiel</a></span>';
		if( $l_website != "" ) $l_html .=" | <span id='map_url'><a href='".$l_website."' title='Bekijk de website van ".$l_name."' alt='Bezoek de website van ".$l_name."'>Bezoek website</a></span>";
		$l_html .= "</div>";
		
		// Make returned html javascript-proof
		$l_html = str_replace("\'","'",$l_html);	// remove any existing escapes
		$l_html = str_replace("'","\'",$l_html);	// add escapes
		return $l_html;
	}
}

?>
