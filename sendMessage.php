<?php
require("config.php");

require_login();

$sent = false;
$message = optional_param('message', '');
$sub = optional_param('Submit', false);
$preview = optional_param('Preview', false);

if ($sub) {
    create_message($message);
    $sent = true;
    header("Location: index.php?sent=1");
}


print_header();

if ($preview) {
    $mesg = new stdClass();
    $mesg->userid = $USER->id;
    $mesg->content = $message;
    $mesg->time = time();
    $mesg->source = SOURCE_DIRECT;

    $body = make_blocks($mesg, true);

    print '<div id="paper">';
    $classes = '';
    print make_html($body, $classes);
    
    print '</div><br>';
}


print '<FORM METHOD=POST>';

print '<textarea cols=32 rows=25 name="message">'.$message.'</textarea>';

print '<br><br>';
print '<INPUT TYPE=SUBMIT NAME=Submit VALUE=Submit> ';
print '<INPUT TYPE=SUBMIT NAME=Preview VALUE=Preview><br>';
print '</FORM>';


print_footer();


?>