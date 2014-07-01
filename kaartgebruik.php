<?php

/***************************************************************************

This file provides documentation on usage of the Buurtlinux-map.
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

	require_once('settings.php');
	require_once('lib/db_lookup_criteria.php');
?>

<!DOCTYPE html>
<html>
	<head>
		<title>
			Buurtlinux  - Documentatie Linux-hulp kaart
		</title>
		<!-- Meta-tags -->
		<meta charset="UTF-8">
		<!-- CSS -->

<?php
		// Add the necessary external CSS-files with the right base-folder 
		// 'MAP_URL'.
		print '	<link rel="stylesheet" type="text/css" href="'.MAP_URL.'styles/general.css">
					<link rel="stylesheet" type="text/css" href="'.MAP_URL.'styles/map.css">
					<link rel="stylesheet" type="text/css" href="'.MAP_URL.'styles/content.css">';
?>
	</head>

	<?php
		// This is supposed to speed up the page-loading
		flush();
	?>

	<body>
		<div style="margin: 30px;">
			<h1>Documentatie Buurtlinux kaart(v1.0)</h1>
			<i>NB: Dit document is niet geheel up-to-date</i>
			<h2>1 Introductie</h2>
			<p>
				De Buurtlinux kaart geeft, zoals gebruikt op de <span id="map_url"><a <?php print'href="'.ZOEK_HULP.'"';?> target="_blanc">"Zoek hulp"-pagina van de Buurtlinux website</a></span>, in eerste instantie alle Linux-helpers in Nederland (en een deel van België) weer. 
				Via het zoek-paneel dat aan de linkerkant wordt weergegeven kun je vervolgens zoekcriteria opgeven om naar meer specifieke Linux-hulp te zoeken. De Buurtlinux kaart heeft echter meer functionaliteit.
				Deze documentatie-pagina legt uit wat je er nog meer mee kunt doen.
			</p>
			<h2>2 Link naar huidige zoekresultaten</h2>
			<p>
				Nadat je op de <span id="map_url"><a <?php print'href="'.MAP_URL.'"';?>>Buurtlinux kaart</a></span> via het zoek-paneel een of meer filters hebt toegepast, dan wil je dit filter misschien wel
				opslaan om later weer te gebruiken, of om naar toe te linken vanaf een andere pagina. Dit kan door na op de "Zoek"-knop te hebben gedrukt in het zoek-paneel de link die weergegeven wordt in de adresbalk van je browser
				te kopieren en op te slaan (of in als link te gebruiken in je favorieten/bookmarks of op je website).<br><br>
				<b>Voorbeeld</b><br>
				Als ik in het zoekpaneel alleen de Ubuntu-distributie aanvink en vervolgens zoek, dan krijg ik de volgende slagzin in mijn adresbalk:<br>
				<i><?php print'href="'.MAP_URL.'"';?>?address=&radius=&distros[]=Ubuntu&groups[]=&targets[]=</i><br><br>
				Deze URL kan ik vervolgens gebruiken om een link van deze pagina te maken naar de Buurtlinux-kaart met specifieke zoekcriteria:<br>
				<span id="map_url"><a <?php print'href="'.MAP_URL.'"';?>?address=&radius=&distros[]=Ubuntu&groups[]=&targets[]=" target="_blanc">Linkje naar de Buurlinux-kaart</a></span>
			</p>
			<h2>3 Filteren via mappen in de URL</h2>
			<p>
				Een andere mogelijkheid is om via de URL zoekcriteria op te geven. Voor de URL dient dan een mappenstructuur aangehouden te worden, relatief aan de hoofdmap <i><?php print'href="'.MAP_URL.'"';?></i>. In deze hoofdmap 
				kun je vervolgens een map (en daarmee een zoekcriterium) kiezen. In deze submap weer een een map (en daarmee een nieuw zoekcriterium), etcetera. De betekenis van de mappenstructuur vanaf deze hoofdmap kan als 
				volgt worden weergegeven (bewust met veel spaties ertussen weergegeven):<br>
				<i><b><?php print'href="'.MAP_URL.'"';?></b>&nbsp;&nbsp;land&nbsp;&nbsp;/&nbsp;&nbsp;adres&nbsp;&nbsp;/&nbsp;&nbsp;radius&nbsp;&nbsp;/&nbsp;&nbsp;distributie&nbsp;&nbsp;/&nbsp;&nbsp;desktop-omgeving&nbsp;&nbsp;/&nbsp;&nbsp;onderwerpen&nbsp;&nbsp;/&nbsp;&nbsp;hulp_van&nbsp;&nbsp;/&nbsp;&nbsp;hulp_aan&nbsp;&nbsp;/&nbsp;&nbsp;beloning </i><br><br>				
				Per betekenis worden hieronder de bestaande mappen (criteria) opgesomd. Indien je voor een betekenis/map helemaal geen criterium wilt opgeven, laat dan deze map niet leeg, maar gebruik hiervoor de asterisk *. Mappen 
				die zich achteraan de URL bevinden, kunnen wel worden weggelaten. Een en ander wordt ook duidelijk gemaakt d.m.v. voorbeelden.<br><br>
				<i>land</i><br>
				Buurtlinux is een Nederlands initiatief, dus de enige geldige waarde momenteel is "NL", zodat de URL wordt:<br>
				<span id="map_url"><a <?php print'href="'.MAP_URL.'"';?>NL" target="_blanc"><?php print'href="'.MAP_URL.'"';?>NL</a></span><br><br>				
				<i>adres</i><br>
				Vul hier een stad, adres of postcode in (of alledrie) om de kaart te centreren op dit specifieke adres. Stel je wilt de plaats Bergen op Zoom centraal stellen, dan wordt de URL:<br>
				<span id="map_url"><a <?php print'href="'.MAP_URL.'"';?>NL/Bergen op Zoom" target="_blanc"><?php print'href="'.MAP_URL.'"';?>NL/Bergen op Zoom</a></span><br><br>
				<i>radius</i><br>
				Deze map geeft de radius rond het punt waar de kaart op is gecentreerd (te manipuleren via de adres-map, zie hierboven) aan. De radius wordt opgegeven in meters. Stel we willen inzoomen tot in een straal van 5km rond Bergen op Zoom, dan wordt de URL:<br>
				<span id="map_url"><a <?php print'href="'.MAP_URL.'"';?>NL/Bergen op Zoom/5000" target="_blanc"><?php print'href="'.MAP_URL.'"';?>NL/Bergen op Zoom/5000</a></span><br><br>
				<i>distributie</i><br>
				Geef in dit veld een of meer (of geen) distributie op. Op dit moment heb je de keuze uit de volgende distributies:<br><br>
					<?php 
						$l_db_access = new CriteriaLookup();
						$l_criteria = $l_db_access->get_distros_criteria("");
						if ( count( $l_criteria ) ) {
							foreach ( $l_criteria as $l_pair ) {
								print $l_pair['name']."<br>";
							}
						}
						print "<br>";
					?>
				Twee voorbeelden:<br>
				<span id="map_url"><a href="<?php print'href="'.MAP_URL.'"';?>NL/*/*/Ubuntu" target="_blanc"><?php print'href="'.MAP_URL.'"';?>NL/*/*/Ubuntu</a></span><br>
				<span id="map_url"><a href="<?php print'href="'.MAP_URL.'"';?>NL/*/*/Linux Mint, Debian" target="_blanc"><?php print'href="'.MAP_URL.'"';?>NL/*/*/Linux Mint, Debian</a></span><br>					
				Zoals je ziet maken we hier voor het eerst gebruik van de asterisk om een overzicht te kunnen geven voor heel Nederland. In het eerste voorbeeld van de Ubuntu-helpers en in het tweede voorbeeld van Linux Mint- en Debian-helpers.<br><br>
				<i>bureaublad-omgeving</i><br>
				Geef in dit veld een of meer (of geen) bureaublad-omgeving op. Op dit moment heb je de keuze uit de volgende bureaublad-omgevingen:<br><br>
					<?php 
						$l_db_access = new CriteriaLookup();
						$l_criteria = $l_db_access->get_desktops_criteria("");
						if ( count( $l_criteria ) ) {							
							foreach ( $l_criteria as $l_pair ) {
								print $l_pair['name']."<br>";
							}
						}
						print "<br>";
					?>
				Twee voorbeelden:<br>
				<span id="map_url"><a href="<?php print'href="'.MAP_URL.'"';?>NL/*/*/*/Gnome Desktop" target="_blanc"><?php print'href="'.MAP_URL.'"';?>NL/*/*/*/Gnome Desktop</a></span><br>
				<span id="map_url"><a href="<?php print'href="'.MAP_URL.'"';?>NL/*/*/Linux Mint, Debian/Enlightment Desktop, Unity Desktop" target="_blanc"><?php print'href="'.MAP_URL.'"';?>NL/*/*/Linux Mint, Debian/Enlightment Desktop, Unity Desktop</a></span><br><br>				
				<i>onderwerpen</i><br>
				Geef in dit veld een of meer (of geen) onderwerpen op. Op dit moment heb je de keuze uit de volgende onderwerpen:<br><br>
					<?php 
						$l_db_access = new CriteriaLookup();
						$l_criteria = $l_db_access->get_actions_criteria("");
						if ( count( $l_criteria ) ) {							
							foreach ( $l_criteria as $l_pair ) {
								print $l_pair['name']."<br>";
							}
						}
						print "<br>";
					?>
				Voorbeeld:<br>
				<span id="map_url"><a href="<?php print'href="'.MAP_URL.'"';?>NL/*/*/*/*/Nieuwe installaties, Verkoop Linux-computers" target="_blanc"><?php print'href="'.MAP_URL.'"';?>NL/*/*/*/*/Nieuwe installaties, Verkoop Linux-computers</a></span><br><br>
				<i>hulp_van</i><br>
				Geef in dit veld welk type hulp je graag wilt ontvangen. Je hebt momenteel de keus uit:
					<?php 
					$l_db_access = new CriteriaLookup();
					$l_criteria = $l_db_access->get_usergroups_criteria("");
					if ( count( $l_criteria ) ) {							
						foreach ( $l_criteria as $l_pair ) {
							print $l_pair['name']."<br>";
						}
					}
					print "<br>";
				?>
				Voorbeeld:<br>
				<span id="map_url"><a href="<?php print'href="'.MAP_URL.'"';?>NL/*/*/*/*/*/Bedrijven" target="_blanc"><?php print'href="'.MAP_URL.'"';?>NL/*/*/*/*/*/Bedrijven</a></span><br><br>
				<i>hulp_aan</i><br>
				Geef in dit veld aan tot welke groep je zelf behoort. Je hebt momenteel de keus uit:
					<?php 
						$l_db_access = new CriteriaLookup();
						$l_criteria = $l_db_access->get_targetgroups_criteria("");
						if ( count( $l_criteria ) ) {							
							foreach ( $l_criteria as $l_pair ) {
								print $l_pair['name']."<br>";
							}
						}
						print "<br>";
				?>
				Voorbeeld:<br>
				<span id="map_url"><a href="<?php print'href="'.MAP_URL.'"';?>NL/*/*/*/*/*/*/Scholen" target="_blanc"><?php print'href="'.MAP_URL.'"';?>NL/*/*/*/*/*/*/Scholen</a></span><br><br>				
				<i>beloning</i><br>
				Geef hier aan op welke beloning je wilt selecteren. Je kunt kiezen tussen:<br><br>
					<?php 
						$l_db_access = new CriteriaLookup();
						$l_criteria = $l_db_access->get_rewards_criteria("");
						if ( count( $l_criteria ) ) {							
							foreach ( $l_criteria as $l_pair ) {
								print $l_pair['id']." ".$l_pair['name']."<br>";
							}
						}
						print "<br>";
				?>
				Voorbeeld:<br>
				<span id="map_url"><a href="<?php print'href="'.MAP_URL.'"';?>NL/*/*/*/*/*/*/*/2" target="_blanc"><?php print'href="'.MAP_URL.'"';?>NL/*/*/*/*/*/*/*/2</a></span><br><br>
			</p>
			<h2>4 Weergave van het zoekpaneel</h2>
			<p>
				Standaard wordt het zoekpaneel weergegeven, maar je kunt ook alleen de kaart laten zien door de parameter <i>nosearch</i> toe te voegen aan de basis-URL: <i><?php print'href="'.MAP_URL.'"';?></i>, zodat deze wordt:
				<span id="map_url"><a href="<?php print'href="'.MAP_URL.'"';?>?nosearch" target="_blanc"><?php print'href="'.MAP_URL.'"';?>?nosearch</a></span><br><br>
			</p>
			<h2>5 Weergave van zoek-criteria in het zoekpaneel</h2>
			<p>
				Standaard worden alle mogelijke zoekcriteria weergegeven in het zoekpaneel. Je kunt er echter ook voor kiezen om per zoekcriteria het weergeven ervan in het zoekpaneel in of uit te schakelen. 
				Dit kan door aan de URL de parameter <i>search_allow</i> toe te voegen aan de basis-URL: <i><?php print'href="'.MAP_URL.'"';?></i>, zodat deze wordt:
				<i><?php print'href="'.MAP_URL.'"';?>?search_allow=</i>. Hieronder worden alle mogelijkheden opgesomd:<br><br>
				Alle criteria inschakelen:<br>
				<i><?php print'href="'.MAP_URL.'"';?>?search_allow=all</i><br><br>
				Distributie-criteria inschakelen:<br>
				<i><?php print'href="'.MAP_URL.'"';?>?search_allow=distros</i><br><br>
				Bureaublad-criteria inschakelen:<br>
				<i><?php print'href="'.MAP_URL.'"';?>?search_allow=desktops</i><br><br>
				Onderwerpen-criteria inschakelen:<br>
				<i><?php print'href="'.MAP_URL.'"';?>?search_allow=actions</i><br><br>
				Hulp-aan-criteria inschakelen:<br>
				<i><?php print'href="'.MAP_URL.'"';?>?search_allow=groups</i><br><br>
				Hulp-van-criteria inschakelen:<br>
				<i><?php print'href="'.MAP_URL.'"';?>?search_allow=targets</i><br><br>
				Belonings-criteria inschakelen:<br>
				<i><?php print'href="'.MAP_URL.'"';?>?search_allow=rewards</i><br><br>
				Twee voorbeelden:<br>
				<span id="map_url"><a href="<?php print'href="'.MAP_URL.'"';?>?search_allow=distros" target="_blanc"><?php print'href="'.MAP_URL.'"';?>?search_allow=distros</a></span><br>
				<span id="map_url"><a href="<?php print'href="'.MAP_URL.'"';?>?search_allow=actions,rewards" target="_blanc"><?php print'href="'.MAP_URL.'"';?>?search_allow=actions,rewards</a></span><br><br>
				Zoals je in het laatste voorbeeld kunt zien, kun je meerdere zoek-criteria toevoegen aan het zoekpaneel door ze komma-gescheiden op te geven in de URL. Verder moet opgemerkt worden dat het
				gebruik van deze parameter alleen functioneert als je andere zoekcriteria NIET op de manier aan de URL meegeeft zoals aangegeven in H3, maar bijvoorbeeld WEL zoals in H2 wordt uitgelegd:<br>
				<span id="map_url"><a href="<?php print'href="'.MAP_URL.'"';?>?search_allow=actions,groups,targets,rewards&distros[]=Ubuntu&desktops[]=Gnome+Desktop" target="_blanc"><?php print'href="'.MAP_URL;?>?search_allow=actions,groups,targets,rewards&distros[]=Ubuntu&desktops[]=Gnome+Desktop</a></span><br><br>
			</p>
			<h2>6 Inbedden van de Buurtlinux kaart</h2>
				Je kunt de Buurtlinux-kaart opnemen in je eigen website door gebruik te maken van IFRAME's. Dit kan door het volgende fragment toe te voegen aan je webpagina:<br><br>
				<div id="code_block">
				&lt;iframe frameborder='0' marginwidth='0' marginheight='0' src='<?php print MAP_URL."index.php";?>' style='width: 100%; height: 100%;'&gt;<br />
					Je browser ondersteunt geen Iframe. Bezoek de &lt;a target='_blanc' alt='Bezoek de oorspronkelijke website' title='Bezoek de oorspronkelijke website' href='<?php print'href="'.MAP_URL;?>index.php'&gt;oorspronkelijke website&lt;/a&gt;<br />
				&lt;/iframe&gt;</div><br /><br />
				De eigenschappen zoals breedte en hoogte van het iframe kun je natuurlijk wijzigen naar eigen inzicht. Een toepassing op basis van onderstaande HTML-code vindt je <span id="map_url"><a href="<?php print MAP_URL.'index_iframed.php';?>" target="_blanc" title="Open de Buurtlinux kaart in een nieuw venster">hier</a></span>.<br><br>
				<div id="code_block">
				&lt;div style="height: 40px;"&gt;Kopje&lt;/div&gt;<br />
		&lt;div style="position: absolute; top: 40px; left: 0px; right: 0px; bottom: 40px;"&gt;<br />
			&lt;iframe frameborder='0' marginwidth='0' marginheight='0' src='<?php print MAP_URL."index.php";?>' style='width: 100%; height: 100%;'&gt;<br />
					Je browser ondersteunt geen Iframe. Bezoek de &lt;a target='_blanc' alt='Bezoek de oorspronkelijke website' title='Bezoek de oorspronkelijke website' href='<?php print MAP_URL;?>index.php&gt;oorspronkelijke website&lt;/a&gt;<br />
				&lt;/iframe&gt;<br />
		&lt;/div&gt;<br />
		&lt;div style="height: 40px; position: absolute; bottom: 0px;"&gt;Voetje&lt;/div&gt;
				</div>
		</div>
	</body>
</html>
