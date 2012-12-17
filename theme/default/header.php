<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Home Page</title>
<link href="theme/default/style.css" rel="stylesheet" media="Screen and (min-device-width:768px) and (min-width:768px), print, projection">
<link media="handheld, only screen and (max-width: 480px), only screen and (max-device-width: 480px)" rel="stylesheet" href="theme/default/mobile.css" type="text/css" />
</head>
 
<body>
<div id="pagewrapper">
	<div class="navbar">
	  <ul class="lavaLampWithImage">
	    <li class="current"><a href="index.php">Home</a></li>
	    <?php
	    if (isset($USER) && isset($USER->id)) {
	    ?>
	    <li><a href="sendMessage.php">Send</a></li>
		<li><a href="userSettings.php">Account</a></li>
		<li><a href="logout.php">Logout</a></li>
	    <?php
	    } else {
	    ?>
	    <li><a href="register.php">Create An Account</a></li>
		<li><a href="login.php">Login</a></li>
	    <?php
	    }
	    ?>
      </ul>
      <div id="printerstatus">
          <?php
              global $CONFIG;
              if ($status = get_record_sql('SELECT * FROM '.$CONFIG->prefix.'status ORDER BY id DESC')) {
                  print "Last Printer Checkin: ".date('m/d/Y H:i:s', $status->time);
                  if (!$status->paper) {
                      print " No Paper";
                  }
              } else {
                  print "No status.";
              }
          ?>
      </div>
  	</div>

	
	