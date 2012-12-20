<?php
include('config.php');

$submit = optional_param('Submit');
$username = optional_param('username');
$passwd = optional_param('passwd');
$passwd2 = optional_param('passwd2');
$fname = optional_param('fname');
$lname = optional_param('lname');
$email = optional_param('email');
$twitter = optional_param('twitter');
$twittersel = optional_param('twittersel');


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
        
        $newuser->twittername = $twitter;
        $newuser->twitterpref = $twittersel;

        $newuser->id = insert_record('users', addslashes_object($newuser));
        
        $USER = $newuser;
    }
}

print_header();



if ($newuser) {
	print 'Your user was successfully created. Click <a href="index.php">here to continue</a>.';
}

if (!$status) {
	print '<FORM METHOD=POST>';
	print 'Username: <INPUT TYPE=TEXT NAME=username value="'.$username.'">';
	if ($missingusername) {
		print '<font color=red>Required</font>';
	}
	if ($takenusername) {
		print '<font color=red>Username Taken</font>';
	}
	print '<br>';
	print 'Password: <INPUT TYPE=PASSWORD NAME=passwd>';
	if ($missingpasswd) {
		print '<font color=red>Required</font>';
		}
	print '<br>';
	print 'Password Again: <INPUT TYPE=PASSWORD NAME=passwd2>';
	if ($passwdmismatch) {
		print '<font color=red>Passwords do not match</font>';
	}
	print '<br>';
	print 'Name: <INPUT TYPE=TEXT NAME=fname value="'.$fname.'">';
	if ($missingfname) {
		print '<font color=red>Required</font>';
	}
	print '<br>';
	print 'Email: <INPUT TYPE=TEXT NAME=email value="'.$email.'">';
	if ($missingemail) {
		print '<font color=red>Required</font>';
	}
	if ($emailinvalid) {
		print '<font color=red>Invalid Email</font>';
	}
	print '<br>';
	print 'Twitter: @<INPUT TYPE=TEXT NAME=twitter value="'.$twitter.'">';
    print '<br>';
    print 'Which tweets: <select name=twittersel>';
    $sel = '';
    if ($twittersel == TWITTER_SELECT_ALL) {
        $sel = 'selected';
    }
    print '<option value='.TWITTER_SELECT_ALL.' '.$sel.'>All Tweets</option>';
    $sel = '';
    if ($twittersel == TWITTER_SELECT_HASH) {
        $sel = 'selected';
    }
    print '<option value='.TWITTER_SELECT_HASH.' '.$sel.'>Tweets with #p</option>';
    $sel = '';
    if ($twittersel == TWITTER_SELECT_MENTION) {
        $sel = 'selected';
    }
    print '<option value='.TWITTER_SELECT_MENTION.' '.$sel.'>Tweets with @'.$CONFIG->twitter_name.'</option>';
    print '</select>';
	print '<br>';
	print '<br>';
	print '<INPUT TYPE=SUBMIT NAME=Submit VALUE=Submit><br>';
	print '</FORM>';
}


print_footer();


?>