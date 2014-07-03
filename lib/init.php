<?php
define('MAINDIR',dirname(__DIR__));
define('LIBDIR',MAINDIR . '/lib');
define('PARTY',MAINDIR . '/3rdparty');
require MAINDIR.'/settings.php';
require LIBDIR.'/functions.php';
require PARTY.'/autoload.php';

$search = array(
	"address" => "",
	"country" => "",
	"radius"  => "",
	"distros" => "",
	"desktops" => "",
	"actions" => "",
	"groups" => "",
	"targets" => "",
	"rewards" => ""
);

process_GET($search);

?>
