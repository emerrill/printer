<?php

define('PARAM_RAW',      0x0000);
define('PARAM_CLEAN',    0x0001);
define('PARAM_INT',      0x0002);
define('PARAM_INTEGER',  0x0002);  // Alias for PARAM_INT
define('PARAM_ALPHA',    0x0004);
define('PARAM_ACTION',   0x0004);  // Alias for PARAM_ALPHA
define('PARAM_FORMAT',   0x0004);  // Alias for PARAM_ALPHA
define('PARAM_NOTAGS',   0x0008);
define('PARAM_FILE',     0x0010);
define('PARAM_PATH',     0x0020);
define('PARAM_HOST',     0x0040);  // FQDN or IPv4 dotted quad
define('PARAM_URL',      0x0080);
define('PARAM_LOCALURL', 0x0180);  // NOT orthogonal to the others! Implies PARAM_URL!
define('PARAM_CLEANFILE',0x0200);
define('PARAM_ALPHANUM', 0x0400);  //numbers or letters only
define('PARAM_BOOL',     0x0800);  //convert to value 1 or 0 using empty()
define('PARAM_CLEANHTML',0x1000);  //actual HTML code that you want cleaned and slashes removed
define('PARAM_ALPHAEXT', 0x2000);  // PARAM_ALPHA plus the chars in quotes: "/-_" allowed
define('PARAM_SAFEDIR',  0x4000);  // safe directory name, suitable for include() and require()




function check_password($pw)
{
	global $CONFIG;
	if ($pw == $CONFIG->loginpass) {
		return true;
	}
	return false;
}


/**
 * Simple class
 */
class object {};

function phpclone($object) {
  if (version_compare(phpversion(), '5.0') < 0) {
   return $object;
  } else {
   return @clone($object);
  }
 }

/**
 * Returns a particular value for the named variable, taken from
 * POST or GET, otherwise returning a given default.
 *
 * This function should be used to initialise all optional values
 * in a script that are based on parameters.  Usually it will be
 * used like this:
 *    $name = optional_param('name', 'Fred');
 *
 * @param string $varname the name of the parameter variable we want
 * @param mixed  $default the default value to return if nothing is found
 * @param integer $options a bit field that specifies any cleaning needed
 * @return mixed
 */
function optional_param($varname, $default=NULL, $options=PARAM_CLEAN) {



    if (isset($_POST[$varname])) {       // POST has precedence
        $param = $_POST[$varname];
    } else if (isset($_GET[$varname])) {
        $param = $_GET[$varname];
    } else {
        return $default;
    }
	
	return $param;
    return clean_param($param, $options);
}


function get_config() {

    global $CONFIG;

    // this was originally in setup.php
    if ($configs = get_records('config')) {
        $localcfg = (array)$CONFIG;
        foreach ($configs as $config) {
            if (!isset($localcfg[$config->name])) {
                $localcfg[$config->name] = $config->value;
            }
            // do not complain anymore if config.php overrides settings from db
        }

        $localcfg = (object)$localcfg;
        return $localcfg;
    } else {
        // preserve $CFG if DB returns nothing or error
        return $CONFIG;
    }


}


function set_config($name, $value) {
/// No need for get_config because they are usually always available in $CFG

    global $CONFIG;

    if (!array_key_exists($name, $CONFIG)) {
        // So it's defined for this invocation at least
        if (is_null($value)) {
            unset($CONFIG->$name);
        } else {
            $CONFIG->$name = (string)$value; // settings from db are always strings
        }
    }

    if ($id = get_field('config', 'id', 'name', $name)) {
        if ($value===null) {
            return delete_records('config', 'name', $name);
        } else {
            $config->id = $id;
            $config->value = addslashes($value);
            return update_record('config', $config);
        }
    } else {
        if ($value===null) {
            return true;
        }
        //$config = new object();
        $config->name = $name;
        $config->value = addslashes($value);
        return insert_record('config', $config);
    }

}



/**
 * Used by {@link optional_param()} and {@link required_param()} to
 * clean the variables and/or cast to specific types, based on
 * an options field.
 *
 * @param mixed $param the variable we are cleaning
 * @param integer $options a bit field that specifies the cleaning needed
 * @return mixed
 */
function clean_param($param, $options) {

    global $CFG;

    if (is_array($param)) {              // Let's loop
        $newparam = array();
        foreach ($param as $key => $value) {
            $newparam[$key] = clean_param($value, $options);
        }
        return $newparam;
    }

    if (!$options) {
        return $param;                   // Return raw value
    }

    if ((string)$param == (string)(int)$param) {  // It's just an integer
        return (int)$param;
    }

    if ($options & PARAM_CLEAN) {
        $param = stripslashes($param);   // Needed by kses to work fine
        $param = clean_text($param);     // Sweep for scripts, etc
        $param = addslashes($param);     // Restore original request parameter slashes
    }

    if ($options & PARAM_INT) {
        $param = (int)$param;            // Convert to integer
    }

    if ($options & PARAM_ALPHA) {        // Remove everything not a-z
        $param = eregi_replace('[^a-zA-Z]', '', $param);
    }

    if ($options & PARAM_ALPHANUM) {     // Remove everything not a-zA-Z0-9
        $param = eregi_replace('[^A-Za-z0-9]', '', $param);
    }

    if ($options & PARAM_ALPHAEXT) {     // Remove everything not a-zA-Z/_-
        $param = eregi_replace('[^a-zA-Z/_-]', '', $param);
    }

    if ($options & PARAM_BOOL) {         // Convert to 1 or 0
        $tempstr = strtolower($param);
        if ($tempstr == 'on') {
            $param = 1;
        } else if ($tempstr == 'off') {
            $param = 0;
        } else {
            $param = empty($param) ? 0 : 1;
        }
    }

    if ($options & PARAM_NOTAGS) {       // Strip all tags completely
        $param = strip_tags($param);
    }

    if ($options & PARAM_SAFEDIR) {     // Remove everything not a-zA-Z0-9_-
        $param = eregi_replace('[^a-zA-Z0-9_-]', '', $param);
    }

    if ($options & PARAM_CLEANFILE) {    // allow only safe characters
        $param = clean_filename($param);
    }

    if ($options & PARAM_FILE) {         // Strip all suspicious characters from filename
        $param = ereg_replace('[[:cntrl:]]|[<>"`\|\':\\/]', '', $param);
        $param = ereg_replace('\.\.+', '', $param);
        if($param == '.') {
            $param = '';
        }
    }

    if ($options & PARAM_PATH) {         // Strip all suspicious characters from file path
        $param = str_replace('\\\'', '\'', $param);
        $param = str_replace('\\"', '"', $param);
        $param = str_replace('\\', '/', $param);
        $param = ereg_replace('[[:cntrl:]]|[<>"`\|\':]', '', $param);
        $param = ereg_replace('\.\.+', '', $param);
        $param = ereg_replace('//+', '/', $param);
        $param = ereg_replace('/(\./)+', '/', $param);
    }

    if ($options & PARAM_HOST) {         // allow FQDN or IPv4 dotted quad
        preg_replace('/[^\.\d\w-]/','', $param ); // only allowed chars
        // match ipv4 dotted quad
        if (preg_match('/(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})/',$param, $match)){
            // confirm values are ok
            if ( $match[0] > 255
                 || $match[1] > 255
                 || $match[3] > 255
                 || $match[4] > 255 ) {
                // hmmm, what kind of dotted quad is this?
                $param = '';
            }
        } elseif ( preg_match('/^[\w\d\.-]+$/', $param) // dots, hyphens, numbers
                   && !preg_match('/^[\.-]/',  $param) // no leading dots/hyphens
                   && !preg_match('/[\.-]$/',  $param) // no trailing dots/hyphens
                   ) {
            // all is ok - $param is respected
        } else {
            // all is not ok...
            $param='';
        }
    }

    if ($options & PARAM_URL) { // allow safe ftp, http, mailto urls

        //include_once($CONFIG->fileroot . '/lib/validateurlsyntax.php');

        //
        // Parameters to validateurlsyntax()
        //
        // s? scheme is optional
        //   H? http optional
        //   S? https optional
        //   F? ftp   optional
        //   E? mailto optional
        // u- user section not allowed
        //   P- password not allowed
        // a? address optional
        //   I? Numeric IP address optional (can use IP or domain)
        //   p-  port not allowed -- restrict to default port
        // f? "file" path section optional
        //   q? query section optional
        //   r? fragment (anchor) optional
        //
        if (!empty($param) && validateUrlSyntax($param, 's?H?S?F?E?u-P-a?I?p-f?q?r?')) {
            // all is ok, param is respected
        } else {
            $param =''; // not really ok
        }
        $options ^= PARAM_URL; // Turn off the URL bit so that simple PARAM_URLs don't test true for PARAM_LOCALURL
    }



    if ($options & PARAM_CLEANHTML) {
        $param = stripslashes($param);         // Remove any slashes 
        $param = clean_text($param);           // Sweep for scripts, etc
        $param = trim($param);                 // Sweep for scripts, etc
    }

    return $param;
}


function addsingleslashes($input){
    return preg_replace("/(['\\\])/", "\\\\$1", $input);
}

function addslashes_object( $dataobject ) {
    $a = get_object_vars( $dataobject);
    foreach ($a as $key=>$value) {
      $a[$key] = addslashes( $value );
    }
    return (object)$a;
}


//weblib?

function make_choose_array($in, $id, $value)
{
	$out = array();
	foreach ($in as $i) {
		$out[strval($i->$id)] = $i->$value;
	}
	return $out;
}

function choose_from_menu ($options, $name, $selected='', $nothing='choose', $script='',
                           $nothingvalue='0', $return=false, $disabled=false, $tabindex=0, $id='') {

    if ($nothing == 'choose') {
        $nothing = 'Choose...';
    }

    $attributes = ($script) ? 'onchange="'. $script .'"' : '';
    if ($disabled) {
        $attributes .= ' disabled="disabled"';
    }

    if ($tabindex) {
        $attributes .= ' tabindex="'.$tabindex.'"';
    }

    if ($id ==='') {
        $id = 'menu'.$name;
         // name may contaion [], which would make an invalid id. e.g. numeric question type editing form, assignment quickgrading
        $id = str_replace('[', '', $id);
        $id = str_replace(']', '', $id);
    }

    $output = '<select id="'.$id.'" name="'. $name .'" '. $attributes .'>' . "\n";
    if ($nothing) {
        $output .= '   <option value="'. s($nothingvalue) .'"';
        if ($nothingvalue === $selected) {
            $output .= ' selected="selected"';
        }
        $output .= '>'. $nothing .'</option>' . "\n";
    }
    if (!empty($options)) {
        foreach ($options as $value => $label) {
            $output .= '   <option value="'. s($value) .'"';
            if ((string)$value == (string)$selected) {
                $output .= ' selected="selected"';
            }
            if ($label === '') {
                $output .= '>'. $value .'</option>' . "\n";
            } else {
                $output .= '>'. $label .'</option>' . "\n";
            }
        }
    }
    $output .= '</select>' . "\n";

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

function s($var, $strip=false) {

    if ($var == '0') {  // for integer 0, boolean false, string '0'
        return '0';
    }

    if ($strip) {
        return preg_replace("/&amp;(#\d+);/i", "&$1;", htmlspecialchars(stripslashes_safe($var)));
    } else {
        return preg_replace("/&amp;(#\d+);/i", "&$1;", htmlspecialchars($var));
    }
}


/**
 * Validates an email to make sure it makes sense.
 *
 * @param string $address The email address to validate.
 * @return boolean
 */
function validate_email($address) {

    return (ereg('^[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+'.
                 '(\.[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+)*'.
                  '@'.
                  '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.
                  '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',
                  $address));
}




/**
 * Validates an email to make sure it makes sense.
 *
 * @param string $address The email address to validate.
 * @return boolean
 */
function validate_username($username) {

    //Do obscenity filter
    
    if (get_record('user', 'username', $username)) {
        return false;
    }
    
    return true;
}


/**
 * Validates an email to make sure it makes sense.
 *
 * @param string $address The email address to validate.
 * @return boolean
 */
function validate_password($password) {
    
    if ($password == '') {
        return false;
    }
    
    return true;
}


function redirect($url, $delay='0') {

    //$url     = clean_text($url);

    $url = html_entity_decode($url); // for php < 4.3.0 this is defined in moodlelib.php
    $url = str_replace(array("\n", "\r"), '', $url); // some more cleaning
    $encodedurl = htmlentities($url);
    $tmpstr = '<a href="'.$encodedurl.'" />'; //clean encoded URL
    $encodedurl = substr($tmpstr, 9, strlen($tmpstr)-13);
    $url = addslashes(html_entity_decode($encodedurl));

        echo '<meta http-equiv="refresh" content="'. $delay .'; url='. $encodedurl .'" />';
        echo '<script type="text/javascript">'. "\n" .'<!--'. "\n". "location.replace('$url');". "\n". '//-->'. "\n". '</script>';   // To cope with Mozilla bug

    die;
}




function print_error($error = 'Error', $redir = NULL) 
{
    print_header('Error');
    
    print $error;
    
    print_footer();
}


function time_string($time)
{
	return date('g:iA, F jS, Y',$time);
}

function shorten_string($string, $len)
{
    if (strlen($string) <= $len) {
        return $string;
    }
    
    $short = substr($string, 0, ($len - 3));
    $loc = strlen(strrchr($short, ' '));
    $short = substr($short, 0, strlen($short)-$loc);
    
    return $short.'...';
}


function email($to, $subject, $body)
{
	if ($to && $to != "") {
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: PAD <admin@merrilldigital.com>' . "\r\n";
		
		mail($to, $subject, $body, $headers, '-fadmin@merrilldigital.com');
	}
}



//function 

?>