<?php
require_once('../config.php');

require_admin();

$run = optional_param('run');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>Super Craigslist Installer</title></head>
<body>

<?php
if ($run) {
	print "This is going to take quite some time. <font color=red>Do not close this page until 'Complete' is desplayed at the bottom</font>.<br>\n";
	scrape_states(true);

	?>
	<br><br><font color=red>Complete.</font><br>
	<form method="post" action="../index.php">
	<input type="submit" name="run" value="Continue...">
	</form>
	<?php
} else {
	?>
	Are you sure you want import or update all craigslist regions and categories? This may take quite some time.<br>
	This must be run at least once in order for Super Craigslist to function properly.<br>
	<form method="post">
	<input type="submit" name="run" value="Continue...">
	</form>
	<form method="post" action="../index.php">
	<input type="submit" name="skip" value="Skip">
	</form>
	<?php
}

?>


</body>
</html>

<?php

?>