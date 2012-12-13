<?php
include($CONFIG->installpath.'/lib/adodb/adodb.inc.php');
include($CONFIG->installpath.'/lib/core.php');
include($CONFIG->installpath.'/lib/db.php');
include($CONFIG->installpath.'/lib/theme.php');
include($CONFIG->installpath.'/lib/user.php');
include($CONFIG->installpath.'/lib/printer.php');

date_default_timezone_set('America/New_York');

global $CONFIG;
global $SESSION;
global $USER;


session_start();

if (! isset($_SESSION['USER']))    {
    $_SESSION['USER']    = new object;
}


$SESSION = &$_SESSION;
$USER = &$_SESSION['USER'];

//if ($USER)

//Connect the db
$DB = ADONewConnection('mysql');
    
//$DB->debug = $CONFIG->debug;

if (!isset($CONFIG->dbpersist) or !empty($CONFIG->dbpersist)) {    // Use persistent connection (default)
    $dbconnected = $DB->PConnect($CONFIG->dbhost,$CONFIG->dbuser,$CONFIG->dbpass,$CONFIG->dbname);
} else {                                                     // Use single connection
    $dbconnected = $DB->Connect($CONFIG->dbhost,$CONFIG->dbuser,$CONFIG->dbpass,$CONFIG->dbname);
}

if (! $dbconnected) {
    // In the name of protocol correctness, monitoring and performance
    // profiling, set the appropriate error headers for machine comsumption
    if (isset($_SERVER['SERVER_PROTOCOL'])) { 
        // Avoid it with cron.php. Note that we assume it's HTTP/1.x
        header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');        
    }
    // and then for human consumption...
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    echo '<html><body>';
    echo '<table align="center"><tr>';
    echo '<td style="color:#990000; text-align:center; font-size:large; border-width:1px; '.
         '    border-color:#000000; border-style:solid; border-radius: 20px; border-collapse: collapse; '.
         '    -moz-border-radius: 20px; padding: 15px">';
    echo '<p>Error: Database connection failed.</p>';
    echo '<p>It is possible that the database is overloaded or otherwise not running properly.</p>';
    echo '<p>The site administrator should also check that the database details have been correctly specified in config.php</p>';
    echo '</td></tr></table>';
    echo '</body></html>';
    
    die;
}


?>