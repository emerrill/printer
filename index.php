<?php
if (!file_exists('config.php')) {
	header("Location: installer/configMaker.php");
    exit;
}
include('config.php');


$sent = optional_param('sent', 0);



print_header();

if (!isset($USER) || !$USER->id) {
	exit();
}
if ($sent) {
    print "<br><br>";
    print "<font color=green>Message queued for printing.</font>";
    print "<br><br>";
}

$recents = get_records('messages', '', '', 'id DESC', '*', '', 20);

print "<table>";
print "<tr><td width=75px>From</td><td width=115px>Source</td><td width=180px>Time</td><td width=75px>Status</td><td></td></tr>";
foreach ($recents as $message) {
    print "<tr>";
    $user = get_record('users', 'id', $message->userid);
    print "<td>";
    print $user->firstname;
    print "</td>";
    print "<td>";
    switch ($message->source) {
        case SOURCE_SOCIAL:
            print "Social";
            break;
        case SOURCE_TWITTER:
            print "Twitter";
            break;
        case SOURCE_TWITTER_DIRECT:
            print "Twitter DM";
            break;
        case SOURCE_TWITTER_MENTION:
            print "Twitter Mention";
            break;
        case SOURCE_DIRECT:
        default:
            print "Direct";
            break;
        
    }
    print "</td>";
    print "<td>";
    print date('m/d/Y H:i:s', $message->time);
    print "</td>";
    print "<td>";
    if ($message->printed) {
        print "<font color=green>Printed</font>";
    } else {
        print "<font color=red>Pending</font>";
    }
    print "</td>";
    print "<td>";
    print '<a href="viewMessage.php?messageid='.$message->id.'">View</a>';
    print "</td>";
    print "</tr>";
}
print "</table>";
print '

';

print_footer();


?>
