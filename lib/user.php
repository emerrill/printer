<?php

function check_login($username, $passwd) {
    if ($user = get_record('users', 'username', $username, 'passwd', encode_password($passwd))) {
        return $user;
    }
    
    return false;
}


function encode_password($passwd) {
    return md5($passwd);
}

function get_user($username) {

}

function check_username($username) {
    if (get_record('users', 'username', $username)) {
        return false;
    }
    return true;
}

function check_email($email) {
    return true;
}

function require_login() {
	global $USER;
	
	if (isset($USER) && $USER->id) {
		return true;
	}
	
	header("Location: login.php");
    exit;
}

function require_admin() {
	global $USER;
	
	if ($USER->isAdmin) {
		return true;
	}
	
	//print_header();
	?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>Super Craigslist Installer</title></head>
<body>
<font color=red>Error:</font><br>
This page requires administrator access.<br>
</body>
</html>
	
	<?php
	//print "<font color=red>Error:</font><br>\nThis page requires administrator access.<br>\n";
	//print_footer();
	exit();
}

?>