<?php
require_once('../config.php');



$sqls = array(
$CONFIG->prefix."messages" =>
"CREATE TABLE IF NOT EXISTS `".$CONFIG->prefix."messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `printed` tinyint(4) NOT NULL,
  `source` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;",

$CONFIG->prefix."printblocks" =>
"CREATE TABLE IF NOT EXISTS `".$CONFIG->prefix."printblocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block` text COLLATE utf8_bin NOT NULL,
  `messageid` int(11) NOT NULL,
  `printed` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;",

$CONFIG->prefix."status" =>
"CREATE TABLE IF NOT EXISTS `".$CONFIG->prefix."status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL,
  `paper` tinyint(4) DEFAULT NULL,
  `data` text COLLATE utf8_bin,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;",

$CONFIG->prefix."users" =>
"CREATE TABLE IF NOT EXISTS `".$CONFIG->prefix."users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `passwd` varchar(64) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `isAdmin` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;"
);



if (! $tables = $DB->Metatables() ) {
	$tables = array();
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>Super Craigslist Installer</title></head>
<body>
<?php

foreach ($sqls as $table => $sql) {
	if (!in_array($table, $tables)) {
		if (execute_sql($sql)) {
			print "<font color=green>Created table '".$table."'</font><br>\n";
		} else {
			print "<font color=red>Error creating table '".$table."'</font><br>\n";
		}
	} else {
		print "Table '".$table."' already exists<br>\n";
	}
	
}

?>
<br>
<br>
<form method="post" action="adminUser.php">
<input type="submit" name="submit" value="Continue...">
</form>
</body>
</html>
<?php


?>