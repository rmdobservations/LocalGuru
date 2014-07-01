<?php

/***************************************************************************

Takes care of connecting to a MySQL-database.
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

class Database
{
	protected $m_db_conn;
	protected $m_connected = false;


	// Creates a database instance
	public function __construct()
	{
		// do nothing
	}


	// Try to connect to the MySQL database. Returns TRUE if successful or FALSE
	// otherwise.
	public function connect()
	{
		// Connect to the MySQL database
		$this->m_db_conn = mysqli_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD,MYSQL_DATABASE);
		if ( mysqli_connect_errno() )
		{
			return false;
		}
		else
		{
			$this->m_connected = true;
			return true;
		}
	}


	// Closes the connection to the MySQL database.
	public function disconnect()
	{
		$this->m_db_conn->close();
		$this->m_connected = false;
	}


	// Returns a handle to the MySQL database connection
	public function connection()
	{
		return $this->m_db_conn;
	}
	

	// Clears the given table(s) [$a_table] at once. Use with care!
	public function clear_table( $a_table )
	{
		// Determine current state of connection and make sure we're connected
		// before continuing.
		$l_leave_connected = true;
		if( !$this->m_connected )
		{
			$this->connect();
			$l_leave_connected = false;
		}
		
		// Either one or more tables are given. Take the appropriate action
		if( is_array($a_table) )
		{
			// Clear each individual table
			foreach($a_table as $l_table)
			{
				$this->connection()->query('truncate '.$l_table.';');
			}
		}
		else
		{
			// Clear the single table.
			$this->connection()->query('truncate '.$a_table.';');
		}
		
		// Revert to disconnected state if this was the state upon entering this 
		// function.
		if( !$l_leave_connected ) $this->disconnect();
	}
}

?>
