<?php

/*
 * Simply remove anything that we don't want. 
 * At this moment we don't allow anything that may have anything else in it.
 */
function clean_text($str) {
	$filtered = preg_replace('/[^\w .,-]/', '', $str);

	return trim( $filtered );
}

/*
 * Checks given array [$a_array] for being array and if so translates it 
 * into a comma-seperated string
 */
function array_to_comma_seperated( $a_array )
{
	$l_count = 0;
	if(is_array( $a_array ) )
	{			
		foreach( $a_array as $l_element )
		{				
			if ( $l_count )
			{
				$l_result .= ",".$l_element;
			}
			else
			{
				$l_result = $l_element;
			}

			$l_count++;
		}			
	}
	else
	{
		$l_result = $a_array;
	}

	return $l_result;
}

/*
 * Checks if the given search criteria variable is valid (Set, non-empty, 
 * non-NULL and no '*')
 */
function is_valid( $a_var )
{		
	if( 	!isset($a_var) ||		// It should be set to be valid
		($a_var == "") ||		// It shouldn't be empty to be valid		
		($a_var == NULL) ||		// It shouldn't be empty to be valid		
		($a_var == "*") 		// It should also not be a "*"
	) return false;

	// Otherwise it is found valid
	return true;
}

/*
 * Find keys from $array in $_GET if it exists and has a valid value.
 * Also remove all characters that we don't want.
 */
function process_GET(&$array)
{
	foreach( array_keys($array) as $key )
       	{
		if ( isset( $_GET[$key] ) && is_valid( $_GET[$key] ) ) {
			$array[$key] = clean_text( $_GET[$key] );
		} 
	}
}
?>
