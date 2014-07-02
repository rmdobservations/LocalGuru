<?php
/* Copy this file to settings.php and set the variables
 * according to your environment.
 */
define("ROOT_URL", 		"http://www.example.com/"); // The URL where visitors will enter your site 
define("MAP_URL", 		ROOT_URL."localguru/"); // Where LocalGuru is located, change to "" if it's in the DocumentRoot
define("MAP_WP_URL",	 	ROOT_URL."zoek-hulp");
define("MAP_BIG_URL",	 	ROOT_URL."localguru/index_iframed.php");
define("ZOEK_HULP", 		ROOT_URL."zoek-hulp/");

define("EMAIL_HOST",		"localhost");
# define("EMAIL_PORT",		465); // Use this when mail needs to sent through a submission port
define("EMAIL_USRNAME",		"website@nllgg.nl"); // Address where e-mails are sent from
define("EMAIL_PASSWRD",		""); // Use this when you need SMTP auth

define("MYSQL_HOST",		"localhost");
define("MYSQL_USER",		"localguru");
define("MYSQL_PASSWORD",	"changeme");
define("MYSQL_DATABASE",	"localguru");

define("ADD_HELPER_PASSWORD",	"Your super secret pass phrase"); // This pass phrase is used for /mysql/add_helper.php to register new users.

?>
