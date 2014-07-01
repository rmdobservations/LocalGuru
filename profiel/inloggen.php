<?php
	// Start a PHP session
	session_start();

	require_once('../settings.php');
	
	/********************************************************************
		Save statistics with bbclone
	********************************************************************/
	define("_BBC_PAGE_NAME", "Inloggen profiel");
	define("_BBCLONE_DIR", ROOT_URL."stats/");
	define("COUNTER", _BBCLONE_DIR."mark_page.php");
	if (is_readable(COUNTER)) include_once(COUNTER);
	
	// We need user- and criteria information from database
	require_once( "../lib/db_lookup_users.php" );
	require_once( "../lib/db_lookup_criteria.php" );
	
	// Any stored wizard data should be destroyed now
	unset( $_SESSION['wizard'] );
	
	// Set the default login status
	$_SESSION['login']['status'] = "login";
	
	// Process form data
	process_form_data();
	
	// Retrieves profile from database based on given username and password
	function get_profile( $a_username, $a_password ) {
		$l_lookup = new UsersLookup();
		$l_user = $l_lookup->get_user_bylogin( $a_username, $a_password );
		if( $l_user['id'] != NULL )
		{
			$l_lookup = new CriteriaLookup();
			$l_tags = $l_lookup->get_criteria_tagged_to( $l_user['id'] );
			$_SESSION['wizard']['profile']['id']	 				= $l_user['id'];
			$_SESSION['wizard']['profile']['firstname'] 				= $l_user['firstname'];
			$_SESSION['wizard']['profile']['lastname'] 			= $l_user['lastname'];
			$_SESSION['wizard']['profile']['companyname'] 		= $l_user['companyname'];
			$_SESSION['wizard']['profile']['street'] 				= $l_user['street'];
			$_SESSION['wizard']['profile']['number'] 				= $l_user['number'];
			$_SESSION['wizard']['profile']['zip'] 					= $l_user['zip'];
			$_SESSION['wizard']['profile']['city'] 					= $l_user['city'];
			$_SESSION['wizard']['profile']['phone1'] 				= $l_user['phone_1st'];
			$_SESSION['wizard']['profile']['phone2'] 				= $l_user['phone_2nd'];
			$_SESSION['wizard']['profile']['website'] 				= $l_user['website'];
			$_SESSION['wizard']['profile']['email']				= $l_user['email'];
			$_SESSION['wizard']['profile']['info'] 					= $l_user['info'];
			$_SESSION['wizard']['profile']['username']			= $l_user['username'];
			$_SESSION['wizard']['profile']['password'] 			= $l_user['password'];
			
			/* We only want the names of the tags, not the id's so strip them (except for the group) */
			foreach ($l_tags['distros'] as $l_tag )				$_SESSION['wizard']['profile']['distros'][] = $l_tag['name'];
			foreach ($l_tags['desktops'] as $l_tag )				$_SESSION['wizard']['profile']['desktops'][] = $l_tag['name'];
			foreach ($l_tags['actions'] as $l_tag )				$_SESSION['wizard']['profile']['actions'][] = $l_tag['name'];
			foreach ($l_tags['rewards'] as $l_tag )				$_SESSION['wizard']['profile']['rewards'][] = $l_tag['name'];
			$_SESSION['wizard']['profile']['group'][] = $l_tags['group'];
			foreach ($l_tags['targets'] as $l_tag ) 				$_SESSION['wizard']['profile']['targets'][] = $l_tag['name'];
			return true;
		}else return false;
	}
	
	require_once( "../lib/db_lookup_criteria.php" );
	
	function process_form_data() {
		// See if user tried to login
		if( $_POST['btn_login'] == "Inloggen" ) {
			if ( isset($_POST['username']) && isset($_POST['password']) ) {
				$_SESSION['login']['username'] = $_POST['username'];
				$_SESSION['login']['password'] = md5( $_POST['password'] );
				if ( get_profile( $_SESSION['login']['username'], $_SESSION['login']['password'] ) ) {
					$_SESSION['login']['status'] = "loggedin";
				} else {
					$_SESSION['login']['status'] = "failed";
					$_SESSION['login']['msg'] = "Deze combinatie van gebruikersnaam en wachtwoord is niet bekend bij het systeem";
				}				
			}else {
				$_SESSION['login']['status'] = "failed";
				$_SESSION['login']['msg'] = "Gebruikersnaam en/of wachtwoord onjuist";
			}
		 }
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>
			Buurtlinux - Inloggen voor profiel
		</title>
		<!-- Meta-tags -->
		<meta charset="UTF-8">
		<!-- CSS -->
<?php print '
		<link rel="stylesheet" type="text/css" href="'.MAP_URL.'styles/general.css">
		<link rel="stylesheet" type="text/css" href="'.MAP_URL.'styles/map.css">
		<link rel="stylesheet" type="text/css" href="'.MAP_URL.'styles/profile.css">
'; ?>
		<!-- JavaScript -->
	</head>
	<?php flush(); ?>
	<body>
		<?php
			switch ( $_SESSION['login']['status'] ) {					
				case "loggedin":
					print '
						<form id="profile_form" name="profile_loggedin" enctype="multipart/form-data" action="">
							<div id="profile_title">Inloggen geslaagd</div>
							<div id="profile_label">
								Je bent nu ingelogd. Druk op de Profiel-knop om je profiel te bekijken en/of aan te passen.
							</div>
							<div id="profile_nav_bot">								
								<input id="profile_button" onClick="parent.location=\''.MAP_URL.'profiel/bewerken\'" value="Profiel" type="button" />
							</div>
						</form>';
					break;
					
				case "failed":
					print '
						<form id="profile_form" name="profile_login_failed" enctype="multipart/form-data" action="">
							<div id="profile_title">Inloggen mislukt</div>
							<div id="profile_label">'.$_SESSION['login']['msg'].'<br><br> Druk op "Terug" om het opnieuw te proberen. Om te registreren druk op "Registreren". Om terug te keren naar de Buurtlinux-kaart, druk op "Annuleren".
							</div>
							<div id="profile_nav_bot">								
								<input id="profile_button" onClick="parent.location=\''.MAP_URL.'profiel/inloggen\'" value="Terug" type="button" />
								<input id="profile_button" onClick="parent.location=\''.MAP_URL.'\'" value="Annuleren" type="button" />
								<input id="profile_button" onClick="parent.location=\''.MAP_URL.'profiel/registreren\'" value="Registreren" type="button" />
							</div>
						</form>';
					break;
					
				case "login":
				default:
					print '
						<form id="profile_form" name="profile_login" enctype="multipart/form-data" action="'.MAP_URL.'profiel/inloggen" target="_self" method="post">
							<div id="profile_title">Linux-hulp inlogscherm</div>							
							<div id="profile_heading">Inloggegevens</div>
							<div id="profile_label">Vul je gegevens in en druk op "Inloggen" om naar je profiel-pagina te gaan of meld jezelf aan als Linux-hulp door te registreren</div>
							<div id="profile_label">Gebruikersnaam:</div>
							<input type="text" id="profile_inputbox" name="username" />
							<div id="profile_label">Wachtwoord:</div>
							<input type="password" id="profile_inputbox" name="password" />
							<div id="profile_nav_bot">
								<input id="profile_button" name="btn_login" value="Inloggen" type="submit" />
								<input id="profile_button" onClick="parent.location=\''.MAP_URL.'profiel/registreren\'" value="Registreren" type="button" />
							</div>
						</form>
					';
					break;
			}
		?>
	</body>
</html>
