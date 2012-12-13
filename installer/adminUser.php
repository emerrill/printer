<?php
require_once('../config.php');

$userExists = get_record('users', 'isAdmin', 1);

if (!$userExists) {
	$submit = optional_param('Submit');
	$username = optional_param('username', 'admin');
	$passwd = optional_param('passwd');
	$passwd2 = optional_param('passwd2');
	$fname = optional_param('fname');
	$email = optional_param('email');
	$location = optional_param('location');
	
	$status = false;
	
	if ($submit) {
	    $status = true;
	    if (!$username) {
	        $missingusername = true;
	        $status = false;
	    } elseif (!check_username($username)) {
	        $takenusername = true;
	        $status = false;
	    }
	    
	    if (!$passwd) {
	        $missingpasswd = true;
	        $status = false;
	    } elseif ($passwd != $passwd2) {
	        $passwdmismatch = true;
	        $status = false;
	    }
	    
	    if (!$email) {
	        $missingemail = true;
	        $status = false;
	    } elseif (!check_email($email)) {
	        $emailinvalid = true;
	        $status = false;
	    }
	    
	    if (!$fname) {
	    	$missingfname = true;
	    	$status = false;
	    }
	    
	    if ($status) {
	        $newuser = new object();
	        $newuser->username = $username;
	        $newuser->passwd = encode_password($passwd);
	        $newuser->firstname = $fname;
	        $newuser->email = $email;
	        $newuser->location = $location;
	        $newuser->isAdmin = 1;
	        
	        $newuser->id = insert_record('users', $newuser);
	        
	        $USER = $newuser;
	    }
	}
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>Super Craigslist Installer</title></head>
<body>


<?php

if ($userExists) {
	?>
	An admin user already exists.
	<form method="post" action="../admin/loadAllCats.php">
	<input type="submit" name="submit" value="Continue...">
	</form>
	<?php
} elseif (isset($newuser) && $newuser->id) {
	?>
	The admin user was successfully created.
	<form method="post" action="../admin/loadAllCats.php">
	<input type="submit" name="submit" value="Continue...">
	</form>
	<?php
} else {
	if (!$status) {
		print "Enter the following information to create the admin user:";
		print '<FORM METHOD=POST>';
		print 'Username: <INPUT TYPE=TEXT NAME=username value="'.$username.'">';
		if (isset($missingusername) && $missingusername) {
			print '<font color=red>Required</font>';
		}
		if (isset($takenusername) && $takenusername) {
			print '<font color=red>Username Taken</font>';
		}
		print '<br>';
		print 'Password: <INPUT TYPE=PASSWORD NAME=passwd>';
		if (isset($missingpasswd) && $missingpasswd) {
			print '<font color=red>Required</font>';
			}
		print '<br>';
		print 'Password Again: <INPUT TYPE=PASSWORD NAME=passwd2>';
		if (isset($passwdmismatch) && $passwdmismatch) {
			print '<font color=red>Passwords do not match</font>';
		}
		print '<br>';
		print 'First Name: <INPUT TYPE=TEXT NAME=fname value="'.$fname.'">';
		if (isset($missingfname) && $missingfname) {
			print '<font color=red>Required</font>';
		}
		print '<br>';
		print 'Email: <INPUT TYPE=TEXT NAME=email value="'.$email.'">';
		if (isset($missingemail) && $missingemail) {
			print '<font color=red>Required</font>';
		}
		if (isset($emailinvalid) && $emailinvalid) {
			print '<font color=red>Invalid Email</font>';
		}
		print '<br>';
		print 'Location: <INPUT TYPE=TEXT NAME=location value="'.$location.'"><br>';
		
		print '<INPUT TYPE=SUBMIT NAME=Submit VALUE=Submit><br>';
		print '</FORM>';
	}

}

?>

</body>
</html>
<?php


?>