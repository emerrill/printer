<?php

define('SOURCE_DIRECT', 0);
define('SOURCE_SOCIAL', 1);
define('SOURCE_TWITTER', 2);
define('SOURCE_TWITTER_DIRECT', 3);
define('SOURCE_TWITTER_MENTION', 4);

define('BLOCK_LIMIT', 256);


function create_message($message, $source = SOURCE_DIRECT, $user = false) {
    global $USER;

    if ($user === false) {
        $user = $USER;
    }


    $mesg = new stdClass();
    $mesg->userid = $user->id;
    $mesg->content = addslashes($message);
    $mesg->time = time();
    $mesg->source = $source;
    $mesg->id = insert_record('messages', $mesg);

    make_blocks($mesg);

    return $mesg->id;
}

function make_blocks($message, $return = false) {
    $body = get_header($message);

    $content = $message->content;
    $content = wordwrap($content, 32, "\n", true);

    $lines = explode("\n", $content);


    foreach ($lines as $line) {
        $body .= "|c:$".$line."\n";
    }

    $body .= get_footer($message);


    if ($return) {
        return $body;
    }

    save_block_chunks($body, $message->id);
}

function save_block_chunks($content, $messageid) {
    $size = 0;
    $block = '';

    $lines = explode("\n", $content);
    foreach ($lines as $line) {
        if ((strlen($block) + strlen($line)) >= BLOCK_LIMIT) {
            save_block($block, $messageid);
            $block = '';
        }
        $block .= $line."\n";
    }

    save_block($block, $messageid);
}

function save_block($content, $messageid) {
    $block = new stdClass();
    $block->messageid = $messageid;
    $block->block = $content;

    insert_record('printblocks', $block);
}

function get_header($message) {
    $head = '';

    if (($message->source == SOURCE_DIRECT) || ($message->source == SOURCE_TWITTER_DIRECT)) {
        $head .= '|WH:C$DIRECT'."\n";
    }

    if (($message->source == SOURCE_TWITTER) || ($message->source == SOURCE_TWITTER_MENTION)) {
        $head .= '|N:C$Tweet'."\n";
    }

    

    $user = get_record('users', 'id', $message->userid);
    $name = $user->firstname;
    $date = date('m/d/Y H:i:s', $message->time);

    $name = str_pad($name, 32-strlen($date));

    $head .= "|NI:L$".$name.$date."\n";
    $head .= "|N:$\n";

    return $head;
}

function get_footer($message) {
    return '|N:$'."\n".'|:$________________________________'."\n|:$\n|:$\n";
}


function printer_view($message) {
    $out = '';
    $classes = '';

    $blocks = get_records('printblocks', 'messageid', $message->id, 'id ASC');

    foreach ($blocks as $block) {
        $out .= make_html(trim($block->block), $classes);
    }

    return $out;
}

function make_html($body, &$classes) {
    $out = '';
    $lines = explode("\n", $body);
    foreach ($lines as $line) {
        $content = substr($line, strpos($line, '$')+1);
        $content = htmlentities($content);
        $content = str_replace(' ', '&nbsp;', $content);

        //$classes = '';
        $chars = substr($line, strpos($line, '|')+1, strpos($line, '$')-strpos($line, '|')-1);

        $chars = str_split($chars);

        $nochar = true;
        $justify = false;
        foreach ($chars as $char) {
            if ($char == ':') {
                $justify = true;
                if ($nochar) {
                    $classes = '';
                }
                continue;
            }
            $nochar = false;

            if (ctype_upper($char) || is_numeric($char)) {
                if ($char == 'N') {
                    $classes = '';
                } else {
                    if ($justify) {
                        $classes .= 'J';
                    }
                    $classes .= $char.' ';
                }
            }
        }

        $out .= '<div class="line '.$classes.'">'.$content.'<br></div>';
    }

    return $out;
}
?>