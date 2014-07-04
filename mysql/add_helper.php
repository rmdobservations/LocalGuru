<?php

/***************************************************************************

Generates the HTML-form with which new Buurtlinux-helper can be registered.
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

	// I want to use session variables
	session_start();

	require_once dirname(__DIR__).'/lib/init.php';
	require_once LIBDIR.'/db_lookup_criteria.php';
	
	function process_form_data() {
		$l_result = false;

		/* First create a user object with all known information */		
		if( isset($_POST['frm_firstname']) )					$l_user['firstname']	= $_POST['frm_firstname']; 				else $l_user['firstname']="";
		if( isset($_POST['frm_lastname']) )						$l_user['lastname'] 	= $_POST['frm_lastname']; 				else $l_user['lastname']="";
		if( isset($_POST['frm_companyname']) ) 					$l_user['companyname'] 	= $_POST['frm_companyname']; 			else $l_user['companyname']="";
		if( isset($_POST['frm_street']) )						$l_user['street']		= $_POST['frm_street'];					else $l_user['street']="";
		if( isset($_POST['frm_number']) )						$l_user['number']		= $_POST['frm_number'];					else $l_user['number']="";
		if( isset($_POST['frm_zip']) )							$l_user['zip']			= $_POST['frm_zip'];					else $l_user['zip']="";
		if( isset($_POST['frm_city']) )							$l_user['city']			= $_POST['frm_city'];					else $l_user['city']="";
		if( isset($_POST['frm_province']) )						$l_user['province']		= $_POST['frm_province'];				else $l_user['province']="";
		if( isset($_POST['frm_country']) )						$l_user['country']		= $_POST['frm_country'];				else $l_user['country']="NL";
		if( isset($_POST['frm_phone1']) )						$l_user['phone1']		= $_POST['frm_phone1'];					else $l_user['phone1']="";
		if( isset($_POST['frm_phone2']) )						$l_user['phone2']		= $_POST['frm_phone2'];					else $l_user['phone2']="";
		if( isset($_POST['frm_website']) )						$l_user['website']		= $_POST['frm_website'];				else $l_user['website']="http://";
		if( isset($_POST['frm_email']) )						$l_user['email']		= $_POST['frm_email'];					else $l_user['email']="";
		if( isset($_POST['frm_info']) )							$l_user['info']			= $_POST['frm_info'];					else $l_user['info']="";
		if( isset($_POST['frm_loc_lat']) )						$l_user['loc_lat']		= $_POST['frm_loc_lat'];				else $l_user['loc_lat']="";
		if( isset($_POST['frm_loc_lon']) )						$l_user['loc_lon']		= $_POST['frm_loc_lon'];				else $l_user['loc_lon']="";
		
		/* Collect all user's tags */
		if( isset($_POST['frm_distros']) )						$l_user['distros']		= $_POST['frm_distros'];				else $l_user['distros']="";
		if( isset($_POST['frm_desktops']) )						$l_user['desktops']		= $_POST['frm_desktops'];				else $l_user['desktops']="";
		if( isset($_POST['frm_actions']) )						$l_user['actions']		= $_POST['frm_actions'];				else $l_user['actions']="";
		if( isset($_POST['frm_groups']) )						$l_user['groups']		= $_POST['frm_groups'];					else $l_user['groups']="";
		if( isset($_POST['frm_targets']) )						$l_user['targets']		= $_POST['frm_targets'];				else $l_user['targets']="";
		if( isset($_POST['frm_Bedrijven_rewards']) )			$l_user['rewards_com']	= $_POST['frm_Bedrijven_rewards'];		else $l_user['rewards_com']="";
		if( isset($_POST['frm_Particulieren_rewards']) )		$l_user['rewards_ind']	= $_POST['frm_Particulieren_rewards'];	else $l_user['rewards_ind']="";
		if( isset($_POST['frm_Scholen_rewards']) )				$l_user['rewards_sch']	= $_POST['frm_Scholen_rewards'];		else $l_user['rewards_sch']="";

		$_SESSION['user'] = $l_user;

		if( isset( $_POST['frm_add_another'] ) && ( $_POST['frm_add_another'] == "Nieuwe gebruiker toevoegen" ) )
		{
			// Clear flag to allow new 'add linux-helper'-form
			unset( $_SESSION['add_done'] );			
		}		
		else if( isset( $_POST['frm_add'] ) && ( $_POST['frm_add'] == "Toevoegen" ) && isset($_POST['frm_pass']) && ($_POST['frm_pass']==ADD_HELPER_PASSWORD))
		{
			// Check if user doesn't already exist
			require_once('../lib/db_lookup_users.php');
			$l_lookup = new UsersLookup();
			$l_existing_user = $l_lookup->get_user_byemail( $l_user['email'] );
		
			// Only proceed when user doesn't already exist
			if( isset($_POST['frm_email']) && !is_array($l_existing_user) )
			{
				// Then create a UsersSave instance to store user with a temporary username and password
				require_once('../lib/db_save_users.php');
				$l_usersave = new UsersSave();

				// Auto-generate and add missing parameters
				$l_user['username'] = $l_user['email'];
				$l_user['password'] = $l_usersave->generate_random_string();
				$l_user_id = $l_usersave->save_new_user( $l_user );
			
				require_once('../lib/db_save_criteria.php');
				$l_criteriasave = new CriteriaSave();
				$l_criterialookup = new CriteriaLookup();

				// Tag each of the relevant criteria to the new user's ID
				if( is_array($l_user['distros']) )
				{
					foreach( $l_user['distros'] as $l_name )
					{
						$l_id = $l_criterialookup->distro_exists( $l_name ) ;
						$l_criteriasave->save_tag_usr_to_distro( $l_user_id, $l_id );
					}
				}

				if( is_array($l_user['desktops']) )
				{
					foreach( $l_user['desktops'] as $l_name )
					{
						$l_id = $l_criterialookup->desktop_exists( $l_name ) ;
						$l_criteriasave->save_tag_usr_to_desktop( $l_user_id, $l_id );
					}
				}

				if( is_array($l_user['actions']) )
				{
					foreach( $l_user['actions'] as $l_name )
					{
						$l_id = $l_criterialookup->action_exists( $l_name ) ;					
						$l_criteriasave->save_tag_usr_to_action( $l_user_id, $l_id );
					}
				}

				if( is_array($l_user['groups']) )
				{
					foreach( $l_user['groups'] as $l_name )
					{
						$l_id = $l_criterialookup->group_exists( $l_name ) ;					
						$l_criteriasave->save_tag_usr_to_group( $l_user_id, $l_id );
					}
				}

				if( is_array($l_user['targets']) )
				{
					foreach( $l_user['targets'] as $l_name )
					{
						$l_id = $l_criterialookup->target_exists( $l_name ) ;
						$l_criteriasave->save_tag_usr_to_target( $l_user_id, $l_id );
					}
				}

				$l_id = $l_criterialookup->reward_exists( $l_user['rewards_ind'] ) ;			
				$l_target_id = $l_criterialookup->target_exists( "Particulieren" ) ;
				$l_criteriasave->save_tag_usr_to_reward( $l_user_id, $l_id, $l_target_id  );
			
				$l_id = $l_criterialookup->reward_exists( $l_user['rewards_com'] ) ;			
				$l_target_id = $l_criterialookup->target_exists( "Bedrijven" ) ;
				$l_criteriasave->save_tag_usr_to_reward( $l_user_id, $l_id, $l_target_id  );
			
				$l_id = $l_criterialookup->reward_exists( $l_user['rewards_sch'] ) ;
				$l_target_id = $l_criterialookup->target_exists( "Scholen" ) ;
				$l_criteriasave->save_tag_usr_to_reward( $l_user_id, $l_id, $l_target_id  );
			}

			$l_result = true;
		}

		return $l_result;
	}

	
	// All [insert_*]-functions below take care of constructing and returning
	// HTML-formatted form-element(s) for a criterium '*'.
	function insert_distros( $a_tags )
	{
		$l_db_access = new CriteriaLookup();
		$l_criteria = $l_db_access->get_distros_criteria("");
		
		if ( count( $l_criteria ) )
		{	
			$l_html = '<div>';

			// Check for active search criteria	
			foreach ( $l_criteria as $l_pair )
			{
				$l_checked = "";
				if( is_array($a_tags) )
				{
					foreach( $a_tags as $l_item )
					{
						if ( $l_pair['name'] == $l_item )
						{
							$l_checked = 'checked="yes"';
							break 1;
						}
					}
				}
				$l_html .= '<input type=checkbox '.$l_checked.' name="frm_distros[]" value="'.$l_pair['name'].'">'.$l_pair['name'].'</input><br>';
			}
			$l_html .= '<div>&nbsp;</div></div>';
		}		
		return $l_html;
	}
	

	function insert_desktops( $a_tags  )
	{
		$l_db_access = new CriteriaLookup();
		$l_criteria = $l_db_access->get_desktops_criteria("");		
		
		if ( count( $l_criteria ) )
		{
			$l_html =  '<div>';

			// Check for active search criteria
			foreach ( $l_criteria as $l_pair )
			{
				$l_checked = "";
				if( is_array($a_tags) )
				{
					foreach( $a_tags as $l_item )
					{
						if ( $l_pair['name'] == $l_item )
						{
							$l_checked = 'checked="yes"';
							break 1;
						}
					}
				}
				$l_html .= '<input id="map_search_pane_checkbox" type=checkbox '.$l_checked.' name="frm_desktops[]" value="'.$l_pair['name'].'">'.$l_pair['name'].'</input><br>';
			}
			$l_html .= '<div>&nbsp;</div></div>';
		}
		
		return $l_html;
	}
	

	function insert_actions( $a_tags  )
	{
		$l_db_access = new CriteriaLookup();
		$l_criteria = $l_db_access->get_actions_criteria("");
		
		if ( count( $l_criteria ) )
		{
			$l_html =  '<div>';

			// Check for active search criteria
			foreach ( $l_criteria as $l_pair )
			{
				$l_checked = "";
				if( is_array($a_tags) )
				{
					foreach( $a_tags as $l_item )
					{
						if ( $l_pair['name'] == $l_item )
						{
							$l_checked = 'checked="yes"';
							break 1;
						}
					}
				}
				$l_html .= '<input type="checkbox" '.$l_checked.' name="frm_actions[]" value="'.$l_pair['name'].'">'.$l_pair['name'].'</input><br>';
			}
			$l_html .= '<div>&nbsp;</div></div>';
		}
		
		return $l_html;
	}

	
	function insert_groups( $a_categories )
	{
		$l_db_access = new CriteriaLookup();
		$l_criteria = $l_db_access->get_usergroups_criteria("");
		
		if ( count( $l_criteria ) )
		{
			$l_html =  '<div>';

			$l_html .= '<select name="frm_groups[]">';
			$l_html .= '<option value="">Wie dan ook</option>';

			// Check for active search criteria
			foreach ( $l_criteria as $l_pair )
			{
				$l_checked = "";

				if( is_array($a_categories) )
				{
					foreach( $a_categories as $l_item )
					{
						if ( $l_pair['name'] == $l_item )
						{
							$l_checked = 'selected';
							break 1;
						}
					}
				}
				$l_html .= '<option '.$l_checked .' value="'.$l_pair['name'].'">'.$l_pair['name'].'</option>';
			}
			$l_html .= '</select><div>&nbsp;</div></div>';
		}		
		return $l_html;
	}

	
	function insert_targetgrps( $a_categories )
	{
		$l_db_access = new CriteriaLookup();
		$l_criteria = $l_db_access->get_targetgroups_criteria("");
		
		if ( count( $l_criteria ) )
		{
			$l_html =  '<div>';

			// Check for active search criteria
			foreach ( $l_criteria as $l_pair )
			{
				$l_checked = "";
				if( is_array($a_categories) )
				{
					foreach( $a_categories as $l_item )
					{
						if ( $l_pair['name'] == $l_item )
						{
							$l_checked = 'checked="yes"';
							break 1;
						}
					}
				}
				$l_html .= '<input type="checkbox" '.$l_checked.' name="frm_targets[]" value="'.$l_pair['name'].'">'.$l_pair['name'].'</input><br>';
			}
			$l_html .= '<div>&nbsp;</div></div>';
		}
		
		return $l_html; 
	}
	
	function insert_rewards()
	{
		$l_db_access = new CriteriaLookup();
		$l_criteria = $l_db_access->get_rewards_criteria("");
		$l_targets = $l_db_access->get_targetgroups_criteria("");
		$l_html = "";
		
		if( count($l_targets) )
		{
			foreach( $l_targets as $l_target )
			{
				$l_html .= "<div>Beloning voor doelgroep: ".$l_target['name']."</div>";
				if ( count( $l_criteria ) )
				{
					$l_html .= '<select name="frm_'.$l_target['name'].'_rewards">';
					$l_html .=  '<div>';
					foreach ( $l_criteria as $l_pair )
					{
						$l_selected = "";
						if( $l_pair['id'] == 6 ) $l_selected = "selected=selected";
						$l_html .= '<option '.$l_selected .' value="'.$l_pair['name'].'">'.$l_pair['name'].'</option>';
					}
					$l_html .= '</select></div>';
				}
				$l_html .= "<br>";
			}
		}
		return $l_html;
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>
			Buurtlinux - Nieuwe Linux-hulp toevoegen
		</title>
		<!-- Meta-tags -->
		<meta charset="UTF-8">		
	</head>

	<?php flush(); ?>

	<body>		
		<?php						
			if( process_form_data() )
			{
				print "Gebruiker is toegevoegd";
				$_SESSION['add_done'] = "DONE";
				print '<form name="add_helper" method="post">
						<input type="submit" name="frm_add_another" value="Nieuwe gebruiker toevoegen" />
				</form>';
			}
			else
			{
				$l_user = $_SESSION['user'];

				print '<h1>Voeg een nieuwe Linux-hulp toe</h1>
						<form name="add_helper" method="post">
						<div>Voornaam:</div>						
						<div><input size="30" type="text" name="frm_firstname" value="'.$l_user['firstname'].'" /></div><br>
						<div>Achternaam:</div>
						<div><input size="30" type="text" name="frm_lastname" value="'.$l_user['lastname'].'" /></div><br>
						<div>Bedrijfsnaam:</div>
						<div><input size="30" type="text" name="frm_companyname" value="'.$l_user['companyname'].'" /></div><br>
						<div>Straat:</div>
						<div><input size="30" type="text" name="frm_street" value="'.$l_user['street'].'" /></div><br>
						<div>Huisnummer</div>
						<div><input size="30" type="text" name="frm_number" value="'.$l_user['number'].'" /></div><br>
						<div>Postcode:</div>
						<div><input size="30" type="text" name="frm_zip" value="'.$l_user['zip'].'" /></div><br>
						<div>Stad:</div>
						<div><input size="30" type="text" name="frm_city" value="'.$l_user['city'].'" /></div><br>
						<div>Provincie:</div>
						<div><input size="30" type="text" name="frm_province" value="'.$l_user['province'].'" /></div><br>
						<div>Landcode:</div>
						<div><input size="30" type="text" name="frm_country" value="'.$l_user['country'].'" /></div><br>
						<div>Tel 1:</div>
						<div><input size="30" type="text" name="frm_phone1" value="'.$l_user['phone1'].'" /></div><br>
						<div>Tel 2:</div>
						<div><input size="30" type="text" name="frm_phone2" value="'.$l_user['phone2'].'" /></div><br>
						<div>Website:</div>
						<div><input size="30" type="text" name="frm_website" value="'.$l_user['website'].'" /></div><br>
						<div>Email:</div>
						<div><input size="30" type="text" name="frm_email" value="'.$l_user['email'].'" /></div><br>
						<div>Aanvullende informatie:</div>
						<div><textarea cols="115" rows="10" name="frm_info">'.$l_user['info'].'</textarea></div><br>
						<div>Locatie (lat, lon):</div>
						<div><input size="30" type="text" name="frm_loc_lat" value="'.$l_user['loc_lat'].'" /> </div>
						<div><input size="30" type="text" name="frm_loc_lon" value="'.$l_user['loc_lon'].'" /> </div>
						</div>Linux-distributies:</div>
						'.insert_distros( $l_user['distros'] ).'<br>
						</div>Bureaublad-omgevingen:</div>
						'.insert_desktops( $l_user['desktops'] ).'<br>					
						</div>Onderwerpen:
						'.insert_actions( $l_user['actions'] ).'<br>
						</div>Groep:</div>
						'.insert_groups( $l_user['groups'] ).'<br>
						</div>Doelgroep:</div>
						'.insert_targetgrps( $l_user['targets'] ).'<br>
						</div>Beloning:</div>
						'.insert_rewards().'<br>
						<div>Supersecret wachtwoord:</div>
						<div><input size="30" type="password" name="frm_pass"/></div><br>
						<input type="submit" name="frm_add" value="Toevoegen" />
				</form>';
			}
		?>
	</body>
</html>
