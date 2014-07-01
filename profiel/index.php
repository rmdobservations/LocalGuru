<?php
	/* Start a PHP session */
	session_start();

	require_once('../settings.php');
	require_once('../lib/db_access.php');

	$_SESSION['form_feedback_msg'] = "";
	
	/********************************************************************
		Save statistics with bbclone
	********************************************************************/
	define("_BBC_PAGE_NAME", "Profiel weergeven");
	define("_BBCLONE_DIR", MAP_URL."stats/");
	define("COUNTER", _BBCLONE_DIR."mark_page.php");
	if (is_readable(COUNTER)) include_once(COUNTER);
	
	// Form post data should always be processed prior to changing the
	// wizard's step count
	if ( 	isset( $_SESSION['wizard'] ) )
	{
		if ( isset($_POST['btn_register']) && $_POST['btn_register'] == "Registreren" ) {
			if( process_form_post() ) {
				register_user();
				$_SESSION['wizard']['end'] = true;
			}
		} else if ( isset($_POST['btn_save']) && $_POST['btn_save'] == "Opslaan" ) {
			if( process_form_post() )  {
				save_profile();
				$_SESSION['wizard']['end'] = true;
			}
		} else if ( isset($_POST['btn_prev']) && $_POST['btn_prev'] == "Vorige" ) {
			if ( $_SESSION['wizard']['step'] > 1) {
				process_form_post();
				$_SESSION['wizard']['step']--;				
				$_SESSION['form_feedback_msg'] = "";	// Clear since message doesn't belong to previous step
			}
		} else if ( isset($_POST['btn_next']) && $_POST['btn_next'] == "Verder" ) {
				if ( $_SESSION['wizard']['step'] < $_SESSION['wizard']['step_max'] )
				{
					if( process_form_post() ) $_SESSION['wizard']['step']++;
				}		
		} else {
			$_SESSION['wizard']['step'] = 1;
			$_SESSION['wizard']['step_max'] = 9;
			$_SESSION['wizard']['end'] = false;
			$_SESSION['wizard']['msg'] = "";
			
			if (	isset( $_GET['do'] ) && ( $_GET['do'] == "bewerken" ) && 											// User want to alter his/her profile
				($_SESSION['login']['status'] == "loggedin") && isset( $_SESSION['wizard']['profile'] ) )		// User is allowed to do so
			{
				$_SESSION['wizard']['new_profile'] = $_SESSION['wizard']['profile'];
			}
		}
	}
	else {
		// Start with empty new profile
		$_SESSION['wizard']['new_profile']['group'] = NULL;
		$_SESSION['wizard']['new_profile']['targets'] = NULL;
		$_SESSION['wizard']['new_profile']['firstname'] = "";
		$_SESSION['wizard']['new_profile']['lastname'] = "";
		$_SESSION['wizard']['new_profile']['companyname'] = "";
		$_SESSION['wizard']['new_profile']['street'] = "";
		$_SESSION['wizard']['new_profile']['number'] = "";
		$_SESSION['wizard']['new_profile']['phone1'] = "";
		$_SESSION['wizard']['new_profile']['phone2'] = "";
		$_SESSION['wizard']['new_profile']['zip'] = "";
		$_SESSION['wizard']['new_profile']['city'] = "";
		$_SESSION['wizard']['new_profile']['website'] = "";
		$_SESSION['wizard']['new_profile']['email'] = "";
		$_SESSION['wizard']['new_profile']['info'] = "";
		$_SESSION['wizard']['new_profile']['username'] = "";
		$_SESSION['wizard']['new_profile']['password'] = "";
		$_SESSION['wizard']['new_profile']['distros'] = NULL;
		$_SESSION['wizard']['new_profile']['desktops'] = NULL;
		$_SESSION['wizard']['new_profile']['actions'] = NULL;
		$_SESSION['wizard']['new_profile']['rewards_companies'] = NULL;
		$_SESSION['wizard']['new_profile']['rewards_schools'] = NULL;
		$_SESSION['wizard']['new_profile']['rewards_individuals'] = NULL;
		$_SESSION['wizard']['new_profile']['image'] = NULL;

		$_SESSION['wizard']['step'] = 1;
		$_SESSION['wizard']['step_max'] = 9;
		$_SESSION['wizard']['end'] = false;
		$_SESSION['wizard']['msg'] = "";
	}
	
	require_once( "../lib/db_lookup_criteria.php" );
	
	function check_array_for( $a_array, $a_needle ) {		
		if ( !isset($a_array) ) return false;
		if ( !isset($a_needle) ) return false;
		
		if ( is_array( $a_array ) ) {
			foreach( $a_array as $a_item ) {
				if( $a_item == $a_needle ) return true;
			}
		}
		else {
			if( $a_array == $a_needle ) return true;
		}
		return false;
	}
	
	function process_form_post() {
		$l_filled_in = true;
		switch ( $_SESSION['wizard']['step'] ) {
			case 1:	$l_filled_in = (isset($_POST['group']) == $l_filled_in);
						$_SESSION['wizard']['new_profile']['group'] 							= $_POST['group'];					break;				
			case 2:	$l_filled_in = (isset($_POST['targets']) == $l_filled_in);
						$_SESSION['wizard']['new_profile']['targets'] 						= $_POST['targets'];					break;
			case 3:	$l_filled_in = (isset($_POST['firstname']) == $l_filled_in);
						$_SESSION['wizard']['new_profile']['firstname']						= $_POST['firstname'];
						$l_filled_in = (isset($_POST['lastname']) == $l_filled_in);
						$_SESSION['wizard']['new_profile']['lastname'] 						= $_POST['lastname'];
						if($_SESSION['wizard']['new_profile']['group'] == "Bedrijf")
						{
							$l_filled_in = (isset($_POST['companyname']) == $l_filled_in);
							$_SESSION['wizard']['new_profile']['companyname'] 				= $_POST['companyname'];
							$l_filled_in = (isset($_POST['street']) == $l_filled_in);
							$_SESSION['wizard']['new_profile']['street'] 					= $_POST['street'];
							$l_filled_in = (isset($_POST['number']) == $l_filled_in);
							$_SESSION['wizard']['new_profile']['number'] 					= $_POST['number'];
							$l_filled_in = (isset($_POST['phone1']) == $l_filled_in);
							$_SESSION['wizard']['new_profile']['phone1'] 					= $_POST['phone1'];
							$l_filled_in = (isset($_POST['phone2']) == $l_filled_in);
							$_SESSION['wizard']['new_profile']['phone2'] 					= $_POST['phone2'];
						}					
						$l_filled_in = (isset($_POST['zip']) == $l_filled_in);
						$_SESSION['wizard']['new_profile']['zip'] 							= $_POST['zip'];
						$l_filled_in = (isset($_POST['city']) == $l_filled_in);
						$_SESSION['wizard']['new_profile']['city'] 							= $_POST['city'];
						$l_filled_in = (isset($_POST['website']) == $l_filled_in);
						$_SESSION['wizard']['new_profile']['website'] 						= $_POST['website'];
						$l_filled_in = (isset($_POST['email']) == $l_filled_in);
						$_SESSION['wizard']['new_profile']['email'] 							= $_POST['email'];
						$l_filled_in = (isset($_POST['info']) == $l_filled_in);
						$_SESSION['wizard']['new_profile']['info'] 							= $_POST['info'];						break;				
			case 4:	$_SESSION['wizard']['new_profile']['username']						= $_POST['username'];
						$_SESSION['wizard']['new_profile']['password'] 						= $_POST['password'];				break;				
			case 5:	$_SESSION['wizard']['new_profile']['distros'] 						= $_POST['distros'];					break;
			case 6:	$_SESSION['wizard']['new_profile']['desktops']						= $_POST['desktops'];				break;
			case 7:	$_SESSION['wizard']['new_profile']['actions'] 						= $_POST['actions'];					break;
			case 8:	$_SESSION['wizard']['new_profile']['rewards_companies'] 			= $_POST['rewards_companies'];	break;
						$_SESSION['wizard']['new_profile']['rewards_schools'] 			= $_POST['rewards_schools'];		break;
						$_SESSION['wizard']['new_profile']['rewards_individuals'] 		= $_POST['rewards_individuals'];	break;
			case 9:	$_SESSION['wizard']['new_profile']['image']							= $_FILES['profile_image'];		break;
			default: 																																break;
		}
		
		// Inform user if needed
		if( !$l_filled_in ) 
			$_SESSION['form_feedback_msg'] = "<div style='color: red'>Je hebt geen keus gemaakt of niet alle vereiste velden zijn ingevuld. Probeer het opnieuw...</div>";
			
		return $l_filled_in;
	}
	
	function register_user() {
		// Save user profile
//		save_new_profile();

		// Provide user with feedback and options
		$_SESSION['wizard']['msg'] = '<div id="profile_form">';
		$_SESSION['wizard']['msg'] .= '<div id="profile_title">Registratie voltooid</div>';
		$_SESSION['wizard']['msg'] .= '<div id="profile_label">Het is gelukt! Je bent nu geregistreerd. <br><br>';
		$_SESSION['wizard']['msg'] .= 'Dit betekent dat je op de Buurtlinux kaart wordt vermeld en dat mensen je kunnen vinden als ze zoeken naar Linux-hulp in hun buurt. ';
		$_SESSION['wizard']['msg'] .= 'Via dit scherm kun je terug gaan naar de Buurtlinux beginpagina of naar de Zoek Hulp pagina met de Buurtlinux kaart. ';
		$_SESSION['wizard']['msg'] .= 'Eventueel kun je ook meteen je profiel wijzigen<br><br>';	
		$_SESSION['wizard']['msg'] .= 'Gebruik onderstaande knoppen om verder te gaan</div>';
		$_SESSION['wizard']['msg'] .= '<div id="profile_nav_bot">';
		$_SESSION['wizard']['msg'] .= '<input id="profile_button" onClick="parent.location=\''.ROO_UTL.'\'" value="Buurtlinux" type="button" />';
		$_SESSION['wizard']['msg'] .= '<input id="profile_button" onClick="parent.location=\''.MAP_URL.'\'" value="Zie kaart" type="button" />';
		$_SESSION['wizard']['msg'] .= '<input id="profile_button" onClick="parent.location=\''.MAP_URL.'profiel/inloggen\'" value="Inloggen" type="button" />';
		$_SESSION['wizard']['msg'] .= '</div></div>';
	}
	
	function save_profile() {
		// Save user profile
//		save_edited_profile();

		// Provide user with feedback and options
		$_SESSION['wizard']['msg'] = '<div id="profile_title">Einde Linux-hulp profiel wizard</div>';
	}
	
	function upload( $a_file ) {
		if(is_uploaded_file($a_file['tmp_name'])) { 
			// check the file is less than the maximum file size
			if($a_file['size'] < $maxsize) {
				// prepare the image for insertion
				$imgData =addslashes (file_get_contents($a_file['tmp_name']));
				// $imgData = addslashes( $a_file );
			 
				// get the image info..
				$size = getimagesize($a_file['tmp_name']);
			 
				// put the image in the db...
				$l_db = new Database();
				if ( !$l_db->connect() ) {						
					return false;
				}
/*
				// database connection
				mysql_connect("localhost", "$username", "$password") OR DIE (mysql_error());
			 
				// select the db
				mysql_select_db ("$dbname") OR DIE ("Unable to select db".mysql_error());
			 
				// our sql query
				$sql = "INSERT INTO testblob
					( image_id , image_type ,image, image_size, image_name)
					VALUES
					('', '{$size['mime']}', '{$imgData}', '{$size[3]}', '{$a_file['name']}')";
			 
				// insert the image
				if(!mysql_query($sql)) {
					echo 'Unable to upload file';
				}
*/
			}
		}
		else {
			// if the file is not less than the maximum allowed, print an error
			echo
			'<div>File exceeds the Maximum File limit</div>
			<div>Maximum File limit is '.$maxsize.'</div>
			<div>File '.$a_file['name'].' is '.$a_file['size'].' bytes</div>
			<hr />';
		}
	}

	function save_new_profile()
	{
		require_once('../lib/db_save_users.php');
		$l_usersave = new UsersSave();
		/* Add missing parameters */
		$l_user['username'] = $l_user['email'];
		$l_user['password'] 	= $l_usersave->generate_random_string() ;			
		$l_user_id = $l_usersave->save_new_user( $l_user );
		
		require_once('../lib/db_save_criteria.php');
		$l_criteriasave = new CriteriaSave();
		$l_criterialookup = new CriteriaLookup();
		if( is_array($l_distros) ) {
			foreach( $l_distros as $l_name ) {
				$l_id = $l_criterialookup->distro_exists( $l_name ) ;
				$l_criteriasave->save_tag_usr_to_distro( $l_user_id, $l_id );
			}
		}
		if( is_array($l_desktops) ) {
			foreach( $l_desktops as $l_name ) {
				$l_id = $l_criterialookup->desktop_exists( $l_name ) ;
				$l_criteriasave->save_tag_usr_to_desktop( $l_user_id, $l_id );
			}
		}
		if( is_array($l_actions) ) {
			foreach( $l_actions as $l_name ) {
				$l_id = $l_criterialookup->action_exists( $l_name ) ;					
				$l_criteriasave->save_tag_usr_to_action( $l_user_id, $l_id );
			}
		}
		if( is_array($l_groups) ) {
			foreach( $l_groups as $l_name ) {
				$l_id = $l_criterialookup->group_exists( $l_name ) ;					
				$l_criteriasave->save_tag_usr_to_group( $l_user_id, $l_id );
			}
		}
		if( is_array($l_targets) ) {
			foreach( $l_targets as $l_name ) {
				$l_id = $l_criterialookup->target_exists( $l_name ) ;
				$l_criteriasave->save_tag_usr_to_target( $l_user_id, $l_id );
			}
		}
		$l_id = $l_criterialookup->reward_exists( $l_rewards_ind ) ;			
		$l_target_id = $l_criterialookup->target_exists( "Particulieren" ) ;
		$l_criteriasave->save_tag_usr_to_reward( $l_user_id, $l_id, $l_target_id  );
		
		$l_id = $l_criterialookup->reward_exists( $l_rewards_com ) ;			
		$l_target_id = $l_criterialookup->target_exists( "Bedrijven" ) ;
		$l_criteriasave->save_tag_usr_to_reward( $l_user_id, $l_id, $l_target_id  );
		
		$l_id = $l_criterialookup->reward_exists( $l_rewards_sch ) ;			
		$l_target_id = $l_criterialookup->target_exists( "Scholen" ) ;
		$l_criteriasave->save_tag_usr_to_reward( $l_user_id, $l_id, $l_target_id  );

		return TRUE;
	}

	function save_edited_profile()
	{
		return TRUE;
	}
	
	function print_distros() {
		$l_criteria = new CriteriaLookup();
		$l_items = $l_criteria->get_distros_criteria("");
		if( is_array($l_items) ) {
			foreach( $l_items as $l_item ) {
				if( check_array_for( $_SESSION['wizard']['new_profile']['distros'], $l_item['name']) ) {
					print "<input type='checkbox' name='distros[]' checked='yes'>".$l_item['name']."</input><br>";
				}
				else
					print "<input type='checkbox' name='distros[]'>".$l_item['name']."</input><br>";
			}
		}
	}
	
	function print_desktops() {
		$l_criteria = new CriteriaLookup();
		$l_items = $l_criteria->get_desktops_criteria("");
		if( is_array($l_items) ) {
			foreach( $l_items as $l_item ) {
				if( check_array_for( $_SESSION['wizard']['new_profile']['desktops'], $l_item['name']) ) {
					print "<input type='checkbox' name='desktops[]' checked='yes'>".$l_item['name']."</input><br>";
				}
				else
					print "<input type='checkbox' name='desktops[]'>".$l_item['name']."</input><br>";
			}
		}
	}
	
	function print_actions() {
		$l_criteria = new CriteriaLookup();
		$l_items = $l_criteria->get_actions_criteria("");
		if( is_array($l_items) ) {
			foreach( $l_items as $l_item ) {
				if( check_array_for( $_SESSION['wizard']['new_profile']['actions'], $l_item['name']) ) {
					print "<input type='checkbox' name='actions[]' checked='yes'>".$l_item['name']."</input><br>";
				}
				print "<input type='checkbox' name='actions[]'>".$l_item['name']."</input><br>";
			}
		}
	}
	
	function print_groups() {		
		$l_criteria = new CriteriaLookup();
		$l_items = $l_criteria->get_usergroups_criteria("");		
		print '<select id="profile_dropdownbox" name="group" >';
		if( is_array($l_items) ) {			
			foreach( $l_items as $l_item ) {				
				if( $_SESSION['wizard']['new_profile']['group'] == $l_item['name'] )  {
					print "<option selected='selected' value='".$l_item['name']."'>".$l_item['name']."</option>";
				}
				else
					print "<option value='".$l_item['name']."'>".$l_item['name']."</option>";
			}			
		}
		print "<select><br>";
	}
	
	function print_targets() {		
		$l_criteria = new CriteriaLookup();
		$l_items = $l_criteria->get_targetgroups_criteria("");
		if( is_array( $l_items ) ) {			
			foreach( $l_items as $l_item ) {
				if( check_array_for( $_SESSION['wizard']['new_profile']['targets'], $l_item['name']) ) {
					print "<input type='checkbox' name='targets[]' checked='yes' value=".$l_item['name'].">".$l_item['name']."</input><br>";					
				}
				else
					print "<input type='checkbox' name='targets[]' value=".$l_item['name'].">".$l_item['name']."</input><br>";
			}			
		}
	}
	
	function print_rewards( $a_type ) {
		$l_criteria = new CriteriaLookup();
		$l_items = $l_criteria->get_rewards_criteria("");
		switch( $a_type  ) {				
			case "schools":		$l_name = "rewards_schools[]";				break;				
			case "companies":		$l_name = "rewards_companies[]";				break;				
			default: 				$l_name = "rewards_individuals[]";			break;		/* = individuals */
		}
		if( is_array($l_items) ) {
			print '<select id="profile_dropdownbox" name="'.$l_name.'">';
			foreach( $l_items as $l_item ) {
				if( check_array_for( $_SESSION['wizard']['new_profile']['targets'], $l_item['name']) ) {
					print "<option selected='selected' value='".$l_item['name']."'>".$l_item['name']."</input><br>";
				}	
				else
					print "<option value='".$l_item['name']."'>".$l_item['name']."</input><br>";
			}
			print "<select><br>";
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>
			Buurtlinux - Profiel
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
		<script>
			function textCounter( field, countfield, maxlimit ) {
				if ( field.value.length > maxlimit ) {
					field.value = field.value.substring( 0, maxlimit );
					alert( 'Het maximum aantal tekens is 1000' );
					return false;
				} 
				else {
					countfield.value = maxlimit - field.value.length;
				}
			}
		</script>
	</head>
	<?php flush(); ?>
	<body>
	<?php
		$l_noprofile = false;
		if( isset($_GET['do']) && ( $_GET['do'] == "bekijken") ) {	// User profile should be shown
			if( !isset($_GET['usr']) || $_GET['usr'] == "") {
				$l_noprofile = true;
			}
			else {
				require_once('../lib/db_lookup_users.php');
				$l_lookup = new UsersLookup();
				$l_user = $l_lookup->get_user_byid( $_GET['usr'] );
				/* does user exist? Then also fetch its tags */
				if( is_array($l_user) ) {
					require_once('../lib/db_lookup_users.php');
					$l_lookup = new CriteriaLookup();
					$l_criteria = $l_lookup->get_criteria_tagged_to( $_GET['usr'] );
					
					$l_actions = "";
					if( isset( $l_criteria['actions'] ) ) {
						$l_actions .= '<div id="profile_heading">Ik bied ondersteuning voor de volgende onderwerpen:</div>';
						foreach( $l_criteria['actions'] as $l_record ) {							
							$l_actions .= '<div id="profile_label">'.$l_record['name'].'</div>';
						}
					}
					
					$l_distros = "";
					if( isset( $l_criteria['distros'] ) ) {
						$l_distros .= '<div id="profile_heading">Ik kan je helpen met de volgende Linux-distributies:</div>';
						foreach( $l_criteria['distros'] as $l_record ) {
							$l_distros .= '<div id="profile_label">'.$l_record['name'].'</div>';
						}
					}
					
					$l_desktops = "";
					if( isset( $l_criteria['desktops'] ) ) {
						$l_desktops .= '<div id="profile_heading">Ik kan je helpen met de volgende bureaublad-omgevingen:</div>';
						foreach( $l_criteria['desktops'] as $l_record ) {
							$l_desktops .= '<div id="profile_label">'.$l_record['name'].'</div>';
						}
					}
					
					$l_targets = "";
					if( isset( $l_criteria['targets'] ) ) {						
						$l_targets .= '<div id="profile_heading">Ik bied ondersteuning aan:</div>';
						foreach( $l_criteria['targets'] as $l_record ) {														
							$l_targets .= '<div id="profile_label">'.$l_record['name'].'</div>';
						}
					}
					
					$l_rewards = "";
					if( isset( $l_criteria['rewards'] ) ) {
						$l_rewards .= '<div id="profile_heading">Mijn gewenste beloning:</div>';
						foreach( $l_criteria['rewards'] as $l_record ) {
							$l_rewards .= '<div id="profile_label">'.$l_record['target'].': '.$l_record['reward'].'</div>';
						}
					}
					
					$l_title = $l_user['firstname'].' '.$l_user['lastname'];
					$l_info = "";
					if( $l_user['companyname'] != "" ) $l_title .= " van ".$l_user['companyname'];
					if( $l_user['info'] != "") {
						$l_info .= '<div id="profile_heading">Aanvullende informatie:</div>
								<div id="profile_label">'.$l_user['info'].'</div>';
					}

					$l_contact = '<form><button style="margin-top: 25px;" onClick="parent.location=\''.MAP_URL.'email.php?user='.$_GET['usr'].'\'; return true" type="button">Neem contact op</button></form>';

					print '
						<div style="font-size: 12px;">
							<div id="profile_title">Profiel van '.$l_title.'</div>
							'.$l_distros.$l_desktops.$l_actions.$l_targets.$l_rewards.$l_info.$l_contact.'
						</div>
					';					
				}
				else $l_noprofile = true;
			}
			/* Display error message if no profile was selected */
			if( $l_noprofile ) {
				print '
					<div style="margin-left: 25px; font-size: 12px;">
						<div id="profile_title">Linux-hulp onbekend</div>
						<div id="profile_label">
							Er is geen bekende Linux-hulp opgegeven. Gebruik de navigatie hieronder om verder te gaan.
						</div>
						<div id="profile_nav_bot">
							<div id="map_url"><a href="'.ROOT_URL.'" target="_self" title="Ga naar de Buurtlinux website" alt="Ga naar de Buurtlinux website">Buurtlinux website</a></div>							
							<div id="map_url"><a href="javascript:void(0);" onclick="history.back(); return false;" target="_blanc" title="Ga terug naar de kaart" alt="Ga terug naar de kaart">Terug naar de kaart</a></div>
						</div>
					</div>
				';
			}
		}		
		else if ( isset( $_SESSION['wizard']['end'] ) && $_SESSION['wizard']['end'] ) {	// Show message of success/failure when user has registered/saved
			print $_SESSION['wizard']['msg'];
		}
		else {
			print '<form id="profile_form" name="profile" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" target="_self" method="post">
					<div id="profile_title">Linux-hulp registratie-wizard (Stap '.$_SESSION['wizard']['step'].' van '.$_SESSION['wizard']['step_max'].')</div>
					<div id="profile_steps">';
			print '<div '; if( $_SESSION['wizard']['step'] == 1 )  print "id='profile_steps_selected'"; print '>Hulpgroep</div>';
			print '<div '; if( $_SESSION['wizard']['step'] == 2 )  print "id='profile_steps_selected'"; print '>Doelgroepen</div>';
			print '<div '; if( $_SESSION['wizard']['step'] == 3 )  print "id='profile_steps_selected'"; print '>Gebruikersgegevens</div>';
			print '<div '; if( $_SESSION['wizard']['step'] == 4 )  print "id='profile_steps_selected'"; print '>Inloggegevens</div>';
			print '<div '; if( $_SESSION['wizard']['step'] == 5 )  print "id='profile_steps_selected'"; print '>Distributies</div>';
			print '<div '; if( $_SESSION['wizard']['step'] == 6 )  print "id='profile_steps_selected'"; print '>Bureaublad-omgevingen</div>';
			print '<div '; if( $_SESSION['wizard']['step'] == 7 )  print "id='profile_steps_selected'"; print '>Onderwerpen</div>';
			print '<div '; if( $_SESSION['wizard']['step'] == 8 )  print "id='profile_steps_selected'"; print '>Beloning</div>';
			print '<div '; if( $_SESSION['wizard']['step'] == 9 )  print "id='profile_steps_selected'"; print '>Afbeelding</div>';
			print '</div>';			
			
			print $_SESSION['form_feedback_msg'];
			
			switch( $_SESSION['wizard']['step'] )  {
				case 9:
					print '
						<div id="profile_heading">Afbeelding toevoegen</div>
						<div id="profile_label">Het kan voor gebruikers belangrijk zijn om een indruk te krijgen van jou als Linux-hulp. Je hebt daarom de mogelijkheid om een afbeelding aan je profiel toevoegen 
						die op de Buurtlinux-kaart zal worden weergegeven. Gebruik deze mogelijkheid a.u.b. alleen om een duidelijke foto van jezelf toe te voegen en niet het logo van je favoriete distributie,
						Tux etcetera, zoals je dat voor een forum-avatar wellicht zou doen.</div>
						<div>&nbsp;</div>
						<div id="profile_label">Afbeeldingen kunnen het beste een afmeting hebben van 200x200 pixels. Afbeeldingen met een grotere afmeting worden automatisch verkleind tot 200x200 pixels.</div>
						<div>&nbsp;</div>
						<input name="profile_image[]" type="file" />
						<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />';						
					break;
					
				case 8:
					print '
						<div id="profile_heading">Beloning</div>
						<div id="profile_label">Ik wil graag de volgende beloning van particulieren:</div>';
						print_rewards("individuals");
					print '
						<div id="profile_label">Ik wil graag de volgende beloning van scholen:</div>';
						print_rewards("schools");
					print '
						<div id="profile_label">Ik wil graag de volgende beloning van bedrijven:</div>';
						print_rewards("companies");
					break;
					
				case 7:
					print '
						<div id="profile_heading">Onderwerpen</div>
						<div id="profile_label">Ik bied ondersteuning voor wat betreft de volgende onderwerpen:</div>';
						print_actions();
					break;
					
				case 6:
					print '
						<div id="profile_heading">Bureaublad-omgevingen</div>
						<div id="profile_label">Ik bied ondersteuning voor de volgende bureaublad-omgevingen:</div>';
						print_desktops();
					break;
					
				case 5:
					print '
						<div id="profile_heading">Distributies</div>
						<div id="profile_label">Ik bied ondersteuning voor de volgende Linux-distributies:</div>';
						print_distros();
					break;
					
				case 4:
					print '
						<div id="profile_heading">Inloggegevens</div>
						<div id="profile_label">Gebruikersnaam:</div>
						<input type="text" id="profile_inputbox" name="username" maxlength="50" value="'.$_SESSION['wizard']['new_profile']['username'].'"/>
						<div id="profile_label">Wachtwoord:</div>
						<input type="password" id="profile_inputbox" name="password" maxlength="32"/>
					';
					break;
					
				case 3:					
					print '
						<div id="profile_heading">Gebruikersgegevens</div>
						<div id="profile_label">Gegevens die <b>vetgedrukt</b> zijn weergegeven zullen openbaar te zien zijn op de Buurtlinux-website. Alle velden moeten worden ingevuld, tenzij anders aangegeven.</div>
						<div id="profile_label"><b>Voornaam:</b></div>
						<input type="text" id="profile_inputbox" name="firstname" maxlength="200" value="'.$_SESSION['wizard']['new_profile']['firstname'].'" />
						<div id="profile_label"><b>Achternaam:</b></div>
						<input type="text" id="profile_inputbox" name="lastname" maxlength="200" value="'.$_SESSION['wizard']['new_profile']['lastname'].'" />';
						
					if($_SESSION['wizard']['new_profile']['group'] == "Bedrijf") {
						print '
							<div id="profile_label"><b>Bedrijfsnaam:</b></div>
							<input type="text" id="profile_inputbox" name="companyname" maxlength="200" value="'.$_SESSION['wizard']['new_profile']['companyname'].'"/>
							<div id="profile_label"><b>Straat:</b></div>
							<input type="text" id="profile_inputbox" name="street" maxlength="200" value="'.$_SESSION['wizard']['new_profile']['street'].'" />
							<div id="profile_label"><b>Nummer:</b></div>
							<input type="text" id="profile_inputbox" name="number" maxlength="10" value="'.$_SESSION['wizard']['new_profile']['number'].'" />
							<div id="profile_label"><b>Postcode:</b></div>
							<input type="text" id="profile_inputbox" name="zip" maxlength="10" value="'.$_SESSION['wizard']['new_profile']['zip'].'" />
							<div id="profile_label"><b>Plaats:</b></div>
							<input type="text" id="profile_inputbox" name="city" maxlength="200" value="'.$_SESSION['wizard']['new_profile']['city'].'" />
							<div id="profile_label"><b>1e telefoonnummer:</b></div>
							<input type="text" id="profile_inputbox" name="phone1" maxlength="20" value="'.$_SESSION['wizard']['new_profile']['phone1'].'" />
							<div id="profile_label">(optioneel)</div>
							<div id="profile_label"><b>2e telefoonnummer:</b></div>
							<input type="text" id="profile_inputbox" name="phone2" maxlength="20" value="'.$_SESSION['wizard']['new_profile']['phone2'].'" />
							<div id="profile_label">(optioneel)</div>';
					}
					else {
						print '
							<div id="profile_label">Postcode:</div>
							<input type="text" id="profile_inputbox" name="zip" maxlength="4" value="'.$_SESSION['wizard']['new_profile']['zip'].'" />
							<div id="profile_label">(Alleen de eerste 4 cijfers)</div>
							<div id="profile_label"><b>Plaats:</b></div>
							<input type="text" id="profile_inputbox" name="city" maxlength="200" value="'.$_SESSION['wizard']['new_profile']['city'].'" />';
					}
					
					print '
						<div id="profile_label">Email:</div>
						<input type="text" id="profile_inputbox" name="email" maxlength="200" value="'.$_SESSION['wizard']['new_profile']['email'].'" />
						<div id="profile_label"><b>Website:</b></div>
						<input type="text" id="profile_inputbox" name="website" value="http://" maxlength="200" value="'.$_SESSION['wizard']['new_profile']['website'].'" />
						<div id="profile_label"><b>Aanvullende informatie:</b> <input type="text" name="counter" maxlength="3" size="3" value="1000"onblur="textCounter(this.form.counter,this, 1000);"> tekens over</div>
						<textarea type="text" id="profile_textarea" name="info" onkeypress="textCounter(this,this.form.counter,1000);">'.$_SESSION['wizard']['new_profile']['info'].'</textarea>';
					break;
					
				case 2:
					print '	<div id="profile_heading">Doelgroep</div>
							<div id="profile_label">Ik verleen hulp aan:</div>';
					print_targets();
					break;
					
				case 1:
				default:
					print '	<div id="profile_heading">Hulpgroep</div>
							<div id="profile_label">Ik ben een:</div>';
					print_groups();
					break;
			}
				
			print '<div id="profile_nav_bot">';				
					if ( $_SESSION['wizard']['step'] == $_SESSION['wizard']['step_max'] )
					{
						// If there's a user, he/she is already logged in, so this is not a new profile
						if ( isset($_SESSION['user'] ) )
						{
							print '<input id="profile_button" name="btn_save" value="Opslaan" type="submit" />';
						}
						else
						{
							print '<input id="profile_button" name="btn_register" value="Registreren" type="submit" />';
						}
					}
					else
					{
						print '<input id="profile_button" name="btn_next" value="Verder" type="submit" />';
					}
					if (  $_SESSION['wizard']['step'] > 1 )
					{
						print '<input id="profile_button" name="btn_prev" value="Vorige" type="submit" />';
					}
				
			print '</div></form>';
		}
	?>
	</body>
</html>
