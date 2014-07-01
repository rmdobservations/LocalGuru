<?php

/***************************************************************************

Generates the HTML index of Buurtlinux-map with optional search-pane.
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


	/* Start a PHP session */
	session_start();
	$body_content = "";
	unset($_SESSION['email']);
	unset($_SESSION['wizard']);

	require_once('settings.php');

	
	// Checks given array [$a_array] for being array and if so translates it 
	// into a comma-seperated string
	function array_to_comma_seperated( $a_array )
	{
		$l_count = 0;
		if(is_array( $a_array ) )
		{			
			foreach( $a_array as $l_element )
			{				
				if ( $l_count )
				{
					$l_result .= ",".$l_element;
				}
				else
				{
					$l_result = $l_element;
				}

				$l_count++;
			}			
		}
		else
		{
			$l_result = $a_array;
		}

		return $l_result;
	}
	
	// Checks if the given search criteria variable is valid (Set, non-empty, 
	// non-NULL and no '*')
	function is_valid( $a_var )
	{		
		if( 	!isset($a_var) ||		// It should be set to be valid
				($a_var == "") ||		// It shouldn't be empty to be valid		
				($a_var == NULL) ||		// It shouldn't be empty to be valid		
				($a_var == "*") 		// It should also not be a "*"
		) return false;

		// Otherwise it is found valid
		return true;
	}

	// Set default search criteria
	$_SERVER['search']['address'] 	= "";
	$_SERVER['search']['country'] 	= "";
	$_SERVER['search']['radius'] 	= "";
	$_SERVER['search']['distros'] 	= "";
	$_SERVER['search']['desktops'] 	= "";
	$_SERVER['search']['actions'] 	= "";
	$_SERVER['search']['groups'] 	= "";
	$_SERVER['search']['targets'] 	= "";
	$_SERVER['search']['rewards'] 	= "";

	
	// See if search criteria/options are present in GET data
	if ( isset($_GET['address']) && is_valid( $_GET['address'] ) )		$_SERVER['search']['address'] 		= $_GET['address'];
	if ( isset($_GET['country']) && is_valid( $_GET['country'] ) )		$_SERVER['search']['country'] 		= $_GET['country'];
	if ( isset($_GET['radius']) && is_valid( $_GET['radius'] ) )		$_SERVER['search']['radius'] 		= $_GET['radius'];	
	if ( isset( $_GET['search_allow'] ) )								$_SERVER['search']['pane_options'] 	= explode("," , $_GET['search_allow']);
	if ( isset($_GET['distros']) && is_valid( $_GET['distros'] ) ) 		$_SERVER['search']['distros'] 		= array_to_comma_seperated( $_GET['distros'] );
	if ( isset($_GET['desktops']) && is_valid( $_GET['desktops'] ) )	$_SERVER['search']['desktops'] 		= array_to_comma_seperated( $_GET['desktops'] );
	if ( isset($_GET['actions']) && is_valid( $_GET['actions'] ) ) 		$_SERVER['search']['actions'] 		= array_to_comma_seperated( $_GET['actions'] );
	if ( isset($_GET['groups']) && is_valid( $_GET['groups'] ) )		$_SERVER['search']['groups'] 		= array_to_comma_seperated( $_GET['groups'] );
	if ( isset($_GET['targets']) && is_valid( $_GET['targets'] ) )		$_SERVER['search']['targets'] 		= array_to_comma_seperated( $_GET['targets'] );
	if ( isset($_GET['rewards']) && is_valid( $_GET['rewards'] ) )		$_SERVER['search']['rewards'] 		= array_to_comma_seperated( $_GET['rewards'] );

	// Determine if user wants to show the search-pane on the side or not
	if (isset( $_GET['nosearch'] ))
	{
																		$_SERVER['search']['showpane'] 		= false;
	}
	else
	{
																		$_SERVER['search']['showpane'] 		= true;
	}
	

	// Construct a search-pane if the user requests so
	if ( $_SERVER['search']['showpane'] ) 
	{
		require_once('search_pane.php');
		$l_search_pane = new SearchPane();

		// The user might have specified to show only a few search-criteria to
		// be shown on the search-pane.
		if( isset($_SERVER['search']['pane_options']) )
		{
			$l_pane_options = $_SERVER['search']['pane_options'];
		}
		else
		{
			$l_pane_options = NULL;
		}

		if( isset($l_pane_options) && count( $l_pane_options ) )
		{
			foreach( $l_pane_options as $l_option )
			{
				switch( $l_option )
				{
					case "all":			$l_search_pane->criteria_set_all( true );		break 2;						
					case "distros": 	$l_search_pane->criteria_set_distros( true ); 	break 1;
					case "desktops":	$l_search_pane->criteria_set_desktops( true );	break 1;
					case "actions":		$l_search_pane->criteria_set_actions( true );	break 1;
					case "groups":		$l_search_pane->criteria_set_groups( true );	break 1;
					case "targets":		$l_search_pane->criteria_set_targetgrp( true );	break 1;
					case "rewards":		$l_search_pane->criteria_set_reward( true );	break 1;						
					default:															break;
				}
			}
		}
		else
		{
			$l_search_pane->criteria_set_all( true );
		}

		// Last but not least, insert the javascript that takes care of some
		// dynamic behaviour in the search-pane such as collapsing options.
		$jscript = $l_search_pane->insert_javascript();
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>
			Buurtlinux - Linux-hulp kaart
		</title>
		<!-- Meta-tags -->
		<meta charset="UTF-8">
		<!-- CSS -->

<?php
		// Add the necessary external CSS- and Javascipt-file(s) with the right
		// base-folder 'MAP_URL'. Also pass on the search-criteria in 
		// $_SERVER['search'] to the Buurtlinux.js javascript which takes care
		// of drawing the Tux'es on the OpenStreetMap.
		print '<link rel="stylesheet" type="text/css" href="'.MAP_URL.'styles/general.css">
		<link rel="stylesheet" type="text/css" href="'.MAP_URL.'styles/map.css">
		<!-- JavaScript -->
		<script type="text/javascript" src="'.MAP_URL.'jscript/OpenLayers.js"></script>
		<script type="text/javascript" src="'.MAP_URL.'jscript/Buurtlinux.js.php?address='.$_SERVER['search']['address'].'&country='.$_SERVER['search']['country'].'&radius='.$_SERVER['search']['radius'].'&distros='.$_SERVER['search']['distros'].'&desktops='.$_SERVER['search']['desktops'].'&actions='.$_SERVER['search']['actions'].'&groups='.$_SERVER['search']['groups'].'&targets='.$_SERVER['search']['targets'].'&rewards='.$_SERVER['search']['rewards'].'"></script>
		'.$jscript;

?>
		<script type="text/javascript">
			/* Check for browser support of event handling capability and take care of loading the map after loading the page */
			if (window.addEventListener)
				window.addEventListener("load", load_map, false);
			else if (window.attachEvent)
				window.attachEvent("onload", load_map);
			else window.onload = load_map;		    
		  </script>
	</head>

	<?php 
		// This is supposed to speed up the page-loading.
		flush();
	?>

	<body>			

<?php 
		// Show the constructed search-pane together with the map or, if user 
		// requests so, show only the map.
		if ( $_SERVER['search']['showpane'] )
		{
			print '<div id="map_canvas" style="margin-left:215px"></div>';
			print '<div id="map_canvas_text_overlay" style="margin-left:215px">Data by <span id="map_url"><a target="_blanc" href="http://www.openstreetmap.org/">OpenStreetMap.org</a></span> contributors under CC <span id="map_url"><a target="_blanc" href="http://creativecommons.org/licenses/by-sa/2.0/">BY-SA 2.0</a></span> license.</div>';
			print '<div id="map_search_pane">'.$l_search_pane->insert_search_pane().'</div>'; 
		}
		else
		{
			print '<div id="map_canvas"></div>';
			print '<div id="map_canvas_text_overlay"><span id="map_url">Data by <span id="map_url"><a target="_blanc" href="http://www.openstreetmap.org/">OpenStreetMap.org</a></span> contributors under CC <span id="map_url"><a target="_blanc" href="http://creativecommons.org/licenses/by-sa/2.0/">BY-SA 2.0</a></span> license.</div>';
		}

?>
		
	</body>
</html>
