<?php

/***************************************************************************

An example-file for embedding the Buurtlinux-map using the <IFRAME> element.
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

	session_start();

	require_once('settings.php');
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
		// Add the necessary external CSS-file(s) with the right base-folder 
		// 'MAP_URL'.
		print '<link rel="stylesheet" type="text/css" href="'.MAP_URL.'styles/general.css">';
	?>

	</head>

	<?php 
		// This is supposed to speed up the page-loading.
		flush(); 
	?>

	<body>

<?php
	// For this example we choose to embed the Buurtlinux-map between this 
	// banner and a copyright-statement. In HTML-terms we have a single-column
	// table with three rows (as <DIV> elements) of which the middle cell holds
	// the embedded Buurtlinux-map.
	print '<!-- Top cell -->
		<div style="height: 88px;"><img width="363px" height="88px" src="'.MAP_URL.'img/buurtlinux_banner.png"</div>'; 
?>

		<!-- Middle cell -->
		<div style="position: absolute; top: 88px; left: 20px; right: 0px; bottom: 20px;">

			<?php 
				// Pass on any address-line arguments to the embedded page.
				$l_query = MAP_URL.strstr($_SERVER["REQUEST_URI"], "?");
				print "<iframe frameborder='0' marginwidth='0' marginheight='0' src='".$l_query."' style='width: 100%; height: 100%;'>
					Je browser ondersteunt geen Iframe. Bezoek de <a target='_blanc' alt='Bezoek de oorspronkelijke website' title='Bezoek de oorspronkelijke website' href='".MAP_URL."index.php'>oorspronkelijke website</a>
				</iframe>";
			?>

		</div>

		<!-- Bottom cell -->
		<div style="height: 20px; position: absolute; bottom: 0px; padding-left: 235px; font-size: 10px;">&copy Buurtlinux</div>
	</body>
</html>
