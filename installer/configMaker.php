<?php
include('../lib/core.php');

$dbhost = optional_param('dbhost', 'localhost');
$dbname = optional_param('dbname');
$dbuser = optional_param('dbuser', 'root');
$dbpass = optional_param('dbpass');
$prefix = optional_param('prefix', 'scl_');
$dir = optional_param('dir', dirname(dirname(__file__)));

$submit = optional_param('submit');
$download = optional_param('download');

$configfile = $dir.'/config.php';

/*if (file_exists($configfile)) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>Super Craigslist Installer</title></head>
<body>
Your config.php file exists! Click to continue to the next installer phase:
<form method="post" action="sqlInstaller.php">
<input type="submit" name="cont" value="Continue...">
</form>
</body>
<?php
exit;
}*/


if ($submit || $download) {

 	$cfgstr  = '<?php  /// Config File '."\r\n";
    $cfgstr .= "\r\n";
	
	$cfgstr .= 'unset($CONFIG);'."\r\n";
	$cfgstr .= "\r\n";
    
    $cfgstr .= '$CONFIG->dbhost    = \''.addslashes($dbhost)."';\r\n";
    
    $cfgstr .= '$CONFIG->dbname    = \''.$dbname."';\r\n";
    
    $cfgstr .= '$CONFIG->dbuser    = \''.addsingleslashes($dbuser)."';\r\n";
    $cfgstr .= '$CONFIG->dbpass    = \''.addsingleslashes($dbpass)."';\r\n";
    $cfgstr .= '$CONFIG->dbpersist =  false;'."\r\n";
    $cfgstr .= '$CONFIG->prefix    = \''.$prefix."';\r\n";
    $cfgstr .= "\r\n";
	
	$cfgstr .= '$CONFIG->installpath    = \''.$dir."';\r\n";
	$cfgstr .= '$CONFIG->limitMI =  true;'."\r\n";
	$cfgstr .= "\r\n";
    $cfgstr .= "include('lib/setup.php');\r\n";
    $cfgstr .= '// MAKE SURE WHEN YOU EDIT THIS FILE THAT THERE ARE NO SPACES, BLANK LINES,'."\r\n";
    $cfgstr .= '// RETURNS, OR ANYTHING ELSE AFTER THE TWO CHARACTERS ON THE NEXT LINE.'."\r\n";
    $cfgstr .= '?>';
	//print "<pre>".$str."</str>";

	if ($download) {
	    header("Content-Type: application/x-forcedownload\n");
	    header("Content-Disposition: attachment; filename=\"config.php\"");
	    echo $cfgstr;
	    exit;
    } else {
	    $configfile = dirname(dirname(__file__)).'/config.php';
		//print $configfile;
	
		if (!file_exists($configfile)) {
			//umask(0137);
		
		    if (( $configsuccess = ($fh = @fopen($configfile, 'w')) ) !== false) {
		        fwrite($fh, $cfgstr);
		        fclose($fh);
		    }
	    }
    }



} else {

}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>Super Craigslist Installer</title></head>
<body>
<?php

if (file_exists($configfile)) {
	//Wrote it to disk. Now should go to SQL setup.
	if ($configsuccess) {
		print "Your config.php file was created! Click to continue to the next installer phase:<br>\n";
	} else {
		print "Your config.php file exists! Click to continue to the next installer phase:<br>\n";
	}
	?>
	
	<form method="post" action="sqlInstaller.php">
	<input type="submit" name="cont" value="Continue...">
	</form>
	<?php
} elseif ($cfgstr) {
	//couldnt write it to disk.
	?>
	The installer could not write the configuration file to disk, probably because it did not have permissions to write to the directory.<br>
	Please create the file config.php and place it in the Super Craigslist directory (which apprears to be <?php echo dirname(dirname(__file__)).'/'; ?>) with the contents below.<br>
	<form method="post">
	<input type="hidden" name="dbhost" value="<?php echo $dbhost; ?>">
	<input type="hidden" name="dbname" value="<?php echo $dbname; ?>">
	<input type="hidden" name="dbuser" value="<?php echo $dbuser; ?>">
	<input type="hidden" name="dbpass" value="<?php echo $dbpass; ?>">
	<input type="hidden" name="prefix" value="<?php echo $prefix; ?>">
	<input type="hidden" name="dir" value="<?php echo $dir; ?>">
	<input type="submit" name="download" value="Download config.php">
	</form>
	<form method="post">
	<input type="hidden" name="dbhost" value="<?php echo $dbhost; ?>">
	<input type="hidden" name="dbname" value="<?php echo $dbname; ?>">
	<input type="hidden" name="dbuser" value="<?php echo $dbuser; ?>">
	<input type="hidden" name="dbpass" value="<?php echo $dbpass; ?>">
	<input type="hidden" name="prefix" value="<?php echo $prefix; ?>">
	<input type="hidden" name="dir" value="<?php echo $dir; ?>">
	<input type="submit" name="submit" value="Check Again ...">
	</form>
	<hr>
	<pre><?php print_r(s($cfgstr));?></pre>
	<?php
} else {
	?>
	<form method="post">
	
	DB Host: <input type="text" name="dbhost" value="<?php echo $dbhost; ?>"><br>
	DB Name: <input type="text" name="dbname" value="<?php echo $dbname; ?>"><br>
	DB Username: <input type="text" name="dbuser" value="<?php echo $dbuser; ?>"><br>
	DB Password: <input type="password" name="dbpass" value="<?php echo $dbpass; ?>"><br>
	Table Prefix: <input type="text" name="prefix" value="<?php echo $prefix; ?>"><br>
	<br>
	Install Directory: <input type="text" name="dir" value="<?php echo $dir; ?>"><br>
	<input type="submit" name="submit" value="Continue"> <input type="reset" name="reset" value="Reset">
	
	<br>
	
	</form>
	
	<?php
}
?>

</body>
</html>

<?php


?>