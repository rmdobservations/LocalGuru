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

	require_once __DIR__.'/lib/init.php';

	// Determine if user wants to show the search-pane on the side or not
	if (isset( $_GET['nosearch'] ))
	{
		$search['showpane'] = false;
	}
	else
	{
		$search['showpane'] = true;
	}
	

	// Construct a search-pane if the user requests so
	if ( $search['showpane'] ) 
	{
		require_once __DIR__.'/search_pane.php';
		$l_search_pane = new SearchPane();

		// The user might have specified to show only a few search-criteria to
		// be shown on the search-pane.
		if( isset($search['pane_options']) )
		{
			$l_pane_options = $search['pane_options'];
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
					case "all":		$l_search_pane->criteria_set_all( true );	break 2;
					case "distros": 	$l_search_pane->criteria_set_distros( true ); 	break 1;
					case "desktops":	$l_search_pane->criteria_set_desktops( true );	break 1;
					case "actions":		$l_search_pane->criteria_set_actions( true );	break 1;
					case "groups":		$l_search_pane->criteria_set_groups( true );	break 1;
					case "targets":		$l_search_pane->criteria_set_targetgrp( true );	break 1;
					case "rewards":		$l_search_pane->criteria_set_reward( true );	break 1;						
					default: break;
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
		// $search to the Buurtlinux.js javascript which takes care
		// of drawing the Tux'es on the OpenStreetMap.
		print '<link rel="stylesheet" type="text/css" href="'.MAP_URL.'styles/general.css">
		<link rel="stylesheet" type="text/css" href="'.MAP_URL.'styles/map.css">
		<!-- JavaScript -->
		<script type="text/javascript" src="'.MAP_URL.'jscript/OpenLayers.js"></script>
		<script type="text/javascript" src="'.MAP_URL.'jscript/Buurtlinux.js.php?address='.$search['address'].'&country='.$search['country'].'&radius='.$search['radius'].'&distros='.$search['distros'].'&desktops='.$search['desktops'].'&actions='.$search['actions'].'&groups='.$search['groups'].'&targets='.$search['targets'].'&rewards='.$search['rewards'].'"></script>
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
		if ( $search['showpane'] )
		{
			print '<div id="map_canvas" style="margin-left:215px"></div>';
			print '<div id="map_canvas_text_overlay" style="margin-left:215px">Data by <span id="map_url"><a target="_blank" href="http://www.openstreetmap.org/">OpenStreetMap.org</a></span> contributors under CC <span id="map_url"><a target="_blank" href="http://creativecommons.org/licenses/by-sa/2.0/">BY-SA 2.0</a></span> license.</div>';
			print '<div id="map_search_pane">'.$l_search_pane->insert_search_pane().'</div>'; 
		}
		else
		{
			print '<div id="map_canvas"></div>';
			print '<div id="map_canvas_text_overlay">Data by <span id="map_url"><a target="_blank" href="http://www.openstreetmap.org/">OpenStreetMap.org</a></span> contributors under CC <span id="map_url"><a target="_blank" href="http://creativecommons.org/licenses/by-sa/2.0/">BY-SA 2.0</a></span> license.</div>';
		}

?>
		
	</body>
</html>
