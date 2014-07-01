<?php

/***************************************************************************

Routines for writing criteria to database and for tagging them to a user.
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

// This class forms the interface to the tables that contain
// the search-criteria
class CriteriaSave
{	

	// Creates a CriteriaTables instance
	public function __construct()
	{
		// do nothing...
	}
	

	// [save_*_criterium]-functions below all add an element with the given name
	// [$a_name] to the correcponding table '*'.
	public function save_distros_criterium( $a_name )
	{
		$this->save_criteria_of("distros", $a_name);
	}
	
	public function save_desktops_criterium( $a_name )
	{
		$this->save_criteria_of("dtenvs", $a_name);
	}
	
	public function save_actions_criterium( $a_name )
	{
		$this->save_criteria_of("actions", $a_name);
	}
	
	public function save_groups_criterium( $a_name )
	{
		$this->save_criteria_of("usergroups", $a_name);
	}
	
	public function save_targets_criterium( $a_name )
	{
		$this->save_criteria_of("targetgroups", $a_name);
	}
	
	public function save_rewards_criterium( $a_name )
	{
		$this->save_criteria_of("rewards", $a_name);
	}
	

	// [save_tag_usr_to_*]-functions below all add an element with the given
	// ( user-ID, '*'-ID ) to the given table '*'
	public function save_tag_usr_to_distro( $a_userid, $a_distroid )
	{
		$this->save_tag_of( "distros", $a_distroid, $a_userid );
	}
	
	public function save_tag_usr_to_desktop( $a_userid, $a_desktopid )
	{
		$this->save_tag_of( "dtenvs", $a_desktopid, $a_userid );
	}
	
	public function save_tag_usr_to_action( $a_userid, $a_actionsid )
	{
		$this->save_tag_of( "actions", $a_actionsid, $a_userid );
	}
	
	public function save_tag_usr_to_group( $a_userid, $a_groupid )
	{
		$this->save_tag_of( "usergroups", $a_groupid, $a_userid );
	}
	
	public function save_tag_usr_to_target( $a_userid, $a_targetid )
	{
		$this->save_tag_of( "targetgroups", $a_targetid, $a_userid );
	}
	
	public function save_tag_usr_to_reward( $a_userid, $a_rewardid, $a_targetid )
	{
		$this->save_tag_of_rewards( $a_rewardid, $a_userid, $a_targetid );
	}
	

	// Generic routine for adding an element with the given name [$a_name] to
	// the given [$a_table]. Returns ID of new record if successfull. Returns -1
	// if adding failed or NULL if database connection failed. 
	protected function save_criteria_of( $a_table, $a_name )
	{
		// We need a database connection
		$l_db = new Database();
		if ( !$l_db->connect() )
		{
			return NULL;
		}

		// Setup query and fire		
		$l_db->connection()->query( 'insert into '.$a_table.' set	name="'.$a_name.'";' );
		$l_return = -1;
		if ( ($l_result = $l_db->connection()->query( 'select id from '.$a_table.' where name="'.$a_name.'";' )) && ($l_result->num_rows > 0) )
		{
			if( $l_record = mysqli_fetch_assoc($l_result) )
			{
				$l_return = $l_record['id'];
			}
		}
		
		// Destroy database connection
		$l_db->disconnect();
		
		return $l_return;
	}
	

	// Generic routine for adding a tag with the given user-ID [$a_userid] and
	// criterium-ID [$a_criteriumid] to the given table [$a_table]. Returns ID 
	// of new record if successfull. Returns -1 in case adding failed or NULL if
	// database connection failed. 
	protected function save_tag_of( $a_table, $a_criteriumid, $a_userid ) {
		// We need a database connection
		$l_db = new Database();
		if ( !$l_db->connect() )
		{
			return NULL;
		}
		
		// Setup query and fire!
		$l_db->connection()->query( 'insert into tag_usr_to_'.$a_table.' set '.$a_table.'_id='.$a_criteriumid.', user_id='.$a_userid.';' );
		$l_return = -1;
		if ( ($l_result = $l_db->connection()->query( 'select id from tag_usr_to_'.$a_table.' where '.$a_table.'_id="'.$a_criteriumid.' and user_id='.$a_userid.';' )) && ($l_result->num_rows > 0) )
		{
			if( $l_record = mysqli_fetch_assoc($l_result) )
			{
				$l_return = $l_record['id'];
			}
		}
		
		// Destroy database connection
		$l_db->disconnect();
		return $l_return;
	}
	

	// Tags user [$a_userid] to a target-group [$a_targetgroupid] with a reward
	// type [$a_criteriumid] and return resulting ID if successful. Returns -1
	// if adding failed or NULL if database connection failed.
	protected function save_tag_of_rewards( $a_criteriumid, $a_userid, $a_targetgroupid )
	{
		// We need a database connection
		$l_db = new Database();
		if ( !$l_db->connect() ) {						
			return NULL;
		}

		// Setup query and fire!		
		$l_db->connection()->query( 'insert into tag_usr_to_rewards set rewards_id='.$a_criteriumid.', user_id='.$a_userid.', targetgroup_id='.$a_targetgroupid.';' );
			
		$l_return = -1;
		if ( ($l_result = $l_db->connection()->query( 'select id from tag_usr_to_rewards where rewards_id='.$a_criteriumid.' and user_id='.$a_userid.' and targetgroup_id='.$a_targetgroupid.';' )) && ($l_result->num_rows > 0) )
		{
			if( $l_record = mysqli_fetch_assoc($l_result) )
			{
				$l_return = $l_record['id'];
			}
		}
		
		// Destroy database connection
		$l_db->disconnect();
		
		return $l_return;
	}
}

?>
