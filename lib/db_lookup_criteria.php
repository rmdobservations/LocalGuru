<?php

/***************************************************************************

Reads search criteria from the Buurtlinux database.
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

// This class forms the interface to the tables that contain the search-criteria
class CriteriaLookup
{	

	// Creates a CriteriaLookup instance
	public function __construct()
	{
		// do nothing...
	}
	
	// '*_exists'-functions below check if the given item-name [$a_name] exists
	// in the corresponding table '*'. Returns the unique ID of the record if
	// a result is found, -1 if no result is found or NULL if database 
	// connection fails.
	public function distro_exists( $a_name )
	{
		return $this->check_exists( "distros", $a_name );
	}
	
	public function desktop_exists( $a_name )
	{
		return $this->check_exists( "dtenvs", $a_name );
	}
	
	public function action_exists( $a_name )
	{
		return $this->check_exists( "actions", $a_name );
	}
	
	public function group_exists( $a_name )
	{
		return $this->check_exists( "usergroups", $a_name );
	}
	
	public function target_exists( $a_name )
	{
		return $this->check_exists( "targetgroups", $a_name );
	}
	
	public function reward_exists( $a_name )
	{
		return $this->check_exists( "rewards", $a_name );
	}


	// 'get_*_criteria'-functions below return an array of records that result
	// from filtering the corresponding table '*' with the given SQL condition
	// [$a_condition].
	public function get_actions_criteria( $a_condition )
	{
		return $this->get_criteria_of( "actions", $a_condition );		
	}
	
	public function get_distros_criteria($a_condition )
	{
		return $this->get_criteria_of( "distros" , $a_condition );		
	}
	
	public function get_desktops_criteria($a_condition )
	{
		return $this->get_criteria_of( "dtenvs" , $a_condition );		
	}
	
	public function get_usergroups_criteria($a_condition )
	{
		return $this->get_criteria_of( "usergroups" , $a_condition );		
	}
	
	public function get_targetgroups_criteria($a_condition )
	{
		return $this->get_criteria_of( "targetgroups" , $a_condition );		
	}
	
	public function get_rewards_criteria($a_condition )
	{
		return $this->get_criteria_of( "rewards" , $a_condition );
	}
	

	// Returns the users' [$a_user_id] reward type for the given specific target
	// group [$a_target_group].
	public function get_reward_tagged_to( $a_user_id, $a_target_id )
	{
		return $this->get_criteria_of( "tag_usr_to_rewards" , "where user_id=".$a_user_id." and targetgroup_id=".$a_target_id );
	}

	
	// Returns the relevant search criteria for the given user [$a_user_id].
	public function get_criteria_tagged_to( $a_user_id )
	{
		$l_result['actions']	= NULL;
		$l_result['distros']	= NULL;
		$l_result['desktops']	= NULL;
		$l_result['targets']	= NULL;
		$l_result['groups']		= NULL;

		// Get action-ID's tagged to the given user
		$l_records= $this->get_criteria_of( "tag_usr_to_actions" , "where user_id=".$a_user_id );
		if ( is_array( $l_records ) && count($l_records) )
		{
			$l_conditions = "where ";
			foreach( $l_records as $l_record )
			{
				$l_conditions .= "id=".$l_record['actions_id']." or ";
			}
			$l_conditions .= "false";

			// Translate action-ID's to human-readible names
			$l_result['actions'] = $this->get_actions_criteria( $l_conditions );
		}
		
		// Get distribution-ID's tagged to the given user
		$l_records = $this->get_criteria_of( "tag_usr_to_distros" , "where user_id=".$a_user_id );
		if ( is_array( $l_records ) && count($l_records) )
		{
			$l_conditions = "where ";
			foreach( $l_records as $l_record )
			{
				$l_conditions .= "id=".$l_record['distros_id']." or ";
			}
			$l_conditions .= "false";

			// Translate distribution-ID's to human-readible names.
			$l_result['distros'] = $this->get_distros_criteria( $l_conditions );
		}
		
		// Get desktop-environment-ID's tagged to the given user
		$l_records = $this->get_criteria_of( "tag_usr_to_dtenvs" , "where user_id=".$a_user_id );
		if ( is_array( $l_records ) && count($l_records) )
		{
			$l_conditions = "where ";
			foreach( $l_records as $l_record )
			{
				$l_conditions .= "id=".$l_record['dtenvs_id']." or ";
			}
			$l_conditions .= "false";

			// Translate desktop-environment-ID's to human-readible names.
			$l_result['desktops'] = $this->get_desktops_criteria( $l_conditions );
		}
		
		// Get target-group-ID's tagged to the given user
		$l_records = $this->get_criteria_of( "tag_usr_to_targetgroups" , "where user_id=".$a_user_id );
		if ( is_array( $l_records ) && count($l_records) )
		{
			$l_conditions = "where ";
			foreach( $l_records as $l_record )
			{
				$l_conditions .= "id=".$l_record['targetgroups_id']." or ";
			}
			$l_conditions .= "false";

			// Translate target-group-ID's to human-readible names.
			$l_result['targets'] = $this->get_targetgroups_criteria( $l_conditions );
		}
		
		// Get reward-type-ID's tagged to the given user
		$l_records = $this->get_criteria_of( "tag_usr_to_rewards" , "where user_id=".$a_user_id );
		if ( is_array( $l_records ) && count($l_records) )
		{
			// The user-ID might be tagged to more than one target-group in any
			// order, so we need to translate each record into a human-readible
			// string-pair (target, reward).
			foreach( $l_records as $l_record )
			{
				// Find record of target-group and reward-type in respective 
				// tables
				$l_target = $this->get_targetgroups_criteria( "where id=".$l_record['targetgroup_id'] );				
				$l_reward = $this->get_rewards_criteria( "where id=".$l_record['rewards_id'] );				

				// We're interested in the human-readible names for the target-
				// group and reward type
				$l_pair['target'] = $l_target[0]['name'];
				$l_pair['reward'] = $l_reward[0]['name'];
				
				// Check if the user is tagged to the same target-group in both
				// rewards-type and target-groups tables.
				$l_present = false;				
				foreach( $l_result['targets'] as $l_elem )
				{
					if( $l_elem['name'] == $l_pair['target'] )
					{
						$l_present = true;
					}
				}				

				// If the target-group tagged to the user in the rewards type
				// table is also present in the target-groups table, we should
				// add its human-readible reward-type and target-group names to
				// the results.
				if( $l_present )
				{
					$l_result['rewards'][]  = $l_pair;				
				}
			}			
		}

		// Get user-group-ID's tagged to the given user		
		$l_records = $this->get_criteria_of( "tag_usr_to_usergroups" , "where user_id=".$a_user_id );
		if ( is_array( $l_records ) && count($l_records) )
		{
			$l_conditions = "where ";
			foreach( $l_records as $l_record )
			{
				$l_conditions .= "id=".$l_record['usergroups_id']." or ";
			}
			$l_conditions .= "false";
			// Translate user-groups-ID's to human readible names.
			$l_result['groups'] = $this->get_usergroups_criteria( $l_conditions );
		}
		
		return $l_result;
	}

	
	// Returns an array of MySQL-records that are the result of filtering the 
	// given table [$a_table] using the given condition(s) [$a_condition]. 
	// Returns NULL upon failure.
	protected function get_criteria_of( $a_table, $a_condition )
	{
		// We need a database connection first
		$l_db = new Database();
		if ( !$l_db->connect() )
		{
			return NULL;
		}
		
		// Setup MySQL query and fire it!
		$l_criteria = array();
		if ( ($l_result = $l_db->connection()->query( 'select * from '.$a_table.' '.$a_condition.';' )) && ($l_result->num_rows > 0) )
		{
			// Fetch the results
			while ( $l_record = mysqli_fetch_assoc($l_result) )
			{
				$l_criteria[] = $l_record;
			}
			
			// Destroy results
			$l_result->close();
		}

		// Destroy database connection and return result(s) as array of records
		$l_db->disconnect();
		return $l_criteria;
	}
	

	// Checks if the given item-name [$a_name] is present in the given table
	// [$a_table]. If present, the corresponding unique ID is returned. Returns
	// NULL if no database connection could be made or -1 if no results where 
	// found.
	protected function check_exists( $a_table, $a_name )
	{
		// We need a database connection first
		$l_db = new Database();
		if ( !$l_db->connect() )
		{
			return NULL;
		}
		
		$l_return = -1;
		// Setup query and fire!
		if ( ($l_result = $l_db->connection()->query( 'select id from '.$a_table.' where name="'.$a_name.'";' )) && ($l_result->num_rows == 1) )
		{
			// Fetch result
			if( $l_record = mysqli_fetch_assoc($l_result) )
			{
				$l_return = $l_record['id'];
			}
		}
		
		/* Destroy database connection */		
		$l_db->disconnect();
		
		return $l_return;
	}
}

?>
