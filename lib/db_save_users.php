<?php

/***************************************************************************

User registration routines.
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

require_once LIBDIR.'/db_access.php';

// This class forms the interface to the tables that contain
// the search-criteria
class UsersSave {	

	// Creates a UserSave instance
	public function __construct()
	{
		// do nothing...
	}
	

	// Saves profile for new user and returns its ID
	public function save_new_user( $a_user )
	{
		// We need a database connection
		$l_db = new Database();
		if ( !$l_db->connect() )
		{
			return NULL;
		}		

		// Setup query and register user
		$l_db->connection()->query( 'insert into users set
			usr_username="'.$a_user['username'].'",
			usr_password="'.$a_user['password'].'",
			usr_firstname="'.$a_user['firstname'].'",
			usr_lastname="'.$a_user['lastname'].'",
			usr_companyname="'.$a_user['companyname'].'",
			usr_street="'.$a_user['street'].'",
			usr_number="'.$a_user['number'].'",
			usr_zip="'.$a_user['zip'].'",
			usr_city="'.$a_user['city'].'",
			usr_province="'.$a_user['province'].'",
			usr_country_code="'.$a_user['country'].'",
			usr_phone_1st="'.$a_user['phone1'].'",
			usr_phone_2nd="'.$a_user['phone2'].'",
			usr_website_url="'.$a_user['website'].'",
			usr_email="'.$a_user['email'].'",
			usr_info="'.$a_user['info'].'",
			usr_loc_lat="'.$a_user['loc_lat'].'",
			usr_loc_lon="'.$a_user['loc_lon'].'";
		');
		
		// Destroy database connection
		$l_db->disconnect();
		
		// Verify existence of new user and store user-ID
		$l_lookup = new UsersLookup();
		$l_user = $l_lookup->get_user_byemail( $a_user['email'] );		
		if( !is_array( $l_user ) )
		{
			return NULL;
		}
		
		// Return user-ID
		return $l_user['id'];
	}
	

	// Generates and returns a string with random characters.
	public function generate_random_string()
	{
		$l_len = 10;
		$l_chars = "0123456789abcdefghijklmnopqrstuvwxyz";
		$l_string = "";

		for ($p = 0; $p < $l_len; $p++)
		{
			$l_string .= $l_chars[mt_rand(0, strlen($l_chars))];
		}

		return $l_string;
	}
}

?>
