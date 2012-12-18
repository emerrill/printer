<?php
include('config.php');

if (optional_param('Login') && ($username = optional_param('username')) && ($passwd = optional_param('passwd'))) {
    if ($user = check_login(addslashes($username), $passwd)) {
        $USER = $user;
        redirect('index.php');
        //print 'logged in';
    } else {
    	$badlogin = true;
    }
}

if (optional_param('logout')) {
    unset($USER);
    $USER = new object();
}

print_header('Login');

if ($badlogin) {
	print '<font color=red>Invalid username or password</font><br>';
}

print '<FORM METHOD=POST>';
print 'Username: <INPUT TYPE=TEXT NAME=username><br>';
print 'Password: <INPUT TYPE=PASSWORD NAME=passwd><br>';
print '<INPUT TYPE=SUBMIT NAME=Login VALUE=Login><br>';
print '</FORM>';

print_footer();

?>