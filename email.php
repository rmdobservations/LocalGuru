<?php

/***************************************************************************

Displays email-form and takes care of sending mail to Buurtlinux-helper.
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

	require_once __DIR__.'/lib/init.php';
	
	$l_error['type'] = "DEFAULT";
	
	/* See if Linux-user has been given and not fetched before */
	if( isset($_GET['user']) && ($_GET['user'] != "") && 
		 !isset( $_SESSION['email']['destination']['valid'] ) )
	{	
		require_once LIBDIR.'/db_lookup_users.php';
		$l_lookup = new UsersLookup();
		$l_user = $l_lookup->get_user_byid( $_GET['user'] );		
		if( is_array($l_user) ) {			
			$_SESSION['email']['destination']['fullname'] = $l_user['firstname']." ".$l_user['lastname'];
			$_SESSION['email']['destination']['company'] = $l_user['companyname'];
			$_SESSION['email']['destination']['emailaddress'] = $l_user['email'];
			$_SESSION['email']['destination']['tries'] = 3;			
			if( ($_SESSION['email']['destination']['fullname'] != "") && ($_SESSION['email']['destination']['emailaddress'] != "") ) {
				$_SESSION['email']['destination']['valid'] = true;
			} else {				
				$_SESSION['email']['destination']['valid'] = false;
				$l_error['type'] = "USER_UNKNOWN";
				$l_error['message'] = "Geen geldige gebruiker opgegeven";
			}
		}
	}
	
	/* See if user pressed send button */
	if ( 	isset($_SESSION['email']['destination']['valid']) && 
			$_SESSION['email']['destination']['valid'] && 
			isset( $_POST['send_mail'] ) &&
			($_POST['send_mail'] == "Versturen") )
	{
		/* See if user gave right verification code */
		if($_SESSION['email']['destination']['verification'] == $_POST['email_verification'])
		{
			unset($_SESSION['email']['destination']['verification']);
			
			if 	( 	
				!isset($_POST['name']) || ($_POST['name'] == "") || 
				!isset($_POST['email']) || ($_POST['email'] == "") || 
				!isset($_POST['subject']) || ($_POST['subject']=="") || 
				!isset($_POST['message']) || ($_POST['message']=="")
				)
			{
				$l_error['type'] = "EMPTY_FIELDS";
				$l_error['message'] = "Niet alle velden zijn ingevuld";
			} 
			else
			{					
				/* Fetch field contents and try to send mail */
				$l_from_name = $_POST['name'];
				$l_from_email = $_POST['email'];
				$l_from_subject = "Buurtlinux - ".$_POST['subject'];
				$l_from_message = $_POST['message'];
				
				#require_once("3rdparty/class.phpmailer.php");
				
				try {
					$l_mail = new PHPmailer(); 			
					$l_mail->CharSet = "UTF-8";
					$l_mail->IsHTML(false);
					//$l_mail->IsSMTP();
					//$l_mail->SMTPAuth  =  "true";
					//$l_mail->SMTPSecure = 'ssl';
					
					$l_mail->Host = EMAIL_HOST;
					//$l_mail->Port = EMAIL_PORT;
					$l_mail->Username = EMAIL_USRNAME;
					$l_mail->Password = EMAIL_PASSWRD;
					
					$l_mail->AddReplyTo($l_from_email, $l_from_name);
					$l_mail->AddAddress($_SESSION['email']['destination']['emailaddress'], $_SESSION['email']['destination']['fullname']);
					$l_mail->SetFrom($l_from_email, $l_from_name );
					$l_mail->Subject = $l_from_subject;		 
					$l_mail->Body = $l_from_message;
					
					$l_mail->Send();
					$l_error['type'] = "NONE";
					$l_error['message'] = "Mail is verstuurd";
				} catch ( phpmailerException $e ) {			
					$l_error['type'] = "MAIL_ERROR";
					$l_error['message'] = "PHPMailer exception (".$e->errorMessage().")";
				} catch (Exception $e) {			
					$l_error['type'] = "MAIL_ERROR";
					$l_error['message'] = "Other exception (".$e->getMessage().")";
				}
			}			
		}
		else
		{			
			if ( $_SESSION['email']['destination']['tries'] > 1 )
			{
				$_SESSION['email']['destination']['tries']--;
				$l_error['type'] = "WRONG_ANTIBOT_CODE";
				$l_error['message'] = "Het antwoord op de formule is verkeerd";
			}
			else
			{
				$l_error['type'] = "TMP_USER_BLOCK";
				$l_error['message'] = "Het antwoord op de formule is voor de 3<sup>e</sup> keer verkeerd. De mogelijkheid om te mailen wordt tijdelijk uitgeschakeld";
			}
		}
		
		
		if( $l_error['type'] == "MAIL_ERROR" ) {
			$l_file_name = "PHPMailerException.log";
			if(!file_exists($l_file_name)) {	// Create and open file
				$l_handle = fopen($l_file_name, 'w+');
			} else {	//open file for writng and place pointer at the end
				$l_handle = fopen($l_file_name, 'a+');
			}

			if($l_handle) {
				//place pointer at the beginning of the file.
				rewind($l_handle);

				//write to file
				fwrite($l_handle, $l_error['message']);
				fclose($l_handle);		
			}
		}
	}

	// Determine page title
	$l_title = "Onbekend";
	if( 	isset($_SESSION['email']['destination']['fullname']) &&
			($_SESSION['email']['destination']['fullname'] != "") )
	{
		$l_title = $_SESSION['email']['destination']['fullname'];
	}
	if( 	isset($_SESSION['email']['destination']['company']) &&
			($_SESSION['email']['destination']['company'] != "" )  )
	{
		$l_title .= " van ".$_SESSION['email']['destination']['company'];
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>
			Buurtlinux - Email <?php print $l_title; ?>
		</title>
		<!-- Meta-tags -->
		<meta charset="UTF-8">
		<!-- CSS -->
<?php print '
		<link rel="stylesheet" type="text/css" href="'.MAP_URL.'styles/general.css">
		<link rel="stylesheet" type="text/css" href="'.MAP_URL.'styles/map.css">
'; ?>
		<!-- JavaScript -->
	</head>
	<?php flush(); ?>
	<body style="font-size: 12px;">
	<?php
		switch ($l_error['type']) {
			case "NONE":
				$l_content = '<form name="map_email_form" method="post" action="'.$_SERVER["REQUEST_URI"].'">
						<div id="map_search_pane_title">Email '.$l_title.'</div>
						<div style="color: green; width: 550px;">Je mail is succesvol verstuurd!</div>
						<div>&nbsp;</div>
						<div width: 550px;">Je kunt dit venster nu sluiten.</div>
				</form>
				';
				break;
				
			case "TMP_USER_BLOCK":
				$l_content = '<form name="map_email_form" method="post" action="'.$_SERVER["REQUEST_URI"].'">
						<div id="map_search_pane_title">Email '.$l_title.'</div>
						<div style="color: red; width: 550px;">Het antwoord op de formule is voor de 3<sup>e</sup> keer ontjuist. De mogelijkheid om een bericht te sturen is tijdelijk geblokkeerd. Ga terug naar de kaart en probeer het opnieuw.</div>
				</form>
				';
				break;
			
			case "WRONG_ANTIBOT_CODE":				
				$l_content = '<form name="map_email_form" method="post" action="'.$_SERVER["REQUEST_URI"].'">
						<div id="map_search_pane_title">Email '.$l_title.'</div>
						<div style="color: red; width: 550px;">Het antwoord op de formule is ontjuist. Geef opnieuw antwoord op de formule. Na 3 verkeerde pogingen 
							wordt de mogelijkheid om een bericht te sturen tijdelijk geblokkeerd. Je hebt nog '.$_SESSION['email']['destination']['tries'].' poging(en) over.</div>
						<div id="map_search_pane_label">Je naam:</div>
						<input type="text" id="map_search_pane_inputbox" name="name" value="'.$_POST['name'].'"/>
						<div id="map_search_pane_label">Je email-adres:</div>
						<input type="text" id="map_search_pane_inputbox" name="email" value="'.$_POST['email'].'" />
						<div id="map_search_pane_label">Onderwerp:</div>
						<input type="text" id="map_search_pane_inputbox" name="subject" value="'.$_POST['subject'].'" />
						<div id="map_search_pane_label">Bericht:</div>
						<textarea type="text" rows="10" cols="75" name="message">'.$_POST['message'].'</textarea>
						<div id="map_search_pane_label">Geef antwoord op de volgende formule:<div>
						<div><img src="email_verification.php"></img></div>
						<input type="text" id="map_search_pane_inputbox" name="email_verification"></input>
						<div>&nbsp;</div>
						<div><input style="width: 100px;" name="send_mail" value="Versturen" type="submit" /></div>
				</form>
				';
				break;
				
			case "EMPTY_FIELDS":				
				$l_content = '<form name="map_email_form" method="post" action="'.$_SERVER["REQUEST_URI"].'">
						<div id="map_search_pane_title">Email '.$l_title.'</div>
						<div style="color: red">Vul alle velden in en geef opnieuw antwoord op de formule.</div>
						<div id="map_search_pane_label">Je naam:</div>
						<input type="text" id="map_search_pane_inputbox" name="name" value="'.$_POST['name'].'"/>
						<div id="map_search_pane_label">Je email-adres:</div>
						<input type="text" id="map_search_pane_inputbox" name="email" value="'.$_POST['email'].'" />
						<div id="map_search_pane_label">Onderwerp:</div>
						<input type="text" id="map_search_pane_inputbox" name="subject" value="'.$_POST['subject'].'" />
						<div id="map_search_pane_label">Bericht:</div>
						<textarea type="text" rows="10" cols="75" name="message">'.$_POST['message'].'</textarea>
						<div id="map_search_pane_label">Geef antwoord op de volgende formule:<div>
						<div><img src="email_verification.php"></img></div>
						<input type="text" id="map_search_pane_inputbox" name="email_verification"></input>
						<div>&nbsp;</div>
						<div><input style="width: 100px;" name="send_mail" value="Versturen" type="submit" /></div>
				</form>
				';
				break;
				
			case "USER_UNKNOWN":
				$l_content = '<div id="map_search_pane_title">Linux-hulp onbekend</div>
				<div id="map_search_pane_label">Er is geen geldige Linux-hulp opgegeven of je bent direct naar deze pagina gegaan. Je juiste manier is om via de Buurtlinux-kaart op de link "Neem contact op" te klikken
				die verschijnt in de ballon van de Linux-hulp (nadat je op de pion hebt geklikt).</div>
				<div id="map_search_pane_menu">				
					<div id="map_url"><a href="'.ROOT_URL.'" target="_self" title="Ga naar de Buurtlinux website" alt="Ga naar de Buurtlinux website">Buurtlinux website</a></div>
				</div>';
				break;
				
			case "MAIL_ERROR":				
				$l_content = '<form name="map_email_form" method="post" action="'.$_SERVER["REQUEST_URI"].'">
						<div id="map_search_pane_title">Email '.$l_title.'</div>
						<div style="color: red">Er is een fout opgetreden. Probeer het opnieuw. Als dit probleem zich blijft voordoen, neem dan contact op met de websitebeheerder.</div>
						<div id="map_search_pane_label">Je naam:</div>
						<input type="text" id="map_search_pane_inputbox" name="name">'.$_POST['name'].'</input>
						<div id="map_search_pane_label">Je email-adres:</div>
						<input type="text" id="map_search_pane_inputbox" name="email">'.$_POST['email'].'</input>
						<div id="map_search_pane_label">Onderwerp:</div>
						<input type="text" id="map_search_pane_inputbox" name="subject">'.$_POST['subject'].'</input>
						<div id="map_search_pane_label">Bericht:</div>
						<textarea type="text" rows="10" cols="75" name="message">'.$_POST['message'].'</textarea>
						<div id="map_search_pane_label">Geef antwoord op de volgende formule:<div>
						<div><img src="email_verification.php"></img></div>
						<input type="text" id="map_search_pane_inputbox" name="email_verification"></input>
						<div>&nbsp;</div>
						<div><input style="width: 100px;" name="send_mail" value="Versturen" type="submit" /></div>
				</form>
				';
				break;
				
			default:				
				$l_content = '<form name="map_email_form" method="post" action="'.$_SERVER["REQUEST_URI"].'">
						<div id="map_search_pane_title">Email '.$l_title.'</div>
						<div id="map_search_pane_label">Je naam:</div>
						<input type="text" id="map_search_pane_inputbox" name="name"></input>
						<div id="map_search_pane_label">Je email-adres:</div>
						<input type="text" id="map_search_pane_inputbox" name="email"></input>
						<div id="map_search_pane_label">Onderwerp:</div>
						<input type="text" id="map_search_pane_inputbox" name="subject"></input>
						<div id="map_search_pane_label">Bericht:</div>
						<textarea type="text" rows="10" cols="75" name="message"></textarea>
						<div id="map_search_pane_label">Geef antwoord op de volgende formule:<div>
						<div><img src="email_verification.php"></img></div>
						<input type="text" id="map_search_pane_inputbox" name="email_verification"></input>
						<div>&nbsp;</div>
						<div><input style="width: 100px;" name="send_mail" value="Versturen" type="submit" /></div>
				</form>
				';
				break;
		}
		
		print $l_content;
	?>
	</body>
</html>
