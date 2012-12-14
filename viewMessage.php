<?php

require('config.php');

require_login();

$mid = optional_param('messageid', 0);

if (!$mid || !($message = get_record('messages', 'id', $mid))) {
    print_header();
    print "Message not found.<br>";
    print_footer();
    exit();
}


print_header();



print '<div id="paper">';

print printer_view($message);

print '</div>';

print_footer();
