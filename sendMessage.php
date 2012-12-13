<?php
require("config.php");

require_login();

$sent = false;
$message = optional_param('message', '');
if (!empty($message)) {
    create_message($message);
    $sent = true;
    header("Location: index.php?sent=1");
}


print_header();


print '<FORM METHOD=POST>';

print '<textarea cols=32 rows=25 name="message">'.$message.'</textarea>';

print '<br><br>';
print '<INPUT TYPE=SUBMIT NAME=Submit VALUE=Submit><br>';
print '</FORM>';


print_footer();


?>