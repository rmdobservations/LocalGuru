<?php

/***************************************************************************

Reads user data from the Buurtlinux database.
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

require_once('db_access.php');

// This class forms the interface to the tables that contain the user data.
class UsersLookup {	

	// Creates a UserLookup instance
	public function __construct()
	{
		// do nothing...
	}


	// Looks up user data based on first- and lastname. Returns an array of 
	// records of which each record contains data for a user with the given 
	// firstname and lastname. Normally one expects just one record to be 
	// returned, since multiple users with the same first- and lastname is rare.
	// If no database-connection could be made NULL is returned. If no users are
	// found FALSE is returned.
	public function get_users_byname( $a_firstname, $a_lastname )
	{
		// We need a database connection for this.
		$l_db = new Database();
		if ( !$l_db->connect() )
		{						
			return NULL;
		}
		
		// Construct the MySQL search query and fire!
		$l_users = array();
		if ( ($l_result = $l_db->connection()->query( 'select * from users where usr_firstname="'.$a_firstname.'" and usr_lastname="'.$a_lastname.'" order by usr_lastname;' )) && ($l_result->num_rows > 0) )
		{			
			while ( $l_record = mysqli_fetch_assoc($l_result) )
			{
				// Copy
				$l_user['id'] 			= $l_record['id'];
				$l_user['username'] 	= $l_record['usr_username'];
				$l_user['password']		= $l_record['usr_password'];
				$l_user['firstname']	= $l_record['usr_firstname'];
				$l_user['lastname']		= $l_record['usr_lastname'];
				$l_user['companyname']	= $l_record['usr_companyname'];
				$l_user['street']		= $l_record['usr_street'];
				$l_user['number']		= $l_record['usr_number'];
				$l_user['zip']			= $l_record['usr_zip'];
				$l_user['city']			= $l_record['usr_city'];
				$l_user['country']		= $l_record['usr_country_code'];
				$l_user['phone_1st']	= $l_record['usr_phone_1st'];
				$l_user['phone_2nd']	= $l_record['usr_phone_2nd'];
				$l_user['website']		= $l_record['usr_website_url'];
				$l_user['email']		= $l_record['usr_email'];
				$l_user['info']			= $l_record['usr_info'];
				$l_user['loc_lat']		= $l_record['usr_loc_lat'];
				$l_user['loc_lon']		= $l_record['usr_loc_lon'];

				// Add user data record to users array [$l_users].
				$l_users[] 				= $l_user;
			}
			
			// Destroy results
			$l_result->close();
		}
		
		// Destroy database connection
		$l_db->disconnect();

		// Return FALSE if unsuccessful
		if( !isset($l_users) ) return FALSE;

		// Return array of one (or more) user(s)
		return $l_users;
	}
		

	// Looks up user data based on email-address. Returns a records of user-data
	// If no database-connection could be made NULL is returned. If no users are
	// found FALSE is returned.
	public function get_user_byemail( $a_email )
	{
		// We need a database connection for this.
		$l_db = new Database();
		if ( !$l_db->connect() )
		{
			return NULL;
		}

		// Construct the MySQL search query and fire!
		if ( ($l_result = $l_db->connection()->query( 'select * from users where usr_email="'.$a_email.'";' )) && ($l_result->num_rows == 1) )
		{
			while ( $l_record = mysqli_fetch_assoc($l_result) )
			{
				// Copy
				$l_user['id'] 			= $l_record['id'];
				$l_user['username'] 	= $l_record['usr_username'];
				$l_user['password']		= $l_record['usr_password'];
				$l_user['firstname']	= $l_record['usr_firstname'];
				$l_user['lastname']		= $l_record['usr_lastname'];
				$l_user['companyname']	= $l_record['usr_companyname'];
				$l_user['street']		= $l_record['usr_street'];
				$l_user['number']		= $l_record['usr_number'];
				$l_user['zip']			= $l_record['usr_zip'];
				$l_user['city']			= $l_record['usr_city'];
				$l_user['country']		= $l_record['usr_country_code'];
				$l_user['phone_1st']	= $l_record['usr_phone_1st'];
				$l_user['phone_2nd']	= $l_record['usr_phone_2nd'];
				$l_user['website']		= $l_record['usr_website_url'];
				$l_user['email']		= $l_record['usr_email'];
				$l_user['info']			= $l_record['usr_info'];
				$l_user['loc_lat']		= $l_record['usr_loc_lat'];
				$l_user['loc_lon']		= $l_record['usr_loc_lon'];
			}			
			
			// Destroy results
			$l_result->close();
		}

		// Destroy database connection
		$l_db->disconnect();

		// Return FALSE if unsuccessful
		if( !isset($l_user) ) return FALSE;

		// Return single user record
		return $l_user;
	}
	

	// Looks up user data based on given username and MD5'd password. Returns
	// record of user data if successful. Returns NULL if database connection
	// failed and FALSE if no user could be found.
	public function get_user_bylogin( $a_username, $a_password_md5 )
	{
		// We need a database connection
		$l_db = new Database();
		if ( !$l_db->connect() )
		{
			return NULL;
		}
				
		// Construct MySQL query and fire!
		if ( ($l_result = $l_db->connection()->query( 'select * from users where usr_username="'.$a_username.'" and usr_password="'.$a_password_md5.'" order by usr_lastname;' )) && ($l_result->num_rows == 1) )
		{
			while ( $l_record = mysqli_fetch_assoc($l_result) )
			{
				$l_user['id'] 			= $l_record['id'];
				$l_user['username'] 	= $l_record['usr_username'];
				$l_user['password']		= $l_record['usr_password'];
				$l_user['firstname']	= $l_record['usr_firstname'];
				$l_user['lastname']		= $l_record['usr_lastname'];
				$l_user['companyname']	= $l_record['usr_companyname'];
				$l_user['street']		= $l_record['usr_street'];
				$l_user['number']		= $l_record['usr_number'];
				$l_user['zip']			= $l_record['usr_zip'];
				$l_user['city']			= $l_record['usr_city'];
				$l_user['country']		= $l_record['usr_country_code'];
				$l_user['phone_1st']	= $l_record['usr_phone_1st'];
				$l_user['phone_2nd']	= $l_record['usr_phone_2nd'];
				$l_user['website']		= $l_record['usr_website_url'];
				$l_user['email']		= $l_record['usr_email'];
				$l_user['info']			= $l_record['usr_info'];
				$l_user['loc_lat']		= $l_record['usr_loc_lat'];
				$l_user['loc_lon']		= $l_record['usr_loc_lon'];
			}
			
			// Destroy results
			$l_result->close();
		}

		// Destroy database connection
		$l_db->disconnect();
		
		// Return FALSE if unsuccessful
		if( !isset($l_user) ) return FALSE;

		// Return single user record
		return $l_user;
	}
	

	// Checks if given username	 [$a_username] is not yet used in the system. 
	// Returns TRUE if username is not present in the system yet and can be used
	// or returns FALSE otherwise.
	public function username_avail( $a_username )
	{
		// We need a database connection
		$l_result = true;
		$l_db = new Database();
		if ( !$l_db->connect() )
		{
			return false;
		}
		
		// Construct MySQL-query and fire!
		if ( ($l_result = $l_db->connection()->query( 'select * from users where usr_username="'.$a_username.'";' )) && ($l_result->num_rows > 0) )
		{
			$l_result = false;			
			// Destroy results
			$l_result->close();
		}

		// Destroy database connection
		$l_db->disconnect();

		return $l_result;
	}	
	

	// Selects users from the database, based on the given search criteria. Each
	// criterium is an array with 0 or more items.
	public function get_users_bytags( $a_actions, $a_distros, $a_desktops, $a_groups, $a_targets, $a_rewards )
	{
		$l_userids_all 		= array();
		$l_userids 			= array();
		$l_userids_actions 	= array();
		$l_userids_distros 	= array();
		$l_userids_desktops = array();
		$l_userids_groups 	= array();
		$l_userids_targets 	= array();
		$l_userids_rewards 	= array();
		$l_users 			= array();
		
		// First we want the complete collection of users, if there are any	
		$l_userids_all = $this->get_all_ids_of( "users" );
		if( !count($l_userids_all) )
		{
			// Return empty collection of users.
			return $l_users;
		}
		
		// Now filter, starting off with the complete collection of user-ID's
		$l_userids = $l_userids_all;

		if( count($a_actions) )
		{
			// Obtain all user-ID's that fall within the search criteria.
			$l_userids_actions 	= $this->get_userids_from_criteria( "actions", $a_actions );
			// Remove ID's from [$l_userids] that are not in [$l_userids_actions].
			$l_userids = $this->compare_ids( $l_userids, $l_userids_actions );
		}

		if( count($a_distros) )
		{
			// Obtain all user-ID's that fall within the search criteria.			
			$l_userids_distros 	= $this->get_userids_from_criteria( "distros", $a_distros );
			// Remove ID's from [$l_userids] that are not in [$l_userids_actions].
			$l_userids = $this->compare_ids( $l_userids, $l_userids_distros );
		}
		
		if( count($a_desktops) )
		{
			// Obtain all user-ID's that fall within the search criteria.			
			$l_userids_desktops 	= $this->get_userids_from_criteria( "dtenvs", $a_desktops );
			// Remove ID's from [$l_userids] that are not in [$l_userids_actions].
			$l_userids = $this->compare_ids( $l_userids, $l_userids_desktops );
		}
		
		if( count($a_groups) )
		{
			// Obtain all user-ID's that fall within the search criteria.
			$l_userids_groups 	= $this->get_userids_from_criteria( "usergroups", $a_groups );
			// Remove ID's from [$l_userids] that are not in [$l_userids_actions].
			$l_userids = $this->compare_ids( $l_userids, $l_userids_groups );
		}
		
		if( count($a_targets) )
		{
			// Obtain all user-ID's that fall within the search criteria.
			$l_userids_targets 	= $this->get_userids_from_criteria( "targetgroups", $a_targets );
			// Remove ID's from [$l_userids] that are not in [$l_userids_actions].
			$l_userids = $this->compare_ids( $l_userids, $l_userids_targets );
		}
		
		if( count($a_rewards) )
		{
			// Obtain all user-ID's that fall within the search criteria.
			$l_userids_rewards 	= $this->get_userids_from_criteria( "rewards", $a_rewards );
			// Remove ID's from [$l_userids] that are not in [$l_userids_actions].
			$l_userids = $this->compare_ids( $l_userids, $l_userids_rewards );
		}
				
		// Lookup users matching the resulting userid's		
		if (count($l_userids) )
		{		
			foreach ($l_userids as $l_id)
			{
				// Keep black-listed users off the map
				if( !$this->is_blacklisted( $l_id ) )
				{
					$l_users[] = $this->get_user_byid( $l_id );
				}
			}
		}
		return $l_users;
	}
	

	// Removes ID's from the given set [$a_set] if they don't appear in the 
	// given subset [$a_subset].
	protected function compare_ids( $a_set, $a_subset )
	{		
		$l_result = array();

		// Filter the set collection		
		foreach ( $a_set as $l_id )
		{
			if ( in_array( $l_id, $a_subset, true ) )
			{
				// User may stay
				$l_result[] = $l_id;
			}
		}

		return $l_result;
	}

	
	// Retrieves and returns an array of user-id's which are tagged to the 
	// criteria [$a_criteria] (given in human-readible format) in the given 
	// table [$a_table].
	protected function get_userids_from_criteria( $a_table, $a_criteria )
	{
		// Translate human-readible criteria tags into id's
		$l_criteria_ids = array();
		$l_criteria_ids = $this->get_ids( $a_table, $a_criteria );
		
		$l_userids = array();
		$l_userids_all = array();
		if ( count( $l_criteria_ids ) )
		{
			// Collect the total user-ID's tagged to one or more of the
			//  criterium-ID's.
			foreach( $l_criteria_ids as $l_id )
			{
				$l_userids = $this->get_userids_by_tagid( $a_table, $l_id );				
				$l_userids_all = array_merge($l_userids_all, $l_userids);
			}
		}

		// Make sure that no user-ID is present more than once.
		$l_userids_all = array_unique( $l_userids_all );
		return $l_userids_all;
	}
	
	
	// Returns TRUE if user is blacklisted (in other words: user requests to not 
	// be shown on the map for a while) or FALSE if not. Returns NULL if 
	// database connection failed.
	public function is_blacklisted( $a_id )
	{
		$l_found = FALSE;

		// We need a database connection		
		$l_db = new Database();
		if ( !$l_db->connect() )
		{
			return NULL;
		}		
		
		// Check if ID is found
		if( ($l_result = $l_db->connection()->query( 'select * from users_off_radar where user_id="'.$a_id.'";' )) && ($l_result->num_rows > 0) )
		{
			$l_found = TRUE;
			
			/* Destroy results */
			$l_result->close();
		}		

		/* Destroy database connection */		
		$l_db->disconnect();
		
		return $l_found;		
	}

	

	// Obtains and returns user data based on given user-ID [$a_id]. Returns 
	// FALSE if no user was found or NULL if database connection fails.
	public function get_user_byid( $a_id )
	{
		// We need a database connection
		$l_db = new Database();
		if ( !$l_db->connect() )
		{
			return NULL;
		}
				
		// Setup query and fire
		if ( ($l_result = $l_db->connection()->query( 'select * from users where id='.$a_id.';' )) && ($l_result->num_rows > 0) )
		{
			while ( $l_record = mysqli_fetch_assoc($l_result) )
			{
				$l_user['id'] 			= $l_record['id'];
				$l_user['username']		= $l_record['usr_username'];
				$l_user['password']		= $l_record['usr_password'];
				$l_user['firstname']	= $l_record['usr_firstname'];
				$l_user['lastname']		= $l_record['usr_lastname'];
				$l_user['companyname']	= $l_record['usr_companyname'];
				$l_user['street']		= $l_record['usr_street'];
				$l_user['number']		= $l_record['usr_number'];
				$l_user['zip']			= $l_record['usr_zip'];
				$l_user['city']			= $l_record['usr_city'];
				$l_user['country']		= $l_record['usr_country_code'];
				$l_user['phone_1st']	= $l_record['usr_phone_1st'];
				$l_user['phone_2nd']	= $l_record['usr_phone_2nd'];
				$l_user['website']		= $l_record['usr_website_url'];
				$l_user['email']		= $l_record['usr_email'];
				$l_user['info']			= $l_record['usr_info'];
				$l_user['loc_lat']		= $l_record['usr_loc_lat'];
				$l_user['loc_lon']		= $l_record['usr_loc_lon'];
			}
			
			// Destroy results
			$l_result->close();
		}

		// Destroy database connection
		$l_db->disconnect();

		// Return FALSE if unsuccessful
		if( !isset($l_user) ) return FALSE;

		return $l_user;
	}
	

	// Returns an array of ID's corresponding to the given names [$a_array] 
	// found in the given table [$a_table].
	protected function get_ids( $a_table, $a_array )
	{
		$l_ids = array();
		if ( count($a_array) )
		{
			foreach ( $a_array as $l_name )
			{
				$l_id = $this->get_id_of( $a_table, $l_name );
				if( isset($l_id) ) $l_ids[] = $l_id;
			}
		}
		return $l_ids;
	}
	

	// Returns single ID corresponding to the given name [$a_name] found in the 
	// given table [$a_table].
	protected function get_id_of( $a_table, $a_name ) {
		// We need a database connection
		$l_db = new Database();
		if ( !$l_db->connect() )
		{
			return NULL;
		}		
		
		// Setup query and fire!
		if ( ($l_result = $l_db->connection()->query( 'select id from '.$a_table.' where name="'.$a_name.'";' )) && ($l_result->num_rows > 0) )
		{
			while ( $l_record = mysqli_fetch_assoc($l_result) )
			{
				$l_id = $l_record['id'];
			}
			
			// Destroy results
			$l_result->close();
		}

		// Destroy database connection
		$l_db->disconnect();
		return $l_id;
	}

	
	// Returns all ID's found in the given table [$a_table] (make sure table
	// actually has an id-field)
	protected function get_all_ids_of( $a_table )
	{
		// We need a database connection
		$l_db = new Database();
		if ( !$l_db->connect() )
		{
			return NULL;
		}
		
		$l_ids = array();
		// Setup query and fire!
		if ( ($l_result = $l_db->connection()->query( 'select id from '.$a_table.';' )) && ($l_result->num_rows > 0) )
		{
			// Collect ID's
			while ( $l_record = mysqli_fetch_assoc($l_result) )
			{
				$l_ids[] = $l_record['id'];
			}
			
			// Destroy results
			$l_result->close();
		}

		// Destroy database connection
		$l_db->disconnect();
		return $l_ids;
	}
	

	// Returns an array of user-ID's that match the given tag-ID [$a_id] within
	// the given table [$a_table]
	protected function get_userids_by_tagid( $a_table, $a_id )
	{
		// We need a database connection
		$l_db = new Database();
		if ( !$l_db->connect() )
		{
			return NULL;
		}
		
		$l_ids = array();
		// Setup query and fire
		if ( ($l_result = $l_db->connection()->query( 'select user_id from tag_usr_to_'.$a_table.' where '.$a_table.'_id='.$a_id.';' )) && ($l_result->num_rows > 0) )
		{
			// Collect user-ID's
			while ( $l_record = mysqli_fetch_assoc($l_result) )
			{
				$l_ids[] = $l_record['user_id'];
			}
			
			// Destroy results
			$l_result->close();
		}

		// Destroy database connection
		$l_db->disconnect();
		return $l_ids;
	}
	

	// Returns the ID's of the tags, described by [$a_table_postfix], associated
	// with the given user [$a_id] in the table 'tag_usr_to_[$a_table_postfix]'.
	protected function get_tag_ids_by_user( $a_table_postfix, $a_id )
	{
		// We need a database connection
		$l_db = new Database();
		if ( !$l_db->connect() )
		{
			return NULL;
		}
				
		// Setup query and fire
		if ( ($l_result = $l_db->connection()->query( 'select '.$a_table_postfix.'_id from tag_usr_to_'.$a_table_postfix.' where user_id='.$a_id.';' )) && ($l_result->num_rows > 0) )
		{
			while ( $l_record = mysqli_fetch_assoc($l_result) )
			{
				$l_tag_ids[] = $l_record[$a_table_postfix.'_id'];
			}
			
			// Destroy results
			$l_result->close();
		}

		// Destroy database connection
		$l_db->disconnect();
		return $l_tag_ids;
	}
}

?>
