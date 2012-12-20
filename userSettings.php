<?php
include('config.php');

require_login();

$user = get_record('users', 'id', $USER->id);


$submit = optional_param('Submit');
$passwd = optional_param('passwd');
$passwd2 = optional_param('passwd2');
$fname = optional_param('fname', $user->firstname);
$email = optional_param('email', $user->email);
$location = optional_param('location', $user->location);
$userid = optional_param('userid');
$twitter = optional_param('twitter', $USER->twittername);
$twittersel = optional_param('twittersel', $USER->twitterpref);
$defaultRegion = optional_param('defaultRegion', $user->defaultRegion);



if ($submit) {
    $status = true;
    
    if ($userid != $user->id) {
    	$status = false;
    }
    
	$upuser = new object();
	$upuser->id = $userid;
    
    if ($passwd) {
        if ($passwd != $passwd2) {
	        $passwdmismatch = true;
	        $status = false;
        } else {
        	$upuser->passwd = encode_password($passwd);
        }
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
    
    //if ($defaultRegion != 0) {
	if ($defaultRegion < 0) {
		$invalidRegion = true;
		$status = false;
	}
    //}

    if ($status) {
        $upuser->firstname = $fname;
        $upuser->email = $email;

        $upuser->twittername = $twitter;
        $upuser->twitterpref = $twittersel;

        if ($USER->twittername !== $twitter) {
            follow_user($twitter);
        }

        update_record('users', addslashes_object($upuser));
		
		if ($USER->id == $upuser->id) {
			$USER = get_record('users', 'id', $upuser->id);
			//print_r($USER);
		}
		
		$update = true;
        
    }
}


print_header($user->firstname."'s settings");

/*print "<form><select>";
print "<select>".make_regions_selectlist($defaultRegion)."</select>\n";
print "</select></form>";*/
	if ($update) {
		print '<font color=red>Settings updated.</font><br>';
	}
	print '<FORM METHOD=POST>';
	print '<input type="hidden" name=userid value='.$user->id.'>';
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
    print '<option value='.TWITTER_SELECT_HASH.' '.$sel.'>#p + @'.$CONFIG->twitter_name.' + DMs</option>';
    $sel = '';
    if ($twittersel == TWITTER_SELECT_MENTION) {
        $sel = 'selected';
    }
    print '<option value='.TWITTER_SELECT_MENTION.' '.$sel.'>@'.$CONFIG->twitter_name.' + DMs</option>';
    $sel = '';
    if ($twittersel == TWITTER_SELECT_DM) {
        $sel = 'selected';
    }
    print '<option value='.TWITTER_SELECT_DM.' '.$sel.'>DMs only</option>';
    print '</select>';
    print '<br><br>When you enter a twitter name, the you will get a follow notification or request.<br>';
    print 'You can select what tweets to send:<br>All tweets<br>Tweets with #p + mention tweets + Direct messages<br>Mention tweets + Direct messages<br>Just Direct messages<br>';
    print '<br>Direct messages will appear on the printer the same as if they were sent from the web interface.';
	print '<br>';
	print '<br>';
	print '<INPUT TYPE=SUBMIT NAME=Submit VALUE=Submit><br>';
	print '</FORM>';


print_footer();

?>