<?php
// Dynamic routing modules
// Copied from ivr and calleridlookup modules
// John Fawcett Sept 2009
require_once dirname(__FILE__)."/functions.inc.php";

if (! function_exists("out")) {
	function out($text) {
		echo $text."<br />";
	}
}

if (! function_exists("outn")) {
	function outn($text) {
		echo $text;
	}
}

global $db;
global $amp_conf;
$autoincrement = (($amp_conf["AMPDBENGINE"] == "sqlite") || ($amp_conf["AMPDBENGINE"] == "sqlite3")) ? "AUTOINCREMENT":"AUTO_INCREMENT";

$sql = "
	CREATE TABLE IF NOT EXISTS dynroute ( 
		`dynroute_id` INTEGER NOT NULL PRIMARY KEY $autoincrement, 
		`displayname` VARCHAR(50) not null, 
		`sourcetype` VARCHAR(100) default NULL, 
		`mysql_host` varchar(60) default NULL,
		`mysql_dbname` varchar(60) default NULL,
		`mysql_query` text,
		`mysql_username` varchar(30) default NULL,
		`mysql_password` varchar(30) default NULL
	);
	";
sql($sql);

$sql = "
CREATE TABLE IF NOT EXISTS dynroute_dests 
( 
	`dynroute_id` INT NOT NULL, 
	`selection` VARCHAR(255), 
	`dest` VARCHAR(50) 
)
";
sql($sql);

// This used to be called from page.ivr.php every time, it should not be needed, it should
// be called once and be done with.
//
dynroute_init();

?>
