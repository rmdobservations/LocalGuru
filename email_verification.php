<?php

/***************************************************************************

Generates a custom CAPTCHA for display in e.g. an email-form.
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
 

	/* Start a PHP session */
	session_start();	
	
	//Send a generated image to the browser
	create_image();
	exit();

function create_image()
{
    //Let's generate a totally random string using md5
    $l_nr1 = rand(0,19); 
    $l_nr2 = rand(0,19); 
    $l_sign = rand(0,1); 
    
	if( $l_sign ) {
		$l_formula = $l_nr1." + ".$l_nr2." = ";
		$_SESSION['email']['destination']['verification'] = $l_nr1 + $l_nr2;
	}
	else {
		$l_formula = $l_nr1." - ".$l_nr2." = ";
		$_SESSION['email']['destination']['verification'] = $l_nr1 - $l_nr2;
	}

    //Set the image width and height
    $width = 200;
    $height = 32; 

    //Create empty image resource 
    $image = ImageCreate($width, $height);  

    //We are making three colors, white, black and gray
    $white = ImageColorAllocate($image, 255, 255, 255);
    $black = ImageColorAllocate($image, 0, 0, 0);
    $grey = ImageColorAllocate($image, 100, 175, 255);
    
    /* Copy tux-image upon */
    $l_tux = ImageCreateFromPNG("img/tux.png");
    ImageCopyResized( $image, $l_tux, $width-$height, 0, 0, 0, $height, $height, 64, 64 );

    //Make the background black 
    ImageFill($image, 0, 0, $black); 

    //Add randomly generated string in white to the image
    ImageString($image, 32, 50, 8, $l_formula, $white); 
    
    //Throw in some lines to make it a little bit harder for any bots to break 
    ImageRectangle($image,0,0,$width-1,$height-1,$grey); 
    imageline($image, 2, $height/4, $width-2, $height/3, $grey); 
    imageline($image, 2, $height-$height/4, $width-2, $height-$height/3, $grey); 
    imageline($image, 1, $height/6, $width/4, $height-2, $grey); 
    imageline($image, $height-1, $height/6, $width/4, $height-2, $grey);
 
    //Tell the browser what kind of file is come in 
    header("Content-Type: image/jpeg"); 

    //Output the newly created image in jpeg format 
    ImageJpeg($image);
   
    //Free up resources
    ImageDestroy($image);
} 
?>
