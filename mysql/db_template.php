<?php

/* This piece of code prevents this file from being read directly and
will output an error message to inform the user. */
define("PAGE", "db_template.php");
if ( strpos($_SERVER["REQUEST_URI"], constant("PAGE") ) ) {
	die("Het lezen van '".constant("PAGE")."' is niet toegestaan, sorry!");
}

print "
create table distros(
	id int(16) auto_increment,
	name varchar(200) not null,
	primary key (id)
);

create table dtenvs(
	id int(16) auto_increment,
	name varchar(200) not null,
	primary key (id)
);

create table actions(
	id int(16) auto_increment,
	name varchar(200) not null,
	primary key (id)
);

create table usergroups(
	id int(16) auto_increment,
	name varchar(200),
	primary key (id)
);

create table targetgroups (
	id int(16) auto_increment,
	name varchar(200),
	primary key (id)
);

create table rewards(
	id int(16) auto_increment,
	name varchar(200),
	primary key (id)
);

create table users(
	id int(16) auto_increment,
	usr_username varchar(50) not null,
	usr_password varchar(32) not null,
	usr_firstname varchar(200) not null,
	usr_lastname varchar(200) not null,
	usr_companyname varchar(200),
	usr_street varchar(200),
	usr_number varchar(10),
	usr_zip varchar(10) not null,
	usr_city varchar(200) not null,
	usr_province varchar(50) not null,
	usr_country_code varchar(10) not null,
	usr_phone_1st varchar(20),
	usr_phone_2nd varchar(20),
	usr_website_url varchar(200),
	usr_email varchar(200) not null,	
	usr_info varchar(1000),
	usr_loc_lat varchar(15) not null,
	usr_loc_lon varchar(15) not null,
	primary key (id)
);

create table tag_usr_to_distros (
	distros_id int(16) not null,
	user_id int(16) not null,
	primary key (distros_id, user_id)
);

create table tag_usr_to_dtenvs (
	dtenvs_id int(16) not null,
	user_id int(16) not null,
	primary key (dtenvs_id, user_id)
);

create table tag_usr_to_actions (
	actions_id int(16) not null,
	user_id int(16) not null,
	primary key (actions_id, user_id)
);

create table tag_usr_to_usergroups (
	usergroups_id int(16) not null,
	user_id int(16) not null,
	primary key (usergroups_id, user_id)
);

create table tag_usr_to_targetgroups (
	targetgroups_id int(16) not null,
	user_id int(16) not null,
	primary key (targetgroups_id, user_id)
);

create table tag_usr_to_rewards (
	rewards_id int(16) not null,
	user_id int(16) not null,
	targetgroup_id int(16) not null,
	primary key (rewards_id, user_id, targetgroup_id)
);

create table users_off_radar (
	user_id int(16) not null,
	primary key (user_id)
);

";

?>
