<?php


function print_header($title = 'Super Craigslist') {
    global $USER;
    
    /*print "<HTML><HEAD><TITLE>$title</TITLE></HEAD><BODY>";
    //display logged in user
    if (isset($USER->id)) {
        print "You are $USER->username<br>";
    }*/
    include 'theme/default/header.php';
}

function print_footer() {
    //print "</BODY></HTML>";
    include 'theme/default/footer.php';
}

?>